<?php

use App\Http\Controllers\LOI\EstimateNewItemController;
use App\Http\Controllers\LOI\TeamFeasibilityCommitmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RBAC\DepartmentController;
use App\Http\Controllers\RBAC\HistoryController;
use App\Http\Controllers\RBAC\MasterMenuController;
use App\Http\Controllers\RBAC\PlantController;
use App\Http\Controllers\RBAC\PositionController;
use App\Http\Controllers\RBAC\SectionController;
use App\Http\Controllers\RBAC\UserDataController;
use App\Http\Controllers\UPLOAD\DrawingController;
use App\Http\Controllers\UPLOAD\LOIExternalController;
use App\Http\Controllers\UPLOAD\MeetingController;
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
Route::prefix('rbac')->middleware(['auth', 'verified'])->group(function () {
    // Calendar Pitching
    Route::get('/calendar-pitching', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'index'])->name('rbac.calendar-pitching.index');
    Route::get('/calendar-pitching/data', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'index'])->name('rbac.calendar-pitching.data');
    Route::post('/calendar-pitching', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'store'])->name('rbac.calendar-pitching.store');
    Route::get('/calendar-pitching/{id}', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'show'])->name('rbac.calendar-pitching.show');
    Route::get('/calendar-pitching/{id}/edit', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'edit'])->name('rbac.calendar-pitching.edit');
    Route::put('/calendar-pitching/{id}', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'update'])->name('rbac.calendar-pitching.update');
    Route::delete('/calendar-pitching/{id}', [App\Http\Controllers\RBAC\CalendarPitchingController::class, 'destroy'])->name('rbac.calendar-pitching.destroy');

    // MoM Customer
    Route::get('/mom-customer', [App\Http\Controllers\RBAC\MomCustomerController::class, 'index'])->name('rbac.mom-customer.index');
    Route::get('/mom-customer/data', [App\Http\Controllers\RBAC\MomCustomerController::class, 'index'])->name('rbac.mom-customer.data');
    Route::post('/mom-customer', [App\Http\Controllers\RBAC\MomCustomerController::class, 'store'])->name('rbac.mom-customer.store');
    Route::get('/mom-customer/{id}', [App\Http\Controllers\RBAC\MomCustomerController::class, 'show'])->name('rbac.mom-customer.show');
    Route::get('/mom-customer/{id}/edit', [App\Http\Controllers\RBAC\MomCustomerController::class, 'edit'])->name('rbac.mom-customer.edit');
    Route::put('/mom-customer/{id}', [App\Http\Controllers\RBAC\MomCustomerController::class, 'update'])->name('rbac.mom-customer.update');
    Route::delete('/mom-customer/{id}', [App\Http\Controllers\RBAC\MomCustomerController::class, 'destroy'])->name('rbac.mom-customer.destroy');
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
    Route::get('rbac/sections/by-department/{dept_id}', [SectionController::class, 'getByDepartment'])->name('rbac.sections.by-department');

    // Position
    Route::get('/position', [PositionController::class, 'index'])->name('rbac.position');
    Route::post('/position', [PositionController::class, 'store'])->name('rbac.position.store');
    Route::get('/position/{id}/edit', [PositionController::class, 'edit'])->name('rbac.position.edit');
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('rbac.position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('rbac.position.destroy');
    Route::get('rbac/positions/by-section/{section_id}', [PositionController::class, 'getBySection'])->name('rbac.positions.by-section');

    // Plant
    Route::get('/plant', [PlantController::class, 'index'])->name('rbac.plant');
    Route::post('/plant', [PlantController::class, 'store'])->name('rbac.plant.store');
    Route::get('/plant/{id}/edit', [PlantController::class, 'edit'])->name('rbac.plant.edit');
    Route::put('/plant/{id}', [PlantController::class, 'update'])->name('rbac.plant.update');
    Route::delete('/plant/{id}', [PlantController::class, 'destroy'])->name('rbac.plant.destroy');

    // Customer
    Route::get('/customer', [App\Http\Controllers\RBAC\CustomerController::class, 'index'])->name('rbac.customer');
    Route::get('/customer/get-users', [App\Http\Controllers\RBAC\CustomerController::class, 'getUsers'])->name('rbac.customer.getUsers');
    Route::post('/customer', [App\Http\Controllers\RBAC\CustomerController::class, 'store'])->name('rbac.customer.store');
    Route::get('/customer/{id}', [App\Http\Controllers\RBAC\CustomerController::class, 'show'])->name('rbac.customer.show');
    Route::get('/customer/{id}/edit', [App\Http\Controllers\RBAC\CustomerController::class, 'edit'])->name('rbac.customer.edit');
    Route::put('/customer/{id}', [App\Http\Controllers\RBAC\CustomerController::class, 'update'])->name('rbac.customer.update');
    Route::delete('/customer/{id}', [App\Http\Controllers\RBAC\CustomerController::class, 'destroy'])->name('rbac.customer.destroy');

    // Subscription
    Route::get('/subscription', [App\Http\Controllers\RBAC\SubscriptionController::class, 'index'])->name('rbac.subscription.index');
    Route::get('/subscription/data', [App\Http\Controllers\RBAC\SubscriptionController::class, 'index'])->name('rbac.subscription.data'); // For DataTables AJAX
    Route::post('/subscription', [App\Http\Controllers\RBAC\SubscriptionController::class, 'store'])->name('rbac.subscription.store');
    Route::get('/subscription/{id}', [App\Http\Controllers\RBAC\SubscriptionController::class, 'show'])->name('rbac.subscription.show');
    Route::get('/subscription/{id}/edit', [App\Http\Controllers\RBAC\SubscriptionController::class, 'edit'])->name('rbac.subscription.edit');
    Route::post('/subscription/{id}', [App\Http\Controllers\RBAC\SubscriptionController::class, 'update'])->name('rbac.subscription.update'); // Using POST for update with file upload
    Route::delete('/subscription/{id}', [App\Http\Controllers\RBAC\SubscriptionController::class, 'destroy'])->name('rbac.subscription.destroy');

    // Monitoring
    Route::get('/monitoring', [App\Http\Controllers\RBAC\MonitoringController::class, 'index'])->name('rbac.monitoring');
    Route::get('/monitoring/chart-data', [App\Http\Controllers\RBAC\MonitoringController::class, 'getChartData'])->name('rbac.monitoring.chart-data');
    Route::get('/monitoring/dashboard-chart-data', [App\Http\Controllers\RBAC\MonitoringController::class, 'getDashboardChartData'])->name('rbac.monitoring.dashboard-chart-data');
    Route::get('/monitoring/process-tasks', [App\Http\Controllers\RBAC\MonitoringController::class, 'getProcessingTasks'])->name('rbac.monitoring.process-tasks');
    Route::get('/monitoring/failed-tasks', [App\Http\Controllers\RBAC\MonitoringController::class, 'getFailedTasks'])->name('rbac.monitoring.failed-tasks');

    // POS Monitoring
    Route::get('/pos-monitoring', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'index'])->name('rbac.pos-monitoring');
    Route::post('/pos-monitoring', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'store'])->name('rbac.pos-monitoring.store');
    Route::post('/pos-monitoring/sync-data', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'syncData'])->name('rbac.pos-monitoring.sync-data');
    Route::get('/pos-monitoring/get-pos-data', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'getPosData'])->name('rbac.pos-monitoring.get-pos-data');
    Route::delete('/pos-monitoring/delete-queue', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'deleteQueue'])->name('rbac.pos-monitoring.delete-queue');
    Route::get('/pos-monitoring/report/{posQueueId}', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'report'])->name('rbac.pos-monitoring.report');
    Route::get('/pos-monitoring/report/{posQueueId}/data', [App\Http\Controllers\RBAC\PosMonitoringController::class, 'getReportData'])->name('rbac.pos-monitoring.report.data');

    // Invoice Monitoring
    Route::get('/invoice-monitoring', [App\Http\Controllers\RBAC\InvoiceMonitoringController::class, 'index'])->name('rbac.invoice-monitoring');
    Route::get('/invoice-monitoring/data', [App\Http\Controllers\RBAC\InvoiceMonitoringController::class, 'getData'])->name('rbac.invoice-monitoring.data');
    Route::get('/invoice-monitoring/history', [App\Http\Controllers\RBAC\InvoiceMonitoringController::class, 'getHistory'])->name('rbac.invoice-monitoring.history');
    Route::put('/invoice-monitoring/{id}', [App\Http\Controllers\RBAC\InvoiceMonitoringController::class, 'update'])->name('rbac.invoice-monitoring.update');
});

// UPLOAD Routes
Route::prefix('upload')->middleware(['auth', 'verified'])->group(function () {
    // Drawing
    Route::get('/drawing', [DrawingController::class, 'index'])->name('upload.drawing');
    Route::post('/drawing', [DrawingController::class, 'store'])->name('upload.drawing.store');
    Route::delete('/drawing/{id}', [DrawingController::class, 'destroy'])->name('upload.drawing.destroy');
    Route::get('/drawing/{partId}', [DrawingController::class, 'show'])->name('upload.drawing.show');

    Route::get('/loi-external', [LOIExternalController::class, 'index'])->name('upload.loi-external');
    Route::post('/loi-external', [LOIExternalController::class, 'store'])->name('upload.loi-external.store');
    Route::delete('/loi-external/{id}', [LOIExternalController::class, 'destroy'])->name('upload.loi-external.destroy');
    Route::get('/loi-external/{partId}', [LOIExternalController::class, 'show'])->name('upload.loi-external.show');

    Route::get('/meeting', [MeetingController::class, 'index'])->name('upload.meeting');
    Route::post('/meeting', [MeetingController::class, 'store'])->name('upload.meeting.store');
    Route::delete('/meeting/{id}', [MeetingController::class, 'destroy'])->name('upload.meeting.destroy');
    Route::get('/meeting/{partId}', [MeetingController::class, 'show'])->name('upload.meeting.show');
});

Route::prefix('loi')->middleware(['auth', 'verified'])->group(function () {
    // AJAX endpoints for form data - HARUS DI ATAS route {id}
    Route::get('/estimate-new-item/ajax/customers', [EstimateNewItemController::class, 'getCustomers'])->name('loi.estimate-new-item.customers');
    Route::get('/estimate-new-item/ajax/parts', [EstimateNewItemController::class, 'getParts'])->name('loi.estimate-new-item.parts');
    Route::get('/estimate-new-item/ajax/part-details', [EstimateNewItemController::class, 'getPartDetails'])->name('loi.estimate-new-item.part-details');
    Route::get('/estimate-new-item/ajax/materials', [EstimateNewItemController::class, 'getMaterials'])->name('loi.estimate-new-item.materials');
    Route::get('/estimate-new-item/ajax/material-details', [EstimateNewItemController::class, 'getMaterialDetails'])->name('loi.estimate-new-item.material-details');
    Route::get('/estimate-new-item/ajax/additional-parts', [EstimateNewItemController::class, 'getAdditionalPartsByCustomer'])->name('loi.estimate-new-item.additional-parts');
    Route::get('/estimate-new-item/ajax/additional-part-details', [EstimateNewItemController::class, 'getAdditionalPartDetails'])->name('loi.estimate-new-item.additional-part-details');
    Route::get('/estimate-new-item/ajax/machines', [EstimateNewItemController::class, 'getMachines'])->name('loi.estimate-new-item.machines');
    Route::get('/estimate-new-item/ajax/users', [EstimateNewItemController::class, 'getUsers'])->name('loi.estimate-new-item.users');

    // Estimate New Item
    Route::get('/estimate-new-item', [EstimateNewItemController::class, 'index'])->name('loi.estimate-new-item');
    Route::get('/estimate-new-item/create', [EstimateNewItemController::class, 'create'])->name('loi.estimate-new-item.create');
    Route::get('/estimate-new-item/data', [EstimateNewItemController::class, 'getData'])->name('loi.estimate-new-item.data');
    Route::post('/estimate-new-item', [EstimateNewItemController::class, 'store'])->name('loi.estimate-new-item.store');

    // Routes dengan parameter {id} - HARUS DI BAWAH route spesifik
    Route::get('/estimate-new-item/{id}/edit', [EstimateNewItemController::class, 'edit'])->name('loi.estimate-new-item.edit');
    Route::get('/estimate-new-item/{id}', [EstimateNewItemController::class, 'show'])->name('loi.estimate-new-item.show');
    Route::put('/estimate-new-item/{id}', [EstimateNewItemController::class, 'update'])->name('loi.estimate-new-item.update');
    Route::delete('/estimate-new-item/{id}', [EstimateNewItemController::class, 'destroy'])->name('loi.estimate-new-item.destroy');

    // Team Feasibility Commitment - AJAX endpoints first
    Route::get('/feasability-commitment/ajax/parts', [TeamFeasibilityCommitmentController::class, 'getRfqParts'])->name('loi.feasability-commitment.parts');
    Route::get('/feasability-commitment/ajax/part-details', [TeamFeasibilityCommitmentController::class, 'getPartDetails'])->name('loi.feasability-commitment.part-details');
    Route::get('/feasability-commitment/ajax/users', [TeamFeasibilityCommitmentController::class, 'getUsers'])->name('loi.feasability-commitment.users');

    // Team Feasibility Commitment - Main routes
    Route::get('/feasability-commitment', [TeamFeasibilityCommitmentController::class, 'index'])->name('loi.feasability-commitment.index');
    Route::get('/feasability-commitment/create', [TeamFeasibilityCommitmentController::class, 'create'])->name('loi.feasability-commitment.create');
    Route::get('/feasability-commitment/data', [TeamFeasibilityCommitmentController::class, 'getData'])->name('loi.feasability-commitment.data');
    Route::post('/feasability-commitment', [TeamFeasibilityCommitmentController::class, 'store'])->name('loi.feasability-commitment.store');
    Route::get('/feasability-commitment/{id}/edit', [TeamFeasibilityCommitmentController::class, 'edit'])->name('loi.feasability-commitment.edit');
    Route::get('/feasability-commitment/{id}', [TeamFeasibilityCommitmentController::class, 'show'])->name('loi.feasability-commitment.show');
    Route::put('/feasability-commitment/{id}', [TeamFeasibilityCommitmentController::class, 'update'])->name('loi.feasability-commitment.update');
    Route::delete('/feasability-commitment/{id}', [TeamFeasibilityCommitmentController::class, 'destroy'])->name('loi.feasability-commitment.destroy');

    // Tooling Inspection - AJAX endpoints first
    Route::get('/tooling-inspection/ajax/master-inspection-details/{id}', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'getMasterInspectionDetails'])->name('loi.tooling-inspection.master-inspection-details');

    // Tooling Inspection - Main routes
    Route::get('/tooling-inspection', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'index'])->name('loi.tooling-inspection.index');
    Route::get('/tooling-inspection/create', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'create'])->name('loi.tooling-inspection.create');
    Route::get('/tooling-inspection/data', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'getData'])->name('loi.tooling-inspection.data');
    Route::post('/tooling-inspection', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'store'])->name('loi.tooling-inspection.store');
    Route::get('/tooling-inspection/{id}/edit', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'edit'])->name('loi.tooling-inspection.edit');
    Route::get('/tooling-inspection/{id}', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'show'])->name('loi.tooling-inspection.show');
    Route::put('/tooling-inspection/{id}', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'update'])->name('loi.tooling-inspection.update');
    Route::delete('/tooling-inspection/{id}', [App\Http\Controllers\LOI\ToolingInspectionController::class, 'destroy'])->name('loi.tooling-inspection.destroy');

    //LOI Internal
     Route::get('/internals', [App\Http\Controllers\LOI\LOIInternalController::class, 'index'])->name('loi.internals.index');
     Route::get('/internals/data', [App\Http\Controllers\LOI\LOIInternalController::class, 'getData'])->name('loi.internals.data');
     Route::get('/internals/create', [App\Http\Controllers\LOI\LOIInternalController::class, 'create'])->name('loi.internals.create');
     Route::post('/internals', [App\Http\Controllers\LOI\LOIInternalController::class, 'store'])->name('loi.internals.store');
     Route::get('/internals/parts', [App\Http\Controllers\LOI\LOIInternalController::class, 'getParts'])->name('loi.internals.parts');
     Route::get('/internals/{id}/hasil-meeting-details', [App\Http\Controllers\LOI\LOIInternalController::class, 'getHasilMeetingDetails'])->name('loi.internals.hasil-meeting-details');
     Route::get('/internals/{id}/loi-external-details', [App\Http\Controllers\LOI\LOIInternalController::class, 'getLoiExternalDetails'])->name('loi.internals.loi-external-details');
});

require __DIR__.'/auth.php';
