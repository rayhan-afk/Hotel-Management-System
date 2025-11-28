<?php

namespace App\Http\Controllers;

use App\Repositories\Interface\KamarDibersihkanRepositoryInterface;
use Illuminate\Http\Request;

class KamarDibersihkanController extends Controller
{
    private $kamarDibersihkanRepository;

    public function __construct(KamarDibersihkanRepositoryInterface $kamarDibersihkanRepository)
    {
        $this->kamarDibersihkanRepository = $kamarDibersihkanRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->kamarDibersihkanRepository->getDatatable($request)
            );
        }

        return view('room-info.cleaning');
    }
}