<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientTransaction;
use Illuminate\Http\Request;

class IngredientTransactionController extends Controller
{
    public function store(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Bahan tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        // Simpan transaksi
        $transaction = IngredientTransaction::create([
            'ingredient_id' => $ingredient->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'note' => $validated['note'] ?? null,
        ]);

        // Update stok
        if ($validated['type'] === 'in') {
            $ingredient->increment('stock', $validated['quantity']);
        } else {
            $ingredient->decrement('stock', $validated['quantity']);
        }

        // Update status otomatis
        $ingredient->update([
            'status' => $ingredient->stock <= 0
                ? 'out'
                : ($ingredient->stock <= $ingredient->min_stock ? 'low' : 'available')
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dicatat',
            'data' => $transaction
        ]);
    }
}
