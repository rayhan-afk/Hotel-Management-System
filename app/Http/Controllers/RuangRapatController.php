<?php

namespace App\Http\Controllers;

use App\Models\RuangRapatPaket;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RuangRapatController extends Controller
{
    /**
     * Menampilkan halaman index ATAU merespon AJAX DataTable.
     */
    public function index(Request $request)
    {
        // Jika ini adalah request AJAX (dari DataTable)
        if ($request->ajax()) {
            $model = RuangRapatPaket::query(); // Ambil model 'RuangRapatPaket'

            // Yajra akan otomatis mengambil kolom 'name', 'isi_paket', 'fasilitas', 'harga'
            return DataTables::of($model)
                ->addIndexColumn() // Menambah kolom 'DT_RowIndex' (untuk 'No')
                ->toJson();
        }

        // Jika ini request biasa (bukan AJAX), tampilkan view
        return view('ruangrapat.index');
    }

    /**
     * Menampilkan form create (untuk di-load di modal).
     */
    public function create()
    {
        $view = view('ruangrapat.create')->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    /**
     * Menyimpan paket baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Diubah dari 'nama_paket'
            'isi_paket' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            // 'kapasitas' dihapus
            'harga' => 'required|numeric',
        ]);

        $paket = RuangRapatPaket::create($request->all());

        return response()->json([
            'message' => 'Paket ' . $paket->name . ' berhasil dibuat', // Menggunakan $paket->name
        ]);
    }

    /**
     * Menampilkan detail (halaman penuh, bukan modal).
     */
    public function show(RuangRapatPaket $ruangrapat)
    {
        return view('ruangrapat.show', compact('ruangrapat'));
    }

    /**
     * Menampilkan form edit (untuk di-load di modal).
     */
    public function edit(RuangRapatPaket $ruangrapat)
    {
        $view = view('ruangrapat.edit', compact('ruangrapat'))->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    /**
     * Meng-update paket.
     */
    public function update(Request $request, RuangRapatPaket $ruangrapat)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Diubah dari 'nama_paket'
            'isi_paket' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            // 'kapasitas' dihapus
            'harga' => 'required|numeric',
        ]);

        $ruangrapat->update($request->all());

        return response()->json([
            'message' => 'Paket ' . $ruangrapat->name . ' berhasil di-update', // Menggunakan $ruangrapat->name
        ]);
    }

    /**
     * Menghapus paket.
     */
    public function destroy(RuangRapatPaket $ruangrapat)
    {
        try {
            $nama_paket = $ruangrapat->name; // Menggunakan $ruangrapat->name
            $ruangrapat->delete();

            return response()->json([
                'message' => 'Paket ' . $nama_paket . ' berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Paket ' . $ruangrapat->name . ' tidak bisa dihapus! Error Code:' . $e->errorInfo[1],
            ], 500);
        }
    }
}