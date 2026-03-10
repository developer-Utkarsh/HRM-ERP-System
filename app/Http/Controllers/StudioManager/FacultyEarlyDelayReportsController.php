<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Timetable;
use Input;
use Auth;
use App\User;
use DB;
use Excel;
use App\Exports\FacultyEarlyDelayExport;
use DateTime;

class FacultyEarlyDelayReportsController extends Controller
{
	
    public function index()
    {
    	
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $selectToDate   = Input::get('tdate');
        $branch_location = Input::get('branch_location');
        $status  		= Input::get('status');
		
		$whereCond  = ' 1=1';
		
		if(!empty($selectFromDate) || !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
		if(!empty($faculty_id) && count($faculty_id) > 0){
			$whereCond .= " AND timetables.faculty_id IN('".implode("','",$faculty_id)."')";
		}
		
		if(!empty($branch_location)){
			$whereCond .= " AND branches.branch_location = '".$branch_location."'";
		}
		
						  
		$search    = Input::get('search');
		$get_faculty=array();
		if(!empty($search)){	  
			$get_faculty = DB::table('timetables')
							  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
							  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
							  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
							  ->leftJoin('branches','branches.id','timetables.branch_id')
							  ->whereRaw($whereCond)
							  ->where('timetables.time_table_parent_id', '0')
							  ->where('timetables.is_deleted', '0')
							  ->where('timetables.is_publish', '1');
			$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
							  ->groupBy('timetables.faculty_id')
							  ->get();				  
	        //echo "<pre>"; print_r($get_faculty); die;
		}
		
		return view('studiomanager.faculty_early_delay_reports.index', compact('get_faculty','selectFromDate','selectToDate'));
        
    }
	
	public function download_excel()
    {
    	$faculty_id     = Input::get('faculty_id');	
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $delay_type   = Input::get('delay_type');
		
		$whereCond  = ' 1=1';
		
		if(!empty($selectFromDate) || !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
		if(!empty($faculty_id)){
			$whereCond .= " AND timetables.faculty_id IN('".str_replace(",","','",$faculty_id)."')";
		}
		
		
				  
		$get_faculty = DB::table('timetables')
		  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
		  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
		  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
		  ->whereRaw($whereCond)
		  ->where('timetables.time_table_parent_id', '0')
		  ->where('timetables.is_deleted', '0')
		  ->where('timetables.is_publish', '1');
		  if(Auth::user()->role_id == 3){
			$get_faculty->where('timetables.assistant_id', Auth::user()->id);
		  }
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  ->get();		
		
		if(count($get_faculty) > 0){  
			return Excel::download(new FacultyEarlyDelayExport($get_faculty,$selectFromDate,$selectToDate,$delay_type), 'FacultyEarlyDelayReportData.xlsx'); 

		} else{
			return redirect()->back()->with('error', 'Something Went Wrong!');
		}
		 
	}
}
