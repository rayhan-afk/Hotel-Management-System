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
        
        $query = RapatTransaction::with('rapatCustomer', 'ruangRapatPaket')
                    ->orderBy('tanggal_pemakaian', 'DESC');

        // Handle Search (untuk tabel reservasi)
        if ($request->filled('search')) {
            $query->where('id', $request->input('search'))
                  ->orWhereHas('rapatCustomer', function ($q) use ($request) {
                      $q->where('nama', 'like', '%' . $request->input('search') . '%');
                  });
        }

        // Filter untuk "Reservasi Aktif & Mendatang"
        $rapatTransactions = $query->clone()
            ->where('tanggal_pemakaian', '>=', Carbon::today())
            ->paginate(10, ['*'], 'active_page'); // Paginator terpisah

        // Filter untuk "Reservasi Selesai"
        $rapatTransactionsExpired = $query->clone()
            ->where('tanggal_pemakaian', '<', Carbon::today())
            ->paginate(10, ['*'], 'expired_page'); // Paginator terpisah

        // Kirim data reservasi ke view
        return view('ruangrapat.index', compact(
            'rapatTransactions', 
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
}