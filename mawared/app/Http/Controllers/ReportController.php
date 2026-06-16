<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Asset::count(),
            'warehouse' => Asset::where('status', AssetStatus::Warehouse)->count(),
            'active' => Asset::where('status', AssetStatus::Active)->count(),
            'maintenance' => Asset::where('status', AssetStatus::Maintenance)->count(),
            'assignments' => Assignment::count(),
        ];

        $byType = Asset::query()
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $recentAssignments = Assignment::with('asset')
            ->orderByDesc('assigned_date')
            ->limit(10)
            ->get();

        $maintenanceAssets = Asset::where('status', AssetStatus::Maintenance)
            ->orderBy('name')
            ->get();

        return view('reports.index', compact('stats', 'byType', 'recentAssignments', 'maintenanceAssets'));
    }
}
