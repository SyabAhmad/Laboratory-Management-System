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
    ];

    /**
     * Get all patients referred by this referral
     */
    public function patients()
    {
        return $this->hasMany(Patients::class, 'referred_by', 'name');
    }
}
