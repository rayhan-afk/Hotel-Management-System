<?php

namespace App\Http\Controllers;

use App\Models\Room; // Tambahkan
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil SEMUA kamar dan Eager Load data transaksi yang dibutuhkan 
        // untuk kalkulasi status dinamis (Defined in Room.php)
        $allRooms = Room::with([
            'currentTransaction', 
            'futureReservation', 
            'latestCheckoutToday',
            'type' // Tambahkan ini jika dibutuhkan di view
        ])->get();
        
        // 2. Inisialisasi penghitung untuk Stats Card
        $availableRoomsCount = 0;
        $occupiedRoomsCount = 0;
        $cleaningRoomsCount = 0;

        // 3. Hitung status dinamis untuk setiap kamar
        // Logika status: Occupied, Cleaning (1 jam), Reserved, atau Available
        foreach ($allRooms as $room) {
            // Gunakan accessor dynamic_status yang harus didefinisikan di Model Room
            $status = $room->dynamic_status; 
            
            if ($status === 'Available') {
                $availableRoomsCount++;
            } elseif ($status === 'Occupied') {
                $occupiedRoomsCount++;
            } elseif ($status === 'Cleaning') {
                $cleaningRoomsCount++;
            }
        }
        
        // 4. Hitung Reservasi Hari Ini (Transaksi yang Check-in TEPAT hari ini)
        $todayReservationsCount = Transaction::whereDate('check_in', Carbon::today())
                                            // Asumsi yang belum Check-out
                                            ->where('check_out', '>=', Carbon::now()) 
                                            ->count();


        // 5. DATA LAMA untuk Tabel Detail "Tamu Hari Ini" (Tamu yang sedang menginap)
        $currentInHouseTransactions = Transaction::with('user', 'room', 'customer')
            ->where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()]])
            ->orderBy('check_out', 'ASC')
            ->orderBy('id', 'DESC')
            ->get();

        return view('dashboard.index', [
            'transactions' => $currentInHouseTransactions, 
            'availableRoomsCount' => $availableRoomsCount, 
            'occupiedRoomsCount' => $occupiedRoomsCount,   
            'cleaningRoomsCount' => $cleaningRoomsCount,   
            'todayReservationsCount' => $todayReservationsCount, 
        ]);
    }
}