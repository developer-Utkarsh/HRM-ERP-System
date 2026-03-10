<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\AttendanceNew;
use App\Attendance;
use App\Leave;
use Auth;
use Hash;
use Input;
use Image;
use DB;
use App\NewTask;

use App\Exports\TodayattendanceExport;
use Excel;
use App\ApiNotification;

class AdminController extends Controller  
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		//echo "Working on this. <a href='employees'>Click Here</a>"; die;
		// echo phpinfo(); die;
        $logged_id    = Auth::user()->id;
		$logged_role_id   = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		$userArray    = array();
		$userArray2   = array();
		$supervisorId = array();
		$present      = 0;
		$i            = 0;

		/*if($logged_role_id == 29 || $logged_role_id == 24){
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get();
		} 
		else if($logged_role_id == 21){
			$employees = User::with('user_details','role')->where([['department_type', '=', $logged_department_type],['role_id', '!=', 1],['status', '=', 1],['id', '!=', $logged_id]])->orderBy('id','desc')->get();
		}
		else{
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->whereRaw('(id = '.$logged_id.' OR supervisor_id LIKE  \'%"'.$logged_id.'"%\')')->orderBy('id','desc')->get(); 
		}
		
		
		if(!empty($employees)){
			foreach($employees as $key=>$value){
				if(!in_array($value->id,$supervisorId)){
					$supervisorId[] = $value->id;
					$userArray[$i]['id']  = $value->id;
					$userArray[$i]['name']    = $value->name;
					$userArray[$i]['register_id'] = $value->register_id;
					$userArray[$i]['role_name'] = $value->role->name;
					$i++;
				}
				
			}
		}*/

		if($logged_role_id == 29){
			$userArray = NewTask::getEmployeeByLogID($logged_id,'approved-emp');
			$userArray2 = NewTask::getEmployeeByLogID($logged_id,'all-employee');
		}
		else if($logged_role_id == 24){
			$userArray = NewTask::getEmployeeByLogID($logged_id,'department-emp');
			$userArray2 = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else if($logged_role_id == 21){
			$userArray = NewTask::getEmployeeByLogID($logged_id,'location_wise');
			// $userArray2 = NewTask::getEmployeeByLogID($logged_id);
			$userArray2 = $userArray;
			// echo count($userArray2);; die;
		}
		else if($logged_role_id == 2){
			$userArray = NewTask::getEmployeeByLogID($logged_id);
			$userArray2 = $userArray;
		}
		else{
			$userArray = NewTask::getEmployeeByLogID($logged_id);
			
		}
		
		$absentEmployee	=	count($userArray);
		$total_users = count($userArray);
		// echo '<pre>'; print_r($total_users);die;
		
		$attendance    = Attendance::selectRaw("id,emp_id,'App' as table_name")->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		$attendance->where('date', '=',date('Y-m-d'));
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$employeeArray   = explode(',',$usr);
		$attendance->whereIn('emp_id', $employeeArray);
		
		$attendance_new = AttendanceNew::selectRaw("id,emp_id,'RFID' as table_name")->orderBy('date', 'desc')->groupBy('emp_id');
		$attendance_new->where('date', '=',date('Y-m-d'));
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$employeeArray   = explode(',',$usr);
		$attendance_new->whereIn('emp_id', $employeeArray);
		
		$comman = $attendance_new->union($attendance);
		$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   //->whereRaw("comman.emp_id != $logged_id")
						   ->groupBy('comman.emp_id')
						   ->get();
		//echo '<pre>'; print_r($comman_result);die;
		
		
		//Total Present	and absent notification		
		if($logged_role_id == 21){
			$inputs['sender_id']   = 	$logged_id;  
			$inputs['receiver_id'] = 	json_encode(array($logged_id));
			$inputs['type']        = 	'Present Employee';    
			$inputs['title']       = 	'Present Employee';  
			$inputs['description'] = 	'Today total persent employees '.count($comman_result). 'AND absent employees '.$absentEmployee;  
			$inputs['date']        = 	date('Y-m-d H:i:s');        

			$persent_notification  = 	ApiNotification::create($inputs);
			$persent_notification->save();
		}
		
		//Time Shift
		$time_shift_count = 0;

		$time_shift_attendance_new = AttendanceNew::select('attendance_new.id','attendance_new.emp_id','attendance_new.date','attendance_new.time','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance_new.time, '00:10:00') as m_time"))->leftJoin('userdetails','userdetails.user_id', '=', 'attendance_new.emp_id')->orderBy('attendance_new.date', 'desc')->groupBy('attendance_new.emp_id');
		
		$time_shift_attendance_new->where('attendance_new.date', '=',date('Y-m-d'));
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance_new->whereIn('attendance_new.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance_new->where('attendance_new.type', 'In');
		$time_shift_attendance_new->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance_new.time, '00:10:00')");
		
		
		
		$time_shift_attendance = Attendance::select('attendance.id','attendance.emp_id','attendance.date','attendance.time','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance.time, '00:10:00') as m_time"))->leftJoin('userdetails','userdetails.user_id', '=', 'attendance.emp_id')->orderBy('date', 'ASC')->groupBy('emp_id');
	
		$time_shift_attendance->where('attendance.date', '=',date('Y-m-d'));
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance->whereIn('attendance.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance->where('attendance.type', 'In');
		$time_shift_attendance->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance.time, '00:10:00')");
			
		$late_comman = $time_shift_attendance_new->union($time_shift_attendance);
		$late_comman_result = DB::table(DB::raw("({$late_comman->toSql()}) as late_comman"))
						   ->mergeBindings($late_comman->getQuery())
						   ->groupBy('late_comman.emp_id')
						   ->groupBy('late_comman.date')
						   ->get();
						   
		if(count($late_comman_result) > 0){
			$time_shift_count = count($late_comman_result);
		}				   
		
		//Leave Details
		$leave_date = date('Y-m-d');
		$leave_data = Leave::with(['leave_details' => function ($q) use ($leave_date){
							$q->where('date', $leave_date);
							//$q->where('type', 'Full Day');
						}]);
		
		if (!empty($leave_date)){
			$leave_data->WhereHas('leave_details', function ($q) use ($leave_date) {
				$q->where('date', $leave_date);
				//$q->where('type', 'Full Day');
			});
		} 
		
		$leaveEmployeeArray  = array();
		$leave_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$leaveEmployeeArray  = explode(',',$leave_usr); 
		$leave_data->whereIn('emp_id', $leaveEmployeeArray);
		//$leave_data->whereIn('emp_id', [5308,5309]);
		
		$leave_data = $leave_data->get();
		
		//echo '<pre>'; print_r($total_users);die;
		$active = 0; $inactive = 0; $total = 0;
		/*if($logged_role_id == 29 || $logged_role_id == 24){
			$active   = User::where('status', 1)->get()->count();
			$inactive = User::where('status', 0)->get()->count();
			$total    = User::get()->count(); 
			
		}	
		else{
			$active   = User::where('status', 1)->whereRaw('(id = '.$logged_id.' OR supervisor_id LIKE  \'%"'.$logged_id.'"%\' )')->get()->count();
			$inactive = User::where('status', 0)->whereRaw('(id = '.$logged_id.' OR supervisor_id LIKE  \'%"'.$logged_id.'"%\' )')->get()->count();
			$total    = User::whereRaw('(id = '.$logged_id.' OR supervisor_id LIKE  \'%"'.$logged_id.'"%\' )')->get()->count();
		}*/
		
		//echo json_encode($userArray2);die;
		
		$active   = count(array_filter($userArray2, function($act){ return $act['status'] === 1; })); 
		$inactive = count(array_filter($userArray2, function($act){ return $act['status'] === 0; }));
		$total    = count($userArray2);
		
		
		//Leave Calculation	
		$empID  =	$request->empCode;
		if($empID!=""){
			$newUID	=	$empID;
		}else{
			$newUID	=	$logged_id;
		}
		
		$user_id = $newUID;
		$url = "http://15.207.232.85/index.php/api/users/leave_types";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Content-Type: application/x-www-form-urlencoded",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = "user_id=$user_id";

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		$pending_leaves =  json_decode($resp);
		
		$employeeArray = array();
		$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$employeeArray   = explode(',',$usr);
		
		$current_month = date("m");
		$current_day = date("d");
		
		$b_day    = User::select('users.*')->leftJoin('userdetails','userdetails.user_id','=','users.id')->whereRaw("(MONTH(userdetails.dob) = $current_month and DAY(userdetails.dob) = $current_day)")->whereIn('users.id', $employeeArray)->where('users.status', '1')->where('users.is_deleted', '0')->get()->count();
		
		
		$work_anniversary    = User::select('users.*')->leftJoin('userdetails','userdetails.user_id','=','users.id')->whereRaw("(MONTH(userdetails.joining_date) = $current_month and DAY(userdetails.joining_date) = $current_day)")->whereIn('users.id', $employeeArray)->get()->count();
		
        return view('admin.index', compact('active','inactive','total','comman_result','total_users','time_shift_count','leave_data','pending_leaves','b_day','work_anniversary')); 
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profile() {        
        return view('admin.updateprofile');
    }

    public function profile_update(Request $request) {

        $user = User::findOrFail(Auth::user()->id);        
        //$user->name = $request->name;
        if (Input::hasfile('image')){
            $this->RemoveProfileImage($user->image);
            $user->image = $this->uploadProfileImage(Input::file('image'));
        }
        //$user->mobile = $request->mobile;

        if ($user->update()){
            return redirect()->route('admin.profile')->with('success', 'Profile Updated Successfully.');
        } else {
            return redirect()->route('admin.profile')->with('error', 'Profile Was Not Updated');
        }
    }

    public function change_password() {        
        return view('admin.updatepassword');
    }


    public function update_password(Request $request) {        
        $user = User::findOrFail(Auth::user()->id);
        $input = $request->only('password');
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {

                if ($request->newpass == $request->renewpass) {
                    $input['password'] = Hash::make($request->newpass);
                    $input['last_password_update'] = date('Y-m-d');
                } else {
                    return redirect()->route('admin.password')->with('error', 'Confirm Password Does not match.');
                }
            } else {
                return redirect()->route('admin.password')->with('error', 'Current Password Does not match.');
            }
        }
        
        $user->update($input);
        return redirect()->route('admin.password')->with('success', 'Admin Password Updated Successfully.');       
    }

    /*Image Upload*/
    function uploadProfileImage($image){
        $extension = $image->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $newImagename = Image::make($image);  
        $newImagename->save(public_path('adminprofile/' . $filename)); 
        return $filename;

    }

    
    /*Remove Image*/
    public function RemoveProfileImage($image) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'adminprofile' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $image;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }
	
	
	public function download_excel()
	{	
		
		$totalPresent   =	Input::get('totalPresent');
        $totalAbesent 	=	Input::get('totalAbesent');
			
		$user 		= 	User::where('is_deleted', '0')->orderBy('id', 'desc');
		
		if(!empty($totalPresent)){
			$user->where('id', '=', $totalPresent);
        }
		
		if(!empty($totalAbesent)){
			$user->where('id', '=', $totalAbesent);
        }
		
		$get_data 	= 	$user->get();
		
		if(count($get_data) > 0){
            return Excel::download(new TodayattendanceExport($get_data), 'users.xlsx');
        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !456');
        }
		
	}
}
