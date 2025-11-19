<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RapatTransaction; // Model transaksi rapat
use Carbon\Carbon; // Untuk perbandingan waktu

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan reservasi ruang rapat yang sudah selesai.
     */
    public function laporanRuangRapat(Request $request)
    {
        $now = Carbon::now();
        // Format Y-m-d H:i:s adalah kunci untuk perbandingan di MySQL
        $nowFormatted = $now->format('Y-m-d H:i:s'); 
        
        $baseQuery = RapatTransaction::with('rapatCustomer', 'ruangRapatPaket');

        // Logic search (text)
        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('id', $request->input('search'))
                  ->orWhereHas('rapatCustomer', function ($subQ) use ($request) {
                      $subQ->where('nama', 'like', '%' . $request->input('search') . '%');
                  });
            });
        }
        
        // ==========================================================
        // == PERBAIKAN: MENAMBAHKAN LOGIKA FILTER PERIODE TANGGAL ==
        // == (Ini hilang dari kode yang Anda kirim) ==
        // ==========================================================
        if ($request->filled('tanggal_mulai')) {
            $baseQuery->where('tanggal_pemakaian', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $baseQuery->where('tanggal_pemakaian', '<=', $request->input('tanggal_selesai'));
        }
        // ==========================================================
        
        // Data Laporan: Hanya reservasi yang sudah selesai (logika ini sudah benar)
        $rapatTransactionsExpired = $baseQuery->clone()
            ->whereRaw("CONCAT(tanggal_pemakaian, ' ', waktu_selesai) <= ?", [$nowFormatted])
            ->orderBy('tanggal_pemakaian', 'DESC')
            ->orderBy('waktu_selesai', 'DESC')
            ->paginate(25); // Paginasi lebih besar untuk laporan

        // File view ini akan kita buat di Langkah 4
        return view('laporan.rapat.index', compact('rapatTransactionsExpired'));
    }

    /**
     * Stub untuk Laporan Kamar Hotel (laporan.kamar.index)
     * Biarkan kosong untuk saat ini agar route tidak error
     */
    public function laporanKamarHotel(Request $request)
    {
        // TODO: Tambahkan logika query untuk transaksi kamar yang sudah selesai di sini
        $transactionsExpired = collect([]); // Data kosong sementara
        
        // Anda perlu membuat view 'laporan.kamar.index'
        // return view('laporan.kamar.index', compact('transactionsExpired'));
        
        // Untuk sementara, kita kembalikan alert agar tidak error
        return redirect()->route('dashboard.index')->with('info', 'Halaman Laporan Kamar Hotel belum dibuat.');
    }
}