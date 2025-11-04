<?php

namespace App\Http\Controllers;

use App\Models\Referrals;
use App\Models\Bills;
use App\Models\ReferralCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $referral = new Referrals;
        $referral->name = $validated['name'];
        $referral->email = $validated['email'] ?? null;
        $referral->phone = $validated['phone'] ?? null;
        $referral->commission_percentage = $validated['commission_percentage'] ?? 0;
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
        $validated = $request->validate([
            'id' => 'required|exists:referrals,id',
            'name1' => 'required|string|max:255',
            'email1' => 'nullable|email|max:255',
            'phone1' => 'nullable|string|max:20',
            'commission_percentage1' => 'nullable|numeric|min:0|max:100',
        ]);

        $referral = Referrals::find($request->id);
        $referral->update([
            'name' => $validated['name1'],
            'email' => $validated['email1'] ?? null,
            'phone' => $validated['phone1'] ?? null,
            'commission_percentage' => $validated['commission_percentage1'] ?? 0,
        ]);

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
     * Create or update referral commission based on bill
     * Called after a bill is created/updated
     */
    public function createCommissionFromBill(Bills $bill)
    {
        try {
            // Check if patient has a referral
            if (!$bill->patient || !$bill->patient->referred_by) {
                return null;
            }

            // Find referral by name
            $referral = Referrals::where('name', $bill->patient->referred_by)->first();

            if (!$referral || $referral->commission_percentage <= 0) {
                return null;
            }

            $billAmount = $bill->total_price ?? $bill->amount;
            $commissionAmount = $billAmount * ($referral->commission_percentage / 100);

            // Check if commission already exists for this bill
            $existingCommission = ReferralCommission::where('bill_id', $bill->id)->first();

            if ($existingCommission) {
                // Update existing commission
                $existingCommission->update([
                    'bill_amount' => $billAmount,
                    'commission_percentage' => $referral->commission_percentage,
                    'commission_amount' => $commissionAmount,
                ]);
                return $existingCommission;
            }

            // Create new commission record
            $commission = ReferralCommission::create([
                'referral_id' => $referral->id,
                'bill_id' => $bill->id,
                'patient_id' => $bill->patient_id,
                'bill_amount' => $billAmount,
                'commission_percentage' => $referral->commission_percentage,
                'commission_amount' => $commissionAmount,
                'status' => 'pending',
                'notes' => 'Commission for test(s): ' . ($bill->all_test ? implode(', ', array_map(function($t) { return $t->test_name ?? ''; }, json_decode($bill->all_test, true) ?? [])) : 'N/A'),
            ]);

            return $commission;
        } catch (\Exception $e) {
            Log::error('Failed to create referral commission: ' . $e->getMessage(), [
                'bill_id' => $bill->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Get all commissions for a referral
     */
    public function commissions($referralId)
    {
        try {
            $referral = Referrals::findOrFail($referralId);
            $commissions = $referral->commissions()->orderBy('created_at', 'desc')->paginate(15);

            $stats = ReferralCommission::getCommissionStats($referralId);

            return view('referrel.commissions', compact('referral', 'commissions', 'stats'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch commissions: ' . $e->getMessage());
            return redirect()->route('referrels.list')->with('error', 'Unable to load commissions');
        }
    }

    /**
     * Mark commission as paid
     */
    public function markCommissionPaid($commissionId)
    {
        try {
            $commission = ReferralCommission::findOrFail($commissionId);
            $commission->update(['status' => 'paid']);

            return response()->json([
                'success' => true,
                'message' => 'Commission marked as paid',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark commission as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update commission status',
            ], 500);
        }
    }

    /**
     * Get commission summary/dashboard
     */
    public function commissionDashboard()
    {
        try {
            $referrals = Referrals::with('commissions')->get();

            $stats = [
                'total_earned' => ReferralCommission::sum('commission_amount'),
                'pending' => ReferralCommission::where('status', 'pending')->sum('commission_amount'),
                'paid' => ReferralCommission::where('status', 'paid')->sum('commission_amount'),
                'total_commissions' => ReferralCommission::count(),
            ];

            // Get top referrals with actual commission data from referral_commissions table
            $topReferrals = Referrals::with('commissions')
                ->withCount('commissions')
                ->withSum('commissions', 'commission_amount')
                ->orderBy('commissions_sum_commission_amount', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($referral) {
                    // Get the actual commission percentage from the first commission record for this referral
                    $firstCommission = $referral->commissions->first();
                    if ($firstCommission && $firstCommission->commission_percentage > 0) {
                        $referral->commission_percentage = $firstCommission->commission_percentage;
                    }
                    return $referral;
                });

            return view('referrel.commission_dashboard', compact('referrals', 'stats', 'topReferrals'));
        } catch (\Exception $e) {
            Log::error('Failed to load commission dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load commission dashboard');
        }
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
