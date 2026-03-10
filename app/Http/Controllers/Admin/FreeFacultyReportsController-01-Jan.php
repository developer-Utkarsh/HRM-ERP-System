<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use Input;
use Excel;
use DB;
use App\Exports\FreeFacultyExport;

class FreeFacultyReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id  = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
		$fdate      = Input::get('fdate');
        $tdate      = Input::get('tdate');

		 
		if(!empty($branch_id) || !empty($faculty_id) || !empty($fdate) || !empty($tdate)){
			$get_data = DB::table('users')
                      ->leftJoin('timetables','users.id','=','timetables.faculty_id')
                      ->leftJoin('userbranches','users.id','=','userbranches.user_id')
                      ->select('users.*')
                      ->whereNull('timetables.faculty_id')
                      ->where([
                      	['users.role_id', '=', 2], 
                      	['users.status', '=', 1],
                      	]);
		if(!empty($faculty_id)){
			$get_data->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_data->where('timetables.cdate', '>=', $fdate);
			$get_data->where('timetables.cdate', '<=', $tdate);
		}
                      
                     
        $get_data = $get_data->get();
                   
		}
		else{		
        $get_data = DB::table('users')
                      ->leftJoin('timetables','users.id','=','timetables.faculty_id')
                      ->select('users.*')
                      ->whereNull('timetables.faculty_id')
                      ->where(['users.role_id'=>2,'users.status'=>1])
                      ->get();
        }
		//echo '<pre>'; print_r($get_data);die;
		
		
        return view('admin.free_faculty_reports.index', compact('get_data'));
    }
	
	public function download_excel()
    {
		$branch_id  = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
		$fdate      = Input::get('fdate');
        $tdate      = Input::get('tdate');
		
		if(!empty($branch_id) || !empty($faculty_id) || !empty($fdate) || !empty($tdate)){
			$get_data = DB::table('users')
                      ->leftJoin('timetables','users.id','=','timetables.faculty_id')
                      ->leftJoin('userbranches','users.id','=','userbranches.user_id')
                      ->select('users.*')
                      ->whereNull('timetables.faculty_id')
                      ->where([
                      	['users.role_id', '=', 2], 
                      	['users.status', '=', 1],
                      	]);
		if(!empty($faculty_id)){
			$get_data->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_data->where('timetables.cdate', '>=', $fdate);
			$get_data->where('timetables.cdate', '<=', $tdate);
		}
                      
                     
        $get_data = $get_data->get();          
		}
		else{
			$get_data = DB::table('users')
                      ->leftJoin('timetables','users.id','=','timetables.faculty_id')
                      ->select('users.*')
                      ->whereNull('timetables.faculty_id')
                      ->where(['users.role_id'=>2,'users.status'=>1])
                      ->get();
		}
		
		
		
        if(!empty($get_data)){
            return Excel::download(new FreeFacultyExport($get_data), 'FreeFacultyData.xlsx'); 

        } else{
            return redirect()->route('admin.free_faculty_reports.index')->with('error', 'Something is wrong');
        }
    }
}
