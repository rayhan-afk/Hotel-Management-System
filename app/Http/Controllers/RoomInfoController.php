<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Transaction;
use Carbon\Carbon;

class RoomInfoController extends Controller
{
    // Eager load relasi yang dibutuhkan untuk kalkulasi status
    protected function getAllRoomsWithStatus()
    {
        return Room::with(['type', 'currentTransaction', 'futureReservation', 'latestCheckoutToday'])->get();
    }
    
    // === 1. KAMAR TERSEDIA (Available Rooms) ===
    public function availableRooms()
    {
        $allRooms = $this->getAllRoomsWithStatus();
        
        // Filter kamar yang berstatus 'Available'
        $availableRooms = $allRooms->filter(function ($room) {
            return $room->dynamic_status === 'Available';
        });

        return view('room-info.available', [
            'rooms' => $availableRooms,
            'title' => 'Kamar Tersedia',
        ]);
    }
    
    // === 2. RESERVASI KAMAR (Pending Reservations) ===
    public function pendingReservations()
    {
        // Ambil semua transaksi yang Check-in-nya di masa depan
        $pendingReservations = Transaction::with('room', 'customer')
                                            ->where('check_in', '>', Carbon::now())
                                            ->orderBy('check_in', 'ASC')
                                            ->get();

        return view('room-info.reservation', [
            'transactions' => $pendingReservations,
            'title' => 'Daftar Reservasi Mendatang',
        ]);
    }

    // === 3. KAMAR DIBERSIHKAN (Cleaning Rooms) ===
    public function cleaningRooms()
    {
        $allRooms = $this->getAllRoomsWithStatus();
        
        // Filter kamar yang berstatus 'Cleaning'
        $cleaningRooms = $allRooms->filter(function ($room) {
            return $room->dynamic_status === 'Cleaning';
        });

        return view('room-info.cleaning', [
            'rooms' => $cleaningRooms,
            'title' => 'Kamar Sedang Dibersihkan',
        ]);
    }
}