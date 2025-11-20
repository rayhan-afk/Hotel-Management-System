<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interface\LaporanRepositoryInterface; // 1. Import Interface

class LaporanController extends Controller
{
    // 2. Inject Repository melalui Constructor
    public function __construct(
        private LaporanRepositoryInterface $laporanRepository
    ) {}

    /**
     * Menampilkan laporan reservasi ruang rapat.
     * Method ini menangani dua hal:
     * 1. Permintaan AJAX dari DataTables (mengembalikan JSON)
     * 2. Permintaan Halaman Biasa (mengembalikan View HTML)
     */
    public function laporanRuangRapat(Request $request)
    {
        // Jika request datang dari DataTables (AJAX)
        if ($request->ajax()) {
            return $this->laporanRepository->getLaporanRapatDatatable($request);
        }

        // Jika akses halaman biasa, tampilkan view kosong
        // Data akan di-load otomatis oleh JS setelah halaman terbuka
        return view('laporan.rapat.index');
    }

    /**
     * Stub untuk Laporan Kamar Hotel (laporan.kamar.index)
     */
    public function laporanKamarHotel(Request $request)
    {
        // Biarkan seperti ini dulu sampai fitur Laporan Kamar dibuat
        return redirect()->route('dashboard.index')
            ->with('info', 'Halaman Laporan Kamar Hotel belum dibuat.');
    }
}