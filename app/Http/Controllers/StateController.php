<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        return State::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state = State::create($request->all());

        return response()->json($state, 201);
    }

    public function show($id)
    {
        return State::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state = State::findOrFail($id);
        $state->update($request->all());

        return response()->json($state, 200);
    }

    public function destroy($id)
    {
        State::destroy($id);

        return response()->json(null, 204);
    }
}

