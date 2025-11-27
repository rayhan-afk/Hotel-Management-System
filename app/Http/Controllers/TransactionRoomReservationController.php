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
        // 1. Hitung Durasi & Harga
        $dayDifference = Helper::getDateDifference($request->check_in, $request->check_out);
        $roomPriceTotal = $room->price * $dayDifference;

        $breakfastPrice = 0;
        if ($request->breakfast === 'Yes') {
            $breakfastPrice = 140000 * $dayDifference; 
        }

        $grandTotal = $roomPriceTotal + $breakfastPrice;

        $request->validate([
            'breakfast' => 'required|in:Yes,No',
        ]);

        // 2. Cek Ketersediaan
        $occupiedRoomIds = $this->getOccupiedRoomID($request->check_in, $request->check_out);
        if ($occupiedRoomIds->contains($room->id)) {
            return redirect()->back()->with('failed', 'Maaf, Kamar ini baru saja dipesan orang lain.');
        }

        // 3. Siapkan Data Transaksi
        $request->merge([
            'total_price' => $grandTotal,
            'status' => 'Paid' // Langsung Lunas
        ]);

        // 4. SIMPAN TRANSAKSI SAJA (Hapus penyimpanan Payment)
        $this->transactionRepository->store($request, $customer, $room);
        
        // 5. Notifikasi (Hanya Event sederhana, JANGAN panggil notifikasi email Payment)
        $superAdmins = User::where('role', 'Super')->get();
        foreach ($superAdmins as $superAdmin) {
            $message = 'Reservation added by ' . $customer->name;
            event(new NewReservationEvent($message, $superAdmin));
            
            // BAGIAN INI DIHAPUS KARENA MEMBUTUHKAN DATA PAYMENT:
            // $superAdmin->notify(new NewRoomReservationDownPayment($transaction, $payment)); 
        }

        event(new RefreshDashboardEvent('Someone reserved a room'));

        return redirect()->route('dashboard.index')
            ->with('success', 'Room ' . $room->number . ' booked successfully!');
            $request->validate([
    'breakfast' => 'required|in:Yes,No',
]);
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