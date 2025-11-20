<?php

namespace App\Repositories\Implementation;

use App\Models\RapatTransaction;
use App\Repositories\Interface\LaporanRepositoryInterface;
use Carbon\Carbon;

class LaporanRepository implements LaporanRepositoryInterface
{
    public function getLaporanRapatDatatable($request)
    {
        $columns = [
            0 => 'rapat_customers.instansi', 
            1 => 'tanggal_pemakaian',
            2 => 'waktu_mulai',
            3 => 'ruang_rapat_pakets.name',  
            4 => 'jumlah_peserta',
            5 => 'total_pembayaran', 
            6 => 'status_pembayaran', 
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'tanggal_pemakaian';
        $dir = $request->input('order.0.dir') ?? 'desc'; 
        $search = $request->input('search.value');
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $nowFormatted = Carbon::now()->format('Y-m-d H:i:s');

        $query = RapatTransaction::select('rapat_transactions.*')
            ->join('rapat_customers', 'rapat_transactions.rapat_customer_id', '=', 'rapat_customers.id')
            ->join('ruang_rapat_pakets', 'rapat_transactions.ruang_rapat_paket_id', '=', 'ruang_rapat_pakets.id')
            ->with(['rapatCustomer', 'ruangRapatPaket']); 

        // Filter Selesai
        $query->whereRaw("CONCAT(tanggal_pemakaian, ' ', waktu_selesai) <= ?", [$nowFormatted]);

        // Filter Tanggal
        if ($startDate) {
            $query->where('tanggal_pemakaian', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal_pemakaian', '<=', $endDate);
        }

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rapat_customers.instansi', 'LIKE', "%{$search}%")
                  ->orWhere('ruang_rapat_pakets.name', 'LIKE', "%{$search}%");
            });
        }

        $totalData = RapatTransaction::whereRaw("CONCAT(tanggal_pemakaian, ' ', waktu_selesai) <= ?", [$nowFormatted])->count();
        $totalFiltered = $query->count();

        $models = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($models as $model) {
            $tanggal = \App\Helpers\Helper::dateFormat($model->tanggal_pemakaian);
            
            $data[] = [
                'instansi' => $model->rapatCustomer->instansi ?? '-',
                'tanggal' => $tanggal,
                'waktu' => $model->waktu_mulai . ' - ' . $model->waktu_selesai,
                'paket' => $model->ruangRapatPaket->name ?? '-',
                'jumlah_peserta' => $model->jumlah_peserta . ' Orang',
                'total_pembayaran' => $model->total_pembayaran,
                'status' => $model->status_pembayaran,
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