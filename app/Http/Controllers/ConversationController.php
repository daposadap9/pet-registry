<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;

class ConversationController extends Controller
{
    /**
     * Obtener todas las conversaciones del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = auth()->id();
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo'])
            ->get();

        return response()->json($conversations);
    }

    /**
     * Crear una nueva conversación o devolver una existente.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        $userId = auth()->id();
        $participantId = $request->participants[0]; // Asumiendo que es una conversación uno a uno

        // Verificar si ya existe una conversación entre los usuarios
        $conversation = Conversation::where(function ($query) use ($userId, $participantId) {
            $query->where('user_one_id', $userId)
                  ->where('user_two_id', $participantId);
        })->orWhere(function ($query) use ($userId, $participantId) {
            $query->where('user_one_id', $participantId)
                  ->where('user_two_id', $userId);
        })->first();

        // Si no existe, crear una nueva conversación
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $userId,
                'user_two_id' => $participantId,
            ]);
        }

        return response()->json($conversation);
    }

    /**
     * Eliminar una conversación específica.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);

        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Eliminar los mensajes relacionados
        $conversation->messages()->delete();

        // Eliminar la conversación
        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted successfully']);
    }
}
