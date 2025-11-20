<?php

namespace App\Repositories\Implementation;

use App\Models\RapatTransaction;
use App\Repositories\Interface\LaporanRepositoryInterface;
use Carbon\Carbon;

class LaporanRepository implements LaporanRepositoryInterface
{
    public function getLaporanRapatDatatable($request)
    {
        // 1. Definisi Kolom untuk Sorting
        $columns = [
            0 => 'rapat_customers.instansi', // Sort by nama instansi
            1 => 'tanggal_pemakaian',
            2 => 'waktu_mulai',
            3 => 'ruang_rapat_pakets.name',  // Sort by nama paket
            4 => 'status_pembayaran',
        ];

        // 2. Parameter DataTables
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'tanggal_pemakaian';
        $dir = $request->input('order.0.dir') ?? 'desc'; // Default DESC agar yang terbaru di atas
        $search = $request->input('search.value');

        // 3. Parameter Filter Tanggal
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        // 4. Waktu Sekarang (untuk filter "Selesai")
        $nowFormatted = Carbon::now()->format('Y-m-d H:i:s');

        // 5. Query Dasar (Join agar bisa sort berdasarkan nama relasi)
        $query = RapatTransaction::select('rapat_transactions.*')
            ->join('rapat_customers', 'rapat_transactions.rapat_customer_id', '=', 'rapat_customers.id')
            ->join('ruang_rapat_pakets', 'rapat_transactions.ruang_rapat_paket_id', '=', 'ruang_rapat_pakets.id')
            ->with(['rapatCustomer', 'ruangRapatPaket']); // Eager load untuk data view

        // 6. Filter: Hanya Reservasi yang SUDAH SELESAI
        $query->whereRaw("CONCAT(tanggal_pemakaian, ' ', waktu_selesai) <= ?", [$nowFormatted]);

        // 7. Filter: Periode Tanggal (Jika user mengisi)
        if ($startDate) {
            $query->where('tanggal_pemakaian', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal_pemakaian', '<=', $endDate);
        }

        // 8. Filter: Pencarian (Search Box)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rapat_customers.instansi', 'LIKE', "%{$search}%")
                  ->orWhere('ruang_rapat_pakets.name', 'LIKE', "%{$search}%")
                  ->orWhere('status_pembayaran', 'LIKE', "%{$search}%");
            });
        }

        // 9. Eksekusi DataTables (Count & Get)
        $totalData = RapatTransaction::whereRaw("CONCAT(tanggal_pemakaian, ' ', waktu_selesai) <= ?", [$nowFormatted])->count();
        $totalFiltered = $query->count();

        $models = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        // 10. Format Data JSON
        $data = [];
        foreach ($models as $model) {
            // Format tanggal agar cantik
            $tanggal = Carbon::parse($model->tanggal_pemakaian)->isoFormat('D MMMM Y');
            
            $data[] = [
                'instansi' => $model->rapatCustomer->instansi ?? '-',
                'tanggal' => $tanggal,
                'waktu' => $model->waktu_mulai . ' - ' . $model->waktu_selesai,
                'paket' => $model->ruangRapatPaket->name ?? '-',
                'status' => $model->status_pembayaran, // Akan di-badge di JS
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