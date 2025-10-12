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
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'bill_no' => 'required|string',
                'gtotal' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'total_' => 'required|numeric',
                'pay' => 'nullable|numeric',
                'paidby' => 'required|string',
                'id' => 'required|array',
                'cat_name' => 'required|array',
                'price' => 'required|array',
            ]);

            // Prepare test data
            $tests = [];
            foreach ($validated['id'] as $index => $testId) {
                $tests[] = [
                    'test_id' => $testId,
                    'test_name' => $validated['cat_name'][$index],
                    'price' => $validated['price'][$index]
                ];
            }

            // Create the bill
            $bill = Bills::create([
                'patient_id' => $validated['patient_id'],
                'bill_no' => $validated['bill_no'],
                'all_test' => json_encode($tests),
                'subtotal' => $validated['gtotal'],
                'discount' => $validated['discount'] ?? 0,
                'total_amount' => $validated['total_'],
                'paid_amount' => $validated['pay'] ?? 0,
                'payment_type' => $validated['paidby'],
                'approval_code' => $request->input('abbroval_code'),
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bill created successfully',
                'bill_id' => $bill->id
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error in BillsController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bill: ' . $e->getMessage()
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
}
