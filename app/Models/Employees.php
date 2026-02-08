<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    /**
     * Staff user_types that should have employee records
     */
    public static $staffRoles = [
        'Employees', 'Admin', 'Accountant', 'Receptionist',
        'Lab Scientist', 'Radiographer', 'Sonographer',
    ];

    protected $fillable = [
        'user_id',
        'employee_id',
        'address',
        'phone',
        'image',
        'dob',
        'position',
        'join_of_date',
        'salary',
        'gender',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Auto-sync: create employee records for staff users who don't have one yet.
     * Returns the count of newly created records.
     */
    public static function syncFromUsers()
    {
        $existingUserIds = self::pluck('user_id')->toArray();

        $staffUsers = User::whereIn('user_type', self::$staffRoles)
            ->whereNotIn('id', $existingUserIds)
            ->where('status', 'Active')
            ->get();

        $created = 0;
        foreach ($staffUsers as $user) {
            $count = self::count();
            self::create([
                'user_id' => $user->id,
                'employee_id' => date('Ym') . '0' . ($count + $user->id),
                'position' => $user->user_type,
                'salary' => 0,
            ]);
            $created++;
        }

        return $created;
    }
}

