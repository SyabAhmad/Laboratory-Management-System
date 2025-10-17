<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Bills;

class Patients extends Model
{
    use HasFactory;

    protected $table = 'patients';

    // allow mass assignment for creating patients
    protected $fillable = [
        'patient_id',
        'user_id',
        'name',
        'mobile_phone',
        'address',
        'gender',
        'age',
        'blood_group',
        'receiving_date',
        'reporting_date',
        'note',
        'referred_by',
        'test_category',
        'test_report',
        'registerd_by',
    ];

    protected $casts = [
        'receiving_date' => 'date',
        'reporting_date' => 'date',
    ];

    /**
     * Get the user that registered this patient
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all bills for this patient
     */
    public function bills()
    {
        return $this->hasMany(Bills::class, 'patient_id');
    }

    /**
     * Get all test reports for this patient
     */
    public function testReports()
    {
        return $this->hasMany(TestReport::class, 'patient_id');
    }

    /**
     * Get age attribute (if birth_date exists, calculate from it)
     */
    public function getAgeAttribute($value)
    {
        // If age is stored directly, return it
        if ($value) {
            return $value;
        }

        // If you have a birth_date column, calculate age
        if (isset($this->attributes['birth_date'])) {
            return \Carbon\Carbon::parse($this->attributes['birth_date'])->age;
        }

        return null;
    }
}
