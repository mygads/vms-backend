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

// List all employees
Route::get('/employee', [EmployeeController::class, 'index']);

// Store a new employee
Route::post('/createemployee', [EmployeeController::class, 'store']);

// Show employee data by name
Route::get('/edit/{name}', [EmployeeController::class, 'show']);

// Update employee data by name
Route::put('/update/{name}', [EmployeeController::class, 'update']);

// Delete employee data by name
Route::delete('/delete/{name}', [EmployeeController::class, 'destroy']);

?>
