<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Patients::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('age', function ($row) {
                    return $row->age;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0);" class="btn btn-warning btn-sm editbtn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></a>';
                    $btn .= '&nbsp&nbsp<a href='.(route("patients.profile", $row->id)).' class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '"><i class="fas fa-eye"></i></a>';
                    $btn = $btn . '&nbsp&nbsp<a href="javascript:void(0);" data-id="' . $row->id .'" class="btn btn-danger btn-sm deletebtn"> <i class="fas fa-trash"></i> </a>';
                    return $btn;
                })
                ->rawColumns(['action','status','user_name','email','home_phone'])
                ->make(true);
        }
        return view('Patient.patient_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Patient.patient_reg');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // basic validation (add rules you need)
        $request->validate([
            'name' => 'required|string|max:191',
            'mobile_phone' => 'nullable|string|max:50',
            'age' => 'nullable|string|max:3',
            // add other validation rules as required
        ]);

        $patientcount = Patients::count();

        $patient = new Patients;
        // ensure proper concatenation and arithmetic
        $patient->patient_id = date('Ym') . '0' . ($patientcount + 1);
        // associate currently authenticated user so $patient->user is not null in views
        $patient->user_id = Auth::id();

        $patient->name = $request->name;
        $patient->mobile_phone = $request->mobile_phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        $patient->blood_group = $request->blood_group ?? null;
        $patient->note = $request->note;
        // $patient->test_report = $request->test_report;
        $patient->referred_by = $request->referred_by;
        // store selected tests as JSON array
        $patient->test_category = $request->input('tests') ? json_encode($request->input('tests')) : null;
        $patient->registerd_by = Auth::user() ? Auth::user()->name : null;
        $patient->save();

        return redirect()->route('patients.list');
    }

    public function statuschange($id, Request $request)
    {
        $user = User::find($id);
        $user->status = $request->status;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // fail fast if not found and eager-load related models used by the view
        $patient = Patients::with(['user', 'referral'])->findOrFail($id);
        return view('Patient.patient_details', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $patient = Patients::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($patient);
        }

        return view('Patient.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patients $patients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employees = Patients::find($id);
                $employees->delete();
                return response()->json(['success'=>'Data Delete successfully.']);
    }
}
