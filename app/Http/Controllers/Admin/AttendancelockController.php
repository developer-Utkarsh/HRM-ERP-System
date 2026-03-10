<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;
use App\AttendanceLock;
use Input;

class AttendancelockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $month = Input::get('month');
        $status = Input::get('status');

        $roles = AttendanceLock::orderBy('id', 'desc');

        if (!empty($month)){
            $roles->where('month', $month);
        }

        if(isset($status)){
            $roles->where('status', '=', $status);
        }

        $roles = $roles->get();

        return view('admin.attendance-lock.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        return view('admin.attendance-lock.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_save(Request $request)
    {
        $validatedData = $request->validate([
            'month' => 'required|unique:attendance_lock',
        ]);

        $inputs = $request->only('month','status');        

        $role = AttendanceLock::create($inputs);    

        if ($role->save()) {
            return redirect()->route('admin.attendance-lock.index')->with('success', 'Added Successfully');
        } else {
            return redirect()->route('admin.attendance-lock.index')->with('error', 'Something Went Wrong !');
        }
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
        $role = AttendanceLock::find($id);
        return view('admin.attendance-lock.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'month' => 'required|unique:attendance_lock,month,'.$id,
        ]);

        $role = AttendanceLock::where('id', $id)->first();

        $inputs = $request->only('month','status');       

        if ($role->update($inputs)) {
            return redirect()->route('admin.attendance-lock.index')->with('success', 'Updated Successfully');
        } else {
            return redirect()->route('admin.attendance-lock.index')->with('error', 'Something Went Wrong !');
        }
    }
}
