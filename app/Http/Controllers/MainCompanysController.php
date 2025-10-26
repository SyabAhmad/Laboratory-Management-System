<?php

namespace App\Http\Controllers;

use App\Models\MainCompanys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MainCompanysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MainCompanys  $mainCompanys
     * @return \Illuminate\Http\Response
     */
    public function show(MainCompanys $mainCompanys)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MainCompanys  $mainCompanys
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $maincompanys = MainCompanys::find($id);
        if (!$maincompanys) {
            return response()->json(['message' => 'Lab information not found'], 404);
        }
        return response()->json($maincompanys);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MainCompanys  $mainCompanys
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:companys,id',
            'lab_name' => 'required|string|max:255',
            'lab_address' => 'required|string',
            'lab_phone' => 'required|string|max:20',
            'lab_email' => 'required|email|max:255',
            'lab_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $maincompanys = MainCompanys::find($validated['id']);
        $maincompanys->lab_name = $validated['lab_name'];
        $maincompanys->lab_address = $validated['lab_address'];
        $maincompanys->lab_phone = $validated['lab_phone'];
        $maincompanys->lab_email = $validated['lab_email'];

        if($request->hasFile('lab_image')){
            $destination = public_path().'/assets/HMS/lablogo/'.$maincompanys->lab_image;
            if(File::exists($destination)){
                File::delete($destination);
            }
            $image = $request->file('lab_image');
            $image_name = time().'.'.$image->getClientOriginalExtension();
            try {
                $image->move(public_path().'/assets/HMS/lablogo/',$image_name);
                $maincompanys->lab_image = $image_name;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to upload image. Please check file permissions or directory.'], 500);
            }
        }

        $maincompanys->save();
        return response()->json($maincompanys);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MainCompanys  $mainCompanys
     * @return \Illuminate\Http\Response
     */
    public function destroy(MainCompanys $mainCompanys)
    {
        //
    }
}
