<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Manejar la solicitud de inicio de sesión del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $credentials = $request->only('email', 'password');

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
    }

    /**
     * Manejar la solicitud de registro del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear un nuevo usuario con los datos validados
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generar un token para el usuario recién creado
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Manejar la solicitud de cierre de sesión del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Eliminar todos los tokens del usuario
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso'], 200);
    }
    public function checkEmail(Request $request)
    {
    $request->validate([
        'email' => 'required|string|email|max:255',
    ]);

    $exists = User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
    }

}
