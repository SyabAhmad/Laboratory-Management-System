<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCommission extends Model
{
    use HasFactory;

    protected $table = 'referral_commissions';

    protected $fillable = [
        'referral_id',
        'bill_id',
        'patient_id',
        'bill_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    /**
     * Get the referral that owns this commission
     */
    public function referral()
    {
        return $this->belongsTo(Referrals::class, 'referral_id');
    }

    /**
     * Get the bill associated with this commission
     */
    public function bill()
    {
        return $this->belongsTo(Bills::class, 'bill_id');
    }

    /**
     * Get the patient associated with this commission
     */
    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    /**
     * Scope to get pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get commissions for a specific referral
     */
    public function scopeForReferral($query, $referralId)
    {
        return $query->where('referral_id', $referralId);
    }

    /**
     * Calculate total commission for a referral
     */
    public static function getTotalCommissionForReferral($referralId, $status = null)
    {
        $query = self::where('referral_id', $referralId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->sum('commission_amount');
    }

    /**
     * Get commission statistics for a referral
     */
    public static function getCommissionStats($referralId)
    {
        return [
            'total_earned' => self::where('referral_id', $referralId)->sum('commission_amount'),
            'pending' => self::where('referral_id', $referralId)->where('status', 'pending')->sum('commission_amount'),
            'paid' => self::where('referral_id', $referralId)->where('status', 'paid')->sum('commission_amount'),
            'count' => self::where('referral_id', $referralId)->count(),
        ];
    }
}
