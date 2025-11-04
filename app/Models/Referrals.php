<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrals extends Model
{
    use HasFactory;
    protected $table = 'referrals';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'commission_percentage',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
    ];

    /**
     * Get all patients referred by this referral
     */
    public function patients()
    {
        return $this->hasMany(Patients::class, 'referred_by', 'name');
    }

    /**
     * Get all commissions for this referral
     */
    public function commissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referral_id');
    }

    /**
     * Get total commission earned
     */
    public function getTotalCommissionAttribute()
    {
        return $this->commissions()->sum('commission_amount');
    }

    /**
     * Get pending commission
     */
    public function getPendingCommissionAttribute()
    {
        return $this->commissions()->where('status', 'pending')->sum('commission_amount');
    }

    /**
     * Get paid commission
     */
    public function getPaidCommissionAttribute()
    {
        return $this->commissions()->where('status', 'paid')->sum('commission_amount');
    }
}
