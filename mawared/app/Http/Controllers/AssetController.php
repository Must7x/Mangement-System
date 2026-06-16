<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function create(): View
    {
        return view('assets.create', [
            'statuses' => $this->manualStatusesForForm(),
            'assetTypes' => $this->commonAssetTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAsset($request);

        if ($error = $this->rejectManualActiveStatus($validated['status'])) {
            return $error;
        }

        Asset::create($validated);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'تم تسجيل المعدات بنجاح.');
    }

    public function show(Asset $asset): View
    {
        $asset->load([
            'assignment.employee.department',
            'openMaintenance',
        ]);

        $assignmentHistories = $asset->assignmentHistories()
            ->with('employee.department')
            ->orderByDesc('assigned_date')
            ->orderByDesc('id')
            ->get();

        $maintenances = $asset->maintenances()
            ->orderByDesc('maintenance_start_date')
            ->orderByDesc('id')
            ->get();

        return view('assets.show', compact('asset', 'assignmentHistories', 'maintenances'));
    }

    public function edit(Asset $asset): View
    {
        $asset->load('assignment');

        return view('assets.edit', [
            'asset' => $asset,
            'statuses' => $this->manualStatusesForForm($asset),
            'assetTypes' => $this->commonAssetTypes(),
        ]);
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $asset->load('assignment');
        $validated = $this->validateAsset($request, $asset);

        if ($error = $this->rejectManualActiveStatus($validated['status'], $asset)) {
            return $error;
        }

        if ($asset->assignment && $validated['status'] === AssetStatus::Warehouse->value) {
            return back()
                ->withErrors(['status' => 'لا يمكن إرجاع الجهاز إلى المخزن يدوياً وهو مُسنَد عهدة. قم بسحب العهدة أولاً.'])
                ->withInput();
        }

        if ($asset->status === AssetStatus::Active && ! $asset->assignment) {
            return back()
                ->withErrors(['status' => 'حالة «نشط» تُدار فقط عبر نظام العهدة.'])
                ->withInput();
        }

        $asset->update($validated);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'تم تحديث بيانات المعدات.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        if ($asset->assignment()->exists()) {
            return back()->withErrors(['asset' => 'لا يمكن حذف جهاز مُسنَد عهدة. قم بسحب العهدة أولاً.']);
        }

        if ($asset->openMaintenance()->exists()) {
            return back()->withErrors(['asset' => 'لا يمكن حذف جهاز له طلب صيانة مفتوح.']);
        }

        $asset->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'تم حذف المعدات من السجل.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAsset(Request $request, ?Asset $asset = null): array
    {
        $allowedStatusValues = array_map(
            fn (AssetStatus $s) => $s->value,
            $this->manualStatusesForForm($asset)
        );

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->ignore($asset?->id),
            ],
            'status' => ['required', Rule::in($allowedStatusValues)],
        ]);
    }

    /**
     * الحالات القابلة للتعديل يدوياً — «نشط» يُدار حصرياً عبر محرك العهدة.
     *
     * @return list<AssetStatus>
     */
    private function manualStatusesForForm(?Asset $asset = null): array
    {
        if ($asset?->status === AssetStatus::Active) {
            return [AssetStatus::Active];
        }

        if ($asset?->status === AssetStatus::Maintenance) {
            return [AssetStatus::Maintenance];
        }

        return [AssetStatus::Warehouse];
    }

    private function rejectManualActiveStatus(string $status, ?Asset $asset = null): ?RedirectResponse
    {
        if ($status !== AssetStatus::Active->value) {
            return null;
        }

        if ($asset?->status === AssetStatus::Active && $asset->assignment) {
            return null;
        }

        return back()
            ->withErrors(['status' => 'لا يمكن تعيين حالة «نشط» يدوياً. استخدم تخصيص العهدة من صفحة العهد.'])
            ->withInput();
    }

    /**
     * @return list<string>
     */
    private function commonAssetTypes(): array
    {
        return ['لابتوب', 'طابعة', 'حاسوب مكتبي', 'شاشة', 'هاتف', 'أثاث', 'شبكات'];
    }
}
