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
use App\TaskDetail;

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
										if(!empty($data)){
											if(!empty($value['task_detail_id'])){
												$task_detail_id = $value['task_detail_id'];
												$data['assigned_userid'] = $value['assigned_userid'];
												$task_details = TaskDetail::find($task_detail_id);
												$task_details->update($data);
												if(!empty($value['assigned_userid'])){
													$assigned_userid[] = $value['assigned_userid'];
													$task_update = Task::find($task_details->task_id);
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
												}
											}
											else{
												if(!empty($value['assigned_userid'])){
													$data['assigned_userid'] = $value['assigned_userid'];
													$assigned_users[] = $value['assigned_userid'];
												}
												
												$data['task_id'] = $check_task->id;
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
							// echo "<pre>"; print_r($request->task_array); die;
							if (is_array($request->task_array) && !empty($request->task_array)) {
								$task_array = $request->task_array;
								$assigned_users = array();
								foreach ($task_array as $key => $value) {
									if(!empty($value)){
										$task_detail_id = $value['task_detail_id'];
										$task_details = TaskDetail::find($task_detail_id);
										
										$data = array();
										if(!empty($value['name'])){
											$data['name'] = $value['name'];
										}
										if(!empty($value['plan_hour'])){
											$data['plan_hour'] = $value['plan_hour'];
										}
										if(!empty($value['spent_hour'])){
											$data['spent_hour'] = $value['spent_hour'];
										}
										if(!empty($value['status'])){
											$data['status'] = $value['status'];
										}
										if(!empty($value['description'])){
											$data['description'] = $value['description'];
										}
										if(!empty($value['assigned_userid'])){
											$data['assigned_userid'] = $value['assigned_userid'];
											$assigned_users[] = $value['assigned_userid'];
										}
										$task_details->update($data);
										
										
										if(!empty($value['status'])){
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
															$data['parent_task_detail_id'] = $task_detail_id;
															$data['task_id'] = $next_day_task->id;
															$data['plan_hour'] = 0;
															TaskDetail::create($data);
														}
													}
												}
												else{
													$inputs['emp_id'] = $emp_id;
													$inputs['date'] = $next_day_date;
													$nextDatTaskSave = Task::create($inputs);
													if($nextDatTaskSave){
														$data['parent_task_detail_id'] = $task_detail_id;
														$data['task_id'] = $nextDatTaskSave->id;
														$data['plan_hour'] = 0;
														TaskDetail::create($data);
													}
												}
											}
											else{
												$next_day_task = Task::where('emp_id', $emp_id)->where('date', $next_day_date)->first();
												if(!empty($next_day_task)){
													TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->delete();
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

            $emp_id = $request->emp_id;

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$task = Task::with(['task_details'])->where(function($query) use ($emp_id) {
							$query->where('emp_id', $emp_id)
							// ->orWhereRaw('JSON_CONTAINS(assigned_users, \'["'.$emp_id.'"]\')');
							->orWhereRaw('assigned_users LIKE  \'%"'.$emp_id.'"%\' ');
							})->orderBy('date', 'desc');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$task->where('date', '>=', $request->date_from);
							$task->where('date', '<=', $request->date_to);
						}
						else{
							$task->whereMonth('date', '=', date('m'));
						}
						$Deleted = "Deleted";
						$task->WhereHas('task_details', function ($q) use ($Deleted) {
							$q->where('status', '!=', $Deleted);
						});
						// echo $task->toSql(); die;
						$task = $task->get();
						// echo "<pre>";
						// print_R($task); die;
						if(!empty($task)){
							$taskArray = array();
							$i = 0;
							foreach($task as $key=>$value){
								
								if(!empty($value->task_details) && count($value->task_details) > 0){
									$task_array = array();
									$ii = 0;
									foreach($value->task_details as $key1=>$taskdetail){
										if($taskdetail->status!="Deleted"){
											if(($value->emp_id==$emp_id) || ($taskdetail->assigned_userid==$emp_id)){
												$taskdetails['task_detail_id'] = $taskdetail->id;
												$taskdetails['task_id'] = $taskdetail->task_id;
												$taskdetails['name'] = $taskdetail->name;
												$taskdetails['plan_hour'] = $taskdetail->plan_hour;
												$taskdetails['spent_hour'] = $taskdetail->spent_hour;
												$taskdetails['status'] = $taskdetail->status;
												$taskdetails['description'] = $taskdetail->description;
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
												
												$task_array[$ii] = $taskdetails;
												$ii++;
											}
											else{
											}
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
										
										$taskArray[$i]['date'] = $value->date;
										$taskArray[$i]['task_array'] = $task_array;
										$i++;
									}
								}
								/* else{
									$taskArray[$key]['task_array'] = array();
								} */
							}
							$data['tasks']['task_dates'] = $taskArray;

							return $this->returnResponse(200, true, "Task Details", $data);
						}else{
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

            if(isset($task_detail_id) && !empty($task_detail_id)){
					if (is_array($task_detail_id) && !empty($task_detail_id)) {
						$updateData['status'] = "Deleted";
						TaskDetail::whereIn('id', $task_detail_id)->update($updateData);
						
						TaskDetail::whereIn('parent_task_detail_id', $task_detail_id)->delete();
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
	
	public function get_open_task(Request $request)
    {
        try{
					
			$task = Task::with(['task_details'])->orderBy('date', 'desc');
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
}
