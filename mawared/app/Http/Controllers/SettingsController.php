<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $permissionGroups = Permission::query()
            ->orderBy('group')
            ->orderBy('slug')
            ->get()
            ->groupBy('group');

        return view('settings.index', [
            'user' => auth()->user(),
            'permissionGroups' => $permissionGroups,
        ]);
    }
}
