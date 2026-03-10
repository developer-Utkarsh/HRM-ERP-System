<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Timetable;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use Input;
use App\FacultyRelation;
use App\ApiNotification;
use App\Users_pending;
use App\Userdetails_pending;
use App\FacultyRelations_pending;
use App\Studio;
use App\Userbranches;
use App\StartClass;
use App\TimeSlot;
use App\Branch;
use Auth;
use DB;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class StudioController extends Controller
{
	public function get_list(Request $request)
    {
        try{
            $emp_id = $request->emp_id;
            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->where('register_id', '!=', NULL)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$i = 0;
						$branches = User::with(['user_branches','user_branches.studio'])->where('id', $emp_id)->where('role_id', 4)->first();
						// echo "<pre>"; print_r($branches); die;
						$studio_array = array();
						if(!empty($branches)){
							if(count($branches->user_branches) > 0){
								foreach($branches->user_branches as $branchDetails){
									if(count($branchDetails->studio) > 0){
										foreach($branchDetails->studio as $studioDetails){
											
											$data_array['id'] = $studioDetails->id;
											$data_array['name'] = $studioDetails->name;
											
											$studio_array[] = $data_array;
										}
									}
								}
							}
						} 
						
						$data['studios'] = $studio_array;
						return $this->returnResponse(200, true, "studio List", $data);
					}
					else{
						return $this->returnResponse(200, false, "User Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "User Id Not Found"); 
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function get_slot_available(Request $request){
		try{
			$timetable_id        = $request->timetable_id;
			$branch_id           = $request->branch_id;
			$time_slot_avalible  = [];
			$get_slot_to_time    = Timetable::where('id', $timetable_id)->first();
			
			$slot_difference_time = (strtotime($get_slot_to_time->to_time)-strtotime($get_slot_to_time->from_time))/60*60;
			$from_time_get        = date("H:i");
			// $to_time_get          = date("H:i",strtotime($from_time_get)+$slot_difference_time);
			
			$get_slot_by_to_time  = TimeSlot::where('time_slot', '>', $from_time_get)->get();
			 
			if(count($get_slot_by_to_time) > 0){
			    foreach($get_slot_by_to_time as $get_slot_by_to_time_value){
					$from_time_get  = $get_slot_by_to_time_value->time_slot;
					$to_time_get    = date("H:i",strtotime($from_time_get)+$slot_difference_time);
					$get_studio     = Studio::where('branch_id', $branch_id)->whereNotNull('assistant_id')->get();
					 
					if(count($get_studio) > 0){
						foreach($get_studio as $get_studio_value){
							if(!empty($from_time_get) && !empty($to_time_get)){
								
								$from_time_id     = TimeSlot::where('time_slot', $from_time_get)->first();
								$get_from_time_id = $from_time_id->id;
								$to_time_id       = TimeSlot::where('time_slot', $to_time_get)->first();
								if(!empty($to_time_id)){
									$get_to_time_id   = $to_time_id->id;
									  
									$get_studio_timetable = Timetable::where('studio_id', $get_studio_value->id)
															->where('cdate', date('Y-m-d'))
															->get();
															
									if (count($get_studio_timetable) > 0){
										$from_time2 = [];
										$to_time2   = [];
										
										foreach ($get_studio_timetable as $value)
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
									   
										if ($chk_condition == 'false')
										{
											$row['avaliable_time'] = $from_time_get;
											$row['studio_id']      = $get_studio_value->id;
											//$row['timetable_id']   = $timetable_id;
											$time_slot_avalible[]  = $row;
											break;
										}
										
									}
									else{
										$row['avaliable_time'] = $from_time_get;
										$row['studio_id']      = $get_studio_value->id;
										//$row['timetable_id']   = $timetable_id;
						
										$time_slot_avalible[]  = $row;
										break;
									}
								}								
							}
							else{
								return response(['status' => false, 'message' => 'From time and To time required.'], 200);
							}
						}
					}
					else{
						return $this->returnResponse(200, false, "Studio not found");
					}
					
					//echo '<pre>'; print_r($time_slot_avaiable);die;
				}
                $data['studios'] = $time_slot_avalible;
			    return $this->returnResponse(200, true, "studio Avaiable List", $data);			
			}
			else{
				return $this->returnResponse(200, false, "Slot time not found");
			}
			
		} catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
	}
	
	public function studio_reports(Request $request){
		// return $this->returnResponse(200, false, "Error", 'Maintenance Due to NPS');
		try{
			$branch_id    = $request->branch_id;
			$assistant_id = $request->assistant_id;
			$studio_id    = $request->studio_id;
			$fdate        = $request->fdate;
			$type        = $request->type;
			
			if(empty($fdate)){
				$fdate = date('Y-m-d');						
			}else{
				$fdate=date('Y-m-d',strtotime($fdate));
			}

			//Cache::forget('studio_reports*');
		    $cacheKey="studio_reports-".$fdate;
		    $studio_reports=Cache::get($cacheKey,'');
		    if(!empty($studio_reports)){
               return $this->returnResponse(200, true, "Reports List ", $studio_reports);	
		    }
			
			$whereCond    = '1=1 ';
			if (!empty($branch_id)){
				if(!empty($branch_id[0])){
					$brnc_id   = implode(",", $branch_id); 
					$whereCond = " id IN ($brnc_id) ";

				}
			}
			
			$get_studios = Branch::with(['studio'=> function ($q) use ($studio_id, $assistant_id,$type)
				{
					if (!empty($studio_id))
					{
						$q->where('id', $studio_id);
					}
					
					if (!empty($assistant_id))
					{
						$q->where('assistant_id', $assistant_id);
					}
					
					if (!empty($type))
					{
						$q->where('type', $type);
					}
					$q->orderBy('order_no', 'asc');
					
				},'studio.assistant', 
				'studio.timetable' => function ($q) use ($fdate)
				{
					if(!empty($fdate)){
						$q->where('cdate', '=', $fdate);
						$q->where('is_deleted', '=', '0');
						$q->where('is_publish', '=', '1');
						$q->orderBy('from_time');
					}
				},'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter','studio.timetable.assistant'])->whereRaw($whereCond);
				
				if (!empty($fdate)){	
					$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
							$q->where('cdate', '=', $fdate);
							// $q->where('cdate', '<=', $tdate);
							$q->where('is_deleted', '=', '0');
							$q->where('is_publish', '=', '1');
					});
				}
				
			$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
				
			$studio_array=[];
			foreach ($get_studios as $branchArray) {
				if(count($branchArray->studio) > 0){ 
				   //echo '<pre>'; print_r($branchArray->studio);
					//Branch Name
					$data_array=array();
					$data_array['branch_id'] 	= $branchArray->id;
					$data_array['branch_name'] 	= $branchArray->name;
					$data_array['nickname'] = $branchArray->nickname;

					foreach ($branchArray->studio as $value) { 
						if(count($value->timetable) > 0){								
							//Hall Details
							$hall_array['studio_id']		= $value->id;
							$hall_array['studio_name']		= $value->name;
							$hall_array['capacity']			= $value->capacity;	
                            $hall_array['hall_timetable']	= array();
							
							foreach($value->timetable as $key => $timetable){							
								//TimeTable
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time        	= new DateTime($timetable->from_time);						
								$to_time         	= new DateTime($timetable->to_time);
								$schedule_interval  = $from_time->diff($to_time);
								$schedule_duration  = $schedule_interval->format('%H : %I Hours');
								
								
								$timetable_array['timetable_id'] 	= $timetable->id;
								$timetable_array['assistant_id'] 	= $timetable->assistant->id;
								$timetable_array['assistant_name'] 	= $timetable->assistant->name;
								$timetable_array['start_time'] 		= date('h:i A', strtotime($timetable->from_time));
								$timetable_array['end_time'] 		= date('h:i A', strtotime($timetable->to_time));
								$timetable_array['date'] 			= date('d-m-Y',strtotime($timetable->cdate));
								$timetable_array['faculty_name']	= $timetable->faculty->name;
								$timetable_array['batch_id']=$timetable->batch->id??'';
								$timetable_array['batch_name']= $timetable->batch->name??'';
								$timetable_array['course_name']= $timetable->course->name??'';
								$timetable_array['subject_name']= $timetable->subject->name??'';
								$timetable_array['chapter_name']= $timetable->chapter->name??'';
								$timetable_array['topic_name']= $timetable->topic->name??''; 
								$timetable_array['type']			= $timetable->online_class_type;
								$timetable_array['schedule_time']	= $schedule_duration;

								/*$tt_topic=DB::table("timetable_topic as tt")
						          ->select("topic.name")
						          ->leftJoin("topic",'topic.id','tt.topic_id')
						          ->where("tt.timetable_id",$timetable->id) 
						          ->where("tt.batch_id",    $timetable->batch_id) 
						          ->where("tt.subject_id",  $timetable->subject_id)->first();
						        if(!empty($tt_topic)){
						        	$timetable_array['topic_name']=$tt_topic->name;
						        }*/

						        $erp_json=$timetable->erp_json??0;
						        $erp_json=json_encode($erp_json);
						        if(is_array($erp_json)){
						        	$timetable_array['erp_course_id']	= $erp_json->erp_course_id??0;
						        	$timetable_array['erp_course_names'] = $erp_json->erp_course_id??0;
						        	$timetable_array['erp_subject_id']	= $erp_json->erp_subject_id??0;
								    $timetable_array['erp_subject_name']= $erp_json->erp_subject_name??0;
								    $timetable_array['erp_topic_id']	= $erp_json->erp_topic_id??0;
								    $timetable_array['erp_topic_name']	= $erp_json->erp_topic_id??0;
						        }


								

								$timetable_array['erp_assistant_id']= 0;
								$timetable_array['erp_faculty_id']  = $timetable->faculty->erp_user_id??0;

								$timetable_array['title']	     = $timetable->topic_name??0;
								$timetable_array['youtube_url']	= '';
								$timetable_array['status']	= '';
								
								//$data_array['timetable'] = $timetable_array;
								$hall_array['hall_timetable'][]	=$timetable_array;
							}
							
							$data_array['hall'][] 	= $hall_array;
						}
						
					}

					$studio_array[] = $data_array;
				}
			}
			
			$data['studioreports'] = $studio_array;
            
			Cache::put($cacheKey, $data, 600);
			return $this->returnResponse(200, true, "Reports List", $data);
		
		} catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
	}

	public function studio_reports_old(Request $request){
		$rules = [
            'fdate' => 'required',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

		
		$branch_id    = $request->branch_id??'';
		$assistant_id = $request->assistant_id??'';
		$studio_id    = $request->studio_id??'';
		$fdate        = $request->fdate??date('Y-m-d');
		$type        = $request->type??'';

		//Cache::forget('studio_reports*');
		$cacheKey="studio_reports-".$fdate;
		$studio_reports=Cache::get('cacheKey','');
		if(!empty($studio_reports)){
		   return $this->returnResponse(200, true, "Reports List", $studio_reports);	
		}
		
		if(empty($fdate)){
			$fdate = date('Y-m-d');						
		}else{
			$fdate=date('Y-m-d',strtotime($fdate));
		}

		$ttCommonWhere=" t.is_deleted='0' AND t.is_publish='1' AND t.is_cancel=0 AND t.cdate='$fdate' ";

		if(!empty($branch_id)){
			if(!empty($branch_id[0])){
				$brnc_id   = implode(",", $branch_id); 
				$ttCommonWhere.=" AND b.id IN ($brnc_id) ";
			}
		}

		if(!empty($studio_id)){
			$ttCommonWhere.=" AND s.id=$studio_id";
		}
		
		if(!empty($assistant_id)){
			$ttCommonWhere.=" AND t.assistant_id=$assistant_id";
		}
		
		if(!empty($type)){
			$ttCommonWhere.=" AND s.type=$type";
		}


		$branches=DB::select("SELECT t.branch_id,b.name,b.nickname
					FROM timetables as t
					Left Join branches as b ON b.id=t.branch_id
					Left Join studios as  s ON s.id=t.studio_id
					Where $ttCommonWhere Group BY t.branch_id
				");
			
		$studio_array=[];
		foreach($branches as $branchArray) {
			$branch_studio=DB::select("SELECT t.studio_id,s.name,s.capacity
					FROM timetables as t
					Left Join branches as b ON b.id=t.branch_id
					Left Join studios as  s ON s.id=t.studio_id
					Where $ttCommonWhere AND t.branch_id=$branchArray->branch_id  Group BY t.studio_id
				");
			if(count($branch_studio) > 0){ 
			    $data_array=array();
				$data_array['branch_id'] 	= $branchArray->branch_id;
				$data_array['branch_name'] 	= $branchArray->name;
				$data_array['nickname'] = $branchArray->nickname;

				

				foreach($branch_studio as $value) { 
					$class=DB::select("SELECT t.*,
						a.name as assistant_name,
						f.name as faculty_name,
						bt.name as batch_name,
						sb.name as subject_name,
						b.name as  branch_name,
						s.name as  studio_name
						FROM timetables as t
						Left Join branches as b ON b.id=t.branch_id
						Left Join studios as  s ON s.id=t.studio_id
						Left Join users as  a ON a.id=t.assistant_id
						Left Join users as  f ON f.id=t.faculty_id
						Left Join batch as  bt ON bt.id=t.batch_id
						Left Join subject as sb ON sb.id=t.subject_id
						Where $ttCommonWhere AND t.studio_id=$value->studio_id
					");

					if(count($class) > 0){
						//Hall Details
						$hall_array['studio_id']		= $value->studio_id;
						$hall_array['studio_name']		= $value->name;
						$hall_array['capacity']			= $value->capacity;	
                        $hall_array['hall_timetable']	= array();
						
						foreach($class as $key => $timetable){							
							//TimeTable
							$schedule_duration  = "00 : 00 Hours"; 	
							$from_time        	= new DateTime($timetable->from_time);						
							$to_time         	= new DateTime($timetable->to_time);
							$schedule_interval  = $from_time->diff($to_time);
							$schedule_duration  = $schedule_interval->format('%H : %I Hours');
							
							
							$timetable_array['timetable_id'] 	= $timetable->id;
							$timetable_array['assistant_id'] 	= $timetable->assistant_id;
							$timetable_array['assistant_name'] 	= $timetable->assistant_name;
							$timetable_array['start_time'] 		= date('h:i A', strtotime($timetable->from_time));
							$timetable_array['end_time'] 		= date('h:i A', strtotime($timetable->to_time));
							$timetable_array['date'] 			= date('d-m-Y',strtotime($timetable->cdate));
							$timetable_array['faculty_name']	= $timetable->faculty_name;
							$timetable_array['batch_id']        =$timetable->batch_id??'';
							$timetable_array['batch_name']      = $timetable->batch_name??'';
							$timetable_array['course_name']     = $timetable->course->name??'';
							$timetable_array['subject_name']    = $timetable->subject_name??'';
							$timetable_array['chapter_name']    = $timetable->chapter->name??'';
							$timetable_array['topic_name']      = $timetable->topic->name??''; 
							$timetable_array['type']			= $timetable->online_class_type;
							$timetable_array['schedule_time']	= $schedule_duration;

							$tt_topic=DB::table("timetable_topic as tt")
					          ->select("topic.name")
					          ->leftJoin("topic",'topic.id','tt.topic_id')
					          ->where("tt.timetable_id",$timetable->id) 
					          ->where("tt.batch_id",    $timetable->batch_id) 
					          ->where("tt.subject_id",  $timetable->subject_id)->first();
					        if(!empty($tt_topic)){
					        	$timetable_array['topic_name']=$tt_topic->name;
					        }

					        $erp_json=$timetable->erp_json??0;
					        $erp_json=json_encode($erp_json);
					        if(is_array($erp_json)){
					        	$timetable_array['erp_course_id']	= $erp_json->erp_course_id??0;
					        	$timetable_array['erp_course_names'] = $erp_json->erp_course_id??0;
					        	$timetable_array['erp_subject_id']	= $erp_json->erp_subject_id??0;
							    $timetable_array['erp_subject_name']= $erp_json->erp_subject_name??0;
							    $timetable_array['erp_topic_id']	= $erp_json->erp_topic_id??0;
							    $timetable_array['erp_topic_name']	= $erp_json->erp_topic_id??0;
					        }

							$timetable_array['erp_assistant_id']= 0;
							$timetable_array['erp_faculty_id']  = $timetable->faculty->erp_user_id??0;

							$timetable_array['title']	     = $timetable->topic_name??0;
							$timetable_array['youtube_url']	= '';
							$timetable_array['status']	= '';
							
							//$data_array['timetable'] = $timetable_array;
							$hall_array['hall_timetable'][]	=$timetable_array;
						}
						
						$data_array['hall'][] 	= $hall_array;
					}
				}

				$studio_array[] = $data_array;
			}
		}
		

		/*$class=DB::select("SELECT t.*,
				a.name as assistant_name,f.name as faculty_name,
				bt.name as batch_name,sb.name as subject_name,
				b.name as  branch_name,s.name as  studio_name
				FROM timetables as t
				Left Join branches as b ON b.id=t.branch_id
				Left Join studios as  s ON s.id=t.studio_id
				Left Join users as  a ON a.id=t.assistant_id
				Left Join users as  f ON f.id=t.faculty_id
				Left Join batch as  bt ON bt.id=t.batch_id
				Left Join subject as sb ON sb.id=t.subject_id
				Where $ttCommonWhere 
			");

		if(count($class) > 0){
			$hall_array['hall_timetable']	= array();
			
			foreach($class as $key => $timetable){							
				//TimeTable
				$schedule_duration  = "00 : 00 Hours"; 	
				$from_time        	= new DateTime($timetable->from_time);						
				$to_time         	= new DateTime($timetable->to_time);
				$schedule_interval  = $from_time->diff($to_time);
				$schedule_duration  = $schedule_interval->format('%H : %I Hours');
				
				
				$timetable_array['timetable_id'] 	= $timetable->id;
				$timetable_array['assistant_id'] 	= $timetable->assistant_id;
				$timetable_array['assistant_name'] 	= $timetable->assistant_name;
				$timetable_array['start_time'] 		= date('h:i A', strtotime($timetable->from_time));
				$timetable_array['end_time'] 		= date('h:i A', strtotime($timetable->to_time));
				$timetable_array['date'] 			= date('d-m-Y',strtotime($timetable->cdate));
				$timetable_array['faculty_name']	= $timetable->faculty_name;
				$timetable_array['batch_id']        =$timetable->batch_id??'';
				$timetable_array['batch_name']      = $timetable->batch_name??'';
				//$timetable_array['course_name']     = $timetable->course->name??'';
				$timetable_array['subject_name']    = $timetable->subject_name??'';
				//$timetable_array['chapter_name']    = $timetable->chapter->name??'';
				$timetable_array['topic_name']      = $timetable->topic->name??''; 
				$timetable_array['type']			= $timetable->online_class_type;
				$timetable_array['schedule_time']	= $schedule_duration;

				$tt_topic=DB::table("timetable_topic as tt")
		          ->select("topic.name")
		          ->leftJoin("topic",'topic.id','tt.topic_id')
		          ->where("tt.timetable_id",$timetable->id) 
		          ->where("tt.batch_id",    $timetable->batch_id) 
		          ->where("tt.subject_id",  $timetable->subject_id)->first();
		        if(!empty($tt_topic)){
		        	$timetable_array['topic_name']=$tt_topic->name;
		        }

		        $erp_json=$timetable->erp_json??0;
		        $erp_json=json_encode($erp_json);
		        if(is_array($erp_json)){
		        	$timetable_array['erp_course_id']	= $erp_json->erp_course_id??0;
		        	$timetable_array['erp_course_names'] = $erp_json->erp_course_id??0;
		        	$timetable_array['erp_subject_id']	= $erp_json->erp_subject_id??0;
				    $timetable_array['erp_subject_name']= $erp_json->erp_subject_name??0;
				    $timetable_array['erp_topic_id']	= $erp_json->erp_topic_id??0;
				    $timetable_array['erp_topic_name']	= $erp_json->erp_topic_id??0;
		        }

				$timetable_array['erp_assistant_id']= 0;
				$timetable_array['erp_faculty_id']  = $timetable->faculty->erp_user_id??0;

				$timetable_array['title']	     = $timetable->topic_name??0;
				$timetable_array['youtube_url']	= '';
				$timetable_array['status']	= '';
				
				//$data_array['timetable'] = $timetable_array;
				$hall_array['hall_timetable'][]	=$timetable_array;
			}
			
			$data_array['hall'][] 	= $hall_array;
			return $this->returnResponse(200, true, "Reports List", $data_array);
		}*/

		$data['studioreports'] = $studio_array;

		Cache::put($cacheKey, $data, 600);
		
		return $this->returnResponse(200, true, "Reports List", $data);
	}
}
