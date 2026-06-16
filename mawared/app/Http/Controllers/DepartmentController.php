<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $departments = Department::withCount('employees')->orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
        ]);

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم إنشاء القسم بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($department->id),
            ],
        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم تحديث القسم بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return back()->withErrors(['department' => 'لا يمكن حذف القسم نظراً لوجود موظفين مسجلين به.']);
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم حذف القسم بنجاح.');
    }
}
