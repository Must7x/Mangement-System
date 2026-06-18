<?php

namespace App\Http\Controllers;

use App\Enums\ActivityAction;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(): View
    {
        $assignments = Assignment::with(['asset', 'employee.department'])
            ->orderByDesc('assigned_date')
            ->get();

        $warehouseAssets = Asset::where('status', AssetStatus::Warehouse)
            ->orderBy('name')
            ->get();

        $employees = Employee::with('department')
            ->orderBy('name')
            ->get();

        return view('assignments.index', compact('assignments', 'warehouseAssets', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'assigned_date' => ['required', 'date'],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $asset = Asset::query()
                    ->whereKey($validated['asset_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($asset->status !== AssetStatus::Warehouse) {
                    throw new \RuntimeException(__('messages.errors.assignment_device_not_in_warehouse'));
                }

                if ($asset->assignment()->exists()) {
                    throw new \RuntimeException(__('messages.errors.assignment_device_has_active_custody'));
                }

                $employee = Employee::with('department')
                    ->whereKey($validated['employee_id'])
                    ->firstOrFail();

                $assignment = Assignment::create([
                    ...$validated,
                    'employee_name' => $employee->name,
                    'department' => $employee->department?->name ?? '',
                ]);

                AssignmentHistory::create([
                    'asset_id' => $asset->id,
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'department_name' => $employee->department?->name ?? '',
                    'assigned_date' => $validated['assigned_date'],
                ]);

                $asset->update(['status' => AssetStatus::Active]);

                ActivityLogger::log(ActivityAction::AssignmentCreated, $assignment, $asset, [
                    'asset_name' => $asset->name,
                    'employee_name' => $employee->name,
                    'serial_number' => $asset->serial_number,
                ]);
            });
        } catch (\RuntimeException $exception) {
            return back()
                ->withErrors(['asset_id' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('assignments.index')
            ->with('success', __('messages.success.assignment_created'));
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $employeeId = $assignment->employee_id;
        $assignedDate = $assignment->assigned_date;
        $employeeName = $assignment->employee_name;

        DB::transaction(function () use ($assignment, $employeeId, $assignedDate, $employeeName): void {
            $asset = Asset::query()
                ->whereKey($assignment->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            ActivityLogger::log(ActivityAction::AssignmentReturned, $assignment, $asset, [
                'asset_name' => $asset->name,
                'employee_name' => $employeeName,
                'serial_number' => $asset->serial_number,
            ]);

            $assignment->delete();

            AssignmentHistory::query()
                ->where('asset_id', $asset->id)
                ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
                ->whereDate('assigned_date', $assignedDate)
                ->whereNull('returned_date')
                ->latest('id')
                ->limit(1)
                ->update(['returned_date' => now()->toDateString()]);

            $asset->update(['status' => AssetStatus::Warehouse]);
        });

        return redirect()
            ->route('assignments.index')
            ->with('success', __('messages.success.assignment_revoked'));
    }

    public function receipt(Assignment $assignment): View
    {
        $assignment->load(['asset', 'employee.department']);

        return view('assignments.receipt', [
            'assignment' => $assignment,
            'receiptNumber' => sprintf('CR-%s-%06d', $assignment->assigned_date->format('Y'), $assignment->id),
            'assignedBy' => auth()->user()->fullName(),
        ]);
    }
}
