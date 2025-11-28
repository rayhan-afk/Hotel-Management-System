<?php

namespace App\Repositories\Implementation;

use App\Models\Transaction;
use App\Repositories\Interface\KamarDibersihkanRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KamarDibersihkanRepository implements KamarDibersihkanRepositoryInterface
{
    public function getDatatable(Request $request)
    {
        $columns = [
            0 => 'transactions.id',
            1 => 'customers.name',
            2 => 'rooms.number',
            3 => 'transactions.check_in',
            4 => 'transactions.check_out',
            5 => 'transactions.id', // Breakfast
            6 => 'rooms.price',
            7 => 'transactions.status',
        ];

        // 1. QUERY UTAMA
        $query = Transaction::query()
            ->select([
                'transactions.*',
                'customers.name as customer_name',
                'rooms.number as room_number',
                'rooms.price as room_price',
                'types.name as type_name'
            ])
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            
            // LOGIKA PENTING: Ambil yang checkout HARI INI dan statusnya CHECKED OUT
            ->whereDate('transactions.check_out', Carbon::today())
            ->where('transactions.status', 'Checked Out');

        // 2. SEARCHING
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', "%{$search}%")
                  ->orWhere('rooms.number', 'LIKE', "%{$search}%")
                  ->orWhere('types.name', 'LIKE', "%{$search}%");
            });
        }

        // 3. ORDERING
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderIdx = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderIdx] ?? 'transactions.check_out';
        $orderDir = $request->input('order.0.dir', 'desc'); // Default yang baru checkout di atas

        // 4. COUNTING
        $countQuery = clone $query;
        $totalFiltered = $countQuery->count();

        // 5. GET DATA
        $models = $query->orderBy($orderCol, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($models as $t) {
            $checkIn  = Carbon::parse($t->check_in);
            $checkOut = Carbon::parse($t->check_out);
            $duration = $checkIn->diffInDays($checkOut) ?: 1;
            
            // Logic Breakfast
            $rawBreakfast = $t->breakfast ?? 'No';
            $breakfast = (strtolower($rawBreakfast) === 'yes' || $rawBreakfast == '1') ? 1 : 0;

            // Hitung Total Harga (Durasi * Harga Kamar)
            $totalPrice = $duration * $t->room_price;

            $data[] = [
                'id'            => $t->id,
                'customer_name' => $t->customer_name,
                
                // Info Kamar
                'room_info'     => [
                    'number' => $t->room_number,
                    'type'   => $t->type_name
                ],

                'check_in'      => $checkIn->format('d/m/Y'),
                'check_out'     => $checkOut->format('d/m/Y'),
                'breakfast'     => $breakfast,
                'total_price'   => $totalPrice,
                'status'        => 'Lunas', // Hardcode Lunas karena sudah Checked Out
            ];
        }

        return [
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => Transaction::whereDate('check_out', Carbon::today())
                                            ->where('status', 'Checked Out')->count(),
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ];
    }
}