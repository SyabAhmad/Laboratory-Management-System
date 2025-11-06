<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\User;
use App\Models\PatientReceipt;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LabTestCat;
use App\Models\Tests\TestModelFactory;
use App\Models\Tests\CBC;
use App\Models\Tests\Urinal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Bills;

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
     * Get registered tests for edit view with templates and saved data
     * Now handles the new associative format: test_report = {"CBC": {...}, "Urinal": {...}}
     *
     * @param  int  $id
     * @return array
     */
    public function getEditTestData($id)
    {
        $patient = Patients::findOrFail($id);

        // Parse registered tests from test_category
        $selectedTests = [];
        if (!empty($patient->test_category)) {
            $decoded = json_decode($patient->test_category, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $selectedTests = $decoded;
            } else {
                $selectedTests = array_map('trim', explode(',', $patient->test_category));
            }
        }

        // Parse saved test reports - expected associative: {"TestName": {...}, ...}
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];

        $testsWithData = [];
        $processedTests = [];

        // 🔹 Handle each registered test
        foreach ($selectedTests as $testName) {
            $testName = trim($testName);
            $processedTests[$testName] = true;

            $testData = $existingTestReports[$testName] ?? [];

            // 🔹 Load template dynamically from DB
            $labTestCategory = \App\Models\LabTestCat::where('cat_name', $testName)->first();

            if ($labTestCategory) {
                $parameters = $labTestCategory->parameters()->get();
                $fields = [];

                foreach ($parameters as $param) {
                    $unitStr = $param->unit ? ' (' . $param->unit . ')' : '';
                    $refRangeStr = $param->reference_range ? ' - Ref: ' . $param->reference_range : '';

                    // Normalize parameter name to a safe key for form input / JSON storage
                    $key = Str::slug($param->parameter_name, '_');

                    $fields[] = [
                        'name' => $key, // use slug key in the template
                        'label' => $param->parameter_name . $unitStr . $refRangeStr,
                        'orig_name' => $param->parameter_name, // keep original for mapping
                        'type' => 'text',
                        'required' => false,
                    ];
                }

                // Only use DB-driven fields; do not add any predefined/report metadata fields
                $template = !empty($fields) ? ['fields' => $fields] : null;

                $savedData = $this->flattenTestData($testData);

                // Map any existing values saved under original parameter names to the slug keys
                foreach ($parameters as $param) {
                    $orig = $param->parameter_name;
                    $key = Str::slug($orig, '_');
                    if (isset($savedData[$orig]) && !isset($savedData[$key])) {
                        $savedData[$key] = $savedData[$orig];
                    }
                }
            } else {
                // No DB-driven template available
                $template = null;
                $savedData = $this->flattenTestData($testData);
            }
            $isMllpData = isset($testData['instrument']) || isset($testData['analytes']);

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template, // ✅ ensure included
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => $labTestCategory ? true : false,
                'is_mllp_data' => $isMllpData,
            ];
        }

        // 🔹 Add any extra analyzer (MLLP) tests not in category
        foreach ($existingTestReports as $testName => $testData) {
            if (isset($processedTests[$testName])) continue;

            $processedTests[$testName] = true;
            $isMllpData = isset($testData['instrument']) || isset($testData['analytes']);

            $analytes = $testData['analytes'] ?? [];
            $fields = [];

            foreach ($analytes as $analyte) {
                if (is_array($analyte) && isset($analyte['name'], $analyte['value'])) {
                    $unitStr = isset($analyte['units']) ? ' (' . $analyte['units'] . ')' : '';
                    $refRangeStr = isset($analyte['ref_range']) ? ' - Ref: ' . $analyte['ref_range'] : '';

                    $key = Str::slug($analyte['name'], '_');

                    $fields[] = [
                        'name' => $key,
                        'label' => $analyte['name'] . $unitStr . $refRangeStr,
                        'orig_name' => $analyte['name'],
                        'type' => 'text',
                        'required' => false,
                    ];
                }
            }

            $template = !empty($fields)
                ? [
                    'fields' => array_merge([
                        [
                            'name' => 'reported_at',
                            'label' => 'Reported At',
                            'type' => 'text',
                            'required' => false,
                        ],
                        [
                            'name' => 'instrument',
                            'label' => 'Instrument',
                            'type' => 'text',
                            'required' => false,
                        ],
                        [
                            'name' => 'accession_no',
                            'label' => 'Accession No',
                            'type' => 'text',
                            'required' => false,
                        ],
                    ], $fields)
                ]
                : [
                    'fields' => [
                        [
                            'name' => 'result',
                            'label' => 'Test Result',
                            'type' => 'textarea',
                            'required' => true,
                        ],
                        [
                            'name' => 'notes',
                            'label' => 'Notes/Comments',
                            'type' => 'textarea',
                            'required' => false,
                        ],
                    ]
                ];

            $savedData = $this->flattenTestData($testData);

            // Map analyte original names to slug keys so saved_data matches template keys
            foreach ($analytes as $analyte) {
                if (is_array($analyte) && isset($analyte['name'])) {
                    $orig = $analyte['name'];
                    $key = Str::slug($orig, '_');
                    if (isset($savedData[$orig]) && !isset($savedData[$key])) {
                        $savedData[$key] = $savedData[$orig];
                    }
                }
            }

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template,
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => false,
                'is_mllp_data' => $isMllpData,
            ];
        }

        return [
            'selectedTests' => $selectedTests,
            'testsWithData' => $testsWithData,
            'existingTestReports' => $existingTestReports,
        ];
    }


    /**
     * Helper function to flatten test data for display
     * Converts analytes array to key-value pairs
     *
     * @param  array $testData
     * @return array
     */
    private function flattenTestData($testData)
    {
        $savedData = [];
        if (is_array($testData)) {
            foreach ($testData as $k => $v) {
                if ($k === 'analytes' && is_array($v)) {
                    // Convert analytes array to key-value pairs
                    foreach ($v as $analyte) {
                        if (is_array($analyte) && isset($analyte['name']) && array_key_exists('value', $analyte)) {
                            $savedData[$analyte['name']] = $analyte['value'];
                        }
                    }
                } else {
                    $savedData[$k] = $v;
                }
            }
        } else {
            $savedData = $testData;
        }

        return $savedData;
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Exclude patients who are "completed": have test_report filled and have a paid bill.
            // First collect IDs of completed patients, then exclude them from main listing.
            $completedIds = Patients::whereNotNull('test_report')
                ->where('test_report', '<>', '')
                ->where('test_report', '<>', 'null')
                ->where('test_report', '<>', '{}')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('bills')
                        ->whereColumn('bills.patient_id', 'patients.id')
                        ->where('bills.status', 'paid');
                })
                ->pluck('id')
                ->toArray();

            $data = Patients::whereNotIn('id', $completedIds)
                ->orderBy('id', 'DESC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('age', function ($row) {
                    return $row->age;
                })
                ->addColumn('data_status', function ($row) {
                    $hasData = !empty($row->test_report) && $row->test_report !== '{}' && $row->test_report !== 'null';
                    return $hasData ? 'Complete' : 'Pending';
                })
                ->addColumn('bill_status', function ($row) {
                    $bill = Bills::where('patient_id', $row->id)->first();
                    if (!$bill) return 'No Bill';
                    $isPaid = strtolower($bill->status ?? '') === 'paid' || ((float)($bill->paid_amount ?? 0) >= (float)($bill->total_price ?? $bill->amount ?? 0) && (float)($bill->total_price ?? $bill->amount ?? 0) > 0);
                    return $isPaid ? 'Paid' : 'Unpaid';
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
     * Return patients with test data filled and bill paid (for completed table)
     */
    public function completedList(Request $request)
    {
        if ($request->ajax()) {
            $data = Patients::whereNotNull('test_report')
                ->where('test_report', '<>', '')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('bills')
                        ->whereColumn('bills.patient_id', 'patients.id')
                        ->where('bills.status', 'paid');
                })
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('age', fn($r) => $r->age)
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="' . route('patients.edit', $row->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("patients.profile", $row->id) . '" class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '"><i class="fas fa-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['message' => 'Invalid request'], 400);
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

        $patient = new Patients;
        // Generate a unique patient_id to avoid duplicate key errors.
        // Use a date prefix plus a random suffix and ensure uniqueness in DB.
        $patient->patient_id = $this->generateUniquePatientId();
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

        // 🔹 Generate patient receipt/token
        $receipt = PatientReceipt::createFromPatient(
            $patient,
            $request->test_category,
            Auth::user() ? Auth::user()->name : null
        );
        $receipt->save();

        // Return with patient ID for CBC machine and receipt info
        return redirect()->route('patients.list')
            ->with('success', 'Patient registered successfully')
            ->with('patient_id', $patient->id)
            ->with('patient_name', $patient->name)
            ->with('receipt_number', $receipt->receipt_number)
            ->with('show_patient_id_modal', true);
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
    public function getPatientTests($id)
    {
        $patient = \App\Models\Patients::with('tests')->find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Assuming you have a relationship: Patient -> hasMany Tests through a pivot
        $tests = $patient->tests->map(function ($test) {
            return [
                'id' => $test->id,
                'name' => $test->name,
                'price' => $test->price,
            ];
        });

        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'age' => $patient->age,
                'gender' => $patient->gender,
                'phone' => $patient->mobile_phone,
            ],
            'tests' => $tests,
        ]);
    }

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


    public function list(Request $request)
    {
        $query = Patients::query();

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->referral) {
            $query->where('referred_by', $request->referral);
        }

        if ($request->min && $request->max) {
            $query->whereBetween('created_at', [$request->min, $request->max]);
        }

        $data = $query->orderBy('id', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
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

        // Get test data with templates (same as edit view)
        $testData = $this->getEditTestData($id);

        return view('Patient.patient_details', array_merge(
            compact('patient'),
            $testData
        ));
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

        // Get test data with templates
        $testData = $this->getEditTestData($id);

        // Get referrals for dropdown
        $referrals = \App\Models\Referrals::orderBy('name')->get();

        return view('Patient.patient_edit', array_merge(
            compact('patient', 'referrals'),
            $testData
        ));
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

    public function fetchCBCResults($patientId)
    {
        $patient = Patients::findOrFail($patientId);

        // Get CBC results from test_report column
        $testReports = json_decode($patient->test_report ?? '[]', true) ?? [];
        $cbcReports = array_filter($testReports, function ($report) {
            return isset($report['test']) && $report['test'] === 'CBC';
        });

        if (!empty($cbcReports)) {
            return response()->json([
                'success' => true,
                'message' => 'CBC results fetched successfully!',
                'count' => count($cbcReports)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No CBC results available. Results are automatically synced from the analyzer.'
        ]);
    }

    /**
     * Render a print-friendly view of a single test report for a patient
     */
    public function printTestReport($patientId, $testName)
    {
        $patient = Patients::findOrFail($patientId);

        // Decode the test name in case it was URL-encoded
        $testName = rawurldecode($testName);

        // Load raw saved test reports for this patient (may be associative or indexed)
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];

        // Try to find the test data by key (case-insensitive), or by entry containing ['test'] field
        $foundKey = null;
        foreach ($existingTestReports as $k => $v) {
            if (is_string($k) && strtolower($k) === strtolower($testName)) {
                $foundKey = $k;
                break;
            }
        }

        if ($foundKey === null) {
            foreach ($existingTestReports as $k => $v) {
                if (is_array($v) && isset($v['test']) && strtolower($v['test']) === strtolower($testName)) {
                    $foundKey = $k;
                    break;
                }
            }
        }

        $testData = $foundKey !== null ? $existingTestReports[$foundKey] : null;

        // Also fetch DB-driven template/parameters for this test (authoritative metadata)
        $templateFields = [];
        try {
            $cat = DB::table('labtest_cat')
                ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                ->first();

            if ($cat) {
                $params = DB::table('lab_test_parameters')
                    ->where('lab_test_cat_id', $cat->id)
                    ->orderBy('id')
                    ->get();

                foreach ($params as $p) {
                    // create a stable field name key that matches view expectations
                    $fieldName = preg_replace('/[^a-z0-9]+/i', '_', strtolower($p->parameter_name));
                    $templateFields[] = [
                        'name' => $fieldName,
                        'label' => $p->parameter_name,
                        'unit' => $p->unit ?? '',
                        'ref' => $p->reference_range ?? '',
                        'required' => false,
                    ];
                }
            }
        } catch (\Exception $e) {
            // ignore DB errors here; we'll still try to render available data
            $templateFields = [];
        }

        // Build the testEntry object the view expects
        $testEntry = [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => is_array($testData) ? $testData : [],
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];


        return view('Patient.patient_test_print', compact('patient', 'testEntry'));
    }

    /**
     * Generate a unique patient_id.
     * Uses a date prefix and a random numeric suffix, checks DB for uniqueness.
     * Falls back to a looped attempt and throws exception after many tries.
     *
     * @return string
     */
    private function generateUniquePatientId()
    {
        $attempt = 0;
        do {
            // e.g. 20251025-8421 (date + 4 random digits)
            $candidate = date('Ymd') . mt_rand(1000, 9999);
            $exists = Patients::where('patient_id', $candidate)->exists();
            $attempt++;
        } while ($exists && $attempt < 10);

        if ($exists) {
            // As a last resort, use a more-unique value with time and random
            $candidate = date('YmdHis') . mt_rand(100, 999);
            // If still exists (extremely unlikely), let DB throw — or you may loop longer
        }

        return (string) $candidate;
    }

    /**
     * View patient receipt/token
     */
    public function viewReceipt($patientId)
    {
        $patient = Patients::findOrFail($patientId);
        $receipt = PatientReceipt::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$receipt) {
            return redirect()->route('patients.list')->with('error', 'Receipt not found');
        }

        return view('Patient.receipt', compact('patient', 'receipt'));
    }

    /**
     * Print patient receipt/token
     */
    public function printReceipt($receiptId)
    {
        $receipt = PatientReceipt::findOrFail($receiptId);
        $patient = $receipt->patient;

        // Mark as printed
        $receipt->status = 'printed';
        $receipt->save();

        return view('Patient.partials.receipt_print', compact('patient', 'receipt'));
    }

    /**
     * Get latest receipt for patient (for modal display)
     */
    public function getLatestReceipt($patientId)
    {
        $receipt = PatientReceipt::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$receipt) {
            return response()->json(['error' => 'Receipt not found'], 404);
        }

        return response()->json([
            'success' => true,
            'receipt_number' => $receipt->receipt_number,
            'formatted_receipt_number' => $receipt->getFormattedReceiptNumber(),
            'total_amount' => $receipt->total_amount,
            'tests' => $receipt->tests,
            'status' => $receipt->status,
            'created_at' => $receipt->created_at->format('d-M-Y H:i A'),
        ]);
    }
}

