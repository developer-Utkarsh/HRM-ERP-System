<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Input;
use App\FacultyRelation;
use App\Userdetails;
use App\Subject;
use DB;
use App\Userbranches;
use App\Studio;
use App\Users_pending;
use Auth;
use Excel;
use App\Exports\EmployeeExport;
use App\AttendanceNew;
use App\Attendance;
use App\Exports\LateEmployeeExport;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		
		
		
		$search = Input::get('search');
        $branch_id = Input::get('branch_id'); 

        $drivers = User::with(['user_branches.branch','role','user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');// dk
		$drivers->where('register_id','!=',NUll);
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
        if (!empty($search)) {
            $drivers->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }

        if(!empty($branch_id)) {
			$drivers->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if($logged_role_id == 21){
            $drivers = $drivers->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
        }
		
       $drivers = $drivers->paginate(50);
        
		$allDepartmentTypes  = $this->allDepartmentTypes();
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        
		//echo '<pre>'; print_r($drivers); die;
        return view('admin.driver.index', compact('drivers','pageNumber','params','allDepartmentTypes')); 
    }

  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	//dk
    public function assign($id)
    {
        $employee = User::with('user_details')
				->WhereHas('user_details', function ($q) { // orWhereHas dk
					$q->where('degination', '=', 'DRIVER');
				})
				->find($id);
		if(!empty($employee)){
			$assign_faculties = array();
			$driver_faculty = DB::table('driver_faculties')->where(['driver_id'=>$id])->first();
			if(!empty($driver_faculty)){
				$assign_faculties = json_decode($driver_faculty->faculty_ids);
			}
		}
		else{
			echo "<h1>Something Went Wrong.</h1>"; die;
		}
		
        return view('admin.driver.assign', compact('employee','assign_faculties'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	//dk
    public function update(Request $request, $id)
    {
		$driver = DB::table('driver_faculties')->where('driver_id',$id)->first();
		if(!empty($request->faculty_id)){
			foreach($request->faculty_id as $f_ids){
				$checkAlready = DB::table('driver_faculties')
					->leftJoin('users', 'users.id', '=', 'driver_faculties.driver_id')
					->where('users.status',1)
					->where('users.is_deleted','0')
					->whereRaw('driver_faculties.faculty_ids LIKE  \'%"'.$f_ids.'"%\' ')
					->count();
				if($checkAlready > 0){
					return redirect()->back()->with('error', 'Faculty alrady assigned !');
				}
			}
		}
		$faculty_id = json_encode($request->faculty_id);
		if(!empty($driver)){
			$update = DB::table('driver_faculties')->where('id', $driver->id)->update([ 'faculty_ids' => $faculty_id]);
			if($update) {				
				return redirect()->route('admin.drivers.index')->with('success', 'Assign Successfully');
			} else {
				return redirect()->route('admin.drivers.assign',$id)->with('error', 'Something Went Wrong !');
			}
		}
		else{
			$insert = DB::table('driver_faculties')->insertGetId([ 'driver_id' => $id, 'faculty_ids' => $faculty_id ]);
			if($insert){
				return redirect()->route('admin.drivers.index')->with('success', 'Assign Successfully');
			}
			else{
				return redirect()->route('admin.drivers.assign',$id)->with('error', 'Something Went Wrong !');
			}
		}
    }
	
	
	public function update_driver(Request $request)
    {
		$driver_id = $request->driver_id;
		$faculty_id = $request->faculty_id;
		$assign_date = $request->assign_date;
		if($driver_id == "0"){
			$update = DB::table('driver_faculties')
					->where('faculty_id', $faculty_id)
					->whereRaw("DATE(assign_date) = '$assign_date'")
					->update([ 'driver_id' => null]);
			if($update) {				
				return response(['status' => true, 'message' => 'Driver remove successfully.'], 200);
			} else {
				return response(['status' => false, 'message' => 'Somethig went wrong.'], 200);
			}
		}
		// echo $assign_date; die;
		$check_alerady = DB::table('driver_faculties')
					->where('driver_id',$driver_id)
					->where('faculty_id',$faculty_id)
					->whereRaw("DATE(assign_date) = '$assign_date'")
					->first();
		if(!empty($check_alerady)){
			return response(['status' => false, 'message' => 'Alrady assign same driver on same date'], 200);
		}
		else{
			$check_alerady = DB::table('driver_faculties')
					->where('faculty_id',$faculty_id)
					->whereRaw("DATE(assign_date) = '$assign_date'")
					->first();
			if(!empty($check_alerady)){
				$update = DB::table('driver_faculties')->where('id', $check_alerady->id)->update([ 'driver_id' => $driver_id]);
				if($update) {				
					return response(['status' => true, 'message' => 'Driver update successfully.'], 200);
				} else {
					return response(['status' => false, 'message' => 'Somethig went wrong.'], 200);
				}
			}
			else{			
				$insert = DB::table('driver_faculties')->insertGetId([ 'driver_id' => $driver_id, 'faculty_id' => $faculty_id, 'assign_date' => $assign_date ]);
				if($insert){
					return response(['status' => true, 'message' => 'Driver assign successfully.'], 200);
				}
				else{
					return response(['status' => false, 'message' => 'Somethig went wrong.'], 200);
				}
			}
		}
		
    }

   
}
