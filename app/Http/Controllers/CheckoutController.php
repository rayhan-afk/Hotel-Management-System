<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // Menampilkan daftar tamu yang sedang menginap atau harus Check-out hari ini
    public function index()
    {
        // Ambil semua transaksi yang saat ini sedang menempati kamar (Status Occupied)
        $occupiedTransactions = Transaction::with('room', 'customer')
            ->where('check_in', '<=', Carbon::now())
            ->where('check_out', '>=', Carbon::now())
            // Filter yang statusnya sudah CHECKED_IN
            // ->where('status', 'CHECKED_IN')
            ->orderBy('check_out', 'ASC')
            ->get();
        
        return view('transaction.checkout.index', [
            'transactions' => $occupiedTransactions,
            'title' => 'Daftar Tamu Sedang Menginap'
        ]);
    }

    // Menampilkan detail pembayaran dan memproses Check-out
    public function process(Request $request, Transaction $transaction)
    {
        // 1. Hitung saldo yang harus dilunasi
        $totalPrice = $transaction->getTotalPrice();
        $totalPaid = $transaction->getTotalPayment();
        $balanceDue = $totalPrice - $totalPaid;
        
        // 2. Verifikasi pembayaran pelunasan
        // ... (Logika pembayaran)
        
        // 3. Update waktu check-out aktual (jika diperlukan)
        // $transaction->actual_check_out = Carbon::now();
        
        // 4. Update status transaksi menjadi "COMPLETED"
        // $transaction->status = 'COMPLETED';
        // $transaction->save();

        // Setelah transaksi selesai/COMPLETED, logika dynamic_status di Room.php akan melihat 
        // bahwa Check-out sudah terjadi hari ini dan otomatis mengubah status kamar menjadi 'Cleaning' selama 1 jam.

        return redirect()->route('dashboard.index')->with('success', 'Check-out berhasil! Kamar dalam proses pembersihan.');
    }
}