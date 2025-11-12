<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RBAC\CustomerController;
use App\Http\Controllers\RBAC\TenantOwnerController;
use App\Http\Controllers\RBAC\DepartmentController;
use App\Http\Controllers\RBAC\HistoryController;
use App\Http\Controllers\RBAC\MasterMenuController;
use App\Http\Controllers\RBAC\PlantController;
use App\Http\Controllers\RBAC\PositionController;
use App\Http\Controllers\RBAC\RoleController;
use App\Http\Controllers\RBAC\SectionController;
use App\Http\Controllers\RBAC\UserDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RBAC Routes
Route::prefix('rbac')->middleware(['auth', 'verified', 'permission.check'])->group(function () {
    // Master Menu
    Route::get('/master-menu', [MasterMenuController::class, 'show'])->name('rbac.master-menu');
    Route::get('/master-menu/data', [MasterMenuController::class, 'data'])->name('rbac.master-menu.data');
    Route::post('/master-menu', [MasterMenuController::class, 'store'])->name('rbac.master-menu.store');
    Route::get('/master-menu/{id}/edit', [MasterMenuController::class, 'edit'])->name('rbac.master-menu.edit');
    Route::put('/master-menu/{id}', [MasterMenuController::class, 'update'])->name('rbac.master-menu.update');
    Route::delete('/master-menu/{id}', [MasterMenuController::class, 'destroy'])->name('rbac.master-menu.destroy');

    // User Data
    Route::get('/user-data', [UserDataController::class, 'index'])->name('rbac.user-data');
    Route::post('/user-data', [UserDataController::class, 'store'])->name('rbac.user-data.store');
    Route::get('/user-data/{id}/edit', [UserDataController::class, 'edit'])->name('rbac.user-data.edit');
    Route::put('/user-data/{id}', [UserDataController::class, 'update'])->name('rbac.user-data.update');
    Route::delete('/user-data/{id}', [UserDataController::class, 'destroy'])->name('rbac.user-data.destroy');

    // Role
    Route::get('/role', [App\Http\Controllers\RBAC\RoleController::class, 'index'])->name('rbac.role');
    Route::post('/role', [App\Http\Controllers\RBAC\RoleController::class, 'store'])->name('rbac.role.store');
    Route::get('/role/{id}/edit', [App\Http\Controllers\RBAC\RoleController::class, 'edit'])->name('rbac.role.edit');
    Route::put('/role/{id}', [App\Http\Controllers\RBAC\RoleController::class, 'update'])->name('rbac.role.update');
    Route::delete('/role/{id}', [App\Http\Controllers\RBAC\RoleController::class, 'destroy'])->name('rbac.role.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('rbac.history');
    Route::get('/history/data', [HistoryController::class, 'data'])->name('rbac.history.data');

    // Department
    Route::get('/department', [DepartmentController::class, 'index'])->name('rbac.department');
    Route::post('/department', [DepartmentController::class, 'store'])->name('rbac.department.store');
    Route::get('/department/{id}/edit', [DepartmentController::class, 'edit'])->name('rbac.department.edit');
    Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('rbac.department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('rbac.department.destroy');
    Route::get('rbac/departments/all', [DepartmentController::class, 'getAllDepartments'])->name('rbac.departments.all');

    // Section
    Route::get('/section', [SectionController::class, 'index'])->name('rbac.section');
    Route::post('/section', [SectionController::class, 'store'])->name('rbac.section.store');
    Route::get('/section/{id}/edit', [SectionController::class, 'edit'])->name('rbac.section.edit');
    Route::put('/section/{id}', [SectionController::class, 'update'])->name('rbac.section.update');
    Route::delete('/section/{id}', [SectionController::class, 'destroy'])->name('rbac.section.destroy');
    Route::get('rbac/sections/all', [SectionController::class, 'getAllSections'])->name('rbac.sections.all');
    Route::get('rbac/departments/by-customer/{customer_id}', [DepartmentController::class, 'getByCustomer'])->name('rbac.departments.by-customer');
    Route::get('rbac/sections/by-customer/{customer_id}', [SectionController::class, 'getByCustomer'])->name('rbac.sections.by-customer');
    Route::get('rbac/sections/by-department/{dept_id}', [SectionController::class, 'getByDepartment'])->name('rbac.sections.by-department');

    // Position
    Route::get('/position', [PositionController::class, 'index'])->name('rbac.position');
    Route::post('/position', [PositionController::class, 'store'])->name('rbac.position.store');
    Route::get('/position/{id}/edit', [PositionController::class, 'edit'])->name('rbac.position.edit');
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('rbac.position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('rbac.position.destroy');
    Route::get('rbac/positions/by-section/{section_id}', [PositionController::class, 'getBySection'])->name('rbac.positions.by-section');
    Route::get('rbac/roles/by-customer/{customer_id}', [RoleController::class, 'getByCustomer'])->name('rbac.roles.by-customer');

    // Tenant List (formerly customer)
    Route::get('/customer', [CustomerController::class, 'index'])->name('rbac.customer');
    Route::post('/customer', [CustomerController::class, 'store'])->name('rbac.customer.store');
    Route::get('/customer/{id}/edit', [CustomerController::class, 'edit'])->name('rbac.customer.edit');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('rbac.customer.update');
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('rbac.customer.destroy');

    // Tenant Owners
    Route::get('/tenant-owner', [TenantOwnerController::class, 'index'])->name('rbac.tenant-owner');
    Route::post('/tenant-owner', [TenantOwnerController::class, 'store'])->name('rbac.tenant-owner.store');
    Route::get('/tenant-owner/{id}/edit', [TenantOwnerController::class, 'edit'])->name('rbac.tenant-owner.edit');
    Route::put('/tenant-owner/{id}', [TenantOwnerController::class, 'update'])->name('rbac.tenant-owner.update');
    Route::delete('/tenant-owner/{id}', [TenantOwnerController::class, 'destroy'])->name('rbac.tenant-owner.destroy');

    // Plant
    Route::get('/plant', [PlantController::class, 'index'])->name('rbac.plant');
    Route::post('/plant', [PlantController::class, 'store'])->name('rbac.plant.store');
    Route::get('/plant/{id}/edit', [PlantController::class, 'edit'])->name('rbac.plant.edit');
    Route::put('/plant/{id}', [PlantController::class, 'update'])->name('rbac.plant.update');
    Route::delete('/plant/{id}', [PlantController::class, 'destroy'])->name('rbac.plant.destroy');


});
require __DIR__.'/auth.php';
