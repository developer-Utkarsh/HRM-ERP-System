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

class LeaveController extends Controller
{
	
	public function add_leave(Request $request)
    {
        try{
            $emp_id = $request->emp_id;
            // $assigned_userid = $request->assigned_userid;

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				 
				if(!empty($user)){
					if($user->status == '1'){
						if (is_array($request->leave_array) && !empty($request->leave_array)) {
							$checkLeave = true;
							foreach($request->leave_array as $leaveRow){
								$before_30_days = strtotime('-30 days',strtotime(date('Y-m-d')));
							    if(strtotime($leaveRow['date']) < $before_30_days){
									$checkLeave = false;
							    }
							}
							
						if($checkLeave){
							$inputs['emp_id'] = $emp_id;
							$inputs['reason'] = $request->reason;;
							$leaveSave = Leave::create($inputs);
							
							$resResult = false;
							$resMessage = "";
							$leave_array = $request->leave_array;
							foreach ($leave_array as $key => $value) {
								if(!empty($value)){
									// $leave_details = LeaveDetail::where('date',$value['date'])->get();
									$date = $value['date'];
									$leave_details = Leave::with(['leave_details'])->where('emp_id', $emp_id);
									$leave_details->WhereHas('leave_details', function ($q) use ($date) {
										$q->where('date', $date);
									});									
									$leave_details = $leave_details->get();
									// echo count($leave_details);
									if(count($leave_details)==0){
										$data = array();
										if(!empty($value['date'])){
											$data['date'] = $value['date'];
										}
										if(!empty($value['type'])){
											$data['type'] = $value['type'];
										}
										if(!empty($data)){
											$resResult = true;
											$data['leave_id'] = $leaveSave->id;
											LeaveDetail::create($data);
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
											$type = $value['type'];
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
												$leave_checked->WhereHas('leave_details', function ($q) use ($date,$type) {
													$q->where('date', $date);
													$q->where('type', $type);
												});
												$leave_checked = $leave_checked->get();
												// $leave_checked = LeaveDetail::where('date',$value['date'])->where('type',$value['type'])->first();												
												if(count($leave_checked)==0){
													$data = array();
													if(!empty($value['date'])){
														$data['date'] = $value['date'];
													}
													if(!empty($value['type'])){
														$data['type'] = $value['type'];
													}
													if(!empty($data)){
														$resResult = true;
														$data['leave_id'] = $leaveSave->id;
														LeaveDetail::create($data);
													}
												}
											}
										}
									}
								}
							}
							if($resResult){
								return $this->returnResponse(200, true, "Leave Add Successfully");
							}
							else{
								return $this->returnResponse(200, false, "Already Leave Request Send.");
							}

						}
						else{
							return $this->returnResponse(200, false, "You wouldn't applied before 30 day leave.");
						
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
							// $employees_array[] = array('emp_id'=>$emp_id,'name'=>$user_data->name); //Self	
							$employees_array[] = $emp_id; //Self	
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
										// $employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
										$employees_array[] = $value->id;
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
										// $employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
										$employees_array[] = $value->id;
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
							if($user->role_id==24){
								$date_wise_leave->where('date','>=', date('Y-m-d'));
								$date_wise_leave->where('date','<=', date('Y-m-d',strtotime('+7 days')));
							}
							else{
								$date_wise_leave->where('date', date('Y-m-d'));
							}
						}
						
						$date_wise_leave = $date_wise_leave->get();
						if(count($date_wise_leave) > 0){
							$ii = 0;
							foreach($date_wise_leave as $key=>$valAtt){
								$date = $valAtt->date;
								$i = 0;
								$employee_leave = array();
								$leave_array = array();
								// foreach($employees_array as $key2=>$employeeId){
									
									$get_leave = Leave::with(['user',
											'leave_details'=>function ($q) use ($date) {
												$q->where('date', $date);
											}
										])
										// ->where('emp_id', $employeeId['emp_id']);
										->whereIn('emp_id', $employees_array);
									/* $get_leave->WhereHas('leave_details', function ($q) use ($date) {
										$q->where('date', $date);
									}); */
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
													$leavedetails['date'] = $leavedetail->date;
													$leavedetails['type'] = $leavedetail->type;
													$leavedetails['status'] = $leavedetail->status;
													
													$leave_array[$i] = $leavedetails;
													$i++;
												}
											}
										}
									}
								// }
								
								if(!empty($leave_array)){
									$responseArray[$ii]['date'] = $valAtt->date;
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
						//if(strtotime($request->date) >= strtotime(date('Y-m-d'))){
							$before_30_days = strtotime('-30 days',strtotime(date('Y-m-d')));
							if(strtotime($request->date) >= $before_30_days){
								$date = $request->date;
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
									if(!empty($request->date)){
										$data['date'] = $request->date;
									}
									if(!empty($request->type)){
										$data['type'] = $request->type;
									}
									if(!empty($request->status)){
										$data['status'] = $request->status;
									}
									$find_leave_details->update($data);
									if(!empty($request->reason)){
										$leave_update = Leave::find($leave_id);
										$leave_update->update(array('reason'=>$request->reason));
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
												if(!empty($request->date)){
													$data['date'] = $request->date;
												}
												if(!empty($request->type)){
													$data['type'] = $request->type;
												}
												if(!empty($request->status)){
													$data['status'] = $request->status;
												}
												$find_leave_details->update($data);
												if(!empty($request->reason)){
													$leave_update = Leave::find($leave_id);
													$leave_update->update(array('reason'=>$request->reason));
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
}
