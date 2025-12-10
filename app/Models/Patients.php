<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Bills;

class Patients extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($patient) {
            try {
                // Only set patient_id when not provided yet
                if (empty($patient->patient_id)) {
                    // Format: PT{YYYY}{serial} e.g. PT2025000001
                    $serial = str_pad($patient->id, 6, '0', STR_PAD_LEFT);
                    $year = null;
                    if ($patient->created_at instanceof Carbon) {
                        $year = $patient->created_at->format('Y');
                    } else {
                        $year = Carbon::now()->format('Y');
                    }
                    $patient->patient_id = 'PT' . $year . $serial;
                    // Save quietly to avoid firing other events
                    if (method_exists($patient, 'saveQuietly')) {
                        $patient->saveQuietly();
                    } else {
                        $patient->timestamps = false;
                        $patient->save();
                    }
                }
            } catch (\Exception $e) {
                // Log error but do not block creation
                \Log::error('Failed to set patient_id for patient: ' . $e->getMessage());
            }
        });
    }

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
        'receiving_datetime',
        'reporting_datetime',
        'note',
        'referred_by',
        'test_category',
        'test_report',
        'registerd_by',
        // New age part fields - migrations not yet applied; safe to keep here for future DB updates
        'age_years',
        'age_months',
        'age_days',
    ];

    protected $casts = [
        'receiving_date' => 'datetime',
        'reporting_date' => 'datetime',
        'receiving_datetime' => 'datetime',
        'reporting_datetime' => 'datetime',
        'age_years' => 'integer',
        'age_months' => 'integer',
        'age_days' => 'integer',
    ];

    /**
     * Get the user that registered this patient
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referral that referred the patient (if any)
     * Note: referrals are stored by name in patients.referred_by column
     */
    public function referral()
    {
        return $this->belongsTo(Referrals::class, 'referred_by', 'name');
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

    /**
     * Mutator: set the age string and attempt to synchronize age parts if present
     */
    public function setAgeAttribute($value)
    {
        $this->attributes['age'] = $value;

        // Parse components like '22Y 3M 5D' to fill separate fields if they exist
        try {
            $years = $months = $days = null;
            if ($value && is_string($value)) {
                if (preg_match('/(\d+)\s*Y/i', $value, $m)) $years = (int)$m[1];
                if (preg_match('/(\d+)\s*M/i', $value, $m)) $months = (int)$m[1];
                if (preg_match('/(\d+)\s*D/i', $value, $m)) $days = (int)$m[1];
                // fallback: plain numeric -> years
                if (is_numeric(trim($value)) && is_null($years) && is_null($months) && is_null($days)) {
                    $years = (int)trim($value);
                }
            }

            if (!is_null($years)) $this->attributes['age_years'] = $years;
            if (!is_null($months)) $this->attributes['age_months'] = $months;
            if (!is_null($days)) $this->attributes['age_days'] = $days;
        } catch (\Exception $e) {
            // silently ignore parsing issues
        }
    }
}
