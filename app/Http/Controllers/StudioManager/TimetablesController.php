<?php

namespace App\Http\Controllers\StudioManager;

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
        $faculty_id = Input::get('faculty_id');
        $fdate = Input::get('fdate');
        $studio_id = Input::get('studio_id');
        $branch_id = Input::get('branch_id');

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
		if(!empty($branch_id)){
			$get_studios->where('branch_id', '=', $branch_id);
		}
		$get_studios->orderBy('id', 'desc');            

		$get_studios = $get_studios->get();      

        return view('studiomanager.timetables.index', compact('timeslots' , 'get_studios'));
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
	
	public function multiple_batch_end_class_partially_offline($timetable_id, $current_time, $class_status,$partially_chapter_topic_id,$partially_status){
		if(!empty($partially_chapter_topic_id)){
			foreach($partially_chapter_topic_id as $keyTTid=>$valTT){
				if($keyTTid > 0){
					$chapterTopic = explode('-',$valTT);
					if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
					
						DB::table('timetables')->where('id', $keyTTid)->update([
								'chapter_id' => $chapterTopic[0],
								'topic_id'   => $chapterTopic[1]
							]);	
					}
					
					
					DB::table('start_classes')->where('timetable_id', $keyTTid)->update([
								'end_time' => $current_time,
								'status'   => $class_status,
								'partially_offline_status' => $partially_status[$keyTTid]
							]);	
				}
			}
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

        $timetable_id = $request->timetable_id;

        $class_status = $request->class_status;
        $class_type = $request->class_type;
		if(!empty($request->end_date) || $class_type == "offline"){
			
			$end_date = $request->end_date;
			
			if($class_type=="offline"){
				$end_date = "1970-01-01";
				if($class_status == 'Partially'){
					$partially_chapter_topic_id = $request->partially_chapter_topic_id;
					$partially_status = $request->partially_status;
					if(empty($partially_chapter_topic_id) || empty($partially_status)){
						return response(['status' => false, 'message' => 'Please select all fields.'], 200);
					}
					foreach($partially_chapter_topic_id as $keyP=>$val){
						if(empty($partially_chapter_topic_id[$keyP]) || empty($partially_status[$keyP]) ){
							return response(['status' => false, 'message' => 'Please select all fields'], 200);
						}
					}
				}
				
			}
			
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
				
				$get_timetable_data = Timetable::where('id', $timetable_id)->first();

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
						$startclass->update($inputs);
						self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
						
						return response(['status' => false, 'message' => 'Class Already Exists'], 200);

					}
					else{
						
						if($class_type=="offline"){
							$startclass->update($inputs);
							
							$class_msg = "";
							if($class_status == 'Partially'){
								self::multiple_batch_end_class_partially_offline($timetable_id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status);
								$class_msg = 'Partially Class Ended';
							}
							else{
								self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
								$class_msg = 'Class Ended';
							}
							return response(['status' => true, 'message' => "$class_msg Successfully."], 200);
						}
						else{
							$get_timetable = Timetable::where('id', $timetable_id)->first();

							$inputs_timetable = $request->only('studio_id','faculty_id','batch_id','course_id','subject_id','chapter_id','topic_id','from_time','to_time','cdate');
		
							if($class_status == 'Partially'){

								$get_next_assign_id = $get_timetable->topic_id;

								$inputs_timetable['studio_id'] = $get_timetable->studio_id;
								$inputs_timetable['faculty_id'] = $get_timetable->faculty_id;
								$inputs_timetable['batch_id'] = $get_timetable->batch_id;
								$inputs_timetable['course_id'] = $get_timetable->course_id;
								$inputs_timetable['subject_id'] = $get_timetable->subject_id;
								$inputs_timetable['chapter_id'] = $get_timetable->chapter_id;
								$inputs_timetable['topic_id'] = $get_next_assign_id;
								$inputs_timetable['from_time'] = $get_timetable->from_time;
								$inputs_timetable['to_time'] =$get_timetable->to_time;
								$inputs_timetable['cdate'] = $nxtday; 
								
								$nxt_class_assign = Timetable::create($inputs_timetable);
								
								if($startclass->update($inputs)){
									self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
									
									self::multiple_batch_partially_end_class($timetable_id, $inputs_timetable, $nxt_class_assign->id);
								
									return response(['status' => true, 'message' => 'Partially Class Ended Successfully.'], 200);
								}else{          
									return response(['status' => false, 'message' => 'Class Not Ended'], 200);
								}

							}
							else{
								$topic = Topic::where('id', $get_timetable->topic_id)->first();
								if($topic){
									// $assigntopic_id = Topic::where('id', '>', $topic->id)->min('id');
									$assigntopic_id = self::check_timetable_topic($topic->id);
									$chk_topic_assign = Topic::where('id', $assigntopic_id)->first();
									if($chk_topic_assign){
										$chk_course = Course::where('id', $chk_topic_assign->course_id)->first();
										if($chk_course){
											$chk_course_subject_relation = CourseSubjectRelation::where('course_id', $chk_topic_assign->course_id)->where('subject_id', $chk_topic_assign->subject_id)->first();
											if($chk_course_subject_relation){
												$chk_subject = Subject::where('id', $chk_topic_assign->subject_id)->first();
												if($chk_subject){
													$chk_chapter = Chapter::where('course_id', $chk_topic_assign->course_id)->where('subject_id', $chk_topic_assign->subject_id)->first();
													if($chk_chapter){
														// $get_next_assign_data = Topic::where('course_id', $chk_course->id)->where('subject_id', $chk_subject->id)->where('chapter_id', $chk_chapter->id)->first();
														$get_next_assign_id = $assigntopic_id;
													}else{
														$startclass->update($inputs);
														self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
														return response(['status' => false, 'message' => 'Topic Id Not Found'], 200);
													}
												}else{
													$startclass->update($inputs);
													self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
													return response(['status' => false, 'message' => 'Chapter Not Found'], 200);
												}
											}else{
												$startclass->update($inputs);
												self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
												return response(['status' => false, 'message' => 'Subject Not Found'], 200);
											}
										}else{
											$startclass->update($inputs);
											self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
											return response(['status' => false, 'message' => 'Course Not Found'], 200);
										}
									}else{
										$startclass->update($inputs); 
										self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
										return response(['status' => false, 'message' => 'Topic Next id Not Found'], 200);
									}
								}else{
									$startclass->update($inputs);
									self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
									return response(['status' => false, 'message' => 'Topic Not Found'], 200);
								}

								$inputs_timetable['studio_id'] = $get_timetable->studio_id;
								$inputs_timetable['faculty_id'] = $get_timetable->faculty_id;
								$inputs_timetable['batch_id'] = $get_timetable->batch_id;
								$inputs_timetable['course_id'] = $get_timetable->course_id;
								$inputs_timetable['subject_id'] = $get_timetable->subject_id;
								//$inputs_timetable['chapter_id'] = $get_timetable->chapter_id;
								$inputs_timetable['chapter_id'] = $chk_topic_assign->chapter_id;
								$inputs_timetable['topic_id'] = $get_next_assign_id;
								$inputs_timetable['from_time'] = $get_timetable->from_time;
								$inputs_timetable['to_time'] =$get_timetable->to_time;
								$inputs_timetable['cdate'] = $nxtday;

								$nxt_class_assign = Timetable::create($inputs_timetable);
								

								if($startclass->update($inputs)){
									self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
									
									self::multiple_batch_partially_end_class($timetable_id, $inputs_timetable, $nxt_class_assign->id);
									
									$inputs_tt['studio_id']    = $get_timetable->studio_id;
									$inputs_tt['assistant_id'] = $get_timetable->assistant_id;
									$inputs_tt['faculty_id']   = $get_timetable->faculty_id;
									$inputs_tt['from_time']    = $get_timetable->from_time;
									$inputs_tt['to_time']      = $get_timetable->to_time;
									$inputs_tt['cdate']        = $nxtday;
									
									Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs_tt, $nxt_class_assign->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects, 'online');
								
									return response(['status' => true, 'message' => 'Class Ended Successfully.'], 200);
								}else{          
									return response(['status' => false, 'message' => 'Class Not Ended'], 200);
								}
							}
							
						}
					}

				}
				else{
					if($class_type == "offline"){ 
						$startclass->update($inputs);
						
						$class_msg = "";
						if($class_status == 'Partially'){
							self::multiple_batch_end_class_partially_offline($timetable_id, $current_time, $class_status, $partially_chapter_topic_id,$partially_status);
							$class_msg = 'Partially Class Ended';
						}
						else{
							self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
							$class_msg = 'Class Ended';
						}
						return response(['status' => true, 'message' => "$class_msg Successfully."], 200);
					}
					else{ 
						$get_timetable = Timetable::where('id', $timetable_id)->first();

						$inputs_timetable = $request->only('studio_id','faculty_id','batch_id','course_id','subject_id','chapter_id','topic_id','from_time','to_time','cdate');

						if($class_status == 'Partially'){ 

							$get_next_assign_id = $get_timetable->topic_id;

							$inputs_timetable['studio_id'] = $get_timetable->studio_id;
							$inputs_timetable['faculty_id'] = $get_timetable->faculty_id;
							$inputs_timetable['batch_id'] = $get_timetable->batch_id;
							$inputs_timetable['course_id'] = $get_timetable->course_id;
							$inputs_timetable['subject_id'] = $get_timetable->subject_id;
							$inputs_timetable['chapter_id'] = $get_timetable->chapter_id;
							$inputs_timetable['topic_id'] = $get_next_assign_id;
							$inputs_timetable['from_time'] = $get_timetable->from_time;
							$inputs_timetable['to_time'] = $get_timetable->to_time;
							$inputs_timetable['cdate'] = $nxtday;                  

							$nxt_class_assign = Timetable::create($inputs_timetable);
							
							if($startclass->update($inputs)){
								self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
								self::multiple_batch_partially_end_class($timetable_id, $inputs_timetable, $nxt_class_assign->id);
								return response(['status' => true, 'message' => 'Partially Class Ended Successfully.'], 200);
							}else{          
								return response(['status' => false, 'message' => 'Class Not Ended'], 200);
							}

						}
						else{ 
							$topic = Topic::where('id', $get_timetable->topic_id)->first();
							if($topic){
								// $assigntopic_id = Topic::where('id', '>', $topic->id)->min('id');
								$assigntopic_id = self::check_timetable_topic($topic->id);
								$chk_topic_assign = Topic::where('id', $assigntopic_id)->first();
								if($chk_topic_assign){
									$chk_course = Course::where('id', $chk_topic_assign->course_id)->first();
									if($chk_course){
										$chk_course_subject_relation = CourseSubjectRelation::where('course_id', $chk_topic_assign->course_id)->where('subject_id', $chk_topic_assign->subject_id)->first();
										if($chk_course_subject_relation){
											$chk_subject = Subject::where('id', $chk_topic_assign->subject_id)->first();
											if($chk_subject){
												$chk_chapter = Chapter::where('course_id', $chk_topic_assign->course_id)->where('subject_id', $chk_topic_assign->subject_id)->first();
												if($chk_chapter){
													// $get_next_assign_data = Topic::where('course_id', $chk_course->id)->where('subject_id', $chk_subject->id)->where('chapter_id', $chk_chapter->id)->first();
													$get_next_assign_id = $assigntopic_id;
												}else{
													$startclass->update($inputs);
													self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
													return response(['status' => false, 'message' => 'Topic Id Not Found'], 200);
												}
											}else{
												$startclass->update($inputs);
												self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
												return response(['status' => false, 'message' => 'Chapter Not Found'], 200);
											}
										}else{
											$startclass->update($inputs);
											self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
											return response(['status' => false, 'message' => 'Subject Not Found'], 200);
										}
									}else{
										$startclass->update($inputs);
										self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
										return response(['status' => false, 'message' => 'Course Not Found'], 200);
									}
								}else{
									$startclass->update($inputs); 
									self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
									return response(['status' => false, 'message' => 'Topic Next id Not Found'], 200);
								}
							}else{
								$startclass->update($inputs);
								self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
								return response(['status' => false, 'message' => 'Topic Not Found'], 200);
							}

							$inputs_timetable['studio_id'] = $get_timetable->studio_id;
							$inputs_timetable['assistant_id'] = $get_timetable->assistant_id;
							$inputs_timetable['faculty_id'] = $get_timetable->faculty_id;
							$inputs_timetable['batch_id'] = $get_timetable->batch_id;
							$inputs_timetable['course_id'] = $get_timetable->course_id;
							$inputs_timetable['subject_id'] = $get_timetable->subject_id;
							// $inputs_timetable['chapter_id'] = $get_timetable->chapter_id;
							
							$inputs_timetable['chapter_id'] = $chk_topic_assign->chapter_id;
							$inputs_timetable['topic_id'] = $get_next_assign_id;
							
							$inputs_timetable['from_time'] = $get_timetable->from_time;
							$inputs_timetable['to_time'] =$get_timetable->to_time;
							$inputs_timetable['cdate'] = $nxtday;

							$nxt_class_assign = Timetable::create($inputs_timetable);
							
							// print_r($request->batch_accord_subject); die;
								

							if($startclass->update($inputs)){
								self::multiple_batch_end_class($timetable_id, $current_time, $class_status);
									
								self::multiple_batch_partially_end_class($timetable_id, $inputs_timetable, $nxt_class_assign->id);
								
								$inputs_tt['studio_id']    = $get_timetable->studio_id;
								$inputs_tt['assistant_id'] = $get_timetable->assistant_id;
								$inputs_tt['faculty_id']   = $get_timetable->faculty_id;
								$inputs_tt['from_time']    = $get_timetable->from_time;
								$inputs_tt['to_time']      = $get_timetable->to_time;
								$inputs_tt['cdate']        = $nxtday;
								
								Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs_tt, $nxt_class_assign->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects, 'online');
									
								return response(['status' => true, 'message' => 'Class Ended Successfully.'], 200);
							}else{          
								return response(['status' => false, 'message' => 'Class Not Ended'], 200);
							}
						}
						
					}
				}
			}             
		}
		else{
			return response(['status' => false, 'message' => 'Date is required.'], 200);
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
		
		$partially_html = view('studiomanager.timetables.partially_end_class_data', compact('timetable_id'))->render();
		
		echo $partially_html;
		die;
    }
	
	public function get_studio(Request $request){
		$branch_id = $request->branch_id;

        $studio = Studio::where('branch_id', $branch_id)->where('status', '1')->where('is_deleted', '0')->get();
   
        if (!empty($studio))
        {
            $res = "<option value=''> Select Studio </option>";
            foreach ($studio as $key => $value)
            {
                if (!empty($value->id) && !empty($value->name))
                {
                    $res .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
			
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<option value=''> Studio Not Found </option>";
            die();
        }
	}
}
