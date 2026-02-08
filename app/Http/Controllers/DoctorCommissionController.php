<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorCommission;
use App\Models\Bills;
use App\Models\Referrals;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoctorCommissionController extends Controller
{
    /**
     * Doctor Commissions listing page
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $commissions = DoctorCommission::with(['bill', 'patient'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = DoctorCommission::getMonthlyStats($month);

        // Group by doctor
        $byDoctor = DoctorCommission::select('doctor_name', 
                DB::raw('SUM(commission_amount) as total'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN commission_amount ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN commission_amount ELSE 0 END) as paid')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('doctor_name')
            ->orderByDesc('total')
            ->get();

        return view('financial.doctor-commissions', compact('month', 'commissions', 'stats', 'byDoctor'));
    }

    /**
     * Store a new doctor commission record
     */
    public function store(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'doctor_name' => 'required|string|max:255',
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $bill = Bills::findOrFail($request->bill_id);
        $billAmount = $bill->total_price ?? $bill->amount;
        $commissionAmount = $billAmount * ($request->commission_percentage / 100);

        // Find referral by doctor name if exists
        $referral = Referrals::where('name', $request->doctor_name)->first();

        DoctorCommission::create([
            'referral_id' => $referral->id ?? null,
            'bill_id' => $bill->id,
            'patient_id' => $bill->patient_id,
            'doctor_name' => $request->doctor_name,
            'bill_amount' => $billAmount,
            'commission_percentage' => $request->commission_percentage,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Doctor commission recorded successfully.');
    }

    /**
     * Mark commission as paid
     */
    public function markPaid($id)
    {
        $commission = DoctorCommission::findOrFail($id);
        $commission->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Commission marked as paid.');
    }

    /**
     * Mark all pending commissions for a doctor as paid (bulk)
     */
    public function markDoctorPaid(Request $request)
    {
        $request->validate(['doctor_name' => 'required|string']);

        DoctorCommission::where('doctor_name', $request->doctor_name)
            ->where('status', 'pending')
            ->update([
                'status' => 'paid',
                'paid_date' => now(),
            ]);

        return redirect()->back()->with('success', 'All pending commissions for ' . $request->doctor_name . ' marked as paid.');
    }

    /**
     * Delete a commission record
     */
    public function destroy($id)
    {
        DoctorCommission::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Commission record deleted.');
    }
}
