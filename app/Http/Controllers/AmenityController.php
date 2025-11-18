<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('amenity.index');
    }

    /**
     * Tampilkan form untuk membuat resource baru.
     */
    public function create()
    {
        // Akan membuat view ini di Langkah 2
        return view('amenity.create');
    }

    /**
     * Simpan resource baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        Amenity::create($validatedData);

        return redirect()->route('amenity.index')
                         ->with('success', 'Data amenity berhasil ditambahkan.');
    }

    /**
     * Tampilkan form untuk mengedit resource.
     */
    public function edit(Amenity $amenity)
    {
        // Akan membuat view ini di Langkah 3
        return view('amenity.edit', compact('amenity'));
    }

    /**
     * Update resource di database.
     */
    public function update(Request $request, Amenity $amenity)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $amenity->update($validatedData);

        return redirect()->route('amenity.index')
                         ->with('success', 'Data amenity berhasil diperbarui.');
    }

    /**
     * Hapus resource dari database.
     */
    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        
        // Redirect kembali ke index
        return redirect()->route('amenity.index')
                         ->with('success', 'Data amenity berhasil dihapus.');
    }

    /**
     * Proses data untuk DataTables.
     * (Method dari langkah sebelumnya)
     */
    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = Amenity::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_stok', function ($row) {
                    if ($row->stok == 0) {
                        return '<span class="badge bg-danger">Habis</span>';
                    } elseif ($row->stok <= 20) {
                        return '<span class="badge bg-warning text-dark">Menipis</span>';
                    } else {
                        return '<span class="badge bg-success">Tersedia</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    // Tombol Edit
                    $editBtn = '<a href="' . route('amenity.edit', $row->id) . '" class="btn btn-warning btn-sm me-1">Edit</a>';
                    
                    // Tombol Delete (Gunakan form untuk method DELETE)
                    $deleteBtn = '<form action="' . route('amenity.destroy', $row->id) . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Yakin ingin menghapus data ini?\')">'
                               . csrf_field()
                               . method_field('DELETE')
                               . '<button type="submit" class="btn btn-danger btn-sm">Delete</button>'
                               . '</form>';
                    
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['status_stok', 'action'])
                ->make(true);
        }
    }
}