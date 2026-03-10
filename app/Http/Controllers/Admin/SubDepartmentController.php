<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;
use App\SubDepartment;
use Input;
use DB;
use Excel;
use App\Exports\SubDepartmentExport;
use App\User;

class SubDepartmentController extends Controller
{
    
    public function index()
    { 
        $name = Input::get('name');
        $status = Input::get('status');
        $department_id = Input::get('department_id');

        $sub_department = SubDepartment::with('department')->where('is_deleted', '0')->orderBy('id', 'desc');
       
        if (!empty($name)){
            $sub_department->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $sub_department->where('status', '=', '');
            }else{
                $sub_department->where('status', '=', 'Active');
            }
        }

        if (!empty($department_id)){
            $sub_department->where('department_id', $department_id);
        }

        $sub_department = $sub_department->get();
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        //echo '<pre>'; print_r($sub_department);die;
        return view('admin.sub_department.index', compact('sub_department','department_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        return view('admin.sub_department.add', compact('department_list'));
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
            'department_id' => 'required',
            'name' => 'required|unique:sub_departments',
        ]);

        $inputs = $request->only('department_id','name','status');        

        $sub_department = SubDepartment::create($inputs);    

        if ($sub_department->save()) {
            return redirect()->route('admin.sub_department.index')->with('success', 'Sub Department Added Successfully');
        } else {
            return redirect()->route('admin.sub_department.index')->with('error', 'Something Went Wrong !');
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
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        $sub_department = SubDepartment::find($id); //echo '<pre>'; print_r($department);die;
        return view('admin.sub_department.edit', compact('sub_department','department_list'));
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
            'department_id' => 'required',
            'name' => 'required|unique:departments,name,'.$id,
        ]);

        $sub_department = SubDepartment::where('id', $id)->first();

        $inputs = $request->only('department_id','name','status');       

        if ($sub_department->update($inputs)) {
            return redirect()->route('admin.sub_department.index')->with('success', 'Sub Department Updated Successfully');
        } else {
            return redirect()->route('admin.sub_department.index')->with('error', 'Something Went Wrong !');
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
        $sub_department = SubDepartment::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($sub_department->update($inputs)) {
            return redirect()->back()->with('success', 'Sub Department Deleted Successfully');
        } else {
            return redirect()->route('admin.sub_department.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function togglePublish($id) {
        $sub_department = SubDepartment::find($id);
        if (is_null($sub_department)) {
            return redirect()->route('admin.sub_department.index')->with('error', 'Sub Department not found');
        }
		
		if($sub_department->status == 'Active'){
			$sts = '0';
		}
		else{
			$sts = '1';
		}
		
		$sub_department->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.sub_department.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function download_excel()
    {   
		$name = Input::get('name');
        $status = Input::get('status');

        $sub_department = SubDepartment::with('department')->where('is_deleted', '0')->orderBy('id', 'desc');
       
        if (!empty($name)){
            $sub_department->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $sub_department->where('status', '=', '');
            }else{
                $sub_department->where('status', '=', 'Active');
            }
        }

        $sub_department = $sub_department->get();
	
		//echo '<pre>'; print_r($designation);die;
        if(count($sub_department) > 0){
            return Excel::download(new SubDepartmentExport($sub_department), 'SubDepartmentData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	
}
