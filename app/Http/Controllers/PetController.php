<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Pet::query();

        // Filtrar por estado si se proporciona el parámetro state_id
        if ($request->has('state_id') && !empty($request->state_id)) {
            $query->where('state_id', $request->state_id);
        }

        // Filtrar por el usuario autenticado
        $query->where('user_id', Auth::id());

        $pets = $query->with('state', 'category')->get();

        return response()->json($pets, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'state_id' => 'required|exists:states,id',
            'image_url' => 'nullable|url'
        ]);

        // Asignar el usuario autenticado
        $validated['user_id'] = Auth::id();

        $pet = Pet::create($validated);

        return response()->json($pet, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pet = Pet::where('id', $id)->where('user_id', Auth::id())->with('state', 'category')->firstOrFail();

        return response()->json($pet, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'state_id' => 'sometimes|required|exists:states,id',
            'image_url' => 'nullable|url'
        ]);

        $pet = Pet::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $pet->update($validated);

        return response()->json($pet, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $pet = Pet::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $pet->delete();

        return response()->json(null, 204);
    }
}
