th<?php

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
            'field_type.*' => 'nullable|in:text,number,dual_option,textarea',
            'dual_options[0][*]' => 'nullable|string|max:100',
            'dual_options[1][*]' => 'nullable|string|max:100',
            'unit.*' => 'nullable|string|max:50',
            'reference_range.*' => 'nullable|string|max:191',
        ]);

        $created = [];
        foreach ($request->parameter_name as $index => $name) {
            $dualOptions = null;
            $fieldType = $request->field_type[$index] ?? 'text';
            
            if ($fieldType === 'dual_option') {
                // For dual option fields, get the two options from the request
                $option1 = $request->input("dual_options.0.{$index}");
                $option2 = $request->input("dual_options.1.{$index}");
                
                if (!empty($option1) || !empty($option2)) {
                    $dualOptions = array_filter([$option1, $option2]);
                    // Ensure we have at least one option
                    if (empty($dualOptions)) {
                        $dualOptions = null;
                    }
                }
            }

            $p = LabTestParameter::create([
                'lab_test_cat_id' => $testId,
                'parameter_name' => $name,
                'unit' => $fieldType === 'dual_option' ? null : ($request->unit[$index] ?? null),
                'reference_range' => $fieldType === 'dual_option' ? null : ($request->reference_range[$index] ?? null),
                'field_type' => $fieldType,
                'dual_options' => $dualOptions ? json_encode($dualOptions) : null,
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
            'field_type' => 'nullable|in:text,number,dual_option,textarea',
        ]);

        $param = LabTestParameter::findOrFail($id);

        $param->parameter_name = $request->input('parameter_name');
        $param->unit = $request->input('unit');
        $param->reference_range = $request->input('reference_range');
        $param->field_type = $request->input('field_type', 'text');

        // Handle dual options for dual_option field type
        if ($param->field_type === 'dual_option') {
            $option1 = $request->input('dual_option_1');
            $option2 = $request->input('dual_option_2');
            $dualOptions = array_filter([$option1, $option2]);
            $param->dual_options = !empty($dualOptions) ? json_encode($dualOptions) : null;
        } else {
            $param->dual_options = null;
        }

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
