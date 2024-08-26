<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LoginController;

Route::get('test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Rutas que no requieren autenticación
Route::post('login', [LoginController::class, 'login']);          // Ruta para iniciar sesión
Route::post('register', [LoginController::class, 'register']);    // Ruta para registrar usuarios
Route::post('/check-email', [LoginController::class, 'checkEmail']);


// Proteger las rutas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para la gestión de mascotas (Pets)
    Route::apiResource('pets', PetController::class);

    // Rutas para la gestión de categorías (Categories)
    Route::apiResource('categories', CategoryController::class);

    // Rutas para la gestión de estados (States)
    Route::apiResource('states', StateController::class);

    // Rutas adicionales si decides utilizarlas
    Route::get('users', [LoginController::class, 'index']);           // Ruta para listar todos los usuarios
    Route::get('users/{id}', [LoginController::class, 'show']);       // Ruta para mostrar un usuario por ID
    Route::put('users/{id}', [LoginController::class, 'update']);     // Ruta para actualizar un usuario por ID
    Route::delete('users/{id}', [LoginController::class, 'destroy']); // Ruta para eliminar un usuario por ID
});
