<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    use HasFactory;

    protected $table = 'testreports';

    protected $fillable = [
        'patient_id',
        'test_id',
        'invoice_id',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    /**
     * Get the patient that owns the test report
     */
    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    /**
     * Get the lab test associated with this report
     */
    public function labtest()
    {
        return $this->belongsTo(LabTest::class, 'test_id');
    }

    /**
     * Get the bill/invoice associated with this report
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'invoice_id');
    }
}
