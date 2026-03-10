<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use Input;
use Excel;
use DB;
use App\Exports\FreeAssistantExport;

class FreeAssistantReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
		    $fdate        = Input::get('fdate');
        $tdate        = Input::get('tdate');

		 
    		if(!empty($branch_id) || !empty($assistant_id) || !empty($fdate) || !empty($tdate)){
    			$get_data = DB::table('users')
                          ->leftJoin('timetables','users.id','=','timetables.assistant_id')
                          ->leftJoin('userbranches','users.id','=','userbranches.user_id')
                          ->select('users.*')
                          ->whereNull('timetables.assistant_id')
                          ->where([
                          	['users.role_id', '=', 3], 
                          	['users.status', '=', 1],
                          	]);
    		if(!empty($assistant_id)){
    			$get_data->where('users.id', $assistant_id);
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
                          ->leftJoin('timetables','users.id','=','timetables.assistant_id')
                          ->select('users.*')
                          ->whereNull('timetables.faculty_id')
                          ->where(['users.role_id'=>3,'users.status'=>1])
                          ->get();
            }
    		//echo '<pre>'; print_r($get_data);die;
		
		
        return view('admin.free_assistant_reports.index', compact('get_data'));
    }
	
	public function download_excel()
    {
		$branch_id    = Input::get('branch_id');
    $assistant_id = Input::get('assistant_id');
		$fdate        = Input::get('fdate');
    $tdate        = Input::get('tdate');
		
		if(!empty($branch_id) || !empty($assistant_id) || !empty($fdate) || !empty($tdate)){
			$get_data = DB::table('users')
                      ->leftJoin('timetables','users.id','=','timetables.assistant_id')
                      ->leftJoin('userbranches','users.id','=','userbranches.user_id')
                      ->select('users.*')
                      ->whereNull('timetables.assistant_id')
                      ->where([
                      	['users.role_id', '=', 3], 
                      	['users.status', '=', 1],
                      	]);
		if(!empty($assistant_id)){
			$get_data->where('users.id', $assistant_id);
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
                      ->leftJoin('timetables','users.id','=','timetables.assistant_id')
                      ->select('users.*')
                      ->whereNull('timetables.assistant_id')
                      ->where(['users.role_id'=>3,'users.status'=>1])
                      ->get();
		}
		
		
		
        if(!empty($get_data)){
            return Excel::download(new FreeAssistantExport($get_data), 'FreeAssistantData.xlsx'); 

        } else{
            return redirect()->route('admin.free_assistant_reports.index')->with('error', 'Something is wrong');
        }
    }
}
