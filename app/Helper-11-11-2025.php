<?php
namespace App;
use DB;
use DateTime;

class Helper{
  
  function __construct()
  {
    
  }

  public function subject_plan_spent_time($batch_id,$subject_id,$faculty_id){
    $planHour=["plan_time"=>"","spent_time"=>"","schedule_time"=>"","remaining_hours"=>""];

    $batch=DB::table('batch')->where('id',$batch_id)->first();
    if(!empty($batch) && $batch->course_planer_enable==1 && $batch->master_planner!=0){
      $no_of_hours=DB::table("course_planner_topic_relation")->selectraw("sum(duration)/60 as no_of_hours")->where("status",1)->where("fstatus",1)->where("course_id",$batch->course_id)->where('subject_id',$subject_id)->first();
    }else if(!empty($batch) && $batch->course_planer_enable==1){
      $no_of_hours=DB::table("topic")->selectraw("sum(duration)/60 as no_of_hours")->where("status",1)->where("course_id",$batch->course_id)->where('subject_id',$subject_id)->first();

    }else{
      $no_of_hours=DB::table('batchrelations')->select('no_of_hours')
      ->where('is_deleted','0')->where('batch_id',$batch_id)
      ->where('subject_id',$subject_id)->first();
    }
	
    if(!empty($no_of_hours)){
		// echo $subject_id;
		
      $no_of_hours=round($no_of_hours->no_of_hours,0);
	  
      /*$class=DB::select("SELECT 
        sum(TIME_TO_SEC(timediff(t.to_time,t.from_time)))/3600 as schedule_hrs,
        DATE_FORMAT(SEC_TO_TIME(sum(TIME_TO_SEC(timediff(sc_1.end_time,sc_1.start_time)))), '%H:%i') as spent_hrs_1,
        DATE_FORMAT(SEC_TO_TIME(sum(TIME_TO_SEC(timediff(sc_2.end_time,sc_2.start_time)))), '%H:%i') as spent_hrs_2,
        count(t.id) as total_class FROM `timetables` as t 
        Left Join start_classes as sc_1 ON sc_1.timetable_id=t.id AND t.time_table_parent_id=0 
        Left Join start_classes as sc_2 ON sc_2.timetable_id=t.time_table_parent_id AND t.time_table_parent_id!=0 
        WHERE t.batch_id=$batch_id AND t.subject_id=$subject_id AND t.faculty_id=$faculty_id AND t.is_deleted= '0' AND t.is_publish= '1' AND t.is_cancel=0");
		
		//print_r($class);
		$class=(object)$class[0];
	  
		// $spent_hrs=round($class->spent_hrs_1??1,0) + round($class->spent_hrs_2??1,0);
		
		$min1 = $this->timeToMinutes($class->spent_hrs_1 ?? '00:00');
		$min2 = $this->timeToMinutes($class->spent_hrs_2  ?? '00:00');
		
		$totalMinutes = $min1 + $min2;
		$spent_hrs = $this->minutesToTime($totalMinutes);
		
		$schedule_hrs=round($class->schedule_hrs??0,0);		
		
		$spent_decimal  = $this->minutesToDecimalHours($totalMinutes);
		$remaining_time = round($no_of_hours - $spent_decimal, 0);
		$planHour=["plan_time"=>$no_of_hours,"spent_time"=>$spent_hrs,'schedule_time'=>$schedule_hrs,'remaining_hours'=>$remaining_time];
		*/
		
		
		$class = DB::select("SELECT 
        sum(TIME_TO_SEC(timediff(t.to_time,t.from_time)))/3600 as schedule_hrs,
        ROUND(SUM(TIME_TO_SEC(TIMEDIFF(t.to_time, t.from_time))) / 3600, 2) AS spent_hrs
        FROM `timetables` as t 
        Left Join start_classes as sc_1 ON sc_1.timetable_id=t.id
        WHERE t.batch_id=$batch_id AND t.subject_id=$subject_id AND t.faculty_id=$faculty_id AND t.is_deleted= '0' AND t.is_publish= '1' AND t.is_cancel=0");
		$class=(object)$class[0];
		$schedule_hrs=round($class->schedule_hrs??0,0);
		
		$remaining_time = round($no_of_hours - $class->spent_hrs, 0);
		
		$planHour=["plan_time"=>$no_of_hours,"spent_time"=>$class->spent_hrs,'schedule_time'=>$schedule_hrs,'remaining_hours'=>$remaining_time];

    }

    return $planHour;
  }
  
	public function timeToMinutes($time) {
		if (!$time || strpos($time, ':') === false) return 0;
		list($h, $m) = explode(':', $time);
		return intval($h) * 60 + intval($m);
	}

	public function minutesToTime($totalMinutes) {
		$hours = floor($totalMinutes / 60);
		$minutes = $totalMinutes % 60;
		return sprintf('%02d:%02d', $hours, $minutes);
	}
  
	public function minutesToDecimalHours($minutes) {
		return $minutes / 60;
	}

	// Convert decimal hours to minutes
	public function decimalHoursToMinutes($decimalHours) {
		return $decimalHours * 60;
	}

  public function get_center_head($branch_id){
    $get_data = DB::table('users')
    ->leftJoin('userbranches','users.id','=','userbranches.user_id')
    ->leftJoin('userdetails','users.id','=','userdetails.user_id')
    ->select('users.name as user_name','users.mobile as mobile')
    ->where('userbranches.branch_id',$branch_id)
    ->where('userdetails.degination','CENTER HEAD')->get();
    $center_heads = "";
    if(count($get_data) > 0){
      foreach($get_data as $center_data){
        $center_heads .= $center_data->user_name."( <a href='tel:<?=$center_data->mobile?>'>".$center_data->mobile."</a> ) ,";
      }

      $center_heads="<b>CH.-</b> ".rtrim($center_heads,',');
    }

    return $center_heads;
  }

  public function class_topics_old($timetable_id){
    $topic=DB::table('timetable_topic as tt')
    ->selectRaw("tt.topic_id,topic.chapter_id,topic.name as topic_name,chapter.name as chapter_name,tt.status")
    ->leftJoin('topic','topic.id','tt.topic_id')
    ->leftJoin('chapter','chapter.id','topic.chapter_id')
    ->where('tt.timetable_id',$timetable_id)
    ->whereIN('tt.status',[1,2])->get();
    //echo json_encode($topic);die();
    if(count($topic)){
      $topic=json_decode(json_encode($topic),true);
      $column="chapter_id";
      $types=array_column($topic,$column);

      $return = [];
      foreach($types as $type) {
        foreach($topic as $key => $value) {
          if($type === $value[$column]) {
            unset($value[$column]);
            $return[$type]["chapter"]=$value["chapter_name"];
            $return[$type]["topic"][] = $value;
            unset($topic[$key]);
          }
        }
      }
      return $return;
    }else{
      $topic=[];
    }
    return $topic;
  }

  public function plannerDetails_old($tt_id,$batch_id=0,$subject_id=0){
    $planners=[];
    if($tt_id!=0){
  		$timetable = DB::table('timetables')
        ->select('timetables.id','timetables.batch_id',
          'batch.course_id','timetables.subject_id','batch.name as batch_name')
        ->leftJoin('batch',      'batch.id','timetables.batch_id')
        ->where('timetables.is_deleted', '0')
        ->where('timetables.is_publish', '1')
        ->where('batch.course_planer_enable',1);
  	  
  		$timetable->whereRAW("(timetables.id=".$tt_id." OR timetables.time_table_parent_id=".$tt_id.")");
  		$timetable = $timetable->get();
  	}else{
  		$timetable  = DB::table('batch')->selectRaw("id as batch_id,name as batch_name,course_id,$subject_id as subject_id")->where('id',$batch_id)->get();
  	}
		
    foreach($timetable as $val){
      $completed_topic= DB::table('timetable_topic as tt')->selectRaw("topic.id,topic.chapter_id,chapter.name as chapter_name,topic.name as topic_name,topic.duration,tt.status,DATE_FORMAT(t.cdate, '%d-%m-%Y') AS cdate,tt.timetable_id")
        ->leftJoin("topic",'topic.id','tt.topic_id')
        ->leftJoin("chapter",'chapter.id','topic.chapter_id')
        ->leftJoin("timetables as t",'t.id','tt.timetable_id')
        ->where("tt.subject_id",$val->subject_id)
        ->where("tt.batch_id",$val->batch_id)
        ->where("topic.course_id",$val->course_id)
        //->groupBy("tt.topic_id")
        ->orderBy('tt.id','asc')
        ->get();

        //echo json_encode($completed_topic);die;
      $completed_topic_array=[];
      $topic_ids=[];
      foreach($completed_topic as $c_t){
        $topic_ids[]=$c_t->id;

        $completed_topic_array[$c_t->id]=$c_t;
      }

      $completed_topic=array_values($completed_topic_array);
      //echo json_encode(array_values($completed_topic_array));die;

      $chapters= DB::table('topic')->selectRaw("topic.id,topic.chapter_id,chapter.name as chapter_name,topic.name as topic_name,topic.duration,null as status")
        ->leftJoin("chapter",'chapter.id','topic.chapter_id')
        ->where("topic.course_id", $val->course_id)
        ->where("topic.subject_id",$val->subject_id)
        ->where("topic.status",1)
        ->where("topic.is_deleted",'0')
        ->whereNOTIN("topic.id",$topic_ids)
        ->orderBy("topic.id",'asc')
        ->get();

      $completed_topic= json_decode(json_encode($completed_topic),true);
      $chapters= json_decode(json_encode($chapters),true);

      //$chapters=array_merge($completed_topic,$chapters);
      $chapters = array_merge($completed_topic,$chapters);

      $planner=[];
      $planner["batch_id"]  =$val->batch_id;
      $planner["subject_id"] =$val->subject_id??'';
      $planner["batch_name"]=$val->batch_name;
      $planner["chapters"]  =$chapters;
      $planners[]=$planner;
    }

    //$planner=json_encode($planner);
    return $planners;
  }


	public function class_topics($timetable_id,$planner_id){
		if($planner_id!=0){
			$topic=DB::table('timetable_topic as tt')
			->selectRaw("tt.topic_id,topic.topic_id as chapter_id,topic.name as topic_name,chapter.name as chapter_name,tt.status")
			->leftJoin('sub_topic_master as topic','topic.id','tt.topic_id')
			->leftJoin('topic_master as chapter','chapter.id','topic.topic_id')
			->where('tt.timetable_id',$timetable_id)
			->whereIN('tt.status',[1,2])->get();
		}else{
			$topic=DB::table('timetable_topic as tt')
			->selectRaw("tt.topic_id,topic.chapter_id,topic.name as topic_name,chapter.name as chapter_name,tt.status")
			->leftJoin('topic','topic.id','tt.topic_id')
			->leftJoin('chapter','chapter.id','topic.chapter_id')
			->where('tt.timetable_id',$timetable_id)
			->whereIN('tt.status',[1,2])->get();
		}
		//echo json_encode($topic);die();
		if(count($topic)){
		  $topic=json_decode(json_encode($topic),true);
		  $column="chapter_id";
		  $types=array_column($topic,$column);

		  $return = [];
		  foreach($types as $type) {
			foreach($topic as $key => $value) {
			  if($type === $value[$column]) {
				unset($value[$column]);
				$return[$type]["chapter"]=$value["chapter_name"];
				$return[$type]["topic"][] = $value;
				unset($topic[$key]);
			  }
			}
		  }
		  return $return;
		}else{
		  $topic=[];
		}
		return $topic;
	}

	public function plannerDetails($tt_id,$batch_id=0,$subject_id=0){
		$planners=[];
		if($tt_id!=0){
			$timetable = DB::table('timetables')
		  ->select('timetables.id','timetables.batch_id',
			'batch.course_id','timetables.subject_id','batch.name as batch_name','batch.master_planner')
		  ->leftJoin('batch','batch.id','timetables.batch_id')
		  ->where('timetables.is_deleted', '0')
		  ->where('timetables.is_publish', '1')
		  ->where('batch.course_planer_enable',1);
		  
			$timetable->whereRAW("(timetables.id=".$tt_id." OR timetables.time_table_parent_id=".$tt_id.")");
			$timetable = $timetable->get();
		}else{
			$timetable  = DB::table('batch')->selectRaw("id as batch_id,name as batch_name,course_id,$subject_id as subject_id,master_planner")->where('id',$batch_id)->get();
		}
		
		$masterPlannerList = $timetable->pluck('master_planner')->filter()->toArray();
		

		$cbatch_id = [];
		$master_planner_id = [];
		foreach($timetable as $val){
			if($val->master_planner != 0){
				$cbatch_id[] = $val->batch_id;
				$master_planner_id[] = $val->master_planner;
			}
			
			if($val->master_planner==0){
				$completed_topic= DB::table('timetable_topic as tt')->selectRaw("topic.id,topic.chapter_id,chapter.name as chapter_name,topic.name as topic_name,topic.duration,tt.status,DATE_FORMAT(t.cdate, '%d-%m-%Y') AS cdate")
				->leftJoin("topic",'topic.id','tt.topic_id')
				->leftJoin("chapter",'chapter.id','topic.chapter_id')
				->leftJoin("timetables as t",'t.id','tt.timetable_id')
				->where("tt.subject_id",$val->subject_id)
				->where("tt.batch_id",$val->batch_id)
				//->groupBy("tt.topic_id")
				->orderBy('tt.id','asc')
				->get();
				
			  $completed_topic_array=[];
			  $topic_ids=[];
			  foreach($completed_topic as $c_t){
				$topic_ids[]=$c_t->id;

				$completed_topic_array[$c_t->id]=$c_t;
			  }

			  $completed_topic=array_values($completed_topic_array);
			  //echo json_encode(array_values($completed_topic_array));die;

			  $chapters= DB::table('topic')->selectRaw("topic.id,topic.chapter_id,chapter.name as chapter_name,topic.name as topic_name,topic.duration,null as status")
				->leftJoin("chapter",'chapter.id','topic.chapter_id')
				->where("topic.course_id", $val->course_id)
				->where("topic.subject_id",$val->subject_id)
				->where("topic.status",1)
				->where("topic.is_deleted",'0')
				->whereNOTIN("topic.id",$topic_ids)
				->orderBy("topic.id",'asc')
				->get();

				$completed_topic= json_decode(json_encode($completed_topic),true);
				$chapters= json_decode(json_encode($chapters),true);

				//$chapters=array_merge($completed_topic,$chapters);
				$chapters = array_merge($completed_topic,$chapters);
				
				$comman_chapters = [];
			}else{
				$completed_topic= DB::table('timetable_topic as tt')->selectRaw("tt.id as tt_id,topic.id,topic.topic_id as chapter_id,chapter.name as chapter_name,topic.name as topic_name,cptr.duration,tt.status,DATE_FORMAT(t.cdate, '%d-%m-%Y') AS cdate")			
				->leftJoin("timetables as t",'t.id','tt.timetable_id')
				->leftJoin("batch",'batch.id','t.batch_id')
				->leftJoin("course_planner_topic_relation as cptr",'cptr.req_id','batch.master_planner')
				->leftJoin("sub_topic_master as topic",'topic.id','tt.topic_id')
				->leftJoin("topic_master as chapter",'chapter.id','topic.topic_id')			
				->where("tt.subject_id",$val->subject_id)
				->where("tt.batch_id",$val->batch_id)		
				->where("cptr.req_id",$val->master_planner)	
				->orderBy('tt.id','asc')
				->get();
				
				
				
				$completed_topic_array=[];
				$topic_ids=[];
				foreach($completed_topic as $c_t){
					$topic_ids[]=$c_t->id;

					$completed_topic_array[$c_t->id]=$c_t;
				}

				$completed_topic=array_values($completed_topic_array);
				$completed_topic= json_decode(json_encode($completed_topic),true);		
				
				//Comman Topic
				$subject_id = $val->subject_id;
				
				$comman_chapters = DB::table('course_planner_topic_relation as cptr')
					->select(
						'topic.id',
						'topic.topic_id as chapter_id',
						'chapter.name as chapter_name',
						'topic.name as topic_name',
						'cptr.duration',
						DB::raw('NULL as status'),
						DB::raw('NULL as cdate')
					)
					->join('sub_topic_master as topic', 'topic.id', '=', 'cptr.sub_topic_id')
					->join('topic_master as chapter', 'chapter.id', '=', 'topic.topic_id')
					->whereIn('cptr.sub_topic_id', function ($query) use ($masterPlannerList) {
						$query->select('sub_topic_id')
							  ->from('course_planner_topic_relation')						  
							  ->whereIn('req_id', $masterPlannerList)
							  ->groupBy('sub_topic_id')
							  ->havingRaw('COUNT(DISTINCT req_id) = ?', [count($masterPlannerList)]);
					})
					->where('cptr.subject_id', $subject_id)
					// ->distinct()
					->groupBy('topic.id', 'topic.topic_id', 'chapter.name', 'topic.name')
					->orderBy('topic.id', 'asc')
					->get();
			
			
					
				$topic_ids2=[];			
				$ctopic = [];
				foreach($comman_chapters as $c_c){
					$topic_ids2[]=$c_c->id;
					
					foreach ($completed_topic as $item) {
						if ($item['id'] == $c_c->id) {
							$c_c->status=$item['status']??0;
							$c_c->cdate  = $item['cdate'] ?? null;
							break;
						}
					}
					$ctopic[]=$c_c;
				}

				$comman_chapters=json_decode(json_encode($ctopic),true);
				//End Comman Topic
										
				$chapters = DB::table('course_planner_topic_relation as cptr')
				->selectRaw("
					topic.id,
					topic.topic_id as chapter_id,
					chapter.name as chapter_name,
					topic.name as topic_name,
					cptr.duration,
					null as status
				")
				->leftJoin('sub_topic_master as topic', 'topic.id', '=', 'cptr.sub_topic_id')
				->leftJoin('topic_master as chapter', 'chapter.id', '=', 'topic.topic_id')
				->where('topic.status', 1)
				// ->whereNotIn('topic.id', $topic_ids)
				->whereNotIn('topic.id', $topic_ids2)
				->where('cptr.req_id', '=', $val->master_planner)
				->where('cptr.subject_id', $subject_id)
				->orderBy('topic.id', 'asc')
				->get();
				
				$ctopic = [];
				foreach($chapters as $c_h){				
					foreach ($completed_topic as $item) {
						if ($item['id'] == $c_h->id) {
							$c_h->status=$item['status']??0;
							break;
						}
					}
					$ctopic[]=$c_h;
				}
				
				// $chapters = array_merge($completed_topic,$chapters);
				
				$chapters= json_decode(json_encode($ctopic),true);	
							
			}
			
			$planner=[];
			$planner["batch_id"]  		=	$val->batch_id;
			$planner["subject_id"] 		=	$val->subject_id??'';
			$planner["batch_name"]		=	$val->batch_name;
			$planner["master_planner"]	=	$val->master_planner;
			$planner["chapters"]  		=	$chapters;
			$planner["comman_chapters"] = 	$comman_chapters;
			$planner["comman_batch_id"] = 	$cbatch_id;
			$planner["master_planner_id"] = $master_planner_id;
			$planner["timetable_id"] 	= 	$val->id??0;
			$planners[]=$planner;
			
		}

		//$planner=json_encode($planner);
		return $planners;
	}
}
