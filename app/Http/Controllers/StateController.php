<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        return response()->json(State::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state = State::create($validated);

        return response()->json($state, 201);
    }

    public function show($id)
    {
        $state = State::findOrFail($id);

        return response()->json($state, 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $state = State::findOrFail($id);
        $state->update($validated);

        return response()->json($state, 200);
    }

    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $state->delete();

        return response()->json(null, 204);
    }
}
