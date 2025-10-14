<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $table = 'labtest';

    protected $fillable = [
        'test_name',
        'price',
        'status',
    ];

    /**
     * Get all test reports for this lab test
     */
    public function testReports()
    {
        return $this->hasMany(TestReport::class, 'test_id');
    }
}