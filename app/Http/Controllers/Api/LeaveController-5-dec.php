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
use App\Leave;
use App\LeaveDetail;
use DB;
use DateTime;
use App\Attendance;
use App\AttendanceNew;
use App\Holiday;
use App\Userdetails;

class LeaveController extends Controller
{
	
	public function add_leave(Request $request)
    {
        try{
            $user_id = $request->emp_id;
            // $assigned_userid = $request->assigned_userid;

            if(isset($user_id) && !empty($user_id)){
				$user = User::where('id', $user_id)->first();
				 
				if(!empty($user)){
					if($user->role_id == 2){
						return $this->returnResponse(200, false, "You can not apply for leave");
					}else{
						if($user->status == '1'){
							$usData 		= 	Userdetails::where('user_id',$user_id)->first();
							$probation_from	=	$usData['probation_from'];
							if($probation_from==""){
								$joining_date	=	$usData['joining_date'];							
								$join_date  	= 	date ("Y-m-d", strtotime ($joining_date ."+90 days")); 
							}else{
								$join_date		=	$probation_from;
							}
							$mindate = date('Y-m-d');
							$categoryArray = array();
							
							$emp_name = $user->name;
							$user_department = $user->department_type;
							$all_department = DB::table('users')->whereRaw("department_type = '$user_department' AND role_id = 21 AND status = 1 ")->get();
							$token = [];
							$dp_head = [];
							if(!empty($all_department)){
								foreach($all_department as $dp_val){
									if(!empty($dp_val->gsm_token)){
										$token[] = $dp_val->gsm_token;
										$dp_head[] = "$dp_val->id";
									}
								}
							}
							$dp_heads = json_encode($dp_head);
							
							if(date('d') > 10){
								$todayDate = date('Y-m-01');
							}
							else{
								$todayDate = date('Y-m-d',strtotime("first day of last month"));
							}
							if (is_array($request->leave_array) && !empty($request->leave_array)) {
								$checkLeave = true;
								foreach($request->leave_array as $leaveRow){
									$categoryArray[] = $leaveRow['category'];
									// $before_30_days = strtotime('-30 days',strtotime(date('Y-m-d')));
									$before_30_days = strtotime($todayDate);
									if(strtotime($leaveRow['date']) < $before_30_days){
										$checkLeave = false;
									}
								}
								
								$a1=array("CL","SL","PL","Comp Off","LWP");
								$a2=$categoryArray;
								$result=array_diff($a2,$a1);
								if(!empty($result)){
									return $this->returnResponse(200, false, "Something Went Wrong. Category Error.");
									exit;
								}
								
								if($mindate < $join_date){
									$a1 = array(0=>'Comp Off',1=>'LWP'); // Comp Off alava type aata h to false 
									if(count(array_diff($categoryArray,$a1)) > 0){
										return $this->returnResponse(200, false, "You are ineligible for any paid leaves as your probation period is still ongoing...");
										exit;
									}
								}
								
								if($checkLeave){
									$save_array = array();
									$leave_array = $request->leave_array;
									$from_date = $leave_array[0]['date'];
									$to_date = $leave_array[count($leave_array)-1]['date'];
									// echo "<pre>"; print_r($leave_array); die;
									foreach ($leave_array as $key => $value) {
										if(!empty($value)){
											$date = $value['date'];
											$type = $value['type'];
											$category = $value['category'];
											if(!empty($date) && !empty($type) && !empty($category)){
												if($date >= '2023-01-01'){
													if($category == 'CL' || $category=='SL'){
														return $this->returnResponse(200, false, "You can't apply CL or SL in 2023.");exit;
													}
												}
											}
											else{
												return $this->returnResponse(200, false, 'All fields are required.');exit;
											}
										}
										else{
											return $this->returnResponse(200, false, 'Something went wrong 1.');exit;
										}
									}
									
									$return_res = $this->save_leave_conditions($user_id,$leave_array);
									if($return_res['status']==false){
										return $this->returnResponse(200, false, $return_res['message']);exit;
									}
									
									$leave_id = DB::table('leave')->insertGetId([ 'emp_id' => $user_id, 'reason' => $request->reason ]);
									
									$input_arr = [ 'emp_id' => $user_id, 'reason' => $request->reason ];
									$this->maintain_history($user_id, 'leave', $leave_id, 'insert_leave', json_encode($input_arr,JSON_UNESCAPED_UNICODE));
									
									foreach ($leave_array as $key => $value) {
										$date = $value['date'];
										$type = $value['type'];
										$category = $value['category'];
										
										$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND DATE(date) = '$date' ")->get();
										
										if(count($already_leaves)==0){
											$data = array();
											$data['emp_id'] = $user_id;
											$data['date'] = $date;
											$data['type'] = $type;
											$data['category'] = $category;
											$data['leave_id'] = $leave_id;
											
											$save_array[] = $data;
										}
										else{
											$allTypes = array();
											foreach($already_leaves as $leaveVal){
												$allTypes[] = $leaveVal->type;
											}
											// print_r($allTypes); die;
											
											if(!in_array('Full Day',$allTypes)){
												if(in_array('1st Half',$allTypes)){
													if($type == "Full Day"){
														return $this->returnResponse(200, false, "$date 1st Half already leave request send."); exit;
													}
												}
												else if(in_array('2nd Half',$allTypes)){
													if($type == "Full Day"){
														return $this->returnResponse(200, false, "$date 2nd Half already leave request send."); exit;
													}
												}
												
												$leave_checked = DB::table('leave_details')->whereRaw("emp_id = $user_id AND DATE(date) = '$date' and type ='$type' ")->get();
																						
												if(count($leave_checked)==0){
													$data = array();
													$data['emp_id'] = $user_id;
													$data['date'] = $date;
													$data['type'] = $type;
													$data['category'] = $category;
													$data['leave_id'] = $leave_id;
													$save_array[] = $data;
												}
												else{
													return $this->returnResponse(200, false, "$date $type already leave request send."); exit;
												}
											}
											else{
												return $this->returnResponse(200, false, "$date Full Day already leave request send."); exit;
											}
										}
									}
									if(count($save_array) > 0){
										
										foreach($save_array as $vals){
											$leave_detail_id =  DB::table('leave_details')->insertGetId([ 
																	'emp_id' => $vals['emp_id'],
																	'leave_id' => $vals['leave_id'],
																	'date' => $vals['date'],
																	'category' => $vals['category'],
																	'type' => $vals['type'],
																]);
											
											$input_arr = ['emp_id' => $vals['emp_id'],'leave_id' => $vals['leave_id'],'date' => $vals['date'],'category' => $vals['category'],'type' => $vals['type']];						
											$this->maintain_history($vals['emp_id'], 'leave_details', $leave_detail_id, 'insert_leave_details', json_encode($input_arr));
											
										}
										
										$load=array();
										$load['sender_id']   = '901';
										$load['receiver_id'] = $dp_heads;
										$load['title']       = 'Employee Leave Request';
										$load['description'] = "$emp_name want leave From $from_date To $to_date";
										$load['type']        = 'General';
										$load['date']        = date('Y-m-d H:i:s');
										ApiNotification::create($load);
										
										unset($load['sender_id']);unset($load['receiver_id']);
										$this->android_notification($token, $load);
										
										return $this->returnResponse(200, true, "Leave Add Successfully");
									}
									else{
										return $this->returnResponse(200, false, "Something went wrong.");
									}

								}
								else{
									// return $this->returnResponse(200, false, "You wouldn't applied before 30 day leave.");
									return $this->returnResponse(200, false, "You wouldn't be able to apply this leave.");
								
								}
							}
							else{
								return $this->returnResponse(200, true, "Leave Empty.");
							}
						}
						else{
							return $this->returnResponse(200, false, "User Not Active");
						}
					}
				}
				else{
					return $this->returnResponse(200, false, "User Id Not Found");  
				}
            }else{
                return $this->returnResponse(200, false, "Employe Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function save_leave_conditions($user_id,$leave_array){
		// return ['status' => false, 'message' => "Working on it. Please wait some time."];exit;
		$session = date('Y',strtotime($leave_array[0]['date']));
		$month = date('m',strtotime($leave_array[0]['date']));
			
		$take_pl = 0;
		$take_cl = 0;
		$take_sl = 0;
		$take_month_pl = 0;
		$take_month_cl = 0;
		$take_month_sl = 0;
		$take_month_comp_off = 0;
		$condition_check_leave = false;
		foreach($leave_array as $key => $all_value){
			$date_value = $all_value['date'];
			$type = $all_value['type'];
			$category = $all_value['category'];
			if($category != 'LWP'){
				$condition_check_leave = true;
			}
			$check_month = date('m',strtotime($date_value));
			
			if($category=='PL'){
				if($type == 'Full Day'){
					$add_pl = 1;
				}
				else{
					$add_pl = 0.5;
				}
				$take_pl += $add_pl;
				if($check_month == $month){
					$take_month_pl += $add_pl;
				}
			}
			else if($category=='CL'){
				if($type == 'Full Day'){
					$add_cl = 1;
				}
				else{
					$add_cl = 0.5;
				}
				$take_cl += $add_cl;
				if($check_month == $month){
					$take_month_cl += $add_cl;
				}
			}
			else if($category=='SL'){
				if($type == 'Full Day'){
					$add_sl = 1;
				}
				else{
					$add_sl = 0.5;
				}
				$take_sl += $add_sl;
				if($check_month == $month){
					$take_month_sl += $add_sl;
				}
			}
			else if($category=='Comp Off'){
				if($type == 'Full Day'){
					$take_month_comp_off += 1;
				}
				else{
					$take_month_comp_off += 0.5;
				}
				if($check_month != $month){
					return ['status' => false, 'message' => "You can't other month comp off."];
					exit;
				}
			}
		}
		if($take_pl > 5){
			return ['status' => false, 'message' => "You can't continue more then 5 PL"];exit;
		}
		if($take_cl > 3){
			return ['status' => false, 'message' => "You can't continue more then 3 CL"];exit;
		}
		if($take_sl > 3){
			return ['status' => false, 'message' => "You can't continue more then 3 SL "];exit;
		}
		
		// Current Month According Conditions
		// if($category!="Comp Off"){
			$pl_already_month = 0;
			$cl_already_month = 0;
			$sl_already_month = 0;
			$comp_off_already_month = 0;
			$month_pl_leave = DB::table('leave_details')->whereRaw("emp_id = $user_id AND MONTH(date) = $month AND YEAR(date) = $session and category IS NOT NULL and category !='Comp Off' and (status='Approved' OR status='Pending' )")->get();
			if(count($month_pl_leave) > 0){
				foreach($month_pl_leave as $val){
					if($val->type=='Full Day'){
						if($val->category=="PL"){
							$pl_already_month += 1;
						}
						else if($val->category=="CL"){
							$cl_already_month += 1;
						}
						else if($val->category=="SL"){
							$sl_already_month += 1;
						}
						else if($val->category=="Comp Off"){
							$comp_off_already_month += 1;
						}
					}
					else{
						if($val->category=="PL"){
							$pl_already_month += 0.5;
						}
						else if($val->category=="CL"){
							$cl_already_month += 0.5;
						}
						else if($val->category=="SL"){
							$sl_already_month += 0.5;
						}
						else if($val->category=="Comp Off"){
							$comp_off_already_month += 0.5;
						}
					}
				}
				
				$total_month_pl = $take_month_pl + $pl_already_month;
				$total_month_cl = $take_month_cl + $cl_already_month;
				$total_month_sl = $take_month_sl + $sl_already_month;
				
				/*
				if($total_month_pl > 5){
					return ['status' => false, 'message' => "Max 5 PL in a Month."];exit;
				}
				if($total_month_cl > 3){
					return ['status' => false, 'message' => "Max 3 CL in a Month."];exit;
				}
				if($total_month_sl > 3){
					return ['status' => false, 'message' => "Max 3 SL in a Month."];exit;
				}
				*/
				
				//Chetan
				if($condition_check_leave){
					$total_month_leave = $total_month_pl + $total_month_cl + $total_month_sl;
					if($total_month_leave > 3){
						// return ['status' => false, 'message' => "Maximum 3 leave apply in a Month."];exit;
					}
				}
			}
		// }
		if($condition_check_leave){
		$url = "http://15.207.232.85/index.php/api/users/leave_types";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Content-Type: application/x-www-form-urlencoded",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = "user_id=$user_id";

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		$pending_leaves =  json_decode($resp);
		if(!empty($pending_leaves->data)){
			$pl = $pending_leaves->data->pending_pl;
			$cl = $pending_leaves->data->pending_cl;
			$sl = $pending_leaves->data->pending_sl;
			$pending_comp_off = $pending_leaves->data->pending_comp_off;
			if($take_pl > $pl){
				return ['status' => false, 'message' => "Your remaining PL $pl"];exit;
			}			
			if($take_cl > $cl){
				return ['status' => false, 'message' => "Your remaining CL $cl"];exit;
			}
			if($take_sl > $sl){
				return ['status' => false, 'message' => "Your remaining SL $sl"];exit;
			}
			
			if( $take_month_comp_off > $pending_comp_off){
				return ['status' => false, 'message' => "Your remaining Compensation Off $pending_comp_off"]; exit;
			}
		}
		else{
			return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
			exit;
		}
		}
		
		return ['status' => true, 'message' => "Success"]; exit;
		
	}
	
	public function get_leave(Request $request)
    {
        try{

            $emp_id = $request->emp_id;
            $role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;

            if(isset($emp_id) && !empty($emp_id)){
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){
						
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
						
						if($user->role_id==24 || $user->role_id==29){ // 24 = HR, 29 = SuperAdmin
							//$all_users = User::where('status', 1)->where('register_id', "!=", null);

							$all_users = User::with(['user_details','user_branches']);
							

							if(!empty($role_id)){
			                    $all_users = $all_users->where('role_id', '=', $role_id);
			                }

			                if(!empty($department_head_id)){
			                    $all_users = $all_users->where('id', $department_head_id);
			                }

			                if(!empty($designation)){
			                    $all_users->WhereHas('user_details', function ($q) use ($designation) {
			                        $q->where('degination', $designation);
			                    });
			                }

			                if(!empty($branch_id)){
			                    $all_users->WhereHas('user_branches', function ($q) use ($branch_id) {
			                            $q->where('branch_id', $branch_id);
			                    });
			                }

							if(!empty($request->name)){
								$name = $request->name;
								$all_users->where(function($query) use ($name) {
									$query->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}
							//$all_users = $all_users->get();
							$all_users = $all_users->where('status', 1)->where('register_id', "!=", null)->where('role_id', '!=', 1)->get();
							if(count($all_users) > 0){
								$employees = $all_users; // Employees List
								foreach($employees as $key=>$value){
									if(!empty($value)){
										$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
									}
								}
							}
						}						
						else{
							// $check_supervisor = User::where('status', 1)->where('supervisor_id', $emp_id);
							//$check_supervisor = User::where('status', 1)->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ');

							$check_supervisor = User::with(['user_details','user_branches']);
							

							if(!empty($role_id)){
			                    $check_supervisor = $check_supervisor->where('role_id', '=', $role_id);
			                }

			                if(!empty($department_head_id)){
			                    $check_supervisor = $check_supervisor->where('id', $department_head_id);
			                }

			                if(!empty($designation)){
			                    $check_supervisor->WhereHas('user_details', function ($q1) use ($designation) {
			                        $q1->where('degination', $designation);
			                    });
			                }

			                if(!empty($branch_id)){
			                    $check_supervisor->WhereHas('user_branches', function ($q2) use ($branch_id) {
			                            $q2->where('branch_id', $branch_id);
			                    });
			                }

							if(!empty($request->name)){
								$name = $request->name;
								$check_supervisor->where(function($query) use ($name) {
									$query->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							}
							//$check_supervisor = $check_supervisor->get();

							$check_supervisor = $check_supervisor->where('status', 1)->where('role_id', '!=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();

							if(count($check_supervisor) > 0){
								$employees = $check_supervisor; // Employees List
								foreach($employees as $key=>$value){
									if(!empty($value)){
										$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
									}
								}
							}
						}
						
						$date_wise_leave = LeaveDetail::orderBy('date','desc')->groupBy('date');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_leave->where('date', '>=', $request->date_from);
							$date_wise_leave->where('date', '<=', $request->date_to);
						}
						else{
							$date_wise_leave->where('date', '>=' ,date('Y-m-d'));
						}
						
						$date_wise_leave = $date_wise_leave->get();
						if(count($date_wise_leave) > 0){
							$ii = 0;
							foreach($date_wise_leave as $key=>$valAtt){
								$date = $valAtt->date;
								$i = 0;
								$employee_leave = array();
								$leave_array = array();
								foreach($employees_array as $key2=>$employeeId){
									
									$get_leave = Leave::with(['user','leave_details'=>function ($q) use ($date) {
										$q->where('date', $date);
										}])
										->where('emp_id', $employeeId['emp_id']);
									$get_leave->WhereHas('leave_details', function ($q) use ($date) {
										$q->where('date', $date);
									});
									$get_leave = $get_leave->get();
									
									
									if(count($get_leave) > 0){
										foreach($get_leave as $value){
											if(!empty($value->leave_details) && count($value->leave_details) > 0){
												foreach($value->leave_details as $key1=>$leavedetail){
													$leavedetails['emp_id'] = $value->emp_id;
													$leavedetails['name'] = $value->user->name;
													$leavedetails['leave_id'] = $value->id;
													$leavedetails['reason'] = $value->reason;
													$leavedetails['leave_detail_id'] = $leavedetail->id;
													$leavedetails['date'] = date("d-m-Y", strtotime($leavedetail->date));
													$leavedetails['type'] = $leavedetail->type;
													$leavedetails['category'] = $leavedetail->category;
													$leavedetails['status'] = $leavedetail->status;
													
													$leave_array[$i] = $leavedetails;
													$i++;
												}
											}
										}
									}
								}
								
								if(!empty($leave_array)){
									$responseArray[$ii]['date'] = date("d-m-Y", strtotime($valAtt->date));
									$responseArray[$ii]['employees'] = $leave_array;
									$ii++;
								}
							}
						
						}
						if(!empty($responseArray)){
							$data['dates'] = $responseArray;
							return $this->returnResponse(200, true, "Leave Details", $data);
						}
						else{
							return $this->returnResponse(200, false, "Leave Not Found");
						} 
						
						
						
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
	
	public function update_leave(Request $request)
    {
        try{
            // $emp_id = $request->emp_id;
            $leave_detail_id = $request->leave_detail_id;

            if(isset($leave_detail_id) && !empty($leave_detail_id)){
				$find_leave_details = LeaveDetail::find($leave_detail_id);
				
				if(!empty($find_leave_details)){
					$leave_id = $find_leave_details->leave_id;
					$primaryLeave = Leave::find($leave_id);
					$emp_id = $primaryLeave->emp_id;
					$resResult = false;
					if(!empty($request->date)){
						$date = date('Y-m-d',strtotime($request->date));
						//if(strtotime($request->date) >= strtotime(date('Y-m-d'))){
							$before_30_days = strtotime('-30 days',strtotime(date('Y-m-d')));
							if(strtotime($date) >= $before_30_days){
								
								$leave_details = Leave::with(['leave_details'])->where('emp_id', $emp_id);
								$leave_details->WhereHas('leave_details', function ($q) use ($date,$leave_detail_id) {
									$q->where('date', $date);
									$q->where('id',"!=", $leave_detail_id);
								});									
								$leave_details = $leave_details->get();
								// echo "<pre>"; print_r($leave_details); die;
								// $leave_details = LeaveDetail::where('date',$request->date)->where('leave_id',$leave_id)->where('id','!=',$leave_detail_id)->get();
								// echo count($leave_details); die;
								if(count($leave_details)==0){
									$data = array();
									if(!empty($date)){
										$data['date'] = $date;
									}
									if(!empty($request->type)){
										$data['type'] = $request->type;
									}
									if(!empty($request->status)){
										$data['status'] = $request->status;
									}
									$find_leave_details->update($data);
									
									$this->maintain_history($emp_id, 'leave_details', $find_leave_details->id, 'update_leave_details', json_encode($data,JSON_UNESCAPED_UNICODE));
									
									if(!empty($request->reason)){
										$leave_update = Leave::find($leave_id);
										$leave_update->update(array('reason'=>$request->reason));
										
										$this->maintain_history($emp_id, 'leave', $leave_update->id, 'update_leave', json_encode(array('reason'=>$request->reason),JSON_UNESCAPED_UNICODE));
										
										$resResult = true;
									}
								}
								else{
									$allTypes = array();
									foreach($leave_details as $leaveVal){
										foreach($leaveVal->leave_details as $leaveDetailVal){
											// echo "<pre>"; print_r($leaveVal); die;
											$allTypes[] = $leaveDetailVal->type;
										}
									}
									// print_r($allTypes); die;
									if(!in_array('Full Day',$allTypes)){
										$type = $request->type;
										$saveLeaveCheck = true;
										if(in_array('1st Half',$allTypes)){
											if($type == "Full Day"){
												$saveLeaveCheck = false;
											}
										}
										else if(in_array('2nd Half',$allTypes)){
											if($type == "Full Day"){
												$saveLeaveCheck = false;
											}
										}
										if($saveLeaveCheck){
											$leave_checked = Leave::with(['leave_details'])->where('emp_id', $emp_id);
											$leave_checked->WhereHas('leave_details', function ($q) use ($date,$leave_detail_id,$type){
												$q->where('date', $date);
												$q->where('id',"!=", $leave_detail_id);
												$q->where('type', $type);
											});
											$leave_checked = $leave_checked->get();
											// $leave_checked = LeaveDetail::where('date',$request->date)->where('leave_id',$leave_id)->where('type',$type)->get();												
											if(count($leave_checked)==0){
												$data = array();
												if(!empty($date)){
													$data['date'] = $date;
												}
												if(!empty($request->type)){
													$data['type'] = $request->type;
												}
												if(!empty($request->status)){
													$data['status'] = $request->status;
												}
												$find_leave_details->update($data);
												
												$this->maintain_history($emp_id, 'leave_details', $find_leave_details->id, 'update_leave_details', json_encode($data,JSON_UNESCAPED_UNICODE));
												
												if(!empty($request->reason)){
													$leave_update = Leave::find($leave_id);
													$leave_update->update(array('reason'=>$request->reason));
													
													$this->maintain_history($emp_id, 'leave', $leave_update->id, 'update_leave', json_encode(array('reason'=>$request->reason),JSON_UNESCAPED_UNICODE));
													
													$resResult = true;
												}
												$resResult = true;
											}
										}
									}
								}
								if($resResult){
									return $this->returnResponse(200, true, "Leave Updated Successfully");
								}
								else{
									return $this->returnResponse(200, false, "Already Leave Exists.");
								}
							}
							else{
								return $this->returnResponse(200, false, "You wouldn't applied before 30 day leave.");  
							}
						//}
						// else{
						// 	return $this->returnResponse(200, false, "Date is invalid.");  
						// }
					}
					else{
						return $this->returnResponse(200, false, "Date is required.");  
					}
				}
				else{
					return $this->returnResponse(200, false, "Leave Id Invalid.");  
				}
				
            }else{
                return $this->returnResponse(200, false, "Leave Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function delete_leave(Request $request)
    {
        try{
            $leave_detail_id = $request->leave_detail_id;

            if(isset($leave_detail_id) && !empty($leave_detail_id)){ 
				$find_leave_details = LeaveDetail::find($leave_detail_id);
				if(!empty($find_leave_details)){				
					LeaveDetail::where('id', $leave_detail_id)->delete();
					
					$this->maintain_history($find_leave_details->emp_id, 'leave_details', $leave_detail_id, 'delete_leave', json_encode(array('id'=>$leave_detail_id)));
					// $updateData['status'] = "Deleted";
					// LeaveDetail::where('id', $leave_detail_id)->update($updateData);
					
					return $this->returnResponse(200, true, "Delete Successfully");
				}
				else{
					return $this->returnResponse(200, false, "Leave Id invalid");  
				}
						 
            }else{
                return $this->returnResponse(200, false, "Leave ids is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function update_leave_status(Request $request)
    {
        try{
            // $emp_id = $request->emp_id;
            $leave_detail_id = $request->leave_detail_id;

            if(isset($leave_detail_id) && !empty($leave_detail_id)){
				$find_leave_details = LeaveDetail::find($leave_detail_id);
				
				if(!empty($find_leave_details)){
					if(!empty($request->status)){
						$leave_details = LeaveDetail::find($leave_detail_id);
						if(!empty($leave_details)){
							$leave_details->update(array('status'=>$request->status));
							
							$this->maintain_history($find_leave_details->emp_id, 'leave_details', $leave_details->id, 'update_leave', json_encode(array('status'=>$request->status)));
							
							$resResult = true;
						}
						if($resResult){
							return $this->returnResponse(200, true, "Leave Updated Successfully");
						}
						else{
							return $this->returnResponse(200, false, "Already Leave Exists.");
						}
					}
					else{
						return $this->returnResponse(200, false, "Status is required.");
					}
				}
				else{
					return $this->returnResponse(200, false, "Leave Id Invalid.");  
				}
				
            }else{
                return $this->returnResponse(200, false, "Leave Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

	public function leave_types(Request $request)
    {
        try{
			// $session = date('Y');
			$session = 2022;
			$user_id = $request->user_id;

            if(isset($user_id) && !empty($user_id)){
				
				$user_check = User::query();
				$user_check->select(\DB::raw("users.*, branches.name as branch_name,branches.branch_location as branch_location,branches.id as branch_id"));
				$user_check->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
				$user_check->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
				$user_check->whereRaw("users.id = $user_id");
				$user_check->groupBy(['userbranches.user_id']);
				$user = $user_check->first();
				// echo "<pre>"; print_r($user); die;
				
				// $user_check = User::where('id', $user_id);
				// $user = $user_check->first();
				if(!empty($user)){
					//$user->status=='1'
					if(1){
						
						$leave_array = array();
						$pl = 0;
						$cl = 0;
						$sl = 0;
						$total_holiday_working = 0;	
						$get_leave_records = DB::table('leave_records')->whereRaw("user_id = $user_id AND session = $session")->first();
						if(!empty($get_leave_records)){
							$pl = $get_leave_records->pl;
							$cl = $get_leave_records->cl;
							$sl = $get_leave_records->sl;
							$pl = $get_leave_records->last_year_pl + $pl;
							$total_holiday_working = $get_leave_records->last_year_co;
						}
						
						$pl_already = 0;
						$cl_already = 0;
						$sl_already = 0;
						$comp_off_already_month = 0;
						$is_comp_off = 1;
						$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND YEAR(date) = $session and (status='Approved' OR status='Pending') and category IS NOT NULL")->get();
						if(count($already_leaves) > 0){
							foreach($already_leaves as $val){
								if($val->type=='Full Day'){
									if($val->category=="PL"){
										$pl_already += 1;
									}
									else if($val->category=="CL"){
										$cl_already += 1;
									}
									else if($val->category=="SL"){
										$sl_already += 1;
									}
									else if($val->category=="Comp Off"){
										$comp_off_already_month += 1;
									}
									
								}
								else{
									if($val->category=="PL"){
										$pl_already += 0.5;
									}
									else if($val->category=="CL"){
										$cl_already += 0.5;
									}
									else if($val->category=="SL"){
										$sl_already += 0.5;
									}
									else if($val->category=="Comp Off"){
										$comp_off_already_month += 0.5;
									}
								}
							}
							$pl = $pl - $pl_already;
							$cl = $cl - $cl_already;
							$sl = $sl - $sl_already;
							
						}
						
						$total_time = $user->total_time;
						$holiday_location = 0;
						if($user->branch_location=='jodhpur'){
							$holiday_location = 1;
						}
						else if($user->branch_location=='jaipur'){
							$holiday_location = 2;
						}
						else if($user->branch_location=='delhi'){
							$holiday_location = 3;
						}
						$today_date = date('Y-m-d');
						
						$holiday_array = array();					
						$get_holiday = Holiday::select('date','branch_id')->where('status', '1')->where('is_deleted', '0')->whereRaw("DATE(date) <= '$today_date' AND (location = 0 OR location = $holiday_location  )")->get();
						if(count($get_holiday) > 0){
							foreach($get_holiday as $get_holiday_val){
								if(!empty($get_holiday_val->branch_id)){
									$holiday_branch = json_decode($get_holiday_val->branch_id);
									if(!empty($user->branch_id) && !empty($holiday_branch) && in_array($user->branch_id, $holiday_branch)){
										array_push($holiday_array, $get_holiday_val->date);
									}	
								}
								else{
									array_push($holiday_array, $get_holiday_val->date);
								}
								
							}
						}
						// echo "<pre>"; print_r($holiday_array); die;
						$months = array();
						// $current_month = date('m');
						$current_month = 12;
						if($user->is_extra_working_salary=='1'){
							$is_comp_off = 0;
							$comp_off_already_month = 0;
							$all_months = array($current_month);
							$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND MONTH(date) = $current_month AND YEAR(date) = $session and (status='Approved' OR status='Pending') and category IS NOT NULL")->get();
							if(count($already_leaves) > 0){
								foreach($already_leaves as $val){
									if($val->type=='Full Day'){
										if($val->category=="Comp Off"){
											$comp_off_already_month += 1;
										}
									}
									else{
										if($val->category=="Comp Off"){
											$comp_off_already_month += 0.5;
										}
									}
								}
							}
						}
						else{
							if(!empty($user->comp_off_start_date)){
								if($session==date('Y',strtotime($user->comp_off_start_date))){
									$all_months = array();
									$start_month = date("n",strtotime($user->comp_off_start_date));
									for ($x = $start_month; $x <= 12; $x++) {
										$all_months[] = $x;
									}
								}
								else{
									$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
								}
								
							}
							else{
								$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
							}
						}
						if($is_comp_off){
							foreach($all_months as $y_month){
								if($session == 2021){
									if($y_month > 9){
										$months[] = $y_month;
									}
								}
								else{
									if($current_month >= $y_month){
										$months[] = $y_month;
									}
								}
							}
							// print_r($months); die;
							$yr = $session;
							foreach($months as $key=>$mt){
								$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
								$first_date = strtotime($yr.'-'.$mt.'-01');
								$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);	
								
								$attendance_array = array();
									
								$first_date_get = date('Y-m-d',$first_date);
								$last_date_get = date('Y-m-d',$last_date);
								
								if($user_id==684000000000){ /* For session 2023 */
									$attendance = Attendance::query();
									$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,time"));
									$attendance->where('emp_id', $user_id);
									$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
									
									
									$attendancenew = AttendanceNew::query();
									$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,time"));
									$attendancenew->where('emp_id', $user_id);
									$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
									
									$comman = $attendancenew->union($attendance);
									$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
											   ->mergeBindings($comman->getQuery())
											   // ->groupBy('comman.emp_id')
											   // ->groupBy('comman.date')
											   ->orderBy('comman.date','asc')
											   ->orderBy('comman.time','asc')
											   ->get();
									
									if(count($comman_result1) > 0){
										$attendance_array = json_decode(json_encode($comman_result1),true);
										while($getWorkSunday> 0){
											$workday = date("D", $first_date);
											$add_get_date = date("Y-m-d", $first_date);
											
											$all_dates_keys = array_keys(array_column($attendance_array, 'date'), $add_get_date);
											$min_time = "";
											$max_time = "";
											if(count($all_dates_keys) > 0){
												$date_array_index = [];
												foreach($all_dates_keys as $indexDate){
													$date_array_index[] = $attendance_array[$indexDate];
												}
												
												$min_time =  min(array_column($date_array_index, 'time'));
												$max_time =  max(array_column($date_array_index, 'time'));
											}
											
											if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) {
												
												$in_time = $min_time;
												$out_time = $max_time; 
												$total_minute = 0;
												if(!empty($in_time) && !empty($out_time)){
													$in_time = date("h:i A", strtotime($in_time));
													$out_time = date("h:i A", strtotime($out_time));
													$intime = new DateTime($in_time);
													$outtime = new DateTime($out_time);
													$interval = $intime->diff($outtime);
													$hours = $interval->format('%H');
													$minute = $interval->format('%I');
													$total_minute = ($hours*60)+$minute;
													$total_minute	=	($total_minute*100)/$total_time;
												}						 
												if($workday=="Sun"){
													if($total_minute <= 44.44){
														//44.44%
														//Absent
													}
													else{
														$total_holiday_working++;
													}
												}
												else if(in_array($add_get_date, $holiday_array)){
													$check_holiday  = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
													$is_optional 	= false;
													if($check_holiday->type=="Optional"){
														$is_optional = true;
													}
													
													if($total_minute >= 66.66){
														if($is_optional){
														}
														else{
															$total_holiday_working++;
														}
													}
												}
												
											}				
											$first_date += 86400; 
											$getWorkSunday--;
										}
									}
									
								}
								else{
								
								$attendance = Attendance::query();
								$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendance->where('emp_id', $user_id);
								$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendance = $attendance->groupBy('date');
								
								
								$attendancenew = AttendanceNew::query();
								$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendancenew->where('emp_id', $user_id);
								$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendancenew = $attendancenew->groupBy('date');				
								
								$comman = $attendancenew->union($attendance);
								$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->groupBy('comman.emp_id')
										   ->groupBy('comman.date')
										   ->get();
								
								if(count($comman_result1) > 0){
									$attendance_array = json_decode(json_encode($comman_result1),true);
								
									while($getWorkSunday> 0){
										$workday = date("D", $first_date);
										$add_get_date = date("Y-m-d", $first_date);
										if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
											
											$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
											
											$in_time = $attendance_array[$array_index]['in_time'];
											$out_time = $attendance_array[$array_index]['out_time'];
											$total_minute = 0;
											if(!empty($in_time) && !empty($out_time)){
												$in_time = date("h:i A", strtotime($in_time));
												$out_time = date("h:i A", strtotime($out_time));
												$intime = new DateTime($in_time);
												$outtime = new DateTime($out_time);
												$interval = $intime->diff($outtime);
												$hours = $interval->format('%H');
												$minute = $interval->format('%I');
												$total_minute = ($hours*60)+$minute;
												$total_minute	=	($total_minute*100)/$total_time;
											}						 
											if($workday=="Sun"){
												if($total_minute <= 44.44){
													//44.44%
													//Absent
												}
												else{
													$total_holiday_working++;
												}
											}
											else if(in_array($add_get_date, $holiday_array)){
												$check_holiday  = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
												$is_optional 	= false;
												if($check_holiday->type=="Optional"){
													$is_optional = true;
												}
												
												if($total_minute >= 66.66){
													if($is_optional){
													}
													else{
														$total_holiday_working++;
													}
												}
											}
											
										}				
										$first_date += 86400; 
										$getWorkSunday--;
									}
								}
								}
							}
							// echo $total_holiday_working; die;
							$total_holiday_working = $total_holiday_working - $comp_off_already_month;
						}
						else{
							$total_holiday_working = 0;
						}
						
						$leave_array['pending_pl'] = $pl;
						$leave_array['pending_cl'] = $cl;
						$leave_array['pending_sl'] = $sl;
						$leave_array['pending_comp_off'] = $total_holiday_working;
						$leave_array['is_comp_off'] = $is_comp_off;
						
						 
						$data['data'] = $leave_array;
						 
						return $this->returnResponse(200, true, "Total Pending Leave Details", $data);
						
						
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
	
	public function leave_types_for_approval(Request $request)
    {
        try{
			// $session = date('Y');
			$session = 2022;
			$user_id = $request->user_id;

            if(isset($user_id) && !empty($user_id)){
				
				$user_check = User::query();
				$user_check->select(\DB::raw("users.*, branches.name as branch_name,branches.branch_location as branch_location"));
				$user_check->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
				$user_check->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
				$user_check->whereRaw("users.id = $user_id");
				$user_check->groupBy(['userbranches.user_id']);
				$user = $user_check->first();
				
				// $user_check = User::where('id', $user_id);
				// $user = $user_check->first();
				if(!empty($user)){
					//$user->status=='1'
					if(1){
						
						$leave_array = array();
						$pl = 0;
						$cl = 0;
						$sl = 0;
						$total_holiday_working = 0;	
						$get_leave_records = DB::table('leave_records')->whereRaw("user_id = $user_id AND session = $session")->first();
						if(!empty($get_leave_records)){
							$pl = $get_leave_records->pl;
							$cl = $get_leave_records->cl;
							$sl = $get_leave_records->sl;
							$pl = $get_leave_records->last_year_pl + $pl;
							$total_holiday_working = $get_leave_records->last_year_co;
						}
						
						$pl_already = 0;
						$cl_already = 0;
						$sl_already = 0;
						$comp_off_already_month = 0;
						$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND YEAR(date) = $session and (status='Approved') and category IS NOT NULL")->get();
						if(count($already_leaves) > 0){
							foreach($already_leaves as $val){
								if($val->type=='Full Day'){
									if($val->category=="PL"){
										$pl_already += 1;
									}
									else if($val->category=="CL"){
										$cl_already += 1;
									}
									else if($val->category=="SL"){
										$sl_already += 1;
									}
									else if($val->category=="Comp Off"){
										$comp_off_already_month += 1;
									}
									
								}
								else{
									if($val->category=="PL"){
										$pl_already += 0.5;
									}
									else if($val->category=="CL"){
										$cl_already += 0.5;
									}
									else if($val->category=="SL"){
										$sl_already += 0.5;
									}
									else if($val->category=="Comp Off"){
										$comp_off_already_month += 0.5;
									}
								}
							}
							$pl = $pl - $pl_already;
							$cl = $cl - $cl_already;
							$sl = $sl - $sl_already;
							
						}
						
						$holiday_location = 0;
						if($user->branch_location=='jodhpur'){
							$holiday_location = 1;
						}
						else if($user->branch_location=='jaipur'){
							$holiday_location = 2;
						}
						else if($user->branch_location=='delhi'){
							$holiday_location = 3;
						}
						$today_date = date('Y-m-d');
						
						$holiday_array = array();					
						$get_holiday = Holiday::select('date')->where('status', '1')->where('is_deleted', '0')->whereRaw("DATE(date) <= '$today_date' AND (location = 0 OR location = $holiday_location  )")->get();
						if(count($get_holiday) > 0){
							foreach($get_holiday as $get_holiday_val){
								array_push($holiday_array, $get_holiday_val->date);
							}
						}
						$months = array();
						// $current_month = date('m');
						$current_month = 12;
						$is_comp_off = 1;
						if($user->is_extra_working_salary=='1'){
							$is_comp_off = 0;
							$comp_off_already_month = 0;
							$all_months = array($current_month);
							$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND MONTH(date) = $current_month AND YEAR(date) = $session and (status='Approved') and category IS NOT NULL")->get();
							if(count($already_leaves) > 0){
								foreach($already_leaves as $val){
									if($val->type=='Full Day'){
										if($val->category=="Comp Off"){
											$comp_off_already_month += 1;
										}
									}
									else{
										if($val->category=="Comp Off"){
											$comp_off_already_month += 0.5;
										}
									}
								}
							}
						}
						else{
							$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
						}
						foreach($all_months as $y_month){
							if($session == 2021){
								if($y_month > 9){
									$months[] = $y_month;
								}
							}
							else{
								if($current_month >= $y_month){
									$months[] = $y_month;
								}
							}
						}
						// print_r($months); die;
						$yr = $session;
						foreach($months as $key=>$mt){
							$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
							$first_date = strtotime($yr.'-'.$mt.'-01');
							$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);	
							
							$attendance_array = array();
								
							$first_date_get = date('Y-m-d',$first_date);
							$last_date_get = date('Y-m-d',$last_date);
							
							$attendance = Attendance::query();
							$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
							$attendance->where('emp_id', $user_id);
							$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
							$attendance = $attendance->groupBy('date');
							
							
							$attendancenew = AttendanceNew::query();
							$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
							$attendancenew->where('emp_id', $user_id);
							$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
							$attendancenew = $attendancenew->groupBy('date');				
							
							$comman = $attendancenew->union($attendance);
							$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
									   ->mergeBindings($comman->getQuery())
									   ->groupBy('comman.emp_id')
									   ->groupBy('comman.date')
									   ->get();
							
							if(count($comman_result1) > 0){
								$attendance_array = json_decode(json_encode($comman_result1),true);
							
								while($getWorkSunday> 0){
									$workday = date("D", $first_date);
									$add_get_date = date("Y-m-d", $first_date);
									if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
										
										$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
										
										$in_time = $attendance_array[$array_index]['in_time'];
										$out_time = $attendance_array[$array_index]['out_time'];
										$total_minute = 0;
										if(!empty($in_time) && !empty($out_time)){
											$in_time = date("h:i A", strtotime($in_time));
											$out_time = date("h:i A", strtotime($out_time));
											$intime = new DateTime($in_time);
											$outtime = new DateTime($out_time);
											$interval = $intime->diff($outtime);
											$hours = $interval->format('%H');
											$minute = $interval->format('%I');
											$total_minute = ($hours*60)+$minute;
										}						 
										if($workday=="Sun"){
											if($total_minute <= 240){
												//240 Mint = 4 hour
												//Absent
											}
											else{
												$total_holiday_working++;
											}
										}
										else if(in_array($add_get_date, $holiday_array)){
											$check_holiday  = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
											$is_optional 	= false;
											if($check_holiday->type=="Optional"){
												$is_optional = true;
											}
											
											if($total_minute >= 360){
												if($is_optional){
												}
												else{
													$total_holiday_working++;
												}
											}
										}
										
									}				
									$first_date += 86400; 
									$getWorkSunday--;
								}
							}
						}
						// echo $total_holiday_working; die;
						$total_holiday_working = $total_holiday_working - $comp_off_already_month;
						
						$leave_array['pending_pl'] = $pl;
						$leave_array['pending_cl'] = $cl;
						$leave_array['pending_sl'] = $sl;
						$leave_array['pending_comp_off'] = $total_holiday_working;
						$leave_array['is_comp_off'] = $is_comp_off;
						
						 
						$data['data'] = $leave_array;
						 
						return $this->returnResponse(200, true, "Total Pending Leave Details", $data);
						
						
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
	
	
	public function leave_types_all(Request $request)
    {
		$allData = array();
        try{
			// $session = date('Y');
			$session = 2022;
			$user_ids = $request->user_id;
			$user_ids = explode(",",$user_ids);
			// print_r($user_ids); die;
            if(isset($user_ids) && !empty($user_ids)){
				foreach($user_ids as $user_id){
					$user_check = User::where('id', $user_id);
					$user = $user_check->first();
					if(!empty($user)){
						if($user->status=='1'){
							$register_id = $user->register_id;
							$leave_array = array();
							$pl = 0;
							$cl = 0;
							$sl = 0;
							$get_leave_records = DB::table('leave_records')->whereRaw("user_id = $user_id AND session = $session")->first();
							if(!empty($get_leave_records)){
								$pl = $get_leave_records->pl;
								$cl = $get_leave_records->cl;
								$sl = $get_leave_records->sl;
							}
							
							$pl_already = 0;
							$cl_already = 0;
							$sl_already = 0;
							$comp_off_already_month = 0;
							$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND YEAR(date) = $session and (status='Approved' OR status='Pending') and category IS NOT NULL")->get();
							if(count($already_leaves) > 0){
								foreach($already_leaves as $val){
									if($val->type=='Full Day'){
										if($val->category=="PL"){
											$pl_already += 1;
										}
										else if($val->category=="CL"){
											$cl_already += 1;
										}
										else if($val->category=="SL"){
											$sl_already += 1;
										}
										else if($val->category=="Comp Off"){
											$comp_off_already_month += 1;
										}
										
									}
									else{
										if($val->category=="PL"){
											$pl_already += 0.5;
										}
										else if($val->category=="CL"){
											$cl_already += 0.5;
										}
										else if($val->category=="SL"){
											$sl_already += 0.5;
										}
										else if($val->category=="Comp Off"){
											$comp_off_already_month += 0.5;
										}
									}
								}
								$pl = $pl - $pl_already;
								$cl = $cl - $cl_already;
								$sl = $sl - $sl_already;
								
							}
							
							$holiday_array = array();
							$total_holiday_working = 0;						
							$get_holiday = Holiday::select('date')->where('status', '1')->where('is_deleted', '0')->get();
							if(count($get_holiday) > 0){
								foreach($get_holiday as $get_holiday_val){
									array_push($holiday_array, $get_holiday_val->date);
								}
							}
							$months = array();
							$current_month = 12;
							if($user->is_extra_working_salary=='1'){
								$comp_off_already_month = 0;
								$all_months = array($current_month);
								$already_leaves = DB::table('leave_details')->whereRaw("emp_id = $user_id AND MONTH(date) = $current_month AND YEAR(date) = $session and (status='Approved' OR status='Pending') and category IS NOT NULL")->get();
								if(count($already_leaves) > 0){
									foreach($already_leaves as $val){
										if($val->type=='Full Day'){
											if($val->category=="Comp Off"){
												$comp_off_already_month += 1;
											}
										}
										else{
											if($val->category=="Comp Off"){
												$comp_off_already_month += 0.5;
											}
										}
									}
								}
							}
							else{
								$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
							}
							foreach($all_months as $y_month){
								if($session == 2021){
									if($y_month > 9){
										$months[] = $y_month;
									}
								}
								else{
									if($current_month >= $y_month){
										$months[] = $y_month;
									}
								}
							}
							// print_r($months); die;
							$yr = $session;
							foreach($months as $key=>$mt){
								$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
								$first_date = strtotime($yr.'-'.$mt.'-01');
								$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);	
								
								$attendance_array = array();
									
								$first_date_get = date('Y-m-d',$first_date);
								$last_date_get = date('Y-m-d',$last_date);
								
								$attendance = Attendance::query();
								$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendance->where('emp_id', $user_id);
								$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendance = $attendance->groupBy('date');
								
								
								$attendancenew = AttendanceNew::query();
								$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendancenew->where('emp_id', $user_id);
								$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendancenew = $attendancenew->groupBy('date');				
								
								$comman = $attendancenew->union($attendance);
								$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->groupBy('comman.emp_id')
										   ->groupBy('comman.date')
										   ->get();
								
								if(count($comman_result1) > 0){
									$attendance_array = json_decode(json_encode($comman_result1),true);
								
									while($getWorkSunday> 0){
										$workday = date("D", $first_date);
										$add_get_date = date("Y-m-d", $first_date);
										if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
											
											$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
											
											$in_time = $attendance_array[$array_index]['in_time'];
											$out_time = $attendance_array[$array_index]['out_time'];
											$total_minute = 0;
											if(!empty($in_time) && !empty($out_time)){
												$in_time = date("h:i A", strtotime($in_time));
												$out_time = date("h:i A", strtotime($out_time));
												$intime = new DateTime($in_time);
												$outtime = new DateTime($out_time);
												$interval = $intime->diff($outtime);
												$hours = $interval->format('%H');
												$minute = $interval->format('%I');
												$total_minute = ($hours*60)+$minute;
											}						 
											if($workday=="Sun"){
												if($total_minute <= 240){
													//240 Mint = 4 hour
													//Absent
												}
												else{
													$total_holiday_working++;
												}
											}
											else if(in_array($add_get_date, $holiday_array)){
												$check_holiday  = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
												$is_optional 	= false;
												if($check_holiday->type=="Optional"){
													$is_optional = true;
												}
												
												if($total_minute >= 360){
													if($is_optional){
													}
													else{
														$total_holiday_working++;
													}
												}
											}
											
										}				
										$first_date += 86400; 
										$getWorkSunday--;
									}
								}
							}
							// echo $total_holiday_working; die;
							
							$total_holiday_working = $total_holiday_working - $comp_off_already_month;
							
							/* if($user->is_extra_working_salary=='1'){
								$total_holiday_working = 0;
							}
							else{
								$total_holiday_working = $total_holiday_working - $comp_off_already_month;
							} */
							
							$leave_array['pending_pl'] = $pl;
							$leave_array['pending_cl'] = $cl;
							$leave_array['pending_sl'] = $sl;
							$leave_array['pending_comp_off'] = $total_holiday_working;
							$leave_array['user_id'] = $user_id;
							
							 
							$allData[$register_id] = $leave_array;
							 
							// return $this->returnResponse(200, true, "Total Pending Leave Details", $data);
							
							
						}
						else{
							// return $this->returnResponse(200, false, "User Not Active");
						}
					}
					else{
						// return $this->returnResponse(200, false, "User Id Not Found"); 
					}				
				}
            }else{
                // return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            // return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            // return $this->returnResponse(500, false, $ex->getMessage());
        }
		$data['data'] = $allData;
		
		return $this->returnResponse(200, true, "Total Pending Leave Details",$data);
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
	
	public function manual_get_pending_leave(Request $request){
		
		$get_users = DB::select("SELECT id FROM users WHERE NOT EXISTS (SELECT * FROM get_pending_leave_test WHERE get_pending_leave_test.user_id = users.id) and users.role_id != 2 and users.role_id != 29 and users.register_id is not null limit 100");
		$i = 0;
		foreach($get_users as $val){
			// $user_id = 5453;
			$user_id = $val->id;
			$request->user_id = $user_id;
			$resp = $this->leave_types($request);
			$pending_leaves =  json_decode(json_encode($resp), true);
			if(!empty($pending_leaves)){
				$all_data = $pending_leaves['original']['data'];
				$i++;
				$history_data = array();
				$history_data['user_id'] = $user_id;
				$history_data['pl'] = $all_data['pending_pl'];
				$history_data['cl'] = $all_data['pending_cl'];
				$history_data['sl'] = $all_data['pending_sl'];
				$history_data['co'] = $all_data['pending_comp_off'];
				
				DB::table('get_pending_leave_test')->insert($history_data);
			}
			
		}
		echo "<pre>"; print_r($i); die;
	}
}
