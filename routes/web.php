<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
Route::get('/api/search', [EmployeeController::class, 'ajaxSearch'])->name('employees.ajax-search');
Route::get('/download-photo/{id}', [EmployeeController::class, 'downloadPhoto'])->name('employees.download-photo');
Route::get('/download-department/{department_id}', [EmployeeController::class, 'downloadByDepartment'])->name('employees.download-department');
