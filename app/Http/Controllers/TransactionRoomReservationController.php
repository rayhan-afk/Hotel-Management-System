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
// Hapus Use PaymentRepositoryInterface
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\RoomRepositoryInterface;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionRoomReservationController extends Controller
{
    private $customerRepository;
    private $roomRepository;
    private $transactionRepository;

    // Hapus PaymentRepository dari constructor
    public function __construct(
        TransactionRepositoryInterface $transactionRepository, 
        CustomerRepositoryInterface $customerRepository, 
        RoomRepositoryInterface $roomRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->customerRepository = $customerRepository;
        $this->roomRepository = $roomRepository;
    }

    public function pickFromCustomer(Request $request, CustomerRepositoryInterface $customerRepository)
    {
        $customers = $customerRepository->getCustomers($request); 
        return view('transaction.reservation.pickFromCustomer', [
            'customers' => $customers,
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
        
        // Hitung total harga kamar
        $roomPriceTotal = $room->price * $dayDifference;
        
        // Tampilkan harga penuh sebagai 'downPayment' agar user tahu totalnya
        $downPayment = $roomPriceTotal; 

        return view('transaction.reservation.confirmation', [
            'customer' => $customer,
            'room' => $room,
            'stayFrom' => $stayFrom,
            'stayUntil' => $stayUntil,
            'downPayment' => $downPayment, 
            'dayDifference' => $dayDifference,
        ]);
    }

    public function payDownPayment(Customer $customer, Room $room, Request $request) 
    {
        // 1. Validasi Input TERLEBIH DAHULU (Best Practice)
        $request->validate([
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'breakfast' => 'required|in:Yes,No',
        ]);

        // 2. Cek Ketersediaan Kamar (Mencegah Double Booking)
        $occupiedRoomIds = $this->getOccupiedRoomID($request->check_in, $request->check_out);
        if ($occupiedRoomIds->contains($room->id)) {
            return redirect()->back()
                ->with('failed', 'Maaf, Kamar ini baru saja dipesan orang lain di tanggal yang sama.');
        }

        // 3. Hitung Durasi & Harga Total
        $dayDifference = Helper::getDateDifference($request->check_in, $request->check_out);
        $roomPriceTotal = $room->price * $dayDifference;
        $breakfastPrice = ($request->breakfast === 'Yes') ? (140000 * $dayDifference) : 0;
        $grandTotal     = $roomPriceTotal + $breakfastPrice;

        // 4. Siapkan Data untuk Repository
        // Kita merge data penting agar Repository bisa langsung pakai $request->all() atau spesifik
        $request->merge([
            'total_price' => $grandTotal,
            'status'      => 'Paid' // Pastikan ini masuk ke DB
        ]);

        // 5. Simpan Transaksi via Repository
        $this->transactionRepository->store($request, $customer, $room);
        
        // 6. Notifikasi Dashboard (Opsional, pastikan event handler ada)
        event(new RefreshDashboardEvent('New reservation created'));

        // 7. Redirect ke Dashboard dengan Pesan Sukses
        return redirect()->route('dashboard.index')
            ->with('success', 'Reservasi kamar ' . $room->number . ' berhasil! Status: Lunas.');
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