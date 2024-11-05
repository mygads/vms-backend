<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitorController;

// List all visitors from today (index)
Route::get('/visitor', [VisitorController::class, 'index']);

// Store a new visitor (check-in)
Route::post('/create', [VisitorController::class, 'store']);

// Update an existing visitor to set checkout time
Route::put('/checkout/{visitor_id}', [VisitorController::class, 'update']);

// Print using Puppeteer
Route::get('/print/{visitor_id}', [VisitorController::class, 'printVisitor']);

// For get visitor data specific by id
Route::get('/visitor/{id}', [VisitorController::class, 'show']);

// For display all data without orderBy
Route::get('/index', [VisitorController::class, 'display']);

?>
