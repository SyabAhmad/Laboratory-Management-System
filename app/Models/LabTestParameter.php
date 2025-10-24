<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestParameter extends Model
{
    use HasFactory;

    protected $table = 'lab_test_parameters';

    protected $fillable = [
        'lab_test_cat_id',
        'parameter_name',
        'unit',
        'reference_range',
    ];

    public function category()
    {
        return $this->belongsTo(LabTestCat::class, 'lab_test_cat_id');
    }
}
