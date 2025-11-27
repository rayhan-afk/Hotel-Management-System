<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Repositories\Interface\LaporanKamarRepositoryInterface;

class LaporanKamarController extends Controller
{
    // Inject Repository lewat Constructor
    public function __construct(
        private LaporanKamarRepositoryInterface $laporanKamarRepository
    ) {}

    public function index(Request $request)
    {
        // Jika request AJAX (dari DataTables)
        if ($request->ajax()) {
            return $this->laporanKamarRepository->getLaporanKamarDatatable($request);
        }

        // Jika request biasa (View Halaman Awal)
        $query = $this->laporanKamarRepository->getLaporanKamarQuery($request);
        $transactions = $query->paginate(10)->appends($request->all());

        return view('laporan.kamar.index', compact('transactions'));
    }

    // RENAMED from export to exportExcel to match route definition
    public function exportExcel(Request $request)
    {
        // 1. Ambil Data dari Repository
        $query = $this->laporanKamarRepository->getLaporanKamarQuery($request);
        $transactions = $query->get();

        // 2. Setup Header CSV
        $fileName = 'laporan_reservasi_kamar_' . date('d-m-Y_H-i') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // 3. Callback Streaming
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            // Header Kolom
            fputcsv($file, [
                'ID Transaksi', 'Nama Tamu', 'Kamar', 'Tipe Kamar', 
                'Check In', 'Check Out', 'Sarapan', 
                'Total Harga (Rp)', 'Status'
            ]);

            // Isi Data
            foreach ($transactions as $t) {
                $totalHarga = $t->total_price ?? $t->getTotalPrice();
                
                $data = [
                    '#' . $t->id,
                    $t->customer->name,
                    $t->room->number,
                    $t->room->type->name ?? '-',
                    $t->check_in, 
                    $t->check_out,
                    $t->breakfast,
                    $totalHarga, 
                    'Paid'
                ];
                fputcsv($file, $data);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}