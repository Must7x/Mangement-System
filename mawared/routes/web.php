<?php

use App\Http\Controllers\AssignmentHistoryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where('locale', 'ar|fr|en');

Route::get('/', function () {
    return auth()->check()
        ? redirect(auth()->user()->homeRoute())
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    });

    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });

    Route::middleware('permission:roles.create')->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('permission:roles.update')->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::middleware('permission:roles.delete')->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    Route::middleware('permission:users.create')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('permission:users.update')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('permission:assets.view')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    });

    Route::middleware('permission:assets.create')->group(function () {
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    });

    Route::middleware('permission:assets.update')->group(function () {
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    });

    Route::middleware('permission:assets.delete')->group(function () {
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });

    Route::middleware('permission:assignments.view')->group(function () {
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    });

    Route::middleware('permission:assignments.create')->group(function () {
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    });

    Route::middleware('permission:assignments.return')->group(function () {
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    });

    Route::middleware('permission:custody_receipts.view')->group(function () {
        Route::get('/assignments/{assignment}/receipt', [AssignmentController::class, 'receipt'])->name('assignments.receipt');
    });

    Route::middleware('permission:assignment_history.view')->group(function () {
        Route::get('/assignment-history', [AssignmentHistoryController::class, 'index'])->name('assignment-history.index');
    });

    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('permission:maintenance.view')->group(function () {
        Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
    });

    Route::middleware('permission:maintenance.create')->group(function () {
        Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
        Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
    });

    Route::middleware('permission:maintenance.update')->group(function () {
        Route::get('/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
        Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
    });

    Route::middleware('permission:maintenance.complete')->group(function () {
        Route::post('maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])
            ->name('maintenances.complete');
    });

    Route::middleware('permission:maintenance.cancel')->group(function () {
        Route::post('maintenances/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])
            ->name('maintenances.cancel');
    });

    Route::middleware('permission:departments.view')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    });

    Route::middleware('permission:departments.create')->group(function () {
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    });

    Route::middleware('permission:departments.update')->group(function () {
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    });

    Route::middleware('permission:departments.delete')->group(function () {
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    Route::middleware('permission:employees.view')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    });

    Route::middleware('permission:employees.create')->group(function () {
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    });

    Route::middleware('permission:employees.update')->group(function () {
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    });

    Route::middleware('permission:employees.delete')->group(function () {
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
});
