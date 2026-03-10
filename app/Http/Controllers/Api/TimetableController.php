<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Timetable;
use App\TimeSlot;
use App\Subject;
use App\Chapter;
use App\Topic;
use App\Studio;
use App\StartClass;
use App\Course;
use App\CourseSubjectRelation;
use Input;
use DB;

class TimetableController extends Controller
{
	public function class_start_end(Request $request)
    {
		print_r('Please use HRM Web Panel to Start/End Class.');
		die();
    	try {
			
    		$class_status = $request->class_status;
    		$timetable_id = $request->timetable_id;
    		if($timetable_id == ''){
    			return $this->returnResponse(200, false, "Timetable id is required.");
    		}
			if($class_status == ''){
    			return $this->returnResponse(200, false, "Class status id is required.");
    		}
			
			$check_timetable = Timetable::where('id', $timetable_id)->first();
			if(!empty($check_timetable)){
				$inputs = $request->only('timetable_id','start_time','end_time','status');
				$inputs['timetable_id'] = $timetable_id;
				$current_time = date('H:i');
				
				if($class_status=="Start Class"){
					$startclass = StartClass::where('timetable_id',$timetable_id)->first();
					if(empty($startclass)){
						$inputs['start_time'] = $current_time;
						$inputs['sc_date'] = date('Y-m-d');
						$inputs['status'] = 'Start Class';
						$startclass = StartClass::create($inputs); 
						if($startclass->save()){
							return $this->returnResponse(200, true, "Class Start successfully");
						}else{
							return $this->returnResponse(200, false, "Something went wrong.");
						}
					}
					else{
						return $this->returnResponse(200, false, "Class Already Exists.");
					}
				}
				else if($class_status=="Partially" || $class_status=="End Class"){
					// if(!empty($request->end_date)){
						$startclass = StartClass::where('timetable_id',$timetable_id)->first(); 
						
						$end_date = $request->end_date;
						
						$inputs['end_time'] = $current_time;

						$inputs['status'] = $class_status;

						$nxtday = $end_date;
						// $nxtday = date('Y-m-d', strtotime('+1 day'));

						$get_timetable_data = Timetable::where('id', $timetable_id)->first();

						$from_time_id = TimeSlot::where('time_slot',$get_timetable_data->from_time)->first();
						$get_from_time_id = $from_time_id->id;
						$to_time_id = TimeSlot::where('time_slot',$get_timetable_data->to_time)->first();
						$get_to_time_id = $to_time_id->id;

						// $get_studio_timetable = Timetable::where('studio_id', $get_timetable_data->studio_id)->where('faculty_id', $get_timetable_data->faculty_id)->where('cdate', $nxtday)->get();

						// if(count($get_studio_timetable) > 0){              
						if( 0 > 1){

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
								return $this->returnResponse(200, false, "Class Already Exists");

							}else{

								$get_timetable = Timetable::where('id', $timetable_id)->first();

								$inputs_timetable = $request->only('studio_id','faculty_id','batch_id','course_id','subject_id','chapter_id','topic_id','from_time','to_time','cdate');

								if($class_status == 'Partially'){

									$get_next_assign_id = $get_timetable->topic_id;

									$inputs_timetable['studio_id'] = $get_timetable->studio_id;
									$inputs_timetable['assistant_id'] = $get_timetable->assistant_id;
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
										return $this->returnResponse(200, true, "Partially Class Ended Successfully.");
									}else{
										return $this->returnResponse(200, false, "Class Not Ended");
									}

								}else{
									$topic = Topic::where('id', $get_timetable->topic_id)->first();
									if($topic){
										$assigntopic_id = Topic::where('id', '>', $topic->id)->min('id');
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
															return $this->returnResponse(200, false, "Topic Id Not Found");
														}
													}else{
														$startclass->update($inputs);
														return $this->returnResponse(200, false, "Chapter Not Found");
													}
												}else{
													$startclass->update($inputs);
													return $this->returnResponse(200, false, "Subject Not Found");
												}
											}else{
												$startclass->update($inputs);
												return $this->returnResponse(200, false, "Course Not Found");
											}
										}else{
											$startclass->update($inputs); 
											return $this->returnResponse(200, false, "Topic Next id Not Found");
										}
									}else{
										$startclass->update($inputs);
										return $this->returnResponse(200, false, "Topic Not Found");
									}

									$inputs_timetable['studio_id'] = $get_timetable->studio_id;
									$inputs_timetable['assistant_id'] = $get_timetable->assistant_id;
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
										return $this->returnResponse(200, true, "Class Ended Successfully.");
									}else{
										return $this->returnResponse(200, false, "Class Not Ended");
									}
								}
							}

						}
						else{

							$get_timetable = Timetable::where('id', $timetable_id)->first();

							$inputs_timetable = $request->only('studio_id','faculty_id','batch_id','course_id','subject_id','chapter_id','topic_id','from_time','to_time','cdate');

							if($class_status == 'Partially'){

								$get_next_assign_id = $get_timetable->topic_id;

								$inputs_timetable['studio_id'] = $get_timetable->studio_id;
								$inputs_timetable['assistant_id'] = $get_timetable->assistant_id;
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
									return response(['status' => true, 'message' => 'Partially Class Ended Successfully.'], 200);
								}else{          
									return response(['status' => false, 'message' => 'Class Not Ended'], 200);
								}

							}
							else{
								/* $topic = Topic::where('id', $get_timetable->topic_id)->first();
								if($topic){
									$assigntopic_id = Topic::where('id', '>', $topic->id)->min('id');
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
														return $this->returnResponse(200, false, "Topic Id Not Found");
													}
												}else{
													$startclass->update($inputs);
													return $this->returnResponse(200, false, "Chapter Not Found");
												}
											}else{
												$startclass->update($inputs);
												return $this->returnResponse(200, false, "Subject Not Found");
											}
										}else{
											$startclass->update($inputs);
											return $this->returnResponse(200, false, "Course Not Found");
										}
									}else{
										$startclass->update($inputs);
										return $this->returnResponse(200, false, "Topic Next id Not Found");
									}
								}else{
									$startclass->update($inputs);
									return $this->returnResponse(200, false, "Topic Not Found");
								} */

								/* $inputs_timetable['studio_id'] = $get_timetable->studio_id;
								$inputs_timetable['assistant_id'] = $get_timetable->assistant_id;
								$inputs_timetable['faculty_id'] = $get_timetable->faculty_id;
								$inputs_timetable['batch_id'] = $get_timetable->batch_id;
								$inputs_timetable['course_id'] = $get_timetable->course_id;
								$inputs_timetable['subject_id'] = $get_timetable->subject_id;
								$inputs_timetable['chapter_id'] = $get_timetable->chapter_id;
								$inputs_timetable['topic_id'] = $get_next_assign_id;
								$inputs_timetable['from_time'] = $get_timetable->from_time;
								$inputs_timetable['to_time'] =$get_timetable->to_time;
								$inputs_timetable['cdate'] = $nxtday;

								$nxt_class_assign = Timetable::create($inputs_timetable); */

								if($startclass->update($inputs)){
									return $this->returnResponse(200, true, "Class Ended Successfully.");
								}else{
									return $this->returnResponse(200, false, "Class Not Ended");
								}
							}
						}
						
					/* }
					else{
						return $this->returnResponse(200, false, "Date is required.");
					} */
				}
				else{
					
				}
			}
			else{
				return $this->returnResponse(200, false, "Invalid Timetable ID.");
			}
			            

            

    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

	public function timtable_duration_update(Request $request)
    {
        try{

            $timetable_id = $request->timetable_id;
            $duration = $request->duration;
            if(!empty($timetable_id) && !empty($duration)){
				$timetable_data= DB::table('timetables')->where('id',$timetable_id)->first();
				if(!empty($timetable_data)){
						 
						
						$user_string_res = DB::table('timetables')->where('id', $timetable_id)->update(['duration' => $duration]);
						if(!empty($user_string_res)){
							return $this->returnResponse(200, true, "Update Successfully !");
						}
						else{							
							return $this->returnResponse(200, false, "Something Went Wrong !");
						} 
					
				}
				else{
					return $this->returnResponse(200, false, "Timetable Id Not Found");  
				}
            }else{
                return $this->returnResponse(200, false, "Timetable and Duration are required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
}
