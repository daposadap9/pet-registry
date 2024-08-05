<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Devuelve una lista de todas las categorías en formato JSON
        return response()->json(Category::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Valida la solicitud
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Crea una nueva categoría con los datos validados
        $category = Category::create($validated);

        // Devuelve la categoría recién creada en formato JSON
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Busca la categoría por ID o lanza una excepción si no se encuentra
        $category = Category::findOrFail($id);

        // Devuelve la categoría en formato JSON
        return response()->json($category, 200);
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
        // Valida la solicitud
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        // Busca la categoría por ID o lanza una excepción si no se encuentra
        $category = Category::findOrFail($id);

        // Actualiza la categoría con los datos validados
        $category->update($validated);

        // Devuelve la categoría actualizada en formato JSON
        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Busca la categoría por ID o lanza una excepción si no se encuentra
        $category = Category::findOrFail($id);

        // Elimina la categoría
        $category->delete();

        // Devuelve una respuesta vacía con código de estado 204 (No Content)
        return response()->json(null, 204);
    }
}
