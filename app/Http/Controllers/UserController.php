<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::whereIn('user_type', ['Admin', 'Employees', 'Super Admin', 'Accountant', 'Receptionist', 'Lab Scientist', 'Radiographer', 'Sonographer'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($item) {
                    $togolebutton = '<input ' . ($item->status == "Active" ? "checked" : "") . ' type="checkbox" class="status" id="status" data-id="' . $item->id . '" />';
                    return $togolebutton;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0);" class="btn btn-info btn-sm view-permissions" data-id="' . $row->id . '" data-name="' . $row->name . '"><i class="fas fa-eye"></i> Permissions</a> ';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-warning btn-sm editbtn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></a> ';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm passchange" data-id="' . $row->id . '"><i class="fas fa-lock"></i></a> ';

                    if (Auth::user()->user_type == $row->user_type && Auth::user()->email == $row->email) {
                        $btn .= '<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-danger btn-sm deletebtn disabled"> <i class="fas fa-trash"></i> </a>';
                    } else {
                        $btn .= '<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-danger btn-sm deletebtn"> <i class="fas fa-trash"></i> </a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('user.user');
    }

    public function statuschange($id, Request $requst)
    {
       try{
        $user = User::find($id);
        $user->status = $requst->status;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
       } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
       }
    }

    public function employeeschange($id, Request $request)
    {
        $user = User::find($id);
        $user->employees = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function patientschange($id, Request $request)
    {
        $user = User::find($id);
        $user->patients = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function testcategory($id, Request $request)
    {
        $user = User::find($id);
        $user->testcategory = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function referral($id, Request $request)
    {
        $user = User::find($id);
        $user->referral = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function billing($id, Request $request)
    {
        $user = User::find($id);
        $user->billing = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function pathology($id, Request $request)
    {
        $user = User::find($id);
        $user->pathology = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function radiology($id, Request $request)
    {
        $user = User::find($id);
        $user->radiology = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function electrocardiography($id, Request $request)
    {
        $user = User::find($id);
        $user->electrocardiography = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function ultrasonography($id, Request $request)
    {
        $user = User::find($id);
        $user->ultrasonography = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function reportbooth($id, Request $request)
    {
        $user = User::find($id);
        $user->reportbooth = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function financial($id, Request $request)
    {
        $user = User::find($id);
        $user->financial = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function report_g($id, Request $request)
    {
        $user = User::find($id);
        $user->report_g = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }
    public function inventory($id, Request $request)
    {
        $user = User::find($id);
        $user->inventory = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Status changed successfully.']);
    }

    // Sub-permission methods
    public function employees_add_change($id, Request $request)
    {
        $user = User::find($id);
        $user->employees_add = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Employees Add permission updated successfully.']);
    }

    public function employees_edit_change($id, Request $request)
    {
        $user = User::find($id);
        $user->employees_edit = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Employees Edit permission updated successfully.']);
    }

    public function employees_delete_change($id, Request $request)
    {
        $user = User::find($id);
        $user->employees_delete = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Employees Delete permission updated successfully.']);
    }

    public function billing_add_change($id, Request $request)
    {
        $user = User::find($id);
        $user->billing_add = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Billing Add permission updated successfully.']);
    }

    public function billing_edit_change($id, Request $request)
    {
        $user = User::find($id);
        $user->billing_edit = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Billing Edit permission updated successfully.']);
    }

    public function billing_delete_change($id, Request $request)
    {
        $user = User::find($id);
        $user->billing_delete = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Billing Delete permission updated successfully.']);
    }

    public function pathology_add_change($id, Request $request)
    {
        $user = User::find($id);
        $user->pathology_add = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Pathology Add permission updated successfully.']);
    }

    public function pathology_edit_change($id, Request $request)
    {
        $user = User::find($id);
        $user->pathology_edit = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Pathology Edit permission updated successfully.']);
    }

    public function pathology_delete_change($id, Request $request)
    {
        $user = User::find($id);
        $user->pathology_delete = $request->catstatus;
        $user->update();
        return response()->json(['success' => 'Pathology Delete permission updated successfully.']);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::find($id);
        return response()->json($users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name1;
        $user->email = $request->email1;
        $user->user_type = $request->user_type1;
        $user->update();
        return response()->json(['success' => 'User updated successfully.']);
    }
    public function updatepass(Request $request)
    {
        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->update();
        return response()->json(['success' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            if (!is_null($user->profile_photo_path)) {
                $image_path = public_path() . '/assets/HMS/employees/' . $user->profile_photo_path;
                unlink($image_path);;
                $user->delete();
                return response()->json(['success' => 'Data Delete successfully.']);
            } else {
                $user->delete();
                return response()->json(['success' => 'Data Delete successfully.']);
            }
        }
    }

    /**
     * Display the authenticated user's profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update the authenticated user's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
    }

    public function getPermissions($id)
    {
        $user = User::find($id);
        return view('user.permissions_modal', compact('user'));
    }

    public function updatePermissions($id, Request $request)
    {
        $user = User::find($id);
        $user->update($request->only([
            'employees_add', 'employees_edit', 'employees_delete',
            'patients', 'testcategory', 'referral',
            'billing_add', 'billing_edit', 'billing_delete',
            'pathology_add', 'pathology_edit', 'pathology_delete',
            'radiology', 'ultrasonography', 'electrocardiography',
            'reportbooth', 'financial', 'report_g', 'inventory'
        ]));
        return response()->json(['success' => 'Permissions updated successfully']);
    }
}
