<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MkProjectsController;

Route::get('/', function () {
    return view('welcome');
    
});
Route::get('/mk/projects', [MkProjectsController::class, 'index']);