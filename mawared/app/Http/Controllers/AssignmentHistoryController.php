<?php

namespace App\Http\Controllers;

use App\Models\AssignmentHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $histories = AssignmentHistory::with(['asset', 'employee.department'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('employee_name', 'like', $term)
                        ->orWhere('department_name', 'like', $term)
                        ->orWhereHas('asset', fn ($aq) => $aq->where('name', 'like', $term)
                            ->orWhere('serial_number', 'like', $term));
                });
            })
            ->orderByDesc('assigned_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('assignment-history.index', [
            'histories' => $histories,
            'filters' => $request->only(['q']),
        ]);
    }
}
