<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RBAC\CompanyController;
use App\Http\Controllers\RBAC\CustomerController;
use App\Http\Controllers\RBAC\TenantOwnerController;
use App\Http\Controllers\RBAC\DepartmentController;
use App\Http\Controllers\RBAC\HistoryController;
use App\Http\Controllers\RBAC\MasterMenuController;
use App\Http\Controllers\RBAC\PlantController;
use App\Http\Controllers\RBAC\PositionController;
use App\Http\Controllers\RBAC\RoleController;
use App\Http\Controllers\RBAC\MasterVariableController;
use App\Http\Controllers\RBAC\SuratJalanController;
use App\Http\Controllers\RBAC\ReceivingController;
use App\Http\Controllers\RBAC\StockOpnameController;
use App\Http\Controllers\RBAC\StockAdjustmentController;
use App\Http\Controllers\RBAC\InventoryController;
use App\Http\Controllers\RBAC\InventoryCaptureController;
use App\Http\Controllers\RBAC\OutgoingController;
use App\Http\Controllers\RBAC\SectionController;
use App\Http\Controllers\RBAC\UserDataController;
use App\Http\Controllers\RBAC\MasterCustomerController;
use App\Http\Controllers\RBAC\MasterItemController;
use App\Http\Controllers\RBAC\MasterFinanceController;
use App\Http\Controllers\RBAC\EmployeeJobController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'twofactor'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.fetch');
    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markOne'])->name('notifications.mark-one');
    Route::get('/dashboard/chart-data', [\App\Http\Controllers\DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/export-performance', [\App\Http\Controllers\DashboardController::class, 'exportEmployeePerformance'])->name('dashboard.export-performance');
    Route::get('/dashboard/employee-history/{id}', [\App\Http\Controllers\DashboardController::class, 'getEmployeeHistory'])->name('dashboard.employee-history');
});

Route::middleware(['auth', 'twofactor'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/profile/two-factor/prepare', [ProfileController::class, 'prepareTwoFactor'])->name('profile.two-factor.prepare');
    Route::post('/profile/two-factor/enable', [ProfileController::class, 'enableTwoFactor'])->name('profile.two-factor.enable');
    Route::post('/profile/two-factor/disable', [ProfileController::class, 'disableTwoFactor'])->name('profile.two-factor.disable');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/tenant/switch', [App\Http\Controllers\TenantSwitcherController::class, 'switch'])->name('tenant.switch');
});

// RBAC Routes
Route::prefix('rbac')->middleware(['auth', 'twofactor', 'verified', 'permission.check'])->group(function () {
    // Master Menu
    Route::get('/master-menu', [MasterMenuController::class, 'show'])->name('rbac.master-menu');
    Route::get('/master-menu/data', [MasterMenuController::class, 'data'])->name('rbac.master-menu.data');
    Route::get('/master-menu/available-menus', [MasterMenuController::class, 'getAvailableMenus'])->name('rbac.master-menu.available-menus');
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

    // Company (tabs for department/section/position/role/plant)
    Route::get('/company', [CompanyController::class, 'index'])->name('rbac.company');

    // Master Variable
    Route::resource('master-variable', MasterVariableController::class)
        ->except(['create', 'show', 'destroy'])
        ->names([
            'index' => 'rbac.master-variable.index',
            'store' => 'rbac.master-variable.store',
            'edit' => 'rbac.master-variable.edit',
            'update' => 'rbac.master-variable.update',
        ]);

    // Surat Jalan
    Route::resource('surat-jalan', SuratJalanController::class)
        ->except(['create'])
        ->names([
            'index' => 'rbac.surat-jalan.index',
            'store' => 'rbac.surat-jalan.store',
            'show' => 'rbac.surat-jalan.show',
            'edit' => 'rbac.surat-jalan.edit',
            'update' => 'rbac.surat-jalan.update',
            'destroy' => 'rbac.surat-jalan.destroy',
        ]);
    Route::get('surat-jalan/{id}/print', [SuratJalanController::class, 'print'])->name('rbac.surat-jalan.print');

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
    Route::get('/customer/get', [CustomerController::class, 'get'])->name('rbac.customer.get');

    // Tenant Owners
    Route::get('/tenant-owner', [TenantOwnerController::class, 'index'])->name('rbac.tenant-owner');
    Route::post('/tenant-owner', [TenantOwnerController::class, 'store'])->name('rbac.tenant-owner.store');
    Route::get('/tenant-owner/{id}/edit', [TenantOwnerController::class, 'edit'])->name('rbac.tenant-owner.edit');
    Route::put('/tenant-owner/{id}', [TenantOwnerController::class, 'update'])->name('rbac.tenant-owner.update');
    Route::delete('/tenant-owner/{id}', [TenantOwnerController::class, 'destroy'])->name('rbac.tenant-owner.destroy');
    Route::get('/tenant-owner/by-customer', [TenantOwnerController::class, 'getByCustomer'])->name('rbac.tenant-owner.by-customer');
    Route::get('/tenant-owner/all', [TenantOwnerController::class, 'getAll'])->name('rbac.tenant-owner.all');

    // Plant
    Route::get('/plant', [PlantController::class, 'index'])->name('rbac.plant');
    Route::post('/plant', [PlantController::class, 'store'])->name('rbac.plant.store');
    Route::get('/plant/{id}/edit', [PlantController::class, 'edit'])->name('rbac.plant.edit');
    Route::put('/plant/{id}', [PlantController::class, 'update'])->name('rbac.plant.update');
    Route::delete('/plant/{id}', [PlantController::class, 'destroy'])->name('rbac.plant.destroy');

    // Master Customer
    Route::get('/master-customer', [MasterCustomerController::class, 'index'])->name('rbac.master-customer');
    Route::post('/master-customer', [MasterCustomerController::class, 'store'])->name('rbac.master-customer.store');
    Route::get('/master-customer/{id}/edit', [MasterCustomerController::class, 'edit'])->name('rbac.master-customer.edit');
    Route::put('/master-customer/{id}', [MasterCustomerController::class, 'update'])->name('rbac.master-customer.update');
    Route::delete('/master-customer/{id}', [MasterCustomerController::class, 'destroy'])->name('rbac.master-customer.destroy');
    Route::get('/master-customer/create', [MasterCustomerController::class, 'create'])->name('rbac.master-customer.create');
    Route::get('/master-customer/{id}', [MasterCustomerController::class, 'show'])->name('rbac.master-customer.show');

    // Master Item
    Route::get('/master-item', [MasterItemController::class, 'index'])->name('rbac.master-item');
    Route::post('/master-item', [MasterItemController::class, 'store'])->name('rbac.master-item.store');
    Route::get('/master-item/{id}/edit', [MasterItemController::class, 'edit'])->name('rbac.master-item.edit');
    Route::put('/master-item/{id}', [MasterItemController::class, 'update'])->name('rbac.master-item.update');
    Route::delete('/master-item/{id}', [MasterItemController::class, 'destroy'])->name('rbac.master-item.destroy');
    Route::get('/master-item/create', [MasterItemController::class, 'create'])->name('rbac.master-item.create');
    Route::get('/master-item/{id}', [MasterItemController::class, 'show'])->name('rbac.master-item.show');

    // Master Finance
    Route::get('/master-finance', [MasterFinanceController::class, 'index'])->name('rbac.master-finance');
    Route::post('/master-finance', [MasterFinanceController::class, 'store'])->name('rbac.master-finance.store');
    Route::get('/master-finance/{id}/edit', [MasterFinanceController::class, 'edit'])->name('rbac.master-finance.edit');
    Route::put('/master-finance/{id}', [MasterFinanceController::class, 'update'])->name('rbac.master-finance.update');
    Route::delete('/master-finance/{id}', [MasterFinanceController::class, 'destroy'])->name('rbac.master-finance.destroy');

    // Receiving
    Route::get('/receiving', [App\Http\Controllers\RBAC\ReceivingController::class, 'index'])->name('rbac.receiving');
    Route::post('/receiving', [App\Http\Controllers\RBAC\ReceivingController::class, 'store'])->name('rbac.receiving.store');
    Route::get('/receiving/{id}/edit', [App\Http\Controllers\RBAC\ReceivingController::class, 'edit'])->name('rbac.receiving.edit');
    Route::put('/receiving/{id}', [App\Http\Controllers\RBAC\ReceivingController::class, 'update'])->name('rbac.receiving.update');
    Route::delete('/receiving/{id}', [App\Http\Controllers\RBAC\ReceivingController::class, 'destroy'])->name('rbac.receiving.destroy');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('rbac.inventory');
    Route::get('/inventory/history/{id}', [InventoryController::class, 'history'])->name('rbac.inventory.history');
    Route::get('/inventory/capture', [InventoryCaptureController::class, 'index'])->name('rbac.inventory.capture');
    Route::post('/inventory/capture', [InventoryCaptureController::class, 'store'])->name('rbac.inventory.capture.store');
    Route::resource('outgoing', OutgoingController::class)
        ->except(['create', 'show', 'edit', 'update'])
        ->names([
            'index' => 'rbac.outgoing.index',
            'store' => 'rbac.outgoing.store',
            'destroy' => 'rbac.outgoing.destroy',
        ]);
    Route::post('/outgoing/{id}/verify', [OutgoingController::class, 'verify'])->name('rbac.outgoing.verify');

    // Stock Opname
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('rbac.stock-opname');
    Route::post('/stock-opname/update', [StockOpnameController::class, 'update'])->name('rbac.stock-opname.update');
    Route::get('/stock-opname/history/{id}', [StockOpnameController::class, 'history'])->name('rbac.stock-opname.history');
    Route::get('/stock-adjustment', [StockAdjustmentController::class, 'index'])->name('rbac.stock-adjustment');
    Route::post('/stock-adjustment/approve', [StockAdjustmentController::class, 'store'])->name('rbac.stock-adjustment.approve');

    // Employee Jobs
    Route::resource('employee-jobs', EmployeeJobController::class)
        ->except(['create', 'show'])
        ->names([
            'index' => 'rbac.employee-jobs.index',
            'store' => 'rbac.employee-jobs.store',
            'edit' => 'rbac.employee-jobs.edit',
            'update' => 'rbac.employee-jobs.update',
            'destroy' => 'rbac.employee-jobs.destroy',
        ]);

});


require __DIR__ . '/auth.php';
