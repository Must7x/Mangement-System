<?php

namespace App\Http\Controllers;

use App\Enums\ActivityAction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with(['user.assignedRole', 'asset'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('user_name', 'like', $term)
                        ->orWhere('user_role', 'like', $term)
                        ->orWhereHas('asset', fn ($aq) => $aq->where('name', 'like', $term)
                            ->orWhere('serial_number', 'like', $term));
                });
            })
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->string('action')))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('activity-log.index', [
            'logs' => $logs,
            'filters' => $request->only(['q', 'action']),
            'actions' => ActivityAction::options(),
        ]);
    }
}
