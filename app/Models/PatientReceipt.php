<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReceipt extends Model
{
    use HasFactory;

    protected $table = 'patient_receipts';

    protected $fillable = [
        'patient_id',
        'receipt_number',
        'total_amount',
        'tests',
        'status',
        'notes',
        'printed_by',
    ];

    protected $casts = [
        'tests' => 'array',
        'total_amount' => 'float',
    ];

    /**
     * Get the patient associated with this receipt
     */
    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    /**
     * Generate unique receipt number (token)
     */
    public static function generateReceiptNumber()
    {
        do {
            // Format: YYYYMMDD + 6 random digits
            $number = date('Ymd') . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('receipt_number', $number)->exists());

        return $number;
    }

    /**
     * Create receipt from patient registration
     */
    public static function createFromPatient($patient, $tests = [], $printedBy = null)
    {
        $receipt = new self();
        $receipt->patient_id = $patient->id;
        $receipt->receipt_number = self::generateReceiptNumber();
        $receipt->printed_by = $printedBy;
        
        // Process tests and calculate total
        $totalAmount = 0;
        $testDetails = [];
        
        if (!empty($tests) && is_array($tests)) {
            foreach ($tests as $testName) {
                // Get test price from LabTest
                $labTest = LabTest::where('test_name', $testName)->first();
                
                if ($labTest) {
                    $price = $labTest->price ?? 0;
                    $totalAmount += $price;
                    
                    $testDetails[] = [
                        'test_name' => $testName,
                        'price' => $price,
                        'paid_status' => 'unpaid',
                        'discount' => 0,
                    ];
                }
            }
        }
        
        $receipt->total_amount = $totalAmount;
        $receipt->tests = $testDetails;
        $receipt->status = 'draft';
        
        return $receipt;
    }

    /**
     * Get formatted receipt number for barcode display
     */
    public function getFormattedReceiptNumber()
    {
        // Format as: XXXX XXX XXXX for better readability
        $num = $this->receipt_number;
        return substr($num, 0, 8) . ' ' . substr($num, 8);
    }

    /**
     * Get total tests count
     */
    public function getTestCount()
    {
        return is_array($this->tests) ? count($this->tests) : 0;
    }

    /**
     * Get paid tests count
     */
    public function getPaidTestCount()
    {
        if (!is_array($this->tests)) {
            return 0;
        }
        
        return collect($this->tests)->where('paid_status', 'paid')->count();
    }

    /**
     * Mark receipt as paid
     */
    public function markAsPaid()
    {
        $this->status = 'paid';
        // Mark all tests as paid
        if (is_array($this->tests)) {
            foreach ($this->tests as &$test) {
                $test['paid_status'] = 'paid';
            }
        }
        return $this->save();
    }

    /**
     * Mark specific test as paid
     */
    public function markTestAsPaid($testName)
    {
        if (is_array($this->tests)) {
            foreach ($this->tests as &$test) {
                if ($test['test_name'] === $testName) {
                    $test['paid_status'] = 'paid';
                }
            }
            return $this->save();
        }
        return false;
    }
}
