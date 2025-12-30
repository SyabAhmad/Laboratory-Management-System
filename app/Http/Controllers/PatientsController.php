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
use Illuminate\Support\Facades\Storage;
use App\Models\Bills;

class PatientsController extends Controller
{
    // 🔥 OPTIMIZATION: Cache for PDF generation to avoid repeated DB queries
    private static $pdfTestCache = [];

    private function getTestDataForPDF($testName)
    {
        if (isset(self::$pdfTestCache[$testName])) {
            return self::$pdfTestCache[$testName];
        }

        $cat = DB::table('labtest_cat')
            ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
            ->first();

        if (!$cat) {
            $cat = DB::table('labtest_cat')
                ->whereRaw('LOWER(cat_name) LIKE ?', ['%' . strtolower($testName) . '%'])
                ->first();
        }

        if (!$cat) {
            return self::$pdfTestCache[$testName] = null;
        }

        $departmentName = $cat->department ?? (isset($cat->department_id) ?
            DB::table('departments')->where('id', $cat->department_id)->value('name') : null);

        $params = DB::table('lab_test_parameters')
            ->where('lab_test_cat_id', $cat->id)
            ->orderBy('id')
            ->get();

        $templateFields = [];
        foreach ($params as $p) {
            $fieldName = \Str::slug($p->parameter_name, '_');
            $field = [
                'name' => $fieldName,
                'label' => $p->parameter_name,
                'unit' => $p->unit ?? '',
                'ref' => $p->reference_range ?? '',
                'type' => $p->field_type ?? 'text',
                'required' => false,
            ];
            if ($p->field_type === 'dual_option' && $p->dual_options) {
                $dualOptions = is_array($p->dual_options) ? $p->dual_options : json_decode($p->dual_options, true);
                if (is_array($dualOptions) && count($dualOptions) >= 2) {
                    $field['dual_options'] = $dualOptions;
                }
            }
            $templateFields[] = $field;
        }

        return self::$pdfTestCache[$testName] = [
            'templateFields' => $templateFields,
            'departmentName' => $departmentName
        ];
    }

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

        // 🔥 OPTIMIZATION: Cache all test categories and parameters to reduce DB queries
        $cacheKey = 'lab_test_categories_with_params_' . md5(implode(',', array_map('strtolower', $selectedTests)));
        $labTestCategories = \Cache::remember($cacheKey, 600, function () use ($selectedTests) { // Cache for 10 minutes
            $testNamesLower = array_map('strtolower', $selectedTests);
            return \App\Models\LabTestCat::where(function($query) use ($testNamesLower) {
                foreach ($testNamesLower as $name) {
                    $query->orWhereRaw('LOWER(cat_name) = ?', [$name])
                          ->orWhereRaw('LOWER(cat_name) LIKE ?', ['%' . $name . '%']);
                }
            })->with(['parameters', 'departmentRelation'])->get()->keyBy(function($item) {
                return strtolower($item->cat_name);
            });
        });

        $testsWithData = [];
        $processedTests = [];

        // 🔹 Handle each registered test
        foreach ($selectedTests as $testName) {
            $testName = trim($testName);
            $processedTests[$testName] = true;

            $testData = $existingTestReports[$testName] ?? [];

            // 🔥 OPTIMIZATION: Use pre-loaded categories instead of individual queries
            $labTestCategory = $labTestCategories[strtolower($testName)] ?? null;

            if ($labTestCategory) {
                $parameters = $labTestCategory->parameters;
                $fields = [];

                foreach ($parameters as $param) {
                    $unitStr = ($param->field_type !== 'dual_option' && $param->unit) ? ' (' . $param->unit . ')' : '';
                    $refRangeStr = ($param->field_type !== 'dual_option' && $param->reference_range) ? ' - Ref: ' . $param->reference_range : '';

                    // Normalize parameter name to a safe key for form input / JSON storage
                    $key = Str::slug($param->parameter_name, '_');

                    $field = [
                        'name' => $key, // use slug key in the template
                        'label' => $param->parameter_name . $unitStr . $refRangeStr,
                        'orig_name' => $param->parameter_name, // keep original for mapping
                        'type' => $param->field_type ?? 'text',
                        'required' => false,
                    ];

                    // Add dual options if field type is dual_option
                    if ($param->field_type === 'dual_option' && $param->dual_options) {
                        $dualOptions = is_array($param->dual_options) ? $param->dual_options : json_decode($param->dual_options, true);
                        if (is_array($dualOptions) && count($dualOptions) >= 2) {
                            $field['dual_options'] = $dualOptions;
                        }
                    }

                    $fields[] = $field;
                }

                // Only use DB-driven fields; do not add any predefined/report metadata fields
                $template = !empty($fields) ? ['fields' => $fields] : null;

                $savedData = $this->flattenTestData($testData);
                // Make sure slug keys from saved data exist so template fields (that use slug keys) can find values
                $savedData = $this->mapSavedDataToSlugKeys($savedData);

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
            $departmentName = $labTestCategory ? ($labTestCategory->department ?? optional($labTestCategory->departmentRelation)->name) : null;

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template, // ✅ ensure included
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => $labTestCategory ? true : false,
                'is_mllp_data' => $isMllpData,
                'department' => $departmentName,
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
                        ]
                    ], $fields)
                ]
                : null;

            $savedData = $this->flattenTestData($testData);

            $testsWithData[] = [
                'name' => $testName,
                'template' => $template,
                'saved_data' => $savedData,
                'has_data' => !empty($testData),
                'has_template' => false,
                'is_mllp_data' => $isMllpData,
                'department' => null,
            ];
        }

        return [
            'testsWithData' => $testsWithData,
            'selectedTests' => $selectedTests
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
     * Normalize saved data keys to slug format, while preserving original keys.
     * This makes mapping to template field keys more robust across variations.
     */
    private function mapSavedDataToSlugKeys(array $savedData)
    {
        $mapped = $savedData;
        foreach ($savedData as $k => $v) {
            $slug = \Str::slug($k, '_');
            if (!array_key_exists($slug, $mapped)) {
                $mapped[$slug] = $v;
            }
        }
        return $mapped;
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

            $data = Patients::with(['bills' => function($query) {
                $query->select('id', 'patient_id', 'status', 'paid_amount', 'total_price', 'amount');
            }, 'receipts' => function($query) {
                $query->select('id', 'patient_id')->latest();
            }])
                ->whereNotIn('id', $completedIds)
                ->orderBy('id', 'DESC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('age', function ($row) {
                    // Use individual age parts if available, otherwise use the combined string
                    if (!empty($row->age_years) || !empty($row->age_months) || !empty($row->age_days)) {
                        $parts = [];
                        if (!empty($row->age_years)) $parts[] = $row->age_years . 'Y';
                        if (!empty($row->age_months)) $parts[] = $row->age_months . 'M';
                        if (!empty($row->age_days)) $parts[] = $row->age_days . 'D';
                        return !empty($parts) ? implode(' ', $parts) : '0Y';
                    }
                    return $row->age ?: 'N/A';
                })
                ->addColumn('data_status', function ($row) {
                    $hasData = !empty($row->test_report) && $row->test_report !== '{}' && $row->test_report !== 'null';
                    return $hasData ? 'Complete' : 'Pending';
                })
                ->addColumn('bill_status', function ($row) {
                    $bill = $row->bills->first();
                    if (!$bill) return 'No Bill';
                    $isPaid = strtolower($bill->status ?? '') === 'paid' || ((float)($bill->paid_amount ?? 0) >= (float)($bill->total_price ?? $bill->amount ?? 0) && (float)($bill->total_price ?? $bill->amount ?? 0) > 0);
                    return $isPaid ? 'Paid' : 'Unpaid';
                })
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="' . route('patients.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("patients.profile", $row->id) . '" class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '" title="View Details"><i class="fas fa-eye"></i></a>';

                    // Add Download Slip button if receipt exists
                    $receipt = $row->receipts->first();
                    if ($receipt) {
                        $btn .= '&nbsp;&nbsp;<a href="' . route('patients.print-receipt', $receipt->id) . '" class="btn btn-success btn-sm" title="Download Slip" onclick="return openPrintModal(event, this)"><i class="fas fa-download"></i> Slip</a>';
                    }

                    $btn .= '&nbsp;&nbsp;<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-danger btn-sm deletebtn" title="Delete"><i class="fas fa-trash"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("billing.create", ['id' => $row->id]) . '" class="btn btn-success btn-sm" title="Create Bill"><i class="fas fa-file-invoice-dollar"></i> Bill</a>';
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
            $data = Patients::with(['receipts' => function($query) {
                $query->select('id', 'patient_id')->latest();
            }])
                ->whereNotNull('test_report')
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
                ->addColumn('age', function ($r) {
                    // Use individual age parts if available, otherwise use the combined string
                    if (!empty($r->age_years) || !empty($r->age_months) || !empty($r->age_days)) {
                        $parts = [];
                        if (!empty($r->age_years)) $parts[] = $r->age_years . 'Y';
                        if (!empty($r->age_months)) $parts[] = $r->age_months . 'M';
                        if (!empty($r->age_days)) $parts[] = $r->age_days . 'D';
                        return !empty($parts) ? implode(' ', $parts) : '0Y';
                    }
                    return $r->age ?: 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="' . route('patients.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a href="' . route("patients.profile", $row->id) . '" class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '" title="View Details"><i class="fas fa-eye"></i></a>';

                    // Add Download Slip button if receipt exists
                    $receipt = $row->receipts->first();
                    if ($receipt) {
                        $btn .= '&nbsp;&nbsp;<a href="' . route('patients.print-receipt', $receipt->id) . '" class="btn btn-success btn-sm" title="Download Slip" onclick="return openPrintModal(event, this)"><i class="fas fa-download"></i> Slip</a>';
                    }

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
            // allow combined age strings up to 20 characters OR accept parts and combine server-side
            'age' => 'required_without:age_years|nullable|string|max:20',
            'age_years' => 'nullable|integer|min:0|max:150',
            'age_months' => 'nullable|integer|min:0|max:11',
            'age_days' => 'nullable|integer|min:0|max:30',
            'receiving_date' => 'required|date:Y-m-d H:i',
            'reporting_date' => 'required|date:Y-m-d H:i',
            'test_category'   => 'required|array|min:1',
            'test_category.*' => 'string|max:255',
            'test_prices'     => 'array',
            'test_prices.*'   => 'numeric',
        ], [
            'test_category.required' => 'Please select at least one test category',
            'test_category.min' => 'Please select at least one test category',
        ]);

        // Combine age parts into the `age` string if needed
        if (empty($request->age) && ($request->filled('age_years') || $request->filled('age_months') || $request->filled('age_days'))) {
            $parts = [];
            if ($request->filled('age_years') && $request->age_years != '0') $parts[] = $request->age_years . 'Y';
            if ($request->filled('age_months') && $request->age_months != '0') $parts[] = $request->age_months . 'M';
            if ($request->filled('age_days') && $request->age_days != '0') $parts[] = $request->age_days . 'D';
            $ageString = !empty($parts) ? implode(' ', $parts) : '0Y';
            $request->merge(['age' => $ageString]);
        }

        $patient = new Patients;
        // patient_id will be generated automatically based on the database id after insert
        $patient->user_id = Auth::id();

        $patient->name = $request->name;
        $patient->mobile_phone = $request->mobile_phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        // Store split age parts for future migrations/DB columns
        if ($request->filled('age_years')) $patient->age_years = (int)$request->age_years;
        if ($request->filled('age_months')) $patient->age_months = (int)$request->age_months;
        if ($request->filled('age_days')) $patient->age_days = (int)$request->age_days;
        // Also store component parts if provided (requires migrations to persist)
        if ($request->filled('age_years')) $patient->age_years = (int)$request->age_years;
        if ($request->filled('age_months')) $patient->age_months = (int)$request->age_months;
        if ($request->filled('age_days')) $patient->age_days = (int)$request->age_days;
        $patient->blood_group = $request->blood_group ?? null;
        $patient->receiving_date = $request->receiving_date;
        $patient->reporting_date = $request->reporting_date;
        $patient->note = $request->note;
        $patient->referred_by = $request->referred_by;

        // Convert array to JSON for storage
        $patient->test_category = json_encode($request->test_category);

        $patient->registerd_by = Auth::user() ? Auth::user()->name : null;
        $saved = $patient->save();
        $patient->refresh();
        Log::info('PatientsController@store - saved patient', ['id' => $patient->id, 'saved' => $saved, 'patient' => $patient->toArray()]);

        // 🔹 Generate patient receipt/token with prices
        $testPrices = $request->test_prices ?? [];

        // Debug: Log what we're receiving
        Log::info('Receipt Creation Debug', [
            'test_category' => $request->test_category,
            'test_prices' => $testPrices,
            'test_category_count' => count($request->test_category ?? []),
            'test_prices_count' => count($testPrices),
        ]);

        $receipt = PatientReceipt::createFromPatientWithPrices(
            $patient,
            $request->test_category,
            $testPrices,
            Auth::user() ? Auth::user()->name : null
        );
        $receipt->save();

        // 🔹 Redirect back to patient list with success message
        return redirect()->route('patients.list')
            ->with('success', 'Patient registered successfully!');
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
        $patient = Patients::find($id);
        $patient->status = $request->status;
        $patient->save();
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
            // allow combined age strings up to 20 chars (e.g. '22Y 3M 5D')
            'age' => 'nullable|string|max:20',
            'age_years' => 'nullable|integer|min:0|max:150',
            'age_months' => 'nullable|integer|min:0|max:11',
            'age_days' => 'nullable|integer|min:0|max:30',
            'receiving_date' => 'required|date:Y-m-d H:i',
            'reporting_date' => 'required|date:Y-m-d H:i',
        ]);

        // If age wasn't provided as a combined string, try to assemble it from the split parts
        if (empty($request->age) && ($request->filled('age_years') || $request->filled('age_months') || $request->filled('age_days'))) {
            $parts = [];
            if ($request->filled('age_years') && $request->age_years != '0') $parts[] = $request->age_years . 'Y';
            if ($request->filled('age_months') && $request->age_months != '0') $parts[] = $request->age_months . 'M';
            if ($request->filled('age_days') && $request->age_days != '0') $parts[] = $request->age_days . 'D';
            $ageString = !empty($parts) ? implode(' ', $parts) : '0Y';
            $request->merge(['age' => $ageString]);
        }

        $patient = Patients::findOrFail($id);
        // Debug: Log incoming request data for troubleshooting updates
        Log::info('PatientsController@update - incoming request', ['id' => $id, 'data' => $request->only(['name', 'mobile_phone', 'address', 'gender', 'age', 'blood_group', 'receiving_date', 'reporting_date', 'note', 'referred_by'])]);

        $patient->name = $request->name;
        $patient->mobile_phone = $request->mobile_phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        // Update individual age parts
        // Clear values if they are not provided in the request
        $patient->age_years = $request->filled('age_years') ? (int)$request->age_years : null;
        $patient->age_months = $request->filled('age_months') ? (int)$request->age_months : null;
        $patient->age_days = $request->filled('age_days') ? (int)$request->age_days : null;
        $patient->blood_group = $request->blood_group ?? null;
        $patient->receiving_date = $request->receiving_date;
        $patient->reporting_date = $request->reporting_date;
        // 同时保存日期和时间字段
        $patient->receiving_datetime = $request->receiving_date;
        $patient->reporting_datetime = $request->reporting_date;
        $patient->note = $request->note;
        $patient->referred_by = $request->referred_by;
        $saved = $patient->save();
        // Refresh to ensure latest values are loaded and log the attributes to confirm persistence
        $patient->refresh();
        Log::info('PatientsController@update - save result', ['id' => $id, 'saved' => $saved, 'patient' => $patient->toArray()]);

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
            if (!$cat) {
                $cat = DB::table('labtest_cat')
                    ->whereRaw('LOWER(cat_name) LIKE ?', ['%' . strtolower($testName) . '%'])
                    ->first();
            }

            $departmentName = null;
            if ($cat) {
                // department may be stored on the category or as department_id
                $departmentName = $cat->department ?? (isset($cat->department_id) ? DB::table('departments')->where('id', $cat->department_id)->value('name') : null);
                $params = DB::table('lab_test_parameters')
                    ->where('lab_test_cat_id', $cat->id)
                    ->orderBy('id')
                    ->get();

                foreach ($params as $p) {
                    // create a stable field name key that matches view expectations
                    $fieldName = \Str::slug($p->parameter_name, '_');
                    $field = [
                        'name' => $fieldName,
                        'label' => $p->parameter_name,
                        'unit' => $p->unit ?? '',
                        'ref' => $p->reference_range ?? '',
                        'type' => $p->field_type ?? 'text',
                        'required' => false,
                    ];

                    // Add dual options if field type is dual_option
                    if ($p->field_type === 'dual_option' && $p->dual_options) {
                        $dualOptions = is_array($p->dual_options) ? $p->dual_options : json_decode($p->dual_options, true);
                        if (is_array($dualOptions) && count($dualOptions) >= 2) {
                            $field['dual_options'] = $dualOptions;
                        }
                    }

                    $templateFields[] = $field;
                }
            }
        } catch (\Exception $e) {
            // ignore DB errors here; we'll still try to render available data
            $templateFields = [];
        }

        // Debug log helpful to trace why there may be missing mapping for some tests
        try {
            \Log::info('printTestReport: computed', [
                'patient_id' => $patientId,
                'testName' => $testName,
                'foundKey' => $foundKey,
                'catId' => $cat->id ?? null,
                'templateFieldCount' => count($templateFields),
                'savedFlattenedKeys' => array_keys($savedDataFlattened ?? []),
            ]);
        } catch (\Exception $ex) {
            // ignore logging errors
        }

        // Build the testEntry object the view expects
        $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
        $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

        $testEntry = [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => $savedDataFlattened,
            'department' => $departmentName,
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];


        return view('Patient.patient_test_print', compact('patient', 'testEntry'));
    }

    /**
     * Print multiple selected test reports for a patient, combined in a single printable page
     * Accepts a comma-separated list of test names (URL encoded) using {testNames}
     */
    public function printMultipleTestReports($patientId, $testNames)
    {
        $patient = Patients::findOrFail($patientId);

        $decoded = rawurldecode($testNames);
        $names = array_filter(array_map('trim', explode('_', $decoded)));

        $testEntries = [];
        foreach ($names as $name) {
            // Reuse the same logic as printTestReport to build each entry
            $testName = $name;
            $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

            // fetch template fields
            $templateFields = [];
            try {
                $cat = DB::table('labtest_cat')
                    ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                    ->first();
                if ($cat) {
                    $departmentName = $cat->department ?? (isset($cat->department_id) ? DB::table('departments')->where('id', $cat->department_id)->value('name') : null);
                    $params = DB::table('lab_test_parameters')
                        ->where('lab_test_cat_id', $cat->id)
                        ->orderBy('id')
                        ->get();
                    foreach ($params as $p) {
                        $fieldName = \Str::slug($p->parameter_name, '_');
                        $field = [
                            'name' => $fieldName,
                            'label' => $p->parameter_name,
                            'unit' => $p->unit ?? '',
                            'ref' => $p->reference_range ?? '',
                            'type' => $p->field_type ?? 'text',
                            'required' => false,
                        ];

                        // Add dual options if field type is dual_option
                        if ($p->field_type === 'dual_option' && $p->dual_options) {
                            $dualOptions = is_array($p->dual_options) ? $p->dual_options : json_decode($p->dual_options, true);
                            if (is_array($dualOptions) && count($dualOptions) >= 2) {
                                $field['dual_options'] = $dualOptions;
                            }
                        }

                        $templateFields[] = $field;
                    }
                }
            } catch (\Exception $e) {
                $templateFields = [];
            }

            $flattenedSaved = is_array($testData) ? $this->flattenTestData($testData) : [];
            $flattenedSaved = $this->mapSavedDataToSlugKeys($flattenedSaved);

            $testEntries[] = [
                'name' => $testName,
                'template' => ['fields' => $templateFields],
                'saved_data' => $flattenedSaved,
                'department' => $departmentName ?? null,
                'has_template' => !empty($templateFields),
                'has_data' => !empty($testData),
            ];
        }

        return view('Patient.patient_tests_print', compact('patient', 'testEntries'));
    }

    /**
     * Print friendly patient test report with header/footer (per-test)
     */
    public function printTestReportWithHeader($patientId, $testName)
    {
        $patient = Patients::findOrFail($patientId);
        $testName = rawurldecode($testName);

        // Reuse the logic from printTestReport
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

        // 🔥 OPTIMIZATION: Use cached test data instead of repeated DB queries
        $cachedData = $this->getTestDataForPDF($testName);
        $templateFields = $cachedData ? $cachedData['templateFields'] : [];
        $departmentName = $cachedData ? $cachedData['departmentName'] : null;

        $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
        $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

        $testEntry = [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => $savedDataFlattened,
            'department' => $departmentName,
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];

        // Use the view with actual headers and footers
        return view('Patient.patient_test_download', compact('patient', 'testEntry'));
    }

    /**
     * Print multiple selected test reports with header/footer for a patient, combined in a single printable page
     * Accepts a comma-separated list of test names (URL encoded) using {testNames}
     */
    public function printMultipleTestReportsWithHeader($patientId, $testNames)
    {
        $patient = Patients::findOrFail($patientId);

        $decoded = rawurldecode($testNames);
        $names = array_filter(array_map('trim', explode('_', $decoded)));

        $testEntries = [];
        foreach ($names as $name) {
            // Reuse the same logic as printTestReport to build each entry
            $testName = $name;
            $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

            // 🔥 OPTIMIZATION: Use cached test data instead of repeated DB queries
            $cachedData = $this->getTestDataForPDF($testName);
            $templateFields = $cachedData ? $cachedData['templateFields'] : [];
            $departmentName = $cachedData ? $cachedData['departmentName'] : null;

            $flattenedSaved = is_array($testData) ? $this->flattenTestData($testData) : [];
            $flattenedSaved = $this->mapSavedDataToSlugKeys($flattenedSaved);

            $testEntries[] = [
                'name' => $testName,
                'template' => ['fields' => $templateFields],
                'saved_data' => $flattenedSaved,
                'department' => $departmentName,
                'has_template' => !empty($templateFields),
                'has_data' => !empty($testData),
            ];
        }

        // Use the view for printing
        return view('Patient.patient_tests_download', compact('patient', 'testEntries'));
    }

    // Save single test report functionality removed

    // Report download removed

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
            // e.g. 202510251422-8421 (date and time + 4 random digits)
            $candidate = date('YmdHis') . mt_rand(1000, 9999);
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

    // NOTE: generateUniquePatientId is left for legacy code but the new approach uses the model-created hook to set patient_id.

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

    public function downloadTestReportPDF($patientId, $testName)
    {
        $patient = Patients::findOrFail($patientId);
        $testName = rawurldecode($testName);

        // Reuse the logic from printTestReport to get testEntry
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

        // 🔥 OPTIMIZATION: Use cached test data instead of repeated DB queries
        $cachedData = $this->getTestDataForPDF($testName);
        $templateFields = $cachedData ? $cachedData['templateFields'] : [];
        $departmentName = $cachedData ? $cachedData['departmentName'] : null;

        $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
        $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

        $testEntry = [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => $savedDataFlattened,
            'department' => $departmentName ?? null,
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];

        $filename = 'test_report_' . $patient->id . '_' . str_replace(' ', '_', $testName) . '.pdf';
        try {
            \Log::info('Generating PDF for test', ['testName' => $testName, 'patientId' => $patientId, 'testEntry' => $testEntry]);
            $pdf = new \Mpdf\Mpdf();
            $html = view('Patient.patient_test_download', compact('patient', 'testEntry'))->render();
            $pdf->WriteHTML($html);
            return $pdf->Output($filename, 'D');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTestReportPDFNoHeader($patientId, $testName)
    {
        // Same as above but pass includeHeader = false
        $patient = Patients::findOrFail($patientId);
        $testName = rawurldecode($testName);

        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

        // 🔥 OPTIMIZATION: Use cached test data instead of repeated DB queries
        $cachedData = $this->getTestDataForPDF($testName);
        $templateFields = $cachedData ? $cachedData['templateFields'] : [];
        $departmentName = $cachedData ? $cachedData['departmentName'] : null;

        $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
        $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

        $testEntry = [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => $savedDataFlattened,
            'department' => $departmentName ?? null,
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];

        $filename = 'test_report_' . $patient->id . '_' . str_replace(' ', '_', $testName) . '.pdf';
        $includeHeader = false;
        try {
            $pdf = new \Mpdf\Mpdf();
            $html = view('Patient.patient_test_download', compact('patient', 'testEntry', 'includeHeader'))->render();
            $pdf->WriteHTML($html);
            return $pdf->Output($filename, 'D');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error (No Header): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function downloadMultipleTestReportsPDF($patientId, $testNames)
    {
        $patient = Patients::findOrFail($patientId);
        $testNames = explode(',', rawurldecode($testNames));
        $testEntries = [];
        $filename = 'multiple_test_reports_' . $patient->id . '.pdf';

        foreach ($testNames as $testName) {
            // Reuse logic from printMultipleTestReports
            $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

            $templateFields = [];
            try {
                $cat = DB::table('labtest_cat')
                    ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                    ->first();
                if (!$cat) {
                    $cat = DB::table('labtest_cat')
                        ->whereRaw('LOWER(cat_name) LIKE ?', ['%' . strtolower($testName) . '%'])
                        ->first();
                }
                if ($cat) {
                    $departmentName = $cat->department ?? (isset($cat->department_id) ? DB::table('departments')->where('id', $cat->department_id)->value('name') : null);
                    $params = DB::table('lab_test_parameters')
                        ->where('lab_test_cat_id', $cat->id)
                        ->orderBy('id')
                        ->get();
                    foreach ($params as $p) {
                        $fieldName = \Str::slug($p->parameter_name, '_');
                        $field = [
                            'name' => $fieldName,
                            'label' => $p->parameter_name,
                            'unit' => $p->unit ?? '',
                            'ref' => $p->reference_range ?? '',
                            'type' => $p->field_type ?? 'text',
                            'required' => false,
                        ];
                        if ($p->field_type === 'dual_option' && $p->dual_options) {
                            $dualOptions = is_array($p->dual_options) ? $p->dual_options : json_decode($p->dual_options, true);
                            if (is_array($dualOptions) && count($dualOptions) >= 2) {
                                $field['dual_options'] = $dualOptions;
                            }
                        }
                        $templateFields[] = $field;
                    }
                }
            } catch (\Exception $e) {
                $templateFields = [];
            }

            $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
            $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

            $testEntries[] = [
                'name' => $testName,
                'template' => ['fields' => $templateFields],
                'saved_data' => $savedDataFlattened,
                'department' => $departmentName ?? null,
                'has_template' => !empty($templateFields),
                'has_data' => !empty($testData),
            ];
        }

        try {
            $pdf = new \Mpdf\Mpdf();
            $html = view('Patient.patient_tests_download', compact('patient', 'testEntries'))->render();
            $pdf->WriteHTML($html);
            return $pdf->Output($filename, 'D');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error (Multiple): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function downloadMultipleTestReportsPDFNoHeader($patientId, $testNames)
    {
        // Same as above
        $patient = Patients::findOrFail($patientId);
        $testNames = explode(',', rawurldecode($testNames));
        $testEntries = [];
        $filename = 'multiple_test_reports_' . $patient->id . '.pdf';

        foreach ($testNames as $testName) {
            $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
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

            $templateFields = [];
            try {
                $cat = DB::table('labtest_cat')
                    ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                    ->first();
                if (!$cat) {
                    $cat = DB::table('labtest_cat')
                        ->whereRaw('LOWER(cat_name) LIKE ?', ['%' . strtolower($testName) . '%'])
                        ->first();
                }
                if ($cat) {
                    $departmentName = $cat->department ?? (isset($cat->department_id) ? DB::table('departments')->where('id', $cat->department_id)->value('name') : null);
                    $params = DB::table('lab_test_parameters')
                        ->where('lab_test_cat_id', $cat->id)
                        ->orderBy('id')
                        ->get();
                    foreach ($params as $p) {
                        $fieldName = \Str::slug($p->parameter_name, '_');
                        $field = [
                            'name' => $fieldName,
                            'label' => $p->parameter_name,
                            'unit' => $p->unit ?? '',
                            'ref' => $p->reference_range ?? '',
                            'type' => $p->field_type ?? 'text',
                            'required' => false,
                        ];
                        if ($p->field_type === 'dual_option' && $p->dual_options) {
                            $dualOptions = is_array($p->dual_options) ? $p->dual_options : json_decode($p->dual_options, true);
                            if (is_array($dualOptions) && count($dualOptions) >= 2) {
                                $field['dual_options'] = $dualOptions;
                            }
                        }
                        $templateFields[] = $field;
                    }
                }
            } catch (\Exception $e) {
                $templateFields = [];
            }

            $savedDataFlattened = is_array($testData) ? $this->flattenTestData($testData) : [];
            $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);

            $testEntries[] = [
                'name' => $testName,
                'template' => ['fields' => $templateFields],
                'saved_data' => $savedDataFlattened,
                'department' => $departmentName ?? null,
                'has_template' => !empty($templateFields),
                'has_data' => !empty($testData),
            ];
        }

        $includeHeader = false;
        try {
            $pdf = new \Mpdf\Mpdf();
            $html = view('Patient.patient_tests_download', compact('patient', 'testEntries', 'includeHeader'))->render();
            $pdf->WriteHTML($html);
            return $pdf->Output($filename, 'D');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error (Multiple No Header): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
}
