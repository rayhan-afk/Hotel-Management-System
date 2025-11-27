<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interface\CheckinRepositoryInterface;
use App\Models\Room;
use App\Models\Transaction;

class CheckinController extends Controller
{
    private $checkinRepository;

    public function __construct(CheckinRepositoryInterface $checkinRepository)
    {
        $this->checkinRepository = $checkinRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->checkinRepository->getCheckinDatatable($request));
        }
        return view('transaction.checkin.index');
    }

    public function edit(Transaction $transaction)
    {
        // Ambil semua kamar untuk dropdown edit
        $rooms = Room::all(); 
        return view('transaction.checkin.edit', compact('transaction', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $this->checkinRepository->update($request, $id);

        return response()->json(['message' => 'Data reservasi berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $this->checkinRepository->delete($id);
        return response()->json(['message' => 'Reservasi berhasil dihapus (Cancel).']);
    }
}