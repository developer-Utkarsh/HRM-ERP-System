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
use App\Task;
use App\NewTask;
use App\TaskDetail;
use App\TaskHistory;
use DB;

class TaskController extends Controller
{
	
	public function add_task(Request $request)
    {
        try{
            $emp_id = $request->emp_id;
            // $assigned_userid = $request->assigned_userid;

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				// print_r($user->user_details); die;
				if(!empty($user)){
					if($user->status=='1'){
						$check_task = array();
						$task = Task::where('emp_id', $request->emp_id)->where('date', date('Y-m-d'))->first();
						if(!empty($task)){
							$check_task = $task;
						}
						else{
							$inputs['emp_id'] = $emp_id;
							$inputs['date'] = date('Y-m-d');
							$taskSave = Task::create($inputs);
							$check_task = $taskSave;
						}
						if(!empty($check_task)){
							if(is_array($request->task_array) && !empty($request->task_array)) {
								$task_array = $request->task_array;
								$assigned_users = array();
								foreach ($task_array as $key => $value) {
									if(!empty($value)){
										$data = array();
										if(!empty($value['name'])){
											$data['name'] = $value['name'];
										}
										if(!empty($value['plan_hour'])){
											$data['plan_hour'] = $value['plan_hour'];
										}
										if(!empty($value['description'])){
											$data['description'] = $value['description'];
										}
										/* if(!empty($value['dropped_reason'])){
											$data['dropped_reason'] = $value['dropped_reason'];
										} */
										if(!empty($data)){
											if(!empty($value['task_detail_id'])){
												$task_detail_id = $value['task_detail_id'];
												$data['assigned_userid'] = $value['assigned_userid'];
												
												$data['task_id'] = $check_task->id;
												// $data['assigned_date'] = $check_task->date;
												$task_details = TaskDetail::find($task_detail_id);
												$old_assigned_userid = $task_details->assigned_userid;
												$task_details->update($data);
												if(!empty($value['assigned_userid'])){
													$assigned_userid[] = $value['assigned_userid'];
													$task_update = Task::find($check_task->id); // $task_details->task_id
													$assigned_users_already = json_decode($task_update->assigned_users);
													if(!empty($assigned_users_already)){
														$assigned_users_update = (array_unique(array_merge($assigned_users_already,$assigned_userid)));
														$assigned_users_update = array_values($assigned_users_update);
													}
													else{
														$assigned_users_update = $assigned_userid;
													}
													$updateData['assigned_users'] = json_encode($assigned_users_update);
													$task_update->update($updateData);
													
													$task_details->update(['assigned_date'=>date('Y-m-d')]);
													
													if($old_assigned_userid != $value['assigned_userid']){ 
														$user_detail = User::where('id', $value['assigned_userid'])->first();
														if($user_detail){
															if($user_detail->gsm_token){
																$gsm_name = $user->name;
																$gsm_status = 'Pending';
																$load = array();
																$load['title'] = 'Task Assigned';
																$load['description'] = "Task Assigend By $gsm_name";
																$load['status'] = $gsm_status;
																$load['type'] = 'Task';
																
																$token = $user_detail->gsm_token;

																$this->android_notification($token, $load);
															}   
														}
													}
													
													
												}
											}
											else{
												if(!empty($value['assigned_userid'])){
													$data['assigned_userid'] = $value['assigned_userid'];
													$assigned_users[] = $value['assigned_userid'];
													
													if($emp_id != $value['assigned_userid']){ 
														$user_detail = User::where('id', $value['assigned_userid'])->first();
														if($user_detail){
															if($user_detail->gsm_token){
																$gsm_name = $user->name;
																$gsm_status = 'Pending';
																$load = array();
																$load['title'] = 'Task Assigned';
																$load['description'] = "Task Assigend By $gsm_name";
																$load['status'] = $gsm_status;
																$load['type'] = 'Task';
																
																$token = $user_detail->gsm_token;

																$this->android_notification($token, $load);
															}   
														}
													}
												}
												
												$data['task_id'] = $check_task->id;
												$data['assigned_date'] = $check_task->date;
												TaskDetail::create($data);
											}
										}
									}
								}
								if(!empty($assigned_users)){
									$assigned_users_already = json_decode($check_task->assigned_users);
									if(!empty($assigned_users_already)){
										$assigned_users_update = (array_unique(array_merge($assigned_users_already,$assigned_users)));
										$assigned_users_update = array_values($assigned_users_update);
									}
									else{
										$assigned_users_update = $assigned_users;
									}
									$updateData['assigned_users'] = json_encode($assigned_users_update);
									$check_task->update($updateData);
								}
								
								return $this->returnResponse(200, true, "Task Add Successfully");
							}
							else{
								return $this->returnResponse(200, true, "Task Empty.");
							}
						}
						else{
							return $this->returnResponse(200, true, "Something went wrong.");
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
                return $this->returnResponse(200, false, "Employe Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function update_task(Request $request)
    {
        try{
            $emp_id = $request->emp_id;
            $task_id = $request->task_id;

            if(isset($emp_id) && !empty($emp_id) && isset($task_id) && !empty($task_id)){
				$user = User::where('id', $emp_id)->first();
				// print_r($user->user_details); die;
				if(!empty($user)){
					if($user->status=='1'){
						$task = Task::where('id', $task_id)->first();
						//->where('emp_id', $request->emp_id)
						// print_r($task); die;
						if(!empty($task)){
							$emp_id = $task->emp_id;
							$task_user_data = User::where('id', $emp_id)->first();
							// echo "<pre>"; print_r($request->task_array); die;
							if (is_array($request->task_array) && !empty($request->task_array)) {
								$task_array = $request->task_array;
								$assigned_users = array();
								foreach ($task_array as $key => $value) {
									if(!empty($value)){
										$task_detail_id = $value['task_detail_id'];
										$task_details = TaskDetail::find($task_detail_id);
										$old_status = $task_details->status;
										$old_assigned_userid = $task_details->assigned_userid;
										$data = array();
										if(!empty($value['name'])){
											$data['name'] = $value['name'];
										}
										if(!empty($value['plan_hour'])){
											$data['plan_hour'] = $value['plan_hour'];
										}
										if(!empty($value['spent_hour']) && ($value['spent_hour'] !="00.00")){
											$data['spent_hour'] = $value['spent_hour'];
										}
										if(!empty($value['status'])){
											$data['status'] = $value['status'];
										}
										if(!empty($value['description'])){
											$data['description'] = $value['description'];
										}
										if(!empty($value['dropped_reason'])){
											$data['dropped_reason'] = $value['dropped_reason'];
										}
										if(!empty($value['assigned_userid'])){
											$data['assigned_userid'] = $value['assigned_userid'];
											$assigned_users[] = $value['assigned_userid'];
										}
										$task_details->update($data);
										
										
										if(!empty($value['status'])){
											
											if($old_assigned_userid != $value['assigned_userid']){
												$user_detail = User::where('id', $value['assigned_userid'])->first();
												if($user_detail){
													if($user_detail->gsm_token){
														$gsm_name = $task_user_data->name;
														$gsm_status = $value['status'];
														$load = array();
														$load['title'] = 'Task Assigned';
														$load['description'] = "Task Assigend By $gsm_name";
														$load['status'] = $gsm_status;
														$load['type'] = 'Task';
														
														$token = $user_detail->gsm_token;

														$this->android_notification($token, $load);
													}   
												}
											}
											
											if($old_status != $value['status']){
												$user_detail = User::where('id', $emp_id)->first();
												if($user_detail){
													if($user_detail->gsm_token){
														$gsm_name = $user_detail->name;
														$gsm_status = $value['status'];
														$load = array();
														$load['title'] = 'Task status updated';
														$load['description'] = "Task status change to $gsm_status by $gsm_name";
														$load['status'] = $gsm_status;
														$load['type'] = 'Task';

														$token = $user_detail->gsm_token;

														$this->android_notification($token, $load);
													}   
												}
											}
											
											$next_day_date = date("Y-m-d", strtotime("+ 1 day"));
											
											$day = date('D', strtotime($next_day_date));
											if(strtolower($day)=="sun"){
												$next_day_date = date("Y-m-d", strtotime("+ 2 day"));
											}
											
											if($value['status']=="In Progress" || $value['status']=="Not Started"){
												$next_day_task = Task::where('emp_id', $emp_id)->where('date', $next_day_date)->first();
												if(!empty($next_day_task)){
													if(!empty($data)){
														$nextDatTaskDetailCheck = TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->first();
														if(!empty($nextDatTaskDetailCheck)){
															$data['plan_hour'] = 0;
															$nextDatTaskDetailCheck->update($data);
														}
														else{
															if(!empty($assigned_users)){
																$assigned_users_already = json_decode($next_day_task->assigned_users);
																if(!empty($assigned_users_already)){
																	$assigned_users_update = (array_unique(array_merge($assigned_users_already,$assigned_users)));
																	$assigned_users_update = array_values($assigned_users_update);
																}
																else{
																	$assigned_users_update = $assigned_users;
																}
																$updateData['assigned_users'] = json_encode($assigned_users_update);
																
																$next_day_task->update($updateData);
															}
															
															$data['parent_task_detail_id'] = $task_detail_id;
															$data['task_id'] = $next_day_task->id;
															$data['plan_hour'] = 0;
															$data['assigned_date'] = $next_day_date;
															TaskDetail::create($data);
														}
													}
												}
												else{
													if(!empty($assigned_users)){
														$assigned_users_update = $assigned_users;
														$inputs['assigned_users'] = json_encode($assigned_users_update);
													}
													
													$inputs['emp_id'] = $emp_id;
													$inputs['date'] = $next_day_date;
													$nextDatTaskSave = Task::create($inputs);
													if($nextDatTaskSave){
														$data['parent_task_detail_id'] = $task_detail_id;
														$data['task_id'] = $nextDatTaskSave->id;
														$data['plan_hour'] = 0;
														$data['assigned_date'] = $next_day_date;
														TaskDetail::create($data);
													}
												}
											}
											else{
												$next_day_task = Task::where('emp_id', $emp_id)->where('date', $next_day_date)->first();
												if(!empty($next_day_task)){
													$taskdetails = TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->first();
										
													$inputs_del = array('is_deleted' => '1'); 
													$taskdetails->update($inputs_del);
										
													//TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->delete();
												}
											}
										}
									}
								}
								
								if(!empty($assigned_users)){
									$assigned_users_already = json_decode($task->assigned_users);
									if(!empty($assigned_users_already)){
										$assigned_users_update = (array_unique(array_merge($assigned_users_already,$assigned_users)));
										$assigned_users_update = array_values($assigned_users_update);
									}
									else{
										$assigned_users_update = $assigned_users;
									}
									// print_r($assigned_users_update); die;
									$updateData['assigned_users'] = json_encode($assigned_users_update);
									$task->update($updateData);
								}
								
							}
							
							if(isset($request->date) && !empty($request->date)){
								$data_update['date'] = $request->date;
								$task->update($data_update);
							}
							
							return $this->returnResponse(200, true, "Task Updated Successfully");
							
						}
						else{							
							return $this->returnResponse(200, false, "Something Went Wrong !");
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
                return $this->returnResponse(200, false, "Employe Id and Task Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
    
	
	public function get_task(Request $request)
    {
		
        try{

             $get_emp_id = $request->emp_id;

            if(isset($get_emp_id) && !empty($get_emp_id)){
				$user_check = User::where('id', $get_emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					$department_type = $user->department_type;
					if($user->status=='1'){
						$user_role_id = $user->role_id;
						$employees_array = array();
						if(!empty($request->name)){
							$name = $request->name;
							$user_check->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name);
							});
							// $user_check->where("name", "LIKE", "%$name%");
						}
						$user_data =  $user_check->first();
						if(!empty($user_data)){
							$employees_array[] = array('emp_id'=>$get_emp_id,'name'=>$user_data->name,'role_id'=>$user_data->role_id,'self'=>true); //Self
							$supervisorId[] = $get_emp_id;
						}
						
						$expectSupervisorId = [];
						$is_supervisor = false;
						$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$get_emp_id.'"%\' ');
						// $check_supervisor = User::where('supervisor_id', $get_emp_id);
						
						if($get_emp_id==902){
							$check_supervisor->where('role_id', 21);
						}
						
						if(!empty($request->name)){
							$name = $request->name;
							$check_supervisor->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name);
							});
						}
						
						
						$check_supervisor = $check_supervisor->get();
						
						//$check_supervisor = $check_supervisor->get();
						
						if(count($check_supervisor) > 0){
							$is_supervisor = true;
							$employees = $check_supervisor; // Employees List
							foreach($employees as $key=>$value){
								if(!empty($value)){
									$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name,'role_id'=>$value->role_id,'self'=>false);
									$expectSupervisorId[] = $value->id;
									$supervisorId[] = $value->id;
								}
							}
						}
						if($user_role_id==21){
							if(!empty($request->name)){
								$name=$request->name;
								$usrDepartmentType = User::with('user_details','role')->where('name', 'LIKE', '%' . $name . '%')->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
							}else{
							 $usrDepartmentType = User::with('user_details','role')->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
							}

							if(!empty($usrDepartmentType)){
								foreach($usrDepartmentType as $key=>$value){
									if(!empty($value)){
										if(!in_array($value->id,$supervisorId)){
											$supervisorId[] = $value->id;
											$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name,'role_id'=>$value->role_id,'self'=>false);
										}
									}
									
								}
							}
						}
						
						// echo "<pre>"; print_r($employees_array); die;
						$date_wise_attendance = TaskDetail::groupBy('assigned_date');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_attendance->where('assigned_date', '>=', $request->date_from);
							$date_wise_attendance->where('assigned_date', '<=', $request->date_to);
						}
						else{
							$t_today_date = date('Y-m-d');
							$last_today_date = date('Y-m-d',strtotime('-1 day'));
							// $date_wise_attendance->whereMonth('assigned_date', '=', date('m'))->whereYear('assigned_date', '=', date('Y'));
							$date_wise_attendance->where('assigned_date', '>=', $last_today_date);
							$date_wise_attendance->where('assigned_date', '<=', $t_today_date);
						}
						$date_wise_attendance = $date_wise_attendance->orderBy('assigned_date', 'desc')->get();
						
						$taskArray = array();
						$i = 0;
						if(count($date_wise_attendance) > 0){
							foreach($date_wise_attendance as $key=>$valAtt){
								$date = $valAtt->assigned_date;  
								foreach($employees_array as $key2=>$employeeId){ 
							
									$emp_id = $employeeId['emp_id'];
									$self = $employeeId['self'];
									$task = Task::with(['user','task_details'=>function($query) use ($emp_id, $user_role_id,$date,$self,$is_supervisor,$expectSupervisorId) {
												// $query->where('assigned_userid', $emp_id);
													$query->where('assigned_date', $date);
													if($self==true && $is_supervisor==true){
														$query->whereNotIn('assigned_userid',$expectSupervisorId);
													}
													if($user_role_id==29){ //Super Admin
														$query->orWhere('assigned_userid', 0);
													}
												}
									
											])
											->where('date', $date)
											->where('emp_id', $emp_id)->orWhereRaw('assigned_users LIKE  \'%"'.$emp_id.'"%\' ');
											/* ->where(function($query) use ($emp_id,$self,$is_supervisor) {
												$query->where('emp_id', $emp_id)->orWhereRaw('assigned_users LIKE  \'%"'.$emp_id.'"%\' ');
											}); */
										

										/* $Deleted = "Deleted";
										$task->WhereHas('task_details', function ($q) use ($Deleted) {
											$q->where('status', '!=', $Deleted);
										}); */									
									
									// echo $task->toSql(); die;
									$task = $task->get();
									if(count($task) > 0){
									 
										
										$task_array = array();
										$ii = 0;
										$check_emp_array = true;
										foreach($task as $key=>$value){
											
											if(!empty($value->task_details) && count($value->task_details) > 0){
												$created_by_task =  $value->emp_id;
												$role_id = $value->user->role_id;
												/* $created_task_user = User::where('id', $created_by_task)->first();
												$role_id = null;
												if(!empty($created_task_user)){
													$role_id = $created_task_user->role_id;
												} */
												foreach($value->task_details as $key1=>$taskdetail){
													$check_emp_array = true;
													if(!empty($value->user->role_id) && $value->user->role_id == 20 && $taskdetail->is_emp_delete==1){
														$check_emp_array = false;
													}
													//echo '<pre>'; print_r($check_emp_array);die;
													//if($taskdetail->status!="Deleted"){
													if($check_emp_array){	
														if(($value->emp_id==$emp_id) || ($taskdetail->assigned_userid==$emp_id)){
														//if(array_search($taskdetail->assigned_userid, array_column($employees_array, 'emp_id')) || empty($taskdetail->assigned_userid)){
														
															$taskdetails['task_detail_id'] = $taskdetail->id;
															$taskdetails['task_id'] = $taskdetail->task_id;
															$taskdetails['name'] = $taskdetail->name;
															$taskdetails['plan_hour'] = $taskdetail->plan_hour;
															$taskdetails['spent_hour'] = $taskdetail->spent_hour;
															$taskdetails['status'] = $taskdetail->status;
															$taskdetails['description'] = $taskdetail->description;
															$taskdetails['dropped_reason'] = $taskdetail->dropped_reason;
															$taskdetails['task_date'] = $value->date;
															$taskdetails['role_id'] = $role_id;
															
															$assigned_userid = NULL;
															$assigned_username = NULL;
															if(!empty($taskdetail->assigned_userid)){
																$assigned_userid = $taskdetail->assigned_userid;
																
																$addedname = User::where('id', $taskdetail->assigned_userid)->first();
																if(!empty($addedname)){
																	$assigned_username = $addedname->name;
																}
															}
															$taskdetails['assigned_userid'] = "$assigned_userid";
															$taskdetails['assigned_username'] = $assigned_username;
															
															$task_array[$ii] = $taskdetails;
															$ii++;
														}
														else{
														}
													}
												}
												
											}
											/* else{
												$taskArray[$key]['task_array'] = array();
											} */
										}
										
										if(!empty($task_array)){
											$taskArray[$i]['task_id'] = $value->id;
											if($value->emp_id==$emp_id){
												$taskArray[$i]['emp_id'] = $value->emp_id;
											}
											else{
												$taskArray[$i]['emp_id'] = $emp_id;
											}
											
											//$taskArray[$i]['date'] = $value->date;
											$taskArray[$i]['date'] = $date;
											$taskArray[$i]['task_array'] = $task_array;
											$i++;
										}
										
									}
									/* else{
										return $this->returnResponse(200, false, "Task Not Found");
									} */
								}
							}
							
							$data['tasks']['task_dates'] = $taskArray;
							return $this->returnResponse(200, true, "Task Details", $data);
						}
						else{
							return $this->returnResponse(200, false, "Task Not Found");
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

    public function delete_task(Request $request)
    {
        try{
            $task_detail_id = $request->task_detail_id;
			// $out = http_build_query($_POST,'',', ');
			 
			// file_put_contents('log.txt',"$task_detail_id".PHP_EOL , FILE_APPEND | LOCK_EX);
			
			
            if(isset($task_detail_id) && !empty($task_detail_id)){
					if (is_array($task_detail_id) && !empty($task_detail_id)) {
						
						$check_delete_usr  = Task::with('user','task_details');
					    
						$check_delete_usr->WhereHas('task_details', function ($q) use ($task_detail_id) {
							$q->where('id', '=', $task_detail_id);
						});
						$check_delete_usr = $check_delete_usr->first(); 
						
						if(!empty($check_delete_usr)){
							if(!empty($check_delete_usr->user->role_id) && $check_delete_usr->user->role_id == 20){
								$updateData['is_emp_delete'] = 1;
							}
						}
						$updateData['status'] = "Deleted";
						TaskDetail::whereIn('id', $task_detail_id)->update($updateData);
						
						//TaskDetail::whereIn('parent_task_detail_id', $task_detail_id)->delete();
						
						
						return $this->returnResponse(200, true, "Delete Successfully");
					}
					else{
						return $this->returnResponse(200, true, "Task ids Empty.");
					}
            }else{
                return $this->returnResponse(200, false, "Task ids is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
    public function new_add_task(Request $request)
    {
       try{
            $assign_id = $request->assign_id;
            if(isset($assign_id) && !empty($assign_id)){
	            $task_array = $request->task_array;
			    foreach ($task_array as $key => $value) {
					if(!empty($value)){
						$data = array();
						$data['title']=$data['plan']=$data['description']=$data['emp_id']="";
						
						$data['assign_id']=$assign_id;
						$data['date']=date('Y-m-d');

						if(!empty($value['emp_id'])){
						  $data['emp_id'] = $value['emp_id'];
						}else{
						  $data['emp_id'] = $assign_id;
						}
						
						if(!empty($value['title'])){
							$data['title'] = $value['title'];
						}

						if(!empty($value['date'])){
							$data['date'] = date("Y-m-d",strtotime($value['date']));
						}

						if(!empty($value['plan'])){
							$data['plan'] = $value['plan'];
						}

						if(!empty($value['description'])){
							$data['description'] = $value['description'];
						}

						DB::table('task_new')->insert($data);
					}
				}
			  
			    return $this->returnResponse(200, true, "Task Add Successfully");
			}else{
				return $this->returnResponse(200, true, "Task Empty.");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

    public function new_update_task(Request $request)
    {
       try{
            if(isset($request->assign_id) && !empty($request->assign_id) && isset($request->task_array)){
            	$assign_id = $request->assign_id;
	            $task_array = $request->task_array;
			    foreach ($task_array as $key => $value) {
					if(!empty($value) && isset($value['task_id']) && !empty($value['task_id'])){
					
						$data = array();
						
						if(isset($value['emp_id']) && !empty($value['emp_id'])){
						  $data['emp_id'] = $value['emp_id'];
						}
						
						if(isset($value['title']) && !empty($value['title'])){
							$data['title'] = $value['title'];
						}

						if(isset($value['date']) && !empty($value['date'])){
							$data['date'] = date("Y-m-d",strtotime($value['date']));
						}

						if(isset($value['plan']) && !empty($value['plan'])){
							$data['plan'] = $value['plan'];
						}

						if(isset($value['description']) && !empty($value['description'])){
							$data['description'] = $value['description'];
						}

						if(isset($value['spent']) && !empty($value['spent'])){
							$data['spent'] = $value['spent'];
						}

						if(isset($value['status']) && !empty($value['status'])){
							$data['status'] = $value['status'];
						}

						if(isset($value['remark']) && !empty($value['remark'])){
							$data['remark'] = $value['remark'];
						}

						$task_id=$value['task_id'];
						
						$whereCond="id=".$task_id." AND (assign_id=".$assign_id." OR emp_id=".$assign_id.")";

						$taskUpdate=DB::table('task_new')->where('id', $task_id)->whereRaw($whereCond)->update($data);
					}else{
			          return $this->returnResponse(200, false, "Task id is empty");
					}
				}

				if($taskUpdate){
                  return $this->returnResponse(200, true, "Task Updated Successfully");
				}else{
	              return $this->returnResponse(200, false, "Task id is invalid.");
				}
			}else{
				return $this->returnResponse(200, true, "Task Empty.");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

    public function new_delete_task(Request $request)
    { 
        try{
            if(isset($request->assign_id) && !empty($request->assign_id) && isset($request->task_array)){
                $assign_id = $request->assign_id;
	            $task_array = $request->task_array;
			    foreach ($task_array as $key => $value) {
					if(!empty($value) && isset($value['task_id']) && !empty($value['task_id'])){
						$data = array();
						$data['remark']="Deleted By".$assign_id;
						$data['status'] = "Deleted";

						$task_id=$value['task_id'];
						
						$whereCond="id=".$task_id." AND assign_id=".$assign_id;

						$taskDeleted=DB::table('task_new')->where('id', $task_id)->whereRaw($whereCond)->update($data);
						if($taskDeleted){
			                return $this->returnResponse(200, true, "Task Deleted Successfully");
						}else{
							 return $this->returnResponse(200, true, "Task id is invalid");
						}
					}else{
			          return $this->returnResponse(200, false, "Task id is empty");
					}
				}
			}else{
				return $this->returnResponse(200, true, "Task Empty.");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

    public function new_get_task(Request $request)
    {
        try{
            $get_emp_id = $request->emp_id;
            if(isset($get_emp_id) && !empty($get_emp_id)){
				$user_check = User::where('id', $get_emp_id);
				$user = $user_check->first();
				if(!empty($user)){
				    $department_type = $user->department_type;
				    $user_role_id = $user->role_id;
					//self
					$supervisorId[] = $get_emp_id;
	                //under employee
					$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$get_emp_id.'"%\' ')->where('status',1)->where('is_deleted','0');
					if($get_emp_id==902 || $user_role_id==29 || $user_role_id==30){
						$check_supervisor->where('role_id', 21);
					}
					
					if(!empty($request->name)){
						$name = $request->name;
						$check_supervisor->where(function($query) use ($name) {
							$query->where('name', 'LIKE', '%' . $name . '%')
							->orWhere('register_id', 'LIKE', '%' . $name);
						});
					}
					
					$check_supervisor = $check_supervisor->select('id','role_id','name')->get();

					if($user_role_id==21){
						$usrDepartmentType = User::where('status',1)->where('is_deleted','0')->where('department_type',$department_type);
						if(!empty($request->name)){
							$name = $request->name;
							$usrDepartmentType->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name);
							});
						}

						$usrDepartmentType=$usrDepartmentType->select('id','role_id','name')->get();

						$merged=$check_supervisor->merge($usrDepartmentType);
					    $check_supervisor = $merged->all();
					}

					if(count($check_supervisor)>0){
						foreach($check_supervisor as $key=>$value){
							if(!empty($value)){
								$supervisorId[] = $value->id;
							}
						}
					}
	                
	                $whereCond =	"1 =1 ";
	                if(!empty($request->date_from) && !empty($request->date_to)){
	                  $whereCond .= " AND date >= '". $request->date_from."' AND date <= '". $request->date_to."'";
	                }else{
		              $whereCond .= " AND date = '".date('Y-m-d')."'";
	                }
	                
	                $date_wise=DB::table('task_new')
	                          ->select('task_new.*','assigned_by.name as assigned_by','users.name','users.role_id')
	                          ->leftJoin('users', function($join) { $join->on('users.id', '=', 'task_new.emp_id');})
	                          ->leftJoin('users as assigned_by', function($join) { $join->on('assigned_by.id', '=', 'task_new.assign_id');})
	                          ->where("task_new.status","!=","Deleted")
	                          ->whereIN('emp_id',$supervisorId)
	                          ->whereRaw($whereCond)
	                          ->orderBy('date', 'desc')
	                          ->get();
	                $taskArray = array();
					$i = 0;
					if(count($date_wise) > 0){
						foreach($date_wise as $key=>$val){
							//$val->date=date('d-m-Y',strtotime($val->date));
							$taskArray[]=$val;
						}
						$data['tasks']= $taskArray;
						return $this->returnResponse(200, true, "Task Details", $data);
					}else{
						return $this->returnResponse(200, false, "Task Not Found");
					}
				}else{
					return $this->returnResponse(200, false, "User Id Not Found"); 
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }          
        }catch(\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch(ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

	public function task_history(Request $request){
		try{
			$name = $request->name;
			$date_from = $request->date_from;
			$date_to = $request->date_to;
            $emp_id = $request->emp_id;
            if(isset($emp_id) && !empty($emp_id)){
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){
						$user_role_id       = $user->role_id;
						$employees_array    = array();	
						
						if(!empty($name)){
							$user_check->where('name', 'LIKE', '%' . $name . '%')->orWhere('register_id', 'LIKE', '%' . $name);
						}
						$user_data =  $user_check->first();
						if(!empty($user_data)){
							$employees_array[] = array('emp_id'=>$emp_id,'name'=>$user_data->name,'role_id'=>$user_data->role_id,'self'=>true); 
						}
						
						
						$expectSupervisorId = [];
						$is_supervisor      = false;
						if($user->role_id==29){
							$check_supervisor  = array();
							if(!empty($name)){
								$check_supervisor = User::where('name', 'LIKE', '%' . $name . '%')->orWhere('register_id', 'LIKE', '%' . $name);
								$check_supervisor = $check_supervisor->get();
							}
						}
						else{
							$check_supervisor  = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ');
							if(!empty($name)){
								$check_supervisor->where('name', 'LIKE', '%' . $name . '%')->orWhere('register_id', 'LIKE', '%' . $name);
							}
							
							$check_supervisor = $check_supervisor->get();
						}
						
						
						if(count($check_supervisor) > 0){
							$is_supervisor = true;
							$employees     = $check_supervisor; 
							foreach($employees as $key=>$value){
								if(!empty($value)){
									$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name,'role_id'=>$value->role_id,'self'=>false);
									$expectSupervisorId[] = $value->id;
								}
							}
						} 
						// echo $is_supervisor; die;
						//$date_wise_attendance = NewTask::groupBy('task_date')->whereMonth('task_date', '=', date('m'))->orderBy('task_date', 'desc')->get();
						
						$date_wise_attendance = NewTask::groupBy('task_date');
						if(!empty($date_from) && !empty($date_to)){
							$date_wise_attendance->where('task_date', '>=', $date_from);
							$date_wise_attendance->where('task_date', '<=', $date_to);
						}
						else{
							$date_wise_attendance->whereMonth('task_date', '=', date('m'));
						}
						
						$date_wise_attendance = $date_wise_attendance->orderBy('task_date', 'desc')->get();
						
						$taskArray = array();
						$i = 0;
						// echo '<pre>'; print_r($employees_array);die;
						if(count($date_wise_attendance) > 0){ 
							foreach($date_wise_attendance as $key=>$valAtt){ 
								$date = $valAtt->task_date; 
								foreach($employees_array as $key2=>$employeeId){
									$emp_id = $employeeId['emp_id'];
									$self   = $employeeId['self'];
									
									$task   = NewTask::select('*');
									$task->where('task_date', $date);
									if($self==true && $is_supervisor==true){
										$task->whereNotIn('task_added_to',$expectSupervisorId);
									}
									if($user->role_id==29){
										// $task->where('task_added_to', $emp_id)->orWhere('task_added_to', 0);
										$task->where(function($query) use ($emp_id) {
											$query->where('task_added_to', 0)->orWhere('task_added_by', $emp_id);
										});
									}
									else{
										// $task->where('task_added_to', $emp_id)->orWhere('task_added_by', $emp_id);
										$task->where(function($query) use ($emp_id) {
											$query->where('task_added_to', $emp_id)->orWhere('task_added_by', $emp_id);
										});
									}
									
									
									$Deleted = "Deleted";
									$task->where('status', '!=', $Deleted);
									
									$task = $task->get();
									
									if(count($task) > 0){
										$task_array = array();
										$ii = 0;
										foreach($task as $key=>$value){
											$created_by_task =  $value->task_added_by;
											
											$created_task_user = User::where('id', $created_by_task)->first();
											$role_id = null;
											if(!empty($created_task_user)){
												$role_id = $created_task_user->role_id;
											}
											if($value->status!="Deleted"){
												
												$taskdetails['task_id']        = $value->id;
												$taskdetails['task_title']     = $value->task_title;
												$taskdetails['plan_hour']      = $value->plan_hour;
												$taskdetails['spent_hour']     = $value->spent_hour;
												$taskdetails['status']         = $value->status;
												$taskdetails['description']    = $value->task_description;
												$taskdetails['dropped_reason'] = $value->dropped_reason;
												$taskdetails['task_date']      = $value->date;
												$taskdetails['role_id']        = $role_id;
												$taskdetails['task_file_link'] = url('/').'/laravel/public/'.$value->task_file_link;
												
												$assigned_userid = NULL;
												$assigned_username = NULL;
												
												if(!empty($value->task_added_to)){
													$assigned_userid = $value->task_added_to;
													
													$addedname = User::where('id', $assigned_userid)->first();
													if(!empty($addedname)){
														$assigned_username = $addedname->name;
													}
												}
												
												$taskdetails['assigned_userid'] = "$assigned_userid";
												$taskdetails['assigned_username'] = $assigned_username;
												
												$task_array[$ii] = $taskdetails;
												$ii++;
															
											}
										}
										if(!empty($task_array)){
											$taskArray[$i]['task_id'] = $value->id;
											if($value->emp_id==$emp_id){
												$taskArray[$i]['emp_id'] = $value->emp_id;
											}
											else{
												$taskArray[$i]['emp_id'] = $emp_id;
											}
											
											$taskArray[$i]['date'] = $value->task_date;
											$taskArray[$i]['task_array'] = $task_array;
											$i++;
										}
									}
									
								}
								
							}
							$data['tasks']['task_dates'] = $taskArray;
							return $this->returnResponse(200, true, "Task Details", $data);
						}
						else{
							return $this->returnResponse(200, false, "Task Not Found");
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

	public function task_history_detail(Request $request){
		try{
            $task_id = $request->task_id;

            if(isset($task_id) && !empty($task_id)){
				$task_history_details_response = TaskHistory::where('task_id', $task_id)->get();

				if(count($task_history_details_response) > 0){
					$total_task_history_detail = [];

					foreach ($task_history_details_response as $history) { 
						$temp['id']               = $history->id; 
						$temp['task_id']          = $history->task_id;
						$temp['task_date']        = $history->task_date;   
						$temp['task_title']       = $history->task_title; 
						$temp['task_description'] = $history->task_description; 
						$temp['task_added_by']    = $history->task_added_by; 
						$temp['task_added_to']    = $history->task_added_to; 
						$temp['plan_hour']        = $history->plan_hour; 
						$temp['spent_hour']       = $history->spent_hour; 
						$temp['parent_id']        = $history->parent_id; 
						$temp['task_type']        = $history->task_type; 
						$temp['task_file_link']   = $history->task_file_link; 
						$temp['task_priority']    = $history->task_priority; 
						$temp['status']           = $history->status; 
						$temp['remark']           = $history->remark; 
						$total_task_history_detail[]     = $temp; 
					}

					$data['history_detail'] = $total_task_history_detail;
					return $this->returnResponse(200, true, "Total Task History Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Task History Details Not Found");
				} 
					
            }else{
                return $this->returnResponse(200, false, "Task id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
	}
	
	public function get_open_task(Request $request)
    {
        try{
					
			$task = Task::with(['task_details','user'])->orderBy('date', 'desc');
			if(!empty($request->date_from) && !empty($request->date_to)){
				$task->where('date', '>=', $request->date_from);
				$task->where('date', '<=', $request->date_to);
			}
			else{
				$task->whereMonth('date', '=', date('m'));
			}
			$Deleted = "Deleted";
			$task->WhereHas('task_details', function ($q) use ($Deleted) {
				$q->where('assigned_userid',  0);
				$q->where('status', '!=', $Deleted);
			});
			// echo $task->toSql(); die;
			$task = $task->get();
			// echo "<pre>";
			// print_R($task); die;
			if(!empty($task)){
				$task_array = array();
				$i = 0;
				$ii = 0;
				foreach($task as $key=>$value){
					$created_by_task =  $value->emp_id;
					$created_task_user = User::where('id', $created_by_task)->first();
					$role_id = null;
					if(!empty($created_task_user)){
						$role_id = $created_task_user->role_id;
					}
					
					// $role_id = isset($value->user->role_id)?$value->user->role_id:'';
					if(!empty($value->task_details) && count($value->task_details) > 0){
						foreach($value->task_details as $key1=>$taskdetail){
							if($taskdetail->status!="Deleted"){
								if($taskdetail->assigned_userid==0){
									$taskdetails['task_detail_id'] = $taskdetail->id;
									$taskdetails['task_id'] = $taskdetail->task_id;
									$taskdetails['name'] = $taskdetail->name;
									$taskdetails['plan_hour'] = $taskdetail->plan_hour;
									$taskdetails['spent_hour'] = $taskdetail->spent_hour;
									$taskdetails['status'] = $taskdetail->status;
									$taskdetails['description'] = $taskdetail->description;
									$taskdetails['dropped_reason'] = $taskdetail->dropped_reason;
									$taskdetails['task_date'] = $value->date;
									
									$assigned_userid = NULL;
									$assigned_username = NULL;
									if(!empty($taskdetail->assigned_userid)){
										$assigned_userid = $taskdetail->assigned_userid;
										
										$addedname = User::where('id', $taskdetail->assigned_userid)->first();
										if(!empty($addedname)){
											$assigned_username = $addedname->name;
										}
									}
									$taskdetails['assigned_userid'] = "$assigned_userid";
									$taskdetails['assigned_username'] = $assigned_username;
									$taskdetails['role_id'] = $role_id;
									
									$task_array[$ii] = $taskdetails;
									$ii++;
								}
							}
						}
					}
				}
				$data['tasks']['task_array'] = $task_array;

				return $this->returnResponse(200, true, "Open Task Details", $data);
			}else{
				return $this->returnResponse(200, false, "Open Task Not Found");
			}        
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	function mime2ext($mime){
		$all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
		"image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
		"image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
		"application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
		"image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
		"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
		"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
		"video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
		"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
		"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
		"application\/x-jar"],"zip":["application\/x-zip","application\/zip",
		"application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
		"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
		"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
		"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
		"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
		"pdf":["application\/pdf","application\/octet-stream"],
		"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
		"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
		"application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
		"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
		"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
		"application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
		"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
		"video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
		"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
		"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
		"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
		"image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
		"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
		"application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
		"application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
		"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
		"application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
		"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
		"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
		"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
		"application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
		"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
		"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
		"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
		"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
		"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
		"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
		"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
		$all_mimes = json_decode($all_mimes,true);
		foreach ($all_mimes as $key => $value) {
			if(array_search($mime,$value) !== false) return $key;
		}
		return false;
	}
}
