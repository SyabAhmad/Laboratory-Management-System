<?php

namespace App\Http\Controllers;

use App\Models\LabTestParameter;
use App\Models\LabTestCat;
use Illuminate\Http\Request;

class LabTestParameterController extends Controller
{
    public function create($testId)
    {
        $test = LabTestCat::with('parameters')->findOrFail($testId);
        return view('LabTest.add_parameters', compact('test'));
    }

    public function store(Request $request, $testId)
    {
        $request->validate([
            'parameter_name.*' => 'required|string|max:191',
            'unit.*' => 'nullable|string|max:50',
            'reference_range.*' => 'nullable|string|max:191',
        ]);

        $created = [];
        foreach ($request->parameter_name as $index => $name) {
            $p = LabTestParameter::create([
                'lab_test_cat_id' => $testId,
                'parameter_name' => $name,
                'unit' => $request->unit[$index] ?? null,
                'reference_range' => $request->reference_range[$index] ?? null,
            ]);

            $created[] = $p;
        }

        // If request is AJAX, return JSON with created parameters so the frontend can update in-place
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'params' => $created,
            ]);
        }

        // Non-AJAX fallback: redirect back to the same add-parameters page for this test
        return redirect()->route('labtest.parameters.create', $testId)->with('success', 'Parameters added successfully.');
    }

    /**
     * Remove the specified parameter.
     */
    public function destroy($id)
    {
        $param = LabTestParameter::findOrFail($id);

        // Delete the parameter
        $param->delete();

        // Redirect back to the parameters page for the same test
        return redirect()->route('labtest.parameters.create', $param->lab_test_cat_id)
            ->with('success', 'Parameter deleted successfully.');
    }

    /**
     * Update the specified parameter.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'parameter_name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:191',
        ]);

        $param = LabTestParameter::findOrFail($id);

        $param->parameter_name = $request->input('parameter_name');
        $param->unit = $request->input('unit');
        $param->reference_range = $request->input('reference_range');
        $param->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'param' => $param,
            ]);
        }

        return redirect()->route('labtest.parameters.create', $param->lab_test_cat_id)
            ->with('success', 'Parameter updated successfully.');
    }
}
