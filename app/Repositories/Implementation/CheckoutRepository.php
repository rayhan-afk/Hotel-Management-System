<?php

namespace App\Repositories\Implementation;

use App\Models\Transaction;
use App\Repositories\Interface\CheckoutRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckoutRepository implements CheckoutRepositoryInterface
{
    // === 1. SESUAIKAN NAMA METHOD DENGAN INTERFACE ===
    public function getCheckoutDatatable($request)
    {
        $columns = [
            0 => 'transactions.id',
            1 => 'customers.name',
            2 => 'rooms.number',
            3 => 'transactions.check_in',
            4 => 'transactions.check_out',
            5 => 'transactions.id',
            6 => 'rooms.price',
            7 => 'transactions.status',
            8 => 'transactions.id',
        ];

        $query = Transaction::query()
            ->select([
                'transactions.*',
                'customers.name as customer_name',
                'rooms.number as room_number',
                'rooms.price as room_price',
                'types.name as type_name'
            ])
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            ->where('transactions.status', 'Check In');

        if ($request->has('search') && $search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', "%{$search}%")
                  ->orWhere('rooms.number', 'LIKE', "%{$search}%")
                  ->orWhere('types.name', 'LIKE', "%{$search}%");
            });
        }

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderIdx = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderIdx] ?? 'transactions.check_out';
        $orderDir = $request->input('order.0.dir', 'asc');

        $countQuery = clone $query;
        $totalFiltered = $countQuery->count();

        $models = $query->orderBy($orderCol, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($models as $t) {
            $checkIn  = Carbon::parse($t->check_in);
            $checkOut = Carbon::parse($t->check_out);
            $duration = $checkIn->diffInDays($checkOut) ?: 1;
            $rawBreakfast = $t->breakfast ?? 'No';
            $breakfast = (strtolower($rawBreakfast) === 'yes' || $rawBreakfast == '1') ? 1 : 0;
            $totalPrice = $duration * $t->room_price;

            $data[] = [
                'id'            => $t->id,
                'customer_name' => $t->customer_name,
                'room_info'     => [
                    'number' => $t->room_number,
                    'type'   => $t->type_name
                ],
                'check_in'      => $checkIn->format('d/m/Y'),
                'check_out'     => $checkOut->format('d/m/Y'),
                'breakfast'     => $breakfast,
                'total_price'   => $totalPrice,
                'status'        => $t->status,
                'raw_id'        => $t->id
            ];
        }

        return [
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => Transaction::where('status', 'Check In')->count(),
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ];
    }

    // === 2. IMPLEMENTASIKAN METHOD YANG HILANG ===
    
    public function getTransaction($id)
    {
        return Transaction::findOrFail($id);
    }

    public function checkoutDelete($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
    }
}