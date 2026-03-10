<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use App\Appointment;
use App\AppointmentStatus;
use Input;
use DB;

class AppointmentController extends Controller
{   
	public function add_appointment(Request $request){
		try { 
			if(!empty($request->user_id) && !empty($request->title) && !empty($request->emp_id) && !empty($request->appointment_date) && !empty($request->start_time) && !empty($request->end_time) && !empty($request->meeting_type)){
				//Username 
				$get_name = User::where('id',$request->user_id)->first();
				
				$emp_id=implode(",",$request->emp_id);
				
				$check_timetable = [];
				if($request->is_forcefully_schedule!=1){
					$check_timetable = DB::table('appointment')
									->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
									->where('appointment_date', $request->appointment_date)
									//->whereIn('appointment_status.emp_id', $request->emp_id)
									->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment.meeting_place_id = '".$request->meeting_place_id."' OR (appointment_status.emp_id IN(".$emp_id."))))")
									->where('appointment.is_deleted','0')
									->where('appointment.meeting_place_id','!=', '0')
									->get();
				}
				
				/*
				else if($request->is_forcefully_schedule!=1){
					$check_timetable = DB::table('appointment')
									->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
									->where('appointment_date', $request->appointment_date)
									->whereRaw("appointment.meeting_place_id = '".$request->meeting_place_id."'")
									->where('appointment.is_deleted','0')
									->where('appointment.meeting_place_id','!=', '0')
									->get();
				}
				*/
								
				
				if(count($check_timetable) > 0){					
					$data = array();
					$checkUser = DB::table('appointment')							
							->select('users.id','users.name')
							->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
							->leftjoin('users', 'users.id', 'appointment_status.emp_id')
							->where('appointment_date', $request->appointment_date)
							->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment_status.emp_id IN (".$emp_id.")))")
							->groupby('users.name')
							->get(); 
							
					if(count($checkUser) > 0){
						$newData	=	'';		
						foreach($checkUser as $cu){
							$newData .=	$cu->name.', ';
						}
						
						$data['meeting_schedule'] = 0;
					}else{
						$newData	=	'Meeting place already booked';
						$data['meeting_schedule'] = 2;
					}
					
					
					return $this->returnResponse(200, false, '('.$newData.') already has a meeting scheduled at this time. Still wish to send an invite?',$data);
				}else{				
					$appointment_input_arr = array(
						'user_id' 			=> $request->user_id,
						'title' 			=> $request->title,
						'description' 		=> $request->description,
						'meeting_place_id' 	=> $request->meeting_place_id,
						'appointment_date' 	=> $request->appointment_date,
						'start_time' 		=> $request->start_time,
						'end_time' 			=> $request->end_time,
						'type' 				=> $request->meeting_type,
						'url' 				=> $request->meeting_url,
						'branch_id' 		=> $request->branch_id,
						'other_city' 		=> $request->other_city,
						'other_place' 		=> $request->other_place,
						'is_group' 			=> $request->is_group,
						'event_id'         =>  empty($request->event_id)?0:$request->event_id,
					);

					$appointment_result = Appointment::insertGetId($appointment_input_arr);
					if($appointment_result){
						//$emp_arr = json_decode($request->emp_id);					
						$emp_arr = $request->emp_id;					
						foreach($emp_arr as $value){
							$appointment_status_input_arr = array('appointment_id' => $appointment_result,'emp_id' => $value);
							AppointmentStatus::insert($appointment_status_input_arr);

						}
						
						
						//Notification Insert
						$employee_id   = $request->emp_id;
						
						$current_date = date('Y-m-d');
						$current_time = date('H:i:s');
		
						$intData	=	array(
							"title"			=>	"Meeting update!!",
							"sender_id"		=>	$request->user_id,
							"date"			=>	$current_date. ' ' .$current_time,
							"description"	=>	"You are a participent in meeting schedued by ".$get_name->name." . Click to view details",
							"receiver_id"	=>	json_encode($employee_id),
							"type"			=>	'Appointment',
							"appointment_id"=>	$appointment_result,
						);
						
						$notification = ApiNotification::create($intData);
						//End
						
						
						//Meeting Add Notification 
						$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $request->emp_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
						$load = array();
						$load['title'] 		 =	"Meeting update!!";
						$load['description'] =	"You are a participent in meeting schedued by ".$get_name->name." . Click to view details"; //$notification->description;
						$load['body'] 		 =	"You are a participent in meeting schedued by ".$get_name->name." . Click to view details"; //$notification->description;
						$load['image'] 		 =	asset('laravel/public/images/test-image.png');
						$load['date'] 		 =	$request->appointment_date;
						$load['status'] 	 =	NULL;
						$load['type'] 		 =	'Appointment';
						
						$appointment_input_arr['appointment_type']='0';
						$appointment_input_arr['appointment_id']=$appointment_result;
						$load['json'] 		 =	json_encode($appointment_input_arr);
				 
						$token_ios= [];
						$token_android= [];
						if(count($user) > 0){
							foreach ($user as $key => $value) {
								if(!empty($value->gsm_token)){
									if($value->device_type=='IOS'){
									  $token_ios[] = $value->gsm_token;
									}else{
									  $token_android[] = $value->gsm_token;
									}
								}
							}
						}

						$this->android_notification($token_android,$load,"Android");
						$this->android_notification($token_ios,$load,"IOS");
						//End
						
						
						return $this->returnResponse(200, true, "Meeting Successfully Added");
					}
					else{
						return $this->returnResponse(200, false, "Something is wrong"); 
					}
				}
			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function appointment_list(Request $request){
		try { 
			if(!empty($request->user_id)){
				$user_id = $request->user_id;
				$fdate 	 = $request->from_date;
				$tdate 	 = $request->to_date;
				$whereCond = " 1=1 AND appointment.is_deleted='0'";
			
				if (!empty($fdate) && !empty($tdate)) {  
					// $whereCond .= ' AND appointment.appointment_date = "'.$request->appointment_date.'"';
					$whereCond .= " AND appointment.appointment_date >= '". date("Y-m-d", strtotime($fdate)) ."' AND appointment.appointment_date <= '". date("Y-m-d", strtotime($tdate))."'";					
				}else{
					$appointment_date=date('Y-m-d');
					$whereCond .= ' AND appointment.appointment_date>= "'.$appointment_date.'"';

				}
				$data = array();
				$appointment_result = Appointment::select('appointment.id','appointment.status as meeting_status','appointment.user_id','u_name.name as user_name','appointment.title','appointment.description','appointment.meeting_place_id','appointment.appointment_date','appointment.start_time','appointment.end_time','meeting_place.name as meeting_place_name','appointment.branch_id','appointment.other_city','appointment.other_place','appointment.url','appointment.is_group','appointment.type','appointment.key_points','appointment.private_key_point','appointment.event_id')
				->leftJoin('meeting_place', 'appointment.meeting_place_id', '=', 'meeting_place.id')
				->leftJoin('users as u_name', 'appointment.user_id', '=', 'u_name.id')
				->whereRaw($whereCond)
				->whereRaw("(appointment.user_id = $user_id OR EXISTS (SELECT * FROM appointment_status WHERE appointment_id = appointment.id AND emp_id = $user_id))")
				->orderby('appointment.appointment_date','ASC')
				->get();
				
				$app_arr = array();
				if(count($appointment_result) > 0 ){
					foreach($appointment_result as $key=>$appointment_result_val){
						
						$app_status_arr = array();
						$appointment_status_result = AppointmentStatus::select('appointment_status.emp_id','users.name','userdetails.degination as designation','appointment_status.status','appointment_status.remark')->leftJoin('users', 'appointment_status.emp_id', '=', 'users.id')->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->where('appointment_id', $appointment_result_val->id)->get();
						if(count($appointment_status_result) > 0){
							foreach($appointment_status_result as $key2=>$appointment_status_result_value){
								$app_status_arr[$key2]['emp_id'] = $appointment_status_result_value->emp_id;
								$app_status_arr[$key2]['emp_name'] = $appointment_status_result_value->name;
								$app_status_arr[$key2]['designation'] = $appointment_status_result_value->designation;
								$app_status_arr[$key2]['status'] = $appointment_status_result_value->status;
								$app_status_arr[$key2]['remark'] = "$appointment_status_result_value->remark";
							}
						}

						
							
						//Task array
						$task_arr = array();
						$task_result = DB::table('task_new')->select('description')->where('appointment_id', $appointment_result_val->id)->get();
						if(count($task_result) > 0){
							foreach($task_result as $key3=>$task_result_value){
								$task_arr[$key3] = $task_result_value->description;
							}
						}
						
						// switch($appointment_result_val->type){
							// case 1 : $typeText = "Physical";	break;
							// case 2 : $typeText = "Virtual";		break;
							// case 3 : $typeText = "Both";		break;
							// default : $typeText	= "";	break;
						// }
						
						
						$app_arr[$key]['id'] = $appointment_result_val->id;
						$app_arr[$key]['meeting_status'] = $appointment_result_val->meeting_status;
						$app_arr[$key]['created_user_id'] = $appointment_result_val->user_id;
						$app_arr[$key]['created_user_name'] = $appointment_result_val->user_name;
						$app_arr[$key]['title'] = $appointment_result_val->title;
						$app_arr[$key]['appointment_date'] =  date("d-m-Y", strtotime($appointment_result_val->appointment_date));
						$app_arr[$key]['start_time'] = $appointment_result_val->start_time;
						$app_arr[$key]['end_time'] = $appointment_result_val->end_time;
						$app_arr[$key]['meeting_place_name'] = $appointment_result_val->meeting_place_name;
						$app_arr[$key]['meeting_type'] = $appointment_result_val->type;
						$app_arr[$key]['meeting_url'] = $appointment_result_val->url;
						$app_arr[$key]['other_city'] = $appointment_result_val->other_city;
						$app_arr[$key]['other_place'] = $appointment_result_val->other_place;
						$app_arr[$key]['task_description'] = $task_arr;
						$app_arr[$key]['key_points'] = $appointment_result_val->key_points;
						$app_arr[$key]['private_key_point'] = $appointment_result_val->private_key_point;
						$app_arr[$key]['event_id'] = $appointment_result_val->event_id;
						
						$app_arr[$key]['description'] = $appointment_result_val->description;
						$app_arr[$key]['meeting_place_id'] = $appointment_result_val->meeting_place_id;
						$app_arr[$key]['branch_id'] = $appointment_result_val->branch_id;
						$app_arr[$key]['is_group'] = $appointment_result_val->is_group;
						
						$app_arr[$key]['employees'] = $app_status_arr;
						
					}
				}
				
				//echo '<pre>'; print_r($app_arr);die;

				if(count($app_arr) > 0){
					$data['appointment_result'] = $app_arr;
					return $this->returnResponse(200, true, "Appointment Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Appointment Not Found"); 
				}

			}
			else{
				return $this->returnResponse(200, false, "User ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function appointment_status(Request $request){
		try { 
			if(!empty($request->appointment_id) && !empty($request->emp_id) && !empty($request->status)){
				$data = array();

				$check_appointment_date =  Appointment::where('id', $request->appointment_id)->first();
				
				if(!empty($check_appointment_date)){
					if($check_appointment_date->appointment_date >= date('Y-m-d')){
						$get_name = User::where('id',$request->emp_id)->first();  //meeting creator name
						
						$check_appointment_status = AppointmentStatus::where('appointment_id', $request->appointment_id)->where('emp_id', $request->emp_id)->first();
						
						if(!empty($check_appointment_status)){
							// $sts = 'Pending';
							// if($check_appointment_status->status == '0'){ $sts = 'Pending'; }
							// if($check_appointment_status->status == '1'){ $sts = 'Accepted'; }
							// if($check_appointment_status->status == '2'){ $sts = 'Reject'; }
							
							if($request->status == '0'){ $sts = 'Pending'; }
							if($request->status == '1'){ $sts = 'Accepted'; }
							if($request->status == '2'){ $sts = 'Reject'; }


							$appointment_res = $check_appointment_status->update(['status' => $request->status, 'remark' => $request->remark]);
							if($appointment_res){
								
								//Notification Insert
								$employee_id   =  $check_appointment_date->user_id;   //Meeting Creator ID
								
								$current_date = date('Y-m-d');
								$current_time = date('H:i:s');
				
								$intData	=	array(
									"title"			=>	"Meeting status update!!",
									"sender_id"		=>	$request->emp_id,
									"date"			=>	$current_date. ' ' .$current_time,
									"description"	=>	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts,
									"receiver_id"	=>	json_encode($employee_id),
									"type"			=>	'Appointment',
									"appointment_id"=>	$request->appointment_id,
								);
								
								 DB::table('api_notifications')->insert($intData);
								
								
								//Push Notification
								$user = DB::table('users')->select('id','gsm_token','device_type')->where('id', $employee_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
								$load = array();
								$load['title'] 		 =	"Meeting status update!!";
								$load['description'] =	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts; //$notification->description;
								$load['body'] 		 =	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts; //$notification->description;
								$load['image'] 		 =	asset('laravel/public/images/test-image.png');
								$load['date'] 		 =	$current_date;
								$load['status'] 	 =	NULL;
								$load['type'] 		 =	'Appointment';
								
								$appointment_input_arr['appointment_type']='1';
								$appointment_input_arr['appointment_id']=$request->appointment_id;
								$load['json'] 		 =	json_encode($appointment_input_arr);
						 
								$token_ios= [];
								$token_android= [];
								if(count($user) > 0){
									foreach ($user as $key => $value) {
										if(!empty($value->gsm_token)){
											if($value->device_type=='IOS'){
											  $token_ios[] = $value->gsm_token;
											}else{
											  $token_android[] = $value->gsm_token;
											}
										}
									}
								}

								$this->android_notification($token_android,$load,"Android");
								$this->android_notification($token_ios,$load,"IOS");
								//End
								
								
								return $this->returnResponse(200, true, "Meeting Status Changed Successfully : ".$sts);
								
							}
							else{
								return $this->returnResponse(200, false, "Meeting Not Found"); 
							}
							
						}
						else{
							return $this->returnResponse(200, false, "Record Not Found"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "Meeting Date Expire"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Meeting ID Not Found"); 
				}

			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function edit_appointment(Request $request){
		try {
            
            // for cancel
            if(!empty($request->is_cancel) && !empty($request->id) && !empty($request->user_id)){
            	$data = array('status'=>2,'cancel_reason'=>$request->cancel_reason);
            	$appointment_result=Appointment::where('id', $request->id)->where('user_id', $request->user_id)->update($data);
            	return $this->returnResponse(200, true, "Meeting Canceled Successfully");
            }

            // for delete
            if(!empty($request->is_delete) && !empty($request->id) && !empty($request->user_id)){
            	$data = array('is_deleted'=>'1','status'=>3);
            	$appointment_result=Appointment::where('id', $request->id)->where('user_id', $request->user_id)->update($data);
            	return $this->returnResponse(200, true, "Meeting Deleted Successfully");
            }

            

			// for edit
			if(!empty($request->id) && !empty($request->user_id) && !empty($request->title) &&  !empty($request->emp_id) && !empty($request->appointment_date) && !empty($request->start_time) && !empty($request->end_time)){
				//Username 
				$get_name = User::where('id',$request->user_id)->first();
				
				$emp_id=implode(",",$request->emp_id);
				
				$check_timetable	=	[];
				if($request->is_forcefully_schedule!=1){
					$check_timetable = DB::table('appointment')
								->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
								->where('appointment_date', $request->appointment_date)
								->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment.meeting_place_id = '".$request->meeting_place_id."'   OR (appointment_status.emp_id IN(".$emp_id."))))")
								->where('appointment.is_deleted','0')
								->where('appointment.id','!=', $request->id)
								->where('appointment.meeting_place_id','!=', '0')
								->get();
				}
				
				/*else{
					$check_timetable = DB::table('appointment')
									->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
									->where('appointment_date', $request->appointment_date)
									->whereRaw("appointment.meeting_place_id = '".$request->meeting_place_id."'")
									->where('appointment.is_deleted','0')
									->where('appointment.meeting_place_id','!=', '0')
									->get();
				}
					*/			
				
				if(count($check_timetable) > 0){
					$data = array();
					$checkUser = DB::table('appointment')							
							->select('users.id','users.name')
							->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
							->leftjoin('users', 'users.id', 'appointment_status.emp_id')
							->where('appointment_date', $request->appointment_date)
							->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment_status.emp_id IN (".$emp_id.")))")
							->groupby('users.name')
							->get(); 
							
					if(count($checkUser) > 0){
						$newData	=	'';		
						foreach($checkUser as $cu){
							$newData .=	$cu->name.', ';
						}
						
						$data['meeting_schedule'] = 0;
					}else{
						$newData	=	'Meeting place already booked';
						
						$data['meeting_schedule'] = 2;
					}
					
					
					return $this->returnResponse(200, false, 'Meeting Already Schedule ('.$newData.')', $data);
				}else{							
					$appointment_input_arr = array(
						'user_id' 			=> $request->user_id,
						'title' 			=> $request->title,
						'description' 		=> $request->description,
						'meeting_place_id'  => $request->meeting_place_id,
						'appointment_date'  => $request->appointment_date,
						'start_time' 		=> $request->start_time,
						'end_time' 			=> $request->end_time,
						'type' 				=> $request->meeting_type,
						'url' 				=> $request->meeting_url,
						'branch_id' 		=> $request->branch_id,
						'other_city' 		=> $request->other_city,
						'other_place' 		=> $request->other_place,
						'is_group' 			=> $request->is_group,
						'status' 			=> 1,						
					);

					$appointment_result = Appointment::where('id', $request->id)->where('user_id', $request->user_id)->update($appointment_input_arr);
					if($appointment_result){
						AppointmentStatus::where('appointment_id', $request->id)->delete();
						//$emp_arr = json_decode($request->emp_id);					
						$emp_arr = $request->emp_id;
						foreach($emp_arr as $value){
							$appointment_status_input_arr = array('appointment_id' => $request->id,'emp_id' => $value);
							AppointmentStatus::insert($appointment_status_input_arr);
						}
						
						
						
						//Notification Insert
						$employee_id   = $request->emp_id;
						
						$current_date = date('Y-m-d');
						$current_time = date('H:i:s');
		
						$intData	=	array(
							"title"			=>	"Meeting update!!",
							"sender_id"		=>	$request->user_id,
							"date"			=>	$current_date. ' ' .$current_time,
							"description"	=>	"You are a participent in meeting schedued by ".$get_name->name." . Click to view details",
							"receiver_id"	=>	json_encode($employee_id),
							"type"			=>	'Appointment',
							"appointment_id"=>	$request->id,
						);
						
						$notification = ApiNotification::create($intData);
						//End
						
						
						//Meeting Add Notification 
						$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $request->emp_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
						$load = array();
						$load['title'] 		 =	"Meeting update!!";
						$load['description'] =	"You are a participent in meeting schedued by ".$get_name->name." . CLick to view details"; //$notification->description;
						$load['body'] 		 =	"You are a participent in meeting schedued by ".$get_name->name." . CLick to view details"; //$notification->description;
						$load['image'] 		 =	asset('laravel/public/images/test-image.png');
						$load['date'] 		 =	$request->appointment_date;
						$load['status'] 	 =	NULL;
						$load['type'] 		 =	'Appointment';
						
						
						$appointment_input_arr['appointment_type']='0';
						$appointment_input_arr['appointment_id']=$request->id;
						$load['json'] 		 =	json_encode($appointment_input_arr);
				 
						$token_ios= [];
						$token_android= [];
						if(count($user) > 0){
							foreach ($user as $key => $value) {
								if(!empty($value->gsm_token)){
									if($value->device_type=='IOS'){
									  $token_ios[] = $value->gsm_token;
									}else{
									  $token_android[] = $value->gsm_token;
									}
								}
							}
						}

						$this->android_notification($token_android,$load,"Android");
						$this->android_notification($token_ios,$load,"IOS");
						//End

						return $this->returnResponse(200, true, "Meeting Successfully Update");
					}
					else{
						return $this->returnResponse(200, false, "Something is wrong"); 
					}
				}
			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
	public function branch_list(Request $request){
		try { 
			$data = array();
			$branches = DB::table('branches')->select('id','branch_location')->where('is_deleted','0')->groupby('branch_location')->orderby('branch_location','ASC')->get();
			
			$app_arr = array();
			if(count($branches) > 0 ){
				foreach($branches as $key=>$b){
					$app_status_arr = array();
					$branch = DB::table('meeting_place')->where('status','Active')->where('is_deleted','0')->where('branch',$b->id)->get();
					
					if(count($branch) > 0){
						foreach($branch as $key2=> $br){
							$app_status_arr[$key2]['id'] 		= $br->id;
							$app_status_arr[$key2]['branch'] 	= $br->name;
						}
					}
					$app_arr[$key]['id'] 			= $b->id;
					$app_arr[$key]['location']  	= ucwords($b->branch_location);
					$app_arr[$key]['branch_data'] 	= $app_status_arr;
				}
			}
			

			if(count($app_arr) > 0){
				$data['data'] = $app_arr;
				return $this->returnResponse(200, true, "Branch Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Branch Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}


	public function get_appointment_group(Request $request){
		try { 
			$user_details = array();
			$branches = DB::table('appointment_status')
						->select('appointment_status.id','appointment_status.emp_id','users.name','users.email','userdetails.degination')
						->leftjoin('users', 'users.id', 'appointment_status.emp_id')
						->leftjoin('userdetails', 'userdetails.user_id', 'users.id')
						->where('appointment_id',$request->appointment_id)
						->get();
			
			$app_arr = array();
			if(count($branches) > 0 ){
				foreach($branches as $key=>$b){					
					$app_arr[$key]['id'] 		= $b->emp_id;
					$app_arr[$key]['emp_id']  	= $b->emp_id;
					$app_arr[$key]['name']  	= $b->name;
					$app_arr[$key]['designation']  	= $b->degination;
					$app_arr[$key]['email']  	= $b->email;
				}
			}
			

			if(count($app_arr) > 0){
				$user_details['user_details'] = $app_arr;
				return $this->returnResponse(200, true, "Branch Details", $user_details);
			}
			else{
				return $this->returnResponse(200, false, "Branch Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
	public function add_key_points(Request $request){
		try { 
			if(!empty($request->appointment_id) && !empty($request->key_points) && !empty($request->user_id) ){
				if($request->notes_type==1){
					$task = array(
						'key_points' 		=> $request->key_points,
						'private_key_point' => $request->private_key_point,
					);
					
					DB::table('appointment')->where('id', $request->appointment_id)->update($task);
					
					$sMessage = "Meeting Key Points Successfully Added";
				}else{				
					$appointment	= 	DB::table('appointment')->where('id', $request->appointment_id)->first();
					$key_points		=	$request->key_points;
					$userID 		=	$request->user_id;
					
					
					$taskCheck   	= 	DB::table('task_new')->where('appointment_id', $request->appointment_id)->where('emp_id', $userID)->where('assign_id', $userID)->first();					
											
					if(empty($taskCheck)){
						$task = array(
							'assign_id'	 	 => $userID,
							'appointment_id' => $request->appointment_id,
							'date'		 	 => date('Y-m-d'),
							'emp_id'	 	 => $userID,
							'title' 	 	 =>	$appointment->title,
							'plan' 	 		 =>	'00',
						);
						
						$insertID = DB::table('task_new')->insertGetId($task);	
					}else{
						$insertID = $taskCheck->id;
					}

					if(!empty($key_points)){
						for($j=0; $j<count($key_points); $j++) {
							$data = array(
								'task_id'	  => $insertID,						
								'description' => $key_points[$j],
							);
												
							DB::table('task_key_points')->insert($data);
						}
					}
					
						
					//Notification Insert
					$employee_id   = $request->user_id;
					
					$current_date = date('Y-m-d');
					$current_time = date('H:i:s');

					$intData	=	array(
						"title"			=>	$appointment->title,
						"sender_id"		=>	$request->user_id,
						"date"			=>	$current_date. ' ' .$current_time,
						"description"	=>	"Your meeting point added in your task",
						"receiver_id"	=>	json_encode($employee_id),
						"type"			=>	'Appointment',
						"appointment_id"=>	$request->appointment_id,
					);
					
					DB::table('api_notifications')->insert($intData);
					
					
					$user = DB::table('users')->select('id','gsm_token','device_type')->where('id', $request->user_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	$appointment->title;
					$load['description'] =	"Your meeting point added in your task"; //$notification->description;
					$load['body'] 		 =	"Your meeting point added in your task"; //$notification->description;
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	$current_date;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'Appointment';
			 
					$token_ios= [];
					$token_android= [];
					if(count($user) > 0){
						foreach ($user as $key => $value) {
							if(!empty($value->gsm_token)){
								if($value->device_type=='IOS'){
								  $token_ios[] = $value->gsm_token;
								}else{
								  $token_android[] = $value->gsm_token;
								}
							}
						}
					}

					$this->android_notification($token_android,$load,"Android");
					$this->android_notification($token_ios,$load,"IOS");
					//End
					
					$sMessage = "Meeting Task Successfully Added";
				}
					
				return $this->returnResponse(200, true, $sMessage);
			
			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
	public function get_attendees(Request $request)
    {
    	try {
			$userID = $request->user_id;
			$user_details = array();
			$app_arr = array();
			$array_key=0;
			
			$appointment_group = DB::table('appointment')
						->select('appointment.id','appointment.title as name','appointment.description as designation')
						->where([['is_group', '=', 1],['is_deleted', '=', '0']])
						->where('user_id', $userID)
						->get();
			
			if(count($appointment_group)>0){
				foreach($appointment_group as $key => $value){
					$app_arr[$key]['id'] 			= $value->id;
					$app_arr[$key]['name']  		= $value->name;
					$app_arr[$key]['designation'] 	= $value->designation;
					$app_arr[$key]['user_type'] 	= 'appointment_group';
					
					$array_key=$key;
					
				}
			}
				
				
			
			$user_result = DB::table('users')
						->select('users.id','users.name','users.email','userdetails.alternate_email as off_email','userdetails.degination as designation')
						->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
						->where([['users.status', '=', '1'],['users.is_deleted', '=', '0']])
						->where('user_id', '!=', $userID)
						->orderby('users.name', 'ASC')
						->get();
			
			if(count($user_result) > 0){
				foreach($user_result as $key => $value){		
					if($value->off_email!=NULL){$abc =  $value->off_email;}else{$abc =  $value->email;}
					$array_key++;		
					$app_arr[$array_key]['id'] 			= $value->id;
					$app_arr[$array_key]['name']  		= $value->name;
					$app_arr[$array_key]['email']       = $abc;
					$app_arr[$array_key]['designation'] = $value->designation;
					$app_arr[$array_key]['user_type'] 	= 'attendees';
				}
			}
						
		   				
			if(count($app_arr)>0){
				$app_arr=array_values($app_arr);
				$user_details['user_details'] = $app_arr;
				return $this->returnResponse(200, true, "Users Details", $user_details);
			}
			else{
				return $this->returnResponse(200, false, "Usres Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
	public function get_appointment_details(Request $request){
		try { 
			if(!empty($request->appointment_id)){
				
				$data = array();
				$appointment_result = Appointment::select('appointment.id','appointment.status as meeting_status','appointment.user_id','u_name.name as user_name','appointment.title','appointment.description','appointment.meeting_place_id','appointment.appointment_date','appointment.start_time','appointment.end_time','meeting_place.name as meeting_place_name','appointment.branch_id','appointment.other_city','appointment.other_place','appointment.url','appointment.is_group','appointment.type','task_new.description as task_description','appointment.key_points','appointment.cancel_reason','appointment.event_id')
				->leftJoin('meeting_place', 'appointment.meeting_place_id', '=', 'meeting_place.id')
				->leftJoin('users as u_name', 'appointment.user_id', '=', 'u_name.id')
				->leftJoin('task_new', 'task_new.appointment_id', '=', 'appointment.id')
				->where('appointment.id',$request->appointment_id)
				->get();
				
				$app_arr = array();
				if(count($appointment_result) > 0 ){
					foreach($appointment_result as $key=>$appointment_result_val){
						$app_status_arr = array();
						$appointment_status_result = AppointmentStatus::select('appointment_status.emp_id','users.name','userdetails.degination as designation','appointment_status.status','appointment_status.remark')->leftJoin('users', 'appointment_status.emp_id', '=', 'users.id')->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->where('appointment_id', $appointment_result_val->id)->get();
						if(count($appointment_status_result) > 0){
							foreach($appointment_status_result as $key2=>$appointment_status_result_value){
								$app_status_arr[$key2]['emp_id'] = $appointment_status_result_value->emp_id;
								$app_status_arr[$key2]['emp_name'] = $appointment_status_result_value->name;
								$app_status_arr[$key2]['designation'] = $appointment_status_result_value->designation;
								$app_status_arr[$key2]['status'] = $appointment_status_result_value->status;
								$app_status_arr[$key2]['remark'] = "$appointment_status_result_value->remark";
							}
						}
						
						
						//Task array
						$task_arr = array();
						$task_result = DB::table('task_new')->select('description')->where('appointment_id', $appointment_result_val->id)->get();
						if(count($task_result) > 0){
							foreach($task_result as $key3=>$task_result_value){
								$task_arr[$key3] = $task_result_value->description;
							}
						}
						
						// switch($appointment_result_val->type){
							// case 1 : $typeText = "Physical";	break;
							// case 2 : $typeText = "Virtual";		break;
							// case 3 : $typeText = "Both";		break;
							// default : $typeText	= "";	break;
						// }
						
						
						$app_arr[$key]['id'] = $appointment_result_val->id;
						$app_arr[$key]['meeting_status'] = $appointment_result_val->meeting_status;
						$app_arr[$key]['created_user_id'] = $appointment_result_val->user_id;
						$app_arr[$key]['created_user_name'] = $appointment_result_val->user_name;
						$app_arr[$key]['title'] = $appointment_result_val->title;
						$app_arr[$key]['description'] = $appointment_result_val->description;
						$app_arr[$key]['meeting_place_id'] = $appointment_result_val->meeting_place_id;
						$app_arr[$key]['appointment_date'] =  date("d-m-Y", strtotime($appointment_result_val->appointment_date));
						$app_arr[$key]['start_time'] = $appointment_result_val->start_time;
						$app_arr[$key]['end_time'] = $appointment_result_val->end_time;
						$app_arr[$key]['meeting_place_name'] = $appointment_result_val->meeting_place_name;
						$app_arr[$key]['meeting_type'] = $appointment_result_val->type;
						$app_arr[$key]['meeting_url'] = $appointment_result_val->url;
						$app_arr[$key]['branch_id'] = $appointment_result_val->branch_id;
						$app_arr[$key]['other_city'] = $appointment_result_val->other_city;
						$app_arr[$key]['other_place'] = $appointment_result_val->other_place;
						$app_arr[$key]['is_group'] = $appointment_result_val->is_group;
						$app_arr[$key]['task_description'] = $task_arr;
						$app_arr[$key]['employees'] = $app_status_arr;
						$app_arr[$key]['key_points'] = $appointment_result_val->key_points;
						$app_arr[$key]['cancel_reason'] = $appointment_result_val->cancel_reason;
						$app_arr[$key]['event_id'] = $appointment_result_val->event_id;
						
					}
				}
				
				//echo '<pre>'; print_r($app_arr);die;

				if(count($app_arr) > 0){
					$data['appointment_result'] = $app_arr;
					return $this->returnResponse(200, true, "Meeting Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Meeting Not Found"); 
				}

			}
			else{
				return $this->returnResponse(200, false, "User ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	
	public function group_destroy(Request $request){
		try { 
			$appointment_id = $request->appointment_id;
			
			if(!empty($appointment_id)){
				DB::table('appointment')->where('id',$appointment_id)->update(['is_group' => 0]);
				
				return $this->returnResponse(200, true, "Meeting Group Deleted");
				
			}else{
				return $this->returnResponse(200, false, "Meeting ID not found"); 
			}
		
		}catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
}
