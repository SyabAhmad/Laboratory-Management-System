<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'registerd_by',
    ];

    protected $casts = [
        'receiving_date' => 'date',
        'reporting_date' => 'date',
        'test_category' => 'array', // Cast to array automatically
    ];

    // append computed attributes to arrays / JSON
    // protected $appends = ['age'];

    // public function users(){
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referral()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    // patient usually has many bills
    public function bills()
    {
        return $this->hasMany(Bills::class, 'patient_id');
    }

    // computed age accessor
    // public function getAgeAttribute()
    // {
    //     if (empty($this->dob)) {
    //         return null;
    //     }

    //     return Carbon::parse($this->dob)->age;
    // }
}
