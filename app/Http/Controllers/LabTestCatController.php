<?php

namespace App\Http\Controllers;

use App\Models\LabTestCat;
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
        $labtest = LabTestCat::all();
        return view('LabTest.labtest', compact('labtest'));
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
            'cat_name'   => 'required|string|max:191',
            'department' => 'nullable|string|max:191',
            'price'      => 'nullable|numeric',
        ]);

        $labtest = LabTestCat::create([
            'cat_name'   => $request->input('cat_name'),
            'department' => $request->input('department'),
            'price'      => $request->input('price', 0),
            'status'     => 1,
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
        $labtest = LabTestCat::find($id);
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
            'id'        => 'required|integer|exists:labtest,id',
            'test_name' => 'required_without:cat_name|string|max:191',
            'cat_name'  => 'required_without:test_name|string|max:191',
            'price'     => 'nullable|numeric',
        ]);

        $labtest = LabTestCat::find($validated['id']);
        $labtest->test_name = $request->input('test_name', $request->input('cat_name'));
        $labtest->price     = $validated['price'] ?? $labtest->price;
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
        $labtest = LabTestCat::find($id);
         $labtest->delete();
         return response()->json(['success'=>'Data Delete successfully.']);

    }
}
