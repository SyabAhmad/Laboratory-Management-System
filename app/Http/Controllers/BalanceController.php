<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Show balance overview and recent transactions.
     */
    public function index()
    {
        // Compute totals from bills and payments to show an outstanding balance
        $totalBilled = \App\Models\Bills::sum('amount') ?? 0;
        $totalPaid = \App\Models\Payments::sum('amount') ?? 0;
        $outstanding = $totalBilled - $totalPaid;

        $transactions = \App\Models\Payments::orderBy('created_at', 'desc')->limit(100)->get();

        return view('Balance.index', [
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'transactions' => $transactions,
        ]);
    }
}
