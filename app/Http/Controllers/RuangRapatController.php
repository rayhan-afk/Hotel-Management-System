<?php
// app/Http/Controllers/RuangRapatController.php

namespace App\Http\Controllers;

use App\Models\RuangRapatPaket; // PANGGIL MODEL YANG BENAR
use Illuminate\Http\Request;

class RuangRapatController extends Controller
{
    /**
     * Menampilkan daftar paket.
     */
    public function index()
    {
        $pakets = RuangRapatPaket::orderBy('name', 'asc')->get();
        return view('ruangrapat.index', compact('pakets'));
    }

    /**
     * Menampilkan form tambah paket baru.
     */
    public function create()
    {
        return view('ruangrapat.create');
    }

    /**
     * Menyimpan paket baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'isi_paket' => 'required|string',
            'fasilitas' => 'required|string',
            'harga' => 'required|numeric|min:0',
        ]);

        RuangRapatPaket::create($request->all());

        return redirect()->route('ruangrapat.index')->with('success', 'Paket Ruang Rapat berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk edit paket.
     */
    public function edit($id)
    {
        $paket = RuangRapatPaket::findOrFail($id);
        return view('ruangrapat.edit', compact('paket'));
    }

    /**
     * Update paket yang ada di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'isi_paket' => 'required|string',
            'fasilitas' => 'required|string',
            'harga' => 'required|numeric|min:0',
        ]);

        $paket = RuangRapatPaket::findOrFail($id);
        $paket->update($request->all());

        return redirect()->route('ruangrapat.index')->with('success', 'Paket Ruang Rapat berhasil diperbarui.');
    }

    /**
     * Hapus paket dari database.
     */
    public function destroy($id)
    {
        $paket = RuangRapatPaket::findOrFail($id);
        $paket->delete();

        return redirect()->route('ruangrapat.index')->with('success', 'Paket Ruang Rapat berhasil dihapus.');
    }

    /**
     * Fungsi 'show' tidak kita gunakan, bisa dihapus atau biarkan.
     * Jika diklik, kita arahkan ke edit saja.
     */
    public function show($id)
    {
        return $this->edit($id);
    }
}