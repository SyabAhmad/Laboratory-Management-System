<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $guarded = []; // adjust as needed, or use $fillable

    /**
     * Optional: relation back to patient.
     * Adjust foreign key / model class name if your patients model differs.
     */
    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    /**
     * Get the referral commissions associated with this bill
     */
    public function referralCommission()
    {
        return $this->hasOne(ReferralCommission::class, 'bill_id');
    }

    /**
     * Calculate referral commission if a referral is present
     */
    public function calculateReferralCommission()
    {
        if (!$this->patient || !$this->patient->referred_by) {
            return null;
        }

        // Find referral by name
        $referral = Referrals::where('name', $this->patient->referred_by)->first();

        if (!$referral || $referral->commission_percentage <= 0) {
            return null;
        }

        $commissionAmount = ($this->total_price ?? $this->amount) * ($referral->commission_percentage / 100);

        return [
            'referral_id' => $referral->id,
            'commission_percentage' => $referral->commission_percentage,
            'commission_amount' => $commissionAmount,
        ];
    }

    /**
     * Get referral commission details
     */
    public function getReferralCommissionDetails()
    {
        $commissionData = $this->calculateReferralCommission();

        if (!$commissionData) {
            return [
                'referral_name' => null,
                'commission_percentage' => 0,
                'commission_amount' => 0,
            ];
        }

        $referral = Referrals::find($commissionData['referral_id']);

        return [
            'referral_name' => $referral->name ?? 'Unknown',
            'commission_percentage' => $commissionData['commission_percentage'],
            'commission_amount' => $commissionData['commission_amount'],
        ];
    }
}
