<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LabTestCat;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{

    public function registeredTests($id)
    {
        $patient = \App\Models\Patients::findOrFail($id);

        $testNames = $patient->test_category;

        if (is_string($testNames)) {
            $decoded = json_decode($testNames, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $testNames = $decoded;
            } else {
                $testNames = array_map('trim', explode(',', $testNames));
            }
        }

        if (!is_array($testNames)) {
            $testNames = [];
        }

        // Fetch by test name instead of ID
        $tests = \App\Models\LabTestCat::whereIn('cat_name', $testNames)->get();

        return response()->json([
            'patient' => $patient,
            'tests' => $tests,
        ]);
    }





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
                    $btn  = '<a href="' . route('patients.edit', $row->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("patients.profile", $row->id) . '" class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '"><i class="fas fa-eye"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-danger btn-sm deletebtn"><i class="fas fa-trash"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("billing.create", ['id' => $row->id]) . '" class="btn btn-success btn-sm"><i class="fas fa-file-invoice-dollar"></i> Bill</a>';
                    return $btn;
                })

                ->rawColumns(['action', 'status', 'user_name', 'email', 'home_phone'])
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

    // Search patients
    public function search(Request $request)
    {
        $query = $request->get('query');

        $patients = \App\Models\Patients::where('name', 'like', "%{$query}%")
            ->orWhere('patient_id', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($patients);
    }

    // Fetch tests for a specific patient
    // public function getPatientTests($id)
    // {
    //     $patient = \App\Models\Patients::with('tests')->find($id);

    //     if (!$patient) {
    //         return response()->json(['error' => 'Patient not found'], 404);
    //     }

    //     // Assuming you have a relationship: Patient -> hasMany Tests through a pivot
    //     $tests = $patient->tests->map(function ($test) {
    //         return [
    //             'id' => $test->id,
    //             'name' => $test->name,
    //             'price' => $test->price,
    //         ];
    //     });

    //     return response()->json([
    //         'patient' => [
    //             'id' => $patient->id,
    //             'name' => $patient->name,
    //             'age' => $patient->age,
    //             'gender' => $patient->gender,
    //             'phone' => $patient->mobile_phone,
    //         ],
    //         'tests' => $tests,
    //     ]);
    // }

    // use Illuminate\Http\Request;
    // use Illuminate\Support\Facades\DB; // for fallback queries
    // use App\Models\Patients; // ensure correct model import
    // use App\Models\LabTestCat;


    // use App\Models\Patients;
    // use App\Models\LabTestCat;

    // public function registeredTests($id)
    // {
    //     $patient = Patients::find($id);
    //     if (! $patient) {
    //         return response()->json(['error' => 'Patient not found'], 404);
    //     }

    //     $tests = [];

    //     // 1) If test_category column exists and is an array (common in your code)
    //     if (!empty($patient->test_category) && is_array($patient->test_category)) {
    //         // normalize possible formats (ids or objects)
    //         $ids = array_map(function ($v) {
    //             if (is_array($v) && isset($v['id'])) return $v['id'];
    //             if (is_object($v) && isset($v->id)) return $v->id;
    //             return $v;
    //         }, $patient->test_category);

    //         $labTests = LabTestCat::whereIn('id', $ids)->get();
    //         foreach ($labTests as $t) {
    //             $tests[] = [
    //                 'id'       => $t->id,
    //                 'cat_name' => $t->cat_name,
    //                 'price'    => $t->price,
    //                 'department' => $t->department ?? '',
    //             ];
    //         }
    //     }

    //     // 2) Fallback — use many-to-many relation if available (patient_tests pivot)
    //     if (empty($tests) && method_exists($patient, 'tests')) {
    //         $labTests = $patient->tests()->get();
    //         foreach ($labTests as $t) {
    //             $tests[] = [
    //                 'id'       => $t->id,
    //                 'cat_name' => $t->cat_name,
    //                 'price'    => $t->price,
    //                 'department' => $t->department ?? '',
    //             ];
    //         }
    //     }

    //     return response()->json([
    //         'patient' => [
    //             'id' => $patient->id,
    //             'name' => $patient->name,
    //             'patient_id' => $patient->patient_id ?? null,
    //             'mobile_phone' => $patient->mobile_phone ?? null,
    //             'age' => $patient->age ?? null,
    //             'gender' => $patient->gender ?? null,
    //         ],
    //         'tests' => $tests
    //     ]);
    // }



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
        try {
            Log::info('=== TEST DATA STORE REQUEST STARTED ===');
            Log::info('Request Method: ' . $request->method());
            Log::info('Request Content Type: ' . $request->header('Content-Type'));
            Log::info('All Request Data:', $request->all());
            Log::info('JSON Input:', $request->json()->all());

            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'test_name' => 'required|string',
                'test_data' => 'required|array',
            ]);

            Log::info('Validation passed');

            $patient = Patients::findOrFail($request->input('patient_id'));
            Log::info('Patient found:', ['id' => $patient->id, 'name' => $patient->name]);

            // Get existing test reports
            $testReports = [];
            if (!empty($patient->test_report)) {
                $decoded = json_decode($patient->test_report, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $testReports = $decoded;
                }
            }

            Log::info('Existing test reports:', $testReports);

            // Add or update the test data
            $testReports[$request->input('test_name')] = $request->input('test_data');

            Log::info('Updated test reports:', $testReports);

            // Save back to database as JSON
            $jsonString = json_encode($testReports);
            Log::info('JSON string to save:', ['json' => $jsonString]);

            $patient->test_report = $jsonString;
            $saved = $patient->save();

            Log::info('Save result:', ['saved' => $saved]);

            // Verify the data was saved
            $patient->refresh();
            Log::info('Verified saved data:', ['test_report' => $patient->test_report]);

            if (!$saved) {
                Log::error('Failed to save - save() returned false');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save test data'
                ], 500);
            }

            Log::info('=== TEST DATA STORE REQUEST COMPLETED SUCCESSFULLY ===');

            return response()->json([
                'success' => true,
                'message' => 'Test data saved successfully',
                'data' => $testReports
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('=== ERROR SAVING TEST DATA ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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
        return response()->json(['success' => 'Patient deleted successfully.']);
    }
}
