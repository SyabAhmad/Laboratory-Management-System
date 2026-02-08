<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $table = 'salary_payments';

    protected $fillable = [
        'employee_id',
        'employee_name',
        'base_salary',
        'bonus',
        'deduction',
        'net_salary',
        'month',
        'payment_date',
        'status',
        'payment_method',
        'notes',
        'paid_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public static function getMonthlyStats($month = null)
    {
        $month = $month ?: date('Y-m');
        $query = self::forMonth($month);

        return [
            'total_payable' => $query->sum('net_salary'),
            'total_paid' => (clone $query)->paid()->sum('net_salary'),
            'total_pending' => (clone $query)->pending()->sum('net_salary'),
            'total_bonuses' => $query->sum('bonus'),
            'total_deductions' => $query->sum('deduction'),
            'count' => $query->count(),
        ];
    }
}
