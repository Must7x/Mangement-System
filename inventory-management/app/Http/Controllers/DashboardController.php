<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total' => Asset::count(),
            'warehouse' => Asset::where('status', AssetStatus::Warehouse)->count(),
            'active' => Asset::where('status', AssetStatus::Active)->count(),
            'maintenance' => Asset::where('status', AssetStatus::Maintenance)->count(),
        ];

        $assets = Asset::with('assignment')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('type', 'like', $term)
                        ->orWhere('serial_number', 'like', $term);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard', [
            'stats' => $stats,
            'assets' => $assets,
            'statuses' => AssetStatus::cases(),
            'filters' => $request->only(['q', 'status']),
        ]);
    }
}
