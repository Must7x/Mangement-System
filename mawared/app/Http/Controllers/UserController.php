<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('assignedRole')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', __('messages.success.user_created'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules($user));

        $user->fill(collect($validated)->except('password')->all());

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', __('messages.success.user_updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => __('messages.errors.user_cannot_delete_self')]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('messages.success.user_deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?User $user = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'employee_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'employee_number')->ignore($user?->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'role_id' => ['required', Rule::exists('roles', 'id')],
        ];
    }
}
