<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    // Menampilkan daftar reservasi yang siap Check-in hari ini
    public function index()
    {
        // Cari semua reservasi yang Check-in-nya hari ini atau sudah terlewat,
        // TAPI Check-out-nya belum terjadi, dan belum dianggap Occupied (Status In-House).
        
        // Kita gunakan logika yang mencari transaksi yang Check-in-nya <= Hari Ini,
        // DAN Check-out-nya >= Hari Ini.
        
        $pendingCheckins = Transaction::with('room', 'customer')
            ->whereDate('check_in', '<=', Carbon::today())
            ->where('check_out', '>=', Carbon::now())
            // Filter reservasi yang statusnya masih BOOKED atau PENDING
            // Anda perlu menentukan kolom status transaksi yang tepat (misal: 'status')
            // ->where('status', 'BOOKED') 
            // Untuk sementara, kita ambil yang masuk kriteria waktu saja:
            ->get(); 
        
        return view('transaction.checkin.index', [
            'transactions' => $pendingCheckins,
            'title' => 'Daftar Reservasi Siap Check-in'
        ]);
    }

    // Memproses Check-in tamu
    public function store(Request $request, Transaction $transaction)
    {
        // 1. Verifikasi data tamu dan pembayaran awal (DP)
        // ... (Logika validasi)
        
        // 2. Update waktu check-in aktual (jika ada perbedaan dengan reservasi)
        // $transaction->actual_check_in = Carbon::now();
        
        // 3. Update status transaksi menjadi "CHECKED_IN"
        // $transaction->status = 'CHECKED_IN';
        // $transaction->save();
        
        // TIDAK PERLU update status kamar di Room.php, karena logic dynamic_status sudah menanganinya
        
        // Redirect ke dashboard atau halaman detail transaksi
        return redirect()->route('dashboard.index')->with('success', 'Check-in berhasil! Kamar kini Terpakai.');
    }
}