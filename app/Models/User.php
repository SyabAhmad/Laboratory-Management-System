<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'employees',
        'patients',
        'testcategory',
        'referral',
        'billing',
        'pathology',
        'radiology',
        'ultrasonography',
        'electrocardiography',
        'reportbooth',
        'financial',
        'report_g',
        'inventory',
        'billing_add',
        'billing_edit',
        'billing_delete',
        'employees_add',
        'employees_edit',
        'employees_delete',
        'pathology_add',
        'pathology_edit',
        'pathology_delete',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
