<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRuangRapatPaketRequest;
use App\Models\RuangRapatPaket;
use App\Repositories\Interface\RuangRapatPaketRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\RapatTransaction;
use Carbon\Carbon;

class RuangRapatController extends Controller
{
    private $ruangRapatPaketRepository;

    public function __construct(RuangRapatPaketRepositoryInterface $ruangRapatPaketRepository)
    {
        $this->ruangRapatPaketRepository = $ruangRapatPaketRepository;
    }

    public function index(Request $request)
    {
        // Bagian 1: Logika DataTable (Dari kode lama Anda)
        // Jika ini adalah request AJAX dari DataTable, layani data paket
        if ($request->ajax()) {
            return $this->ruangRapatPaketRepository->getPaketsDatatable($request);
        }

        // Bagian 2: Logika Daftar Reservasi (Logika baru)
        // Jika ini adalah kunjungan halaman biasa, ambil data reservasi
        
        $now = Carbon::now();
        
        // Query dasar untuk search
        $baseQuery = RapatTransaction::with('rapatCustomer', 'ruangRapatPaket');

        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('id', $request->input('search'))
                  ->orWhereHas('rapatCustomer', function ($subQ) use ($request) {
                      $subQ->where('nama', 'like', '%' . $request->input('search') . '%');
                  });
            });
        }

        // --- KOLOM KIRI: JADWAL RESERVASI ---
        // (Semua reservasi hari ini dan ke depan, diurutkan dari yang paling dekat)
        $rapatTransactionsJadwal = $baseQuery->clone()
            ->where('tanggal_pemakaian', '>=', $now->format('Y-m-d'))
            ->orderBy('tanggal_pemakaian', 'ASC')
            ->orderBy('waktu_mulai', 'ASC')
            ->paginate(10, ['*'], 'jadwal_page'); // Paginator: jadwal_page

        // --- KOLOM KANAN: RESERVASI BERLANGSUNG ---
        // (Reservasi hari ini DAN sedang berlangsung SAAT INI)
        $rapatTransactionsBerlangsung = $baseQuery->clone()
            ->where('tanggal_pemakaian', $now->format('Y-m-d')) // Hari ini
            ->where('waktu_mulai', '<=', $now->format('H:i:s')) // Sudah mulai
            ->where('waktu_selesai', '>=', $now->format('H:i:s')) // Belum selesai
            ->orderBy('waktu_mulai', 'ASC')
            ->paginate(10, ['*'], 'berlangsung_page'); // Paginator: berlangsung_page

        // --- TABEL BAWAH: RESERVASI SELESAI ---
        $rapatTransactionsExpired = $baseQuery->clone()
            ->where('tanggal_pemakaian', '<', $now->format('Y-m-d'))
            ->orderBy('tanggal_pemakaian', 'DESC')
            ->paginate(10, ['*'], 'expired_page'); // Paginator: expired_page

        // Kirim semua data reservasi ke view
        return view('ruangrapat.index', compact(
            'rapatTransactionsJadwal', 
            'rapatTransactionsBerlangsung',
            'rapatTransactionsExpired'
        ));
    }

    // --- PERBAIKAN METHOD CREATE ---
    public function create()
    {
        // render() mengubah view menjadi string HTML
        $view = view('ruangrapat.create')->render();

        // Kirim sebagai JSON agar bisa ditangkap oleh JavaScript
        return response()->json([
            'view' => $view,
        ]);
    }

    public function store(StoreRuangRapatPaketRequest $request)
    {
        RuangRapatPaket::create($request->validated());

        return response()->json([
            'message' => 'Paket berhasil ditambahkan!',
        ]);
    }

    public function show(RuangRapatPaket $ruangrapat)
    {
        return view('ruangrapat.show', compact('ruangrapat'));
    }

    // --- PERBAIKAN METHOD EDIT ---
    public function edit(RuangRapatPaket $ruangrapat)
    {
        // render() mengubah view menjadi string HTML
        $view = view('ruangrapat.edit', [
            'ruangrapat' => $ruangrapat
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function update(StoreRuangRapatPaketRequest $request, RuangRapatPaket $ruangrapat)
    {
        $ruangrapat->update($request->validated());

        return response()->json([
            'message' => 'Paket berhasil diperbarui!',
        ]);
    }

    public function destroy(RuangRapatPaket $ruangrapat)
    {
        try {
            $ruangrapat->delete();
            return response()->json(['message' => 'Paket berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus paket!'], 500);
        }
    }
    public function cancelReservation(RapatTransaction $rapatTransaction)
    {
        try {
            // Hapus data reservasi
            $rapatTransaction->delete();
            
            // Redirect kembali ke halaman index ruang rapat dengan pesan sukses
            return redirect()->route('ruangrapat.index')
                         ->with('success', 'Reservasi ID ' . $rapatTransaction->id . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            // Jika gagal, redirect kembali dengan pesan error
            return redirect()->route('ruangrapat.index')
                         ->with('error', 'Gagal membatalkan reservasi.');
        }
    }
}