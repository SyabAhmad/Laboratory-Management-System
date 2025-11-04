<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestCat extends Model
{
    use HasFactory;

    // set correct table name (change to 'labtests' if DB uses plural)
    protected $table = 'labtest_cat';

    // set to true if your table has created_at/updated_at columns
    public $timestamps = true;

    // allow mass assignment when using Model::create()
    protected $fillable = [
        'cat_name',
        'department',
        'price',
        'status',
        'notes',
    ];

    public function parameters()
    {
        return $this->hasMany(LabTestParameter::class, 'lab_test_cat_id');
    }
}
