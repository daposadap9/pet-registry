<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    // Mostrar todos los estados
    public function index()
    {
        $states = State::all();
        return response()->json($states, 200);
    }

    // Crear un nuevo estado
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state = State::create($request->only('name')); // Solo permite el campo 'name'

        return response()->json($state, 201);
    }    

    // Mostrar un estado específico
    public function show($id)
    {
        $state = State::findOrFail($id);
        return response()->json($state, 200);
    }

    // Actualizar un estado existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state = State::findOrFail($id);
        $state->update($request->only('name')); // Solo actualiza el campo 'name'

        return response()->json($state, 200);
    }

    // Eliminar un estado
    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $state->delete();

        return response()->json(null, 204);
    }
}
