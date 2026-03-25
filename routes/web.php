<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
Route::get('/employees/api/search', [EmployeeController::class, 'ajaxSearch'])->name('employees.ajax-search');
Route::get('/employees/photo/{id}', [EmployeeController::class, 'showPhoto'])->name('employees.show-photo');
Route::get('/employees/download-photo/{id}', [EmployeeController::class, 'downloadPhoto'])->name('employees.download-photo');
Route::get('/employees/download-department/{department_id}', [EmployeeController::class, 'downloadByDepartment'])->name('employees.download-department');
