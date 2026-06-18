<?php

namespace App\Http\Controllers;

use App\Enums\ActivityAction;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Services\ActivityLogger;
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

        $asset = Asset::create($validated);

        ActivityLogger::log(ActivityAction::AssetCreated, $asset, $asset, [
            'asset_name' => $asset->name,
            'serial_number' => $asset->serial_number,
        ]);

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.asset_created'));
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

        $activityLogs = auth()->user()->hasPermission('activity_log.view')
            ? $asset->activityLogs()
                ->with(['user.assignedRole', 'asset'])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('assets.show', compact('asset', 'assignmentHistories', 'maintenances', 'activityLogs'));
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
                ->withErrors(['status' => __('messages.errors.asset_cannot_return_while_assigned')])
                ->withInput();
        }

        if ($asset->status === AssetStatus::Active && ! $asset->assignment) {
            return back()
                ->withErrors(['status' => __('messages.errors.asset_active_status_managed_by_assignments')])
                ->withInput();
        }

        $asset->update($validated);

        ActivityLogger::log(ActivityAction::AssetUpdated, $asset, $asset, [
            'asset_name' => $asset->name,
            'serial_number' => $asset->serial_number,
        ]);

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.asset_updated'));
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        if ($asset->assignment()->exists()) {
            return back()->withErrors(['asset' => __('messages.errors.asset_cannot_delete_assigned')]);
        }

        if ($asset->openMaintenance()->exists()) {
            return back()->withErrors(['asset' => __('messages.errors.asset_cannot_delete_open_maintenance')]);
        }

        ActivityLogger::log(ActivityAction::AssetDeleted, $asset, $asset, [
            'asset_name' => $asset->name,
            'serial_number' => $asset->serial_number,
        ]);

        $asset->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.asset_deleted'));
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
            ->withErrors(['status' => __('messages.errors.asset_cannot_set_active_manually')])
            ->withInput();
    }

    /**
     * @return list<string>
     */
    private function commonAssetTypes(): array
    {
        /** @var array<string, string> $options */
        $options = __('fields.asset_type_options');

        return array_keys($options);
    }
}
