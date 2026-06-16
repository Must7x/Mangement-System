<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $maintenances = Maintenance::with('asset')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('issue_description', 'like', $term)
                        ->orWhere('technician_name', 'like', $term)
                        ->orWhereHas('asset', fn ($aq) => $aq->where('name', 'like', $term)
                            ->orWhere('serial_number', 'like', $term));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->orderByDesc('maintenance_start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('maintenances.index', [
            'maintenances' => $maintenances,
            'filters' => $request->only(['q', 'status', 'priority']),
            'statuses' => MaintenanceStatus::cases(),
            'priorities' => MaintenancePriority::options(),
        ]);
    }

    public function create(): View
    {
        return view('maintenances.create', [
            'assets' => $this->availableAssets(),
            'priorities' => MaintenancePriority::options(),
            'statuses' => MaintenanceStatus::editableStatuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMaintenance($request);

        try {
            DB::transaction(function () use ($validated): void {
                $asset = Asset::query()
                    ->whereKey($validated['asset_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->assertAssetAvailableForMaintenance($asset);

                Maintenance::create($validated);

                $asset->update(['status' => AssetStatus::Maintenance]);
            });
        } catch (\RuntimeException $exception) {
            return back()
                ->withErrors(['asset_id' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'تم فتح طلب الصيانة بنجاح.');
    }

    public function edit(Maintenance $maintenance): View
    {
        $maintenance->load('asset');

        return view('maintenances.edit', [
            'maintenance' => $maintenance,
            'priorities' => MaintenancePriority::options(),
            'statuses' => MaintenanceStatus::editableStatuses(),
        ]);
    }

    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        if ($maintenance->isClosed()) {
            return back()->withErrors(['maintenance' => 'لا يمكن تعديل طلب صيانة مكتمل أو ملغى.']);
        }

        $validated = $this->validateMaintenance($request, $maintenance, updating: true);

        $maintenance->update($validated);

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'تم تحديث طلب الصيانة.');
    }

    public function complete(Maintenance $maintenance): RedirectResponse
    {
        if ($maintenance->isClosed()) {
            return back()->withErrors(['maintenance' => 'طلب الصيانة مغلق بالفعل.']);
        }

        DB::transaction(function () use ($maintenance): void {
            $asset = Asset::query()
                ->whereKey($maintenance->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $maintenance->update([
                'status' => MaintenanceStatus::Completed,
                'maintenance_end_date' => now()->toDateString(),
            ]);

            $asset->update(['status' => AssetStatus::Warehouse]);
        });

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'تم إكمال الصيانة وإرجاع الجهاز إلى المخزن.');
    }

    public function cancel(Maintenance $maintenance): RedirectResponse
    {
        if ($maintenance->isClosed()) {
            return back()->withErrors(['maintenance' => 'طلب الصيانة مغلق بالفعل.']);
        }

        DB::transaction(function () use ($maintenance): void {
            $asset = Asset::query()
                ->whereKey($maintenance->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $maintenance->update([
                'status' => MaintenanceStatus::Cancelled,
                'maintenance_end_date' => now()->toDateString(),
            ]);

            $asset->update(['status' => AssetStatus::Warehouse]);
        });

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'تم إلغاء طلب الصيانة وإرجاع الجهاز إلى المخزن.');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Asset>
     */
    private function availableAssets(): \Illuminate\Database\Eloquent\Collection
    {
        return Asset::query()
            ->where('status', AssetStatus::Warehouse)
            ->whereDoesntHave('assignment')
            ->whereDoesntHave('openMaintenance')
            ->orderBy('name')
            ->get();
    }

    private function assertAssetAvailableForMaintenance(Asset $asset): void
    {
        if ($asset->status !== AssetStatus::Warehouse) {
            throw new \RuntimeException('الصيانة مسموحة فقط للأجهزة في المخزن.');
        }

        if ($asset->assignment()->exists()) {
            throw new \RuntimeException('لا يمكن فتح صيانة لجهاز مُسنَد عهدة.');
        }

        if ($asset->openMaintenance()->exists()) {
            throw new \RuntimeException('يوجد طلب صيانة مفتوح على هذا الجهاز.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMaintenance(Request $request, ?Maintenance $maintenance = null, bool $updating = false): array
    {
        $statusValues = array_map(
            fn (MaintenanceStatus $status) => $status->value,
            MaintenanceStatus::editableStatuses()
        );

        $priorityValues = array_map(
            fn (MaintenancePriority $priority) => $priority->value,
            MaintenancePriority::options()
        );

        $rules = [
            'issue_description' => ['required', 'string'],
            'priority' => ['required', Rule::in($priorityValues)],
            'technician_name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in($statusValues)],
            'maintenance_start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];

        if (! $updating) {
            $rules['asset_id'] = ['required', 'exists:assets,id'];
        }

        return $request->validate($rules);
    }
}
