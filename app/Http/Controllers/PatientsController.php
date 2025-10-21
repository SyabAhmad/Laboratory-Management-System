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
use App\Models\Tests\TestModelFactory;
use App\Models\Tests\CBC;
use App\Models\Tests\Urinal;
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
     * Get registered tests for edit view with templates and saved data
     * Now handles the new associative format: test_report = {"CBC": {...}, "Urinal": {...}}
     *
     * @param  int  $id
     * @return array
     */
    public function getEditTestData($id)
    {
        $patient = Patients::findOrFail($id);
        $testTemplates = config('test_templates', []);

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

        // Parse saved test reports - now expected to be associative: {"TestName": {...}, ...}
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];

        // Build test data with templates
        $testsWithData = [];
        $processedTests = []; // Track which tests we've already added

        // First, add all registered tests from test_category
        foreach ($selectedTests as $testName) {
            $testName = trim($testName);
            $processedTests[$testName] = true;

            // Check if this test has saved data in test_report
            $testData = $existingTestReports[$testName] ?? [];

            // Use test-specific model if available
            $modelClass = TestModelFactory::getModelClass($testName);
            $template = null;
            if ($modelClass && class_exists($modelClass)) {
                // Use analytes from model for template
                $defs = $modelClass::analytes();
                $fields = [];
                foreach ($defs as $analyteName => $meta) {
                    $unitStr = $meta['units'] ? ' (' . $meta['units'] . ')' : '';
                    $refRangeStr = $meta['ref_range'] ? ' - Ref: ' . $meta['ref_range'] : '';
                    $fields[] = [
                        'name' => $analyteName,
                        'label' => $analyteName . $unitStr . $refRangeStr,
                        'type' => 'text',
                        'required' => false,
                    ];
                }
                $template = [
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
                ];
            }

            // Use configured template if exists and no test model was found
            if ($template === null && isset($testTemplates[$testName])) {
                $template = $testTemplates[$testName];
            }

            // Fall back to generic template
            if ($template === null) {
                $template = [
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
            }

            $savedData = $this->flattenTestData($testData);

            $isMllpData = isset($testData['instrument']) || isset($testData['analytes']);

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template,
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => isset($testTemplates[$testName]),
                'is_mllp_data' => $isMllpData,
            ];
        }

        // Second, add any tests in test_report that are NOT in test_category
        // This handles MLLP-received data (like CBC from analyzer) that wasn't pre-registered
        foreach ($existingTestReports as $testName => $testData) {
            // Skip if we've already processed this test (it was in test_category)
            if (isset($processedTests[$testName])) {
                continue;
            }

            // Mark as processed
            $processedTests[$testName] = true;

            // Detect if this is MLLP data (has 'instrument' or 'analytes')
            $isMllpData = isset($testData['instrument']) || isset($testData['analytes']);

            // Extract analytes if present
            $analytes = [];
            if (is_array($testData) && isset($testData['analytes'])) {
                $analytes = $testData['analytes'];
            }

            // Create a template from analytes or use default
            $fields = [];
            if (!empty($analytes) && is_array($analytes)) {
                foreach ($analytes as $analyte) {
                    if (is_array($analyte) && isset($analyte['name']) && isset($analyte['value'])) {
                        // Create field name with units for display
                        $unitStr = isset($analyte['units']) ? ' (' . $analyte['units'] . ')' : '';
                        $refRangeStr = isset($analyte['ref_range']) ? ' - Ref: ' . $analyte['ref_range'] : '';

                        $fields[] = [
                            'name' => $analyte['name'],
                            'label' => $analyte['name'] . $unitStr . $refRangeStr,
                            'type' => 'text',
                            'required' => false,
                        ];
                    }
                }
            }

            // Use template if exists, otherwise build from analytes or use generic
            if (isset($testTemplates[$testName])) {
                $template = $testTemplates[$testName];
            } elseif (!empty($fields)) {
                // Template from MLLP analytes with metadata fields
                $template = [
                    'fields' => array_merge(
                        [
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
                        ],
                        $fields
                    )
                ];
            } else {
                $template = [
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
            }

            // Flatten test data for display
            $savedData = $this->flattenTestData($testData);

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template,
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => isset($testTemplates[$testName]),
                'is_mllp_data' => $isMllpData,
            ];
        }


        return [
            'selectedTests' => $selectedTests,
            'testsWithData' => $testsWithData,
            'existingTestReports' => $existingTestReports,
            'testTemplates' => $testTemplates,
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

        // Return with patient ID for CBC machine
        return redirect()->route('patients.list')
            ->with('success', 'Patient registered successfully')
            ->with('patient_id', $patient->id)
            ->with('patient_name', $patient->name)
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

        return view('Patient.patient_edit', array_merge(
            compact('patient'),
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

        // Use the same helper to build templates/data
        $data = $this->getEditTestData($patientId);

    // Decode the test name in case it was URL-encoded
    $testName = rawurldecode($testName);

    // Find the matching test in testsWithData
        $testEntry = null;
        foreach ($data['testsWithData'] as $t) {
            if ($t['name'] === $testName) {
                $testEntry = $t;
                break;
            }
        }

        if (!$testEntry) {
            abort(404, 'Test report not found for this patient');
        }

        return view('Patient.patient_test_print', array_merge(
            compact('patient'),
            $data,
            ['testEntry' => $testEntry]
        ));
    }
}
