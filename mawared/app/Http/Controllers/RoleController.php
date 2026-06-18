<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create', [
            'role' => new Role,
            'permissionGroups' => $this->groupedPermissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRole($request);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', __('messages.success.role_created'));
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->groupedPermissions(),
            'selectedPermissions' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $this->validateRole($request, $role);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', __('messages.success.role_updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->withErrors(['role' => __('messages.errors.role_cannot_delete_system')]);
        }

        if ($role->isInUse()) {
            return back()->withErrors(['role' => __('messages.errors.role_cannot_delete_in_use')]);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', __('messages.success.role_deleted'));
    }

    /**
     * @return array<string, \Illuminate\Support\Collection<int, Permission>>
     */
    private function groupedPermissions(): array
    {
        return Permission::query()
            ->orderBy('group')
            ->orderBy('slug')
            ->get()
            ->groupBy('group')
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Role::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug ?: 'role';
    }
}
