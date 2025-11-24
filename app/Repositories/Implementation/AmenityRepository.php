<?php

namespace App\Repositories\Implementation;

use App\Models\Amenity;
use App\Repositories\Interface\AmenityRepositoryInterface;

class AmenityRepository implements AmenityRepositoryInterface
{
    public function getAmenities($request)
    {
        return Amenity::orderBy('nama_barang')->get();
    }

    public function getAmenitiesDatatable($request)
    {
        // Mapping urutan kolom untuk sorting dari DataTables
        // Index harus sesuai dengan urutan <th> di HTML (dikurangi kolom 'No')
        $columns = [
            0 => 'nama_barang',
            1 => 'stok',
            2 => 'satuan',
            3 => 'stok',       // Kolom Status (disortir berdasarkan stok)
            4 => 'keterangan',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'nama_barang';
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $main_query = Amenity::select(
            'id',
            'nama_barang',
            'satuan',
            'stok',
            'keterangan'
        );

        $totalData = $main_query->count();

        // Logika Pencarian
        $main_query->when($search, function ($query) use ($search, $columns) {
            $query->where(function ($q) use ($search, $columns) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('satuan', 'LIKE', "%{$search}%")
                  ->orWhere('keterangan', 'LIKE', "%{$search}%");
            });
        });

        $totalFiltered = $main_query->count();

        $main_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir);

        $models = $main_query->get();

        $data = [];
        foreach ($models as $model) {
            $data[] = [
                'id' => $model->id,
                'nama_barang' => $model->nama_barang,
                'stok' => $model->stok,
                'satuan' => $model->satuan,
                // Status tidak perlu ada di DB, kita olah di JS berdasarkan stok
                'keterangan' => $model->keterangan ?? '-',
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