<?php

namespace App\Repositories\Implementation;

use App\Models\Room;
use App\Repositories\Interface\KamarTersediaRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KamarTersediaRepository implements KamarTersediaRepositoryInterface
{
    public function getDatatable(Request $request)
    {
        $columns = [
            0 => 'rooms.id',
            1 => 'rooms.number',
            2 => 'rooms.area_sqm',
            3 => 'rooms.price',
            4 => 'rooms.id', // Status
            5 => 'rooms.room_facilities', // Fasilitas
        ];

        // 1. QUERY UTAMA
        // Ambil kamar beserta tipenya
        $query = Room::query()
            ->select([
                'rooms.*',
                'types.name as type_name'
            ])
            ->join('types', 'rooms.type_id', '=', 'types.id');

        // 2. LOGIKA KETERSEDIAAN (FILTERING)
        // Kamar dianggap tersedia jika TIDAK ADA transaksi yang sedang berjalan hari ini
        $query->whereDoesntHave('transactions', function($q) {
            $now = Carbon::now();
            $q->where('check_in', '<=', $now)
              ->where('check_out', '>=', $now)
              ->whereNotIn('status', ['Cancel', 'Checked Out']); 
        });

        // 3. SEARCHING
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('rooms.number', 'LIKE', "%{$search}%")
                  ->orWhere('types.name', 'LIKE', "%{$search}%")
                  ->orWhere('rooms.price', 'LIKE', "%{$search}%");
            });
        }

        // 4. ORDERING
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderIdx = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderIdx] ?? 'rooms.number';
        $orderDir = $request->input('order.0.dir', 'asc');

        // 5. COUNTING
        $countQuery = clone $query;
        $totalFiltered = $countQuery->count();

        // 6. GET DATA
        $models = $query->orderBy($orderCol, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($models as $room) {
            $data[] = [
                'id'            => $room->id,
                
                // Gabungan Nomor & Tipe
                'room_info'     => [
                    'number' => $room->number,
                    'type'   => $room->type_name,
                    'image'  => $room->main_image_path // Opsional: jika mau nampilin gambar
                ],

                'area'          => $room->area_sqm ?? '-',
                'price'         => $room->price,
                
                // Status pasti Tersedia karena sudah difilter di query
                'status'        => 'Tersedia', 

                // Gabungan Fasilitas
                'facilities'    => [
                    'room' => $room->room_facilities,
                    'bath' => $room->bathroom_facilities
                ]
            ];
        }

        return [
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => Room::count(), // Total semua kamar
            'recordsFiltered' => $totalFiltered, // Total kamar tersedia sesuai search
            'data'            => $data,
        ];
    }
}