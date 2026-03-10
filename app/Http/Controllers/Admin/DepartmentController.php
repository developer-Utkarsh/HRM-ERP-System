<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;
use Input;
use DB;
use Excel;
use App\Exports\DepartmentExport;
use App\User;

class DepartmentController extends Controller
{
    
    public function index()
    { 
        $name = Input::get('name');
        $status = Input::get('status');

        $department = Department::where('is_deleted', '0')->orderBy('id', 'desc');
        //echo '<pre>'; print_r($department);die;
        if (!empty($name)){
            $department->where('name', 'LIKE', '%' . $name . '%');
        }
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            if($status == 'Inactive'){
                $department->where('status', '=', '0');
            }else{
                $department->where('status', '=', 'Active');
            }
        }

        $department = $department->get();

        return view('admin.department.index', compact('department'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.department.add');
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
            'name' => 'required|unique:departments',
        ]);

        $inputs = $request->only('name','status');        

        $department = Department::create($inputs);    

        if ($department->save()) {
            return redirect()->route('admin.department.index')->with('success', 'Department Added Successfully');
        } else {
            return redirect()->route('admin.department.index')->with('error', 'Something Went Wrong !');
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
        $department = Department::find($id); //echo '<pre>'; print_r($department);die;
        return view('admin.department.edit', compact('department'));
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
            'name' => 'required|unique:departments,name,'.$id,
        ]);

        $role = Department::where('id', $id)->first();

        $inputs = $request->only('name','status');       

        if ($role->update($inputs)) {
            return redirect()->route('admin.department.index')->with('success', 'Department Updated Successfully');
        } else {
            return redirect()->route('admin.department.index')->with('error', 'Something Went Wrong !');
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
        $role = Department::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($role->update($inputs)) {
            return redirect()->back()->with('success', 'Department Deleted Successfully');
        } else {
            return redirect()->route('admin.department.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function togglePublish($id) {
        $department = Department::find($id);
        if (is_null($department)) {
            return redirect()->route('admin.department.index')->with('error', 'Department not found');
        }
		
		if($department->status == 'Active'){
			$sts = '0';
		}
		else{
			$sts = '1';
		}
		
		$department->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        // try {
            // $department->update([
                // 'status' => !$department->status,
                // 'updated_at' => new \DateTime(),
            // ]);
        // } catch (\PDOException $e) {
            // Log::error($this->getLogMsg($e));
            // return redirect()->back()->with('error', $this->getMessage($e));
        // }
        return redirect()->route('admin.department.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function download_excel()
    {   
		$name = Input::get('name');
        $status = Input::get('status');

        $department = Department::where('is_deleted', '0')->orderBy('id', 'desc');
 
        if (!empty($name)){
            $department->where('name', 'LIKE', '%' . $name . '%');
        }
        
        if(!empty($status)){
            if($status == 'Inactive'){
                $department->where('status', '=', 'Dective');
            }else{
                $department->where('status', '=', 'Active');
            }
        }

        $department = $department->get();
	
		//echo '<pre>'; print_r($designation);die;
        if(count($department) > 0){
            return Excel::download(new DepartmentExport($department), 'DepartmentData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function changeDepartment(){
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        $employees_list = User::where('status','1')->where('is_deleted','0')->get();
        return view('admin.department.change-department', compact('department_list','employees_list'));
    }

    public function storeChangeDepartment(Request $request){
        //dd($request->post());
        $validatedData = $request->validate([
            'from_department' => 'required',
            'to_department' => 'required|different:from_department',
        ]);

        $emp_list = User::where('status', '1')->where('is_deleted', '0')->where('department_type', $request->from_department);
        if($emp_list->count() > 0){
            $emp_list->update(['department_type' => $request->to_department ]);

            if ($emp_list) {
                return redirect()->route('admin.change-department')->with('success', 'Department Changed Successfully');
            } else {
                return redirect()->route('admin.change-department')->with('error', 'Something Went Wrong !');
            }
        }
        else {
            return redirect()->route('admin.change-department')->with('error', 'User Not Found');
        }
    }

    public function storeChangeEmployeeDepartment(Request $request){
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'employee_to_department' => 'required',
        ]);
        //echo '<pre>'; print_r($request->post()); die;
        $single_emp_list = User::where('status', '1')->where('is_deleted', '0')->whereIn('id', $request->employee_name);
        
        if($single_emp_list->count() > 0){
            $single_emp_list->update(['department_type' => $request->employee_to_department ]);

            if ($single_emp_list) {
                return redirect()->route('admin.change-department')->with('success', 'Employee Department Changed Successfully');
            } else {
                return redirect()->route('admin.change-department')->with('error', 'Something Went Wrong !');
            }
        }
        else {
            return redirect()->route('admin.change-department')->with('error', 'User Not Found');
        }
    }
}
