<?php

namespace App\Repositories\Implementation;

use App\Helpers\Helper;
use App\Models\Transaction;
use App\Models\Room;
use App\Repositories\Interface\CheckinRepositoryInterface;
use Carbon\Carbon;

class CheckinRepository implements CheckinRepositoryInterface
{
    public function getCheckinDatatable($request)
    {
        // Query Data: Ambil transaksi yang statusnya 'Paid' (Aktif)
        // Dan Check Out nya belum lewat (masih menginap atau akan datang)
        $query = Transaction::with(['customer', 'room.type', 'user'])
            ->where('status', 'Paid') 
            ->where('check_out', '>=', Carbon::now()->format('Y-m-d'));

        // Filter Pencarian
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
        $query->orderBy('check_in', 'ASC'); // Urutkan dari yang check-in duluan

        // Pagination
        $limit = $request->length ?? 10;
        $start = $request->start ?? 0;
        $data = $query->skip($start)->take($limit)->get();

        // Format JSON untuk DataTables
        $formattedData = [];
        foreach ($data as $trx) {
            $formattedData[] = [
                'id' => $trx->id,
                'customer' => $trx->customer->name,
                'room' => $trx->room->number . ' (' . $trx->room->type->name . ')',
                'check_in' => Helper::dateFormat($trx->check_in),
                'check_out' => Helper::dateFormat($trx->check_out),
                'action' => $trx->id // ID untuk tombol aksi
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

    public function update($request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Jika user mengubah kamar, kita harus update harga total juga (opsional, tapi disarankan)
        // Di sini kita update basic info saja
        $transaction->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'room_id' => $request->room_id,
            // Total price bisa dihitung ulang jika perlu
        ]);

        return $transaction;
    }

    public function delete($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
    }
}