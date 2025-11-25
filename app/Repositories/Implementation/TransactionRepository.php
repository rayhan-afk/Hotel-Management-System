<?php

namespace App\Repositories\Implementation;

use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function store($request, $customer, $room)
    {
        // Pastikan 'total_price' diambil dari $request yang sudah di-merge di Controller
        // Jika sebelumnya menghitung harga manual disini, ganti dengan mengambil dari request
        
        return Transaction::create([
            'user_id' => auth()->user()->id, // Yang menginput reservasi
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => 'Reservation', // Status awal
            'total_price' => $request->total_price, // AMBIL DARI CONTROLLER (Grand Total)
            'breakfast' => $request->breakfast ?? 'No', // KOLOM BARU
        ]);
    }

    public function getTransaction($request)
    {
        return Transaction::with('user', 'room', 'customer')
            ->where('check_out', '>=', Carbon::now())
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where('id', '=', $request->search);
            })
            ->orderBy('check_out', 'ASC')->orderBy('id', 'DESC')->paginate(20)
            ->appends($request->all());
    }

    public function getTransactionExpired($request)
    {
        return Transaction::with('user', 'room', 'customer')->where('check_out', '<', Carbon::now())
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where('id', '=', $request->search);
            })
            ->orderBy('check_out', 'ASC')->paginate(20)
            ->appends($request->all());
    }
}
