<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Reschedule;
use App\Timetable;
use App\User;
use App\Swap;
use App\CancelClass;
use App\Studio;
use App\Userbranches;
use App\TimeSlot;

class ClassRequestController extends Controller
{
    /**
    * @var FacultyReschedule
    * @param Request $request
    * @return Json Response
    */
    public function facultyreschedule(Request $request)
    {
		return $this->returnResponse(200, false, "Please contact to timetable manager. Thanks !!"); exit;
    	try {

    		$timetable_id   = $request->timetable_id;
    		$from_time      = $request->from_time;
    		// $to_time = $request->to_time;
    		$faculty_reason = $request->faculty_reason;
			$studio_id      = $request->studio_id;

    		if($timetable_id == ''){
    			return $this->returnResponse(200, false, "Enter Timetable Id");
    		}
    		if($from_time == ''){
    			return $this->returnResponse(200, false, "Enter From Time");
    		}
    		/* if($to_time == ''){
    			return $this->returnResponse(200, false, "Enter To Time");
    		} */
    		if($faculty_reason == ''){
    			return $this->returnResponse(200, false, "Enter Faculty Reason");
    		}
			if($studio_id == ''){
    			return $this->returnResponse(200, false, "Enter Studio ID");
    		}

    		$inputs = $request->only('timetable_id','from_time','to_time','faculty_reason');

    		$inputs['timetable_id'] = $timetable_id;
    		$inputs['from_time'] = $from_time;
    		// $inputs['to_time'] = $to_time;
    		$inputs['faculty_reason'] = $faculty_reason;
			$inputs['studio_id'] = $studio_id;

    		$reschedule = Reschedule::create($inputs);

    		if($reschedule->save()){
    			return $this->returnResponse(200, true, "Faculty Timetable Reschedule");
    		}else{
    			return $this->returnResponse(200, false, "Faculty Timetable Not Reschedule");
    		}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var getFacultySchedule
    * @param Request $request
    * @return Json Response
    */
    public function getFacultyRescheduleClass(Request $request)
    {
    	try {
    		$input              = $request->only(['faculty_id']);
    		$role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;
	        $name               = $request->name;

    		if(isset($input['faculty_id']) && !empty($input['faculty_id'])){

    			$faculty_id = $input['faculty_id'];
				$user = User::where('id', $faculty_id)->first(); 
				if(!empty($user)){
					if($user->status=='1'){
						$current_date = date('Y-m-d');
						$date_from = $request->date_from;
						$date_to = $request->date_to;
						if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with([
								'timetable'=>function ($q) use ($date_from, $date_to,$current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'timetable.reschedule','timetable.topic','timetable.chapter','timetable.faculty','timetable.course'])->where('assistant_id', $faculty_id);
							
							$studios->WhereHas('timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

                            if(!empty($branch_id)){
								$studios->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
                            }

                            if(!empty($designation)){
								$studios->WhereHas('assistant.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}

							if(!empty($department_head_id)){
								$studios->WhereHas('assistant', function ($q4) use ($department_head_id) {
									$q4->where('id', $department_head_id);
								});
							}

       //                      if(!empty($role_id)){
							// 	$studios->WhereHas('assistant', function ($q3) use ($role_id) {
							// 		$q3->where('role_id', $role_id);
							// 	});
							// }

							if(!empty($name)){
								$studios->WhereHas('assistant', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}

							$studios = $studios->first();
							//echo '<pre>'; print_r($studios);die;
							$get_faculty_reschedule = [];
							if(!empty($studios)){
								if(!empty($studios->timetable)){
									foreach($studios->timetable as $key => $timetable){
										if(!empty($timetable->reschedule)){
											foreach($timetable->reschedule as $reschedule){
												$temp['reschedule_id'] = $reschedule->id;
												$temp['timetable_id'] = $reschedule->timetable_id;
												$temp['strtime_from_time'] = strtotime($reschedule->from_time);
												$temp['from_time'] = $reschedule->from_time;
												$temp['to_time'] = $reschedule->to_time;
												$temp['faculty_reason'] = $reschedule->faculty_reason;
												$temp['admin_reason'] = $reschedule->admin_reason;
												$temp['status'] = $reschedule->status;
												$temp['topic_id'] = $timetable->topic->id;
												$temp['topic_name'] = $timetable->topic->name;
												$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
												$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
												$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
												$temp['date'] = $timetable->cdate;
												
												$get_faculty_reschedule[] = $temp;
											}
										}
									}
								}
								/* if(!empty($get_faculty_reschedule)){
									usort($get_faculty_reschedule, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								$data['faculty_reschedule'] = $get_faculty_reschedule;
								return $this->returnResponse(200, true, "Reschedule List", $data);
							}
							else{
								return $this->returnResponse(200, false, "Timetable Reschedule Not Found");
							}
						}
						else if($user->role_id==4 || $user->role_id==27){ //4 = Studio Manager Role // 27 = Time table Manager
						
							$branches = Userbranches::with(['studio','studio.timetable'=>function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'studio.timetable.reschedule','studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->where('user_id', $faculty_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q3) use ($department_head_id) {
									$q3->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q4) use ($designation) {
									$q4->where('degination', $designation);
								});
							}

							if(!empty($name)){
								$branches->WhereHas('user', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}


							$branches = $branches->groupBy('branch_id')->get();
							if(!empty($branches)){
								$get_faculty_reschedule = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $timetable){
													if(!empty($timetable->reschedule)){
														foreach($timetable->reschedule as $reschedule){
															$temp['reschedule_id'] = $reschedule->id;
															$temp['timetable_id'] = $reschedule->timetable_id;
															$temp['strtime_from_time'] = strtotime($reschedule->from_time);
															$temp['from_time'] = $reschedule->from_time;
															$temp['to_time'] = $reschedule->to_time;
															$temp['faculty_reason'] = $reschedule->faculty_reason;
															$temp['admin_reason'] = $reschedule->admin_reason;
															$temp['status'] = $reschedule->status;
															$temp['topic_id'] = $timetable->topic->id;
															$temp['topic_name'] = $timetable->topic->name;
															$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
															$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
															$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
															$temp['date'] = $timetable->cdate;
															
															$get_faculty_reschedule[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								/* if(!empty($get_faculty_reschedule)){
									usort($get_faculty_reschedule, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								$data['faculty_reschedule'] = $get_faculty_reschedule;
								return $this->returnResponse(200, true, "Reschedule List", $data);
								
							}
							else{
								return $this->returnResponse(200, false, "Timetable Reschedule Not Found");
							}
							
						}
						else if($user->role_id==29){
							
							$branches = Userbranches::with(['studio','studio.timetable'=>function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'studio.timetable.reschedule','studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter']);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q3) use ($department_head_id) {
									$q3->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q4) use ($designation) {
									$q4->where('degination', $designation);
								});
							}

							if(!empty($role_id)){
								$branches->WhereHas('user', function ($q5) use ($role_id) {
									$q5->where('role_id', $role_id);
								});
							}

							if(!empty($name)){
								$branches->WhereHas('user', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}

							$branches = $branches->groupBy('branch_id')->get();

							//echo '<pre>'; print_r($branches);die;
							if(!empty($branches)){
								$get_faculty_reschedule = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $timetable){
													if(!empty($timetable->reschedule)){
														foreach($timetable->reschedule as $reschedule){
															$temp['reschedule_id'] = $reschedule->id;
															$temp['timetable_id'] = $reschedule->timetable_id;
															$temp['strtime_from_time'] = strtotime($reschedule->from_time);
															$temp['from_time'] = $reschedule->from_time;
															$temp['to_time'] = $reschedule->to_time;
															$temp['faculty_reason'] = $reschedule->faculty_reason;
															$temp['admin_reason'] = $reschedule->admin_reason;
															$temp['status'] = $reschedule->status;
															$temp['topic_id'] = $timetable->topic->id;
															$temp['topic_name'] = $timetable->topic->name;
															$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
															$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
															$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
															$temp['date'] = $timetable->cdate;
															
															$get_faculty_reschedule[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								/* if(!empty($get_faculty_reschedule)){
									usort($get_faculty_reschedule, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								$data['faculty_reschedule'] = $get_faculty_reschedule;
								return $this->returnResponse(200, true, "Reschedule List", $data);
								
							}
							else{
								return $this->returnResponse(200, false, "Timetable Reschedule Not Found");
							}
							
						}
						else{ //Faculty Role  role_id = 2 OR ALL 

							$get_faculty_timetable_reschedule = Timetable::with(['reschedule','topic','chapter','faculty','course'])->where('faculty_id', $faculty_id)->orderBy('cdate','desc');
							
							if(!empty($request->date_from) && !empty($request->date_to)){
								$get_faculty_timetable_reschedule->where('cdate', '>=', $request->date_from);
								$get_faculty_timetable_reschedule->where('cdate', '<=', $request->date_to);
							}
							else{
								$get_faculty_timetable_reschedule->where('cdate', $current_date);
							}

							if(!empty($branch_id)){
								$get_faculty_timetable_reschedule->WhereHas('studio.branch', function ($q) use ($branch_id) {
										$q->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$get_faculty_timetable_reschedule->WhereHas('faculty', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$get_faculty_timetable_reschedule->WhereHas('faculty.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}

							// if(!empty($role_id)){
							// 	$get_faculty_timetable_reschedule->WhereHas('faculty', function ($q4) use ($role_id) {
							// 		$q4->where('role_id', $role_id);
							// 	});
							// }

							if(!empty($name)){
								$get_faculty_timetable_reschedule->WhereHas('faculty', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}
							
							$get_faculty_timetable_reschedule = $get_faculty_timetable_reschedule->get();
							

							if(count($get_faculty_timetable_reschedule) > 0){

								$get_faculty_reschedule = [];

								foreach($get_faculty_timetable_reschedule as $key => $timetable){
									if(!empty($timetable->reschedule)){
										foreach($timetable->reschedule as $reschedule){
											$temp['reschedule_id'] = $reschedule->id;
											$temp['timetable_id'] = $reschedule->timetable_id;
											$temp['strtime_from_time'] = strtotime($reschedule->from_time);
											$temp['from_time'] = $reschedule->from_time;
											$temp['to_time'] = $reschedule->to_time;
											$temp['faculty_reason'] = $reschedule->faculty_reason;
											$temp['admin_reason'] = $reschedule->admin_reason;
											$temp['status'] = $reschedule->status;
											$temp['topic_id'] = isset($timetable->topic->id)?$timetable->topic->id:0;
											$temp['topic_name'] = isset($timetable->topic->name)?$timetable->topic->name:'';
											$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
											$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
											$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
											$temp['date'] = $timetable->cdate;
											
											$get_faculty_reschedule[] = $temp;

										}
									}                
								}
								/* if(!empty($get_faculty_reschedule)){
									usort($get_faculty_reschedule, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */

								$data['faculty_reschedule'] = $get_faculty_reschedule;

								return $this->returnResponse(200, true, "Faculty Reschedule", $data);
							}else{
								return $this->returnResponse(200, false, "Faculty Timetable Reschedule Not Found");
							}
						}
					}
					else{
						return $this->returnResponse(200, false, "Faculty Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Faculty Id Not Found"); 
				}

    		}else{
    			return $this->returnResponse(200, false, "Faculty Id Not Found");
    		}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var get All Faculty
    * @param Request $request
    * @return Json Response
    */
    public function getfaculty(Request $request)
    {
    	try {
    		$faculty = User::select('id','name','mobile','email')->where('status', '1')->where('role_id', '2')->orderBy('id','desc')->get();

    		if($faculty){
    			$data['faculty'] = $faculty;
    			return $this->returnResponse(200, true, "Get All Faculty", $data);
    		}else{
    			return $this->returnResponse(200, false, "Faculty Not Found");
    		}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var get Single Faculty
    * @param Request $request
    * @return Json Response
    */
    public function getSingleFacultySchedule(Request $request)
    {
		return $this->returnResponse(200, false, "Please contact to timetable manager. Thanks !!"); exit;
    	try {

    		$faculty_id = $request->faculty_id;

    		if(isset($faculty_id) && !empty($faculty_id)){
				$user = User::where('id', $faculty_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						if(!empty($request->date)){
							$date = $request->date;
						}else{
							$date = date('Y-m-d');
						}
						$current_time = date("H:i");

						$get_faculty_timetable = Timetable::with([
							'faculty' => function($q){
								$q->select('id','name','mobile');
							},
						])->select('id','faculty_id','from_time','to_time','cdate')->where('faculty_id', $faculty_id)
						->where('cdate',$date)
						->where('from_time', ">=", $current_time)
						->get();

						if(count($get_faculty_timetable) > 0){
							$data['get_faculty_timetable'] = $get_faculty_timetable;
							return $this->returnResponse(200, true, "Get Faculty Timetable", $data);
						}else{
							return $this->returnResponse(200, false, "Faculty Timetable Not Found");
						}
					}
					else{
						return $this->returnResponse(200, false, "Faculty Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Faculty Id Not Found"); 
				}
    		}else{
    			return $this->returnResponse(200, false, 'Faculty Id Not Found.');
    		}
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var FacultyReschedule
    * @param Request $request
    * @return Json Response
    */
    public function swap(Request $request)
    {
		return $this->returnResponse(200, false, "Please contact to timetable manager. Thanks !!"); exit;
    	try {

    		$timetable_id = $request->timetable_id;
    		$swap_with_faculty_id = $request->swap_with_faculty_id;
    		$swap_timetable_id = $request->swap_timetable_id;

    		if($timetable_id == ''){
    			return $this->returnResponse(200, false, "Enter Timetable Id");
    		}
    		if($swap_with_faculty_id == ''){
    			return $this->returnResponse(200, false, "Enter Swap With Faculty Id");
    		}
    		if($swap_timetable_id == ''){
    			return $this->returnResponse(200, false, "Enter Swap Timetable Id");
    		}

    		$inputs = $request->only('timetable_id', 'swap_with_faculty_id','swap_timetable_id');

    		$inputs['timetable_id'] = $timetable_id;
    		$inputs['swap_with_faculty_id'] = $swap_with_faculty_id;
    		$inputs['swap_timetable_id'] = $swap_timetable_id;

    		$swap = Swap::create($inputs);

    		if($swap->save()){
    			return $this->returnResponse(200, true, "Faculty Swap Sucessfully.");
    		}else{
    			return $this->returnResponse(500, false, "Faculty Not Swap.");
    		}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var getFacultySchedule
    * @param Request $request
    * @return Json Response
    */
    public function scheduleswap(Request $request)
    {
		return $this->returnResponse(200, false, "Please contact to timetable manager. Thanks !!"); exit;
    	try {

    		$faculty_id         = $request->faculty_id;
    		$role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;
	        $name               = $request->name;

			if(isset($faculty_id) && !empty($faculty_id)){
				$user = User::where('id', $faculty_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$current_date = date('Y-m-d');
						$date_from = $request->date_from;	
						$date_to = $request->date_to;
						if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with([
								'timetable'=>function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'timetable.swap.s_timetable.topic','timetable.swap.faculty','timetable.swap.swap_timetable.topic','timetable.chapter','timetable.faculty','timetable.course'])->where('assistant_id', $faculty_id);
							
							$studios->WhereHas('timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$studios->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$studios->WhereHas('assistant', function ($q3) use ($department_head_id) {
									$q3->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$studios->WhereHas('assistant.user_details', function ($q4) use ($designation) {
									$q4->where('degination', $designation);
								});
							}

							if(!empty($name)){
								$studios->WhereHas('assistant', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
						    }

							$studios = $studios->first(); //echo '<pre>'; print_r($studios);die;
							if(!empty($studios)){
								$get_faculty_swap_timetable = [];
								if(!empty($studios->timetable)){
									foreach($studios->timetable as $key => $timetable){
										if(!empty($timetable->swap)){
											foreach($timetable->swap as $swap){
												$temp['swap_id'] = $swap->id;
												$temp['timetable_id'] = $swap->timetable_id;
												$temp['swap_with_faculty_id'] = $swap->swap_with_faculty_id;
												$temp['swap_timetable_id'] = $swap->swap_timetable_id;
												$temp['status'] = $swap->status;
												$temp['topic_id'] = $swap->s_timetable->topic->id;
												$temp['topic_name'] = $swap->s_timetable->topic->name;
												$temp['strtime_from_time'] = strtotime($swap->s_timetable->from_time);
												$temp['from_time'] = $swap->s_timetable->from_time;
												$temp['to_time'] = $swap->s_timetable->to_time;
												$temp['date'] = $swap->s_timetable->cdate;
												$temp['faculty_id'] = isset($swap->faculty->id)?$swap->faculty->id:0;
												$temp['swap_with_faculty_name'] = isset($swap->faculty->name)?$swap->faculty->name:'';
												$temp['swap_from_time'] = $swap->swap_timetable->from_time;
												$temp['swap_to_time'] = $swap->swap_timetable->to_time;
												$temp['swap_date'] = $swap->swap_timetable->cdate;
												$temp['swap_topic_id'] = $swap->swap_timetable->topic->id;
												$temp['swap_topic_name'] = $swap->swap_timetable->topic->name;
												$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
												$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
												$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
												
												$get_faculty_swap_timetable[] = $temp;
											}
										}
									}
								}
								/* if(!empty($get_faculty_swap_timetable)){
									usort($get_faculty_swap_timetable, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								$data['get_faculty_swap_timetable'] = $get_faculty_swap_timetable;
								return $this->returnResponse(200, true, "Swap List", $data);
							}
							else{
								return $this->returnResponse(200, false, "Swap TimeTable Not Found.");
							}
						}
						else if($user->role_id==4 || $user->role_id==27){ //4 = Studio Manager Role // 27 = Time table Manager
							$branches = Userbranches::with(['studio','studio.timetable'=>function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'studio.timetable.swap.s_timetable.topic','studio.timetable.swap.faculty','studio.timetable.swap.swap_timetable.topic','studio.timetable.faculty','studio.timetable.chapter','studio.timetable.course'])->where('user_id', $faculty_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q1) use ($branch_id) {
									$q1->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}

							if(!empty($name)){
								$branches->WhereHas('user', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
						    }

							$branches = $branches->groupBy('branch_id')->get();
							if(!empty($branches)){
								$get_faculty_swap_timetable = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $timetable){
													if(!empty($timetable->swap)){
														foreach($timetable->swap as $swap){
															$temp['swap_id'] = $swap->id;
															$temp['timetable_id'] = $swap->timetable_id;
															$temp['swap_with_faculty_id'] = $swap->swap_with_faculty_id;
															$temp['swap_timetable_id'] = $swap->swap_timetable_id;
															$temp['status'] = $swap->status;
															$temp['topic_id'] = $swap->s_timetable->topic->id;
															$temp['topic_name'] = $swap->s_timetable->topic->name;
															$temp['strtime_from_time'] = strtotime($swap->s_timetable->from_time);
															$temp['from_time'] = $swap->s_timetable->from_time;
															$temp['to_time'] = $swap->s_timetable->to_time;
															$temp['date'] = $swap->s_timetable->cdate;
															$temp['faculty_id'] = isset($swap->faculty->id)?$swap->faculty->id:0;
															$temp['swap_with_faculty_name'] = isset($swap->faculty->name)?$swap->faculty->name:'';
															$temp['swap_from_time'] = $swap->swap_timetable->from_time;
															$temp['swap_to_time'] = $swap->swap_timetable->to_time;
															$temp['swap_date'] = $swap->swap_timetable->cdate;
															$temp['swap_topic_id'] = $swap->swap_timetable->topic->id;
															$temp['swap_topic_name'] = $swap->swap_timetable->topic->name;
															$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
															$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
															$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
															$get_faculty_swap_timetable[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								/* if(!empty($get_faculty_swap_timetable)){
									usort($get_faculty_swap_timetable, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								
								$data['get_faculty_swap_timetable'] = $get_faculty_swap_timetable;
								return $this->returnResponse(200, true, "Swap List", $data);
							}
							else{
								return $this->returnResponse(200, false, "Swap TimeTable Not Found.");
							}
						}
						else if($user->role_id==29){ //Super Admin
							$branches = Userbranches::with(['studio','studio.timetable'=>function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							},'studio.timetable.swap.s_timetable.topic','studio.timetable.swap.faculty','studio.timetable.swap.swap_timetable.topic','studio.timetable.faculty','studio.timetable.chapter','studio.timetable.course']);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to, $current_date) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', $current_date);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q3) use ($department_head_id) {
									$q3->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q4) use ($designation) {
									$q4->where('degination', $designation);
								});
							}

							if(!empty($role_id)){
								$branches->WhereHas('user', function ($q5) use ($role_id) {
									$q5->where('role_id', $role_id);
								});
							}

							if(!empty($name)){
								$branches->WhereHas('user', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
						    }

							$branches = $branches->groupBy('branch_id')->get();
							if(!empty($branches)){
								$get_faculty_swap_timetable = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $timetable){
													if(!empty($timetable->swap)){
														foreach($timetable->swap as $swap){
															$temp['swap_id'] = $swap->id;
															$temp['timetable_id'] = $swap->timetable_id;
															$temp['swap_with_faculty_id'] = $swap->swap_with_faculty_id;
															$temp['swap_timetable_id'] = $swap->swap_timetable_id;
															$temp['status'] = $swap->status;
															$temp['topic_id'] = $swap->s_timetable->topic->id;
															$temp['topic_name'] = $swap->s_timetable->topic->name;
															$temp['strtime_from_time'] = strtotime($swap->s_timetable->from_time);
															$temp['from_time'] = $swap->s_timetable->from_time;
															$temp['to_time'] = $swap->s_timetable->to_time;
															$temp['date'] = $swap->s_timetable->cdate;
															$temp['faculty_id'] = isset($swap->faculty->id)?$swap->faculty->id:0;
															$temp['swap_with_faculty_name'] = isset($swap->faculty->name)?$swap->faculty->name:'';
															$temp['swap_from_time'] = $swap->swap_timetable->from_time;
															$temp['swap_to_time'] = $swap->swap_timetable->to_time;
															$temp['swap_date'] = $swap->swap_timetable->cdate;
															$temp['swap_topic_id'] = $swap->swap_timetable->topic->id;
															$temp['swap_topic_name'] = $swap->swap_timetable->topic->name;
															$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
															$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
															$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
															
															$get_faculty_swap_timetable[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								/* if(!empty($get_faculty_swap_timetable)){
									usort($get_faculty_swap_timetable, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */
								
								$data['get_faculty_swap_timetable'] = $get_faculty_swap_timetable;
								return $this->returnResponse(200, true, "Swap List", $data);
							}
							else{
								return $this->returnResponse(200, false, "Swap TimeTable Not Found.");
							}
						}
						else{ // Faculty Role  role_id = 2 OR ALL 
							$get_swap_timetable = Timetable::with(['swap.s_timetable.topic','swap.faculty','swap.swap_timetable.topic','chapter','faculty','course']);
							
							if(!empty($request->date_from) && !empty($request->date_to)){
								$get_swap_timetable->where('cdate', '>=', $request->date_from);
								$get_swap_timetable->where('cdate', '<=', $request->date_to);
							}
							else{
								$get_swap_timetable->where('cdate', $current_date);
							}

							if(!empty($branch_id)){
								$get_swap_timetable->WhereHas('studio.branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);	
								});
							}

							if(!empty($department_head_id)){
								$get_swap_timetable->WhereHas('faculty', function ($q3) use ($department_head_id) {
									$q3->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$get_swap_timetable->WhereHas('faculty.user_details', function ($q4) use ($designation) {
									$q4->where('degination', $designation);
								});
							}

							if(!empty($name)){
								$get_swap_timetable->WhereHas('faculty', function ($q5) use ($name) {
									$q5->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
						    }

							$get_swap_timetable = $get_swap_timetable->where('faculty_id', $faculty_id)->orderBy('cdate','desc')->get();

							if(count($get_swap_timetable) > 0){

								$get_faculty_swap_timetable = [];

								foreach($get_swap_timetable as $key => $swap_timetable){
									if(!empty($swap_timetable->swap)){
										foreach($swap_timetable->swap as $swap){
											$temp['swap_id'] = $swap->id;
											$temp['timetable_id'] = $swap->timetable_id;
											$temp['swap_with_faculty_id'] = $swap->swap_with_faculty_id;
											$temp['swap_timetable_id'] = $swap->swap_timetable_id;
											$temp['status'] = $swap->status;
											$temp['topic_id'] = isset($swap->s_timetable->topic->id)?$swap->s_timetable->topic->id:0;
											$temp['topic_name'] = isset($swap->s_timetable->topic->name)?$swap->s_timetable->topic->name:'';
											$temp['strtime_from_time'] = strtotime($swap->s_timetable->from_time);
											$temp['from_time'] = $swap->s_timetable->from_time;
											$temp['to_time'] = $swap->s_timetable->to_time;
											$temp['date'] = $swap->s_timetable->cdate;
											$temp['faculty_id'] = $swap->faculty->id;
											$temp['swap_with_faculty_name'] = $swap->faculty->name;
											$temp['swap_from_time'] = $swap->swap_timetable->from_time;
											$temp['swap_to_time'] = $swap->swap_timetable->to_time;
											$temp['swap_date'] = $swap->swap_timetable->cdate;
											$temp['swap_topic_id'] = isset($swap->swap_timetable->topic->id)?$swap->swap_timetable->topic->id:0;
											$temp['swap_topic_name'] = isset($swap->swap_timetable->topic->name)?$swap->swap_timetable->topic->name:'';
											$temp['chapter_name'] = isset($swap_timetable->chapter->name)?$swap_timetable->chapter->name:'';
											$temp['faculty_name'] = isset($swap_timetable->faculty->name)?$swap_timetable->faculty->name:'';
											$temp['course_name'] = isset($swap_timetable->course->name)?$swap_timetable->course->name:'';
											$get_faculty_swap_timetable[] = $temp;
										}
									}                
								}
								/* if(!empty($get_faculty_swap_timetable)){
									usort($get_faculty_swap_timetable, function($a, $b) {
										return $b['strtime_from_time'] <= $a['strtime_from_time']; // <=> for desc
									});
								} */

								$data['get_faculty_swap_timetable'] = $get_faculty_swap_timetable;

								return $this->returnResponse(200, true, "Get Swap TimeTable", $data);
							}else{
								return $this->returnResponse(200, false, "Swap TimeTable Not Found.");
							}
						}
					}
					else{
						return $this->returnResponse(200, false, "Swap with Faculty Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Swap with Faculty Not Found"); 
				}
    		}else{
    			return $this->returnResponse(500, false, "Swap with Faculty Id Not Found.");
    		}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

    /**
    * @var facultycancelclass
    * @param Request $request
    * @return Json Response
    */
    public function cancelclass(Request $request)
    {
		return $this->returnResponse(200, false, "Please contact to timetable manager. Thanks !!"); exit;
    	try {

    		$timetable_id = $request->timetable_id;
    		$days = $request->days;
    		$faculty_reason = $request->faculty_reason;

    		if($timetable_id == ''){
    			return $this->returnResponse(200, false, "Enter Timetable Id");
    		}
    		if($days == ''){
    			return $this->returnResponse(200, false, "Enter Days");
    		}
    		if($faculty_reason == ''){
    			return $this->returnResponse(200, false, "Enter Faculty Reason");
    		}

    		$inputs = $request->only('timetable_id','days','faculty_reason','admin_reason');

    		$inputs['timetable_id'] = $timetable_id;
    		$inputs['days'] = $days;
    		$inputs['faculty_reason'] = $faculty_reason;

    		$cancelclass = CancelClass::create($inputs);

    		if($cancelclass->save()){
    			return $this->returnResponse(200, true, "Faculty Class Cancel Sucessfully.");
    		}else{
    			return $this->returnResponse(200, false, "Faculty Class Not Cancel");
    		}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }

     /**
    * @var getFacultycalcelclass
    * @param Request $request
    * @return Json Response
    */
    public function GetCancelClass(Request $request)
    {
     	try {

     		$faculty_id         = $request->faculty_id;
            $role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;
	        $name               = $request->name;


     		if(isset($faculty_id) && !empty($faculty_id)){
				$user = User::where('id', $faculty_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$date_from = $request->date_from;
						$date_to = $request->date_to;
						if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with(['branch',
								'timetable'=>function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								$q->orderBy('cdate','desc');
							},
							'timetable.cancelclass',
							'timetable.topic' => function($q){
								$q->select('id','name');
							},
							'timetable.chapter','timetable.faculty','timetable.course'
							])->where('assistant_id', $faculty_id);
							
							$studios->WhereHas('timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								$q->orderBy('cdate','desc');
							});

                            if(!empty($branch_id)){
								$studios->WhereHas('branch', function ($q2) use ($branch_id) {
									$q2->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$studios->WhereHas('assistant', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$studios->WhereHas('assistant.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}
							if(!empty($name)){
								$studios->WhereHas('assistant', function ($q4) use ($name) {
									$q4->where('name', 'LIKE', '%' . $name . '%')
											->orWhere('register_id', 'LIKE', '%' . $name)
											->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}

							$studios = $studios->first();
							if(!empty($studios)){
								$get_faculty_cancelclass = [];
								if(!empty($studios->timetable)){
									foreach($studios->timetable as $key => $getcancelclass){
										if(!empty($getcancelclass->cancelclass)){
											foreach ($getcancelclass->cancelclass as $cancelclass) {
												$temp['cancel_id'] = $cancelclass->id;
												$temp['timetable_id'] = $cancelclass->timetable_id;
												$temp['days'] = $cancelclass->days;
												$temp['faculty_reason'] = $cancelclass->faculty_reason;
												$temp['admin_reason'] = $cancelclass->admin_reason;
												$temp['status'] = $cancelclass->status;
												$temp['topic_id'] = $getcancelclass->topic->id;
												$temp['topic_name'] = $getcancelclass->topic->name;
												$temp['chapter_name'] = isset($getcancelclass->chapter->name)?$getcancelclass->chapter->name:'';
												$temp['faculty_name'] = isset($getcancelclass->faculty->name)?$getcancelclass->faculty->name:'';
												$temp['course_name'] = isset($getcancelclass->course->name)?$getcancelclass->course->name:'';
												$temp['date'] = $getcancelclass->cdate;
												
												$get_faculty_cancelclass[] = $temp;
											}
										}
									}
								}
								$data['faculty_cancelclass'] = $get_faculty_cancelclass;
								return $this->returnResponse(200, true, "Faculty Cancel Class", $data);
							}
							else{
								return $this->returnResponse(200, false, "Faculty Cancel Class Not Found.");
							}
						}
						else if($user->role_id==4 || $user->role_id==27){ //4 = Studio Manager Role // 27 = Time table Manager
							$branches = Userbranches::with(['studio',
								'studio.timetable'=>function ($q) use ($date_from, $date_to) {
									if(!empty($date_from) && !empty($date_to) ){
										$q->where('cdate', '>=', $date_from);
										$q->where('cdate', '<=', $date_to);
									}
									$q->orderBy('cdate','desc');
								},
								'studio.timetable.cancelclass',
								'studio.timetable.topic' => function($q){
									$q->select('id','name');
								},
								'studio.timetable.faculty','studio.timetable.chapter','studio.timetable.course'
							])->where('user_id', $faculty_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q1) use ($branch_id) {
									$q1->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}
							if(!empty($name)){
								$branches->WhereHas('user', function ($q4) use ($name) {
									$q4->where('name', 'LIKE', '%' . $name . '%')
											->orWhere('register_id', 'LIKE', '%' . $name)
											->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}

							$branches = $branches->groupBy('branch_id')->get();
							if(!empty($branches)){
								$get_faculty_cancelclass = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $getcancelclass){
													if(!empty($getcancelclass->cancelclass)){
														foreach ($getcancelclass->cancelclass as $cancelclass) {
															$temp['cancel_id'] = $cancelclass->id;
															$temp['timetable_id'] = $cancelclass->timetable_id;
															$temp['days'] = $cancelclass->days;
															$temp['faculty_reason'] = $cancelclass->faculty_reason;
															$temp['admin_reason'] = $cancelclass->admin_reason;
															$temp['status'] = $cancelclass->status;
															$temp['topic_id'] = $getcancelclass->topic->id;
															$temp['topic_name'] = $getcancelclass->topic->name;
															$temp['chapter_name'] = isset($getcancelclass->chapter->name)?$getcancelclass->chapter->name:'';
															$temp['faculty_name'] = isset($getcancelclass->faculty->name)?$getcancelclass->faculty->name:'';
															$temp['course_name'] = isset($getcancelclass->course->name)?$getcancelclass->course->name:'';
															$temp['date'] = $getcancelclass->cdate;
															
															$get_faculty_cancelclass[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								
								$data['faculty_cancelclass'] = $get_faculty_cancelclass;
								return $this->returnResponse(200, true, "Faculty Cancel Class", $data);
							}
							else{
								return $this->returnResponse(200, false, "Faculty Cancel Class Not Found.");
							}
						}
						else if($user->role_id==29){ //Super Admin
							$branches = Userbranches::with(['studio',
								'studio.timetable'=>function ($q) use ($date_from, $date_to) {
									if(!empty($date_from) && !empty($date_to) ){
										$q->where('cdate', '>=', $date_from);
										$q->where('cdate', '<=', $date_to);
									}
									$q->orderBy('cdate','desc');
								},
								'studio.timetable.cancelclass',
								'studio.timetable.topic' => function($q){
									$q->select('id','name');
								},
								'studio.timetable.faculty',
								'studio.timetable.chapter',
								'studio.timetable.course'
							])->where('user_id', $faculty_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								$q->orderBy('cdate','desc');
							});

							if(!empty($branch_id)){
								$branches->WhereHas('branch', function ($q1) use ($branch_id) {
									$q1->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$branches->WhereHas('user', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$branches->WhereHas('user.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}
							if(!empty($name)){
								$branches->WhereHas('user', function ($q4) use ($name) {
									$q4->where('name', 'LIKE', '%' . $name . '%')
											->orWhere('register_id', 'LIKE', '%' . $name)
											->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}

							if(!empty($role_id)){
								$branches->WhereHas('user', function ($q5) use ($role_id) {
									$q5->where('role_id', $role_id);
								});
							}

							$branches = $branches->groupBy('branch_id')->get();
							//echo '<pre>'; print_r($branches);die;
							if(!empty($branches)){
								$get_faculty_cancelclass = [];
								foreach($branches as $key=>$value){
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											if(!empty($studios->timetable)){
												foreach($studios->timetable as $key => $getcancelclass){
													if(!empty($getcancelclass->cancelclass)){
														foreach ($getcancelclass->cancelclass as $cancelclass) {
															$temp['cancel_id'] = $cancelclass->id;
															$temp['timetable_id'] = $cancelclass->timetable_id;
															$temp['days'] = $cancelclass->days;
															$temp['faculty_reason'] = $cancelclass->faculty_reason;
															$temp['admin_reason'] = $cancelclass->admin_reason;
															$temp['status'] = $cancelclass->status;
															$temp['topic_id'] = $getcancelclass->topic->id;
															$temp['topic_name'] = $getcancelclass->topic->name;
															$temp['chapter_name'] = isset($getcancelclass->chapter->name)?$getcancelclass->chapter->name:'';
															$temp['faculty_name'] = isset($getcancelclass->faculty->name)?$getcancelclass->faculty->name:'';
															$temp['course_name'] = isset($getcancelclass->course->name)?$getcancelclass->course->name:'';
															$temp['date'] = $getcancelclass->cdate;
															
															$get_faculty_cancelclass[] = $temp;
														}
													}
												}
											}
										}
									}
								}
								
								$data['faculty_cancelclass'] = $get_faculty_cancelclass;
								return $this->returnResponse(200, true, "Faculty Cancel Class", $data);
							}
							else{
								return $this->returnResponse(200, false, "Faculty Cancel Class Not Found.");
							}
						}
						else{
							$get_faculty_cancelclassess = Timetable::with([
								'cancelclass',
								'topic' => function($q){
									$q->select('id','name');
								},
								'chapter','faculty','course'
							])->where('faculty_id', $faculty_id)->orderBy('cdate','desc');
							
							if(!empty($request->date_from) && !empty($request->date_to)){
								$get_faculty_cancelclassess->where('cdate', '>=', $request->date_from);
								$get_faculty_cancelclassess->where('cdate', '<=', $request->date_to);
							}

							if(!empty($branch_id)){
								$get_faculty_cancelclassess->WhereHas('studio.branch', function ($q1) use ($branch_id) {
									$q1->where('id', $branch_id);
								});
							}

							if(!empty($department_head_id)){
								$get_faculty_cancelclassess->WhereHas('faculty', function ($q2) use ($department_head_id) {
									$q2->where('id', $department_head_id);
								});
							}

							if(!empty($designation)){
								$get_faculty_cancelclassess->WhereHas('faculty.user_details', function ($q3) use ($designation) {
									$q3->where('degination', $designation);
								});
							}
							if(!empty($name)){
								$get_faculty_cancelclassess->WhereHas('faculty', function ($q4) use ($name) {
									$q4->where('name', 'LIKE', '%' . $name . '%')
											->orWhere('register_id', 'LIKE', '%' . $name)
											->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}
							
							$get_faculty_cancelclassess = $get_faculty_cancelclassess->get();

							if(count($get_faculty_cancelclassess) > 0){

								$get_faculty_cancelclass = [];

								foreach($get_faculty_cancelclassess as $key => $getcancelclass){
									if(!empty($getcancelclass->cancelclass)){
										foreach ($getcancelclass->cancelclass as $cancelclass) {
											$temp['cancel_id'] = $cancelclass->id;
											$temp['timetable_id'] = $cancelclass->timetable_id;
											$temp['days'] = $cancelclass->days;
											$temp['faculty_reason'] = $cancelclass->faculty_reason;
											$temp['admin_reason'] = $cancelclass->admin_reason;
											$temp['status'] = $cancelclass->status;
											$temp['topic_id'] = isset($getcancelclass->topic->id)?$getcancelclass->topic->id:0;
											$temp['topic_name'] = isset($getcancelclass->topic->name)?$getcancelclass->topic->name:'';
											$temp['chapter_name'] = isset($getcancelclass->chapter->name)?$getcancelclass->chapter->name:'';
											$temp['faculty_name'] = isset($getcancelclass->faculty->name)?$getcancelclass->faculty->name:'';
											$temp['course_name'] = isset($getcancelclass->course->name)?$getcancelclass->course->name:'';
											$temp['date'] = $getcancelclass->cdate;
											
											$get_faculty_cancelclass[] = $temp;
										}
									}
								}

								$data['faculty_cancelclass'] = $get_faculty_cancelclass;

								return $this->returnResponse(200, true, "Faculty Cancel Class", $data);

							}
							else{
								return $this->returnResponse(200, false, "Faculty Cancel Class Not Found.");
							}
						}
					}
					else{
						return $this->returnResponse(200, false, "Faculty Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Faculty Id Not Found"); 
				}

     		}else{
     			return $this->returnResponse(200, false, "Faculty Id Not Found.");
     		} 

     	} catch (\Illuminate\Database\QueryException $ex) {
     		return $this->returnResponse(500, false, $ex->getMessage());
     	} catch (ModelNotFoundException $ex) {
     		return $this->returnResponse(500, false, $ex->getMessage());
     	}
    }
    
    public function update_cancelclass(Request $request){
		try {
			$id     = $request->cancel_id;
			$inputs['status'] = $request->status; 

			$cancelclass = CancelClass::where('id', $id)->first();
            //echo '<pre>'; print_r($cancelclass);die;
			if($request->status == 'Approved'){

					$days = '+'.$cancelclass->days . 'day';

					$nxtday = date('Y-m-d', strtotime($days));

					$get_timetable_data = Timetable::with('faculty')->where('id', $cancelclass->timetable_id)->first();

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

							$inputs['status'] = 'Reject';

							if($get_timetable_data){
								if($get_timetable_data->faculty->gsm_token){

									$load = array();
									$load['title'] = 'Error';
									$load['admin_reason'] = 'Class Already Exists';
									$load['status'] = 'Reject';
									$load['type'] = 'faculty_cancel';

									$token = $get_timetable_data->faculty->gsm_token;

									//$this->android_notification($token, $load);
								}   
							}

							$cancelclass->update($inputs);

							return $this->returnResponse(200, false, "Class Already Exists");
						}else{

							$get_timetable_data->cdate = $nxtday;
							$get_timetable_data->update();

							if($get_timetable_data){
							if($get_timetable_data->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Success';
								$load['admin_reason'] = $request->admin_reason;
								$load['status'] = 'Approved';
								$load['type'] = 'faculty_cancel';

								$token = $get_timetable_data->faculty->gsm_token;

								//$this->android_notification($token, $load);
							}   
							}

							if ($cancelclass->update($inputs)) {
							return $this->returnResponse(200, true, "Class Cancel Approved Successfully");
							} else {
							return $this->returnResponse(200, false, "Something Went Wrong !");
							}
					    }
					}else{

						$get_timetable_data->cdate = $nxtday;
						$get_timetable_data->update();

						if($get_timetable_data){
							if($get_timetable_data->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Success';
								$load['admin_reason'] = $request->admin_reason;
								$load['status'] = 'Approved';
								$load['type'] = 'faculty_cancel';

								$token = $get_timetable_data->faculty->gsm_token;

								//$this->android_notification($token, $load);
							}   
						}

						if($cancelclass->update($inputs)){
							return $this->returnResponse(200, true, "Class Cancel Approved Successfully");
						}else{
							return $this->returnResponse(200, false, "Class Cancel Not Approved");
						}    
					}
			}else{

				$inputs['status'] = 'Reject';

				$get_faculty = Timetable::with('faculty')->where('id', $cancelclass->timetable_id)->first();

				if($get_faculty){
					if($get_faculty->faculty->gsm_token){

						$load = array();
						$load['title'] = 'Error';
						$load['admin_reason'] = $request->admin_reason;
						$load['status'] = 'Reject';
						$load['type'] = 'faculty_cancel';

						$token = $get_faculty->faculty->gsm_token;

						//$this->android_notification($token, $load);
					}   
				}

				if ($cancelclass->update($inputs)) {
					return $this->returnResponse(200, true, "Class Cancel Request Rejected");
				} else {
					return $this->returnResponse(200, false, "Something Went Wrong !");
				}
			}


		} catch (\Illuminate\Database\QueryException $ex) {
		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
    
    public function update_swap(Request $request){
		try {
			$id   = $request->swap_id;
			$swap = Swap::where('id', $id)->first();
			
			if(!empty($swap)){
				
				$get_timetable      = Timetable::where('id',$swap->timetable_id)->first();
			    $get_swap_timetable = Timetable::where('id',$swap->swap_timetable_id)->first();
			
				$inputs = $request->only('status');
				if($swap->status == $request->status){
					return $this->returnResponse(200, false, "No anything changed!");
				}
				else{
					if($request->status == 'Approved'){
						$get_timetables      = Timetable::with('faculty')->where('id',$swap->timetable_id)->first();
						$get_swap_timetables = Timetable::with('faculty')->where('id',$swap->swap_timetable_id)->first();
                       
						$get_timetable->update([
							'from_time' => $get_swap_timetables->from_time,
							'to_time'   => $get_swap_timetables->to_time,
							'studio_id' => $get_swap_timetables->studio_id,
						]);

						$get_swap_timetable->update([
							'from_time' => $get_timetables->from_time,
							'to_time'   => $get_timetables->to_time,
							'studio_id' => $get_timetables->studio_id,
						]);        

						if($get_timetable){
							if($get_timetable->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Success';
								$load['description'] = 'Class Swap Successfully';
								$load['status'] = 'Approved';
								$load['type'] = 'faculty_swap';

								$token = $get_timetable->faculty->gsm_token;

								//$this->android_notification($token, $load);
							}   
						}

						if($get_swap_timetable){
							if($get_swap_timetable->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Success';
								$load['description'] = 'Class Swap Successfully';
								$load['status'] = 'Approved';
								$load['type'] = 'faculty_swap';

								$token = $get_swap_timetable->faculty->gsm_token;
								
								//$this->android_notification($token, $load);
							}   
						}        
					}
					else{

						$get_faculty = Timetable::with('faculty')->where('id', $swap->timetable_id)->first();

						if($get_faculty){
							if($get_faculty->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Error';
								$load['description'] = 'Class Not Swap. Status has been changed '.$swap->status. " to ".$request->status;
								$load['status'] = $request->status;
								$load['type'] = 'faculty_swap';

								$token = $get_faculty->faculty->gsm_token;

								//$this->android_notification($token, $load);
							}   
						}       
					}

					if ($swap->update($inputs)) {
						return $this->returnResponse(200, true, "Swap Request Updated Successfully");
					} else {
						return $this->returnResponse(200, false, "Something Went Wrong !");
					}
				}
			}
			else{
				return $this->returnResponse(200, false, "Something Went Wrong !");
			}
		
		} catch (\Illuminate\Database\QueryException $ex) {
		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
    public function update_reschedule(Request $request){
		try {
			$id         = $request->reschedule_id;
			$reschedule = Reschedule::where('id', $id)->first();
			if(!empty($reschedule)){
			if($request->status == 'Approved'){

				$get_timetable_data = Timetable::where('id', $reschedule->timetable_id)->first();

				$minutes =  round(abs(strtotime($get_timetable_data->from_time) - strtotime($get_timetable_data->to_time)) / 60,2);

				$reschedule_from_time = strtotime($reschedule->from_time) + $minutes*60;

				$get_reschedule_to_time = date('H:i', $reschedule_from_time);
				
				$get_from_time_id = 0;
				$from_time_id = TimeSlot::where('time_slot',$reschedule->from_time)->first();  //echo '<pre>'; print_r($reschedule);die;
				if(!empty($from_time_id)){
					$get_from_time_id = $from_time_id->id;
				}
				
				$get_to_time_id = 0;
				$to_time_id = TimeSlot::where('time_slot',$get_reschedule_to_time)->first();
				if(!empty($to_time_id)){
					$get_to_time_id = $to_time_id->id;
				}
				

				$get_studio_timetable = Timetable::where('studio_id', $get_timetable_data->studio_id)->where('faculty_id', $get_timetable_data->faculty_id)->where('cdate', $get_timetable_data->cdate)->get();

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
						if($get_from_time_id > 0 && $get_from_time_id>=$from_time2[$i] && $get_from_time_id<=$to_time2[$i])
						{
							$chk_condition = 'true';
						}else if($get_to_time_id > 0 && $get_to_time_id>=$from_time2[$i] && $get_to_time_id<=$to_time2[$i]){
							$chk_condition = 'true';
						}
					}

					if($chk_condition == 'true'){

						$inputs = $request->only('from_time','faculty_reason','status');

						$inputs['status'] = 'Reject';

						$get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

						if($get_faculty){
							if($get_faculty->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Error';
								$load['admin_reason'] = 'Class Already Exists';
								$load['status'] = 'Reject';
								$load['type'] = 'faculty_reschedule';

								$token = $get_faculty->faculty->gsm_token;

								$this->android_notification($token, $load);
							}   
						}                    

						if ($reschedule->update($inputs)) {
							return $this->returnResponse(200, false, "Class Already Exists.");
						}
					}else{

						$inputs = $request->only('from_time','faculty_reason', 'admin_reason','status');

						$inputs['status'] = 'Approved';
						if(!empty($reschedule->studio_id)){
							$get_timetable_data->studio_id = $reschedule->studio_id;
						}
						$get_timetable_data->from_time = $reschedule->from_time;
						$get_timetable_data->to_time = $get_reschedule_to_time;
						$get_timetable_data->update();                   

						$get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

						if($get_faculty){
							if($get_faculty->faculty->gsm_token){

								$load = array();
								$load['title'] = 'Success';
								$load['admin_reason'] = $request->admin_reason;
								$load['status'] = 'Approved';
								$load['type'] = 'faculty_reschedule';

								$token = $get_faculty->faculty->gsm_token;

								$this->android_notification($token, $load);
							}   
						}                    
						
						if ($reschedule->update($inputs)) {
							return $this->returnResponse(200, true, "Reschedule Request Updated Successfully");
						} else {
							return $this->returnResponse(200, false, "Something Went Wrong !");
						}
					}

				}else{
					return redirect()->back()->with('error', 'Studio Time Table Not Found');
				}
			}else{

				$inputs = $request->only('from_time','faculty_reason', 'admin_reason','status');

				$inputs['status'] = 'Reject';            

				$get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

				if($get_faculty){
					if($get_faculty->faculty->gsm_token){

						$load = array();
						$load['title'] = 'Error';
						$load['admin_reason'] = $request->admin_reason;
						$load['status'] = 'Reject';
						$load['type'] = 'faculty_reschedule';

						$token = $get_faculty->faculty->gsm_token;

						$this->android_notification($token, $load);
					}   
				}           

				if ($reschedule->update($inputs)) {
					return $this->returnResponse(200, true, "Reschedule Request Updated Successfully");
				}

			}  
			}
			else{
				return $this->returnResponse(200, false, "Reschedule Id invalid.");
			}
    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
 }
