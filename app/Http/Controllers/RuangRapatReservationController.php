<?php

namespace App\Http\Controllers;

// Import Model
use App\Models\RapatCustomer;
use App\Models\RapatTransaction;
use App\Models\RuangRapatPaket;

// Import class lain
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class RuangRapatReservationController extends Controller
{
    /**
     * Session Key untuk menyimpan data sementara antar step
     */
    private $sessionKey = 'rapat_reservation';
    
    /**
     * Konfigurasi Harga Sewa Ruang per Jam
     */
    private $hargaSewaPerJam = 100000; 

    // =========================================================================
    // STEP 1: DATA CUSTOMER
    // =========================================================================
    public function showStep1_CustomerInfo()
    {
        // Ambil data lama jika user kembali dari step 2
        $reservationData = Session::get($this->sessionKey, []);
        $customer = $reservationData['customer'] ?? null;
        
        return view('rapat.reservation.step1_customer', compact('customer'));
    }

    public function storeStep1_CustomerInfo(Request $request)
    {
        // Validasi data diri
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'instansi' => 'nullable|string|max:255',
        ]);

        // Simpan ke session
        Session::put($this->sessionKey . '.customer', $validated);

        return redirect()->route('rapat.reservation.showStep2');
    }

    // =========================================================================
    // STEP 2: DATA WAKTU & DURASI (UPDATED)
    // =========================================================================
    public function showStep2_TimeInfo()
    {
        if (!Session::has($this->sessionKey . '.customer')) {
            return redirect()->route('rapat.reservation.showStep1')->with('error', 'Harap isi data diri terlebih dahulu.');
        }
        
        $reservationData = Session::get($this->sessionKey, []);
        $timeInfo = $reservationData['time'] ?? null;
        $customer = $reservationData['customer']; // Untuk info di sidebar/atas

        return view('rapat.reservation.step2_time', compact('timeInfo', 'customer'));
    }

    public function storeStep2_TimeInfo(Request $request)
    {
        // Validasi Tanggal, Jam Mulai, Selesai DAN Durasi
        $validated = $request->validate([
            'tanggal_pemakaian' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            // Input baru: Durasi (Integer) untuk hitung biaya
            'durasi_jam' => 'required|integer|min:1|max:24', 
        ]);

        // Simpan ke session
        Session::put($this->sessionKey . '.time', $validated);

        return redirect()->route('rapat.reservation.showStep3');
    }

    // =========================================================================
    // STEP 3: PILIH PAKET
    // =========================================================================
    public function showStep3_PaketInfo(Request $request)
    {
        if (!Session::has($this->sessionKey . '.time')) {
            return redirect()->route('rapat.reservation.showStep2')->with('error', 'Harap isi data waktu terlebih dahulu.');
        }

        $reservationData = Session::get($this->sessionKey);
        $timeInfo = $reservationData['time'];
        $customer = $reservationData['customer'];
        $selectedPaket = $reservationData['paket'] ?? null;
        
        // Fitur Sorting Paket
        $sort_name = $request->input('sort_name', 'harga');
        $sort_type = $request->input('sort_type', 'ASC');

        if (!in_array($sort_name, ['harga', 'name'])) $sort_name = 'harga';
        if (!in_array($sort_type, ['ASC', 'DESC'])) $sort_type = 'ASC';

        // Ambil Data Paket
        $pakets = RuangRapatPaket::orderBy($sort_name, $sort_type)->paginate(5);
        $paketsCount = $pakets->total();

        return view('rapat.reservation.step3_paket', compact(
            'pakets', 'paketsCount', 'timeInfo', 'customer', 'selectedPaket', 'sort_name', 'sort_type'
        ));
    }

    public function storeStep3_PaketInfo(Request $request)
    {
        // Validasi Paket & Peserta
        $validated = $request->validate([
            'ruang_rapat_paket_id' => 'required|exists:ruang_rapat_pakets,id',
            'jumlah_peserta' => 'required|integer|min:20', // Minimal 20 sesuai request
        ], [
            'jumlah_peserta.min' => 'Mohon maaf, minimal peserta rapat adalah 20 orang.'
        ]);

        Session::put($this->sessionKey . '.paket', $validated);

        return redirect()->route('rapat.reservation.showStep4');
    }

    // =========================================================================
    // STEP 4: KONFIRMASI & HITUNG BIAYA (UPDATED)
    // =========================================================================
    public function showStep4_Confirmation()
    {
        if (!Session::has($this->sessionKey . '.paket')) {
            return redirect()->route('rapat.reservation.showStep3')->with('error', 'Harap pilih paket terlebih dahulu.');
        }

        // Ambil semua data session
        $reservationData = Session::get($this->sessionKey);
        $customer = $reservationData['customer'];
        $timeInfo = $reservationData['time'];
        $paketInfo = $reservationData['paket'];

        // Ambil Objek Paket dari DB
        $paket = RuangRapatPaket::findOrFail($paketInfo['ruang_rapat_paket_id']);

        // --- LOGIKA PERHITUNGAN BIAYA ---
        
        // 1. Ambil Durasi (Jam) yang dipilih user di Step 2
        $durasiJam = $timeInfo['durasi_jam']; 
        
        // 2. Ambil Jumlah Peserta
        $jumlahOrang = $paketInfo['jumlah_peserta'];

        // 3. Hitung Komponen Biaya
        // A. Biaya Paket = Harga Paket x Jumlah Orang
        $biayaPaketTotal = $paket->harga * $jumlahOrang;

        // B. Biaya Sewa Ruang = Rp 100.000 x Durasi Jam
        $biayaSewaRuangTotal = $this->hargaSewaPerJam * $durasiJam;

        // 4. Total Tagihan Akhir
        $totalHarga = $biayaPaketTotal + $biayaSewaRuangTotal;

        // Simpan total ke session (penting untuk proses bayar)
        Session::put($this->sessionKey . '.harga', $totalHarga);

        return view('rapat.reservation.step4_confirmation', compact(
            'customer', 
            'timeInfo', 
            'paket', 
            'paketInfo', 
            'totalHarga', 
            'durasiJam', 
            'biayaPaketTotal',
            'biayaSewaRuangTotal',
            'jumlahOrang'
        ));
    }

    // =========================================================================
    // FINAL: PROSES PEMBAYARAN (LUNAS OTOMATIS)
    // =========================================================================
    public function processPayment(Request $request)
    {
        // Cek apakah session harga ada
        if (!Session::has($this->sessionKey . '.harga')) {
            return redirect()->route('rapat.reservation.showStep1')->with('error', 'Sesi reservasi habis/tidak lengkap.');
        }
        
        // Ambil data final dari session
        $data = Session::get($this->sessionKey);
        $totalTagihan = $data['harga'];

        // 1. Simpan Customer Baru
        $customer = RapatCustomer::create([
            'nama' => $data['customer']['nama'],
            'no_hp' => $data['customer']['no_hp'],
            'email' => $data['customer']['email'],
            'instansi' => $data['customer']['instansi'],
        ]);

        // 2. Simpan Transaksi Rapat
        $transaction = RapatTransaction::create([
            'rapat_customer_id' => $customer->id,
            'ruang_rapat_paket_id' => $data['paket']['ruang_rapat_paket_id'],
            
            // Data Waktu
            'tanggal_pemakaian' => $data['time']['tanggal_pemakaian'],
            'waktu_mulai' => $data['time']['waktu_mulai'],
            'waktu_selesai' => $data['time']['waktu_selesai'],
            
            // Data Peserta & Harga
            'jumlah_peserta' => $data['paket']['jumlah_peserta'],
            'harga' => $totalTagihan,
            
            // Data Pembayaran (Otomatis Lunas)
            'total_pembayaran' => $totalTagihan,
            'status_pembayaran' => 'Paid',
            'status_reservasi' => 'Confirmed',
        ]);

        // 3. Bersihkan Session
        Session::forget($this->sessionKey);

        // 4. Redirect ke Index dengan Pesan Sukses
        return redirect()->route('ruangrapat.index')
                         ->with('success', 'Reservasi Berhasil! Transaksi #' . $transaction->id . ' status Lunas.');
    }

    /**
     * Batalkan reservasi (bersihkan session).
     */
    public function cancelReservation()
    {
        Session::forget($this->sessionKey);
        return redirect()->route('dashboard.index')->with('success', 'Reservasi dibatalkan.');
    }
}