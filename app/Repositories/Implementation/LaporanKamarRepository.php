<?php

namespace App\Repositories\Implementation;

use App\Helpers\Helper;
use App\Models\Transaction;
use App\Repositories\Interface\LaporanKamarRepositoryInterface;
use Carbon\Carbon;

class LaporanKamarRepository implements LaporanKamarRepositoryInterface
{
    /**
     * Query Dasar: Digunakan bersama oleh DataTables DAN Export Excel.
     */
    public function getLaporanKamarQuery($request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // --- LOGIKA SEARCH (SAMA SEPERTI SEBELUMNYA) ---
        $searchDatatableValue = $request->input('search.value');
        $searchUrlValue = $request->input('search');
        $search = $searchDatatableValue ?: $searchUrlValue;
        
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        // ------------------------------------------------

        $query = Transaction::select('transactions.*')
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            ->with(['customer.user', 'room.type']); 

        // Filter Tanggal
        if ($startDate) {
            $query->where('transactions.check_in', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('transactions.check_in', '<=', $endDate);
        }

        // Filter Search Global
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', "%{$search}%")
                  ->orWhere('rooms.number', 'LIKE', "%{$search}%")
                  ->orWhere('types.name', 'LIKE', "%{$search}%");
            });
        }
        
        // Default Order
        $query->orderBy('transactions.check_in', 'DESC');

        return $query;
    }

    public function saveToLaporan($t)
    {
        Transaction::create([
            'customer_id' => $t->customer_id,
            'room_id' => $t->room_id,
            'check_in' => $t->check_in,
            'check_out' => $t->check_out,
            'breakfast' => $t->breakfast,
            'total_price' => $t->total_price,
            'status' => 'Paid'
        ]);
    }

    /**
     * Khusus DataTables
     */
    public function getLaporanKamarDatatable($request)
    {
        $query = $this->getLaporanKamarQuery($request); 

        // Kolom untuk Sorting
        $columns = [
            0 => 'customers.name',
            1 => 'rooms.number',
            2 => 'transactions.check_in',
            3 => 'transactions.check_out',
            4 => 'transactions.breakfast',
            5 => 'transactions.total_price', 
            6 => 'transactions.status',
        ];

        $totalData = Transaction::count();
        $totalFiltered = $query->count(); 

        // Pagination
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir') ?? 'desc';

        $orderBy = $columns[$orderColumnIndex] ?? 'transactions.check_in';

        if ($limit) {
            $query->offset($start)->limit($limit);
        }

        $query->orderBy($orderBy, $orderDir);

        $models = $query->get();

        $data = [];
        foreach ($models as $model) {
            // Hitung Total Harga
            // Prioritaskan kolom 'total_price' di DB, fallback ke hitung manual
            $totalHarga = $model->total_price ?? $model->getTotalPrice();

            $data[] = [
                'tamu' => $model->customer->name,
                'kamar' => 'Room ' . $model->room->number . ' (' . ($model->room->type->name ?? '-') . ')',
                'check_in' => Helper::dateFormat($model->check_in),
                'check_out' => Helper::dateFormat($model->check_out),
                'sarapan' => $model->breakfast,
                
                // === PERBAIKAN UTAMA ===
                // Kirim ANGKA MENTAH (float/int), bukan string "Rp ...".
                // Javascript akan memformatnya menjadi Rupiah.
                'total_harga' => (float) $totalHarga, 
                // =======================
                
                'status' => 'Paid', 
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'iTotalRecords' => $totalData,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data, 
        ]);
    }
}