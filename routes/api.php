<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\CategoryController;

Route::get('test', function () {
    return response()->json(['message' => 'API is working!']);
});


// Definir las rutas para el controlador PetController
Route::apiResource('pets', PetController::class);

// Definir las rutas para el controlador CategoryController
Route::apiResource('categories', CategoryController::class);
