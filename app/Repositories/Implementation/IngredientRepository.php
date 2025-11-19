<?php

namespace App\Repositories\Implementation;

use App\Models\Ingredient;
use App\Repositories\Interface\IngredientRepositoryInterface;

class IngredientRepository implements IngredientRepositoryInterface
{
    public function getCategories()
    {
        // Ambil list kategori unik untuk dropdown filter
        return Ingredient::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
    }

    public function getIngredientsDatatable($request)
    {
        // Mapping urutan kolom untuk sorting
        $columns = [
            0 => 'name',
            1 => 'category',
            2 => 'stock',
            3 => 'unit',
            4 => 'stock', // Status (disortir by stok)
            5 => 'description',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'name';
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        
        // Filter Kategori
        $filterCategory = $request->input('category_filter');

        $query = Ingredient::query();

        // 1. Filter Kategori
        if (!empty($filterCategory) && $filterCategory !== 'All') {
            $query->where('category', $filterCategory);
        }

        // 2. Pencarian Global
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $totalData = Ingredient::count();
        $totalFiltered = $query->count();

        $models = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($models as $model) {
            $data[] = [
                'id' => $model->id,
                'name' => $model->name,
                'category' => $model->category,
                'stock' => $model->stock,
                'unit' => $model->unit,
                'description' => $model->description ?? '-',
            ];
        }

        return json_encode([
            'draw' => intval($request->input('draw')),
            'iTotalRecords' => $totalData,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data,
        ]);
    }
}