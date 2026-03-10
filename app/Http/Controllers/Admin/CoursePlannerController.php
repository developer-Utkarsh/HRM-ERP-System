<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use DB;
use Excel;
use Auth;
use App\Exports\CourseBySubjectExport;
use App\Timetable;
use DateTime;


class CoursePlannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function batchReports(Request $request)
    {
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		$erp_category  		= 	Auth::user()->course_category;
		$designation  		=	Auth::user()->user_details->degination;
		
		
		$batch_report = DB::table('course_planer_batch_report as cp')
						->select('cp.*','course.name as course_name','batch.name as batch_name','batch.erp_course_id')
						->leftjoin('course','course.id','cp.course_id')						
						->leftjoin('batch','batch.id','cp.batch_id');
        $batch_report->where('course_planer_enable',1);
        
        if(!empty($request->branch_location)){
           $batch_report->where("batch.branch",$request->branch_location);
        }
		
		if($designation=="CATEGORY HEAD"){
			if(!empty($erp_category)){
				$erp_category = explode(',',$erp_category);
				$course_category = "'".implode("','",$erp_category)."'";
				$batch_report->whereRaw("batch.category IN (".$course_category.")");
			}
		}

        if(!empty($request->course_id)){
           $batch_report->where("cp.course_id",$request->course_id);
        }

        if(!empty($request->batch_id) && count($request->batch_id)){
           $batch_report->whereIN("cp.batch_id",$request->batch_id);
        }
		
		if(!empty($request->erp_id)){
           $batch_report->where("batch.erp_course_id",$request->erp_id);
        }

        if(!empty($request->indicator)){
            if($request->indicator==10){
             $batch_report->where("cp.possible_delay",">=",10);
            }else if($request->indicator==-10){
             $batch_report->where("cp.possible_delay","<=",-10);
            }else{
                $batch_report->where("cp.possible_delay",">",-10);
                $batch_report->where("cp.possible_delay","<",10);
            }
        }

        if(!empty($request->percentage_batch_complete)){
           $batch_report->where("cp.percentage_batch_complete",">=",$request->percentage_batch_complete);
        }

        if(!empty($request->possible_delay)){
           $batch_report->where("cp.possible_delay",">=",$request->possible_delay);
        }

        if(!empty($request->batch_status)){
           $batch_report->where("cp.batch_status",$request->batch_status);
        }
						
		$batch_report = $batch_report->paginate(10);
		$pageNumber = 1;
		if(isset($page)){ 
			$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
        return view('admin.report.course_planner.batch',compact('batch_report','pageNumber','params','designation','erp_category'));       
    }

    public function batchSubjectReports(Request $request,$batch_id)
    {
    	if(empty($request->cron)){
           $batch=DB::table("course_planer_batch_report")->where("batch_id",$batch_id)->orderby('percentage_batch_complete','desc')->first();
           $report=json_decode($batch->batch_json);
          // echo json_encode($report);
          return response(["status"=>true,"batch"=>$batch,"report"=>$report]);
        }

        $tt=DB::table('timetables')
        ->select('timetables.batch_id')
        ->where('cdate',date('Y-m-d'))
        //->where('timetables.batch_id',2452)
        ->leftjoin('batch','batch.id','timetables.batch_id')
        ->leftjoin('course_planer_batch_report as cpr','cpr.batch_id','timetables.batch_id')
        ->where('batch.course_planer_enable',1)
        ->whereRAW("(cpr.updated_at<='".date("Y-m-d")."' OR cpr.updated_at is null)")
        ->groupby('timetables.batch_id')->limit(5)->get();
        foreach($tt as $val){
            $batch_id=$val->batch_id;

            //$batch_id=767;
            //$batch_id = $request->batch_id;
            //echo $batch_id;die('ddd');
            $batch=$report=[];
            
            $subject=DB::table("timetables as t")
            ->selectraw("t.faculty_id,t.subject_id,s.name as subject,users.name as faculty,users.id as faculty_id,t.batch_id,batch.course_id,batch.start_date,batch.end_date")
            ->leftJoin("subject as s","s.id","t.subject_id")
            ->leftJoin("users","users.id","t.faculty_id")
            ->leftJoin("batch","batch.id","t.batch_id")
            ->where("t.batch_id",$batch_id)
            ->where("t.is_publish",'1')
            ->where("t.is_cancel",0)
            ->where("t.is_deleted","0")
            //->groupby("t.faculty_id")
            ->groupby("t.subject_id")
            ->get();
             
            $batch_duration_hours=0;
            if(count($subject)){
                $topic_all=DB::table("topic")->selectraw("sum(duration)/60 as hrs")
                    ->where("course_id",$subject[0]->course_id)->where("status",1)->first();
                $batch_duration_hours=$topic_all->hrs;
            }

            $sunday=$holiday=$batch_duration=$batch_remaining
            =$batch_status=0;
            if(count($subject)){
                $batch_duration=strtotime($subject[0]->end_date)-strtotime($subject[0]->start_date);
                

                $batch_duration=round($batch_duration / (60 * 60 * 24));

                $batch_remaining=strtotime($subject[0]->end_date)-strtotime(date("Y-m-d"));
                $batch_remaining=round($batch_remaining / (60 * 60 * 24));

                $sunday=$this->getSunday(date("Y-m-d"),$subject[0]->end_date);
                $holiday=$this->holiday($batch_id);

                $batch=[
                 "batch_id"=>$batch_id,
                 "course_id" =>$subject[0]->course_id,
                 "start_date"=>$subject[0]->start_date,
                 "end_date"  =>$subject[0]->end_date,
                 "sunday"    =>$sunday,
                 "holiday"   =>$holiday,
                 "batch_duration_days"=>$batch_duration,
                 "batch_remaining_days"=>$batch_remaining,
                 "batch_duration_hours"=>$batch_duration_hours,
                 "batch_schedule_hours"=>0,
                 "batch_spent_time_hours"=>0,
                 "batch_topic_completed_hours"=>0
                ];
            }

            if(count($batch)==0){
                return response(["status"=>false,"batch"=>$batch,"report"=>$report]);
            }

            foreach($subject as $key => $val){
                /*$class=DB::table("timetables as t")
                ->selectraw("sum(TIME_TO_SEC(timediff(t.to_time,t.from_time)))/3600 as schedule_hrs,
                    sum(TIME_TO_SEC(timediff(sc_1.end_time,sc_1.start_time)))/3600 as spent_hrs_1,sum(TIME_TO_SEC(timediff(sc_2.end_time,sc_2.start_time)))/3600 as spent_hrs_2,count(t.id) as total_class")
                //->where("t.faculty_id",$val->faculty_id)
                ->where("t.subject_id",$val->subject_id)
                ->where("t.batch_id",$val->batch_id)
                ->leftJoin("start_classes as sc_1","sc_1.timetable_id","t.id")
                ->leftJoin("start_classes as sc_2","sc_2.timetable_id","t.time_table_parent_id")
                ->where('t.is_publish', '1')
                ->where('t.is_cancel', 0)
                ->where('t.is_deleted', '0')
                ->first();

                print_r($class);*/

                $class=DB::select("SELECT 
                    sum(TIME_TO_SEC(timediff(t.to_time,t.from_time)))/3600 as schedule_hrs,
                    sum(TIME_TO_SEC(timediff(sc_1.end_time,sc_1.start_time)))/3600 as spent_hrs_1,
                    sum(TIME_TO_SEC(timediff(sc_2.end_time,sc_2.start_time)))/3600 as spent_hrs_2,
                    count(t.id) as total_class
                    FROM `timetables` as t
                    Left Join start_classes as sc_1 ON sc_1.timetable_id=t.id AND t.time_table_parent_id=0
                    Left Join start_classes as sc_2 ON sc_2.timetable_id=t.time_table_parent_id AND t.time_table_parent_id!=0
                    WHERE
                    t.batch_id=$val->batch_id
                    AND t.subject_id=$val->subject_id
                    AND t.is_deleted= '0' 
                    AND t.is_publish= '1' 
                    AND t.is_cancel=0 ");
                $class=(object)$class[0];
                //print_r($class);die();
               
               $topic_ids=DB::table("timetable_topic as tt")
                ->selectraw("tt.topic_id")
                ->where("tt.batch_id",$val->batch_id)
                ->where("tt.subject_id",$val->subject_id)
                ->whereIN("tt.status",[1,2])
                ->groupby("tt.topic_id")
                ->get();
                $tt_ids=[];
                foreach($topic_ids as $tid){
                 $tt_ids[]=$tid->topic_id;
                }

                $topic_completed=DB::table("topic")->selectraw("sum(duration)/60 as hrs")
                ->where("status",1)->where("course_id",$val->course_id)->whereIn("id",$tt_ids)->first();
				
				

                $topic_all=DB::table("topic")
                ->selectraw("sum(duration)/60 as hrs")
                ->where("course_id",$val->course_id)
                ->where("subject_id",$val->subject_id)
                ->where("status",1)->first();

                $topic_all=round($topic_all->hrs??0,0);
                $topic_completed_hrs=round($topic_completed->hrs??0,0);

                $spent_hrs=round($class->spent_hrs_1??1,0) + round($class->spent_hrs_2??1,0);;
                $schedule_hrs=round($class->schedule_hrs??1,0);
                
                $topic_all=$topic_all>0?$topic_all:1;
                //$subject_compelete= round(($spent_hrs*100)/$topic_all,2);
                $subject_compelete= round(($topic_completed_hrs*100)/$topic_all,2);

                $batch_status= ($subject_compelete==100?1:2);
                
                $total_class=$class->total_class??1;

                $avg_class_duration=round($spent_hrs/$total_class,0);

                //echo $avg_class_duration;

                $avg_class_duration=$avg_class_duration>1?$avg_class_duration:1;

                //die('ddd');

                $remaining_topic_duration=$topic_all-$topic_completed_hrs;

                /* formula
                $required_days= ($remaining_topic_duration* ($spent_time/$topic_conpleted_hrs))/$avg_class_duration;
                */
                
                $required_days=$possible_delay=0;
                $expected_end_date="-";
                if($topic_all>1){
                    $factor=($topic_completed_hrs!=0?($spent_hrs/$topic_completed_hrs):$spent_hrs);

                    $required_days= (($remaining_topic_duration*$factor)/$avg_class_duration);
                    $required_days=round($required_days,0);
                    
                    $possible_delay=$required_days-($batch_remaining-$holiday-$sunday);
                    $possible_delay=round($possible_delay,0);

                    $expected_end_date=date("Y-m-d",strtotime($val->end_date." +".$possible_delay." day"));
                }

                $indicator= $possible_delay>=10?"Red":($possible_delay<=-10?"Blue":"Green");

                $val->total_subject_duration   =$topic_all;
                $val->schedule_hrs     =$schedule_hrs;
                $val->spent_hrs        =$spent_hrs;
                $val->topic_completed_hrs=$topic_completed_hrs;
                $val->subject_compelete=$subject_compelete;
                $val->avg_class_duration=$avg_class_duration;
                $val->required_days    =$required_days;

                $val->possible_delay   =$possible_delay;
                $val->expected_end_date=$expected_end_date;
                $val->indicator        =$indicator;
                $report[]=$val;
            }
            
            $avg_class_duration=$required_days=0;
            foreach($report as $val){
                //$batch["batch_duration_hours"]+=(int)$val->total_subject_duration;
                $batch["batch_schedule_hours"]+=(int)$val->schedule_hrs;
                $batch["batch_spent_time_hours"]+=(int)$val->spent_hrs;
                $batch["batch_topic_completed_hours"]+=(int)$val->topic_completed_hrs;

                $avg_class_duration+=$val->avg_class_duration;
                $required_days     +=$val->required_days;

                $avg_class_duration=round($avg_class_duration,0);
                $required_days     =round($required_days,0);
            }
            
            $count_report=count($report)==0?1:count($report);
            $batch["avg_class_duration"]=round($avg_class_duration/$count_report,0);
            $batch["required_days"]=round($required_days/$count_report,0);

            $batch["batch_topic_completed_hours"]=$batch["batch_topic_completed_hours"]!=0?$batch["batch_topic_completed_hours"]:1;

            $required_days=(($batch["batch_duration_hours"]-$batch["batch_topic_completed_hours"])*($batch["batch_spent_time_hours"]/$batch["batch_topic_completed_hours"]))/$batch["avg_class_duration"];
            $batch["required_days"]=round($required_days,0);


            $possible_delay=$required_days-($batch['batch_remaining_days']-$holiday-$sunday);
            $possible_delay=round($possible_delay,0);
            $expected_end_date=date("Y-m-d",strtotime($batch['end_date']." +".$possible_delay." day"));
            $indicator= $possible_delay>=10?"Red":($possible_delay<=-10?"Blue":"Green");

            $batch["possible_delay"]=$possible_delay;
            $batch["expected_end_date"]=$expected_end_date;
            $batch["indicator"]=$indicator;
            $batch["batch_status"]=$batch_status;

            $batch["batch_duration_hours"]=$batch["batch_duration_hours"]!=0?$batch["batch_duration_hours"]:1;
            
            $batch["percentage_batch_complete"]
            =round(($batch["batch_topic_completed_hours"]*100)/$batch["batch_duration_hours"],0);


            $batch_report=DB::table("course_planer_batch_report")->where("batch_id",$batch_id)->first();

            $batch['batch_json']=json_encode($report);
            if(empty($batch_report)){
                DB::table("course_planer_batch_report")->insert($batch);
            }else{
                //additions_planner
                $course_id=$batch_report->course_id;
                $additions = DB::select("SELECT SUM(duration)/60 as duration FROM `topic` WHERE `course_id` = $course_id AND `created_at` > (SELECT DATE(`created_at`) + INTERVAL 1 DAY FROM `topic` WHERE `course_id` = $course_id ORDER BY `created_at` ASC LIMIT 1)");
                $additions_planner = round($additions[0]->duration ?? 0, 0);
                $batch['additions_planner'] = $additions_planner > 0 ? $additions_planner : $batch_report->additions_planner;
                ///end

               DB::table("course_planer_batch_report")->where("batch_id",$batch_id)->update($batch);
            }

        }       
    }

    public function getSunday($start,$end){
        if($start>$end){
            return 0;
        }

        $start = new DateTime($start);
        $end = new DateTime($end);
        $days = $start->diff($end, true)->days;

        $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
        return $sundays;
    }

    public function holiday($batch_id){
        $batch=DB::table("batch")->where("id",$batch_id)->first();
        
        $holiday=DB::table("batch_holidays")
        ->where("status",1)
        ->where("date",">=",date("Y-m-d"));
        
        $where="(type='location-wise' AND location='".$batch->branch."')";
        $where.=" OR (type='course-wise' AND course_id=".$batch->course_id.")";
        $where.=" OR (type='batch-wise' AND JSON_CONTAINS(batch_id,'\"".$batch_id."\"','$'))";

        $holiday=$holiday->whereRAW("(".$where.")")->get();
        //echo count($holiday);
        return count($holiday);
    }
	
	public function issue_raise_reports(Request $request){
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		$erp_category  		= 	Auth::user()->course_category;
		$designation  		=	Auth::user()->user_details->degination;
		
		$issue_report = DB::table('start_classes')
						->select('start_classes.*','timetables.cdate','branches.name as branch_name','course.name as course_name','batch.name as batch_name','subject.name as subject_name','users.name as faculty_name','assistant.name as assistant_name')
						->leftjoin('timetables','timetables.id','start_classes.timetable_id')
						->leftjoin('branches','branches.id','timetables.branch_id')						
						->leftjoin('course','course.id','timetables.course_id')						
						->leftjoin('batch','batch.id','timetables.batch_id')						
						->leftjoin('subject','subject.id','timetables.subject_id')						
						->leftjoin('users','users.id','timetables.faculty_id')						
						->leftjoin('users as assistant','assistant.id','timetables.assistant_id')
						->where('start_classes.topic_issue', '!=', '')
						->orderby('start_classes.created_at','desc');

        if(!empty($request->branch_location)){
           $issue_report->where("batch.branch",$request->branch_location);
        }

        if(!empty($request->course_id)){
           $issue_report->where("batch.course_id",$request->course_id);
        }

        if(!empty($request->batch_id) && count($request->batch_id)){
           $issue_report->whereIN("timetables.batch_id",$request->batch_id);
        }
		
		
		if($designation=="CATEGORY HEAD"){
			if(!empty($erp_category)){
				$erp_category = explode(',',$erp_category);
				$course_category = "'".implode("','",$erp_category)."'";
				$issue_report->whereRaw("batch.category IN (".$course_category.")");
			}
		}

        if(!empty($request->assistant_id)){
           $issue_report->where("timetables.assistant_id",$request->assistant_id);
        }

        if(!empty($request->fdate) && !empty($request->tdate)){
            $fdate=date("Y-m-d",strtotime($request->fdate));
            $tdate=date("Y-m-d",strtotime($request->tdate));
            $issue_report->where("timetables.cdate",">=",$fdate);
            $issue_report->where("timetables.cdate","<=",$tdate);
        }

        if(!empty($request->remark)){
			if($request->remark=='Yes'){
				$issue_report->whereRAW("start_classes.topic_issue != ''");
			}else if($request->remark=='No'){
				$issue_report->whereRAW("start_classes.topic_issue = ''");
			}
        }



        // echo $issue_report->toSql();
						
		$issue_report = $issue_report->paginate(10);
		$pageNumber = 1;
		if(isset($page)){ 
			$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		return view('admin.report.course_planner.faculty_topic_issue',compact('issue_report','pageNumber','params'));  
	}	
}
