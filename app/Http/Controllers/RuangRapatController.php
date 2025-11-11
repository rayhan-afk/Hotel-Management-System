<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRuangRapatPaketRequest;
use App\Models\RuangRapatPaket;
use App\Repositories\Interface\RuangRapatPaketRepositoryInterface;
use Illuminate\Http\Request;

class RuangRapatController extends Controller
{
    private $ruangRapatPaketRepository;

    public function __construct(RuangRapatPaketRepositoryInterface $ruangRapatPaketRepository)
    {
        $this->ruangRapatPaketRepository = $ruangRapatPaketRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->ruangRapatPaketRepository->getPaketsDatatable($request);
        }

        return view('ruangrapat.index');
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