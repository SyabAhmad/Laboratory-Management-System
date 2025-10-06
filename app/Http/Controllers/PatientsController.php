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
                    $btn = '<a href="'.route('patients.edit', $row->id).'" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
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
        $request->validate([
            'name' => 'required|string|max:191',
            'mobile_phone' => 'required|string|max:50',
            'address' => 'required|string',
            'gender' => 'required|string',
            'age' => 'required|string|max:3',
            'receiving_date' => 'required|date',
            'reporting_date' => 'required|date',
            'test_category'   => 'required|array|min:1',
            'test_category.*' => 'string|max:255',
        ]);

        $patientcount = Patients::count();

        $patient = new Patients;
        $patient->patient_id = date('Ym') . '0' . ($patientcount + 1);
        $patient->user_id = Auth::id();

        $patient->name = $request->name;
        $patient->mobile_phone = $request->mobile_phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        $patient->blood_group = $request->blood_group ?? null;
        $patient->receiving_date = $request->receiving_date;
        $patient->reporting_date = $request->reporting_date;
        $patient->note = $request->note;
        $patient->referred_by = $request->referred_by;
        
        // Convert array to JSON for storage
        $patient->test_category = json_encode($request->test_category);
        
        $patient->registerd_by = Auth::user() ? Auth::user()->name : null;
        $patient->save();

        return redirect()->route('patients.list')->with('success', 'Patient registered successfully');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Patients::with(['user', 'bills'])->findOrFail($id);
        return view('Patient.patient_details', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $patient = Patients::findOrFail($id);
        
        // Decode test_category if it's JSON
        if (!empty($patient->test_category)) {
            $patient->test_category_array = json_decode($patient->test_category, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If it's not valid JSON, try to split by comma
                $patient->test_category_array = array_map('trim', explode(',', $patient->test_category));
            }
        } else {
            $patient->test_category_array = [];
        }
        
        return view('Patient.patient_edit', compact('patient'));
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
        $request->validate([
            'name' => 'required|string|max:191',
            'mobile_phone' => 'nullable|string|max:50',
            'age' => 'nullable|string|max:3',
            'receiving_date' => 'required|date',
            'reporting_date' => 'required|date',
        ]);

        $patient = Patients::findOrFail($id);
        
        $patient->name = $request->name;
        $patient->mobile_phone = $request->mobile_phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        $patient->blood_group = $request->blood_group ?? null;
        $patient->receiving_date = $request->receiving_date;
        $patient->reporting_date = $request->reporting_date;
        $patient->note = $request->note;
        $patient->referred_by = $request->referred_by;
        $patient->save();

        return redirect()->route('patients.edit', $id)->with('success', 'Patient updated successfully');
    }

    /**
     * Store test data for a patient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTestData(Request $request)
    {
        // This will be implemented to store test data
        // For now, just return success
        return response()->json(['success' => 'Test data saved successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $patient = Patients::find($id);
        $patient->delete();
        return response()->json(['success'=>'Patient deleted successfully.']);
    }
}
