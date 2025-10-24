<?php

namespace App\Http\Controllers;

use App\Models\LabTestParameter;
use App\Models\LabTestCat;
use Illuminate\Http\Request;

class LabTestParameterController extends Controller
{
    public function create($testId)
    {
        $test = LabTestCat::findOrFail($testId);
        return view('LabTest.add_parameters', compact('test'));
    }

    public function store(Request $request, $testId)
    {
        $request->validate([
            'parameter_name.*' => 'required|string|max:191',
            'unit.*' => 'nullable|string|max:50',
            'reference_range.*' => 'nullable|string|max:191',
        ]);

        foreach ($request->parameter_name as $index => $name) {
            LabTestParameter::create([
                'lab_test_cat_id' => $testId,
                'parameter_name' => $name,
                'unit' => $request->unit[$index],
                'reference_range' => $request->reference_range[$index],
            ]);
        }

        return redirect()->route('labtest.index')->with('success', 'Parameters added successfully.');
    }
}
