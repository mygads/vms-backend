<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\EmployeeController;

// List all visitors from today (index)
Route::get('/visitor', [VisitorController::class, 'index']);

// Store a new visitor (check-in)
Route::post('/create', [VisitorController::class, 'store']);

// Update an existing visitor to set checkout time
Route::put('/checkout/{visitor_id}', [VisitorController::class, 'update']);

// Print using Html2pdf.js
Route::get('/print/{visitor_id}', [VisitorController::class, 'printVisitor']);

// For display all data without orderBy
Route::get('/index', [VisitorController::class, 'display']);

// List all employee (index)
Route::get('/employee', [EmployeeController::class, 'index']);

// Store a new employee (index)
Route::post('/createemployee', [EmployeeController::class, 'store']);

// Edit employee data
Route::get('/employee/{id}', [EmployeeController::class, 'edit']);

// Update employee data
Route::put('/update/{id}', [EmployeeController::class, 'update']);

// Delete employee data
Route::delete('/delete/{id}', [EmployeeController::class, 'destroy']);

?>
