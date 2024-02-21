<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::middleware('auth:sanctum')
            ->post('/logout', 'logout');
    });


Route::controller(LinkController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/links', 'index');
        Route::get('/links/{link}', 'show');
        Route::post('/links', 'store');
        Route::patch('/links/{link}', 'update');
        Route::delete('/links/{link}', 'destroy');
    });
