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
use App\FacultyRelation;
use App\Reschedule;
use App\CancelClass;
use App\Batchrelation;
use App\Batch;
use App\User;
use App\Swap;
use App\Userdetails;
use App\ClassRemark;
use App\Leave;
use Excel;
use App\Exports\TimetableExport;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* $branch_id  = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
        $studio_id  = Input::get('studio_id');
		$batch_id   = Input::get('batch_id');
        $fdate      = Input::get('fdate');

        $timeslots = TimeSlot::get();

        $get_studios = array();
        

        if ($branch_id || $studio_id || $faculty_id || $batch_id || $fdate)
        {

            $get_studios = Studio::with(['assistant',
			'timetable' => function ($q) use ($faculty_id, $fdate)
            {
				$q->where('is_deleted', '0');
				
                if (!empty($faculty_id))
                {
                    $q->where('faculty_id', $faculty_id)->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
                }
                if (!empty($fdate))
                {
                    $q->Where('cdate', $fdate)->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
                }
				else{
					$q->Where('cdate',  date('Y-m-d'))->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
				}
            }
            , 'timetable.topic','timetable.chapter',
			'timetable.batch' => function ($q) use ($batch_id)
            {
                if(!empty($batch_id)){
					$q->where('id', '=', $batch_id);
				}
            }
            ])->orderBy('id', 'desc');

            if (!empty($branch_id))
            {
                $get_studios->where('branch_id', '=', $branch_id);
            }

            if (!empty($studio_id))
            {
                $get_studios->where('id', '=', $studio_id);
            }
			if (!empty($faculty_id) || !empty($fdate)){	
			$get_studios->WhereHas('timetable', function ($q) use ($faculty_id, $fdate) {
						if (!empty($faculty_id))
						{
							$q->where('faculty_id', $faculty_id)->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
						}
						if (!empty($fdate))
						{
							$q->Where('cdate', $fdate)->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
						}
						else{
							$q->Where('cdate',  date('Y-m-d'))->where('time_table_parent_id', 0)->orderBy('from_time', 'asc');
						}
					});
		    }		
			if (!empty($batch_id)){	
				$get_studios->WhereHas('timetable.batch', function ($q) use ($batch_id) {
					if(!empty($batch_id)){
						$q->where('id', '=', $batch_id);
					}
				});
		    }

            $get_studios = $get_studios->get();
			 //echo '<pre>'; print_r($get_studios);die;
        } */
        $tt_date = Input::get('tt_date');
        return view('studiomanager.timetable.index', compact('tt_date')); //, compact('timeslots', 'get_studios')
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('studiomanager.timetable.add');
    }
	
	public function checkAllConditionsOfAvailability($request){  
		//echo '<pre>'; print_r($request->post());die;
		if(!empty($request->from_time) && !empty($request->to_time)){
			$class_type = $request->class_type; 
			$assistant_id = $request->assistant_id;
			$studioId = $request->studio_id;
			$fromTime = $request->from_time;
			$toTime = $request->to_time;
			$chapterId = $request->chapter_id;
			//$topicId = $request->topic_id;
			$cdates = $request->cdate;
			$facultyId = $request->faculty_id;
			$batchId = $request->batch_id;
			$courseId = $request->course_id;
			$subjectId = $request->subject_id;
			$remarks = $request->remark;
			$onlineClassType = $request->online_class_type;
			foreach($request->from_time as $key=>$value){
			//$assistant_id = $assistant_id[$key];
			//$studioId     = $studioId[$key]; 

	
				if(empty($batchId[$key])){
					return ['status' => false, 'message' => 'Batch is required'];
					exit;
				}
				if(empty($studioId[$key])){
					return ['status' => false, 'message' => 'Studio is required'];
					exit;
				}
				if(empty($fromTime[$key])){
					return ['status' => false, 'message' => 'From Time is required'];
					exit;
				}
				if(empty($toTime[$key])){
					return ['status' => false, 'message' => 'To Time is required'];
					exit;
				}
				if(empty($facultyId[$key])){
					return ['status' => false, 'message' => 'Faculty is required'];
					exit;
				}
				if(empty($subjectId[$key])){
					return ['status' => false, 'message' => 'Subject is required'];
					exit;
				}				
				
				
				$from_time = date("H:i", strtotime($fromTime[$key]));
				$to_time = date("H:i", strtotime($toTime[$key]));
				if($class_type=='offline'){
					if(empty($request->chapter_id_offline)){
						return ['status' => false, 'message' => 'Chapter and Topic is required'];
						exit;
					}
					elseif(empty($onlineClassType[$key])){
						return ['status' => false, 'message' => 'Class Type is required'];
						exit;
					}
				}
				else{
					/* if(empty($chapterId[$key])){
						return ['status' => false, 'message' => 'Chapter is required'];
						exit;
					} */
					/* elseif(empty($topicId[$key])){
						return ['status' => false, 'message' => 'Topic is required'];
						exit;
					} */
					if(empty($onlineClassType[$key])){
						return ['status' => false, 'message' => 'Class Type is required'];
						exit;
					}
				}
				
				$from_time_id = TimeSlot::where('time_slot', $from_time)
				->first(); 
				if(!empty($from_time_id->id)){
					$get_from_time_id = $from_time_id->id;
				}
				else{
					return ['status' => false, 'message' => 'From Time Not Avaliable'];
					exit;
				}
				
				$to_time_id = TimeSlot::where('time_slot', $to_time)
				->first();
				if(!empty($to_time_id->id)){
					$get_to_time_id = $to_time_id->id;
				}
				else{
					return ['status' => false, 'message' => 'To Time Not Avaliable'];
					exit;
				}
				
				$error = false;
				if(strtotime($from_time) > strtotime($to_time)){
					$error = true;
				} 
				
				if($error){ 
					return ['status' => false, 'message' => 'End time should not be less than start time.'];
				}
				
				$get_studio_timetables = Timetable::with('studio','batch','faculty')->where('studio_id', $studioId)
				->where('cdate', $cdates[$key])->where('is_deleted', '0')
				->get();
				//echo '<pre>'; print_r($get_studio_timetables);die('dd');
				$msg1       = ''; 
				if(count($get_studio_timetable) > 0)
				{ 
					$from_time2 = [];
					$to_time2   = [];
					
				
					foreach ($get_studio_timetable as $value)
					{   
						if(!empty($value->from_time) && !empty($value->to_time)){
							$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
							->first();
							$from_time2[] = $from_time1->id;
							$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
							->first();
							$to_time2[] = $to_time1->id;
							$msg1 = "In particular ".$value->studio->name." of ".$value->batch->name." ".date('h:i A', strtotime($value->from_time))." - ".date('h:i A', strtotime($value->to_time))." ".$value->faculty->name." class are scheduled"; 
						}
					}

					$chk_condition = 'false';

					for ($i = 0;$i < count($from_time2);$i++)
					{
						if(!empty($request->id[$key])){  
							if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
							{
								$chk_condition = 'false';
							}
							else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
							else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
							
						}
						else{
							if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
							else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
						}
						
					}

					if ($chk_condition == 'true')
					{
						return ['status' => false, 'message' => $msg1];
					}
					else
					{

						$get_faculty_studio_timetable = Timetable::with('studio','studio.branch','batch','faculty')->where('faculty_id', $facultyId[$key])
						->where('cdate', $cdates[$key])->where('is_deleted', '0')
						->get();
						
						if (count($get_faculty_studio_timetable) > 0)
						{

							$from_time2 = [];
							$to_time2 = [];

							foreach ($get_faculty_studio_timetable as $value)
							{ 
								if(!empty($value->from_time) && !empty($value->to_time)){
									$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
									->first();
									$from_time2[] = $from_time1->id;
									$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
									->first();
									$to_time2[] = $to_time1->id;
								}
								$msg1 = "Faculty ".$value->faculty->name." their  branch is ".$value->studio->branch->name." and studio ".$value->studio->name." of batch ".$value->batch->name." from ".date('h:i A', strtotime($value->from_time))." - ".date('h:i A', strtotime($value->to_time))." class are scheduled"; 
							}

							$chk_condition = 'false';

							for ($i = 0;$i < count($from_time2);$i++)
							{
								if(!empty($request->id[$key])){
									if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
									{
										$chk_condition = 'false';
									}
									else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
									else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
								}
								else{
									if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
									else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
								}
								
							}

							if ($chk_condition == 'true')
							{
								return ['status' => false, 'message' => $msg1];
							}

						}
						else
						{
							
						}
					}

				}
				else
				{
					$get_faculty_studio_timetable = Timetable::with('studio','studio.branch','batch','faculty')->where('faculty_id', $facultyId[$key])
					->where('cdate', $cdates[$key])->where('is_deleted', '0')
					->get();
						
					if (count($get_faculty_studio_timetable) > 0)
					{

						$from_time2 = [];
						$to_time2 = [];

						foreach ($get_faculty_studio_timetable as $value)
						{ 
							$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
							->first();
							$from_time2[] = $from_time1->id;
							$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
							->first();
							$to_time2[] = $to_time1->id;
							
							$msg1 = "Faculty ".$value->faculty->name." their  branch is ".$value->studio->branch->name." and studio ".$value->studio->name." of batch ".$value->batch->name." from ".date('h:i A', strtotime($value->from_time))." - ".date('h:i A', strtotime($value->to_time))." class are scheduled"; 
						}

						$chk_condition = 'false';

						for ($i = 0;$i < count($from_time2);$i++)
						{
							if(!empty($request->id[$key])){
								if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
								{
									$chk_condition = 'false';
								}
								else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}
							else{
								if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}
							
						}

						if ($chk_condition == 'true')
						{
							return ['status' => false, 'message' => $msg1];
						}
						else
						{
							
						}

					}
					else
					{
						
					}
				}
				
				
				
				
			}
			
			return ['status' => true];
			
		}
		else{
			return ['status' => false, 'message' => 'Something Went Wrong'];
		}
	}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   //echo '<pre>'; print_r($request->post());die;
        if ($request->ajax())
        {
			$response = $this->checkAllConditionsOfAvailability($request);
			
			if($response['status']==false){
				return response(['status' => false, 'message' => $response['message']], 200);
				exit;
			}
			$res = "";
			
			if(!empty($request->from_time) && !empty($request->to_time)){
				$class_type = $request->class_type;
				$assistant_id = $request->assistant_id;
				$studioId = $request->studio_id;
				$fromTime = $request->from_time;
				$toTime = $request->to_time;
				//$chapterId = $request->chapter_id;
				//$topicId = $request->topic_id;
				$cdates = $request->cdate;
				$facultyId = $request->faculty_id;
				$batchId = $request->batch_id;
				$courseId = $request->course_id;
				$subjectId = $request->subject_id;
				$remarks = $request->remark;
				$onlineClassType = $request->online_class_type;
				
				$saveDataCount = 0;
				
				$res .="<div class='table-responsive'>
							<table class='table'>";
				$a = 1;					
				foreach($request->from_time as $key=>$value){
					$from_time = date("H:i", strtotime($fromTime[$key]));
					$to_time = date("H:i", strtotime($toTime[$key]));
					
					$from_time_id = TimeSlot::where('time_slot', $from_time)
					->first();
					$get_from_time_id = $from_time_id->id;
					$to_time_id = TimeSlot::where('time_slot', $to_time)
					->first();
					$get_to_time_id = $to_time_id->id;
					
					$get_studio_timetable = Timetable::where('studio_id', $studioId)
					->where('cdate', $cdates[$key])->where('is_deleted', '0')
					->get();
					
					if (count($get_studio_timetable) > 0)
					{  
						$from_time2 = [];
						$to_time2 = [];

						foreach ($get_studio_timetable as $value)
						{
							if(!empty($value->from_time) && !empty($value->to_time)){
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}
						}

						$chk_condition = 'false';

						for ($i = 0;$i < count($from_time2);$i++)
						{
							if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
							else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
						}

						if ($chk_condition == 'true')
						{
							//return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
						}
						else
						{

							$get_faculty_studio_timetable = Timetable::where('faculty_id', $facultyId[$key])
							->where('cdate', $cdates[$key])->where('is_deleted', '0')
							->get();

							if (count($get_faculty_studio_timetable) > 0)
							{

								$from_time2 = [];
								$to_time2 = [];

								foreach ($get_faculty_studio_timetable as $value)
								{
									if(!empty($value->from_time) && !empty($value->to_time)){
										$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
										->first();
										$from_time2[] = $from_time1->id;
										$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
										->first();
										$to_time2[] = $to_time1->id;
									}
								}

								$chk_condition = 'false';

								for ($i = 0;$i < count($from_time2);$i++)
								{
									if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
									else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
								}

								if ($chk_condition == 'true')
								{
									//return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
								}
								else
								{
									$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate','class_type','online_class_type');  //, 'topic_id','chapter_id'
									$inputs['from_time'] = $from_time;
									$inputs['to_time'] = $to_time;
									//$inputs['studio_id'] = $studioId;
									//$inputs['assistant_id'] = $assistant_id;
									$inputs['faculty_id'] = $facultyId[$key];
									$inputs['course_id'] = $courseId[$key];
									$inputs['subject_id'] = $subjectId[$key];
									//$inputs['chapter_id'] = $chapterId[$key];
									//$inputs['topic_id'] = $topicId[$key];
									$inputs['cdate'] = $cdates[$key];
									$inputs['online_class_type'] = $onlineClassType[$key];
									$inputs['assistant_id'] = $assistant_id[$key];
									$inputs['studio_id'] = $studioId[$key];
									if($class_type == 'offline'){
										$chapter_id_offline = $request->chapter_id_offline;
										if(!empty($chapter_id_offline)){
											foreach($chapter_id_offline as $key11=>$chapterTopic){
												$chapterTopic = explode('-',$chapterTopic);
												if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
													//$inputs['chapter_id']  = $chapterTopic[0];
													//$inputs['topic_id'] = $chapterTopic[1];
													if($key11 > 0){
														$inputs['from_time'] = null;
														$inputs['to_time'] = null;
													}
													$timetable = Timetable::create($inputs);
													if(empty($inputs['time_table_parent_id'])){
														$timetable_id = $timetable->id;
														$inputs['time_table_parent_id'] = $timetable_id;
													}
													
												}								
											}
										}
										
									}
									else{
										if(count($batchId[$key]) > 0){
											$time_parent_id = 1;
											foreach($batchId[$key] as $batchIdVal){
												$inputs['batch_id'] = $batchIdVal;
												
												if($time_parent_id == 1){ $timetable = Timetable::insertGetId($inputs); $inputs['time_table_parent_id'] = $timetable; }else{ Timetable::insertGetId($inputs);}
												$time_parent_id++;
											}
										}
										
										
										//Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs, $timetable->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects);
										
									}

									$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

									if($remark){
										$remark->remark = $remarks[$key];
										$remark->update();

									}else{
										$input_remark = $request->only('subject_id','remark');
										$input_remark['subject_id'] = $subjectId[$key];
										$input_remark['remark'] = $remarks[$key];
										$remark = ClassRemark::create($input_remark);
									}                            

									if ($timetable)
									{
										$saveDataCount++;
										//return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
									}
									else
									{
										//return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
									}

								}

							}
							else
							{
								$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate', 'class_type','online_class_type'); //, 'topic_id'
								$inputs['from_time'] = $from_time;
								$inputs['to_time'] = $to_time;
								//$inputs['assistant_id'] = $assistant_id;
								//$inputs['studio_id'] = $studioId;
								$inputs['faculty_id'] = $facultyId[$key];
								//$inputs['batch_id'] = $batchId[$key];
								$inputs['course_id'] = $courseId[$key];
								$inputs['subject_id'] = $subjectId[$key];
								//$inputs['chapter_id'] = $chapterId[$key];
								//$inputs['topic_id'] = $topicId[$key];
								$inputs['cdate'] = $cdates[$key];
								$inputs['online_class_type'] = $onlineClassType[$key];
								$inputs['assistant_id'] = $assistant_id[$key];
								$inputs['studio_id'] = $studioId[$key];
								if($class_type == 'offline'){ 
									$chapter_id_offline = $request->chapter_id_offline;
									if(!empty($chapter_id_offline)){
										foreach($chapter_id_offline as $key11=>$chapterTopic){
											$chapterTopic = explode('-',$chapterTopic);
											if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
												//$inputs['chapter_id']  = $chapterTopic[0];
												//$inputs['topic_id'] = $chapterTopic[1];
												if($key11 > 0){
													$inputs['from_time'] = null;
													$inputs['to_time'] = null;
												}
												$timetable = Timetable::create($inputs);
												if(empty($inputs['time_table_parent_id'])){
													$timetable_id = $timetable->id;
													$inputs['time_table_parent_id'] = $timetable_id;
												}
												
											}								
										}
									}
									
								}
								else{
									if(count($batchId[$key]) > 0){
										$time_parent_id = 1;
										foreach($batchId[$key] as $batchIdVal){
											$inputs['batch_id'] = $batchIdVal;
										
											if($time_parent_id == 1){ $timetable = Timetable::insertGetId($inputs); $inputs['time_table_parent_id'] = $timetable; }else{ Timetable::insertGetId($inputs);}
											$time_parent_id++;
										}
									}
									//$timetable = Timetable::create($inputs);
									
									//Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs, $timetable->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects);
									
								}

								$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

								if($remark){
									$remark->remark = $remarks[$key];
									$remark->update();

								}
								else{
									$input_remark = $request->only('subject_id','remark');
									$input_remark['subject_id'] = $subjectId[$key];
									$input_remark['remark'] = $remarks[$key];
									$remark = ClassRemark::create($input_remark);
								}
								

								if ($timetable)
								{
									$saveDataCount++;
									//return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
								}
								else
								{
									//return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
								}

							}
						}

					}
					else
					{ 
						$get_faculty_studio_timetable = Timetable::where('faculty_id', $facultyId[$key])
						->where('cdate', $cdates[$key])->where('is_deleted', '0')
						->get();
						
						if (count($get_faculty_studio_timetable) > 0)
						{ 

							$from_time2 = [];
							$to_time2 = [];

							foreach ($get_faculty_studio_timetable as $value)
							{
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}

							$chk_condition = 'false';

							for ($i = 0;$i < count($from_time2);$i++)
							{
								if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}

							if ($chk_condition == 'true')
							{
								// return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
							}
							else
							{
								$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'time_table_parent_id', 'cdate', 'class_type','online_class_type'); //, 'topic_id'
								$inputs['from_time'] = $from_time;
								$inputs['to_time'] = $to_time;
								//$inputs['assistant_id'] = $assistant_id;
								//$inputs['studio_id'] = $studioId;
								$inputs['faculty_id'] = $facultyId[$key];
								//$inputs['batch_id'] = $batchId[$key];
								$inputs['course_id'] = $courseId[$key];
								$inputs['subject_id'] = $subjectId[$key];
								//$inputs['chapter_id'] = $chapterId[$key];
								//$inputs['topic_id'] = $topicId[$key];
								$inputs['cdate'] = $cdates[$key];
								$inputs['online_class_type'] = $onlineClassType[$key];
								$inputs['assistant_id'] = $assistant_id[$key];
								$inputs['studio_id'] = $studioId[$key];
								if($class_type == 'offline'){
									$chapter_id_offline = $request->chapter_id_offline;
									if(!empty($chapter_id_offline)){
										foreach($chapter_id_offline as $key11=>$chapterTopic){
											$chapterTopic = explode('-',$chapterTopic);
											if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
												//$inputs['chapter_id']  = $chapterTopic[0];
												//$inputs['topic_id'] = $chapterTopic[1];
												if($key11 > 0){
													$inputs['from_time'] = null;
													$inputs['to_time'] = null;
												}
												$timetable = Timetable::create($inputs);
												if(empty($inputs['time_table_parent_id'])){
													$timetable_id = $timetable->id;
													$inputs['time_table_parent_id'] = $timetable_id;
												}
												
											}								
										}
									}
									
								}
								else{
									if(count($batchId[$key]) > 0){
										$time_parent_id = 1;
										foreach($batchId[$key] as $batchIdVal){
											$inputs['batch_id'] = $batchIdVal;
											
											if($time_parent_id == 1){ $timetable = Timetable::insertGetId($inputs); $inputs['time_table_parent_id'] = $timetable; }else{ Timetable::insertGetId($inputs);}
											$time_parent_id++;
										}
									}
									
									
									//Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs, $timetable->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects);
									
								}
							
								
								$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

								if($remark){
									$remark->remark = $remarks[$key];
									$remark->update();

								}else{
									$input_remark = $request->only('subject_id','remark');
									$input_remark['subject_id'] = $subjectId[$key];
									$input_remark['remark'] = $remarks[$key];
									$remark = ClassRemark::create($input_remark);
								}

								if ($timetable)
								{
									$saveDataCount++;
									//return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
								}
								else
								{
									//return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
								}
							}

						}
						else
						{ 	
							$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate','class_type','online_class_type'); //, 'topic_id'
							$inputs['from_time'] = $from_time;
							$inputs['to_time'] = $to_time;
							//$inputs['assistant_id'] = $assistant_id;
							//$inputs['studio_id'] = $studioId;
							$inputs['faculty_id'] = $facultyId[$key];
							$inputs['batch_id'] = $batchId[$key];
							$inputs['course_id'] = $courseId[$key];
							$inputs['subject_id'] = $subjectId[$key];
							//$inputs['chapter_id'] = $chapterId[$key];
							//$inputs['topic_id'] = $topicId[$key];
							$inputs['cdate'] = $cdates[$key];
							$inputs['online_class_type'] = $onlineClassType[$key];
							$inputs['assistant_id'] = $assistant_id[$key];
							$inputs['studio_id'] = $studioId[$key];
						
							if($class_type == 'offline'){
								$chapter_id_offline = $request->chapter_id_offline;
								if(!empty($chapter_id_offline)){
									foreach($chapter_id_offline as $key11=>$chapterTopic){
										$chapterTopic = explode('-',$chapterTopic);
										if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
											//$inputs['chapter_id']  = $chapterTopic[0];
											//$inputs['topic_id'] = $chapterTopic[1];
											if($key11 > 0){
												$inputs['from_time'] = null;
												$inputs['to_time'] = null;
											}
											$timetable = Timetable::create($inputs);
											if(empty($inputs['time_table_parent_id'])){
												$timetable_id = $timetable->id;
												$inputs['time_table_parent_id'] = $timetable_id;
											}
											
										}								
									}
								}
								
							}
							else{  
								if(count($batchId[$key]) > 0){
									$time_parent_id = 1;
									foreach($batchId[$key] as $batchIdVal){
										$inputs['batch_id'] = $batchIdVal;
										if($time_parent_id == 1){ $timetable = Timetable::insertGetId($inputs); $inputs['time_table_parent_id'] = $timetable; }else{ Timetable::insertGetId($inputs);}
										$time_parent_id++;
									}
								}
								//$timetable = Timetable::create($inputs);
								// Self::store_batch_accrd_subject($request->batch_accord_subject, $inputs, $timetable->id, $request->batch_accord_chapter, $request->batch_accord_topic, $request->batch_accord_course, $request->batch_accord_subjects);
								
							}
							//echo '<pre>'; print_r();die;
							$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

							if($remark){
								$remark->remark = $remarks[$key];
								$remark->update();

							}else{
								$input_remark = $request->only('subject_id','remark');
								$input_remark['subject_id'] = $subjectId[$key];
								$input_remark['remark'] = $remarks[$key];
								$remark = ClassRemark::create($input_remark);
							}
							
							if ($timetable)
							{
								$saveDataCount++;
								//return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
							}
							else
							{
								//return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
							}

						}
					}
					
					if(!empty($timetable)){
						$save_details = DB::table('timetables')
											->select('timetables.*','batch.name as batch_name','studios.name as studios_name','studios.branch_id','users.name as faculty_name','subject.name as subject_name')
											->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
											->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
											->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
											->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
											->where('timetables.id', $timetable)
											->where('timetables.cdate', date('Y-m-d'))
											->where('timetables.is_deleted','0')
											->where('timetables.time_table_parent_id','0')
											->first();
										
					if(!empty($save_details)){

							$multiple_batch_array = array(); $multiple_batch_str = ''; 
							$check_timetable_value = $save_details;
							array_push($multiple_batch_array, $save_details->batch_id);
							$multiple_batch_str .= $save_details->batch_name.', ';
							
							$get_multiple_batch = DB::table('timetables')
													->select('timetables.*','batch.name as batch_name')
													->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->where('studios.branch_id', $save_details->branch_id)
													->where('timetables.cdate', date('Y-m-d'))
													->where('timetables.is_deleted','0')
													->where('time_table_parent_id', $save_details->id)
													->get();
							
						
							
							if(count($get_multiple_batch) > 0){
								foreach($get_multiple_batch as $get_multiple_batch_val){
									 
									array_push($multiple_batch_array, $get_multiple_batch_val->batch_id); 
									$multiple_batch_str .= $get_multiple_batch_val->batch_name.', ';
									
								}
							}
							 	
						$res .="<tr>
								<td>
								<form action='javascript:void(0)' method='get' class='edittimetable' id='editSubmitForm$a'>
								<table style='width: 100%;'>";
						if($a == 1){
							$res .= "<tr class='text-center'>
										<th>Batch</th>
										<th>Studio</th>
										<th>From Time</th>
										<th>To Time</th>
										<th>Faculty</th>
										<th>Subject</th>
										<th>Action</th>
									</tr>";
						}	
						$res .="<tr class='text-center add_row'>
								<td style='width:15%'>
								<span class='edit_span s_batch_id'>".rtrim($multiple_batch_str, ", ")."</span>
								<fieldset class='form-group hide'>";
								
						$batch = Batch::where('status', '1')->orderBy('id','desc')->get();
						$res .= "<select class='form-control select-multiple11 batch_id' name='batch_id[0][]' onChange='getSubject(this);getCourse(this)' multiple='multiple'>
									<option value=''> Select Batch </option>";
									if(count($batch) > 0){
										foreach($batch as $value){
									$res .=	"<option value='$value->id'";
									if(count($multiple_batch_array) > 0 && in_array($value->id, $multiple_batch_array)){ $res .="selected"; }
									$res .=	">$value->name</option>";  
										}
									}
						$res .= "</select>												
								</fieldset>
								</td>
								
								<td class='course_online_faculty' style='display: none;'>
									<span class='edit_span s_course_id'>$check_timetable_value->course_id</span>
									<fieldset class='form-group hide'>
									<select class='form-control course_id' name='course_id[]'>
										<option value='".$check_timetable_value->course_id."'> - Select Course - </option>
									</select>
									</fieldset>
								</td>
								<td style='width:15%'>
									<span class='edit_span s_studio_id'>$check_timetable_value->studios_name</span>
									<fieldset class='form-group hide'>";
									//$studio = Studio::where('branch_id', $request->branch_id[$key])->where('status', '1')->orderBy('id','desc')->get();
									$studio = Studio::with(['assistant'=>function($q){ $q->where('status',1); }])->where('branch_id', $request->branch_id[$key])->where('status',1)->get();
									$res .="<select class='form-control select-multiple11 studio_id' name='studio_id[]' onChange='getStudioName(this)'>
										<option value=''> Select Studio </option>";
										if(count($studio) > 0){
											foreach($studio as $value){
												if (!empty($value->name) && !empty($value->name) && !empty($value->assistant)){
													$res .= "<option value='$value->id' data-asst-id='$value->assistant_id'";  
													if(!empty($check_timetable_value->studio_id) && $check_timetable_value->studio_id == $value->id){ $res .= "selected";}
													$res .=">$value->name</option>";
												}
											}
										}
								
								
									$res .="</select>												
									</fieldset>
								</td>
								<td style='width:10%'>
									<span class='edit_span s_from_time'>".
									date('h:i A', strtotime($check_timetable_value->from_time));
									$res .= "</span>
									<fieldset class='form-group hide'>
										<input type='time' name='from_time[]' class='form-control from_time' class='form-control' placeholder='Time' value='$check_timetable_value->from_time' autocomplete='off'>
									</fieldset>
								 </td>
								 
								 <td style='width:10%'>
									<span class='edit_span s_to_time'>".
									date('h:i A', strtotime($check_timetable_value->to_time));
									$res .= "</span>
									<fieldset class='form-group hide'>
										<input type='time' name='to_time[]' class='form-control to_time' class='form-control' placeholder='Time' value='$check_timetable_value->to_time' autocomplete='off'>
									</fieldset>
								 </td>
								 
								 <td style='width:15%'>
									<span class='edit_span s_faculty'>$check_timetable_value->faculty_name</span>
									<fieldset class='form-group hide'>";
										$faculty = \App\User::where('status', '1')->where('role_id', 2)->orderBy('id','desc')->get();
									$res.=	"<select class='form-control select-multiple11 faculty' name='faculty_id[]'>
										<option value=''> Select Faculty </option>";
										if(count($faculty) > 0){
											foreach($faculty as $value){
											$res.=	"<option value='$value->id'";   if(!empty($check_timetable_value->faculty_id) && $check_timetable_value->faculty_id == $value->id){ $res.= "selected"; }
											$res.= ">$value->name</option>";
											}
										}										
									$res.= "</select>												
									</fieldset>
								</td>
								<td style='width:20%'>
									<span class='edit_span s_subject_id'>$check_timetable_value->subject_name</span>
									<fieldset class='form-group hide'>";
									$subject = Subject::where('id', $check_timetable_value->subject_id)->first();
									$res.=" <select class='form-control select-multiple11 subject_id' name='subject_id[]'>";
											if(!empty($subject->id)){
											$res.= "<option value='$subject->id' selected>$subject->name</option>";
											}
									$res.=	"</select>												
									</fieldset>
								 </td>
								 <td style='width:15%'>
									<span class='edit_span'>
										<a href='javascript:void(0)' class='float-right pl-1' onclick='deleteTimetable(this, $check_timetable_value->id)'>
											<span class='btn btn-danger btn-sm action-delete delete_id'><i class='feather icon-trash'></i></span>
										</a>
										<a href='javascript:void(0)' class='float-right' data-id='$a' onclick='editTimetable(this)'>
											<span class='btn btn-success btn-sm action-edit edit_id'><i class='feather icon-edit'></i></span>
										</a>
										
									</span>
									<fieldset class='form-group hide'>
										<button type='submit' id='time_table_edit_btn$a' data-id='$a' class='btn btn-outline-primary btn-sm float-right click_edit_class'>
											<i class='feather icon-check'></i>
											<i class='fa fa-spinner fa-spin set-loader' style='display: none;'></i>
										</button>
									</fieldset>
									<input type='hidden' name='id[]' class='id' value='$check_timetable_value->id'>
								</td>
								</tr>	
									</table>
									<div class='row mt-2' style='display:none'>
									<input type='hidden' class='online_class_type' name='online_class_type[]' value='online'>
									<input type='hidden' class='assistant_id' name='assistant_id[]' value='$check_timetable_value->assistant_id'>
									<input type='hidden' class='cdate' name='cdate[]' value='".date('Y-m-d')."'>
								</div>
								</form>	
								</td>
								</tr>";	

								
					}
					$a++;
				}
						
				}	
				$res .="</div>
						</table>";				
				
				if($saveDataCount > 0){
					return response(['status' => true, 'message' => 'Class Added Successfully.', 'result' => $res], 200);
				}
				else{
					return response(['status' => false, 'message' => 'Something Went Wrong.'], 200);
				}
			}
			else{
				return response(['status' => false, 'message' => 'From time and To time required.'], 200);
			}

        }
        else
        {
            return response('Something Went Wrong', 500);
        }
    }
	
	public function store_batch_accrd_subject($batch_accord_subject, $requests, $timetable_id, $batch_accord_chapter, $batch_accord_topic, $batch_accord_course, $batch_accord_subjects){
		
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
						'created_at'           => date('Y-m-d H:i:s'),
						'updated_at'           => date('Y-m-d H:i:s')
					]);	
				}
			}
		}

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
    public function update(Request $request)
    {
        //echo '<pre>'; print_r($request->post());die;
		if ($request->ajax())
        {
			$response = $this->checkAllConditionsOfAvailability($request);
			
			if($response['status']==false){
				return response(['status' => false, 'message' => $response['message']], 200);
				exit;
			}
			
			if(!empty($request->from_time) && !empty($request->to_time)){
				$class_type = $request->class_type;
				$assistant_id = $request->assistant_id;
				$studioId = $request->studio_id;
				$fromTime = $request->from_time;
				$toTime = $request->to_time;
				$cdates = $request->cdate;
				$facultyId = $request->faculty_id;
				$batchId = $request->batch_id;
				$courseId = $request->course_id;
				$subjectId = $request->subject_id;
				$remarks = $request->remark;
				$onlineClassType = $request->online_class_type;
				
				$saveDataCount = 0;$n_parent_id = '';
				foreach($request->from_time as $key=>$value){
					$from_time = date("H:i", strtotime($fromTime[$key]));
					$to_time = date("H:i", strtotime($toTime[$key]));
					
					$from_time_id = TimeSlot::where('time_slot', $from_time)
					->first();
					$get_from_time_id = $from_time_id->id;
					$to_time_id = TimeSlot::where('time_slot', $to_time)
					->first();
					$get_to_time_id = $to_time_id->id;
					
					$get_studio_timetable = Timetable::where('studio_id', $studioId)
					->where('cdate', $cdates[$key])->where('is_deleted', '0')
					->get();
					
					if (count($get_studio_timetable) > 0)
					{
						$from_time2 = [];
						$to_time2 = [];

						foreach ($get_studio_timetable as $value)
						{
							if(!empty($value->from_time) && !empty($value->to_time)){
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}
						}

						$chk_condition = 'false';

						for ($i = 0;$i < count($from_time2);$i++)
						{
							
							if(!empty($request->id[$key])){
								if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
								{
									$chk_condition = 'false';
								}
								else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}
							else{
								if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}
						}

						if ($chk_condition == 'true')
						{
						}
						else
						{

							$get_faculty_studio_timetable = Timetable::where('faculty_id', $facultyId[$key])
							->where('cdate', $cdates[$key])->where('is_deleted', '0')
							->get();

							if (count($get_faculty_studio_timetable) > 0)
							{   
 
								$from_time2 = [];
								$to_time2 = [];

								foreach ($get_faculty_studio_timetable as $value)
								{
									if(!empty($value->from_time) && !empty($value->to_time)){
										$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
										->first();
										$from_time2[] = $from_time1->id;
										$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
										->first();
										$to_time2[] = $to_time1->id;
									}
								}

								$chk_condition = 'false';

								for ($i = 0;$i < count($from_time2);$i++)
								{
									if(!empty($request->id[$key])){
										if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
										{
											$chk_condition = 'false';
										}
										else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
										{
											$chk_condition = 'true';
										}
										else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
										{
											$chk_condition = 'true';
										}
									}
									else{
										if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
										{
											$chk_condition = 'true';
										}
										else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
										{
											$chk_condition = 'true';
										}
									}
								}

								if ($chk_condition == 'true')
								{
								}
								else
								{
									$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate','class_type','online_class_type');  
									$inputs['from_time'] = $from_time;
									$inputs['to_time'] = $to_time;
									$inputs['faculty_id'] = $facultyId[$key];
									//$inputs['batch_id'] = $batchId[$key];
									$inputs['course_id'] = $courseId[$key];
									$inputs['subject_id'] = $subjectId[$key];
									$inputs['cdate'] = $cdates[$key];
									$inputs['online_class_type'] = $onlineClassType[$key];
									$inputs['assistant_id'] = $assistant_id[$key];
									$inputs['studio_id'] = $studioId[$key];
									if($class_type == 'offline'){
										$chapter_id_offline = $request->chapter_id_offline;
										if(!empty($chapter_id_offline)){
											foreach($chapter_id_offline as $key11=>$chapterTopic){
												$chapterTopic = explode('-',$chapterTopic);
												if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
													if($key11 > 0){
														$inputs['from_time'] = null;
														$inputs['to_time'] = null;
													}
													$timetable = Timetable::where('id', $request->id[$key])->update($inputs);
													if(empty($inputs['time_table_parent_id'])){
														$timetable_id = $timetable->id;
														$inputs['time_table_parent_id'] = $timetable_id;
													}
													
												}								
											}
										}
										
									}
									else{  
										if(count($batchId[$key]) > 0){ 
											$batch_array =  array();
											foreach($batchId[$key] as $batchIdVal){
												array_push($batch_array, $batchIdVal);
												 $check_id_data = Timetable::where('batch_id', $batchIdVal)->where('is_deleted', '0')->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->first();
												 if(!empty($check_id_data)){
													 $inputs['batch_id']             = $check_id_data->batch_id; 
													 $inputs['time_table_parent_id'] = $check_id_data->time_table_parent_id;
													 $timetable = Timetable::where('id', $check_id_data->id)->update($inputs);
												 }
												 else{ 
													  $inputs['batch_id'] = $batchIdVal; 
													  $inputs['time_table_parent_id'] = $request->id[$key];
													  $timetable = Timetable::insertGetId($inputs);
												 }
											}
										}
										
										$unselected_timetable = Timetable::whereNotIn('batch_id', $batch_array)->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->get();
										if(count($unselected_timetable) > 0){
											foreach($unselected_timetable as $unselected_timetable_val){
												if($unselected_timetable_val->time_table_parent_id == '0'){
													Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
													
													$new_parent_id = Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->first();
													$n_parent_id = $new_parent_id->id;
													Timetable::where('id', $new_parent_id->id)->update([ 'time_table_parent_id' => '0']);
													$timetable =  Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->update([ 'time_table_parent_id' => $new_parent_id->id]);
												}
												else{
													$timetable = Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
												}
												
											}
											$timetable = true;
										}
										
										
									}

									$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

									if($remark){
										$remark->remark = $remarks[$key];
										$remark->update();

									}else{
										$input_remark = $request->only('subject_id','remark');
										$input_remark['subject_id'] = $subjectId[$key];
										$input_remark['remark'] = $remarks[$key];
										$remark = ClassRemark::create($input_remark);
									}                            

									if ($timetable)
									{
										$saveDataCount++;
									}
									else
									{
										
									}

								}

							}
							else
							{    
								$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate', 'class_type','online_class_type'); //, 'topic_id'
								$inputs['from_time'] = $from_time;
								$inputs['to_time'] = $to_time;
								$inputs['faculty_id'] = $facultyId[$key];
								$inputs['batch_id'] = $batchId[$key];
								$inputs['course_id'] = $courseId[$key];
								$inputs['subject_id'] = $subjectId[$key];
								$inputs['cdate'] = $cdates[$key];
								$inputs['online_class_type'] = $onlineClassType[$key];
								$inputs['assistant_id'] = $assistant_id[$key];
								$inputs['studio_id'] = $studioId[$key];
								if($class_type == 'offline'){ 
									$chapter_id_offline = $request->chapter_id_offline;
									if(!empty($chapter_id_offline)){
										foreach($chapter_id_offline as $key11=>$chapterTopic){
											$chapterTopic = explode('-',$chapterTopic);
											if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
												if($key11 > 0){
													$inputs['from_time'] = null;
													$inputs['to_time'] = null;
												}
												$timetable = Timetable::where('id', $request->id[$key])->update($inputs);
												if(empty($inputs['time_table_parent_id'])){
													$timetable_id = $timetable->id;
													$inputs['time_table_parent_id'] = $timetable_id;
												}
												
											}								
										}
									}
									
								}
								else{
									if(count($batchId[$key]) > 0){ 
										$batch_array =  array();
										foreach($batchId[$key] as $batchIdVal){
											array_push($batch_array, $batchIdVal);
											 $check_id_data = Timetable::where('batch_id', $batchIdVal)->where('is_deleted', '0')->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->first();
											 if(!empty($check_id_data)){
												 $inputs['batch_id']             = $check_id_data->batch_id; 
												 $inputs['time_table_parent_id'] = $check_id_data->time_table_parent_id;
												 $timetable = Timetable::where('id', $check_id_data->id)->update($inputs);
											 }
											 else{ 
												  $inputs['batch_id'] = $batchIdVal; 
												  $inputs['time_table_parent_id'] = $request->id[$key];
												  $timetable = Timetable::insertGetId($inputs);
											 }
										}
									}
									
									$unselected_timetable = Timetable::whereNotIn('batch_id', $batch_array)->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->get();
									if(count($unselected_timetable) > 0){
										foreach($unselected_timetable as $unselected_timetable_val){
											if($unselected_timetable_val->time_table_parent_id == '0'){
												Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
												
												$new_parent_id = Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->first();
												$n_parent_id = $new_parent_id->id;
												Timetable::where('id', $new_parent_id->id)->update([ 'time_table_parent_id' => '0']);
												$timetable =  Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->update([ 'time_table_parent_id' => $new_parent_id->id]);
											}
											else{
												$timetable = Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
											}
											
										}
										$timetable = true;
									}
								}

								$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

								if($remark){
									$remark->remark = $remarks[$key];
									$remark->update();

								}
								else{
									$input_remark = $request->only('subject_id','remark');
									$input_remark['subject_id'] = $subjectId[$key];
									$input_remark['remark'] = $remarks[$key];
									$remark = ClassRemark::create($input_remark);
								}
								

								if ($timetable)
								{
									$saveDataCount++;
								}
								else
								{
									
								}

							}
						}

					}
					else
					{ 
						$get_faculty_studio_timetable = Timetable::where('faculty_id', $facultyId[$key])
						->where('cdate', $cdates[$key])->where('is_deleted', '0')
						->get();
						
						if (count($get_faculty_studio_timetable) > 0)
						{ 

							$from_time2 = [];
							$to_time2 = [];

							foreach ($get_faculty_studio_timetable as $value)
							{
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}

							$chk_condition = 'false';

							for ($i = 0;$i < count($from_time2);$i++)
							{
								if(!empty($request->id[$key])){
									if ($get_from_time_id == $from_time2[$i] && $get_to_time_id == $to_time2[$i])
									{
										$chk_condition = 'false';
									}
									else if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
									else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
								}
								else{
									if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
									else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
									{
										$chk_condition = 'true';
									}
								}
							}

							if ($chk_condition == 'true')
							{
								
							}
							else
							{
								$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate', 'class_type','online_class_type'); //, 'topic_id'
								$inputs['from_time'] = $from_time;
								$inputs['to_time'] = $to_time;
								$inputs['faculty_id'] = $facultyId[$key];
								$inputs['batch_id'] = $batchId[$key];
								$inputs['course_id'] = $courseId[$key];
								$inputs['subject_id'] = $subjectId[$key];
								$inputs['cdate'] = $cdates[$key];
								$inputs['online_class_type'] = $onlineClassType[$key];
								$inputs['assistant_id'] = $assistant_id[$key];
								$inputs['studio_id'] = $studioId[$key];
								if($class_type == 'offline'){
									$chapter_id_offline = $request->chapter_id_offline;
									if(!empty($chapter_id_offline)){
										foreach($chapter_id_offline as $key11=>$chapterTopic){
											$chapterTopic = explode('-',$chapterTopic);
											if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
												if($key11 > 0){
													$inputs['from_time'] = null;
													$inputs['to_time'] = null;
												}
												$timetable = Timetable::where('id', $request->id[$key])->update($inputs);
												if(empty($inputs['time_table_parent_id'])){
													$timetable_id = $timetable->id;
													$inputs['time_table_parent_id'] = $timetable_id;
												}
												
											}								
										}
									}
									
								}
								else{
									if(count($batchId[$key]) > 0){
										$batch_array =  array();
										foreach($batchId[$key] as $batchIdVal){
											array_push($batch_array, $batchIdVal);
											 $check_id_data = Timetable::where('batch_id', $batchIdVal)->where('is_deleted', '0')->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->first();
											 if(!empty($check_id_data)){
												 $inputs['batch_id']             = $check_id_data->batch_id; 
												 $inputs['time_table_parent_id'] = $check_id_data->time_table_parent_id;
												 $timetable = Timetable::where('id', $check_id_data->id)->update($inputs);
											 }
											 else{ 
												  $inputs['batch_id'] = $batchIdVal; 
												  $inputs['time_table_parent_id'] = $request->id[$key];
												  $timetable = Timetable::insertGetId($inputs);
											 }
										}
									}
									
									$unselected_timetable = Timetable::whereNotIn('batch_id', $batch_array)->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->get();
									if(count($unselected_timetable) > 0){
										foreach($unselected_timetable as $unselected_timetable_val){
											if($unselected_timetable_val->time_table_parent_id == '0'){
												Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
												
												$new_parent_id = Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->first();
												$n_parent_id = $new_parent_id->id;
												Timetable::where('id', $new_parent_id->id)->update([ 'time_table_parent_id' => '0']);
												$timetable =  Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->update([ 'time_table_parent_id' => $new_parent_id->id]);
											}
											else{
												$timetable = Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
											}
											
										}
										$timetable = true;
									}
								}
							
								
								$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

								if($remark){
									$remark->remark = $remarks[$key];
									$remark->update();

								}else{
									$input_remark = $request->only('subject_id','remark');
									$input_remark['subject_id'] = $subjectId[$key];
									$input_remark['remark'] = $remarks[$key];
									//$remark = ClassRemark::create($input_remark);
								}

								if ($timetable)
								{
									$saveDataCount++;
								}
								else
								{
								}
							}

						}
						else
						{ 	
							$inputs = $request->only('studio_id', 'assistant_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'from_time', 'to_time', 'time_table_parent_id', 'cdate','class_type','online_class_type'); //, 'topic_id'
							$inputs['from_time'] = $from_time;
							$inputs['to_time'] = $to_time;
							$inputs['faculty_id'] = $facultyId[$key];
							$inputs['batch_id'] = $batchId[$key];
							$inputs['course_id'] = $courseId[$key];
							$inputs['subject_id'] = $subjectId[$key];
							$inputs['cdate'] = $cdates[$key];
							$inputs['online_class_type'] = $onlineClassType[$key];
							$inputs['assistant_id'] = $assistant_id[$key];
							$inputs['studio_id'] = $studioId[$key];
						
							if($class_type == 'offline'){
								$chapter_id_offline = $request->chapter_id_offline;
								if(!empty($chapter_id_offline)){
									foreach($chapter_id_offline as $key11=>$chapterTopic){
										$chapterTopic = explode('-',$chapterTopic);
										if(!empty($chapterTopic[0]) && !empty($chapterTopic[1])){
											if($key11 > 0){
												$inputs['from_time'] = null;
												$inputs['to_time'] = null;
											}
											$timetable = Timetable::where('id', $request->id[$key])->update($inputs);
											if(empty($inputs['time_table_parent_id'])){
												$timetable_id = $timetable->id;
												$inputs['time_table_parent_id'] = $timetable_id;
											}
											
										}								
									}
								}
								
							}
							else{  
								if(count($batchId[$key]) > 0){
									$batch_array =  array();
									foreach($batchId[$key] as $batchIdVal){
										array_push($batch_array, $batchIdVal);
										 $check_id_data = Timetable::where('batch_id', $batchIdVal)->where('is_deleted', '0')->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->first();
										 if(!empty($check_id_data)){
											 $inputs['batch_id']             = $check_id_data->batch_id; 
											 $inputs['time_table_parent_id'] = $check_id_data->time_table_parent_id;
											 $timetable = Timetable::where('id', $check_id_data->id)->update($inputs);
										 }
										 else{ 
											  $inputs['batch_id'] = $batchIdVal; 
											  $inputs['time_table_parent_id'] = $request->id[$key];
											  $timetable = Timetable::insertGetId($inputs);
										 }
									}
								}
								
								$unselected_timetable = Timetable::whereNotIn('batch_id', $batch_array)->whereRaw("(id = ".$request->id[$key]." OR time_table_parent_id = ".$request->id[$key].")")->get();
								if(count($unselected_timetable) > 0){
									foreach($unselected_timetable as $unselected_timetable_val){
										if($unselected_timetable_val->time_table_parent_id == '0'){
											Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
											
											$new_parent_id = Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->first();
											$n_parent_id = $new_parent_id->id;
											Timetable::where('id', $new_parent_id->id)->update([ 'time_table_parent_id' => '0']);
											$timetable =  Timetable::where('time_table_parent_id', $unselected_timetable_val->id)->update([ 'time_table_parent_id' => $new_parent_id->id]);
										}
										else{
											$timetable = Timetable::where('id', $unselected_timetable_val->id)->update([ 'is_deleted' => '1']);
										}
										
									}
									$timetable = true;
								}
								
							}
							$remark = ClassRemark::where('subject_id', $subjectId[$key])->first();

							if($remark){
								$remark->remark = $remarks[$key];
								$remark->update();

							}else{
								$input_remark = $request->only('subject_id','remark');
								$input_remark['subject_id'] = $subjectId[$key];
								$input_remark['remark'] = $remarks[$key];
								//$remark = ClassRemark::create($input_remark);
							}
							
							if ($timetable)
							{
								$saveDataCount++;
							}
							else
							{
							}

						}
					}
					
					$tt_id = !empty($n_parent_id) ? $n_parent_id : $request->id[$key]; 
					$save_details = DB::table('timetables')
										->select('timetables.*','batch.name as batch_name','studios.name as studios_name','studios.branch_id','users.name as faculty_name','subject.name as subject_name')
										->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
										->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
										->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
										->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
										->where('timetables.id', $tt_id)
										->where('timetables.is_deleted','0')
										->where('timetables.time_table_parent_id','0')
										->first();
					//echo '<pre>'; print_r($save_details);die;					
					if(!empty($save_details)){
						$multiple_batch_array = array(); $multiple_batch_str = ''; 
														
						//foreach($save_details as $check_timetable_value){
							$check_timetable_value = $save_details;
							array_push($multiple_batch_array, $check_timetable_value->batch_id);
							$multiple_batch_str .= $check_timetable_value->batch_name.', ';
							
							$get_multiple_batch = DB::table('timetables')
													->select('timetables.*','batch.name as batch_name')
													->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->where('studios.branch_id', $check_timetable_value->branch_id)
													->where('timetables.cdate', date('Y-m-d'))
													->where('timetables.is_deleted','0')
													->where('time_table_parent_id', $check_timetable_value->id)
													->get();
							
						
							
							if(count($get_multiple_batch) > 0){
								foreach($get_multiple_batch as $get_multiple_batch_val){
									 
									array_push($multiple_batch_array, $get_multiple_batch_val->batch_id); 
									$multiple_batch_str .= $get_multiple_batch_val->batch_name.', ';
									
								}
							}
							
							$check_timetable_value->batch_id = $multiple_batch_array;
							$check_timetable_value->batch_name = $multiple_batch_str;
							$check_timetable_value->studios_name = $check_timetable_value->studios_name;
							$check_timetable_value->from_time = date('h:i A', strtotime($check_timetable_value->from_time));	
							$check_timetable_value->to_time = date('h:i A', strtotime($check_timetable_value->to_time));
							$check_timetable_value->faculty_name = $check_timetable_value->faculty_name;
							$check_timetable_value->subject_name = $check_timetable_value->subject_name;
						//}					
								
						//echo '<pre>'; print_r($check_timetable_value);die;			
									
					}
					
				if($saveDataCount){
					return response(['status' => true, 'message' => 'Class Update Successfully.', 'result' => $check_timetable_value], 200);
				}
				else{
					return response(['status' => false, 'message' => 'Something Went Wrong.'], 200);
				}
			  }
			}
			else{
				return response(['status' => false, 'message' => 'From time and To time required.'], 200);
			}

        }
        else
        {
            return response('Something Went Wrong', 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!empty($request->id)){
			$result = Timetable::whereRaw("(id = ".$request->id." OR time_table_parent_id = ".$request->id.")")->update(['is_deleted' => '1']);
			if($result){
				return response(['status' => true, 'message' => 'Successfully Deleted'], 200);
			}
			else{
				return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
			}
			
		}
		return response(['status' => false, 'message' => 'ID Not Found'], 200);
    }

    public function get_batch(Request $request)
    {

        $faculty_id = $request->faculty_id;

        $batchs = Batchrelation::with('batch')->select('batch_id')->where('faculty_id', $faculty_id)->groupBy('batch_id')->get();

        if (!empty($batchs))
        {
            echo $res = "<option value=''> Select Batch </option>";
            foreach ($batchs as $key => $value)
            {
                if (!empty($value->batch->name) && !empty($value->batch->name))
                {
                    echo $res = "<option value='" . $value->batch->id . "'>" . $value->batch->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Batch Not Found </option>";
            die();
        }
    }

    public function get_course(Request $request)
    {

        $batch_id = $request->batch_id;

        $course = Batch::with('course')->select('course_id')->where('id', $batch_id)->get();

        //print_r($course->toArray()); die;
        // echo $res = "<input type='hidden' name='batch_id' value='" . $batch_id . "'>";

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
	
	public function get_data_by_topic(Request $request){ 
		$get_batch = DB::table('batch')->groupBy('batch.id')->get(); 
							//echo '<pre>'; print_r($batch_by_subject);die;
		if (count($get_batch) > 0) {
			
					
				echo $res = '<div class="row remove_rows" >';
				echo $res = "<div class='col-md-3 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_subject select-multiple-51' name='batch_accord_subject[]'>";
				echo $res = "<option value=''> Batch </option>";				
				    foreach ($get_batch as $key => $value) {
						if(!empty($value->id) && !empty($value->name)){
							echo $res = "<option value='". $value->id ."' >" . $value->name ."</option>";
						}
					}
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				
				echo $res = "<div class='col-md-3 col-12'  style='display: none;'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_course select-multiple-61' name='batch_accord_course[]'>";
				echo $res = "<option value=''> Course </option>";
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				
				echo $res = "<div class='col-md-3 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_subjects select-multiple-61' name='batch_accord_subjects[]'>";
				echo $res = "<option value=''> Subject </option>";
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				 
				echo $res = "<div class='col-md-3 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_chapter select-multiple-61' name='batch_accord_chapter[]'>";
				echo $res = "<option value=''> Chapter </option>";
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				
				echo $res = "<div class='col-md-3 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_topic select-multiple-71' name='batch_accord_topic[]'>";
				echo $res = "<option value=''> Topic </option>";
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				echo $res = "</div>";
			die();
			
		}
		
		
	}
	
	public function get_batch_by_subject(Request $request){
		$subject_id = $request->subject_id;
		$batch_id   = $request->batch_id;
		$i = 1;
		$j = 1;
		$batch_by_subject = DB::table('batch')
		                    ->join('batchrelations', 'batch.id', '=', 'batchrelations.batch_id')
							->select('batch.id', 'batch.name')
							->where([['batchrelations.subject_id', '=', $subject_id],['batch.id', '!=', $batch_id]])
							->groupBy('batch.id')
		                    ->get(); 
							//echo '<pre>'; print_r($batch_by_subject);die;
		if (!empty($batch_by_subject)) {
			
			foreach ($batch_by_subject as $key => $value) {		
				echo $res = '<div class="row subject_batches" >';
				echo $res = "<div class='col-md-4 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_subject select-multiple-51' name='batch_accord_subject[]'>";	
				
					if(!empty($value->id) && !empty($value->name)){
						echo $res = "<option value='". $value->id ."' >" . $value->name ."</option>";
					}
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				
				echo $res = "<div class='col-md-4 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_chapter select-multiple-61' name='batch_accord_chapter[]'>";
				
				$chapters = Chapter::where('subject_id', $subject_id)->get();
				echo $res = "<option value=''> Select Chapter </option>";
				if (!empty($chapters)) {
					foreach ($chapters as $chapterskey => $chaptersvalue) {
						if(!empty($chaptersvalue->id) && !empty($chaptersvalue->name)){
							echo $res = "<option value='". $chaptersvalue->id ."' >" . $chaptersvalue->name ."</option>";
						}
						
					}
				}
				
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				echo $res = "<div class='col-md-4 col-12'>";	
				echo $res = "<div class='form-label-group'>";	
				echo $res = "<select class='form-control batch_accord_topic select-multiple-71' name='batch_accord_topic[]'>";
				echo $res = "<option value=''> Select Topic </option>";
				echo $res = "</select>";
				echo $res = "</div>";
				echo $res = "</div>";
				echo $res = "</div>";
				$j++;
				$i++;
			}
			
		}
		
		die();		
		//echo '<pre>'; print_r($batch_by_topic);die;
	}

    public function get_batch_subject(Request $request){

        $batch_id = $request->batch_id;
		$count_batch_array =  count($batch_id);
		$subjects = Batchrelation::with('subject')->whereIn('batch_id', $batch_id)->groupBy('subject_id')->havingRaw('COUNT(subject_id) = ?', [$count_batch_array])->get();
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
	
	public function get_topic_by_chapter(Request $request){
		$chapter_id = $request->chapter_id;
		
		$topics = Topic::where('chapter_id', $chapter_id)->get();
        
        if (!empty($topics)) {                         
            echo $res = "<option value=''> Select Topic </option>";
            foreach ($topics as $topicskey => $topicsvalue) {
                if(!empty($topicsvalue->id) && !empty($topicsvalue->name)){
                    echo $res = "<option value='". $topicsvalue->id ."'>" . $topicsvalue->name ."</option>";
                }
            }
            exit();
        } else {
            echo $res = "<option value=''> Topic Not Found </option>";
            die();
        }
	}

    public function get_remark(Request $request)
    {
        $subject_id = $request->subject_id;

        $classremark = ClassRemark::where('subject_id', $subject_id)->first();

        if(!empty($classremark)){
            echo json_encode(['status' => true, 'data' => $classremark->remark]);
        }else{
            echo json_encode(['status' => false, 'data' => '']);
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
				$temp['name'] = $topics_value->name . " (".$topics_value->duration.")";
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

    public function reschedule_store(Request $request)
    {
        $inputs = $request->only('timetable_id','from_time','to_time','faculty_reason');            

        $reschedule = Reschedule::create($inputs);           

        if($reschedule->save()){
            return response(['status' => true, 'message' => 'Reschedule Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function swap_store(Request $request)
    {
        $inputs = $request->only('timetable_id', 'swap_with_faculty_id','swap_timetable_id');

        $inputs['swap_with_faculty_id'] = $request->swap_faculty_id;

        $swap = Swap::create($inputs);           

        if($swap->save()){
            return response(['status' => true, 'message' => 'Swap Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function cancelclass_store(Request $request)
    {
        $inputs = $request->only('timetable_id','days','faculty_reason');

        if(!empty($request->other_reason)){
            $inputs['faculty_reason'] = $request->other_reason;
        }           

        $cancelclass = CancelClass::create($inputs);           

        if($cancelclass->save()){
            return response(['status' => true, 'message' => 'Cancel Class Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function get_swap_faculty(Request $request)
    {

        $faculty_id = $request->faculty_id;
        $get_faculty_branch = Timetable::with('studio')->where('faculty_id', $faculty_id)->first();
        //print_r($get_faculty_branch->studio->branch_id);

        $userdeatils = Userdetails::with([
            'user' => function($q){
                $q->select('id','name')->where('role_id','2')->where('status', '1');
            },
        ])->where('user_id','!=',$faculty_id)->where('branch_id',$get_faculty_branch->studio->branch_id)->get();

        //print_r($userdeatils->toArray()); die;

        //$get_faculty = User::select('id','name')->where('role_id','2')->where('id','!=',$faculty_id)->get();

        //print_r($get_faculty->toArray()); die;       

        if (!empty($userdeatils))
        {
            echo $res = "<option value=''> Select Faculty </option>";
            foreach ($userdeatils as $key => $value)
            {
                if (!empty($value->user->name) && !empty($value->user->name))
                {
                    echo $res = "<option value='" . $value->user->id . "'>" . $value->user->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Faculty Not Found </option>";
            die();
        }
    }

    public function get_swap_faculty_timetable(Request $request)
    {

        $swap_faculty_id = $request->swap_faculty_id;

        $get_faculty_timetable = Timetable::select('id','from_time')->where('faculty_id',$swap_faculty_id)->get();

        //print_r($get_faculty_timetable->toArray()); die;       

        if (!empty($get_faculty_timetable))
        {
            echo $res = "<option value=''> Select From Time </option>";
            foreach ($get_faculty_timetable as $key => $value)
            {
                if (!empty($value->from_time) && !empty($value->from_time))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->from_time . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> From Time Not Found </option>";
            die();
        }
    }

    public function timetable_export(Request $request)
    {
        return view('studiomanager.timetable.exportdata');
    }

    public function export_data(Request $request)
    {
        if(is_array($request->id) && !empty($request->id)){

            $id = [];

            if ($request->has('id') && !empty($request->id)) {

                $id =  $request->id;
                return Excel::download(new TimetableExport($id), 'TimetableData.xlsx');
            }

        } else{
            return redirect()->route('studiomanager.timetables.export')->with('error', 'Please Select Checkbox');
        }
    }
	
	public function get_faculty(Request $request)
    {

        $subject_id = $request->subject_id;
        $batch_id = $request->batch_id;

        $faculty = Batchrelation::with('user')->where('batch_id', $batch_id)->where('subject_id', $subject_id)->get();
        $faculty_array    = array();
        $faculty_id       = 0;
        $res              = '';
        $today_on_leave   = false;
        if (!empty($faculty))
        {
            
            foreach ($faculty as $key => $value)
            {   

            	$today_date = date('Y-m-d'); 
            	$check_today_leave_faculty = Leave::with('leave_details');

            	$check_today_leave_faculty->WhereHas('leave_details', function ($q) {
					$q->where('date', date('Y-m-d'));
					$q->where('status', 'Approved');
				});
				$check_today_leave_faculty = $check_today_leave_faculty->where('emp_id', $value->user->id)->first();

                //echo '<pre>'; print_r($check_today_leave_faculty);die;
            	if(!empty($check_today_leave_faculty->leave_details[0]->leave_id)){
                	$today_on_leave = true;
                }
                if (!empty($value->user->name) && !empty($value->user->name))
	                {
	                    $res .= "<option value='" . $value->user->id . "'>" . $value->user->name ." (". $value->user->register_id .")". "</option>";
						
						$faculty_id = $value->user->id;
	                    
	                }
            }
			
        }
        else
        {
            $res = "<option value='No data'> Faculty Not Found </option>";
          
            
        }
		
		$batches_html = "";
		if($faculty_id > 0){
			$batches_html = view('studiomanager.timetable.faculty_batches', compact('faculty_id'))->render();
		}
        return json_encode(array('response' => $res, 'today_leave' => $today_on_leave, 'batches_html'=>$batches_html));

        exit();
    }
	
	public function get_chapter_and_topic(Request $request)
    {
        $batch_id = $request->batch_id;
        $course_id = $request->course_id;
        $subject_id = $request->subject_id;

        $chapters = Chapter::where('subject_id', $subject_id)->where('course_id', $course_id)->get();
		$res = "";
        if (!empty($chapters))
        {
            $res .= "<option value=''> Select Chapter && Topic </option>";
            foreach ($chapters as $key => $value)
            {
                if (!empty($value->name) && !empty($value->name))
                {
					$getTopic = $this->get_topic_chapter($batch_id,$course_id, $subject_id, $value->id);
					if(!empty($getTopic)){
						$chapter_id = $value->id;
						// $res .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
						foreach ($getTopic as $tvalue)
						{   
							if (!empty($tvalue['id']) && !empty($tvalue['name']))
							{
								$topic_id = $tvalue['id'];
								$res .= "<option value='".$chapter_id.'-'.$topic_id."'>" . $value->name .' - '. $tvalue['name'] . "</option>";
							}
						}
					}
                }
            }
        }
        else
        {
            $res .= "<option value=''> Chapter Not Found </option>";
        }
		echo $res;
		die;
    }
	
	public function get_topic_chapter($batch_id, $course_id, $subject_id, $chapter_id){
		$topics = [];
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
         
        return $topics;
	}
	
	public function delete_class(Request $request){

        $timetable_id = $request->timetable_id;

        if($request->ajax()){ 

            $inputs = $request->only('timetable_id');

            $inputs['timetable_id'] = $timetable_id;
            
			$get_parent_timetable = DB::table('timetables')->where('id', $timetable_id)->orWhere('time_table_parent_id', $timetable_id)->get();
			if(count($get_parent_timetable) > 0){
				foreach($get_parent_timetable as $parent_value){
					DB::table('timetables')->where('id', $parent_value->id)->update([
								'is_deleted' => '1'
							]);			
				}
			}	
            return response(['status' => true, 'message' => 'Delete Successfully'], 200);
        }                
    }
	
	public function get_studio_edit(Request $request){//echo '<pre>'; print_r($request->post());die;
		$timetable_id = $request->timetable_id;
		$typ          = $request->typ;
		$get_edit_data = Timetable::where('id', $timetable_id)->first();
		$res = '';
		if (!empty($timetable_id) && !empty($typ))
        {
			$batch = Batch::where('status', '1')->orderBy('id', 'asc')->get(); 
			return $html = view('studiomanager.timetable.online_edit_class', compact('batch','get_edit_data'))->render();		
		}
	}
	
	public function edit_online_store(Request $request){
		if(!empty($request['online_timetable_id'])){
			if(!empty($request['from_time']) && !empty($request['to_time'])){
				
				$from_time = date("H:i", strtotime($request['from_time']));
				$to_time = date("H:i", strtotime($request['to_time']));
				
				$from_time_id = TimeSlot::where('time_slot', $from_time)
				->first();
				$get_from_time_id = $from_time_id->id;
				$to_time_id = TimeSlot::where('time_slot', $to_time)
				->first();
				$get_to_time_id = $to_time_id->id;


		           
				$error = false;
				if(strtotime($from_time) > strtotime($to_time)){
					$error = true;
				}  


				if($error){ 
					return response(['status' => false, 'message' => 'End time should not be less than start time.'], 200);
				}
				
				$get_studio_timetable = Timetable::where('studio_id', $request['online_studio_id'])
				->where('cdate', $request['online_cdate'])->where('id', '!=', $request['online_timetable_id'] )
				->get();
				
				if (count($get_studio_timetable) > 0)
				{
					$from_time2 = [];
					$to_time2 = [];

					foreach ($get_studio_timetable as $value)
					{
						if(!empty($value->from_time) && !empty($value->to_time)){
							$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
							->first();
							$from_time2[] = $from_time1->id;
							$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
							->first();
							$to_time2[] = $to_time1->id;
						}
					}

					$chk_condition = 'false';

					for ($i = 0;$i < count($from_time2);$i++)
					{
						if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
						{
							$chk_condition = 'true';
						}
						else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
						{
							$chk_condition = 'true';
						}
					}

					if ($chk_condition == 'true')
					{
						return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
					}
					else
					{

						$get_faculty_studio_timetable = Timetable::where('faculty_id', $request['online_faculty_id'])
						->where('cdate', $request['online_cdate'])
						->where('id', '!=', $request['online_timetable_id'])
						->get();

						if (count($get_faculty_studio_timetable) > 0)
						{

							$from_time2 = [];
							$to_time2 = [];

							foreach ($get_faculty_studio_timetable as $value)
							{
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}

							$chk_condition = 'false';

							for ($i = 0;$i < count($from_time2);$i++)
							{
								if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}

							if ($chk_condition == 'true')
							{
								return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
							}
							else
							{ 
								$store_online_data = DB::table('timetables')->where('id', $request['online_timetable_id'])->update([
										'faculty_id'   => $request['online_faculty_id'],
										'batch_id'     => $request['online_batch_id'],
										'course_id'    => $request['online_course_id'],
										'subject_id'   => $request['online_subject_id'],
										'chapter_id'   => $request['online_chapter_id'],
										'topic_id'     => $request['online_topic_id'],
										'from_time'    => $from_time,
										'to_time'      => $to_time,
										'cdate'        => $request['online_cdate'],
										'updated_at'   => date('Y-m-d H:i:s'),
								]);

								$remark = ClassRemark::where('subject_id', $request['online_subject_id'])->first();

								if($remark){
									$remark->remark = $request['online_remark'];
									$remark->update();

								}else{
									$input_remark = $request->only('online_subject_id','online_remark');
									$remark = ClassRemark::create($input_remark);
								}                           

								if ($store_online_data)
								{
									return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
								}
								else
								{
									return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
								}

							}

						}
						else
						{
							$store_online_data = DB::table('timetables')->where('id', $request['online_timetable_id'])->update([
										'faculty_id'   => $request['online_faculty_id'],
										'batch_id'     => $request['online_batch_id'],
										'course_id'    => $request['online_course_id'],
										'subject_id'   => $request['online_subject_id'],
										'chapter_id'   => $request['online_chapter_id'],
										'topic_id'     => $request['online_topic_id'],
										'from_time'    => $from_time,
										'to_time'      => $to_time,
										'cdate'        => $request['online_cdate'],
										'updated_at'   => date('Y-m-d H:i:s'),
				            ]);

							$remark = ClassRemark::where('subject_id', $request['online_subject_id'])->first();

							if($remark){
								$remark->remark = $request['online_remark'];
								$remark->update();

							}else{
								$input_remark = $request->only('online_subject_id','online_remark');
								$remark = ClassRemark::create($input_remark);
							}

							if ($store_online_data)
							{
								return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
							}
							else
							{
								return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
							}

						}
					}

				}
				else
				{
					$store_online_data = DB::table('timetables')->where('id', $request['online_timetable_id'])->update([
										'faculty_id'   => $request['online_faculty_id'],
										'batch_id'     => $request['online_batch_id'],
										'course_id'    => $request['online_course_id'],
										'subject_id'   => $request['online_subject_id'],
										'chapter_id'   => $request['online_chapter_id'],
										'topic_id'     => $request['online_topic_id'],
										'from_time'    => $from_time,
										'to_time'      => $to_time,
										'cdate'        => $request['online_cdate'],
										'updated_at'   => date('Y-m-d H:i:s'),
				            ]);

							$remark = ClassRemark::where('subject_id', $request['online_subject_id'])->first();

							if($remark){
								$remark->remark = $request['online_remark'];
								$remark->update();

							}else{
								$input_remark = $request->only('online_subject_id','online_remark');
								$remark = ClassRemark::create($input_remark);
							}

							if ($store_online_data)
							{
								return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
							}
							else
							{
								return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
							}
				}
				
				
					
				return response(['status' => true, 'message' => 'Successfully Add.'], 200);
			}	
			else{
				return response(['status' => false, 'message' => 'From time and To time required.'], 200);
			}
		}
		else
		{
			return response('Something Went Wrong', 500);
		}
	}
	
	
	public function getStudioByBranch(Request $request){
		$branch_id = $request->branch_id;
	
		if(!empty($branch_id)){
			$batch = Batch::where('status', '1')->orderBy('id','desc')->get();
			$res   = '';
			$res .= "<tr class='text-center add_row'>
					<td>
						<fieldset class='form-group'>
							<select class='form-control select-multiple1 batch_id' multiple='multiple' name='batch_id[$request->index_count][]' onChange='getSubject(this);getCourse(this)'>";
								if (!empty($batch))
								{
									$res .= "<option value=''> Select Batch </option>";
									foreach ($batch as $key => $value)
									{
										if (!empty($value->name) && !empty($value->name))
										{
											$res .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
										}
									}
									
								}
								else
								{
									$res .= "<option value=''> Batch Not Found </option>";
								}
		$res .= "           </select>												
					 </fieldset>
					 </td>";
					 
					 
			$res .= "<td class='course_online_faculty' style='display: none;'>
						<div class='form-label-group'>
							<select class='form-control course_id' name='course_id[$request->index_count]'>
								<option value=''> - Select Course - </option>
							</select>
						</div>
					</td>";


			$res .= "<td>
							<fieldset class='form-group'>
								<select class='form-control select-multiple1 studio_id' name='studio_id[$request->index_count]'  onChange='getStudioName(this)'>";
								
								$studio = Studio::with(['assistant'=>function($q) { $q->where('status',1); }])->where('branch_id', $branch_id)->where('status',1)->get();
								
								$res .= "<option value=''> Select Studio </option>";	
								foreach ($studio as $key => $value)
								{
									if (!empty($value->name) && !empty($value->name) && !empty($value->assistant))
									{
										$res .= "<option value='" . $value->id . "' data-asst-id='" .$value->assistant_id. "'>" . $value->name . "</option>";
									}
								}
									
			$res .= "           </select>												
						 </fieldset>
					 </td>";
					 
					 
			$res .= "<td>
							<fieldset class='form-group'>
								<input type='time' name='from_time[$request->index_count]' class='form-control from_time' class='form-control' placeholder='Time' autocomplete='off'>
							</fieldset>
					 </td>";

			$res .= "<td>
							<fieldset class='form-group'>
								<input type='time' name='to_time[$request->index_count]' class='form-control to_time' class='form-control' placeholder='Time' autocomplete='off'>
							</fieldset>
					 </td>";
					 
			$res .= "<td>
							<fieldset class='form-group'>
								<select class='form-control select-multiple1 faculty_id' name='faculty_id[$request->index_count]'>";
								$faculty = User::where('status', '1')->where('role_id', 2)->orderBy('id','desc')->get();
									if (!empty($faculty))
									{
										$res .= "<option value=''> Select Faculty </option>";
										foreach ($faculty as $key => $value)
										{
											if (!empty($value->name) && !empty($value->name))
											{
												$res .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
											}
										}
										
									}
									else
									{
										$res .= "<option value=''> Faculty Not Found </option>";
									}
			$res .= "           </select>												
						 </fieldset>
					 </td>";

			
			$res .= "<td>
							<fieldset class='form-group'>
								<select class='form-control select-multiple1 subject_id' name='subject_id[$request->index_count]'>
									<option value=''>Select Subject</option>
								</select>												
						 </fieldset>
					 </td>";

			
			$res .= "<td style='display:none'>
						<input type='hidden' class='online_class_type' name='online_class_type[$request->index_count]' value='online'>
						<input type='hidden' class='assistant_id' name='assistant_id[$request->index_count]' value=''>
						<input type='hidden' class='cdate' name='cdate[$request->index_count]' value='".date('Y-m-d')."'>
						<input type='hidden' class='branch_id' name='branch_id[$request->index_count]' value='".$branch_id."'>
					</td>";
			
			$res .= "<td>
						<a href='javascript:void(0);' class='float-right' onclick='removeDiv(this)'>
							<span class='btn btn-danger btn-sm action-edit remove_id'><i class='feather icon-x-square'></i></span>
						</a>
					</td>
					</tr>";
					
					 
			return response(['status' => true, 'data' => $res], 200);
					 
		}
		else{
			return response(['status' => false, 'message' => 'Something is wrong!'], 200);
		}
        
	}
	
	public function copyTimetable($date){ 
		if(!empty($date)){
			$check_timetable = Timetable::where('cdate', date('Y-m-d'))->where('is_deleted', '0')->get();
			if(count($check_timetable) == 0){
				$get_prev_timetable = Timetable::where('cdate', $date)->where('is_deleted', '0')->where('time_table_parent_id', '0')->get();
				
				if(count($get_prev_timetable) > 0){
					foreach($get_prev_timetable as $get_prev_timetable_val){
						$input['studio_id']            = $get_prev_timetable_val->studio_id;
						$input['assistant_id']         = $get_prev_timetable_val->assistant_id;
						$input['faculty_id']           = $get_prev_timetable_val->faculty_id;
						$input['batch_id']             = $get_prev_timetable_val->batch_id;
						$input['course_id']            = $get_prev_timetable_val->course_id;
						$input['subject_id']           = $get_prev_timetable_val->subject_id;
						$input['chapter_id']           = $get_prev_timetable_val->chapter_id;
						$input['from_time']            = $get_prev_timetable_val->from_time;
						$input['to_time']              = $get_prev_timetable_val->to_time;
						$input['class_type']           = $get_prev_timetable_val->class_type;
						$input['online_class_type']    = $get_prev_timetable_val->online_class_type;
						$input['cdate']                = date('Y-m-d');
						
						$f_tt_res = Timetable::insertGetId($input); 
						
						$get_sub_prev_timetable = Timetable::where('time_table_parent_id', $get_prev_timetable_val->id)->where('is_deleted', '0')->get();
						if(count($get_sub_prev_timetable) > 0){
							foreach($get_sub_prev_timetable as $get_sub_prev_timetable_val){
								$inputs['studio_id']            = $get_sub_prev_timetable_val->studio_id;
								$inputs['assistant_id']         = $get_sub_prev_timetable_val->assistant_id;
								$inputs['faculty_id']           = $get_sub_prev_timetable_val->faculty_id;
								$inputs['batch_id']             = $get_sub_prev_timetable_val->batch_id;
								$inputs['course_id']            = $get_sub_prev_timetable_val->course_id;
								$inputs['subject_id']           = $get_sub_prev_timetable_val->subject_id;
								$inputs['chapter_id']           = $get_sub_prev_timetable_val->chapter_id;
								$inputs['from_time']            = $get_sub_prev_timetable_val->from_time;
								$inputs['to_time']              = $get_sub_prev_timetable_val->to_time;
								$inputs['time_table_parent_id'] = $f_tt_res;
								$inputs['class_type']           = $get_sub_prev_timetable_val->class_type;
								$inputs['online_class_type']    = $get_sub_prev_timetable_val->online_class_type;
								$inputs['cdate']                = date('Y-m-d');
								
								$s_tt_res = Timetable::insertGetId($inputs); 
							}
						}
						
					}
					
					return redirect()->route('studiomanager.timetable.index')->with('success', 'Timetable Successfully add');
					
				}
				else{
					return redirect()->route('studiomanager.timetable.index')->with('error', 'Timetable Not Found');
				}
			}
			else{
				return redirect()->route('studiomanager.timetable.index')->with('error', 'Today entry already added');
			}
		}
		else{
			return redirect()->route('studiomanager.timetable.index')->with('error', 'Date Not Found');
		}
	}
}

