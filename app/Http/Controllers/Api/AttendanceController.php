<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use App\User;
use App\Userbranches;
use App\Attendance;
use App\AttendanceNew;
use App\ApiNotification;
use DateTime;
use DB;
use App\Branch;
use App\LeaveDetail;


class AttendanceController extends Controller
{
	
	public function biometricttendence(Request $request){
		try {
			$emp_code = $request->emp_code;
			$date     = $request->date;
			$time     = $request->time;
			$location = $request->location;
			$type     = $request->type;

			if($emp_code == ''){
    			return $this->returnResponse(200, false, "Employee Code is required.");
    		}
			if($date == ''){
    			return $this->returnResponse(200, false, "Date is required.");
    		}
			if($time == ''){
    			return $this->returnResponse(200, false, "Time is required.");
    		}
			
			$get_emp_id = DB::table('users')->where('register_id', 'LIKE', '%' . $emp_code)->first();
			$entry_type="";	
			
			if(!empty($get_emp_id->id)){
				$set_attendence  = '';
				$chk_first_entry = DB::table('attendance_new')->where('emp_id', $get_emp_id->id)->where('date', $date)->where('time', $time)->where('type', ucfirst($type))->first();							
				if(empty($chk_first_entry)){
					$set_attendence = DB::table('attendance_new')->insertGetId([
											'emp_id' => $get_emp_id->id,
											'date'   => $date,
											'time'   => $time,
											'type'   => ucfirst($type),
											'location' => $location
									  ]);  
									
				}
				
				/* elseif(!empty($chk_first_entry) && $chk_first_entry->type == 'In'){
					$entry_type="OUT";
					$set_attendence = DB::table('attendance_new')->insertGetId([
											'emp_id' => $get_emp_id->id,
											'date'   => $date,
											'time'   => $time,
											'type'   => 'Out',
											'location' => $location
									  ]);  
				}
				elseif(!empty($chk_first_entry) && $chk_first_entry->type == 'Out'){
					$entry_type="IN";
					$set_attendence = DB::table('attendance_new')->insertGetId([
											'emp_id' => $get_emp_id->id,
											'date'   => $date,
											'time'   => $time,
											'type'   => 'In',
											'location' => $location
									  ]);  
				} */


				
				if($set_attendence){
					$input_arr = ['emp_id' => $get_emp_id->id,'date'   => $date,'time'   => $time,'type'   => ucfirst($type),'location' => $location];
					$this->maintain_history($get_emp_id->id, 'attendance_new', $set_attendence, 'create_attendance', json_encode($input_arr));
					
					// start notification for rfid card
						$load=array();
						$load['sender_id']   = '901';
						$emp_id=$get_emp_id->id;
						$load['receiver_id'] = '["'.$emp_id.'"]';
						if($entry_type=="IN"){
						  $load['title']       = 'Attendance Update! Great, your attendance is successful marked via card as Punch-In.';
					      $load['description'] = 'Great, your attendance is successful marked via card as Punch-In. To stay safe in the fight against COVID-19, wear a mask. Have A Good Day';
					    }else{
					      $load['title']       = 'Attendance Update ! Great, your attendance is successful marked via card as Punch-Out.';
					      $load['description'] = 'Great, your attendance is successful marked via card as Punch-Out. Leave sooner, drive slower, live longer. Good Bye, see you soon'; 	
					    }
					
						$load['type']        = 'General';
						$load['date']        = date('Y-m-d H:i:s');
						/* ApiNotification::create($load);
						//$load['image']       =asset('laravel/public/images/test-image.png');
						if(!empty($get_emp_id->gsm_token)){
							$token[]=$get_emp_id->gsm_token;
							unset($load['sender_id']);unset($load['receiver_id']);
			                $this->android_notification($token, $load);
						} */
						// end notification for rfid card
					return $this->returnResponse(200, true, "Attendence Add Successfully");
				}
				else{
					return $this->returnResponse(200, false, "Something is wrong");
				}	
			}
			else{
				return $this->returnResponse(200, false, "Employee Not Found");
			}
			
		} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
    public function add_attendance(Request $request)
    {   
		
		$userArr = array(8500,8501,8502,8503,8504,8505,8506,8507,8618,8803,8804); 
		if(!in_array($request->emp_id, $userArr)){
			if(date('Y-m-d') >= '2024-05-25'){
				return $this->returnResponse(200, false, "Please mark attendance and apply for leaves using the DawinBox app only.");
			}
		}
			
    	try {
    		$emp_id = $request->emp_id;
    		$type = $request->type;
			$lat = $request->latitude;
    		$long = $request->longitude;
    		
    		if($emp_id == ''){
    			return $this->returnResponse(200, false, "Employee Id is required.");
    		}
    		if($type == ''){
    			return $this->returnResponse(200, false, "Type is required.");
    		}

			if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$inputs = $request->only('emp_id','type','image','latitude','longitude');  
						$inputs['image'] = '-';
						if(Input::hasfile('image')){
							$inputs['image'] = NULL;
							$valid_extensions = array('jpeg', 'jpg', 'png');
							$images = $_FILES['image'];
							if($images['name'] != ''){
								$img = $images['name'];
								$tmp = $images['tmp_name'];
								$extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));
								$drive = public_path(DIRECTORY_SEPARATOR . 'attendance' . DIRECTORY_SEPARATOR);
								$imagename = uniqid().'-'. time() .'-'. $emp_id . '.' . $extension;    
								$newImage = $drive . $imagename;
								if(move_uploaded_file($tmp,$newImage)){
									$inputs['image'] = 'attendance/'.$imagename;					 
								}
							}
						}
						
						$rangeCheck ="SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$lat." - `latitude`) * pi()/180 / 2), 2) +COS( ".$lat." * pi()/180) * COS(`latitude` * pi()/180) * POWER(SIN(( ".$long." - `longitude`) * pi()/180 / 2), 2) ))) as distance from branches having distance<=0.4 ORDER BY distance";
						$rangeCheck = DB::select($rangeCheck);
						$location 	= "";
						if(!empty($rangeCheck)){
							$location =	$rangeCheck[0]->name;
						}else{
							//$location=$request->address;
							// return $this->returnResponse(200, false, "Invalid Location.");
							
							
							$url="https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&sensor=true_or_false&key=AIzaSyBJLkCBb_g5hq3P42qdPezt9MJ4-elI1u0";
							$curl = curl_init($url);
							curl_setopt($curl, CURLOPT_URL, $url);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							//for debug only!
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
							$resp = curl_exec($curl);
							curl_close($curl);
							//$location = "";
							if(!empty($resp)){
								$result=json_decode($resp,true);
								if(!empty($result['results']['0']['formatted_address'])){
									$location = $result['results']['0']['formatted_address'];
								}
							}
						}
						
						$inputs['emp_id'] = $emp_id;
						$inputs['date'] = date('Y-m-d');
						$inputs['time'] = date('H:i');
						$inputs['location'] = $location;

						$attendance = Attendance::create($inputs);
						
						/*$check_leave = LeaveDetail::where('emp_id', $request->emp_id)->where('date', date('Y-m-d'))->get();
						if(count($check_leave) > 0 ){
							foreach($check_leave as $check_leave_value){
								if($check_leave_value->type == 'Full Day'){
									// LeaveDetail::where('id', $check_leave_value->id)->update([ 'status' => 'Rejected' ]);
								}
								if($check_leave_value->type == '1st Half'){ 
									if(strtotime($inputs['time']) <= strtotime('11:00')){
										// LeaveDetail::where('id', $check_leave_value->id)->update([ 'status' => 'Rejected' ]);
									}
								}
								if($check_leave_value->type == '2nd Half'){  
									if(strtotime($inputs['time']) >= strtotime('11:00')){ 
										// LeaveDetail::where('id', $check_leave_value->id)->update([ 'status' => 'Rejected' ]);
									}
								}
							}
							
						}*/

                        // start notification for rfid card
						/*$user_branche = Userbranches::where('user_id',$emp_id)->latest('created_at')->first();
						$norifd_branches=array(43,44,46);
						$load=array();
						if(!in_array($user_branche->branch_id,$norifd_branches)){
							$load['title']       = 'Reminder ! Attendance will be only accepted via RFID card';
						    $load['description'] = 'Reminder ! Attendance will be only accepted via RFID card. RFID कार्ड से लगाई  गई उपस्थिति ही मान्य होगी। यदि आपने RFID कार्ड से उपस्थिति नहीं लगाई है तो तुरंत लगा देवें - आज्ञा उत्कर्ष मैनेजमेंट टीम ';
							
						}else{
                          if($type=="In"){
							  $load['title']       = 'Attendance Update! Great, your attendance is successful marked via card as Punch-In.';
					          $load['description'] = 'Great, your attendance is successful marked via card as Punch-In. To stay safe in the fight against COVID-19, wear a mask. Have A Good Day';
						    }else{
						      $load['title']       = 'Attendance Update ! Great, your attendance is successful marked via card as Punch-Out.';
					          $load['description'] = 'Great, your attendance is successful marked via card as Punch-Out. Leave sooner, drive slower, live longer. Good Bye, see you soon'; 	
						    }
						}
						
						$load['sender_id']   = '901';
						$load['receiver_id'] = '["'.$emp_id.'"]';
						//$load['image']       =asset('laravel/public/images/test-image.png');
						$load['type']        = 'General';
						$load['date']        = date('Y-m-d H:i:s');
						ApiNotification::create($load);
						$emp_detail = DB::table('users')->where('id',$emp_id)->first();
						if(!empty($emp_detail->gsm_token)){
							$token[]=$emp_detail->gsm_token;
							unset($load['sender_id']);unset($load['receiver_id']);
			                $this->android_notification($token, $load);
						} */
						// end notification for rfid card

						if($attendance->save()){
							$this->maintain_history($request->emp_id, 'attendance', $attendance->id, 'create_attendance', json_encode($inputs));
							return $this->returnResponse(200, true, "Attendance save successfully");
						}else{
							return $this->returnResponse(200, false, "Something went wrong.");
						}
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Employee Id Not Found");
			}

    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }
	
	public function get_attendance(Request $request)
    {
    	try {
    		$input = $request->only(['emp_id']);
    		$role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;

    		if(isset($input['emp_id']) && !empty($input['emp_id'])){

    			$emp_id = $input['emp_id'];
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$responseArray = array();
						$employees_array = array();
						if(!empty($request->name)){
							$name = $request->name;
							$user_check->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name)
								->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
						}
						$user_data =  $user_check->first();
						if(!empty($user_data)){
							$employees_array[] = array('emp_id'=>$emp_id,'name'=>$user_data->name); //Self	
						}
						
						$user_type = "";
						//$team_users = User::where('status', 1)->where('register_id', "!=", null);

						$team_users = User::with(['user_details','user_branches']);

						if(!empty($role_id)){
			                $team_users = $team_users->where('role_id', '=', $role_id);
		                }

		                if(!empty($department_head_id)){
		                    $team_users = $team_users->where('id', $department_head_id);
		                }

		                if(!empty($designation)){
		                    $team_users->WhereHas('user_details', function ($q) use ($designation) {
		                        $q->where('degination', $designation);
		                    });
		                }

		                if(!empty($branch_id)){
		                    $team_users->WhereHas('user_branches', function ($q) use ($branch_id) {
		                            $q->where('branch_id', $branch_id);
		                    });
		                }

						if($user->role_id==24 || $user->role_id==29){ 
						   // 24 = HR, 29 = SuperAdmin
							$user_type = "superadmin";

							// for Nirmal sir add on urgent
							$team_users->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ');
							$check_supervisor = $team_users->get();
							if(count($check_supervisor) > 0){
								$user_type = "supervisor";
							}
						}
						else{
							//Supervisor
							// $team_users->where('supervisor_id', $emp_id);
							$team_users->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ');
							$check_supervisor = $team_users->get();
							if(count($check_supervisor) > 0){
								$user_type = "supervisor";
							}
						}
						
						if(!empty($request->name)){
							$name = $request->name;
							$team_users->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name)
								->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
						}
						
						//$team_users = $team_users->get();
						$team_users = $team_users->where('status', 1)->where('register_id', "!=", null)->get();

						if(count($team_users) > 0){
							$employees = $team_users; // Employees List
							foreach($employees as $key=>$value){
								if(!empty($value)){
									$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
								}
							}
						}
						
						$date_wise_attendance = Attendance::groupBy('date')->orderBy('date', 'desc');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_attendance->where('date', '>=', $request->date_from);
							$date_wise_attendance->where('date', '<=', $request->date_to);
						}
						else{
							$data_show_days = date('Y-m-d', strtotime('-7 days'));
							if($user_type=="superadmin"){ // or HR
								$data_show_days = date('Y-m-d');
							}
							else if($user_type=="supervisor"){
								$data_show_days = date('Y-m-d');
							}
							
							$date_wise_attendance->where('date', '>=', $data_show_days);
							$date_wise_attendance->where('date', '<=', date('Y-m-d'));
						}
						$date_wise_attendance = $date_wise_attendance->get();
						
						if(count($date_wise_attendance) > 0){
							foreach($date_wise_attendance as $key=>$valAtt){
								$responseArray[$key]['date'] = date("d-m-Y", strtotime($valAtt->date));
								$i = 0;
								$employee_attendance = array();
								foreach($employees_array as $key2=>$employeeId){
									$get_attendance = Attendance::where('emp_id', $employeeId['emp_id'])->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
									$time_array = array();
									$time_array1 = array();
									$ii=0;
									$total_minute = 0;
									$less_9_hours = true;
									if(count($get_attendance) > 0){
										foreach($get_attendance as $key1 => $AttendanceDetail){
											if($less_9_hours){
												$in_time = "";
												$out_time = "";
												if(empty($time_array[$ii]['in_time'])){
													$time_array[$ii]['in_time'] = "";
													$time_array1[$ii]['in_time1'] = 0;
												}
												if(empty($time_array[$ii]['out_time'])){
													$time_array[$ii]['out_time'] = "";
													$time_array1[$ii]['out_time1'] =0;
												}
												
												if($AttendanceDetail->type=="In"){
													$in_time = date("h:i A", strtotime($AttendanceDetail->time));
													$in_time1 = $AttendanceDetail->time;
													if(empty($time_array[$ii]['in_time'])){
														$time_array[$ii]['in_time'] = $in_time;
														$time_array1[$ii]['in_time1'] = $in_time1;
													}
													else{
														$ii++;
														$time_array[$ii]['in_time'] = $in_time;
														$time_array1[$ii]['in_time1'] = $in_time1;
														$time_array[$ii]['out_time'] = "";
														$time_array1[$ii]['out_time1'] = 0;
													}
												}
												else if($AttendanceDetail->type=="Out"){
													$out_time = date("h:i A", strtotime($AttendanceDetail->time));
													$out_time1 = $AttendanceDetail->time;
													if(empty($time_array[$ii]['out_time'])){
														$time_array[$ii]['out_time'] = $out_time;
														$time_array1[$ii]['out_time1'] = $out_time1;
														if(!empty($time_array1[$ii]['in_time1']) && $time_array1[$ii]['out_time1']){
															$intime = new DateTime($time_array1[$ii]['in_time1']);
															$outtime = new DateTime($time_array1[$ii]['out_time1']);
															$interval = $intime->diff($outtime);
															$hours = $interval->format('%H');
															$minute = $interval->format('%I');
															$total_minute += ($hours*60)+$minute;
															// New Code DK for max 9 hours
															if($total_minute >= 540){
																$less_9_hours = false;
																$format = '%02d:%02d';
																$totalhours = floor($total_minute / 60);
																$totalminutes = ($total_minute % 60);
																$total_hours = sprintf($format, $totalhours, $totalminutes);
																$explode_hour = explode(":",$total_hours);
																$minus_hours = 0;
																if($explode_hour[0] > 9){
																	$minus_hours = $explode_hour[0] - 9;
																}
																
																
																$out_time = date("h:i A", strtotime("-$minus_hours hours", strtotime($AttendanceDetail->time)));
																$time_array[$ii]['out_time'] = $out_time;
															}
														}
														
														$ii++;
													}
												}
											}
										}
										// print_r($time_array); die;
										$i++;
									}
									$employee_attendance[$key2]['emp_id'] = $employeeId['emp_id'];
									$employee_attendance[$key2]['name'] = $employeeId['name'];
									$total_hours = "00:00";
									if($total_minute > 0){
										$format = '%02d:%02d';
										$totalhours = floor($total_minute / 60);
										$totalminutes = ($total_minute % 60);
										$total_hours = sprintf($format, $totalhours, $totalminutes);
										$explode_hour = explode(":",$total_hours);
										if($explode_hour[0] > 9){
											$total_hours = "09:".$explode_hour[1];
										}
									}
									$employee_attendance[$key2]['total_hours'] = $total_hours;
									$employee_attendance[$key2]['time'] = $time_array;
								}
								
								$responseArray[$key]['employees'] = $employee_attendance;
							}
								

							$data['dates'] = $responseArray;

							return $this->returnResponse(200, true, "Attendance", $data);
						}else{
							return $this->returnResponse(200, false, "Attendance Not Found");
						}
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}

    		}else{
    			return $this->returnResponse(200, false, "Employee Id Not Found");
    		}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }
	
	public function uploadImage($image,$emp_id){
        $drive = public_path(DIRECTORY_SEPARATOR . 'attendance' . DIRECTORY_SEPARATOR);
        $extension = $image->getClientOriginalExtension();
        $imagename = uniqid().'-'. time() .'-'. $emp_id . '.' . $extension;    
        $newImage = $drive . $imagename;
        $imgResource = $image->move($drive, $imagename);
        return 'attendance/'.$imagename;
    }
	
	public function get_attendance_rfid(Request $request){
		try {
    		$input = $request->only(['emp_id']);
    		$role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;

    		if(isset($input['emp_id']) && !empty($input['emp_id'])){

    			$emp_id = $input['emp_id'];
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$responseArray = array();
						$employees_array = array();
						if(!empty($request->name)){
							$name = $request->name;
							$user_check->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name)
								->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
						}
						$user_data =  $user_check->first();
						if(!empty($user_data)){
							$employees_array[] = array('emp_id'=>$emp_id,'name'=>$user_data->name); //Self	
						}
						
						$user_type = "";
						//$team_users = User::where('status', 1)->where('register_id', "!=", null);

						$team_users = User::with(['user_details','user_branches']);

						if(!empty($role_id)){
			                $team_users = $team_users->where('role_id', '=', $role_id);
		                }

		                if(!empty($department_head_id)){
		                    $team_users = $team_users->where('id', $department_head_id);
		                }

		                if(!empty($designation)){
		                    $team_users->WhereHas('user_details', function ($q) use ($designation) {
		                        $q->where('degination', $designation);
		                    });
		                }

		                if(!empty($branch_id)){
		                    $team_users->WhereHas('user_branches', function ($q) use ($branch_id) {
		                            $q->where('branch_id', $branch_id);
		                    });
		                }

						if($user->role_id==24 || $user->role_id==29){ // 24 = HR, 29 = SuperAdmin
							$user_type = "superadmin";
						}
						else{
							//Supervisor
							// $team_users->where('supervisor_id', $emp_id);
							$team_users->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ');
							$check_supervisor = $team_users->get();
							if(count($check_supervisor) > 0){
								$user_type = "supervisor";
							}
						}
						
						if(!empty($request->name)){
							$name = $request->name;
							$team_users->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name)
								->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
						}
						
						//$team_users = $team_users->get();
						$team_users = $team_users->where('status', 1)->where('register_id', "!=", null)->get();

						if(count($team_users) > 0){
							$employees = $team_users; // Employees List
							foreach($employees as $key=>$value){
								if(!empty($value)){
									$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
								}
							}
						}
						
						$date_wise_attendance = AttendanceNew::groupBy('date')->orderBy('date', 'desc');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_attendance->where('date', '>=', $request->date_from);
							$date_wise_attendance->where('date', '<=', $request->date_to);
						}
						else{
							$data_show_days = date('Y-m-d', strtotime('-7 days'));
							if($user_type=="superadmin"){ // or HR
								$data_show_days = date('Y-m-d');
							}
							else if($user_type=="supervisor"){
								$data_show_days = date('Y-m-d');
							}
							
							$date_wise_attendance->where('date', '>=', $data_show_days);
							$date_wise_attendance->where('date', '<=', date('Y-m-d'));
						}
						$date_wise_attendance = $date_wise_attendance->get();
						
						if(count($date_wise_attendance) > 0){
							foreach($date_wise_attendance as $key=>$valAtt){
								$responseArray[$key]['date'] = date("d-m-Y", strtotime($valAtt->date));
								$i = 0;
								$employee_attendance = array();
								foreach($employees_array as $key2=>$employeeId){
									$get_attendance = AttendanceNew::where('emp_id', $employeeId['emp_id'])->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
									$time_array = array();
									$time_array1 = array();
									$ii=0;
									$total_minute = 0;
									$less_9_hours = true;
									if(count($get_attendance) > 0){
										foreach($get_attendance as $key1 => $AttendanceDetail){
											if($less_9_hours){
												$in_time = "";
												$out_time = "";
												if(empty($time_array[$ii]['in_time'])){
													$time_array[$ii]['in_time'] = "";
													$time_array1[$ii]['in_time1'] = 0;
												}
												if(empty($time_array[$ii]['out_time'])){
													$time_array[$ii]['out_time'] = "";
													$time_array1[$ii]['out_time1'] =0;
												}
												
												if($AttendanceDetail->type=="In"){
													$in_time = date("h:i A", strtotime($AttendanceDetail->time));
													$in_time1 = $AttendanceDetail->time;
													if(empty($time_array[$ii]['in_time'])){
														$time_array[$ii]['in_time'] = $in_time;
														$time_array1[$ii]['in_time1'] = $in_time1;
													}
													else{
														$ii++;
														$time_array[$ii]['in_time'] = $in_time;
														$time_array1[$ii]['in_time1'] = $in_time1;
														$time_array[$ii]['out_time'] = "";
														$time_array1[$ii]['out_time1'] = 0;
													}
												}
												else if($AttendanceDetail->type=="Out"){
													$out_time = date("h:i A", strtotime($AttendanceDetail->time));
													$out_time1 = $AttendanceDetail->time;
													if(empty($time_array[$ii]['out_time'])){
														$time_array[$ii]['out_time'] = $out_time;
														$time_array1[$ii]['out_time1'] = $out_time1;
														if(!empty($time_array1[$ii]['in_time1']) && $time_array1[$ii]['out_time1']){
															$intime = new DateTime($time_array1[$ii]['in_time1']);
															$outtime = new DateTime($time_array1[$ii]['out_time1']);
															$interval = $intime->diff($outtime);
															$hours = $interval->format('%H');
															$minute = $interval->format('%I');
															$total_minute += ($hours*60)+$minute;
															// New Code DK for max 9 hours
															if($total_minute >= 540){
																$less_9_hours = false;
																$format = '%02d:%02d';
																$totalhours = floor($total_minute / 60);
																$totalminutes = ($total_minute % 60);
																$total_hours = sprintf($format, $totalhours, $totalminutes);
																$explode_hour = explode(":",$total_hours);
																$minus_hours = 0;
																if($explode_hour[0] > 9){
																	$minus_hours = $explode_hour[0] - 9;
																}
																
																
																$out_time = date("h:i A", strtotime("-$minus_hours hours", strtotime($AttendanceDetail->time)));
																$time_array[$ii]['out_time'] = $out_time;
															}
														}
														
														$ii++;
													}
												}
											}
											
										}
										// print_r($time_array); die;
										$i++;
									}
									$employee_attendance[$key2]['emp_id'] = $employeeId['emp_id'];
									$employee_attendance[$key2]['name'] = $employeeId['name'];
									$total_hours = "00:00";
									if($total_minute > 0){
										$format = '%02d:%02d';
										$totalhours = floor($total_minute / 60);
										$totalminutes = ($total_minute % 60);
										$total_hours = sprintf($format, $totalhours, $totalminutes);
										$explode_hour = explode(":",$total_hours);
										if($explode_hour[0] > 9){
											$total_hours = "09:".$explode_hour[1];
										}
									}
									$employee_attendance[$key2]['total_hours'] = $total_hours;
									$employee_attendance[$key2]['time'] = $time_array;
								}
								
								$responseArray[$key]['employees'] = $employee_attendance;
							}
								

							$data['dates'] = $responseArray;

							return $this->returnResponse(200, true, "Attendance", $data);
						}else{
							return $this->returnResponse(200, false, "Attendance Not Found");
						}
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}

    		}else{
    			return $this->returnResponse(200, false, "Employee Id Not Found");
    		}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function manual_biometricttendence(Request $request){
			
			if(!empty($request->array1)){
				 // echo "<pre>";  print_r($request->array1); die('d');
				foreach($request->array1 as $vvvv){
					$attendance_id = isset($vvvv['id'])?$vvvv['id'] :'';
					$type = isset($vvvv['type'])?$vvvv['type'] :'';
					$emp_code = isset($vvvv['emp_code'])?$vvvv['emp_code'] :'';
					$date     = isset($vvvv['date'])?$vvvv['date'] :'';
					$time     = isset($vvvv['time'])?$vvvv['time'] :'';
					$location  = isset($vvvv['location'])?$vvvv['location'] :'';
					
					if($type == ''){
						// return $this->returnResponse(200, false, "Type is required.");
					}
					if($emp_code == ''){
						// return $this->returnResponse(200, false, "Employee Code is required.");
					}
					if($date == ''){
						// return $this->returnResponse(200, false, "Date is required.");
					}
					if($time == ''){
						// return $this->returnResponse(200, false, "Time is required.");
					}
					if($type != '' && $emp_code != '' && $date != '' && $time != ''){
						$get_emp_id = DB::table('users')->where('register_id', 'LIKE', '%' . $emp_code)->first();
						
						if(!empty($get_emp_id) && !empty($get_emp_id->id)){
							$set_attendence  = '';
							if($type=="in"){
								$type = "In";
							}
							else if($type=='out'){
								$type = "Out";
							}
							else{
								// return $this->returnResponse(200, false, "Error12");
							}
							if($type !=''){
								$chk_first_entry = DB::table('attendance_new')->where('emp_id', $get_emp_id->id)->where('date', $date)->where('type', $type)->first();
											
								if(empty($chk_first_entry)){
									$set_attendence = DB::table('attendance_new')->insertGetId([
															'emp_id' => $get_emp_id->id,
															'date'   => $date,
															'time'   => $time,
															'type'   => $type,
															'location' => $location,
															'is_cron' => '1',
													  ]); 
									if($set_attendence){
										$input_arr = ['emp_id' => $get_emp_id->id,'date'   => $date,'time'   => $time,'type'   => $type,'location' => $location,'is_cron' => '1'];
										$this->maintain_history($get_emp_id->id, 'attendance_new', $set_attendence, 'create_attendance', json_encode($input_arr));
										
										// return $this->returnResponse(200, true, "Attendence Add Successfully");
									}
									else{
										// return $this->returnResponse(200, false, "Something is wrong");
									}
													
								}
								else{
									// return $this->returnResponse(200, false, "Already");
								}
							}
								
						}
						else{
							// return $this->returnResponse(200, false, "Employee Not Found");
						}
					}
				}
			}
			return $this->returnResponse(200, true, "Empty");
			
		 
	}
	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('all_history')->insert($history_data);
	}
	
}
