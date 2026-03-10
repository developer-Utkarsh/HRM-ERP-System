<?php

namespace App\Http\Controllers\Admin;

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
use App\Exports\FacultyMonthlyHoursExport;
use DateTime;

class ClassTypeWiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	
        $branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		
		$whereCond  = ' 1=1';
		
		 
		if(!empty($selectFromDate) && !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectFromDate .'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
		}
		 	  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.online_class_type',DB::raw('COUNT(timetables.online_class_type) as classCount'))
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
		$get_faculty =	$get_faculty->groupBy('timetables.online_class_type')->get();				  
        // echo "<pre>"; print_r($get_faculty); die;
		 
		
		return view('admin.class_type_wise.index', compact('get_faculty','selectFromDate','selectToDate','status','branch_id'));
        
    }
	
	
	

   public function download_pdf() {
		$branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		$whereCond  = ' 1=1';
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectFromDate .'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
		}
		
		$get_faculty = DB::table('timetables')
						  ->select('timetables.online_class_type')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
		$get_faculty =	$get_faculty->groupBy('timetables.online_class_type')->get();		
	    
		return view('admin.class_type_wise.pdf_html', compact('get_faculty','selectFromDate','selectToDate', 'status'));
		 
   }
	
}
