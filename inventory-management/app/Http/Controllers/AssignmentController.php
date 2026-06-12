<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(): View
    {
        $assignments = Assignment::with('asset')
            ->orderByDesc('assigned_date')
            ->get();

        $warehouseAssets = Asset::where('status', AssetStatus::Warehouse)
            ->orderBy('name')
            ->get();

        return view('assignments.index', compact('assignments', 'warehouseAssets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'employee_name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'assigned_date' => ['required', 'date'],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $asset = Asset::query()
                    ->whereKey($validated['asset_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($asset->status !== AssetStatus::Warehouse) {
                    throw new \RuntimeException('الجهاز غير متاح في المخزن.');
                }

                if ($asset->assignment()->exists()) {
                    throw new \RuntimeException('يوجد عهدة نشطة على هذا الجهاز.');
                }

                Assignment::create($validated);

                $asset->update(['status' => AssetStatus::Active]);
            });
        } catch (\RuntimeException $exception) {
            return back()
                ->withErrors(['asset_id' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('assignments.index')
            ->with('success', 'تم تخصيص العهدة بنجاح.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        DB::transaction(function () use ($assignment): void {
            $asset = Asset::query()
                ->whereKey($assignment->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $assignment->delete();

            $asset->update(['status' => AssetStatus::Warehouse]);
        });

        return redirect()
            ->route('assignments.index')
            ->with('success', 'تم سحب العهدة وإرجاع الجهاز إلى المخزن.');
    }
}
