<?php

namespace App\Http\Controllers\StudioManager;

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
        //$tdate      = Input::get('tdate');

		/* $ddd = "111";
		$get_timetables = Timetable::with(['faculty'=>function ($q) use ($ddd) {
			$q->where('role_id', 2);
			$q->where('status', 1);
		}
		,'faculty.user_branches'=>function ($q) use ($branch_id) {
			if(!empty($branch_id)){
				$q->where('branch_id', $branch_id);
			}
		}
		]);
		
		if(!empty($faculty_id)){
			$get_timetables->where(['faculty_id'=>$faculty_id]);
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
				$available_faculty[] = $timetableVal->faculty_id;
			}
		}
		
		// echo "<pre>"; print_r($available_faculty); die;
		
		
		$get_data = DB::table('users')
				->leftJoin('userbranches','users.id','=','userbranches.user_id')
				->select('users.*')
				->where(['users.role_id'=>2,'users.status'=>1]);
		if(!empty($available_faculty)){
			$get_data->whereNotIn('users.id', $available_faculty);
		}
		if(!empty($faculty_id)){
			$get_data->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		
		$get_data = $get_data->get(); */
		
		
		$faculty_record = DB::table('users')
							->select('users.*')
							->leftJoin('userbranches','users.id','=','userbranches.user_id')
							->where([['role_id', '=', '2'], ['status', '=', '1']]);
		
		if(!empty($faculty_id)){
			$faculty_record->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$faculty_record->where('userbranches.branch_id', $branch_id);
		}
		
		$faculty_record = $faculty_record->orderBy('name')->get();
		
        return view('studiomanager.free_faculty_reports.index', compact('faculty_record','fdate'));
    }
	
	public function download_excel()
    {
		$branch_id  = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
		$fdate      = Input::get('fdate');
        //$tdate      = Input::get('tdate');
		
		/* $ddd = "111";
		$get_timetables = Timetable::with(['faculty'=>function ($q) use ($ddd) {
			$q->where('role_id', 2);
			$q->where('status', 1);
		}
		,'faculty.user_branches'=>function ($q) use ($branch_id) {
			if(!empty($branch_id)){
				$q->where('branch_id', $branch_id);
			}
		}
		]);
		
		if(!empty($faculty_id)){
			$get_timetables->where(['faculty_id'=>$faculty_id]);
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
				$available_faculty[] = $timetableVal->faculty_id;
			}
		}
		
		// echo "<pre>"; print_r($available_faculty); die;
		
		
		$get_data = DB::table('users')
				->leftJoin('userbranches','users.id','=','userbranches.user_id')
				->select('users.*')
				->where(['users.role_id'=>2,'users.status'=>1]);
		if(!empty($available_faculty)){
			$get_data->whereNotIn('users.id', $available_faculty);
		}
		if(!empty($faculty_id)){
			$get_data->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$get_data->where('userbranches.branch_id', $branch_id);
		}
		
		$get_data = $get_data->get(); */
		
		$faculty_record = DB::table('users')
							->select('users.*')
							->leftJoin('userbranches','users.id','=','userbranches.user_id')
							->where([['role_id', '=', '2'], ['status', '=', '1']]);
		
		if(!empty($faculty_id)){
			$faculty_record->where('users.id', $faculty_id);
		}
		if(!empty($branch_id)){
			$faculty_record->where('userbranches.branch_id', $branch_id);
		}
		
		$faculty_record = $faculty_record->orderBy('name')->get();
		
		if(count($faculty_record) > 0){
			$user_free_array = array();
			$i = 0;
			foreach ($faculty_record as $key => $faculty_record_val){   //echo '<pre>'; print_r($value);die;
				
				$check_timetable = DB::table('timetables');
						
				if(!empty($fdate)){
					$check_timetable->where('cdate', $fdate);
				}
				else{
					$check_timetable->where('cdate', date('Y-m-d'));
				}
				$check_timetable = $check_timetable->where('faculty_id', $faculty_record_val->id)->where('is_deleted', '0')->orderBy('from_time')->get();
				
				$f_time = '';$t_time = '';$free_array = array();$ii = 0;
				if(count($check_timetable) > 0){
					
					foreach($check_timetable as $keys=>$check_timetable_value){ 
					
					$free_faculty_details['name']   = $faculty_record_val->name;
					$free_faculty_details['email']  = $faculty_record_val->email;
					$free_faculty_details['mobile'] = $faculty_record_val->mobile;
					
					if($keys == 0 && date('H:i A', strtotime($check_timetable_value->from_time)) == '06:00 AM'){
						$f_time =  date('h:i A', strtotime($check_timetable_value->to_time));
						continue;	
					}
					elseif($keys == 0){  
						$f_time = '06:00 AM'; 
					}
					$t_time = date('h:i A', strtotime($check_timetable_value->from_time));
					
					
					$free_faculty_details['from_time'] = $f_time;
					$free_faculty_details['to_time']   = $t_time;
					$f_time =  date('h:i A', strtotime($check_timetable_value->to_time));
					
					$free_array[$ii] = $free_faculty_details;
					$ii++;
					}
					if($f_time != '11:00 PM'){
						$free_faculty_details['from_time'] = $f_time;
						$free_faculty_details['to_time'] = '11:00 PM';
						$free_array[$ii] = $free_faculty_details;
					}
				}
				else{
					$free_faculty_details['name']       = $faculty_record_val->name;
					$free_faculty_details['from_time']  = '06:00 AM';
					$free_faculty_details['to_time']    = '11:00 PM';
					$free_array[$ii] = $free_faculty_details;
				}
				
				$user_free_array[$i] = $free_array;
				$i++;
			}
		}
		//echo '<pre>'; print_r($user_free_array);die;		
        if(!empty($user_free_array)){
            return Excel::download(new FreeFacultyExport($user_free_array), 'FreeFacultyData.xlsx'); 

        } else{
            return redirect()->route('studiomanager.free_faculty_reports.index')->with('error', 'Something is wrong');
        }
    }
}
