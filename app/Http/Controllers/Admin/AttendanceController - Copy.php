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
use App\Exports\AbsentFullAttendanceExport;
use Auth;
use DB;
use Illuminate\Pagination\Paginator;
use App\AttendanceNew;
use DataTables;
use DateTime;

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
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches         = $this->allBranches();
		
		
		if(!empty($date)){
			$date = $date;
		}
		else{
			$date = date('Y-m-d');
		}
		
		$where = "";
		if(!empty($name)){
			$where .= " and users.name LIKE '%$name%' ";
		}
		
		if(!empty($department_type)){
			$where .= " and users.department_type = '$department_type'";
		}
		
		if(!empty($branch_id)){
			$where .= " and userdetails.branch_id = '$branch_id'";
		}
		
		if($logged_role_id == 21){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userdetails ON users.id = userdetails.user_id WHERE users.id NOT IN (SELECT emp_id FROM attendance where date="'.$date.'") and users.role_id != 1 and users.role_id = "'.$logged_role_id.'" and users.department_type = "'.$logged_type.'" and users.status = 1 '.$where);
			
		}
		else if($logged_role_id == 29){
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userdetails ON users.id = userdetails.user_id WHERE users.id NOT IN (SELECT emp_id FROM attendance where date="'.$date.'") and users.role_id != 1 '.$where);
			
		}
		else{
			$responseArray = DB::select('SELECT users.* FROM users INNER JOIN userdetails ON users.id = userdetails.user_id WHERE users.id NOT IN (SELECT emp_id FROM attendance where date="'.$date.'") and users.id = "'.$logged_id.'" and users.role_id != 1 '.$where);
		}
		return view('admin.attendance.absentuser', compact('responseArray','allDepartmentTypes','allBranches'));
		
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
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		if($logged_role_id == 20){
			$users        = User::where('id', $login_id)->get();
		}
		else if($logged_role_id == 21){
			$users = NewTask::getEmployeeForDepartmentHead($login_id, $logged_role_id, $logged_department_type);
		}
		else{
			$users        = NewTask::getEmployeeByLogID($login_id,'attendance');
		}
		
        return view('admin.attendance.add', compact('users'));
    }
	
	public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emp_id' => 'required',
            'date' => 'required',
        ]);
		// print_r($request->date); die;
		if(!empty($request->emp_id)){
			$emp_id = $request->emp_id;
			$dates  = $request->date;
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
								Attendance::create($data);
								$i++;
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
	                    }

						return redirect()->route('admin.attendance.index')->with('success', 'Attendance Added Successfully');
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
		if(!empty($request->time)){
			$i = 0;
			$img_array = array();
			if(!empty($request->att_id)){
				$exp_id = array_filter(explode(",", $request->att_id));
				if(count($exp_id) > 0){
					foreach($exp_id as $exp_id_val){
						$att_img = Attendance::where('id', $exp_id_val)->first();
						array_push($img_array, $att_img->image, $att_img->latitude, $att_img->longitude);
					}
				 
					DB::table('attendance')->whereIn('id', $exp_id)->delete();
				}
			}
			//echo '<pre>'; print_r($img_array);die;
			foreach($request->time as $key => $timeVal){ 
				DB::table('attendance')->insertGetId([
					'emp_id' =>  $request->emp_id,
					'date'   =>  $request->date,
					'time' =>  $timeVal,
					'type' =>  $request->type[$key],
					'image' => !empty($img_array[0]) ? $img_array[0] : '',
					'latitude' => !empty($img_array[1]) ? $img_array[1] : '',
					'longitude' => !empty($img_array[1]) ? $img_array[1] : ''
				]);
				$i++;
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
		$login_id        = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$logged_type     = Auth::user()->department_type;
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$attendance = Attendance::with('user')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
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
		
		if($logged_role_id == 21){
			$attendance->WhereHas('user', function ($q) use ($login_id, $logged_type, $logged_role_id) {
                $q->where('role_id', '=', $logged_role_id);
				$q->where('department_type', '=', $logged_type);
				$q->where('role_id', '!=', 1);
				$q->where('status', '=', 1);
				$q->where('id', '!=', $login_id);
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
        return view('admin.attendance.gallery', compact('responseArray'));
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
        $users               = NewTask::getEmployeeByLogID($logged_id,'attendance');
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
		return DataTables::of($responseArray)->make(true);
	}
	
	public function fullattendence(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.fullattendence', compact('allDepartmentTypes','allBranches'));
	}
	
	public function fullAttendanceDetail(Request $request){
		$allDepartmentTypes= $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		$fdate               = $request->fdate;
        $tdate               = $request->tdate;
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
				
	   // echo '<pre>'; print_r($comman_result);die;
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 
			    $valAttRes = User::with('user_details.branch','department')->where('id',$valAtt->emp_id)->first();
				//echo '<pre>'; print_r($valAttRes->department->name);die;
				
				$branch = '';
				/* foreach($valAtt->user->user_branches as $branch_value){ 
					$branch = $branch_value->branch->name;
				} */
				$responseArray[$key]['table_name'] = $valAtt->table_name;
				$responseArray[$key]['id'] = $valAtt->id;				
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = isset($valAttRes->register_id)?$valAttRes->register_id:'';
				$responseArray[$key]['emp_id']      = isset($valAttRes->id)?$valAttRes->id:0;
				$responseArray[$key]['name']        = isset($valAttRes->name)?$valAttRes->name:'';
				$responseArray[$key]['branch']      = isset($valAttRes->user_details->branch->name)?$valAttRes->user_details->branch->name:'';
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
				 $get_u_attendance_new = AttendanceNew::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();				 
				 
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
		
		//$u_attendance = DB::table("attendance")->select('attendance.emp_id','attendance.date','attendance.time','attendance.type')->where('date', '2021-05-01')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		//$u_new_attendance = DB::table("attendance_new")->select('attendance_new.emp_id','attendance_new.date','attendance_new.time','attendance_new.type')->where('date', '2021-05-01')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id')->union($u_attendance)->get();
		
		//$attendane = DB::select("SELECT * FROM (SELECT * FROM attendance WHERE date='2021-05-01' GROUP BY date Order BY date DESC) as X UNION SELECT * FROM attendance_new WHERE date='2021-05-01' GROUP BY date Order BY date DESC");
		
		//$attendane = DB::select("SELECT * FROM (SELECT * FROM attendance WHERE date='2021-05-01' GROUP BY date Order BY date DESC) as X UNION SELECT * FROM attendance_new WHERE date='2021-05-01' GROUP BY date Order BY date DESC");
		
		
		//echo '<pre>'; print_r($responseArray);die;
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
				$responseArray[$key]['branch']      = isset($valAttRes->user_details->branch->name)?$valAttRes->user_details->branch->name:'';
				$responseArray[$key]['designation']      = isset($valAttRes->user_details->degination)?$valAttRes->user_details->degination:'';
				$responseArray[$key]['department']  = isset($valAttRes->department->name) ? $valAttRes->department->name : '';
				$responseArray[$key]['total_hours'] = "00";
				
				
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
						
						
						/* $j = 1;
						
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
						} */
					}
				}
				/* $total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				$responseArray[$key]['total_hours'] = $total_hours ." Hour";
				*/
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
			$new_whereCond .= " AND (userdetails.branch_id = '$branch_id')";
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
		$attendance->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
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
							
					if(count($check_attendance) == 0 && count($check_attendance_new) == 0){
						$user_array['id'] = $user_id;
						$user_array['name'] = $val->name;
						$user_array['email'] = $val->email;
						$user_array['mobile'] = $val->mobile;
						$user_array['branch_name'] = $val->branch_name;
						$user_array['departments_name'] = $val->departments_name;
						$user_array['designation_name'] = $val->designation_name;
						$user_array['date'] = $fdate;
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
				$responseArray[$key]['date'] = $valAtt['date'];
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
			$new_whereCond .= " AND (userdetails.branch_id = '$branch_id')";
		}
		
		$employeeArray= array();
		$usr= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.register_id,users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name,userdetails.degination as designation_name"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
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
							
					if(count($check_attendance) == 0 && count($check_attendance_new) == 0){
						$user_array['id'] = $user_id;
						$user_array['register_id'] = $val->register_id;
						$user_array['name'] = $val->name;
						$user_array['email'] = $val->email;
						$user_array['mobile'] = $val->mobile;
						$user_array['branch_name'] = $val->branch_name;
						$user_array['departments_name'] = $val->departments_name;
						$user_array['designation_name'] = $val->designation_name;
						$user_array['date'] = $fdate;
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
				$responseArray[$key]['date'] = $valAtt['date'];
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
		if(!empty($request->time)){
			$i = 0;
			$img_array = array();
			if(!empty($request->att_id)){
				$exp_id = array_filter(explode(",", $request->att_id));
				if(count($exp_id) > 0){
					foreach($exp_id as $exp_id_val){
						$att_img = $request->tbl::where('id', $exp_id_val)->first();
						array_push($img_array, $att_img->image, $att_img->latitude, $att_img->longitude);
					}
				 
					$request->tbl::whereIn('id', $exp_id)->delete();
				}
			}
			//echo '<pre>'; print_r($img_array);die;
			foreach($request->time as $key => $timeVal){ 
				$request->tbl::insertGetId([
					'emp_id' =>  $request->emp_id,
					'date'   =>  $request->date,
					'time' =>  $timeVal,
					'type' =>  $request->type[$key],
					'image' => !empty($img_array[0]) ? $img_array[0] : '',
					'latitude' => !empty($img_array[1]) ? $img_array[1] : '',
					'longitude' => !empty($img_array[1]) ? $img_array[1] : ''
				]);
				$i++;
			}
			if($i > 0){				
				return redirect()->route("admin.attendance.fullattendence")->with('success', 'Full Attendance Updated Successfully');
			}
		}
		else{
			return redirect()->route("admin.attendance.fullattendence")->with('error', 'Something Went Wrong !');
		}
	}
	
}
