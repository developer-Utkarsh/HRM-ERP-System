<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use App\TimeSlot;
use App\Subject;
use App\Chapter;
use App\Topic;
use DB;
use App\Studio;
use Input;
use Auth;
use App\User;
use App\StartClass;
use App\Course;
use App\CourseSubjectRelation;
use App\Batch;
use App\Batchrelation;

class TimetablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		if(Auth::user()->role_id == 3){
			$faculty_id = Input::get('faculty_id');
			$fdate = Input::get('fdate');
			$studio_id = Input::get('studio_id');

			if(!empty($fdate)){
				$get_date = $fdate;
			}else{
				$get_date = date('Y-m-d');
			} 

			$timeslots = TimeSlot::get();

			$get_studios = array();
			// echo "<pre>"; print_r($user->user_branches[0]->branch_id); die;
			$get_studios = Studio::with([
				'assistant',
				'timetable' => function($q) use ($get_date,$faculty_id){
					if(!empty($get_date)){
						$q->Where('cdate', $get_date)->where('time_table_parent_id', 0)->where('is_deleted', '0')->orderBy('from_time', 'asc');
					}
					if(!empty($faculty_id)){
						$q->where('faculty_id', $faculty_id)->where('time_table_parent_id', 0)->where('is_deleted', '0')->orderBy('from_time', 'asc');
					}
				},
				'timetable.topic',
			]);
			if(!empty($studio_id)){
				$get_studios->where('id', '=', $studio_id);
			}
			
			if(Auth::user()->user_details->degination == 'STUDIO ASSISTANT'){
				$user = User::with('user_details','user_branches')->where('id', Auth::user()->id)->first();
				$branch_id = 0;
				if(!empty($user->user_branches[0])){
					$branch_id = $user->user_branches[0]->branch_id;
				}
				
				$get_studios->where('assistant_id', Auth::user()->id)->where('branch_id', $branch_id);
			}
			else{
				$user = User::with('user_details','user_branches')->where('id', Auth::user()->id)->first();
				$branch_id = 0;
				if(!empty($user->user_branches[0])){
					$branch_id = $user->user_branches[0]->branch_id;
				}
				
				$get_studios->where('assistant_id', Auth::user()->id)->where('branch_id', $branch_id);
			}
			
			$get_studios->orderBy('id', 'desc');            

			$get_studios = $get_studios->get();      

			return view('admin.timetables.index', compact('timeslots' , 'get_studios'));
		}
		else{ 
			return redirect()->route('admin.timetable.index');
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function store_class(Request $request){

        $timetable_id = $request->timetable_id;
        
        if($request->ajax()){ 

            $inputs = $request->only('timetable_id','start_time','end_time','sc_date','status');

            $inputs['timetable_id'] = $timetable_id;
			

            date_default_timezone_set('Asia/Kolkata');

            $current_time = date('H:i');

            $inputs['start_time'] = $current_time;

            $inputs['sc_date'] = date('Y-m-d'); 

            $inputs['status'] = 'Start Class';            
            			
			
			 
            $startclass = StartClass::create($inputs); 
            
			$get_parent_timetable = DB::table('timetables')->where('time_table_parent_id', $timetable_id)->get();
			if(count($get_parent_timetable) > 0){
				foreach($get_parent_timetable as $parent_value){
					DB::table('start_classes')->insertGetId([
									'timetable_id'       => $parent_value->id,
									'start_time'         => $current_time,
									'sc_date'            =>  date('Y-m-d'),
									'status'             => 'Start Class',
									'created_at'         => date('Y-m-d H:i:s'),
									'updated_at'         => date('Y-m-d H:i:s')
								]);		
				}
			}			

            if($startclass->save()){
                return response(['status' => true, 'message' => 'Class Start.'], 200);
            }else{          
                return response(['status' => false, 'message' => 'Class Not Start'], 200);
            }
        }                
    }
	
	public function multiple_batch_end_class($timetable_id, $current_time, $class_status){
		$get_parent_timetable = DB::table('timetables')->where('time_table_parent_id', $timetable_id)->get();
		if(count($get_parent_timetable) > 0){
			foreach($get_parent_timetable as $parent_value){ 
				DB::table('start_classes')->where('timetable_id', $parent_value->id)->update([
								'end_time' => $current_time,
								'status'   => $class_status
							]);		
			}
		}
		return true;
	}
	
	public function multiple_batch_end_class_partially_offline($timetable_id, $current_time, $class_status,$partially_chapter_topic_id,$partially_status, $remark, $topic_name){
		
		if(!empty($partially_chapter_topic_id)){
			DB::table('timetables')->where('id', $timetable_id)->update([
						'chapter_id' => $timetable_id,
						'topic_name' => $topic_name,
						'remark'     => $remark
					]);	
		
			
			
			DB::table('start_classes')->where('timetable_id', $timetable_id)->update([
						'end_time' => $current_time,
						'status'   => $class_status,
						'partially_offline_status' => $partially_status
					]);	
			
		}
		 
		return true;
	}
	
	public function multiple_batch_partially_end_class($timetable_id, $inputs_timetable, $nxt_class_assign_id){
		$check_parent = DB::table('timetables')->where('time_table_parent_id', $timetable_id)->get();
		//echo '<pre>'; print_r($check_parent);die;		
        if(count($check_parent) > 0){
			foreach($check_parent as $check_parent_row){
				
				$batch_by_topic_res = DB::table('topic')
										->join('batchrelations', 'topic.subject_id', '=', 'batchrelations.subject_id')
										->join('batch', 'batchrelations.batch_id', '=', 'batch.id')
										->select('topic.course_id', 'topic.subject_id', 'topic.chapter_id')
										->where('batch.id', $inputs_timetable['batch_id'])
										->groupBy('batch.id')
										->first();
				$batch_by_topic_insert = DB::table('timetables')->insertGetId([
					'studio_id'            => $inputs_timetable['studio_id'],
					'faculty_id'           => $inputs_timetable['faculty_id'],
					'batch_id'             => $inputs_timetable['batch_id'],
					'course_id'            => $batch_by_topic_res->course_id,
					'subject_id'           => $batch_by_topic_res->subject_id,
					'chapter_id'           => $batch_by_topic_res->chapter_id,
					'topic_id'             => $inputs_timetable['topic_id'],
					'time_table_parent_id' => $nxt_class_assign_id,
					'cdate'                => $inputs_timetable['cdate'],
					'created_at'           => date('Y-m-d H:i:s'),
					'updated_at'           => date('Y-m-d H:i:s')
				]);
			}
		}
		return true;
	}

    public function update_class(Request $request){ //echo '<pre>'; print_r($request->post());die;

        if(empty($request['partially_chapter_topic_id'])){
			return response(['status' => false, 'message' => 'Chapter is required.'], 200);
		}
		if(empty($request['topic_name'])){
			return response(['status' => false, 'message' => 'Topic Name is required.'], 200);
		}
		
		
        $timetable_id               = $request->timetable_id;
        $class_status               = $request->class_status;
        $class_type                 = $request->class_type;
		$partially_chapter_topic_id = $request->partially_chapter_topic_id;
		$topic_name                 = $request->topic_name;
		$remark                     = $request->remark;
		$partially_status           = "Completed";
		if($class_type == "online"){
			
			$end_date = date('Y-m-d');
			
			
			if($request->ajax()){

				$startclass = StartClass::where('timetable_id',$timetable_id)->first(); 

				$inputs = $request->only('timetable_id','start_time','end_time','status');

				$inputs['timetable_id'] = $timetable_id;

				date_default_timezone_set('Asia/Kolkata');

				$current_time = date('H:i');

				$inputs['end_time'] = $current_time;

				$inputs['status'] = $class_status;

				$nxtday = $end_date;
				// $nxtday = date('Y-m-d', strtotime('+1 day'));
				$msg = ""; $msg_status = "";
				$get_timetable_res = Timetable::where('id', $timetable_id)->orWhere('time_table_parent_id', $timetable_id)->get();

				if(count($get_timetable_res) > 0){
					foreach($get_timetable_res as $get_timetable_data){
						$from_time_id = TimeSlot::where('time_slot',$get_timetable_data->from_time)->first();
						$get_from_time_id = $from_time_id->id;
						$to_time_id = TimeSlot::where('time_slot',$get_timetable_data->to_time)->first();
						$get_to_time_id = $to_time_id->id;

						$get_studio_timetable = Timetable::where('studio_id', $get_timetable_data->studio_id)->where('faculty_id', $get_timetable_data->faculty_id)->where('cdate', $nxtday)->get();

						if(count($get_studio_timetable) > 0){              

							$from_time2 = [];
							$to_time2 = [];

							foreach($get_studio_timetable as $value){
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)->first();
								
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)->first();
								$to_time2[] = $to_time1->id;
							}

							$chk_condition = 'false';          

							for($i=0;$i<count($from_time2);$i++)
							{
								if($get_from_time_id>=$from_time2[$i] && $get_from_time_id<=$to_time2[$i])
								{
									$chk_condition = 'true';
								}else if($get_to_time_id>=$from_time2[$i] && $get_to_time_id<=$to_time2[$i]){
									$chk_condition = 'true';
								}
							}

							if($chk_condition == 'true'){ 
							 $get_branch_studio = DB::table('timetables')
													->select('studios.name as studios_name', 'branches.name as branches_name')
													->join('studios', 'studios.id', '=', 'timetables.studio_id')
													->join('branches', 'branches.id', '=', 'studios.branch_id')
													->where('timetables.id', $get_timetable_data->id)
													->first();
							
								$startclass->update($inputs);
								//self::multiple_batch_end_class($get_timetable_data->id, $current_time, $class_status);
								self::multiple_batch_end_class_partially_offline($get_timetable_data->id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status, $remark, $topic_name);
								//$msg = "Class ".$class_status." successfully. But Class Already Exists In Branch Name - '".$get_branch_studio->branches_name."' AND Studio Name - ".$get_branch_studio->studios_name;
								$msg = $class_status." successfully";
								$msg_status = true;
								/* return response(['status' => false, 'message' => 'Class Already Exists In Branch Name - '.$get_branch_studio->branches_name.' AND Studio Name - '.$get_branch_studio->studios_name], 200); */

							}
							else{
								
								if($class_type=="online"){
									$startclass->update($inputs);
									
									$class_msg = "";
									if($class_status == 'Partially'){
										self::multiple_batch_end_class_partially_offline($get_timetable_data->id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status, $remark, $topic_name);
										$class_msg = 'Partially Class Ended';
									}
									else{
										//self::multiple_batch_end_class($get_timetable_data->id, $current_time, $class_status);
										self::multiple_batch_end_class_partially_offline($get_timetable_data->id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status, $remark, $topic_name);
										$class_msg = 'Class Ended';
									}
									$msg = "$class_msg Successfully.";
									$msg_status = true;
									//return response(['status' => true, 'message' => "$class_msg Successfully."], 200);
								}
								
							}

						}
						else{
							if($class_type == "online"){ 
								$startclass->update($inputs);
								
								$class_msg = "";
								if($class_status == 'Partially'){
									self::multiple_batch_end_class_partially_offline($get_timetable_data->id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status, $remark, $topic_name);
									$class_msg = 'Partially Class Ended';
								}
								else{
									//self::multiple_batch_end_class($get_timetable_data->id, $current_time, $class_status);
									self::multiple_batch_end_class_partially_offline($get_timetable_data->id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status, $remark, $topic_name);
									$class_msg = 'Class Ended';
								}
								$msg = "$class_msg Successfully.";
								$msg_status = true;
								//return response(['status' => true, 'message' => "$class_msg Successfully."], 200);
							}
						}
					}
					
					
					return response(['status' => $msg_status, 'message' => $msg], 200);
					
					
				}
				else{
					return response(['status' => false, 'message' => "Something is wrong"], 200);
				}
				
			}             
		}
    }
	
	public function check_timetable_topic($topic_id){
		$assigntopic_id = Topic::where('id', '>', $topic_id)->min('id');
		$check_timetable = Timetable::where('topic_id', $assigntopic_id)->first();
		if(!empty($check_timetable)){
			return self::check_timetable_topic($assigntopic_id);
		}
		else{
			return $assigntopic_id;
		}
	}
	
	public function get_course(Request $request)
    { 

        $batch_id = $request->batch_id;

        $course = Batch::with('course')->select('course_id')->where('id', $batch_id)->get();

        //print_r($course->toArray()); die;
        echo $res = "<input type='hidden' name='batch_id' value='" . $batch_id . "'>";

        if (!empty($course))
        {
            $res = "";
            foreach ($course as $key => $value)
            {
                if (!empty($value->course->name) && !empty($value->course->name))
                {
                    $res .= "<option value='" . $value->course->id . "'>" . $value->course->name . "</option>";
                }
            }
			if(empty($res)){
				$res = "<option value=''> Select Course </option>";
			}
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Course Not Found </option>";
            die();
        }
    }
	
	public function get_batch_subject(Request $request){ 

        $batch_id = $request->batch_id;
        
        $subjects = Batchrelation::with('subject')->where('batch_id', $batch_id)->get();

        if (!empty($subjects)) {                         
            echo $res = "<option value=''> Select Subject </option>";
            foreach ($subjects as $key => $value) {
                if(!empty($value->subject->name) && !empty($value->subject->name)){
                    echo $res = "<option value='". $value->subject->id ."'>" . $value->subject->name ."</option>";
                }
            }
            exit();
        } else {
            echo $res = "<option value='No data'> Subject Not Found </option>";
            die();
        }
    }
	
	public function get_chapter(Request $request)
    {
        $subject_id = $request->subject_id;
        $course_id = $request->course_id;

        $chapters = Chapter::where('subject_id', $subject_id)->where('course_id', $course_id)->get();

        if (!empty($chapters))
        {
            echo $res = "<option value=''> Select Chapter </option>";
            foreach ($chapters as $key => $value)
            {
                if (!empty($value->name) && !empty($value->name))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Chapter Not Found </option>";
            die();
        }
    }
	
	
	public function get_topic(Request $request)
    {

        $batch_id   = $request->batch_id;
		$course_id  = $request->course_id;
		$subject_id = $request->subject_id;
		$chapter_id = $request->chapter_id;
        $topics = [];
        //$topics = Topic::where('chapter_id', $chapter_id)->get();
		
		$chk_topics = DB::table('timetables')
					->join('start_classes', 'timetables.id', '=', 'start_classes.timetable_id')
					->select('timetables.topic_id')
					->where([['timetables.batch_id', '=', $batch_id],['timetables.course_id', '=', $course_id],['timetables.subject_id', '=', $subject_id],['timetables.chapter_id', '=', $chapter_id],['start_classes.status', '=', 'End Class']])->get();
		
        $expected_ids = [];		
		if(count($chk_topics) > 0){
			foreach($chk_topics as $chk_topics_value){
				$expected_ids[] = $chk_topics_value->topic_id;
				//echo '<pre>'; print_r($topics);die;
			}
		}
		
		$get_topics = Topic::where('chapter_id', '=', $chapter_id)->whereNotIn('id', $expected_ids)->get();
		if(count($get_topics) > 0){
			foreach($get_topics as $topics_value){
				$temp['id']   = $topics_value->id;
				$temp['name'] = $topics_value->name;
				$topics[] = $temp;
			}
		}
         
        if (count($topics) > 0)
        {
            echo $res = "<option value=''> Select Topic </option>";
            foreach ($topics as $key => $value)
            {   
                if (!empty($value['id']) && !empty($value['name']))
                {
                    echo $res = "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Topic Not Found </option>";
            die();
        }
    }
	
	public function store_batch_accrd_subject($batch_accord_subject, $requests, $timetable_id, $batch_accord_chapter, $batch_accord_topic, $batch_accord_course, $batch_accord_subjects, $class_type){
		
		if(count($batch_accord_subject) > 0){
			foreach($batch_accord_subject as $accord_batch_key=>$accord_batch_val){
				
				if(!empty($accord_batch_val) && !empty($batch_accord_chapter[$accord_batch_key]) && !empty($batch_accord_topic[$accord_batch_key]) && !empty($batch_accord_course[$accord_batch_key]) && !empty($batch_accord_subjects[$accord_batch_key])){					
					$batch_by_topic_insert = DB::table('timetables')->insertGetId([
						'studio_id'            => $requests['studio_id'],
						'assistant_id'         => $requests['assistant_id'],
						'faculty_id'           => $requests['faculty_id'],
						'batch_id'             => $accord_batch_val,
						'course_id'            => $batch_accord_course[$accord_batch_key],
						'subject_id'           => $batch_accord_subjects[$accord_batch_key],
						'chapter_id'           => $batch_accord_chapter[$accord_batch_key],
						'topic_id'             => $batch_accord_topic[$accord_batch_key],
						'from_time'            => $requests['from_time'],
						'to_time'              => $requests['to_time'],
						'time_table_parent_id' => $timetable_id,
						'cdate'                => $requests['cdate'],
						'class_type'           => $class_type,
						'created_at'           => date('Y-m-d H:i:s'),
						'updated_at'           => date('Y-m-d H:i:s')
					]);	
				}
			}
		}

	}

	public function partially_end_class_data(Request $request)
    {
        $timetable_id = $request->timetable_id;
		
		$partially_html = view('admin.timetables.partially_end_class_data', compact('timetable_id'))->render();
		
		echo $partially_html;
		die;
    }
	
}
