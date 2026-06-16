<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::with('department')
            ->withCount('assignments')
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validateEmployee($request));

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم إنشاء الموظف بنجاح.');
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validateEmployee($request, $employee));

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->assignments()->exists()) {
            return back()->withErrors(['employee' => 'لا يمكن حذف موظف لديه عهد نشطة. قم بسحب العهدة أولاً.']);
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateEmployee(Request $request, ?Employee $employee = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'name')->ignore($employee?->id),
            ],
            'department_id' => ['required', 'exists:departments,id'],
        ]);
    }
}
