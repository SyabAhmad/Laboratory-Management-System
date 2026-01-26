<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Payments;
use App\Models\Referrals;
use App\Models\TestReport;
use App\Models\MainCompanys;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportGenarationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function patientindex()
    {
        return view('allreport.patientlist');
    }

    public function patientindexData(Request $request)
    {
        $query = Patients::with('referral')->select('patients.*');

        $min = $request->input('min');
        $max = $request->input('max');

        if ($min && $max) {
            $start = Carbon::createFromFormat('Y-m-d', $min)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $max)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($min) {
            $start = Carbon::createFromFormat('Y-m-d', $min)->startOfDay();
            $query->where('created_at', '>=', $start);
        } elseif ($max) {
            $end = Carbon::createFromFormat('Y-m-d', $max)->endOfDay();
            $query->where('created_at', '<=', $end);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->orderColumn('DT_RowIndex', 'id $1')
            ->addColumn('referral_name', function ($item) {
                if ($item->referred_by === 'none') {
                    return 'None';
                }
                return optional($item->referral)->name ?? 'N/A';
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d') : '';
            })
            ->make(true);
    }
    public function ledger()
    {
        return view('allreport.ledger');
    }
    public function ledgerdetails(Request $request)
    {
        $data1 = $request->input('from');
        $data2 = $request->input('to');

        // All payments in date range
        $data3 = Payments::whereBetween('created_at', [$data1, $data2])
            ->orderBy('created_at')
            ->get();

        // Total income (sum of all payments)
        $data4 = Payments::whereBetween('created_at', [$data1, $data2])
            ->sum('amount');

        // No expense data, so set to 0
        $data5 = 0;

        // Previous balance (sum of payments before start date)
        $previousdate = Carbon::createFromDate($request->input('from'))->subDays();

        $data6 = Payments::whereBetween('created_at', ['2000-01-01', $previousdate])
            ->sum('amount');

        $data7 = 0; // No expenses
        $data8 = $data6 - $data7; // Previous balance

        return view('allreport.ledgerdetails', compact('data1', 'data3', 'data8', 'data4', 'data5'));
    }


    public function referrallist()
    {
        return view('allreport.referrallist');
    }

    public function referrallistData()
    {
        $query = Referrals::withCount('patients');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->orderColumn('DT_RowIndex', 'id $1')
            ->addColumn('total_referred', function ($item) {
                return $item->patients_count ?? 0;
            })
            ->make(true);
    }

    public function reportbooth()
    {
        $testreport = TestReport::where('status', '=', 'Test Complete')
            ->orderBy('updated_at', 'DESC')
            ->paginate(50);
        return view('XrayReport.reportbooth', compact('testreport'));
    }
    public function report_statuschange($id, $status)
    {
        $testreport = TestReport::find($id);
        $testreport->status = $status;
        $testreport->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }

    public function report_details($id)
    {
        $testreport = TestReport::find($id);
        $company = MainCompanys::find(1);
        return view('XrayReport.report_details', compact('testreport', 'company'));
    }

    /**
     * Daily finance summary: shows all payments for a given day (default: today)
     */
    public function dailyFinance(Request $request)
    {
        $date = $request->input('date') ?: Carbon::now()->format('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();

        $payments = Payments::whereBetween('created_at', [$start, $end])->orderBy('created_at')->get();
        $total = $payments->sum('amount');

        return view('allreport.daily_finance', compact('date', 'payments', 'total'));
    }
    // public function expanseledger()
    // {
    //     return view('allreport.expense');
    // }

    // public function expanseledgerdetails(Request $request)
    // {
    //     $data1 = $request->input('from');
    //     $data2 = $request->input('to');

    //     $data3 = Payments::whereBetween('date', [$data1, $data2])
    //         ->orderBy('date')->get();

    //     $data4 = Payments::where('type', 'Income')
    //         ->whereBetween('date', [$data1, $data2])
    //         ->orderBy('date')
    //         ->get()
    //         ->sum('amount');

    //     $data5 = Payments::where('type', 'Expense')
    //         ->whereBetween('date', [$data1, $data2])
    //         ->orderBy('date')
    //         ->get()
    //         ->sum('amount');

    //     $previousdate = Carbon::createFromDate($request->input('from'))->subDays();
    //     $data6 = Payments::where('type','Income')
    //     ->whereBetween('date',['2000-01-01',$previousdate])
    //     ->get()
    //     ->sum('amount');

    //     $data7 = Payments::where('type','Expense')
    //     ->whereBetween('date',['2000-01-01',$previousdate])
    //     ->orderBy('date')
    //     ->get()
    //     ->sum('amount');

    //     $data8 = $data6 - $data7;

    //     return view('allreport.expanseled', compact('data1','data3','data8','data4','data5' ));
    // }
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
        //
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
