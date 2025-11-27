<?php

namespace App\Repositories\Implementation;

use App\Helpers\Helper;
use App\Models\Transaction;
use App\Repositories\Interface\CheckoutRepositoryInterface;
use Carbon\Carbon;

class CheckoutRepository implements CheckoutRepositoryInterface
{
    public function getCheckoutDatatable($request)
    {
        // Ambil tamu yang sudah waktunya checkout
        $query = Transaction::with(['customer', 'room.type', 'user'])
            ->where('status', 'Paid')
            ->where('check_out', '<=', Carbon::now()->format('Y-m-d'));

        // Filter pencarian
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('room', fn($r) => $r->where('number', 'like', "%{$search}%"))
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $totalData = $query->count();
        $query->orderBy('check_out', 'ASC');

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
            'recordsTotal' => Transaction::where('status', 'Paid')->count(),
            'recordsFiltered' => $totalData,
            'data' => $formattedData
        ];
    }

    public function getTransaction($id)
    {
        return Transaction::findOrFail($id);
    }

    public function checkoutDelete($id)
    {
        $transaction = Transaction::findOrFail($id);
        return $transaction->delete();
    }
}
