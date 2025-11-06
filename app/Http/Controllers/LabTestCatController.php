<?php

namespace App\Http\Controllers;

use App\Models\LabTestCat;
use App\Models\Department;
use Illuminate\Http\Request;

class LabTestCatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labtest = LabTestCat::with('departmentRelation')->orderBy('cat_name')->get();
        $departments = Department::orderBy('name')->get();
        return view('LabTest.labtest', compact('labtest', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'cat_name' => 'required|string|max:191',
            'department_id' => 'nullable|exists:departments,id',
            'price' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $labtest = LabTestCat::create([
            'cat_name' => $request->input('cat_name'),
            'department_id' => $request->input('department_id'),
            'price' => $request->input('price', 0),
            'notes' => $request->input('notes'),
            'status' => 1,
        ]);

        return response()->json(['success' => 'Data added successfully.', 'data' => $labtest]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LabTestCat  $labTestCat
     * @return \Illuminate\Http\Response
     */
    public function show(LabTestCat $labTestCat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LabTestCat  $labTestCat
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $labtest = LabTestCat::findOrFail($id);
        return response()->json($labtest);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LabTestCat  $labTestCat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:labtest_cat,id',
            'cat_name1' => 'required|string|max:191',
            'department_id1' => 'nullable|exists:departments,id',
            'price1' => 'nullable|numeric',
            'notes1' => 'nullable|string',
        ]);

        $labtest = LabTestCat::findOrFail($validated['id']);
        $labtest->cat_name = $request->input('cat_name1');
        $labtest->department_id = $request->input('department_id1');
        // Clear old string department when using department_id
        if ($request->has('department_id1')) {
            $labtest->department = null;
        }
        $labtest->price = $validated['price1'] ?? $labtest->price;
        $labtest->notes = $request->input('notes1');
        $labtest->save();

        return response()->json(['success' => 'Data updated successfully.', 'data' => $labtest]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LabTestCat  $labTestCat
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $labtest = LabTestCat::findOrFail($id);
        $labtest->delete();
        return response()->json(['success' => 'Data Delete successfully.']);
    }
}
