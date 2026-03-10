<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use Input;
use Excel;
use App\NewTask;
use App\Leave;
use App\LeaveDetail;
use App\Exports\AttendanceExport;
use App\Exports\NewAttendanceExport;
use App\Exports\NewAbsentAttendanceExport;
use App\Exports\FullAttendanceExport;
use App\Exports\FullAttendanceExportTwo;
use App\Exports\AbsentFullAttendanceExport;
use App\Exports\IncompleteExport;
use App\Exports\AbsentuserExport;
use Auth;
use DB;
use Illuminate\Pagination\Paginator;
use App\AttendanceNew;
use App\Holiday;
use DataTables;
use DateTime;
use App\AttendanceLock;

use App\Userbranches;  //Chetan
use App\Branch;  	   //Chetan

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	public function absentuser()
    {
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logged_type     = Auth::user()->department_type;
		$name            = Input::get('name');
		$date            = Input::get('fdate');
		$department_type = Input::get('department_type');
        $branch_id       = Input::get('branch_id');
        $branch_location        = Input::get('branch_location');
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches         = $this->allBranches();
		
		
		if(!empty($date)){
			$date = $date;
		}
		else{
			$date = date('Y-m-d');
		}
		
		$where = "";
		
		$where .= " and users.register_id != '' ";
		if(!empty($name)){
			$where .= " and users.name LIKE '%$name%' ";
		}
		
		if(!empty($department_type)){
			$where .= " and users.department_type = '$department_type'";
		}
		
		if(!empty($branch_id)){
			$where .= " and userbranches.branch_id = '$branch_id'";
		}
		else{
			if($branch_location=='jaipur'){
				$where .= " and userbranches.branch_id in (43,55,57,58,59,61,62,64,72,91)";
			}
		}
		
		$where .= " and users.role_id != 2";
		
		if($logged_role_id == 21){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.role_id != 1 and users.role_id = "'.$logged_role_id.'" and users.department_type = "'.$logged_type.'" and users.status = 1 '.$where);
			
		}
		else if($logged_role_id == 29 || $logged_role_id == 24){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.role_id != 1 and users.status = 1'.$where);
			
		}
		else{
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.id = "'.$logged_id.'" and users.role_id != 1 and users.status = 1'.$where);
		}
		return view('admin.attendance.absentuser', compact('responseArray','allDepartmentTypes','allBranches'));
		
	}
	
	public function absentuser_download_excel()
    {   
		
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logged_type     = Auth::user()->department_type;
		$name            = Input::get('name');
		$date            = Input::get('fdate');
		$department_type = Input::get('department_type');
        $branch_id       = Input::get('branch_id');
        $branch_location        = Input::get('branch_location');
		
		
		
		if(!empty($date)){
			$date = $date;
		}
		else{
			$date = date('Y-m-d');
		}
		
		$where = "";
		
		$where .= " and users.register_id != '' ";
		if(!empty($name) && $name!='undefined'){
			$where .= " and users.name LIKE '%$name%' ";
		}
		
		if(!empty($department_type) && $department_type!='undefined'){
			$where .= " and users.department_type = '$department_type'";
		}
		
		if(!empty($branch_id) && $branch_id!='undefined'){
			$where .= " and userbranches.branch_id = '$branch_id'";
		}
		else{
			if($branch_location=='jaipur' && $branch_location!='undefined'){
				$where .= " and userbranches.branch_id in (43,55,57,58,59,61,62,64,72,91)";
			}
		}
		
		$where .= " and users.role_id != 2";
		
		if($logged_role_id == 21){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.role_id != 1 and users.role_id = "'.$logged_role_id.'" and users.department_type = "'.$logged_type.'" and users.status = 1 '.$where);
			
		}
		else if($logged_role_id == 29 || $logged_role_id == 24){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.role_id != 1 and users.status = 1'.$where);
			
		}
		else{
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userbranches ON users.id = userbranches.user_id WHERE (users.id NOT IN (SELECT emp_id FROM attendance where date ="'.$date.'") AND users.id NOT IN (SELECT emp_id FROM attendance_new where date ="'.$date.'")) and users.id = "'.$logged_id.'" and users.role_id != 1 and users.status = 1'.$where);
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new AbsentuserExport($responseArray), 'AbsentuserExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
	
	
    public function index()
    {
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
       
		//echo "<pre>"; print_r($pageNumber); die;
		return view('admin.attendance.index', compact('allDepartmentTypes','allBranches'));
    }
	
	public function create()
    {
		// if(date('Y-m-d') >= '2024-05-25'){
			// echo "<h3 style='color:red;'>Please mark attendance and apply for leaves using the DawinBox app only.<h3>";  die;
		// }
		
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		
		if($logged_role_id != 29 && $logged_role_id != 24){
			if(date('Y-m-d') >= '2023-10-01'){
				//echo "<h3 style='color:red;'>From 1 October attendance is only accepted by Darwinbox application. If you are facing any issues, contact the HR department.<h3>";  die;
			}
		}
		
		if($login_id == 901 || $login_id == 5344 || $login_id == 6267){
			
		}
		else{
			//echo "<b style='color:red;'>Note - Not access you from today.</b>"; die;
		}
		
		
		if($logged_role_id == 20){
			$users        = User::where('id', $login_id)->get();
		}
		else if($logged_role_id == 21){
			//$users = NewTask::getEmployeeForDepartmentHead($login_id, $logged_role_id, $logged_department_type);
			$users        = NewTask::getEmployeeByLogID($login_id,'create-attendance');
		}
		else{
			$users        = NewTask::getEmployeeByLogID($login_id,'create-attendance');
		}
		
        return view('admin.attendance.add', compact('users'));
    }
	
	public function store(Request $request)
    {
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
        $validatedData = $request->validate([
            'emp_id' => 'required',
            'date' => 'required',
        ]);
		if(empty($login_id) ){
			return redirect()->back()->with('error', 'You have not access.');
		}
		
		/* if($login_id != 901 && date('m') != date('m', strtotime($request->date))){
			return redirect()->back()->with('error', 'You have not access.');
		} */
		
		if(!empty($request->emp_id)){
			$emp_id = $request->emp_id;
			$dates  = $request->date;
			
			$mont_year = date('Y-m',strtotime($dates));
			if($logged_role_id != 24 && $logged_role_id!=29){
				$alock = AttendanceLock::where('month', $mont_year)->first();
				if(!empty($alock)){
					if($alock->status=='1'){
						return redirect()->back()->with('error', 'Attendance is locked.');
					}
				}
			}
			$type   = 'Full Day';
			$user = User::where('id', $emp_id)->first();
			if(!empty($user)){
				if($user->status=='1'){ 
					$inputs['emp_id'] = $emp_id;
					$inputs['date'] = $request->date;
					$i = 0;

					if (is_array($request->time) && !empty($request->time)) {
						$time = $request->time;
						$type = $request->type;
						foreach ($time as $key => $value) {
							if(!empty($value)){ 
								$data = array();
								$data['emp_id'] = $emp_id;
								$data['date'] = $request->date;
								$data['time'] = $value;
								$data['type'] = $type[$key];
								$data['created_at'] = date('Y-m-d H:i:s');
								$data['updated_at'] = date('Y-m-d H:i:s');
								$data['admin_id'] = $login_id;
								$data['reason'] = $request->reason;
								$data['for_reason'] = $request->for_reason;
								$attendance_id = Attendance::create($data);
								$i++;
								
								$this->maintain_history(Auth::user()->id, 'attendance', $attendance_id->id, 'create_attendance', json_encode($data));
							}
						}
						
					}
					
					if($i > 0){

						/*$check_leave = Leave::with('leave_details');

	                    $check_leave->WhereHas('leave_details', function ($q) use ($dates) {
											if(!empty($dates)){
												$q->where('date', $dates);
											}
										});
	                    $check_leave = $check_leave->where('emp_id', $emp_id)->first();*/
						
						$check_leave = DB::table('leave')
							->join('leave_details', 'leave.id', '=', 'leave_details.leave_id')
							->select('leave.*')
							->where('leave.emp_id', $emp_id)
							->where('leave_details.date', $dates)
							->first();
 
	                    if(!empty($check_leave->id)){
	                    	Leave::where('id', $check_leave->id)->delete();
	                    	LeaveDetail::where('leave_id', $check_leave->id)->delete();
							
							
							$leave_details_id = LeaveDetail::where('leave_id', $check_leave->id)->first();
							$check_leave_id = [$check_leave->id];
							$leave_details_id_res = [$leave_details_id->id];
							$this->maintain_history(Auth::user()->id, 'leave', $check_leave->id, 'delete_leave', json_encode($check_leave_id));

							$this->maintain_history(Auth::user()->id, 'leave_details', $leave_details_id->id, 'delete_leave_details', json_encode($leave_details_id_res));
	                    }

						return redirect()->route('admin.attendance.fullattendence')->with('success', 'Attendance Added Successfully');
					}
					else{
						return redirect()->back()->with('error', 'Attendance Empty');
					}
				}
				else{
					return redirect()->back()->with('error', 'User Not Active');
				}
				
			}
			else{
				return redirect()->back()->with('error', 'User Id Not Found');
			}
		}
		else{
			return redirect()->back()->with('error', 'User Id Not Found');
		}
    }

	public function edit($emp_id, $date)
    {
		$login_id        = Auth::user()->id;
		
		
		$attendance = Attendance::with('user');
		if(!empty($emp_id)){
			$attendance->where('emp_id', $emp_id);
		}
		if(!empty($date)){
			$attendance->where('date', $date);
		}
		
		$attendance = $attendance->first();
		
		$attendance_data = array();
		if(!empty($attendance)){
				$attendance_data['date'] = $attendance->date;
				$attendance_data['emp_id'] = $attendance->user->id;
				$attendance_data['name'] = $attendance->user->name;
				$attendance_data['total_hours'] = "00";
				
				$all_time = array();
				$get_attendance = Attendance::where('emp_id', $attendance->emp_id)->where('date', $attendance->date)->orderBy('id', 'asc')->get();
				if(count($get_attendance) > 0){
					$all_time = $get_attendance;
				}
				$attendance_data['time'] = $all_time;
		}
		
		// echo "<pre>"; print_r($attendance_data); die;
		
        return view('admin.attendance.edit', compact('attendance_data'));
    }
	
	public function update(Request $request, $id)
    {
		die('fffffffff');
		$login_id        = Auth::user()->id;
		if(!empty($request->time)){
			$i = 0;
			$img_array = array();
			if(!empty($request->att_id)){
				$exp_id = array_filter(explode(",", $request->att_id));
				if(count($exp_id) > 0){
					foreach($exp_id as $exp_id_val){
						$att_img = Attendance::where('id', $exp_id_val)->first();
						array_push($img_array, $att_img->image, $att_img->latitude, $att_img->longitude, $att_img->type);
						
						$exp_id_val_res = [$exp_id_val]; 
						$this->maintain_history(Auth::user()->id, 'attendance', $exp_id_val, 'delete_attendance', json_encode($exp_id_val_res));
					}
				 
					DB::table('attendance')->whereIn('id', $exp_id)->delete();
				}
			}
			//echo '<pre>'; print_r($img_array);die;
			foreach($request->time as $key => $timeVal){ 
				$insertArray = array(
					'emp_id' =>  $request->emp_id,
					'updated_by' =>  $login_id,
					'date'   =>  $request->date,
					'time' =>  $timeVal,
					'type' =>  $request->type[$key],
					'image' => (!empty($img_array[0]) && $request->type[$key] == $img_array[3]) ? $img_array[0] : '',
					'latitude' => (!empty($img_array[1]) && $request->type[$key] == $img_array[3]) ? $img_array[1] : '',
					'longitude' => (!empty($img_array[2]) && $request->type[$key] == $img_array[3]) ? $img_array[2] : ''
				);
				$attendance_id = DB::table('attendance')->insertGetId($insertArray);
				$i++;
				
				$this->maintain_history(Auth::user()->id, 'attendance', $attendance_id, 'create_attendance', json_encode($insertArray));
			}
			if($i > 0){				
				return redirect()->route("admin.attendance.index")->with('success', 'Attendance Updated Successfully');
			}
		}
		else{
			return redirect()->route("admin.attendance.index")->with('error', 'Something Went Wrong !');
		}
	
		
		/* if(is_array($request->time) && !empty($request->time))
		{
			$i = 0; 
			if (is_array($request->time) && !empty($request->time)) {
				$att_id = $request->att_id;
				$time = $request->time;
				$type = $request->type;
				foreach ($att_id as $key => $value) {
					// print_r($value); die;
					if(isset($time[$key])){
						$attendance_details = Attendance::find($key);
						$data = array();
						$data['time'] = $time[$key];
						$data['type'] = $type[$key];
						$attendance_details->update($data);
						$i++;
					}
					else{
						Attendance::where('id', $value)->delete();
					}
				}
			}
			
			if($i > 0){				
				return redirect()->route("admin.attendance.index")->with('success', 'Attendance Updated Successfully');
			}
			else{
				return redirect()->route("admin.attendance.index")->with('error', 'Something Went Wrong !');
			}
			
			
			
		}
		else {
            return redirect()->route("admin.task.index")->with('error', 'Something Went Wrong !');
        } */
    }

	
	//work gallery for
	public function show()
    {
		$allBranches = $this->allBranches();
		if(Input::get('search')==''){
	      $responseArray=array();
	      return view('admin.attendance.gallery', compact('responseArray','allBranches'));
	    }


		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$logged_type     = Auth::user()->department_type;
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
        $branch_id = Input::get('branch_id');
        $branch_location = Input::get('branch_location');
		
	
		
		$attendance = Attendance::with('user.user_branches.branch')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', date('Y-m-d'));
		}
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if(!empty($branch_location)){
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_location) { // orWhereHas dk				
				$q->where('branch_location', $branch_location);							
			});	
		}
		
		if($logged_role_id == 21){
			$attendance->WhereHas('user', function ($q) use ($login_id, $logged_type, $logged_role_id) {
                // $q->where('role_id', '=', $logged_role_id);
				if($login_id != 5525){					
					$q->where('department_type', '=', $logged_type);
				}
				$q->where('role_id', '!=', 1);
				$q->where('status', '=', 1);
				$q->where('id', '!=', $login_id);
				$q->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ');
            });
		}
		
		
		$responseArray = array();
		$attendance = $attendance->get();
		//echo '<pre>'; print_r($attendance);die;
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['emp_id'] = $valAtt->user->id;
				$responseArray[$key]['name'] = $valAtt->user->name;
				$responseArray[$key]['total_hours'] = "00";
				
				$all_time = array();
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				if(count($get_attendance) > 0){
					$all_time = $get_attendance;
				}
				$responseArray[$key]['time'] = $all_time;
			}
			
		}
		// echo "<pre>"; print_r($responseArray); die;
        return view('admin.attendance.gallery', compact('responseArray','allBranches'));
    }
	
    public function gallery()
    {
        
    }
    
	public function download_excel()
    {
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$logid               = array();
		$name            = Input::get('name');
		$fdate           = Input::get('fdate');
        $tdate           = Input::get('tdate');
		$department_type = Input::get('department_type');
        $branch_id       = Input::get('branch_id');
		$users           = NewTask::getEmployeeByLogID($logged_id,'attendance');
		$attendance = Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', date('Y-m-d'));
		}
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
            });
        } */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->whereRaw("department_type = '$department_type'");
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);

			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		
		$responseArray = array();
		$attendance = $attendance->get();
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				
				$branch = '';
				foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				}
				
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAtt->user->register_id)?$valAtt->user->register_id:'';
				$responseArray[$key]['emp_id'] = isset($valAtt->user->id)?$valAtt->user->id:'';
				$responseArray[$key]['name'] = isset($valAtt->user->name)?$valAtt->user->name:'';
				$responseArray[$key]['branch'] = isset($branch)?$branch:'';
				$responseArray[$key]['department'] = isset($valAtt->user->department->name)?$valAtt->user->department->name:'';
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] = $in_time;
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] = $in_time;
								$time_array[$ii]['out_time'] = "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] = $out_time;
								$ii++;
							}
						}
					}
				}
				$responseArray[$key]['time'] = $time_array;
			}
			// echo "<pre>"; print_r($responseArray); die;
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new AttendanceExport($responseArray), 'AttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function rpAttendanceDetail(Request $request){
		
    	$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$logid               = array();
        $users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
      
		$attendance          = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->whereRaw("department_type = '$department_type'");
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

		
		if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);

			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);

		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		
		
		
		
		$responseArray = array();
		$attendance = $attendance->get();
	   // echo '<pre>'; print_r($attendance);die;
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				
				$branch = '';
				foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				}
				
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAtt->user->register_id)?$valAtt->user->register_id:'';
				$responseArray[$key]['emp_id'] = isset($valAtt->user->id)?$valAtt->user->id:0;
				$responseArray[$key]['name'] = isset($valAtt->user->name)?$valAtt->user->name:'';
				$responseArray[$key]['branch']      = isset($branch)?$branch:'';
				$responseArray[$key]['department']  = isset($valAtt->user->department->name) ? $valAtt->user->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['out_time'] = "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
								$ii++;
							}
						}
						
						/* if(!empty($time_array[$ii]['in_time']) && !empty($time_array[$ii]['out_time'])){
							$intime = new DateTime($$time_array[$ii]['in_time']);
							$outtime = new DateTime($$time_array[$ii]['out_time']);
							$interval = $intime->diff($outtime);
							$hours = $interval->format('%H');
							$minute = $interval->format('%I');
							$total_minute += ($hours*60)+$minute;
						} */
						$j = 1;
						
						if(count($time_array) > 0){
							foreach($time_array as $time_array_value){ 
								if(count($time_array) == $j){
									if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
										$intime = new DateTime($time_array_value['in_time']); 
										$outtime = new DateTime($time_array_value['out_time']); 
										$interval = $intime->diff($outtime);
										$hours = $interval->format('%H');
										$minute = $interval->format('%I');
										$total_minute += ($hours*60)+$minute;
									}	
								}
								$j++;
							}
						}
					}
				}
				
				/*
				$total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				*/
				
				$responseArray[$key]['total_hours'] = $total_minute ." Minutes";
				$responseArray[$key]['time'] = $time_array;
				
				
				//Attadance Type //Chetan
				$totalMin	=	($total_minute*100)/540;				
				if($totalMin > 75){
					$attendanceType = "P";
				}else if($totalMin > 50){
					$attendanceType = "H";
				}else{
					$attendanceType = "A";
				}
				
				$responseArray[$key]['attendance_type'] = $attendanceType;
			}
		} 
		
		
		return DataTables::of($responseArray)->make(true);
	}
	
	public function rpattendance(Request $request){ 
    	$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.rpattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	public function new_download_excel(Request $request){
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$logid               = array();
        $users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		$attendance          = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->whereRaw("department_type = '$department_type'");
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

		
		if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);

			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		
		
		
		
		$responseArray = array();
		$attendance = $attendance->get();
	    //echo '<pre>'; print_r($attendance);die;
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				
				$branch = '';
				foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				}
				
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAtt->user->register_id)?$valAtt->user->register_id:'';
				$responseArray[$key]['emp_id'] = isset($valAtt->user->id)?$valAtt->user->id:0;
				$responseArray[$key]['name'] = isset($valAtt->user->name)?$valAtt->user->name:'';
				$responseArray[$key]['branch'] = isset($branch)?$branch:'';
				$responseArray[$key]['department'] = isset($valAtt->user->department->name)?$valAtt->user->department->name:'';
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['out_time'] = "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
								$ii++;
							}
						}
						
						if(!empty($time_array[$ii]['in_time']) && !empty($time_array[$ii]['out_time'])){
							$intime = new DateTime($$time_array[$ii]['in_time']);
							$outtime = new DateTime($$time_array[$ii]['out_time']);
							$interval = $intime->diff($outtime);
							$hours = $interval->format('%H');
							$minute = $interval->format('%I');
							$total_minute += ($hours*60)+$minute;
						}
					}
				}
				$total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				$responseArray[$key]['total_hours'] = $total_hours ." Hour";
				$responseArray[$key]['time'] = $time_array;
			}
		} 
		//echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new NewAttendanceExport($responseArray), 'RPAttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function rpnotpresentattendance(Request $request){ 
    	$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.rpnotattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	public function rpNotPresentAttendanceDetail(Request $request){
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        //$tdate               = $request->tdate;
		$logid               = array();
        $users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		$usr          = User::where('status', 1);
		
		
		$whereCond    = 'AND 1=1 ';
		$whereCondAtt = 'AND 1=1 ';
		if(!empty($name) && !empty($department_type)){
			$whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$whereCond .= " AND (users.department_type = '$department_type')";
		}
		
		
		if(!empty($branch_id)){
			$whereCond .= " AND (userdetails.branch_id = '$branch_id')";
		}
				
				
		if(!empty($fdate)){
			$whereCondAtt .= ' AND attendance_new.date = "'.$fdate.'"';
			//$whereCondAtt .= ' AND attendance_new.date <= "'.$tdate.'"';
		}
		else{
			$whereCondAtt .= ' AND attendance_new.date = "'.date("Y-m-d").'"';
		}
		
		$employeeArray= array();
		$usr= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray= explode(',',$usr);
		
		$whereCond .= " AND (users.id IN ($usr))";

		$attendance = DB::select("SELECT users.*, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance_new WHERE attendance_new.emp_id=users.id $whereCondAtt) AS present FROM users LEFT JOIN userdetails ON users.id = userdetails.user_id  LEFT JOIN branches ON userdetails.branch_id = branches.id LEFT JOIN departments ON users.department_type = departments.id  WHERE users.status = 1 $whereCond");
		
		$responseArray = array();
	    //echo '<pre>'; print_r($attendance);die;
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				if($valAtt->present == 0){
					$responseArray[$key]['name']   = $valAtt->name;
					$responseArray[$key]['branch_name']      = $valAtt->branch_name ? $valAtt->branch_name : '';
					$responseArray[$key]['departments_name'] = $valAtt->departments_name ? $valAtt->departments_name : '';
					$responseArray[$key]['email']  = $valAtt->email;
					$responseArray[$key]['mobile'] = $valAtt->mobile;
				}
			}
				
		} 
		
		//echo '<pre>'; print_r("SELECT users.*, (SELECT COUNT(*) FROM attendance_new WHERE attendance_new.emp_id=users.id $whereCondAtt) AS present FROM users WHERE status = 1 $whereCond");die;
		return DataTables::of($responseArray)->make(true);
	}
	
	public function new_not_attdence_download_excel(Request $request){ 
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        //$tdate               = $request->tdate;
		$logid               = array();
        $users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		$usr          = User::where('status', 1);
		
		
		$whereCond    = 'AND 1=1 ';
		$whereCondAtt = 'AND 1=1 ';
		if(!empty($name) && !empty($department_type)){
			$whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$whereCond .= " AND (users.department_type = '$department_type')";
		}
		
		
		if(!empty($branch_id)){
			$whereCond .= " AND (userdetails.branch_id = '$branch_id')";
		}
				
				
		if(!empty($fdate)){
			$whereCondAtt .= ' AND attendance_new.date = "'.$fdate.'"';
			//$whereCondAtt .= ' AND attendance_new.date <= "'.$tdate.'"';
		}
		else{
			$whereCondAtt .= ' AND attendance_new.date = "'.date("Y-m-d").'"';
		}
		
		$employeeArray   = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		
		$whereCond .= " AND (users.id IN ($usr))";
		
		$attendance = DB::select("SELECT users.*, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance_new WHERE attendance_new.emp_id=users.id $whereCondAtt) AS present FROM users LEFT JOIN userdetails ON users.id = userdetails.user_id  LEFT JOIN branches ON userdetails.branch_id = branches.id LEFT JOIN departments ON users.department_type = departments.id  WHERE users.status = 1 $whereCond");
		
		$responseArray = array();
	   //echo '<pre>'; print_r($attendance);die;
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				if($valAtt->present == 0){
					$responseArray[$key]['register_id']   = $valAtt->register_id;
					$responseArray[$key]['name']   = $valAtt->name;
					$responseArray[$key]['branch_name']      = $valAtt->branch_name ? $valAtt->branch_name : '';
					$responseArray[$key]['departments_name'] = $valAtt->departments_name ? $valAtt->departments_name : '';
					$responseArray[$key]['email']  = $valAtt->email;
					$responseArray[$key]['mobile'] = $valAtt->mobile;
				}
			}
				
		} 
        if(count($responseArray) > 0){
            return Excel::download(new NewAbsentAttendanceExport($responseArray), 'RPAbsentData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function attendanceDetail(Request $request){
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$logid               = array();
        //$users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
		 $users               = NewTask::getEmployeeByLogID($logged_id,'approved-emp');
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches         = $this->allBranches();
        $name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$attendance = Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
            });
        } */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->whereRaw("department_type = '$department_type'");
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

		
		if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);

			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		}
		
		$responseArray = array();
		$attendance = $attendance->get();
		
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				
				$branch = '';
				foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				}
								
				$responseArray[$key]['date']        = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAtt->user->register_id)?$valAtt->user->register_id:'';
				$responseArray[$key]['emp_id']      = isset($valAtt->user->id)?$valAtt->user->id:0;
				$responseArray[$key]['name']        = isset($valAtt->user->name)?$valAtt->user->name:'';
				$responseArray[$key]['branch']      = isset($branch)?$branch:'';
				$responseArray[$key]['department']  = isset($valAtt->user->department->name) ? $valAtt->user->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_location = "";
						$out_location = "";
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
							$time_array[$ii]['in_location'] =  "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
							$time_array[$ii]['out_location'] =  "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['out_time'] = "";
								$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
								$time_array[$ii]['out_location'] =  "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
								$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
								$ii++;
							}
						}
						
						/*  if(!empty($time_array[$ii]['in_time']) && !empty($time_array[$ii]['out_time'])){
							$intime = new DateTime($time_array[$ii]['in_time']);
							$outtime = new DateTime($time_array[$ii]['out_time']);
							$interval = $intime->diff($outtime);  
							$hours = $interval->format('%H');
							$minute = $interval->format('%I');
							$total_minute += ($hours*60)+$minute;
						} */ 
						
						/* if(count($time_array) > 0){
							foreach($time_array as $time_array_value){
								if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
								$intime = new DateTime($time_array_value['in_time']);
								$outtime = new DateTime($time_array_value['out_time']);
								$interval = $intime->diff($outtime);
								$hours = $interval->format('%H');
								$minute = $interval->format('%I');
								$total_minute += ($hours*60)+$minute;
								}
								
							}
						//	echo "<pre>"; print_r($total_minute);
						} */
						
						$j = 1;
						
						if(count($time_array) > 0){
							foreach($time_array as $time_array_value){ 
								if(count($time_array) == $j){
									if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
										$intime = new DateTime($time_array_value['in_time']); 
										$outtime = new DateTime($time_array_value['out_time']); 
										$interval = $intime->diff($outtime);
										$hours = $interval->format('%H');
										$minute = $interval->format('%I');
										$total_minute += ($hours*60)+$minute;
									}	
								}
								$j++;
							}
						}
						
					}
				}
				
				/*
				$total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				*/
				
				$responseArray[$key]['total_hours'] = $total_minute ." Minutes";
				$responseArray[$key]['time'] = $time_array;
				
				//Attadance Type
				$totalMin	=	($total_minute*100)/540;				
				if($totalMin > 75){
					$attendanceType = "P";
				}else if($totalMin > 50){
					$attendanceType = "H";
				}else{
					$attendanceType = "A";
				}
				
				$responseArray[$key]['attendance_type'] = $attendanceType;
			}
		} 
		
		//echo '<pre>'; print_r($responseArray);die;
		return DataTables::of($responseArray)->make(true);
	}
	
	public function fullattendence_old(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.fullattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	public function fullAttendanceDetail(Request $request){ //echo '<pre>'; print_r($request->name);die;


		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
        $last_location       = $request->last_location;
		$logid               = array();
        $users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		//$attendance= AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		$attendance= Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendance->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendance->whereRaw("location like '%$last_location%'");
		}
		 
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		// if($logged_role_id->role_id==24 || $logged_role_id->role_id==29){ 
			// $attendance->WhereHas('user.supervisor_id', function ($q) use ($supervisor_id) {
				// $q->where('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
			// });
		// }
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);
			// $employeeArray = array();
			// $usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			// $employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		} */
		
		
		$attendance = $attendance->selectRaw("id,emp_id,date,reason,'App' as table_name");
		
		
		//$attendance = $attendance->get();
		$attendancenew      = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendancenew->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendancenew->where('date', '>=', $fdate);
			$attendancenew->where('date', '<=', $tdate);
		}
		else{
			$attendancenew->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendancenew->whereRaw("location like '%$last_location%'");
		}
		
		/* if (!empty($name) || !empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendancenew->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		} 
		
		if(!empty($branch_id)) {
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendancenew->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			$attendancenew->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendancenew->whereIn('emp_id', $employeeArray);
		} */
		
		$attendancenew = $attendancenew->selectRaw("id,emp_id,date,reason,'RFID' as table_name");
		
		
		$comman = $attendancenew->union($attendance);
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')
						   ->get();
				
	   // echo '<pre>'; print_r($comman_result);die;
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 
			    $valAttRes = User::with('user_branches.branch','department')->where('id',$valAtt->emp_id)->first();
				//echo '<pre>'; print_r($valAttRes->department->name);die;
				
				$branch = '';
				/* foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				} */
				
				//echo '<pre>'; print_r($valAtt->id);die;
				
				
				$responseArray[$key]['table_name'] = $valAtt->table_name;
				$responseArray[$key]['id'] = $valAtt->id;				
				$responseArray[$key]['date'] = date('d-m-Y',strtotime($valAtt->date));
				$responseArray[$key]['edit_date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
				$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
				$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
				$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
				$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				
				/* $get_attendance_comman_result     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc');
				
				$get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc');
				$get_attendance = $get_attendance_comman_result->union($get_u_attendance_new)->get(); */
				
				
				/* $get_attendance_comman_result = DB::table(DB::raw("({$get_attendance->toSql()}) as get_attendance"))
						   ->mergeBindings($get_attendance->getQuery())
						   ->groupBy('get_attendance.emp_id')
						   ->get(); */
				 $get_attendance= array();		 
				 // $get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('time', 'asc')->get();				 
				 $get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->get();				 
				 
				 if(count($get_u_attendance_new) > 0){
					 $get_attendance = $get_u_attendance_new;
				 }
				 else{
					 $get_u_attendance     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
					 $get_attendance  = $get_u_attendance;
				 }
				// echo '<pre>'; print_r($get_attendance);die;
				//echo '<pre>'; print_r($get_attendance_comman_result);die;
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				$last_location = "";
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_location = "";
						$out_location = "";
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
							$time_array[$ii]['in_location'] =  "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
							$time_array[$ii]['out_location'] =  "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
								$last_location = $AttendanceDetail->location;
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
								$time_array[$ii]['out_time'] = "";
								$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
								$time_array[$ii]['out_location'] =  "";
								$last_location = $AttendanceDetail->location;
							}
						}
						else if($AttendanceDetail->type=="Out"){							
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
								$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
								$last_location = $AttendanceDetail->location;
								$ii++;
							}
						}
						
						/* if(!empty($time_array[$ii]['in_time']) && !empty($time_array[$ii]['out_time'])){
							$intime = new DateTime($$time_array[$ii]['in_time']);
							$outtime = new DateTime($$time_array[$ii]['out_time']);
							$interval = $intime->diff($outtime);
							$hours = $interval->format('%H');
							$minute = $interval->format('%I');
							$total_minute += ($hours*60)+$minute;
						} */
						
						$j = 1;
						
						if(count($time_array) > 0){
							foreach($time_array as $time_array_value){ 
								if(count($time_array) == $j){
									if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
										$intime = new DateTime($time_array_value['in_time']); 
										$outtime = new DateTime($time_array_value['out_time']); 
										$interval = $intime->diff($outtime);
										$hours = $interval->format('%H');
										$minute = $interval->format('%I');
										$total_minute += ($hours*60)+$minute;
									}	
								}
								$j++;
							}
						}
						
						if($AttendanceDetail->for_reason=="1"){
							$forReason  = "In";
						}else if($AttendanceDetail->for_reason=="2"){
							$forReason  = "Out";
						}else{							
							$forReason  = "Both";
						}
						
						$userReason =	$AttendanceDetail->reason;
						
					}
				}
				
				/*
				$total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				*/
				
				if(!empty($userReason)){
					$responseArray[$key]['reason'] = $userReason.' ('.$forReason.')';
				}else{
					$responseArray[$key]['reason'] = '';
				}
				
				$responseArray[$key]['total_hours'] = $total_minute ." Minutes";
				$responseArray[$key]['last_location'] = $last_location;
				$responseArray[$key]['time'] = $time_array;
				
				
				$check_holiday  = Holiday::select('type','branch_id')->whereRaw("DATE(date) = '$valAtt->date'")->where('status', '1')->where('is_deleted', '0')->first();
				
				
				// if(!empty($check_holiday)){
					// $holiday_branch = json_decode($check_holiday->branch_id); 
					// $is_optional 	= false;
					// if($check_holiday->type=="Optional"){
						// $is_optional = true;
					// }

					// if($total_minute >= 66.66){
						// $branch_id = $valAttRes->user_branches[0]->branch->id;	 	
						// if(!empty($branch_id) && !empty($holiday_branch) && in_array($branch_id, $holiday_branch)){
							// $attendanceType = 'HW';

						// }else{
							// $attendanceType = 'P';
						// }								
					// }
					// else{
						// if(!empty($branch_id) && !empty($holiday_branch) && in_array($branch_id, $holiday_branch)){
							// $attendanceType = 'H';
						// }
						// else{
							// $attendanceType = 'A';
						// }
						
					// }
				// }
				if(!empty($check_holiday->branch_id)){
					$holiday_branch = json_decode($check_holiday->branch_id); 
					$is_optional 	= false;
					if($check_holiday->type=="Optional"){
						$is_optional = true;
					}
					$totalMin	=	($total_minute*100)/$valAttRes->total_time;
					if($totalMin <= 22.22){
						if(!empty($branch_id) && !empty($holiday_branch) && in_array($branch_id, $holiday_branch)){
							$attendanceType = 'H';
						}
						else{
							$attendanceType = 'A';
						}						
					}
					else if($totalMin <= 66.66){
						$branch_id = $valAttRes->user_branches[0]->branch->id;	 	
						if(!empty($branch_id) && !empty($holiday_branch) && in_array($branch_id, $holiday_branch)){
							$attendanceType = 'HW';

						}else{
							$attendanceType = 'PH';
						}
					}
					else{
						
						$branch_id = $valAttRes->user_branches[0]->branch->id;	 	
						if(!empty($branch_id) && !empty($holiday_branch) && in_array($branch_id, $holiday_branch)){
							$attendanceType = 'HW';

						}else{
							$attendanceType = 'P';
						}	
						
					}
				}
				else{
					//Attadance Type //Chetan
					//$totalMin	=	($total_minute*100)/540;				
					$totalMin	=	($total_minute*100)/$valAttRes->total_time;
					if($totalMin <= 22.22){
						$attendanceType = "A";
					}
					else if($totalMin <= 66.66){
						$attendanceType = "PH";
					}
					else{
						$attendanceType = "P";
					}
				}
				
				$responseArray[$key]['attendance_type'] = $attendanceType;
				
			}
		}
		
		//$u_attendance = DB::table("attendance")->select('attendance.emp_id','attendance.date','attendance.time','attendance.type')->where('date', '2021-05-01')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		//$u_new_attendance = DB::table("attendance_new")->select('attendance_new.emp_id','attendance_new.date','attendance_new.time','attendance_new.type')->where('date', '2021-05-01')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id')->union($u_attendance)->get();
		
		//$attendane = DB::select("SELECT * FROM (SELECT * FROM attendance WHERE date='2021-05-01' GROUP BY date Order BY date DESC) as X UNION SELECT * FROM attendance_new WHERE date='2021-05-01' GROUP BY date Order BY date DESC");
		
		//$attendane = DB::select("SELECT * FROM (SELECT * FROM attendance WHERE date='2021-05-01' GROUP BY date Order BY date DESC) as X UNION SELECT * FROM attendance_new WHERE date='2021-05-01' GROUP BY date Order BY date DESC");
		
		
		// echo '<pre>'; print_r($responseArray);die;
		return DataTables::of($responseArray)->make(true);
	}
	
	public function full_download_excel(Request $request){
		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$last_location       = $request->last_location;
		$logid               = array();
        $users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		//$attendance= AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		$attendance= Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendance->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendance->whereRaw("location like '%$last_location%'");
		}
		 
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);
			// $employeeArray = array();
			// $usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			// $employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		} */
		
		
		$attendance = $attendance->selectRaw("id,emp_id,date,'App' as table_name");
		
		
		//$attendance = $attendance->get();
		$attendancenew      = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendancenew->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendancenew->where('date', '>=', $fdate);
			$attendancenew->where('date', '<=', $tdate);
		}
		else{
			$attendancenew->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendancenew->whereRaw("location like '%$last_location%'");
		}
		
		/* if (!empty($name) || !empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendancenew->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		} 
		
		if(!empty($branch_id)) {
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendancenew->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			$attendancenew->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendancenew->whereIn('emp_id', $employeeArray);
		} */
		
		$attendancenew = $attendancenew->selectRaw("id,emp_id,date,'RFID' as table_name");
		
		
		$comman = $attendancenew->union($attendance);
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')
						   ->get();
				
	
		$responseArray = array();
		//echo '<pre>'; print_r($comman_result);die;
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 
			    $valAttRes = User::with('user_details.branch','department')->where('id',$valAtt->emp_id)->first();
				
				
				$branch = '';
				$responseArray[$key]['table_name'] = $valAtt->table_name;
				$responseArray[$key]['id'] = $valAtt->id;				
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
				$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
				$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
				//$responseArray[$key]['branch']      = isset($valAttRes->user_details->branch->name)?$valAttRes->user_details->branch->name:'';
				$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
				$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
				$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				
				 /* $get_attendance= array();		   
			 
				 $get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();				 
				 
				 if(count($get_u_attendance_new) > 0){
					 $get_attendance = $get_u_attendance_new;
				 }
				 else{
					 $get_u_attendance     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
					 $get_attendance  = $get_u_attendance;
				 } */
				 
				$get_attendance_comman_result     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->selectRaw("id,emp_id,date,reason,'App' as table_name,time,location,for_reason");
				
				$get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->selectRaw("id,emp_id,date,reason,'RFID' as table_name,time,location,for_reason");
				
				$comman_in = $get_u_attendance_new->union($get_attendance_comman_result);
				$get_attendance = DB::table(DB::raw("({$comman_in->toSql()}) as comman_in"))
								->mergeBindings($comman_in->getQuery())
								->orderBy('comman_in.time', 'asc')
								->get();
				// echo "<pre>"; print_R($get_attendance); die;
				$fetchData = array();				
				if(count($get_attendance)>0){
					if(count($get_attendance) == 1){
						$fetchData[] = $get_attendance[0];
					}
					else{
						$fetchData[] = $get_attendance[0];
						$fetchData[] = $get_attendance[count($get_attendance)-1];
					}
					
				}
				
				
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				$last_location = "";
				if(count($fetchData) > 0){
					$in_time = "";
					$out_time = "";
					foreach($fetchData as $key1 => $AttendanceDetail){
						
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
							$time_array[$ii]['in_location'] =  "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
							$time_array[$ii]['out_location'] =  "";
						}
						if($key1==0){ // First In 
							$in_time = $AttendanceDetail->time;
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
						}
						else{ // Last Out					
							$out_time = $AttendanceDetail->time;
							$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
							$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
							$ii++;
						}
						
						
					
					}
				}
				
				$responseArray[$key]['last_location'] = $last_location;
				$responseArray[$key]['time'] = $time_array;
			}
		}
		
		//echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new FullAttendanceExport($responseArray), 'FullAttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function absentfullattendence(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.absentfullattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	public function absentFullAttendanceDetail(Request $request){		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$logid               = array();
        //$users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        $users= NewTask::getEmployeeByLogID($logged_id,'approved-emp');
		// $usr          = User::where('status', 1);
		
		
		$new_whereCond    = 'users.status = 1 ';
		$new_whereCondAtt = '';
		$whereCondAtt = '';
		if(!empty($name) && !empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$new_whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type')";
		}
		if(!empty($branch_id)){
			$new_whereCond .= " AND (userbranches.branch_id = '$branch_id')";
		}
		/* if(!empty($fdate)){
			$new_whereCondAtt .= ' AND attendance_new.date >= "'.$fdate.'" AND attendance_new.date <= "'.$tdate.'"';
			$whereCondAtt .= ' AND attendance.date = "'.$fdate.'" AND attendance.date = "'.$tdate.'"';
		}
		else{
			$today = date("Y-m-d");
			$new_whereCondAtt .= ' AND attendance_new.date >= "'.$today.'" AND attendance_new.date <= "'.$today.'"';
			$whereCondAtt .= ' AND attendance.date >= "'.$today.'" AND attendance.date <= "'.$today.'"';
		} */
		
		$employeeArray= array();
		$usr= substr($logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users)), 0, -1);
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name,userdetails.degination as designation_name"));
		// $attendance->select(\DB::raw("users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance WHERE attendance.emp_id=users.id $whereCondAtt) AS present"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('userbranches', 'userdetails.user_id', '=', 'userbranches.user_id');
		$attendance->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
		$attendance->groupBy('users.id');
		$array1 = $attendance->get();
		$comman_result = array();
		if(count($array1) > 0){
			foreach($array1 as $val){
				$user_id = $val->id;
				$fdate  = $request->fdate;
				$tdate  = $request->tdate;
				if(!empty($fdate) && !empty($tdate)){
					
				}
				else{
					$fdate = date("Y-m-d");
					$tdate = date("Y-m-d");
					
				}
				$user_array = array();
				$user_array_new = array();
				$sts = '';
				while (strtotime($fdate) <= strtotime($tdate)) { 
					
					$check_attendance = DB::table('attendance')
							->select('attendance.*')
							->where('attendance.emp_id', $user_id)
							->where('attendance.date', $fdate)
							->get();
							
					$check_attendance_new = DB::table('attendance_new')
							->select('attendance_new.*')
							->where('attendance_new.emp_id', $user_id)
							->where('attendance_new.date', $fdate)
							->get();
					$sun = strtolower(date("l", strtotime($fdate)));		
					if(count($check_attendance) == 0 && count($check_attendance_new) == 0 && $sun != 'sunday'){
						
						$check_leave = LeaveDetail::where("emp_id", $user_id)->where("date", $fdate)->first();
						if(!empty($check_leave)){
							if($check_leave->status == 'Approved'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Approved';
							}
							if($check_leave->status == 'Rejected'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Rejected';
							}
							if($check_leave->status == 'Deleted'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Deleted';
							}
							if($check_leave->status == 'Pending'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Pending';
							}
						}
						else{
							$sts = 'Leave Not applied';
						}
						
						$user_array['id'] = $user_id;
						$user_array['name'] = $val->name;
						$user_array['email'] = $val->email;
						$user_array['mobile'] = $val->mobile;
						$user_array['branch_name'] = $val->branch_name;
						$user_array['departments_name'] = $val->departments_name;
						$user_array['designation_name'] = $val->designation_name;
						$user_array['date'] = $fdate;
						$user_array['status'] = $sts;
						$comman_result[] = $user_array;
					}
					
					
					
					
					$fdate = date ("Y-m-d", strtotime("+1 day", strtotime($fdate)));
				}
				
				
			}
		}
		
		// print_r($comman_result); die;
		
		/* $attendancenew = User::query();
		$attendancenew->select(\DB::raw("users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance_new WHERE attendance_new.emp_id=users.id $new_whereCondAtt) AS present"));
		$attendancenew->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendancenew->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		$attendancenew->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendancenew->whereRaw($new_whereCond);
		$array2 = $attendancenew->get();
		
		$array1 = json_decode(json_encode($array1), true);
		$array2 = json_decode(json_encode($array2), true);
		$comman_result = array();
		$array2 = (array)$array2;
		foreach ($array1 as $key1 => $value1) {
			foreach ($array2 as $key2 => $value2) {
				if ($value1['id']==$value2['id']) {
					if($value1['present']>0){
						
					}
					else if($value2['present']>0){
						
					}
					else{
						$comman_result[]=$value2+$value1;
					}
				}
			}
		} */
		
		$responseArray = array();
		 
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){
				$responseArray[$key]['name']   = $valAtt['name'];
				$responseArray[$key]['branch_name']      = $valAtt['branch_name'] ? $valAtt['branch_name'] : '';
				$responseArray[$key]['departments_name'] = $valAtt['departments_name'] ? $valAtt['departments_name'] : '';
				$responseArray[$key]['designation_name']  = $valAtt['designation_name'];
				$responseArray[$key]['mobile'] = $valAtt['mobile'];
				$responseArray[$key]['date'] = date('d-m-Y',strtotime($valAtt['date']));
				$responseArray[$key]['status'] = $valAtt['status'];
			}
				
		}
		
		return DataTables::of($responseArray)->make(true);

		
	}
	
	
	public function absent_full_download_excel(Request $request){
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$logid               = array();
        $users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		// $usr          = User::where('status', 1);
		
		
		$new_whereCond    = 'users.status = 1 ';
		$new_whereCondAtt = '';
		$whereCondAtt = '';
		if(!empty($name) && !empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$new_whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type')";
		}
		if(!empty($branch_id)){
			//$new_whereCond .= " AND (userdetails.branch_id = '$branch_id')";
			$new_whereCond .= " AND (userbranches.branch_id = '$branch_id')";
		}
		
		$employeeArray= array();
		$usr= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.register_id,users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name,userdetails.degination as designation_name"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('userbranches', 'userdetails.user_id', '=', 'userbranches.user_id');
		$attendance->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
		$attendance->groupBy('users.id');
		$array1 = $attendance->get();
		$comman_result = array();
		if(count($array1) > 0){
			foreach($array1 as $val){
				$user_id = $val->id;
				$fdate  = $request->fdate;
				$tdate  = $request->tdate;
				if(!empty($fdate) && !empty($tdate)){
					
				}
				else{
					$fdate = date("Y-m-d");
					$tdate = date("Y-m-d");
					
				}
				$user_array = array();
				$user_array_new = array();
				while (strtotime($fdate) <= strtotime($tdate)) {
					
					$check_attendance = DB::table('attendance')
							->select('attendance.*')
							->where('attendance.emp_id', $user_id)
							->where('attendance.date', $fdate)
							->get();
							
					$check_attendance_new = DB::table('attendance_new')
							->select('attendance_new.*')
							->where('attendance_new.emp_id', $user_id)
							->where('attendance_new.date', $fdate)
							->get();
					$sun = strtolower(date("l", strtotime($fdate)));		
					if(count($check_attendance) == 0 && count($check_attendance_new) == 0 && $sun != 'sunday'){	

						$check_leave = LeaveDetail::where("emp_id", $user_id)->where("date", $fdate)->first();
						if(!empty($check_leave)){
							if($check_leave->status == 'Approved'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Approved';
							}
							if($check_leave->status == 'Rejected'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Rejected';
							}
							if($check_leave->status == 'Deleted'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Deleted';
							}
							if($check_leave->status == 'Pending'){
								$sts = 'Leave Applied on '.date("d-m-Y", strtotime($fdate)).' and status is Pending';
							}
						}
						else{
							$sts = 'Leave Not applied';
						}
						
						$user_array['id'] = $user_id;
						$user_array['register_id'] = $val->register_id;
						$user_array['name'] = $val->name;
						$user_array['email'] = $val->email;
						$user_array['mobile'] = $val->mobile;
						$user_array['branch_name'] = $val->branch_name;
						$user_array['departments_name'] = $val->departments_name;
						$user_array['designation_name'] = $val->designation_name;
						$user_array['date'] = $fdate;
						$user_array['status'] = $sts;
						$comman_result[] = $user_array;
					}
					
					
					
					
					$fdate = date ("Y-m-d", strtotime("+1 day", strtotime($fdate)));
				}
				
				
			}
		}
		
		$responseArray = array();
		 
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){
				$responseArray[$key]['name']   = $valAtt['name'];
				$responseArray[$key]['register_id']   = $valAtt['register_id'];
				$responseArray[$key]['branch_name']      = $valAtt['branch_name'] ? $valAtt['branch_name'] : '';
				$responseArray[$key]['departments_name'] = $valAtt['departments_name'] ? $valAtt['departments_name'] : '';
				$responseArray[$key]['designation_name']  = $valAtt['designation_name'];
				$responseArray[$key]['mobile'] = $valAtt['mobile'];
				$responseArray[$key]['date'] = date('d-m-Y',strtotime($valAtt['date']));
				$responseArray[$key]['status'] = $valAtt['status'];
			}
				
		}
		 
		
		
		// echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new AbsentFullAttendanceExport($responseArray), 'AbsentFullAttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function fullAttendanceEdit($emp_id, $date, $tbl){
		$login_id        = Auth::user()->id;
		if($login_id == 901 || $login_id == 5344){
			
		}
		else{
			//echo "<b style='color:red;'>Note - Not access you from today.</b>"; die;
		}
		$modl = '';
		if($tbl == 'App'){
			$modl = "App\Attendance";
		}
		else if($tbl == 'RFID'){
			$modl = "App\AttendanceNew";
		}
		
		
		$attendance = $modl::with('user');
		if(!empty($emp_id)){
			$attendance->where('emp_id', $emp_id);
		}
		if(!empty($date)){
			$attendance->where('date', $date);
		}
		
		$attendance = $attendance->first();
		
		$attendance_data = array();
		if(!empty($attendance)){
				$attendance_data['date'] = $attendance->date;
				$attendance_data['emp_id'] = $attendance->user->id;
				$attendance_data['name'] = $attendance->user->name;
				$attendance_data['total_hours'] = "00";
				$attendance_data['location'] = $attendance->location;
				$attendance_data['reason'] = $attendance->reason;
				$attendance_data['for_reason'] = $attendance->for_reason;
				
				
				$all_time = array();
				$get_attendance = $modl::where('emp_id', $attendance->emp_id)->where('date', $attendance->date)->orderBy('id', 'asc')->get();
				if(count($get_attendance) > 0){
					$all_time = $get_attendance;
				}
				$attendance_data['time'] = $all_time;
		}
		//echo '<pre>'; print_r($attendance_data);die;
        return view('admin.attendance.edit-full-attendance', compact('attendance_data','modl'));
	}
	
	public function updateFullAttendence(Request $request){
		//echo '<pre>'; print_r($request->post());die;
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$dates  = $request->date;
		$mont_year = date('Y-m',strtotime($dates));
		if($logged_role_id != 24 && $logged_role_id!=29){
			$alock = AttendanceLock::where('month', $mont_year)->first();
			if(!empty($alock)){
				if($alock->status=='1'){
					return redirect()->back()->with('error', 'Attendance is locked.');
				}
			}
		}
		
		if(!empty($request->time)){
			$i = 0;
			$img_array = array();
			if(!empty($request->att_id)){
				$exp_id = array_filter(explode(",", $request->att_id));
				if(count($exp_id) > 0){
					foreach($exp_id as $exp_id_val){
						$att_img = $request->tbl::where('id', $exp_id_val)->first();
						array_push($img_array, $att_img->image, $att_img->latitude, $att_img->longitude);
						
						$exp_id_val_res = [$exp_id_val];
						$this->maintain_history(Auth::user()->id, $request->tbl, $exp_id_val, 'delete_'.$request->tbl, json_encode($exp_id_val_res));
					}
				 
					$request->tbl::whereIn('id', $exp_id)->delete();
				}
			}
			//echo '<pre>'; print_r($img_array);die;
			foreach($request->time as $key => $timeVal){ 
				$for_reason_val = '';$for_reason_val2 = '';
				if($request->for_reason == '1' && $request->type[$key] == 'In'){
					$for_reason_val = $request->reason;
					$for_reason_val2 = $request->for_reason;
				}
				else if($request->for_reason == '2' && $request->type[$key] == 'Out'){
					$for_reason_val = $request->reason;
					$for_reason_val2 = $request->for_reason;
				}
				else if($request->for_reason == '0'){
					$for_reason_val = $request->reason;
					$for_reason_val2 = $request->for_reason;
				}
							
				$insertArray = array(
								'emp_id' =>  $request->emp_id,
								'date'   =>  $request->date,
								'time' =>  $timeVal,
								'type' =>  $request->type[$key],
								'image' => !empty($img_array[0]) ? $img_array[0] : '',
								'latitude' => !empty($img_array[1]) ? $img_array[1] : '',
								'longitude' => !empty($img_array[1]) ? $img_array[1] : '',
								'reason' =>  $for_reason_val,
								'for_reason' => $for_reason_val2,
								'location' =>  $request->location,
								'updated_by' =>  $login_id,

							);

				$insert_id = $request->tbl::insertGetId($insertArray);			
				$i++;
				
				$this->maintain_history(Auth::user()->id, $request->tbl, $insert_id, 'insert_'.$request->tbl, json_encode($insertArray));
			}
			if($i > 0){				
				//return redirect()->route("admin.attendance.fullattendence")->with('success', 'Full Attendance Updated Successfully');
			    return redirect()->back()->with('success', 'Full Attendance Updated Successfully');
			}
		}
		else{
			//return redirect()->route("admin.attendance.fullattendence")->with('error', 'Something Went Wrong !');
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	
	public function update_manual_emp_attendance_notification(){ 
	
		die('qqq');
		$file_name = asset('laravel/public/attendance-noti.csv');
		
		$ext  = pathinfo($file_name, PATHINFO_EXTENSION);
		if($ext=='csv'){
			$file     = fopen($file_name, "r");
			$jj       = 0;

			while(($filesop=fgetcsv($file, 10000, ",")) !== FALSE) {
				// echo '<pre>'; print_r($filesop);die;
				$jj++;
				if($jj!=1){  
					// echo '<pre>'; print_r($filesop);die;
					
					$emp_id    = $filesop[0];
					if(!empty($emp_id)){
					$check_record = DB::table('users')->where('register_id',$emp_id)->first();
					if(!empty($check_record)){
						$emp_id = $check_record->id;
						// echo $emp_id; die;
						// $emp_id = "5453"; 
						// echo $emp_id; die;
						$attendance  = $filesop[1];
						// echo $emp_id .'/'. $attendance; die;
						if(!empty($emp_id)){
							// $user = User::where('is_deleted', '0')->whereRaw("(register_id LIKE '%$emp_id%')")->first();
							if(!empty($emp_id)){
								// $msg = "विशेष सूचना- आपके अगस्त माह मे कुल (  ) दिनों की उपस्थिति दर्ज की गई हैं इसमें अगर किसी प्रकार की त्रुटि हो तो आप कल (02-09-2022) सुबह 11 बजे से पहले नीचे दिए HR team के व्हाट्सप्प नंबर पर व्हाट्सप्प करके HRMS app में update करवाए। कृपया कॉल नहीं करे। जयपुर के लिए- 8905985547(गौरव) और प्रयागराज,दिल्ली,नेहल टॉवर, विनोद टॉवर, उत्कर्ष भवन के लिए - 8279246623(गौरव) व जोधपुर के अन्य सेंटर के लिए 9166456008(यश)। धन्यवाद उत्कर्ष टीम";
							
								$msg = "उपस्थिति संबंधित सूचना-

आपके अप्रैल माह में कुल ( $attendance ) दिनों की उपस्थिति दर्ज की गई हैं, इसमें अगर किसी प्रकार की त्रुटि हो तो आप आज 
शनिवार (27-4-2024)  5 बजे से पहले नीचे दिए गये HR team के व्हाट्सएप नंबर पर एम्प्लॉयी कोड, दिनांक व त्रुटि (सुधार कारण के साथ अपने HOD की अप्रूवल) व्हाट्सएप करके HRMS app व Darwin Box App में update करवा लेवें। कृपया कॉल नहीं करे।
1:- नेहल टावर – मनीष 7849829855        </br>
2:- इंदौर - दीपांशु पांडे  9773343256   </br>
3 :- जिलानी टावर और रुक्मणि टावर - जूही चौहान 9257018617   </br>
4 :-प्रयागराज – सुमाईला   9151064475   </br>
5 :-अभय चैम्बर/अग्रवाल भवन/गीता भवन/जालोरी गेट/मंगल टावर/राजमोहन टावर uco बैंक बिल्डिंग /व्यास भवन - मनीष कछवाहा 7849829855   </br>
6:-उत्कर्ष भवन और जोधपुर गेस्ट हाउस :- प्रेरणा गहलोत 9257018616   </br>
7:-जयपुर -प्रियंका 8905985547   </br>
8:-उत्कर्ष काम्प्लेक्स :- रोबिन 9257018619   </br>
9 :- विनोद टावर :- 8279246623  गौरव सोलंकी           </br>
।  नोट: आज शनिवार (27-4-2024) 5  बजे के बाद 25 अप्रैल तक व उससे पहले की उपस्थिति में कोई परिवर्तन(त्रुटि सुधार) स्वीकार नहीं होंगे व ना ही अगले महीने की सैलरी के साथ 25 अप्रैल से पहले की उपस्थिति के त्रुटि सुधार जोड़े जायेंगे।
अत: किसी कर्मचारी को लीव अप्लाइ करनी है तो आज ही लीव अप्लाइ करके अपनी लीव भी HOD से अप्रूव करवाए।   </br>
धन्यवाद
 उत्कर्ष टीम";
								
								
								// $user_id = $user->id;
								$user_id = $emp_id;
								DB::table('api_notifications')->insertGetId([
									'sender_id' =>  901,
									'receiver_id' =>  json_encode(array("$user_id")),
									'title' =>  "Month April-2024 Attendance Update!!",
									'description' =>  $msg,
									'type' => 'General',
									'date' => date('Y-m-d H:i:s'),
									'created_at' => date('Y-m-d H:i:s'),
									'check_dk' => 2
								]);
								
							}
							
							die('dddd');
							
						}
					}
				}}
			}
			
			die('Success');
		}
	}

	//Incomplete
	public function incompleteattendence(Request $request){
		
		// echo $request->incomplete_type; die;
		$logged_role_id  	 = Auth::user()->role_id;
		$logged_id       	 = Auth::user()->id;
		$logid 			 	 = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
		
		//Chetan
		/*
		if($logged_role_id == 24){
			$userBranch	=	Userbranches::where('user_id', $logged_id)->first();
			$ubData		=	$userBranch['branch_id'];
			
			if($ubData== 57 || $ubData== 58 || $ubData== 43 || $ubData== 55){																
				$allBranches = Branch::where('status', '1')->where('related', '2')->orderBy('id', 'desc')->get(); 
			}else{
				$allBranches = $this->allBranches();
			}
		}else{
			$allBranches = $this->allBranches();
		}
		*/
        //End
		
		$allBranches = $this->allBranches();
		return view('admin.attendance.incompleteattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	
	function incomplteAttendanceDetail(Request $request){		
		$allDepartmentTypes	 =	$this->allDepartmentTypes();
        //$allBranches = $this->allBranches();
		$logged_role_id      =	Auth::user()->role_id;
		$logged_id           =	Auth::user()->id;
		$name                =	$request->name;
		$department_type     =	$request->department_type;
		$branch_id           =	$request->branch_id;
		$fdate               =	$request->fdate;
        $tdate               =	$request->tdate;
        $last_location       =	$request->last_location;
        $incomplete_type     =	$request->incomplete_type;
		$logid               =	array();
        $users				 = 	NewTask::getEmployeeByLogID($logged_id,'attendance');
		
		
		//Chetan
		
		if($logged_role_id == 24){
			$userBranch	=	Userbranches::where('user_id', $logged_id)->first();
			$ubData		=	$userBranch['branch_id'];
			
			if($ubData== 57 || $ubData== 58 || $ubData== 43 || $ubData== 55){																
				$allBranches = Branch::where('status', '1')->where('related', '2')->orderBy('id', 'desc')->get(); 
			}else{
				$allBranches = $this->allBranches();
			}
		}else{
			$allBranches = $this->allBranches();
		}
        //End
        		
		//App Attendance
		$attendance= Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendance->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);			
		}
		else{
			$attendance->where('date', '=', date('Y-m-d', strtotime("-1 days")));	
		}
		
		if (!empty($last_location)){
			$attendance->whereRaw("location like '%$last_location%'");
		}
				
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
			
		
		$attendance = $attendance->selectRaw("id,emp_id,date,'App' as table_name");
		
		//RFID Attendance
		$attendancenew      = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendancenew->groupBy('emp_id');
		
		
		if(!empty($fdate) && !empty($tdate)){
			$attendancenew->where('date', '>=', $fdate);
			$attendancenew->where('date', '<=', $tdate);
		}
		else{
			$attendancenew->where('date', '=', date('Y-m-d', strtotime("-1 days")));		
		}
		
		if (!empty($last_location)){
			$attendancenew->whereRaw("location like '%$last_location%'");
		}
		
		if (!empty($name)){
			$attendancenew->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		} 
		
		if(!empty($branch_id)) {
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
				
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		
		$attendancenew->whereIn('emp_id', $employeeArray);	
		
		
		$attendancenew = $attendancenew->selectRaw("id,emp_id,date,'RFID' as table_name");
		
		
		
		$comman = $attendancenew->union($attendance);		
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')	
						   ->get();
		
		
		//Record Part
						
		$responseArray = array();
		foreach($comman_result as $key=>$valAtt){		
			
			$valAttRes = User::with('user_branches.branch','department')->where('id',$valAtt->emp_id)->first();
			$branch = '';
			
			$get_attendance= array();		 
			$get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();				 
			 
		
			if(count($get_u_attendance_new) > 0){
				$get_attendance = $get_u_attendance_new;					
			}
			else{
				$get_u_attendance     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$get_attendance  = $get_u_attendance;	
			}
			
			$time_array = array();
			$ii=0;
			$total_minute = 0;
			$last_location = "";
			
			
			if(count($get_attendance)%2!="0"){				
				// $responseArray[$key]['table_name']  = $valAtt->table_name;
				// $responseArray[$key]['id'] 			= $valAtt->id;				
				// $responseArray[$key]['date'] 		= $valAtt->date;
				// $responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
				// $responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
				// $responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
				// $responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
				// $responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
				// $responseArray[$key]['total_hours'] = "00";
		
		
		
				foreach($get_attendance as $key1 => $AttendanceDetail){
					$in_location = "";
					$out_location = "";
					$in_time = "";
					$out_time = "";
					if(empty($time_array[$ii]['in_time'])){
						$time_array[$ii]['in_time'] = "";
						$time_array[$ii]['in_location'] =  "";
					}
					if(empty($time_array[$ii]['out_time'])){
						$time_array[$ii]['out_time'] = "";
						$time_array[$ii]['out_location'] =  "";
					}
					
					if($AttendanceDetail->type=="In"){
						$in_time = $AttendanceDetail->time;
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
						}
						else{
							$ii++;
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$time_array[$ii]['out_time'] = "";
							$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
							$time_array[$ii]['out_location'] =  "";
							$last_location = $AttendanceDetail->location;
						}
					}
					else if($AttendanceDetail->type=="Out"){
						$out_time = $AttendanceDetail->time;
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
							$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
							$ii++;
						}
					}
					
					/* if(!empty($time_array[$ii]['in_time']) && !empty($time_array[$ii]['out_time'])){
						$intime = new DateTime($$time_array[$ii]['in_time']);
						$outtime = new DateTime($$time_array[$ii]['out_time']);
						$interval = $intime->diff($outtime);
						$hours = $interval->format('%H');
						$minute = $interval->format('%I');
						$total_minute += ($hours*60)+$minute;
					} */
					
					$j = 1;
					
					if(count($time_array) > 0){
						foreach($time_array as $time_array_value){ 
							if(count($time_array) == $j){
								if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
									$intime = new DateTime($time_array_value['in_time']); 
									$outtime = new DateTime($time_array_value['out_time']); 
									$interval = $intime->diff($outtime);
									$hours = $interval->format('%H');
									$minute = $interval->format('%I');
									$total_minute += ($hours*60)+$minute;
								}	
							}
							$j++;
						}
					}
				}
				
				
				
				
				
				//Attadance Type //Chetan
				$totalMin	=	($total_minute*100)/540;				
				
				if($incomplete_type=='p'){
					if($totalMin > 75){
						$responseArray[$key]['table_name']  	= $valAtt->table_name;
						$responseArray[$key]['id'] 				= $valAtt->id;				
						$responseArray[$key]['date'] 			= $valAtt->date;
						$responseArray[$key]['edit_date'] 		= $valAtt->date;
						$responseArray[$key]['register_id'] 	= isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      	= isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        	= isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      	= isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
						$responseArray[$key]['department']  	= isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] 	= "00";
						$responseArray[$key]['total_hours'] 	= $total_minute ." Minutes";
						$responseArray[$key]['last_location'] 	= $last_location;
						$responseArray[$key]['time'] 			= $time_array;
						$responseArray[$key]['attendance_type'] = "P";
					}
				}else if($incomplete_type=='h'){
					if($totalMin > 50 && $totalMin < 75){
						$responseArray[$key]['table_name']  	= $valAtt->table_name;
						$responseArray[$key]['id'] 				= $valAtt->id;				
						$responseArray[$key]['date'] 			= $valAtt->date;
						$responseArray[$key]['edit_date'] 		= $valAtt->date;
						$responseArray[$key]['register_id'] 	= isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      	= isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        	= isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      	= isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
						$responseArray[$key]['department']  	= isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] 	= "00";
						$responseArray[$key]['total_hours'] 	= $total_minute ." Minutes";
						$responseArray[$key]['last_location'] 	= $last_location;
						$responseArray[$key]['time'] 			= $time_array;
						$responseArray[$key]['attendance_type'] = "H";
					}
				}else if($incomplete_type=='a'){
					if($totalMin <= 50){
						$responseArray[$key]['table_name']  	= $valAtt->table_name;
						$responseArray[$key]['id'] 				= $valAtt->id;				
						$responseArray[$key]['date'] 			= $valAtt->date;
						$responseArray[$key]['edit_date'] 		= $valAtt->date;
						$responseArray[$key]['register_id'] 	= isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      	= isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        	= isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      	= isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
						$responseArray[$key]['department']  	= isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] 	= "00";
						$responseArray[$key]['total_hours'] 	= $total_minute ." Minutes";
						$responseArray[$key]['last_location'] 	= $last_location;
						$responseArray[$key]['time'] 			= $time_array;
						$responseArray[$key]['attendance_type'] = "A";
					}
				}else{
					if($totalMin > 75){
						$attendanceType = "P";
					}else if($totalMin > 50){
						$attendanceType = "H";
					}else{
						$attendanceType = "A";
					}
					
					$responseArray[$key]['table_name']  	= $valAtt->table_name;
					$responseArray[$key]['id'] 				= $valAtt->id;				
					$responseArray[$key]['date'] 			= date('d-m-Y',strtotime($valAtt->date));
					$responseArray[$key]['edit_date'] 		= $valAtt->date;
					$responseArray[$key]['register_id'] 	= isset($valAttRes->register_id)?$valAttRes->register_id:'';
					$responseArray[$key]['emp_id']      	= isset($valAttRes->id)?$valAttRes->id:0;
					$responseArray[$key]['name']        	= isset($valAttRes->name)?$valAttRes->name:'';
					$responseArray[$key]['branch']      	= isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
					$responseArray[$key]['department']  	= isset($valAttRes->department->name) ? $valAttRes->department->name : '';
					$responseArray[$key]['total_hours'] 	= "00";
					$responseArray[$key]['total_hours'] 	= $total_minute ." Minutes";
					$responseArray[$key]['last_location'] 	= $last_location;
					$responseArray[$key]['time'] 			= $time_array;
					$responseArray[$key]['attendance_type'] = $attendanceType;
				}
				
				
			}
			
		}
						
		return DataTables::of($responseArray)->make(true);
	}
	
	public function incomplete_download_excel(Request $request){
		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$last_location       = $request->last_location;
		$incomplete_type     =	$request->incomplete_type;
		
		
		$logid               = array();
        $users= NewTask::getEmployeeByLogID($logged_id,'attendance');
       
		$attendance= Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendance->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=', date('Y-m-d', strtotime("-1 days")));
		}
		
		if (!empty($last_location)){
			$attendance->whereRaw("location like '%$last_location%'");
		}
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
		
		
		$attendance = $attendance->selectRaw("id,emp_id,date,'App' as table_name");
		
		
		//$attendance = $attendance->get();
		$attendancenew      = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendancenew->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendancenew->where('date', '>=', $fdate);
			$attendancenew->where('date', '<=', $tdate);
		}
		else{
			$attendancenew->where('date', '=', date('Y-m-d', strtotime("-1 days")));
		}
		
		if (!empty($last_location)){
			$attendancenew->whereRaw("location like '%$last_location%'");
		}
		
		if (!empty($name)){
			$attendancenew->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		} 
		
		if(!empty($branch_id)) {
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendancenew->whereIn('emp_id', $employeeArray);
		
	
		$attendancenew = $attendancenew->selectRaw("id,emp_id,date,'RFID' as table_name");
		
		
		$comman = $attendancenew->union($attendance);
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')
						   ->get();
				
	
		$responseArray = array();			
		foreach($comman_result as $key=>$valAtt){ 
			$valAttRes = User::with('user_details.branch','department')->where('id',$valAtt->emp_id)->first();
			
			
			$get_attendance= array();		   
		 
			$get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();				 
			 
			if(count($get_u_attendance_new) > 0){
				 $get_attendance = $get_u_attendance_new;
			}
			else{
				 $get_u_attendance     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				 $get_attendance  = $get_u_attendance;
			}
			
			
			$time_array = array();
			$ii=0;
			$total_minute = 0;
			$last_location = "";
			if(count($get_attendance)%2!="0"){
				foreach($get_attendance as $key1 => $AttendanceDetail){
					$branch = '';
					// $responseArray[$key]['table_name'] = $valAtt->table_name;
					// $responseArray[$key]['id'] = $valAtt->id;				
					// $responseArray[$key]['date'] = $valAtt->date;
					// $responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
					// $responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
					// $responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
					// $responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
					// $responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
					// $responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
					// $responseArray[$key]['total_hours'] = "00";
					
					
					
					$in_time = "";
					$out_time = "";
					if(empty($time_array[$ii]['in_time'])){
						$time_array[$ii]['in_time'] = "";
					}
					if(empty($time_array[$ii]['out_time'])){
						$time_array[$ii]['out_time'] = "";
					}
					
					if($AttendanceDetail->type=="In"){
						$in_time = $AttendanceDetail->time;
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$last_location = $AttendanceDetail->location;
						}
						else{
							$ii++;
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$time_array[$ii]['out_time'] = "";
							$last_location = $AttendanceDetail->location;
						}
					}
					else if($AttendanceDetail->type=="Out"){
						$out_time = $AttendanceDetail->time;
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
							$last_location = $AttendanceDetail->location;
							$ii++;
						}
					}
					
					$j = 1;
					if(count($time_array) > 0){
						foreach($time_array as $time_array_value){ 
							if(count($time_array) == $j){
								if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
									$intime = new DateTime($time_array_value['in_time']); 
									$outtime = new DateTime($time_array_value['out_time']); 
									$interval = $intime->diff($outtime);
									$hours = $interval->format('%H');
									$minute = $interval->format('%I');
									$total_minute += ($hours*60)+$minute;
								}	
							}
							$j++;
						}
					}
					
				}
				
				
				$totalMin	=	($total_minute*100)/540;				
				
				if($incomplete_type=='p'){
					if($totalMin > 75){
						$responseArray[$key]['table_name'] = $valAtt->table_name;
						$responseArray[$key]['id'] = $valAtt->id;				
						$responseArray[$key]['date'] = $valAtt->date;
						$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
						$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
						$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] = "00";
					
						$responseArray[$key]['last_location'] = $last_location;
						$responseArray[$key]['time'] = $time_array;
					}
				}else if($incomplete_type=='h'){
					if($totalMin > 50 && $totalMin < 75){
						$responseArray[$key]['table_name'] = $valAtt->table_name;
						$responseArray[$key]['id'] = $valAtt->id;				
						$responseArray[$key]['date'] = $valAtt->date;
						$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
						$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
						$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] = "00";
					
						$responseArray[$key]['last_location'] = $last_location;
						$responseArray[$key]['time'] = $time_array;
					}
				}else if($incomplete_type=='a'){
					if($totalMin < 50){
						$responseArray[$key]['table_name'] = $valAtt->table_name;
						$responseArray[$key]['id'] = $valAtt->id;				
						$responseArray[$key]['date'] = $valAtt->date;
						$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
						$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
						$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
						$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
						$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
						$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
						$responseArray[$key]['total_hours'] = "00";
					
						$responseArray[$key]['last_location'] = $last_location;
						$responseArray[$key]['time'] = $time_array;
					}
				}else if($incomplete_type==""){
					$responseArray[$key]['table_name'] = $valAtt->table_name;
					$responseArray[$key]['id'] = $valAtt->id;				
					$responseArray[$key]['date'] = date('d-m-Y',strtotime($valAtt->date));
					$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
					$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
					$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
					$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';  
					$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
					$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
					$responseArray[$key]['total_hours'] = "00";
				
					$responseArray[$key]['last_location'] = $last_location;
					$responseArray[$key]['time'] = $time_array;
				}
				
			}
			
		}
		
		//echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new IncompleteExport($responseArray), 'IncompleteAttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	//Chetan
	public function full_download_excel_two(Request $request){
		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
		$last_location       = $request->last_location;
		$logid               = array();
        //$users				 = NewTask::getEmployeeByLogID($logged_id,'attendance');
		
		if($fdate==""){
			$fdate = date('Y-m-d');
			$tdate = date('Y-m-d');
		}
        
		$responseArray = $this->calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$fdate,$tdate);
		
		// echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new FullAttendanceExportTwo($responseArray), 'FullAttendanceDataTwo.xlsx'); 
        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$fdate,$tdate){
		
		$users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		// $usr          = User::where('status', 1);
		
		$new_whereCond    = 'users.register_id>=1001 AND users.register_id!=""';
		$new_whereCondAtt = '';
		$whereCondAtt = '';
		if(!empty($name) && !empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$new_whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type')";
		}
		if(!empty($branch_id)){
			$new_whereCond .= " AND (userbranches.branch_id = '$branch_id')";
		}
		
		
				
		$employeeArray= array();
		$usr	= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.register_id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name,userdetails.degination as designation_name,userdetails.net_salary,userdetails.joining_date,users.is_extra_working_salary,users.total_time"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
		$attendance->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
		$attendance->orderBy('name');
		$attendance->groupBy(['userbranches.user_id']);
		$array1 = $attendance->get();
				 			
		$comman_result = array();
		
		$year_wise_month = explode('-',$fdate);
		
		$yr = $year_wise_month[0];
		$mt = $year_wise_month[1];
		$getWorkSunday1 = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				
		if(count($array1) > 0){
			foreach($array1 as $val){
				$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($fdate);
				$last_date = strtotime($tdate);
				$user_id = $val->id;
				//$previous_year_month = date("Y-m", strtotime ( '-1 month' , ( $first_date ) )) ;
				$previous_year_month = date("Y-m", $first_date);
				$leave_balance = 0;
				$current_month_wise = strtotime(date('Y-m').'-'.cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'))); // date('n') = month without 0
				
				
				
				$user_array 					= array(); 
				$user_array['id'] 				= $user_id;
				$user_array['name'] 			= $val->name;
				$user_array['register_id'] 		= $val->register_id;
				$user_array['email'] 			= $val->email;
				$user_array['branch_name'] 		= $val->branch_name;
				$user_array['departments_name'] = $val->departments_name;
				$user_array['designation_name'] = $val->designation_name;
				$user_array['net_salary'] 		= $val->net_salary;
				
				
				$attendance_array = array();
				
				$first_date_get = date('Y-m-d',$first_date);
				$last_date_get = date('Y-m-d',$last_date);

				$attendance = Attendance::query();
				$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
				$attendance->where('emp_id', $user_id);
				$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');

				$attendance = $attendance->groupBy('date');
				
				
				$attendancenew = AttendanceNew::query();
				$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
				$attendancenew->where('emp_id', $user_id);
				$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
				$attendancenew = $attendancenew->groupBy('date');				
				
				$comman = $attendancenew->union($attendance);
				$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')
						   ->get();
				
				if(count($comman_result1) > 0){
					$attendance_array = json_decode(json_encode($comman_result1),true);
				}
				
				//echo '<pre>'; print_r($attendance_array); die;
				//Get Timing				
				$time_array = array();
				$ii=0;				
				$in_time = "";
				$out_time = "";
				
				if(empty($time_array[$ii]['in_time'])){
					$time_array[$ii]['in_time'] = "";
				}
				if(empty($time_array[$ii]['out_time'])){
					$time_array[$ii]['out_time'] = "";
				}  
				
				if(count($attendance_array) > 0){
					foreach($attendance_array as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
														
							
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						$in_time  = $AttendanceDetail['in_time'];
						
						if($AttendanceDetail['out_time']==0){
							$out_time = " ";
							$oTime	  = $out_time;
						}else{
							$out_time = $AttendanceDetail['out_time'];
							$oTime	  = date("h:i A", strtotime($out_time));
						}
						
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] =  !empty($in_time) ? date("h:i A", strtotime($in_time)) : '';
							$time_array[$ii]['out_time'] =  $oTime;
						}	
						
						$time_array[$ii]['date'] = $AttendanceDetail['date'];
						$ii++;
						
					}
				}
				
				if(count($comman_result1) > 0){
					//print_r($time_array); die;
					$user_array['date_array'] 	=	$time_array;				
					$comman_result[] 			=	$user_array;
				}
			}
		}
		
		
		$responseArray = array();
		 
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){		
				$responseArray[$key]['name']   				= $valAtt['name'];
				$responseArray[$key]['register_id']  		= $valAtt['register_id'];
				$responseArray[$key]['branch_name']      	= $valAtt['branch_name'] ? $valAtt['branch_name'] : '';
				$responseArray[$key]['departments_name'] 	= $valAtt['departments_name'] ? $valAtt['departments_name'] : '';
				$responseArray[$key]['designation_name']  	= $valAtt['designation_name'];
				$responseArray[$key]['fdate'] 				= $fdate; 
				$responseArray[$key]['tdate'] 				= $tdate; 
				$responseArray[$key]['date_array'] 			= $valAtt['date_array']; 				
			}
		}
		return $responseArray;
	}
	
	
	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('all_history')->insert($history_data);
	}
	
	public function finalAttendence(Request $request){
		$mt = date('m');
		$yr = date('Y');
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
			$year_wise_month = explode('-',$params['se_year_wise_month']);
			
			$yr = $year_wise_month[0];
			$mt = $year_wise_month[1];
		}
		
		$getWorkSunday 			= 	cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		return view('admin.attendance.final-attendence', compact('getWorkSunday'));
	}
	
	public function storeFinalAttendence(Request $request){
		die('No Allowed');
		//dd($request->post());
		$validatedData = $request->validate([
			'import_file' => 'required',
			'year_wise_month' => 'required',
		]);
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}
        	
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		if(!empty($import[0])){
			$check_record = DB::table('final_attendence')->where('month_year',$request->year_wise_month.'-01')->first(); 
			if(empty($check_record)){
				$emp_code_arr = [];
				foreach ($import[0] as $key => $value) {
					if(!in_array($value[0], $emp_code_arr)){
						array_push($emp_code_arr,$value[0]);
						DB::table('final_attendence')->insertGetId([
								'emp_code' => $value[0],
								'month_year' => $request->year_wise_month.'-01', 
								'salary' => $value[2],
								'd_1' => $value[3],
								'd_2' => $value[4], 
								'd_3' => $value[5],
								'd_4' => $value[6],
								'd_5' => $value[7],
								'd_6' => $value[8],
								'd_7' => $value[9],
								'd_8' => $value[10],
								'd_9' => $value[11],
								'd_10' => $value[12],
								'd_11' => $value[13],
								'd_12' => $value[14],
								'd_13' => $value[15],
								'd_14' => $value[16],
								'd_15' => $value[17],
								'd_16' => $value[18],
								'd_17' => $value[19], 
								'd_18' => $value[20],
								'd_19' => $value[21],
								'd_20' => $value[22],
								'd_21' => $value[23],
								'd_22' => $value[24],
								'd_23' => $value[25],
								'd_24' => $value[26],
								'd_25' => $value[27],
								'd_26' => $value[28],
								'd_27' => $value[29],
								'd_28' => $value[30],
								'd_29' => $value[31],
								'd_30' => $value[32],
								'd_31' => $value[33],
						]);
					}
				}
			}
			else{ 
				 return redirect()->route('admin.attendance.final-attendence')->with('error', "Attendence Already Added");
			}			
		}
		else{
			return redirect()->route('admin.attendance.final-attendence')->with('error', "Something went wrong !");
		}
        return back()->with('success', 'Attendence Data Imported successfully.'); 
	}
	
	public function finalAttendenceDetail(Request $request){ 
		$name = $request->name; 
		$se_year_wise_month = $request->se_year_wise_month;   
		
		$responseArray = DB::table('final_attendence')->select('final_attendence.*')->join('users','users.register_id','=','final_attendence.emp_code');
		if(!empty($name)){
			$responseArray->whereRaw("users.name LIKE '%$name%' or users.register_id LIKE '%$name%'");
		}
		
		if(!empty($request->se_year_wise_month)){
			$date = $request->se_year_wise_month.'-01';
		}
		else{
			$date = date('Y-m').'-01';
		}
		$responseArray->where('month_year', $date); 
		
		$responseArray = $responseArray->groupBy('final_attendence.emp_code')->get();
		
		//echo "<pre>";print_R($responseArray); die;
		return DataTables::of($responseArray)->make(true);

		
	}

	public function fullattendence(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.fullattendence_new', compact('allDepartmentTypes','allBranches'));
	}
	
	public function fullAttendanceDetail_new(Request $request){ //echo '<pre>'; print_r($request->name);die;
	   
		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_location     = $request->branch_location;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
        $last_location       = $request->last_location;
		$logid               = array();
        $users= NewTask::getEmployeeByLogID($logged_id,'attendance');
        
		//$attendance= AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		$attendance= Attendance::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendance->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		else{
			$attendance->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendance->whereRaw("location like '%$last_location%'");
		}
		 
		/* if (!empty($name) || !empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendance->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		}
		
		if(!empty($branch_id)) {
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if(!empty($branch_location)){
			$attendance->WhereHas('user.user_branches.branch', function ($q) use ($branch_location) { // orWhereHas dk				
				$q->where('branch_location', $branch_location);							
			});	
		}
		
		// if($logged_role_id->role_id==24 || $logged_role_id->role_id==29){ 
			// $attendance->WhereHas('user.supervisor_id', function ($q) use ($supervisor_id) {
				// $q->where('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
			// });
		// }
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			//$attendance->whereIn('emp_id', [$logged_id]);
			// $employeeArray = array();
			// $usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			// $employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendance->whereIn('emp_id', $employeeArray);
		} */
		
		
		$attendance = $attendance->selectRaw("id,emp_id,date,reason,'App' as table_name,time");
		
		
		//$attendance = $attendance->get();
		$attendancenew      = AttendanceNew::with('user.user_branches.branch','user.department')->groupBy('date')->orderBy('date', 'desc');
		$attendancenew->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendancenew->where('date', '>=', $fdate);
			$attendancenew->where('date', '<=', $tdate);
		}
		else{
			$attendancenew->where('date', '=',date('Y-m-d'));
		}
		
		if (!empty($last_location)){
			$attendancenew->whereRaw("location like '%$last_location%'");
		}
		
		/* if (!empty($name) || !empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($name,$department_type) {
				if(!empty($name) && !empty($department_type)){
					$q->whereRaw("(department_type = '$department_type' and (name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($name)){
					$q->whereRaw("((name LIKE '%$name%' or register_id LIKE '%$name%'))");
				}
				else if(!empty($department_type)){
					$q->whereRaw("(department_type = '$department_type')");
				}
			});
		} */
		
		if (!empty($name)){
			$attendancenew->WhereHas('user', function ($q) use ($name) {
				$q->whereRaw("(name LIKE '%$name%' or register_id LIKE '%$name%')");
			});
		}
		
		if (!empty($department_type)){
			$attendancenew->WhereHas('user.department', function ($q) use ($department_type) {
				$q->where('department_type', '=', $department_type);
			});
		} 
		
		if(!empty($branch_id)) {
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if(!empty($branch_location)){
			$attendancenew->WhereHas('user.user_branches.branch', function ($q) use ($branch_location) { // orWhereHas dk				
				$q->where('branch_location', $branch_location);							
			});	
		}
		
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$attendancenew->whereIn('emp_id', $employeeArray);
		
		/* if($logged_role_id == 20){
			$attendancenew->whereIn('emp_id', [$logged_id]);
		}
		else{
			$employeeArray = array();
			
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
			$attendancenew->whereIn('emp_id', $employeeArray);
		} */
		
		$attendancenew = $attendancenew->selectRaw("id,emp_id,date,reason,'RFID' as table_name,time");
		
		
		$comman = $attendancenew->union($attendance);
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->orderBy('comman.date', 'asc')
						   ->orderBy('comman.time', 'asc')
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')

						   ->get();
				
	   // echo '<pre>'; print_r($comman_result);die;
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 
			    $valAttRes = User::with('user_branches.branch','department')->where('id',$valAtt->emp_id)->first();
				//echo '<pre>'; print_r($valAttRes->department->name);die;
				
				$branch = '';
				/* foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				} */
				
				//echo '<pre>'; print_r($valAtt->id);die;
				
				
				$responseArray[$key]['table_name'] = $valAtt->table_name;
				$responseArray[$key]['id'] = $valAtt->id;				
				$responseArray[$key]['date'] = date('d-m-Y',strtotime($valAtt->date));
				$responseArray[$key]['edit_date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
				$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
				$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
				$responseArray[$key]['branch']      = isset($valAttRes->user_branches[0]->branch->name)?$valAttRes->user_branches[0]->branch->name:'';
				$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				
				$get_attendance_comman_result     = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->selectRaw("id,emp_id,date,reason,'App' as table_name,time,location,for_reason");
				
				$get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->selectRaw("id,emp_id,date,reason,'RFID' as table_name,time,location,for_reason");
				
				$comman_in = $get_u_attendance_new->union($get_attendance_comman_result);
				$get_attendance = DB::table(DB::raw("({$comman_in->toSql()}) as comman_in"))
								->mergeBindings($comman_in->getQuery())
								->orderBy('comman_in.time', 'asc')
								->get();
				// echo "<pre>"; print_R($get_attendance); die;
				$fetchData = array();				
				if(count($get_attendance)>0){
					if(count($get_attendance) == 1){
						$fetchData[] = $get_attendance[0];
					}
					else{
						$fetchData[] = $get_attendance[0];
						$fetchData[] = $get_attendance[count($get_attendance)-1];
					}
					
				}
				
				// echo "<pre>"; print_R($fetchData); die;
				 
				$time_array = array();
				$ii=0;
				$total_minute = 0;
				$last_location = "";
				$userReason = "";
				if(count($fetchData) > 0){
					$in_time = "";
					$out_time = "";
					foreach($fetchData as $key1 => $AttendanceDetail){
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
							$time_array[$ii]['in_location'] =  "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
							$time_array[$ii]['out_location'] =  "";
						}
						if($key1==0){ // First In 
							$in_time = $AttendanceDetail->time;
							$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
							$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
						}
						else{ // Last Out					
							$out_time = $AttendanceDetail->time;
							$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
							$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
							$last_location = $AttendanceDetail->location;
							$ii++;
						}
						
						if($AttendanceDetail->for_reason=="1"){
							$forReason  = "In";
						}else if($AttendanceDetail->for_reason=="2"){
							$forReason  = "Out";
						}else{							
							$forReason  = "Both";
						}
						if(!empty($AttendanceDetail->reason)){
							$userReason =	$AttendanceDetail->reason;
						}
						
					}
					if(!empty($in_time) && !empty($out_time)){
						$intime = new DateTime($in_time); 
						$outtime = new DateTime($out_time); 
						$interval = $intime->diff($outtime);
						$hours = $interval->format('%H');
						$minute = $interval->format('%I');
						$total_minute = ($hours*60)+$minute;
					}
				}
				
				if(!empty($userReason)){
					$responseArray[$key]['reason'] = $userReason.' ('.$forReason.')';
				}else{
					$responseArray[$key]['reason'] = '';
				}
				
				
				
				$responseArray[$key]['total_hours'] = $total_minute ." Minutes";
				$responseArray[$key]['last_location'] = $last_location;
				$responseArray[$key]['time'] = $time_array;
				
				$user_branch_id = $valAttRes->user_branches[0]->branch->id;
				
				$check_holiday  = Holiday::select('type','branch_id')->whereRaw("DATE(date) = '$valAtt->date'")->where('status', '1')->where('is_deleted', '0')->first();
				$holiday_branch = array();
				if(!empty($check_holiday->branch_id)){
					$holiday_branch = json_decode($check_holiday->branch_id); 
				}
				
				if(!empty($holiday_branch) && in_array($user_branch_id, $holiday_branch)){
					
					$totalMin	=	($total_minute*100)/$valAttRes->total_time;
					if($totalMin < 38.88){
						$attendanceType = 'H';						
					}
					else if($totalMin < 66.66){
						$attendanceType = 'H+HW/2';
					}
					else{						
						$attendanceType = 'H+HW';						
					}
				}
				else{
					//Attadance Type //Chetan
					//$totalMin	=	($total_minute*100)/540;				
					$totalMin	=	($total_minute*100)/$valAttRes->total_time;
					if($totalMin < 50){
						$attendanceType = "A";
					}
					else if($totalMin < 88.88){
						$attendanceType = "PH";
					}
					else{
						$attendanceType = "P";
					}
				}
				
				$responseArray[$key]['attendance_type'] = $attendanceType;
				
			}
		}
		
		// echo '<pre>'; print_r($responseArray);die;
		return DataTables::of($responseArray)->make(true);
	}

	public function one_time_attendance_updated()
    {
		
		$login_id        = Auth::user()->id;
		
	
		
		if($login_id == 901 || $login_id == 7107){
			
		}
		else{
			echo "<b style='color:red;'>Note - Not access you from today.</b>"; die;
		}
		
		$users = array();
		/* $logged_role_id  = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		if($logged_role_id == 20){
			$users        = User::where('id', $login_id)->get();
		}
		else if($logged_role_id == 21){
			//$users = NewTask::getEmployeeForDepartmentHead($login_id, $logged_role_id, $logged_department_type);
			$users        = NewTask::getEmployeeByLogID($login_id,'create-attendance');
		}
		else{
			$users        = NewTask::getEmployeeByLogID($login_id,'create-attendance');
		} */
		
        return view('admin.attendance.one_time_attendance_updated', compact('users'));
    }
	
	public function store_attendance(Request $request)
    {
		// print_r($_POST); die;
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
        $validatedData = $request->validate([
            'emp_id' => 'required',
            'date' => 'required',
        ]);
		if(empty($login_id) ){
			return redirect()->back()->with('error', 'You have not access.');
		}
		
		 
		if(!empty($request->emp_id)){
			$emp_id = $request->emp_id;
			$dates  = $request->date;
			
			/* $mont_year = date('Y-m',strtotime($dates));
			if($logged_role_id != 24 && $logged_role_id!=29){
				$alock = AttendanceLock::where('month', $mont_year)->first();
				if(!empty($alock)){
					if($alock->status=='1'){
						return redirect()->back()->with('error', 'Attendance is locked.');
					}
				}
			} */
			
			$type   = 'Full Day';
			//dd($emp_id);
			

			$myArray = explode(',', $emp_id);
			$user = User::select('*')->whereIn('register_id', $myArray)->get();
			// dd($user);
			if(!empty($user)){ 
					
				foreach($user as $use){
					$i = 0;

					if (is_array($request->time) && !empty($request->time)) {
						$time = $request->time;
						$type = $request->type;
						foreach ($time as $key => $value) {
							echo $value;
							if(!empty($value)){
								
								DB::table('attendance')->where('date',$request->date)->where('type',$type[$key])->where('emp_id',$use->id)->delete();
								DB::table('attendance_new')->where('date',$request->date)->where('type',$type[$key])->where('emp_id', $use->id)->delete();
								
								$data = array();
								$data['emp_id'] = $use->id;
								$data['date'] = $request->date;
								$data['location'] = $request->location;
								$data['time'] = $value;
								$data['type'] = $type[$key];
								$data['created_at'] = date('Y-m-d H:i:s');
								$data['updated_at'] = date('Y-m-d H:i:s');
								$data['admin_id'] = $login_id;
								$data['reason'] = $request->reason;
								$data['for_reason'] = $request->for_reason;
								$attendance_id = Attendance::create($data);
								$i++;
								
								$this->maintain_history(Auth::user()->id, 'attendance', $attendance_id->id, 'create_attendance', json_encode($data));
							}
						}
						
					}

				} 
				
				return redirect()->back()->with('success', 'Attendance Added Successfully');
				
			}
			else{
				return redirect()->back()->with('error', 'User Id Not Found');
			}
		}
		else{
			return redirect()->back()->with('error', 'User Id Not Found');
		}
    }

}
