<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Leave;
use App\LeaveDetail;
use App\NewTask;
use App\User;
use Input;
use Excel;
use App\Exports\LeaveExport;
use Auth;
use DB;
use DateTime;
use App\Attendance;
use App\AttendanceNew;

 
class FacultyLeaveController extends Controller
{
	public function faculty_leave()
    {
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
	 
        $branch_id    = Input::get('branch_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$faculty_id   = Input::get('faculty_id');
		if(empty($fdate) || empty($tdate)){
			$fdate = date('Y-m-d');
			$tdate = date('Y-m-d');
		}
		else{
			
		}
		
		$faculties = DB::table('faculty_leave')
			->select('faculty_leave.*','users.name','users.mobile')
			->leftJoin('users', 'faculty_leave.faculty_id', '=', 'users.id')
			->where('faculty_leave.is_deleted', '0')
			->whereRaw("date(faculty_leave.date) >= '$fdate' and date(faculty_leave.date) <= '$tdate'");
			if(!empty($faculty_id)){
				$faculties->where('faculty_leave.faculty_id', $faculty_id);
			}
		
		if(Auth::user()->id==927){
			$faculties->where('users.department_type', 43);
		}
			
			
		$get_data = $faculties->get();
		
		
		$all_faculty = DB::table('users')->where('role_id',2);		
		if(Auth::user()->id==927){
			$all_faculty->where('department_type', 43);
		}
		
		$all_faculty = $all_faculty->where('is_deleted', '0')->get();
		
        return view('admin.faculty_leave.index', compact('get_data','all_faculty'));
    }
	
	public function faculty_leave_download()
    {
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
	 
        // $branch_id    = Input::get('branch_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$faculty_id   = Input::get('faculty_id');
		if(empty($fdate) || empty($tdate)){
			$fdate = date('Y-m-d');
			$tdate = date('Y-m-d');
		}
		else{
			
		}
		
		$faculties = DB::table('faculty_leave')
			->select('faculty_leave.*','users.name','users.mobile')
			->leftJoin('users', 'faculty_leave.faculty_id', '=', 'users.id')
			->where('faculty_leave.is_deleted', '0')
			->whereRaw("date(faculty_leave.date) >= '$fdate' and date(faculty_leave.date) <= '$tdate'");
			if(!empty($faculty_id)){
				$faculties->where('faculty_leave.faculty_id', $faculty_id);
			}
		$get_data = $faculties->get();
		
        return view('admin.faculty_leave.faculty_leave_pdf', compact('get_data','fdate','tdate'));
    }
	
	public function faculty_leave_add(){
		$login_id               = Auth::user()->id;
		$logged_role_id         = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		$users        = User::where('role_id', 2);
		
		if(Auth::user()->id==927){
			$users->where('department_type', 43);
		}
		
		$users = $users->where('status', 1)->where('is_deleted', '0')->get();
		return view('admin.faculty_leave.add', compact('users'));
	}
	
	public function faculty_leave_add_save(Request $request){
		$login_id = Auth::user()->id;
		$faculty_id 	=	$request->emp_id;
		$from_date 	=	$request->from_date;
		$to_date 	=	$request->to_date;
		$reason 	=	$request->reason;
		$leave_add = 0;
		if(!empty($login_id) && !empty($faculty_id) && !empty($from_date) && !empty($to_date) && !empty($reason)){
			if($to_date < $from_date){
				return response(['status' => false, 'message' => 'From date less then To date'], 200);
				exit;
			}
			$begin = new DateTime($from_date);
			$end = new DateTime($to_date);
			$all = "";
			for($i = $begin; $i <= $end; $i->modify('+1 day')){
				$selected_date = $i->format("Y-m-d");
				$all .=$selected_date .'/';
				
				$check_already = DB::table('faculty_leave')->whereRaw("faculty_id = $faculty_id AND date = '$selected_date' AND is_deleted = '0'")->get();
				if(count($check_already) == 0){
					$leaveArray = array('faculty_id' => $faculty_id, 'date' => $selected_date, 'reason' => $request->reason);
					DB::table('faculty_leave')->insertGetId($leaveArray);
					$leave_add++;
				}
				
			}
			if($leave_add > 0){
				return response(['status' => true, 'message' => 'Leave Added Successfully'], 200);
			}
			else{
				return response(['status' => false, 'message' => 'Leave Already Added'], 200);
			}
		}
		else{
			return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
		}
		
		return response(['status' => false, 'message' => $all], 200);
		
	}
	
	public function faculty_leave_update(Request $request)
    {
		$faculty_leave_id = $request->faculty_leave_id;
		$reason = $request->reason;
		$action = $request->action;
		if($action=='delete'){
			if(!empty($faculty_leave_id)){
				$update=DB::table('faculty_leave')->where('id', $faculty_leave_id)->update([ 'is_deleted' => '1']);
				if($update) {				
					return response(['status' => true, 'message' => 'Delete successfully.'], 200);
				} else {
					return response(['status' => false, 'message' => 'No any change.'], 200);
				}
			}
			else{
				return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
			}
		}
		else{
			if(!empty($faculty_leave_id) && !empty($reason)){
				$update=DB::table('faculty_leave')->where('id', $faculty_leave_id)->update([ 'reason' => $reason]);
				if($update) {				
					return response(['status' => true, 'message' => 'Update successfully.'], 200);
				} else {
					return response(['status' => false, 'message' => 'No any change.'], 200);
				}
			}
			else{
				return response(['status' => false, 'message' => 'Reason required.'], 200);
			}
		}
		
		
    }

 
}
