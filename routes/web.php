<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentAssignmentController;
use App\Http\Controllers\EquipmentCategoryController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentTypeController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PpeCategoryController;
use App\Http\Controllers\PreventiveMaintenanceController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');

Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

Route::get('/equipment-categories', [EquipmentCategoryController::class, 'index'])->name('equipment-categories.index');

Route::get('/equipment-types', function () {
    return view('equipment-types.index');
})->name('equipment-types.index');

Route::get('/equipment', function () {
    return view('equipment.index');
})->name('equipment.index');

Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');

Route::get('/equipment-assignments', [EquipmentAssignmentController::class, 'index'])->name('equipment-assignments.index');

Route::get('/ppe-categories', [PpeCategoryController::class, 'index'])->name('ppe-categories.index');

Route::get('/preventive-maintenances', [PreventiveMaintenanceController::class, 'index'])->name('preventive-maintenances.index');
Route::get('/preventive-maintenances/create', [PreventiveMaintenanceController::class, 'create'])->name('preventive-maintenances.create');
Route::post('/preventive-maintenances', [PreventiveMaintenanceController::class, 'store'])->name('preventive-maintenances.store');
Route::get('/preventive-maintenances/{preventiveMaintenance}/edit', [PreventiveMaintenanceController::class, 'edit'])->name('preventive-maintenances.edit');
Route::put('/preventive-maintenances/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'update'])->name('preventive-maintenances.update');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/property-acknowledgement-receipt', [ReportController::class, 'par'])->name('reports.par');
Route::get('/reports/property-transfer', [ReportController::class, 'propertyTransfer'])->name('reports.property-transfer');
Route::get('/reports/receipt-of-returned-property', [ReportController::class, 'returnedProperty'])->name('reports.returned-property');
Route::get('/reports/ppe-in-stations', [ReportController::class, 'ppeInStations'])->name('reports.ppe-in-stations');
Route::get('/reports/lost-stolen-damaged-ppe-property', [ReportController::class, 'lostStolenDamaged'])->name('reports.lost-stolen-damaged');
Route::get('/reports/property-tagging', [ReportController::class, 'propertyTagging'])->name('reports.property-tagging');
Route::get('/reports/inventory-custodian-slip', [ReportController::class, 'ics'])->name('reports.ics');
