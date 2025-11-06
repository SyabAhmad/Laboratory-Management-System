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
        
        // Total paid from Payments
        $totalPaymentsPaid = \App\Models\Payments::sum('amount') ?? 0;
        
        // Total paid from ReferralCommission (paid status)
        $totalCommissionsPaid = \App\Models\ReferralCommission::where('status', 'paid')
            ->sum('commission_amount') ?? 0;
        
        // Combined total paid
        $totalPaid = $totalPaymentsPaid + $totalCommissionsPaid;
        $outstanding = $totalBilled - $totalPaid;

        // Fetch bills (all billed amounts)
        $bills = \App\Models\Bills::join('patients', 'bills.patient_id', '=', 'patients.id')
            ->select(
                'bills.created_at',
                \DB::raw('CONCAT("Bill #", bills.id) as reference'),
                'bills.amount',
                \DB::raw('NULL as note'),
                'patients.name as patient_name',
                \DB::raw('"Bill" as type')
            );

        // Fetch transactions from Payments with patient info
        $payments = \App\Models\Payments::join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('patients', 'bills.patient_id', '=', 'patients.id')
            ->select(
                'payments.created_at',
                \DB::raw('CONCAT("Payment #", payments.id) as reference'),
                'payments.amount',
                'payments.note',
                'patients.name as patient_name',
                \DB::raw('"Payment" as type')
            );

        // Fetch paid commissions from ReferralCommission with bill info
        $commissions = \App\Models\ReferralCommission::where('referral_commissions.status', 'paid')
            ->join('bills', 'referral_commissions.bill_id', '=', 'bills.id')
            ->join('patients', 'referral_commissions.patient_id', '=', 'patients.id')
            ->select(
                'referral_commissions.created_at',
                \DB::raw('CONCAT("Bill #", referral_commissions.bill_id) as reference'),
                'referral_commissions.commission_amount as amount',
                'referral_commissions.notes as note',
                'patients.name as patient_name',
                \DB::raw('"Commission" as type')
            );

        // Union all queries and order by created_at desc, limit to 100
        $transactions = $bills->union($payments)->union($commissions)->orderBy('created_at', 'desc')->limit(100)->get();

        return view('Balance.index', [
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'transactions' => $transactions,
        ]);
    }
}
