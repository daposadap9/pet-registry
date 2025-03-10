<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use WebSocket\Client;

class MessageController extends Controller
{
    /**
     * Obtener todos los mensajes de una conversación específica.
     *
     * @param int $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $messages = Message::where('conversation_id', $conversationId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Obtener un mensaje específico dentro de una conversación.
     *
     * @param int $conversationId
     * @param int $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($conversationId, $messageId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::where('conversation_id', $conversationId)
            ->where('id', $messageId)
            ->with(['sender', 'recipient'])
            ->firstOrFail();

        return response()->json($message);
    }

    /**
     * Crear un nuevo mensaje dentro de una conversación.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string',
            'recipient_id' => 'required|exists:users,id'
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
        ]);

        // Cargar relaciones sender y recipient
        $message->load(['sender', 'recipient']);

        // Enviar el mensaje al servidor WebSocket
        $this->broadcastMessage('create', $message);

        return response()->json($message, 201);
    }

    /**
     * Eliminar un mensaje específico de una conversación.
     *
     * @param int $conversationId
     * @param int $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($conversationId, $messageId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::findOrFail($messageId);

        // Verificar si el usuario autenticado es el remitente o destinatario del mensaje
        if ($message->sender_id !== auth()->id() && $message->recipient_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->delete();

        // Enviar la eliminación al servidor WebSocket
        $this->broadcastMessage('delete', [
            'id' => (int)$messageId,
            'conversation_id' => (int)$conversationId
        ]);

        return response()->json(['message' => 'Message deleted successfully']);
    }

    /**
     * Actualizar el contenido de un mensaje específico.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $conversationId
     * @param int $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $conversationId, $messageId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar si el usuario autenticado es parte de la conversación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::findOrFail($messageId);

        // Verificar si el usuario autenticado es el remitente del mensaje
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->update([
            'message' => $request->message
        ]);

        // Cargar relaciones sender y recipient
        $message->load(['sender', 'recipient']);

        // Enviar la actualización al servidor WebSocket
        $this->broadcastMessage('update', $message);

        return response()->json($message);
    }

    /**
     * Método para enviar mensajes al servidor WebSocket.
     *
     * @param string $action
     * @param mixed $message
     * @return void
     */
    private function broadcastMessage($action, $message)
    {
        // URL del servidor WebSocket
        $websocketUrl = 'ws://localhost:3001'; // Asegúrate de que este puerto coincida con el de server.js

        // Construir los datos a enviar
        if ($action === 'delete') {
            $data = [
                'action' => 'delete',
                'message' => [
                    'id' => (int)$message['id'],
                    'conversation_id' => (int)$message['conversation_id']
                ]
            ];
        } else {
            $data = [
                'action' => $action,
                'message' => [
                    'conversation_id' => (int)$message->conversation_id,
                    'sender_id' => (int)$message->sender_id,
                    'recipient_id' => (int)$message->recipient_id,
                    'message' => $message->message,
                    'updated_at' => $message->updated_at->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                    'id' => (int)$message->id,
                    'sender' => [
                        'id' => (int)$message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                        'email_verified_at' => $message->sender->email_verified_at,
                        'created_at' => $message->sender->created_at->toIso8601String(),
                        'updated_at' => $message->sender->updated_at->toIso8601String(),
                    ],
                    'recipient' => [
                        'id' => (int)$message->recipient->id,
                        'name' => $message->recipient->name,
                        'email' => $message->recipient->email,
                        'email_verified_at' => $message->recipient->email_verified_at,
                        'created_at' => $message->recipient->created_at->toIso8601String(),
                        'updated_at' => $message->recipient->updated_at->toIso8601String(),
                    ],
                ]
            ];
        }

        try {
            // Crear una instancia del cliente WebSocket
            $client = new Client($websocketUrl);

            // Enviar el mensaje como JSON
            $client->send(json_encode($data));

            // Cerrar la conexión WebSocket
            $client->close();
        } catch (\Exception $e) {
            // Manejar errores de conexión
            \Log::error('Error al enviar el mensaje al WebSocket: ' . $e->getMessage());
        }
    }
}
