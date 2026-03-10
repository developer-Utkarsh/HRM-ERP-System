<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccessRequestMail;
use App\Mail\AccessApprovedMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;


class AcademicReportsController extends Controller
{
    public function index(Request $request)
    {
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get();
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}

		$report = DB::table('timetables as t')
					->selectRaw("
						users.name,
						ra.avg_rating,
						ra.avg_nps_4,
						ra.avg_nps_3,
						users.committed_hours,
						users.agreement,
						ROUND(SUM(TIME_TO_SEC(TIMEDIFF(s.end_time, s.start_time))) / 3600, 2) as spent_hrs_1,
						branches.branch_location as city,
						subject.name as subject_name,
						ra.subject_avg
					")
					->leftJoin('users','users.id','t.faculty_id')
					->leftJoin('userbranches','userbranches.user_id','users.id')
					->leftJoin('branches','branches.id','userbranches.branch_id')
					->leftJoin('subject','subject.id','t.subject_id')
					->leftJoin('rating_all_5 as ra','ra.hrms_faculty_id','users.id')					
					->leftJoin('start_classes as s','s.timetable_id','t.id')
					->where('ra.type','a_rating')
					->where('t.cdate', 'LIKE', $smonth . '%')
					->where('t.is_publish','1')
					->where('t.is_deleted','0')
					->where('t.is_cancel',0)
					->where('t.time_table_parent_id',0)
					->groupBy('users.id');
		
		if(!empty($subject_id)){
			$report->where('t.subject_id',$subject_id);
		}
		
		if(!empty($faculty_id)){
			$report->where('users.id',$faculty_id);
		}
		
		if(!empty($location_id)){
			$report->where('branches.branch_location',$location_id);
		}
		
		if(!empty($agreement)){
			if($agreement=='Yes'){
				$report->where('users.agreement','Yes');
			}else if($agreement=='No'){
				$report->where('users.agreement','No');
			}else if($agreement=='Both'){
				$report->where('users.agreement','=','');
			}
		}
		
		$report = $report->get();
        return view('admin.academic.academic_faculty',compact('report','subject','faculty','location'));
    }
	
	public function prediction(Request $request){
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get();
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}

		$report = DB::table('timetables as t')
					->selectRaw("
						users.id,
						users.name,
						users.committed_hours,
						users.agreement,
						ROUND(SUM(TIME_TO_SEC(TIMEDIFF(s.end_time, s.start_time))) / 3600, 2) as spent_hrs_1,
						branches.branch_location as city,
						GROUP_CONCAT(DISTINCT subject.name ORDER BY subject.name SEPARATOR ', ') as subject_name
					")
					->leftJoin('users','users.id','t.faculty_id')
					->leftJoin('userbranches','userbranches.user_id','users.id')
					->leftJoin('branches','branches.id','userbranches.branch_id')
					->leftJoin('subject','subject.id','t.subject_id')				
					->leftJoin('start_classes as s','s.timetable_id','t.id')
					->where('t.cdate', 'LIKE', $smonth . '%')
					->where('t.is_publish','1')
					->where('t.is_deleted','0')
					->where('t.is_cancel',0)
					->where('t.time_table_parent_id',0)
					->groupBy('users.id');
		
		if(!empty($subject_id)){
			$report->where('t.subject_id',$subject_id);
		}
		
		if(!empty($faculty_id)){
			$report->where('users.id',$faculty_id);
		}
		
		if(!empty($location_id)){
			$report->where('branches.branch_location',$location_id);
		}
		
		if(!empty($agreement)){
			if($agreement=='Yes'){
				$report->where('users.agreement','Yes');
			}else if($agreement=='No'){
				$report->where('users.agreement','No');
			}else if($agreement=='Both'){
				$report->where('users.agreement','=','');
			}
		}
		
		$report = $report->get();
		
		$startOfMonth = Carbon::now()->startOfMonth();
		$today = Carbon::now();

		$daysCount = $startOfMonth->diffInDays($today) + 1;
		return view('admin.academic.predication',compact('report','subject','faculty','location','daysCount'));
	}
	
	public function faculty_utilization_dashboard(Request $request){
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get(); 
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}

		$subQuery = DB::table('timetables as t')
			->selectRaw('
				t.faculty_id,
				ROUND(SUM(TIME_TO_SEC(TIMEDIFF(s.end_time, s.start_time))) / 3600, 2) AS total_spent_hours
			')
			->leftJoin('start_classes as s', 's.timetable_id', '=', 't.id')
			->where('t.cdate', 'LIKE', $smonth . '%')
			->where('t.is_publish', '1')
			->where('t.is_deleted', '0')
			->where('t.is_cancel', 0)
			->where('t.time_table_parent_id', 0)
			->groupBy('t.faculty_id');

		$report = DB::table(DB::raw("({$subQuery->toSql()}) as u_spent"))
			->mergeBindings($subQuery) // include bindings from subquery
			->join('users as u', 'u.id', '=', 'u_spent.faculty_id')
			->leftJoin('userbranches as ub', 'ub.user_id', '=', 'u.id')
			->leftJoin('branches as b', 'b.id', '=', 'ub.branch_id')
			->selectRaw('
				b.branch_location,
				COUNT(*) AS total_users,
				SUM(u.committed_hours) AS total_committed_hours,
				SUM(u.committed_hours * 500) AS total_planned_cost,
				SUM(u_spent.total_spent_hours) AS total_spent_hours,
				SUM(u_spent.total_spent_hours * 500) AS total_overrun_cost,
				SUM(CASE WHEN u_spent.total_spent_hours > u.committed_hours THEN 1 ELSE 0 END) AS users_exceeding_commitment,
				SUM(CASE WHEN u_spent.total_spent_hours <= u.committed_hours THEN 1 ELSE 0 END) AS users_within_commitment
			')
			->groupBy('b.branch_location')
			->get();

		
		$startOfMonth = Carbon::now()->startOfMonth();
		$today = Carbon::now();

		$daysCount = $startOfMonth->diffInDays($today) + 1;
		return view('admin.academic.faculty_utilization_dashboard',compact('report','subject','faculty','location','daysCount'));
	}
	
	public function subject_utilization_dashboard(Request $request){
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get();
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}
		
		
		$report = DB::table('timetables as t')
					->selectRaw("
						batch.name as batch_name,
						batch.id as batch_id
					")
					->leftJoin('subject','subject.id','t.subject_id')				
					->leftJoin('batch','batch.id','t.batch_id')				
					->where('t.cdate', 'LIKE', $smonth . '%')
					->where('t.is_publish','1')
					->where('t.is_deleted','0')
					->where('t.is_cancel',0)
					->where('t.time_table_parent_id',0)
					->where('t.subject_id',$subject_id)
					->groupBy('t.batch_id')
					->get();
			
		
		
		$startOfMonth = Carbon::now()->startOfMonth();
		$today = Carbon::now();

		$daysCount = $startOfMonth->diffInDays($today) + 1;
		return view('admin.academic.subject_utilization_dashboard',compact('report','subject','faculty','location','daysCount','subject_id','location_id','agreement','faculty_id','smonth'));
	}
	
	
	public function faculty_plan(Request $request){
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get();
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}
		
		
		$record = DB::table('timetables as t')
					->select([
						'u.name as faculty_name',
						'u.id as faculty_id',
						'u.committed_hours',
						DB::raw('ROUND(SUM(TIME_TO_SEC(TIMEDIFF(s.end_time, s.start_time))) / 3600, 2) as total_spent_hours'),
						DB::raw('ROUND(SUM(TIME_TO_SEC(TIMEDIFF(t.to_time, t.from_time))) / 3600, 2) as total_allotted_hour'),
						DB::raw("(
							SELECT ROUND(SUM(tp.duration) / 60, 2)
							FROM timetable_topic tt
							INNER JOIN topic tp ON tp.id = tt.topic_id
							WHERE tt.timetable_id = t.id AND tt.status = 1
						) as completed_mark_hrs")
					])
					->leftJoin('start_classes as s', 's.timetable_id', '=', 't.id')
					->leftJoin('users as u', 'u.id', '=', 't.faculty_id')
					->leftJoin('userbranches as ub', 'ub.user_id', '=', 'u.id')
					->leftJoin('branches as b', 'b.id', '=', 'ub.branch_id')
					->where('t.is_publish', '1')
					->where('t.is_deleted', '0')
					->where('t.is_cancel', 0)
					->where('t.time_table_parent_id', 0)
					->where('t.cdate', 'like', '2025-06%')
					->groupBy('t.faculty_id', 'u.name', 'u.id');
					
					
		if(!empty($subject_id)){
			$record->where('t.subject_id',$subject_id);
		}
		
		if(!empty($faculty_id)){
			$record->where('users.id',$faculty_id);
		}
		
		if(!empty($location_id)){
			$record->where('branches.branch_location',$location_id);
		}
		
		if(!empty($agreement)){
			if($agreement=='Yes'){
				$record->where('users.agreement','Yes');
			}else if($agreement=='No'){
				$record->where('users.agreement','No');
			}else if($agreement=='Both'){
				$record->where('users.agreement','=','');
			}
		}			
					
		$record = $record->get();
			
		
		
		$startOfMonth = Carbon::now()->startOfMonth();
		$today = Carbon::now();

		$daysCount = $startOfMonth->diffInDays($today) + 1;
		return view('admin.academic.faculty_plan',compact('record','subject','faculty','location','daysCount','subject_id','location_id','agreement','faculty_id','smonth'));
	}
	
	public function proposed_plan(Request $request){
		$subject = DB::table('subject')->where('status',1)->where('is_deleted','0')->get();
		$faculty = DB::table('users')->where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		$location = DB::table('branches')->select('branch_location')->where('status', 1)->where('is_deleted', '0')->groupBy('branch_location')->get();
		
		$subject_id   = $request->subject;
		$faculty_id   = $request->faculty;
		$location_id  = $request->location;
		$agreement 	  = $request->agreement;
		$month 	      = $request->month;
		
		if(!empty($month)){
			$smonth = $month;
		}else{
			$smonth = date('Y-m');
		}

		$report = DB::table('timetables as t')
					->selectRaw("
						users.id,
						users.name,
						users.committed_hours,
						users.agreement,
						ROUND(SUM(TIME_TO_SEC(TIMEDIFF(s.end_time, s.start_time))) / 3600, 2) as spent_hrs_1,
						branches.branch_location as city,
						subject.name as subject_name,t.subject_id")
					->leftJoin('users','users.id','t.faculty_id')
					->leftJoin('userbranches','userbranches.user_id','users.id')
					->leftJoin('branches','branches.id','userbranches.branch_id')
					->leftJoin('subject','subject.id','t.subject_id')				
					->leftJoin('start_classes as s','s.timetable_id','t.id')
					->where('t.cdate', 'LIKE', $smonth . '%')
					->where('t.is_publish','1')
					->where('t.is_deleted','0')
					->where('t.is_cancel',0)
					->where('t.time_table_parent_id',0)
					->groupBy('users.id')
					->OrderBy('t.subject_id','asc');
		
		if(!empty($subject_id)){
			$report->where('t.subject_id',$subject_id);
		}
		
		if(!empty($faculty_id)){
			$report->where('users.id',$faculty_id);
		}
		
		if(!empty($location_id)){
			$report->where('branches.branch_location',$location_id);
		}
		
		if(!empty($agreement)){
			if($agreement=='Yes'){
				$report->where('users.agreement','Yes');
			}else if($agreement=='No'){
				$report->where('users.agreement','No');
			}else if($agreement=='Both'){
				$report->where('users.agreement','=','');
			}
		}
		
		$report = $report->get();
		
		$startOfMonth = Carbon::now()->startOfMonth();
		$today = Carbon::now();

		$daysCount = $startOfMonth->diffInDays($today) + 1;
		return view('admin.academic.proposed_plan',compact('report','subject','faculty','location','daysCount'));
	}
}
