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
        'first_name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'email',
        'user_id',
        'referred_by',
        // ...add other fields from your patients table
    ];

    // append computed attributes to arrays / JSON
    protected $appends = ['age'];

    // public function users(){
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referral()
    {
        return $this->belongsTo(Referrals::class, 'referred_by');
    }

    // patient usually has many bills
    public function bills()
    {
        return $this->hasMany(Bill::class, 'patient_id');
    }

    // computed age accessor
    public function getAgeAttribute()
    {
        if (empty($this->dob)) {
            return null;
        }

        return Carbon::parse($this->dob)->age;
    }
}
