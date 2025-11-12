<?php

namespace App\Http\Controllers;

// Import Model yang kita buat
use App\Models\RapatCustomer;
use App\Models\RapatTransaction;
use App\Models\RuangRapatPaket; //

// Import class yang dibutuhkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // PENTING untuk menyimpan data antar step
use Carbon\Carbon; // Untuk menghitung harga berdasarkan durasi

class RuangRapatReservationController extends Controller
{
    /**
     * Kunci unik untuk session reservasi rapat.
     */
    private $sessionKey = 'rapat_reservation';

    /**
     * STEP 1: Menampilkan form data customer.
     */
    public function showStep1_CustomerInfo()
    {
        // Ambil data lama dari session jika ada (untuk fitur back/edit)
        $reservationData = Session::get($this->sessionKey, []);
        $customer = $reservationData['customer'] ?? null;
        
        return view('rapat.reservation.step1_customer', compact('customer'));
    }

    /**
     * STEP 1: Menyimpan data customer ke session.
     */
    public function storeStep1_CustomerInfo(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'instansi' => 'nullable|string|max:255',
        ]);

        // Simpan data customer ke session
        Session::put($this->sessionKey . '.customer', $validated);

        return redirect()->route('rapat.reservation.showStep2');
    }

    /**
     * STEP 2: Menampilkan form waktu.
     */
    public function showStep2_TimeInfo()
    {
        // Cek apakah data step 1 sudah ada
        if (!Session::has($this->sessionKey . '.customer')) {
            return redirect()->route('rapat.reservation.showStep1')->with('error', 'Harap isi data diri terlebih dahulu.');
        }

        $reservationData = Session::get($this->sessionKey, []);
        $timeInfo = $reservationData['time'] ?? null;
        
        // --- INI PERBAIKANNYA ---
        $customer = $reservationData['customer']; // 1. Ambil data customer dari session
        
        // 2. Kirim 'customer' ke view
        return view('rapat.reservation.step2_time', compact('timeInfo', 'customer'));
        // --- AKHIR PERBAIKAN ---
    }

    /**
     * STEP 2: Menyimpan data waktu ke session.
     */
    public function storeStep2_TimeInfo(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'tanggal_pemakaian' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        // Simpan data waktu ke session
        Session::put($this->sessionKey . '.time', $validated);

        return redirect()->route('rapat.reservation.showStep3');
    }

    /**
     * STEP 3: Menampilkan form paket.
     */
    /**
     * STEP 3: Menampilkan form paket (VERSI UPGRADE DENGAN SORTING/PAGINATION).
     */
    public function showStep3_PaketInfo(Request $request) // Tambahkan Request $request
    {
        // Cek apakah data step 2 sudah ada
        if (!Session::has($this->sessionKey . '.time')) {
            return redirect()->route('rapat.reservation.showStep2')->with('error', 'Harap isi data waktu terlebih dahulu.');
        }

        // Ambil data dari session
        $reservationData = Session::get($this->sessionKey);
        $timeInfo = $reservationData['time'];
        $customer = $reservationData['customer']; // Ambil data customer untuk card info
        $selectedPaket = $reservationData['paket'] ?? null;
        
        // Ambil parameter sorting dari request (URL)
        $sort_name = $request->input('sort_name', 'harga'); // Default sort by harga
        $sort_type = $request->input('sort_type', 'ASC'); // Default sort ASC

        // Validasi kolom sort
        if (!in_array($sort_name, ['harga', 'name'])) {
            $sort_name = 'harga';
        }
        if (!in_array($sort_type, ['ASC', 'DESC'])) {
            $sort_type = 'ASC';
        }

        // --- LOGIKA PENTING (Ketersediaan) ---
        // TODO: Tambahkan logika filter ketersediaan di sini
        
        // Ambil data paket DENGAN sorting dan pagination
        $pakets = RuangRapatPaket::orderBy($sort_name, $sort_type)
                                ->paginate(5); // Tampilkan 5 paket per halaman

        $paketsCount = $pakets->total(); // Hitung total paket

        return view('rapat.reservation.step3_paket', compact(
            'pakets', 
            'paketsCount',
            'timeInfo', 
            'customer', 
            'selectedPaket', 
            'sort_name', // Kirim balik untuk 'sticky' dropdown
            'sort_type'  // Kirim balik untuk 'sticky' dropdown
        ));
    }

    /**
     * STEP 3: Menyimpan data paket ke session.
     */
    public function storeStep3_PaketInfo(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'ruang_rapat_paket_id' => 'required|exists:ruang_rapat_pakets,id',
            'jumlah_peserta' => 'required|integer|min:1',
        ]);

        // Simpan data paket ke session
        Session::put($this->sessionKey . '.paket', $validated);

        return redirect()->route('rapat.reservation.showStep4');
    }

    /**
     * STEP 4: Menampilkan halaman konfirmasi.
     */
    public function showStep4_Confirmation()
    {
        // Cek apakah data step 3 sudah ada
        if (!Session::has($this->sessionKey . '.paket')) {
            return redirect()->route('rapat.reservation.showStep3')->with('error', 'Harap pilih paket terlebih dahulu.');
        }

        // Ambil semua data dari session
        $reservationData = Session::get($this->sessionKey);
        $customer = $reservationData['customer'];
        $timeInfo = $reservationData['time'];
        $paketInfo = $reservationData['paket'];

        // Ambil detail paket dari DB
        $paket = RuangRapatPaket::findOrFail($paketInfo['ruang_rapat_paket_id']);

        // --- LOGIKA PENTING (Hitung Harga) ---
        $waktuMulai = Carbon::parse($timeInfo['waktu_mulai']);
        $waktuSelesai = Carbon::parse($timeInfo['waktu_selesai']);
        $durasiJam = $waktuSelesai->diffInHours($waktuMulai);
        
        // Asumsi: Harga paket adalah per jam
        // Jika harga paket BUKAN per jam, ubah logika ini
        $totalHarga = $paket->harga * $durasiJam;

        // Simpan harga ke session untuk disimpan saat pembayaran
        Session::put($this->sessionKey . '.harga', $totalHarga);

        return view('rapat.reservation.step4_confirmation', compact('customer', 'timeInfo', 'paket', 'paketInfo', 'totalHarga', 'durasiJam'));
    }

    /**
     * FINAL STEP: Proses Pembayaran dan Simpan ke Database.
     */
    public function processPayment(Request $request)
    {
        // Cek apakah semua data lengkap di session
        if (!Session::has($this->sessionKey . '.harga')) {
            return redirect()->route('rapat.reservation.showStep1')->with('error', 'Sesi reservasi tidak lengkap.');
        }

        $data = Session::get($this->sessionKey);

        // --- LOGIKA SIMPAN KE DATABASE ---

        // 1. Simpan Customer
        $customer = RapatCustomer::create([
            'nama' => $data['customer']['nama'],
            'no_hp' => $data['customer']['no_hp'],
            'email' => $data['customer']['email'],
            'instansi' => $data['customer']['instansi'],
        ]);

        // 2. Simpan Transaksi
        $transaction = RapatTransaction::create([
            'rapat_customer_id' => $customer->id,
            'ruang_rapat_paket_id' => $data['paket']['ruang_rapat_paket_id'],
            'tanggal_pemakaian' => $data['time']['tanggal_pemakaian'],
            'waktu_mulai' => $data['time']['waktu_mulai'],
            'waktu_selesai' => $data['time']['waktu_selesai'],
            'status_reservasi' => 'Confirmed', // Anggap langsung confirmed setelah bayar
            'jumlah_peserta' => $data['paket']['jumlah_peserta'],
            'harga' => $data['harga'],
            'total_pembayaran' => $data['harga'], // Asumsi langsung lunas
            'status_pembayaran' => 'Paid', // Asumsi langsung lunas
        ]);

        // 3. TODO: Proses pembayaran (jika pakai payment gateway)
        // 4. TODO: Buat RapatPayment record jika Anda membuatnya

        // Hapus session setelah berhasil
        Session::forget($this->sessionKey);

        // TODO: Redirect ke halaman sukses atau invoice
        return "Reservasi Berhasil! ID Transaksi: " . $transaction->id;
    }

    /**
     * Batalkan reservasi (bersihkan session).
     */
    public function cancelReservation()
    {
        Session::forget($this->sessionKey);
        return redirect()->route('dashboard.index')->with('success', 'Reservasi ruang rapat dibatalkan.');
    }
}