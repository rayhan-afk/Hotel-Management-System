<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return response()->json($ingredients);
    }

    public function show($id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($ingredient);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'numeric|min:0',
            'min_stock' => 'numeric|min:0',
            'status' => 'in:available,low,out',
        ]);

        $ingredient = Ingredient::create($validated);

        return response()->json([
            'message' => 'Bahan baru berhasil ditambahkan',
            'data' => $ingredient
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'unit' => 'string|max:50',
            'stock' => 'numeric|min:0',
            'min_stock' => 'numeric|min:0',
            'status' => 'in:available,low,out',
        ]);

        $ingredient->update($validated);

        return response()->json([
            'message' => 'Data bahan berhasil diperbarui',
            'data' => $ingredient
        ]);
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $ingredient->delete();

        return response()->json(['message' => 'Data bahan berhasil dihapus']);
    }
}
