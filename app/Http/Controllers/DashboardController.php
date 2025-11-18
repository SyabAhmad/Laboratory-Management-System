<?php

namespace App\Http\Controllers;

use App\Models\MainCompanys;
use App\Models\Payments;
use App\Models\ReferralCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Bills;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = MainCompanys::first();
        
        if (!$company) {
            return view('maincompany.maincompany');
        }
        
        // Calculate total balance from bills
        $totalBilled = Bills::sum('amount') ?? 0;
        
        // Calculate total paid from Payments table
        $totalPaymentsPaid = Payments::sum('amount') ?? 0;
        
        // Calculate total paid from ReferralCommission (paid status)
        $totalCommissionsPaid = ReferralCommission::where('status', 'paid')
            ->sum('commission_amount') ?? 0;
        
        // Combined total paid
        $totalPaid = $totalPaymentsPaid + $totalCommissionsPaid;
        
        // Outstanding balance
        $outstandingBalance = $totalBilled - $totalPaid;

        // Today's stats
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $billedToday = Bills::whereBetween('created_at', [$todayStart, $todayEnd])->sum('amount') ?? 0;
        $paidToday = Payments::whereBetween('created_at', [$todayStart, $todayEnd])->sum('amount') ?? 0;
        $billsCountToday = Bills::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $paymentsCountToday = Payments::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        // Prepare monthly billed and paid totals for last 12 months
        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        // Months labels (YYYY-MM) in ascending order
        $period = [];
        $cursor = (clone $start);
        while ($cursor->lte($end)) {
            $period[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        // Query billed amounts grouped by month
        $billedRows = DB::table('bills')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Query paid amounts from Payments table
        $paidPaymentsRows = DB::table('payments')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        // Query paid commissions from ReferralCommission table
        $paidCommissionsRows = DB::table('referral_commissions')
            ->where('status', 'paid')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(commission_amount),0) as total'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        // Merge paid amounts (payments + commissions)
        $paidRows = [];
        foreach ($period as $month) {
            $paidRows[$month] = ($paidPaymentsRows[$month] ?? 0) + ($paidCommissionsRows[$month] ?? 0);
        }

        $labels = array_map(function($m){ return Carbon::createFromFormat('Y-m', $m)->format('M Y'); }, $period);
        $billedData = array_map(function($m) use ($billedRows){ return isset($billedRows[$m]) ? (float)$billedRows[$m] : 0; }, $period);
        $paidData = array_map(function($m) use ($paidRows){ return isset($paidRows[$m]) ? (float)$paidRows[$m] : 0; }, $period);

        return view('dashboard', [
            'company' => $company,
            'totalBalance' => $outstandingBalance,
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'billedToday' => $billedToday,
            'paidToday' => $paidToday,
            'billsCountToday' => $billsCountToday,
            'paymentsCountToday' => $paymentsCountToday,
            'chartLabels' => $labels,
            'chartBilled' => $billedData,
            'chartPaid' => $paidData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lab = new MainCompanys;
        $lab->id = 1;
        $lab->lab_name = $request->lab_name;
        $lab->lab_address = $request->lab_address;
        $lab->lab_phone = $request->lab_phone;
        $lab->lab_email = $request->lab_email;
        $lab->balance = 0;
        if ($request->hasFile('lab_image')) {
            $file = $request->file('lab_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/assets/HMS/lablogo/'), $filename);
            $lab->lab_image = $filename;
        }
        $lab->save();
        return response()->json(['success' => 'Data Add successfully.']);
    }

    public function details(){
        return view('maincompany.details');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
