<?php

namespace App\Http\Controllers;

use App\Models\Bills;
use App\Models\LabTestCat;
use App\Models\Patients;
use App\Models\MainCompanys;
use App\Models\Payments;
use App\Models\TestReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class BillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tests = LabTestCat::all();
        return view('Bill.bills', compact('tests'));
    }

    public function allbills(Request $request)
    {
        if ($request->ajax()) {
            $data = Bills::with('patient')->orderBy('id', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('patient_id', function ($item) {
                    return optional($item->patient)->patient_id ?? 'N/A';
                })
                ->addColumn('patient_name', function ($item) {
                    return optional($item->patient)->name ?? 'N/A';
                })
                ->addColumn('billing_date', function ($item) {
                    return $item->created_at ? $item->created_at->format('d-m-Y') : 'N/A';
                })
                ->addColumn('all_test', function ($item) {
                    $all_test = json_decode($item->all_test);
                    $all_test_name = [];
                    if ($all_test) {
                        foreach ($all_test as $test) {
                            $all_test_name[] = $test->test_name;
                        }
                    }
                    return implode(', ', $all_test_name);
                })
                ->addColumn('action', function ($row) {
                    $btn = '&nbsp;&nbsp;<a href="' . route("billing.details", $row->id) . '" class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '"><i class="fas fa-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Bill.allbills');
    }


    public function allbills1(Request $request)
    {
        if ($request->ajax()) {
            $data = Bills::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('patient_id', function ($item) {
                    return $item->patients->patient_id;
                })
                ->addColumn('patient_name', function ($item) {
                    return $item->patients->name;
                })
                ->addColumn('billing_date', function ($item) {
                    return $item->created_at->format('d-m-Y');
                })
                ->addColumn('all_test', function ($item) {
                    $all_test = json_decode($item->all_test);
                    $all_test_name = [];
                    if ($all_test) {
                        foreach ($all_test as $test) {
                            $all_test_name[] = $test->test_name;
                        }
                    }
                    return $all_test_name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '&nbsp&nbsp<a href=' . (route("billing.details", $row->id)) . ' class="btn btn-info btn-sm detailsview" data-id="' . $row->id . '"><i class="fas fa-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['patient_id', 'patient_name', 'all_test', 'action', 'billing_date',])
                ->make(true);
        }

        return view('Bill.allbills');
    }

    /**
     * Show the form for creating a new resource.
     * REQUIRED: Patient ID must be provided via URL
     */
    public function create($id)
    {
        try {
            if (empty($id)) {
                return redirect()->route('patients.list')->with('error', 'Patient ID is required.');
            }

            $patient = Patients::findOrFail($id);
            $tests = LabTestCat::all();
            $registeredTests = $this->resolvePatientTests($patient);

            return view('Bill.bills', compact('patient', 'tests', 'registeredTests'));
        } catch (\Exception $e) {
            Log::error('BillsController@create failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('patients.list')->with('error', 'Unable to load billing page.');
        }
    }

    /**
     * Get registered tests for a patient (AJAX endpoint)
     */
    public function getRegisteredTests($patientId)
    {
        try {
            $patient = Patients::findOrFail($patientId);
            $tests = $this->resolvePatientTests($patient);

            return response()->json([
                'tests' => $tests->map(fn($test) => [
                    'id' => $test->id,
                    'name' => $test->cat_name,
                    'department' => $test->department ?? 'N/A',
                    'price' => (float) $test->price,
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error('getRegisteredTests failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Unable to refresh tests'], 500);
        }
    }

    private function resolvePatientTests(Patients $patient)
    {
        $raw = $patient->test_category ?? [];
        if (!is_array($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        $ids = [];
        $names = [];

        foreach ($raw as $entry) {
            if (is_array($entry)) {
                if (!empty($entry['id']) && is_numeric($entry['id'])) {
                    $ids[] = (int) $entry['id'];
                }
                if (!empty($entry['cat_name'])) {
                    $names[] = $entry['cat_name'];
                }
            } elseif (is_numeric($entry)) {
                $ids[] = (int) $entry;
            } elseif (is_string($entry) && $entry !== '') {
                $names[] = $entry;
            }
        }

        $ids = array_unique($ids);
        $names = array_unique($names);

        return LabTestCat::query()
            ->when($ids, fn($query) => $query->whereIn('id', $ids))
            ->when($names, fn($query) => $ids
                ? $query->orWhereIn('cat_name', $names)
                : $query->whereIn('cat_name', $names))
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $patientId = $request->input('patient_id');
            $selectedNames = array_filter((array) $request->input('cat_name', []));
            // use total_ sent by your form
            $total = (float) ($request->input('total_') ?? $request->input('amount') ?? 0);
            $paid  = (float) ($request->input('pay', 0));
            $status = ($paid >= $total && $total > 0) ? 'paid' : 'unpaid';

            if (!$patientId || empty($selectedNames) || $total <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields (patient, tests, or amount).'
                ], 400);
            }

            $patient = \App\Models\Patients::find($patientId);
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found.'
                ], 404);
            }

            // Update patient's test_category (your existing logic)
            $rawCategories = $patient->test_category ?? [];
            if (!is_array($rawCategories)) {
                $decoded = json_decode($rawCategories, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $rawCategories = $decoded;
                } else {
                    $rawCategories = array_map('trim', explode(',', (string)$rawCategories));
                }
            }
            $merged = array_values(array_unique(array_merge($rawCategories, $selectedNames)));
            $patient->test_category = json_encode($merged);
            $patient->save();

            // Update or create the single bill row for the patient
            \App\Models\Bills::updateOrCreate(
                ['patient_id' => $patient->id],
                [
                    'amount' => $total,
                    'status' => $status,
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Bill saved successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('BillsController@store failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save bill'
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $bill = Bills::with('patient')->findOrFail($id);
            return view('Bill.show_bill', compact('bill'));
        } catch (\Exception $e) {
            Log::error('Error in BillsController@show: ' . $e->getMessage());
            return redirect()->route('billing.index')
                ->with('error', 'Bill not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bills $bills)
    {
        try {
            $patient = $bills->patient;
            $tests = LabTestCat::all();
            $companies = MainCompanys::all();

            return view('Bill.edit_bill', compact('bills', 'patient', 'tests', 'companies'));
        } catch (\Exception $e) {
            Log::error('Error in BillsController@edit: ' . $e->getMessage());
            return redirect()->route('billing.index')
                ->with('error', 'Failed to load bill for editing');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bills $bills)
    {
        try {
            $validated = $request->validate([
                'total_amount' => 'required|numeric',
            ]);

            $bills->update($validated);

            return redirect()->route('billing.show', $bills->id)
                ->with('success', 'Bill updated successfully');
        } catch (\Exception $e) {
            Log::error('Error in BillsController@update: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update bill: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bills $bills)
    {
        try {
            $bills->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bill deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in BillsController@destroy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bill: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createTestRequest(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_id' => 'required|exists:labtest,id',
        ]);

        // Create pending test with accession
        $accession = 'ACC-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $testReport = TestReport::create([
            'patient_id' => $validated['patient_id'],
            'test_id' => $validated['test_id'],
            'invoice_id' => null, // Link to bill if needed
            'result' => json_encode([
                'status' => 'pending',
                'accession_no' => $accession,
                'created_at' => now()->toISOString(),
            ]),
        ]);

        return response()->json([
            'accession' => $accession,
            'test_report_id' => $testReport->id,
        ]);
    }
}
