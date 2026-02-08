<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorCommission extends Model
{
    use HasFactory;

    protected $table = 'doctor_commissions';

    protected $fillable = [
        'referral_id',
        'bill_id',
        'patient_id',
        'doctor_name',
        'bill_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_date' => 'date',
    ];

    public function bill()
    {
        return $this->belongsTo(Bills::class, 'bill_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function referral()
    {
        return $this->belongsTo(Referrals::class, 'referral_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForMonth($query, $month)
    {
        return $query->where('created_at', '>=', $month . '-01')
                     ->where('created_at', '<', date('Y-m-d', strtotime($month . '-01 +1 month')));
    }

    public static function getMonthlyStats($month = null)
    {
        $month = $month ?: date('Y-m');
        $query = self::forMonth($month);

        return [
            'total' => $query->sum('commission_amount'),
            'pending' => (clone $query)->pending()->sum('commission_amount'),
            'paid' => (clone $query)->paid()->sum('commission_amount'),
            'count' => $query->count(),
        ];
    }
}
