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
}
