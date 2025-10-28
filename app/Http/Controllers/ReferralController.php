<?php

namespace App\Http\Controllers;

use App\Models\Referrals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Referrals::query()->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0);" class="btn btn-warning btn-sm editbtn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></a>';
                    $btn = $btn . '&nbsp&nbsp<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-danger btn-sm deletebtn"> <i class="fas fa-trash"></i> </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                // ->make(true);
                ->toJson();
        }
        return view('referrel.referrel');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('referrel.add_referral');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $referral = new Referrals;
        $referral->name = $request->name;
        $referral->email = $request->email;
        $referral->phone = $request->phone;
        $referral->save();
        return response()->json($referral);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Referrals  $referrals
     * @return \Illuminate\Http\Response
     */
    public function show(Referrals $referrals)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Referrals  $referrals
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $referral = Referrals::find($id);
        return response()->json($referral);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Referrals  $referrals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $referral = Referrals::find($request->id);
        $referral->name = $request->name1;
        $referral->email = $request->email1;
        $referral->phone = $request->phone1;
        $referral->update();
        return response()->json($referral);
    }

    public function patients(Request $request)
    {
        // Handle single referral view with pagination
        if ($request->has('referral_id') && $request->referral_id) {
            $referral = Referrals::findOrFail($request->referral_id);

            $patientsQuery = $referral->patients()->orderBy('receiving_date', 'desc');

            // Apply search filter if provided
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $patientsQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('patient_id', 'like', '%' . $search . '%');
                });
            }

            $patients = $patientsQuery->paginate(15);

            return view('referrel.referral_detail', compact('referral', 'patients'));
        }

        // Handle all referrals view
        $query = Referrals::with(['patients' => function($q) {
            $q->orderBy('receiving_date', 'desc');
        }]);

        // Search functionality for all referrals
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('patients', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('patient_id', 'like', '%' . $search . '%');
            });
        }

        $referrals = $query->get();

        // Calculate total patients
        $totalPatients = $referrals->sum(function($referral) {
            return $referral->patients->count();
        });

        return view('referrel.patients', compact('referrals', 'totalPatients'));
    }

    public function patientReport()
    {
        $referrals = Referrals::all();
        return view('Reports.patientlist', compact('referrals'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Referrals  $referrals
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $referral = Referrals::find($id);
        $referral->delete();
        return response()->json(['success' => 'Referral deleted successfully.']);
    }
}
