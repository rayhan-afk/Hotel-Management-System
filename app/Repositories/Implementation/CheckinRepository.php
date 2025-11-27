<?php

namespace App\Repositories\Implementation;

use App\Helpers\Helper;
use App\Models\Transaction;
use App\Repositories\Interface\CheckinRepositoryInterface;
use Carbon\Carbon;

class CheckinRepository implements CheckinRepositoryInterface
{
    public function getCheckinDatatable($request)
{
    // Ambil transaksi yang statusnya 'Paid', belum checkout, dan check-in HARI INI
    $query = Transaction::with(['customer', 'room.type', 'user'])
        ->where('status', 'Paid')
        ->whereDate('check_in', Carbon::today()) // FILTER HARI INI
        ->where('check_out', '>=', Carbon::now()->format('Y-m-d'));

    // Filter pencarian
    if (!empty($request->search['value'])) {
        $search = $request->search['value'];
        $query->where(function($q) use ($search) {
            $q->whereHas('customer', function($c) use ($search) {
                $c->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('room', function($r) use ($search) {
                $r->where('number', 'like', "%{$search}%");
            })
            ->orWhere('id', 'like', "%{$search}%");
        });
    }

    $totalData = $query->count();
    $query->orderBy('check_in', 'ASC');

    // Pagination
    $limit = $request->length ?? 10;
    $start = $request->start ?? 0;
    $data = $query->skip($start)->take($limit)->get();

    $formattedData = [];
    foreach ($data as $trx) {
        $formattedData[] = [
            'id' => $trx->id,
            'customer' => $trx->customer->name,
            'room' => $trx->room->number . ' <span class="text-muted">(' . $trx->room->type->name . ')</span>',
            'check_in' => Helper::dateFormat($trx->check_in),
            'check_out' => Helper::dateFormat($trx->check_out),
            'breakfast' => $trx->breakfast ? $trx->breakfast : 'No',
            'total_price' => Helper::convertToRupiah($trx->total_price),
            'status' => $trx->status,
            'action' => $trx->id
        ];
    }

    return [
        'draw' => $request->draw,
        'recordsTotal' => Transaction::where('status', 'Paid')
            ->whereDate('check_in', Carbon::today()) // total data hari ini
            ->count(),
        'recordsFiltered' => $totalData,
        'data' => $formattedData
    ];
}

    public function getTransaction($id)
    {
        return Transaction::findOrFail($id);
    }

    public function update($request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'room_id' => $request->room_id,
        ]);
        return $transaction;
    }

    public function delete($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
    }
}