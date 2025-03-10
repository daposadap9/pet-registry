<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;

Route::get('test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Rutas que no requieren autenticación
Route::post('login', [LoginController::class, 'login']);          // Ruta para iniciar sesión
Route::post('register', [LoginController::class, 'register']);    // Ruta para registrar usuarios
Route::post('/check-email', [LoginController::class, 'checkEmail']);

// Rutas para la gestión de categorías (Categories)
Route::apiResource('categories', CategoryController::class);

// Rutas para la gestión de estados (States)
Route::apiResource('states', StateController::class);

// Proteger las rutas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para la gestión de mascotas (Pets)
    Route::apiResource('pets', PetController::class);

    // Rutas adicionales para la gestión de usuarios
    Route::get('users', [LoginController::class, 'index']);           // Ruta para listar todos los usuarios
    Route::get('users/{id}', [LoginController::class, 'show']);       // Ruta para mostrar un usuario por ID
    Route::put('users/{id}', [LoginController::class, 'update']);     // Ruta para actualizar un usuario por ID
    Route::delete('users/{id}', [LoginController::class, 'destroy']); // Ruta para eliminar un usuario por ID

    // Rutas para la gestión de conversaciones (Chat)
    Route::get('conversations', [ConversationController::class, 'index']);  // Obtener todas las conversaciones del usuario autenticado
    Route::post('conversations', [ConversationController::class, 'store']); // Crear una nueva conversación
    Route::delete('conversations/{id}', [ConversationController::class, 'destroy']); // Eliminar una conversación

   // Rutas para la gestión de mensajes en una conversación específica
    Route::get('conversations/{conversationId}/messages', [MessageController::class, 'index']);
    Route::get('conversations/{conversationId}/messages/{messageId}', [MessageController::class, 'show']);
    Route::post('conversations/{conversationId}/messages', [MessageController::class, 'store']);
    Route::delete('conversations/{conversationId}/messages/{messageId}', [MessageController::class, 'destroy']);
    Route::put('conversations/{conversationId}/messages/{messageId}', [MessageController::class, 'update']);
});
