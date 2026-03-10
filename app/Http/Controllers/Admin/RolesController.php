<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;
use Input;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = Input::get('name');
        $status = Input::get('status');

        $roles = Role::where('is_deleted', '0')->orderBy('id', 'desc');

        if (!empty($name)){
            $roles->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $roles->where('status', '=', '0');
            }else{
                $roles->where('status', '=', '1');
            }
        }

        $roles = $roles->get();

        return view('admin.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.role.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles',
        ]);

        $inputs = $request->only('name','status');        

        $role = Role::create($inputs);    

        if ($role->save()) {
            return redirect()->route('admin.roles.index')->with('success', 'Role Added Successfully');
        } else {
            return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong !');
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
        $role = Role::find($id);
        return view('admin.role.edit', compact('role'));
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
            'name' => 'required|unique:roles,name,'.$id,
        ]);

        $role = Role::where('id', $id)->first();

        $inputs = $request->only('name','status');       

        if ($role->update($inputs)) {
            return redirect()->route('admin.roles.index')->with('success', 'Role Updated Successfully');
        } else {
            return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
		
		if(!empty($role)){
			$role->update([
                'is_deleted' => '1',
                'status' => '0',
            ]);
			
            return redirect()->back()->with('success', 'Role Deleted Successfully');
        } else {
            return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function togglePublish($id) {
        $role = Role::find($id);
        if (is_null($role)) {
            return redirect()->route('admin.roles.index')->with('error', 'Role not found');
        }
        try {
            $role->update([
                'status' => !$role->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.roles.index')->with('success', 'Status Updated Successfully.');
    }
}
