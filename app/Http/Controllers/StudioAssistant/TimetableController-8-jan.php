<?php

namespace App\Http\Controllers\StudioAssistant;

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

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::with('user_details','user_branches')->where('id', Auth::user()->id)->first();
        $faculty_id = Input::get('faculty_id');
        $fdate = Input::get('fdate');

        if(!empty($fdate)){
            $get_date = $fdate;
        }else{
            $get_date = date('Y-m-d');
        } 

        $timeslots = TimeSlot::get();

        $get_studios = array();
		// echo "<pre>"; print_r($user->user_branches[0]->branch_id); die;
        if(!empty($user->user_branches)){
			if(!empty($user->user_branches[0])){
				$branch_id = $user->user_branches[0]->branch_id;
				$get_studios = Studio::with([
					'assistant',
					'timetable' => function($q) use ($get_date,$faculty_id){
						if(!empty($get_date)){
							$q->Where('cdate', $get_date)->orderBy('from_time', 'asc');
						}
						if(!empty($faculty_id)){
							$q->where('faculty_id', $faculty_id)->orderBy('from_time', 'asc');
						}
					},
					'timetable.topic',
				])->where('assistant_id', Auth::user()->id)->where('branch_id', $branch_id)->orderBy('id', 'desc');            

				$get_studios = $get_studios->get();
			}

        }else{
            echo 'Branch Not Found';
        }        

        return view('studioassistant.timetable.index', compact('timeslots' , 'get_studios'));
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

            if($startclass->save()){
                return response(['status' => true, 'message' => 'Class Start.'], 200);
            }else{          
                return response(['status' => false, 'message' => 'Class Not Start'], 200);
            }
        }                
    }

    public function update_class(Request $request){

        $timetable_id = $request->timetable_id;

        $class_status = $request->class_status;
		if(!empty($request->end_date)){
			
			$end_date = $request->end_date;
			
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
						return response(['status' => false, 'message' => 'Class Already Exists'], 200);

					}else{

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
								return response(['status' => true, 'message' => 'Partially Class Ended Successfully.'], 200);
							}else{          
								return response(['status' => false, 'message' => 'Class Not Ended'], 200);
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
													return response(['status' => false, 'message' => 'Topic Id Not Found'], 200);
												}
											}else{
												$startclass->update($inputs);
												return response(['status' => false, 'message' => 'Chapter Not Found'], 200);
											}
										}else{
											$startclass->update($inputs);
											return response(['status' => false, 'message' => 'Subject Not Found'], 200);
										}
									}else{
										$startclass->update($inputs);
										return response(['status' => false, 'message' => 'Course Not Found'], 200);
									}
								}else{
									$startclass->update($inputs); 
									return response(['status' => false, 'message' => 'Topic Next id Not Found'], 200);
								}
							}else{
								$startclass->update($inputs);
								return response(['status' => false, 'message' => 'Topic Not Found'], 200);
							}

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
								return response(['status' => true, 'message' => 'Class Ended Successfully.'], 200);
							}else{          
								return response(['status' => false, 'message' => 'Class Not Ended'], 200);
							}
						}
					}

				}else{

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
							return response(['status' => true, 'message' => 'Partially Class Ended Successfully.'], 200);
						}else{          
							return response(['status' => false, 'message' => 'Class Not Ended'], 200);
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
												return response(['status' => false, 'message' => 'Topic Id Not Found'], 200);
											}
										}else{
											$startclass->update($inputs);
											return response(['status' => false, 'message' => 'Chapter Not Found'], 200);
										}
									}else{
										$startclass->update($inputs);
										return response(['status' => false, 'message' => 'Subject Not Found'], 200);
									}
								}else{
									$startclass->update($inputs);
									return response(['status' => false, 'message' => 'Course Not Found'], 200);
								}
							}else{
								$startclass->update($inputs); 
								return response(['status' => false, 'message' => 'Topic Next id Not Found'], 200);
							}
						}else{
							$startclass->update($inputs);
							return response(['status' => false, 'message' => 'Topic Not Found'], 200);
						}

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
							return response(['status' => true, 'message' => 'Class Ended Successfully.'], 200);
						}else{          
							return response(['status' => false, 'message' => 'Class Not Ended'], 200);
						}
					}
				}
			}             
		}			
    }
}
