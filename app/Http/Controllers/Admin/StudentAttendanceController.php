<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Excel;
use Input;
use Validator;
use DataTables;
use DB;
use Auth;
use Image;


class StudentAttendanceController extends Controller
{
	public function index(){		
		$logged_id  = Auth::user()->id;	
		$role_id    = Auth::user()->role_id;	

		$Reg_No	=	Input::get('Reg_No');
		
		$attendance = DB::table('student_attendance')->where('status','0');
		
		if(!empty($Reg_No)){
			$attendance->where('reg_no',$Reg_No);
		}
		
		$attendance = $attendance->get();
		
		return view('admin.student-attendance.index', compact('attendance')); 
	}
	
}
