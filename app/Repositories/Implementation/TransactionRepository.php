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
        // Gunakan $request->total_price dan $request->status yang sudah di-merge di Controller
        return Transaction::create([
            'user_id'     => auth()->id(), // Asumsi yang input adalah admin yang login
            'customer_id' => $customer->id,
            'room_id'     => $room->id,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
            'status'      => $request->status,      // Pastikan kolom ini diisi
            'total_price' => $request->total_price, // Pastikan kolom ini diisi
            'breakfast'   => $request->breakfast    // Simpan status breakfast
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