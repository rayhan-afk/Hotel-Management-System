<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return view('transaction.index', [
                'transactions' => $this->transactionRepository->getTransaction($request),
                'transactionsExpired' => $this->transactionRepository->getTransactionExpired($request)
            ]);
        }

        return view('transaction.index', [
            'transactions' => $this->transactionRepository->getTransaction($request),
            'transactionsExpired' => $this->transactionRepository->getTransactionExpired($request)
        ]);
    }

    public function show(Transaction $transaction)
    {
        return view('transaction.show', compact('transaction'));
    }
}