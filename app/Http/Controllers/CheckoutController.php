<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interface\CheckoutRepositoryInterface;
use App\Repositories\Interface\LaporanKamarRepositoryInterface;

class CheckoutController extends Controller
{
    private $checkoutRepository;
    private $laporanRepository;

    public function __construct(
        CheckoutRepositoryInterface $checkoutRepository,
        LaporanKamarRepositoryInterface $laporanRepository
    ) {
        $this->checkoutRepository = $checkoutRepository;
        $this->laporanRepository = $laporanRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->checkoutRepository->getCheckoutDatatable($request)
            );
        }

        return view('transaction.checkout.index');
    }

    /**
     * PROSES CHECKOUT:
     * - simpan ke laporan kamar
     * - hapus dari table transaksi yang aktif (check-in)
     */
    public function processCheckout($id)
    {
        // 1. Ambil data transaksi
        $transaction = $this->checkoutRepository->find($id);

        // 2. Simpan ke laporan (INSERT)
        $this->laporanRepository->saveToLaporan($transaction);

        // 3. Hapus dari data check-in (DELETE)
        $this->checkoutRepository->checkoutDelete($id);

        return response()->json([
            'message' => 'Tamu berhasil checkout & data masuk ke Laporan Kamar!'
        ]);
    }
}
