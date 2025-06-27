<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\PropertyImageController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/properties', [PropertyController::class, 'index']);

// Ruta pública específica para ver propiedades (DEBE IR ANTES DEL GRUPO PROTEGIDO)
Route::get('/properties/details/{id}', [PropertyController::class, 'show']); // 👈 Cambiado el path

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas de propiedades protegidas
    Route::get('/properties/mine', [PropertyController::class, 'myProperties']);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    // Otras rutas protegidas
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
    Route::post('/properties/{property}/images', [PropertyImageController::class, 'store']);
});


// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\PropertyController;
// use App\Http\Controllers\Api\FavoriteController;
// use App\Http\Controllers\Api\PropertyImageController;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// */

// // Rutas públicas
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/properties', [PropertyController::class, 'index']); // 👈 Nueva ruta pública


// // Rutas protegidas (requieren autenticación)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
//     Route::post('/logout', [AuthController::class, 'logout']);

//     // Rutas de propiedades protegidas
//     Route::get('/properties/mine', [PropertyController::class, 'myProperties']);
//     Route::get('/properties/{id}', [PropertyController::class, 'show']);
//     Route::post('/properties', [PropertyController::class, 'store']); // Opcional: puedes hacerla pública también
//     Route::put('/properties/{id}', [PropertyController::class, 'update']);
//     Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

//     // Otras rutas protegidas
//     Route::get('/favorites', [FavoriteController::class, 'index']); // Nuevo endpoint
//     Route::post('/favorites', [FavoriteController::class, 'store']);
//     Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
//     Route::post('/properties/{property}/images', [PropertyImageController::class, 'store']);
// });