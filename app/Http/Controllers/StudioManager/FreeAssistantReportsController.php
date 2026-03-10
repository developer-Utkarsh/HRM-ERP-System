<?php

namespace App\Http\Controllers\StudioManager;

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

		 
    	$ddd = "111";
		$get_timetables = Timetable::with(['assistant'=>function ($q) use ($ddd) {
			$q->where('role_id', 3);
			$q->where('status', 1);
		}
		,'assistant.user_branches'=>function ($q) use ($branch_id) {
			if(!empty($branch_id)){
				$q->where('branch_id', $branch_id);
			}
		}
		]);
		
		if(!empty($assistant_id)){
			$get_timetables->where(['assistant_id'=>$assistant_id]);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_timetables->where('timetables.cdate', '>=', $fdate);
			$get_timetables->where('timetables.cdate', '<=', $tdate);
		}
		else{
			$get_timetables->where('cdate', date('Y-m-d'));
		}
		
		$available_faculty = [];
		$get_timetables = $get_timetables->get();
		if(count($get_timetables) > 0){
			foreach($get_timetables as $timetableVal){
				$available_faculty[] = $timetableVal->assistant_id;
			}
		}
		
		// echo "<pre>"; print_r($available_faculty); die;
		
		
		$get_data = DB::table('users')
				->leftJoin('userbranches','users.id','=','userbranches.user_id')
				->select('users.*')
				->where(['users.role_id'=>3,'users.status'=>1]);
		if(!empty($available_faculty)){
			$get_data->whereNotIn('users.id', $available_faculty);
		}
		if(!empty($assistant_id)){
			$get_data->where('users.id', $assistant_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		
		$get_data = $get_data->get();
		
		
        return view('studiomanager.free_assistant_reports.index', compact('get_data'));
    }
	
	public function download_excel()
    {
		$branch_id    = Input::get('branch_id');
		$assistant_id = Input::get('assistant_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		
		$ddd = "111";
		$get_timetables = Timetable::with(['assistant'=>function ($q) use ($ddd) {
			$q->where('role_id', 3);
			$q->where('status', 1);
		}
		,'assistant.user_branches'=>function ($q) use ($branch_id) {
			if(!empty($branch_id)){
				$q->where('branch_id', $branch_id);
			}
		}
		]);
		
		if(!empty($assistant_id)){
			$get_timetables->where(['assistant_id'=>$assistant_id]);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_timetables->where('timetables.cdate', '>=', $fdate);
			$get_timetables->where('timetables.cdate', '<=', $tdate);
		}
		else{
			$get_timetables->where('cdate', date('Y-m-d'));
		}
		
		$available_faculty = [];
		$get_timetables = $get_timetables->get();
		if(count($get_timetables) > 0){
			foreach($get_timetables as $timetableVal){
				$available_faculty[] = $timetableVal->assistant_id;
			}
		}
		
		// echo "<pre>"; print_r($available_faculty); die;
		
		
		$get_data = DB::table('users')
				->leftJoin('userbranches','users.id','=','userbranches.user_id')
				->select('users.*')
				->where(['users.role_id'=>3,'users.status'=>1]);
		if(!empty($available_faculty)){
			$get_data->whereNotIn('users.id', $available_faculty);
		}
		if(!empty($assistant_id)){
			$get_data->where('users.id', $assistant_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		
		$get_data = $get_data->get();
		
		
		
        if(!empty($get_data)){
            return Excel::download(new FreeAssistantExport($get_data), 'FreeAssistantData.xlsx'); 

        } else{
            return redirect()->route('studiomanager.free_assistant_reports.index')->with('error', 'Something is wrong');
        }
    }
}
