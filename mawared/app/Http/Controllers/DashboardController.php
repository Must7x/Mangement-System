<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\Maintenance;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Asset::count(),
            'warehouse' => Asset::where('status', AssetStatus::Warehouse)->count(),
            'active' => Asset::where('status', AssetStatus::Active)->count(),
            'maintenance' => Asset::where('status', AssetStatus::Maintenance)->count(),
            'open_maintenances' => Maintenance::query()->open()->count(),
            'assignments' => Assignment::count(),
        ];

        $recentAssets = Asset::with('assignment')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentAssets'));
    }
}
