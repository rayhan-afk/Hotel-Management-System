<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Repositories\Interface\CheckoutRepositoryInterface;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    private $checkoutRepository;

    public function __construct(CheckoutRepositoryInterface $checkoutRepository)
    {
        $this->checkoutRepository = $checkoutRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // === PERHATIKAN PERUBAHAN NAMA METHOD INI ===
            return response()->json(
                $this->checkoutRepository->getCheckoutDatatable($request)
            );
            // =============================================
        }

        return view('transaction.checkout.index');
    }

    public function processCheckout($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $transaction->update([
            'status' => 'Checked Out'
        ]);

        return response()->json(['message' => 'Checkout berhasil diproses']);
    }
}