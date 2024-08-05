<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index()
    {
        return response()->json(Pet::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url'
        ]);

        $pet = Pet::create($validated);

        return response()->json($pet, 201);
    }

    public function show($id)
    {
        $pet = Pet::findOrFail($id);

        return response()->json($pet, 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image_url' => 'nullable|url'
        ]);

        $pet = Pet::findOrFail($id);
        $pet->update($validated);

        return response()->json($pet, 200);
    }

    public function destroy($id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();

        return response()->json(null, 204);
    }
}
