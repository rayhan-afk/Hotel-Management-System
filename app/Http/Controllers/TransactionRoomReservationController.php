<?php

namespace App\Http\Controllers;

use App\Events\NewReservationEvent;
use App\Events\RefreshDashboardEvent;
use App\Helpers\Helper;
use App\Http\Requests\ChooseRoomRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewRoomReservationDownPayment;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\PaymentRepositoryInterface;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionRoomReservationController extends Controller
{
    // ... (Method pickFromCustomer, createIdentity, storeCustomer, viewCountPerson tetap sama)

    public function pickFromCustomer(Request $request, CustomerRepositoryInterface $customerRepository)
    {
        $customers = $customerRepository->get($request);
        $customersCount = $customerRepository->count($request);

        return view('transaction.reservation.pickFromCustomer', [
            'customers' => $customers,
            'customersCount' => $customersCount,
        ]);
    }

    public function createIdentity()
    {
        return view('transaction.reservation.createIdentity');
    }

    public function storeCustomer(StoreCustomerRequest $request, CustomerRepositoryInterface $customerRepository)
    {
        $customer = $customerRepository->store($request);
        return redirect()
            ->route('transaction.reservation.viewCountPerson', ['customer' => $customer->id])
            ->with('success', 'Customer ' . $customer->name . ' created!');
    }

    public function viewCountPerson(Customer $customer)
    {
        return view('transaction.reservation.viewCountPerson', [
            'customer' => $customer,
        ]);
    }

    public function chooseRoom(ChooseRoomRequest $request, Customer $customer)
    {
        $stayFrom = $request->check_in;
        $stayUntil = $request->check_out;
        $occupiedRoomIds = $this->getOccupiedRoomID($stayFrom, $stayUntil);
        
        // Query Pencarian Kamar (Filter & Sort)
        $query = Room::with('type')->whereNotIn('id', $occupiedRoomIds);

        if ($request->has('type_id') && $request->type_id != '') {
            $query->where('type_id', $request->type_id);
        }

        $sortPrice = $request->input('sort_price', 'ASC');
        $sortPrice = in_array(strtoupper($sortPrice), ['ASC', 'DESC']) ? strtoupper($sortPrice) : 'ASC';
        $query->orderBy('price', $sortPrice);

        $rooms = $query->paginate(10);
        $roomsCount = $rooms->total();

        return view('transaction.reservation.chooseRoom', [
            'customer' => $customer,
            'rooms' => $rooms,
            'stayFrom' => $stayFrom,
            'stayUntil' => $stayUntil,
            'roomsCount' => $roomsCount,
        ]);
    }

    public function confirmation(Customer $customer, Room $room, $stayFrom, $stayUntil)
    {
        $dayDifference = Helper::getDateDifference($stayFrom, $stayUntil);
        
        // Hitung harga dasar (tanpa sarapan) untuk tampilan awal
        $roomPriceTotal = $room->price * $dayDifference;
        $downPayment = $roomPriceTotal * 0.15; 

        return view('transaction.reservation.confirmation', [
            'customer' => $customer,
            'room' => $room,
            'stayFrom' => $stayFrom,
            'stayUntil' => $stayUntil,
            'downPayment' => $downPayment,
            'dayDifference' => $dayDifference,
        ]);
    }

    // === LOGIKA PEMBAYARAN DIPERBARUI ===
    public function payDownPayment(
        Customer $customer,
        Room $room,
        Request $request,
        TransactionRepositoryInterface $transactionRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        // 1. Hitung Durasi
        $dayDifference = Helper::getDateDifference($request->check_in, $request->check_out);
        
        // 2. Hitung Harga Kamar
        $roomPriceTotal = $room->price * $dayDifference;

        // 3. Hitung Harga Sarapan (Logika di Server)
        $breakfastPrice = 0;
        if ($request->breakfast === 'Yes') {
            $breakfastPrice = 140000 * $dayDifference; // Rp 140.000 per malam
        }

        // 4. Hitung Grand Total & DP
        $grandTotal = $roomPriceTotal + $breakfastPrice;
        $minimumDownPayment = $grandTotal * 0.15;

        // 5. Validasi Data
        $request->validate([
            'breakfast' => 'required|in:Yes,No',
        ]);

        // 6. Validasi Ketersediaan Kamar
        $occupiedRoomIds = $this->getOccupiedRoomID($request->check_in, $request->check_out);
        if ($occupiedRoomIds->contains($room->id)) {
            return redirect()->back()->with('failed', 'Maaf, Kamar ini baru saja dipesan orang lain.');
        }

        // 7. Masukkan Data Hasil Hitungan ke Request (Agar tersimpan di Repo)
        $request->merge([
            'total_price' => $grandTotal, // Total harga masuk database
            'downPayment' => $minimumDownPayment, // DP otomatis minimal 15%
            'status' => 'Down Payment' 
        ]);

        // 8. Simpan Transaksi
        $transaction = $transactionRepository->store($request, $customer, $room);
        
        // 9. Simpan Pembayaran
        $status = 'Down Payment';
        $payment = $paymentRepository->store($request, $transaction, $status);

        // 10. Notifikasi
        $superAdmins = User::where('role', 'Super')->get();
        foreach ($superAdmins as $superAdmin) {
            $message = 'Reservation added by ' . $customer->name;
            event(new NewReservationEvent($message, $superAdmin));
            $superAdmin->notify(new NewRoomReservationDownPayment($transaction, $payment));
        }

        event(new RefreshDashboardEvent('Someone reserved a room'));

        return redirect()->route('transaction.index')
            ->with('success', 'Room ' . $room->number . ' booked successfully!');
    }

    private function getOccupiedRoomID($checkIn, $checkOut)
    {
        return Transaction::where(function($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
            })
            ->pluck('room_id');
    }
}