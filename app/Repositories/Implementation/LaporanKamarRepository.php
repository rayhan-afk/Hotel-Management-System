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
        $startDate = $request->input('start_date'); // Nama input filter di view kamar
        $endDate = $request->input('end_date');
        
        // --- PERBAIKAN PENTING UNTUK MENCEGAH ARRAY TO STRING CONVERSION ---
        // 1. Prioritaskan pengambilan nilai dari 'search.value' (untuk DataTables)
        $searchDatatableValue = $request->input('search.value');
        
        // 2. Jika bukan dari DataTables (misalnya dari URL Export), gunakan 'search'
        $searchUrlValue = $request->input('search');
        
        // Pilih nilai search yang paling relevan (string), jika ada
        $search = $searchDatatableValue ?: $searchUrlValue;
        
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        // --------------------------------------------------------------------

        // Query Builder untuk Reservasi Kamar (Transaction Model)
        $query = Transaction::select('transactions.*')
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id') // Join Type Kamar
            ->with(['customer.user', 'room.type']); 

        // Filter 1: Rentang Tanggal Check-In
        if ($startDate) {
            $query->where('transactions.check_in', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('transactions.check_in', '<=', $endDate);
        }

        // Filter 2: Pencarian Teks (Nama Tamu, No Kamar, Tipe Kamar)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', "%{$search}%")
                  ->orWhere('rooms.number', 'LIKE', "%{$search}%")
                  ->orWhere('types.name', 'LIKE', "%{$search}%");
            });
        }
        
        // Default urutan: Check-in Terbaru
        $query->orderBy('transactions.check_in', 'DESC');

        return $query;
    }

    /**
     * Khusus DataTables: Mengambil query dasar + Pagination.
     */
    public function getLaporanKamarDatatable($request)
    {
        $query = $this->getLaporanKamarQuery($request); 

        // Definisi Kolom untuk Sorting DataTables
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

        // --- PAGINATION ---
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir') ?? 'desc';

        $orderBy = $columns[$orderColumnIndex] ?? 'transactions.check_in';

        if ($limit) {
            $query->offset($start)->limit($limit);
        }

        // Terapkan sorting
        $query->orderBy($orderBy, $orderDir);

        $models = $query->get();

        // Mapping Data JSON
        $data = [];
        foreach ($models as $model) {
            // Hitung Total Harga (Support method model lama & kolom baru)
            $totalHarga = $model->total_price ?? $model->getTotalPrice();

            $data[] = [
                'tamu' => $model->customer->name,
                'kamar' => 'Room ' . $model->room->number . ' (' . ($model->room->type->name ?? '-') . ')',
                'check_in' => Helper::dateFormat($model->check_in),
                'check_out' => Helper::dateFormat($model->check_out),
                'sarapan' => $model->breakfast,
                'total_harga' => Helper::convertToRupiah($totalHarga), 
                'status' => 'Paid', // Hardcode status 'Paid' sesuai logika baru
            ];
        }

        // Return format JSON untuk DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'iTotalRecords' => $totalData,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data, // Versi lama DataTables pakai aaData
        ]);
    }
}