<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
                'user' => $user, // No cargar 'profile_photo_url' como relación
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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validar la imagen
        ]);

        // Subir la foto de perfil si se proporciona
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Crear un nuevo usuario con los datos validados
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_photo' => $profilePhotoPath, // Guardar la ruta de la foto de perfil
        ]);

        // Generar un token para el usuario recién creado
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'user' => $user, // No cargar 'profile_photo_url' como relación
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

    /**
     * Verificar si un correo electrónico ya está en uso.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    // Métodos para la gestión de usuarios

    /**
     * Listar todos los usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Mostrar un usuario por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

    /**
     * Actualizar un usuario por ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'profile_photo' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048', // Validar la imagen
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Subir la nueva foto de perfil si se proporciona
        if ($request->hasFile('profile_photo')) {
            // Eliminar la foto de perfil anterior si existe
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Actualiza solo los campos que han sido validados
        $user->fill($validated);
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return response()->json($user);
    }

    /**
     * Eliminar un usuario por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        // Eliminar la foto de perfil si existe
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }
}
