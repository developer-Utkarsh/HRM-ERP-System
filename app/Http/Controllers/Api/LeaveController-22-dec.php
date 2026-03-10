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
				// print_r($user->user_details); die;
				if(!empty($user)){
					if($user->status == '1'){
						if (is_array($request->leave_array) && !empty($request->leave_array)) {
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
											$allTypes[] = $leaveVal->type;
										}
										
										if(!in_array('Full Day',$allTypes)){
											$type = $value['type'];
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
							if($resResult){
								return $this->returnResponse(200, true, "Leave Add Successfully");
							}
							else{
								return $this->returnResponse(200, false, "Already Leave Request Send.");
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

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$employees_array = array();
						
						$employees_array[] = array('emp_id'=>$emp_id,'name'=>$user->name); //Self
						
						$is_supervisor = false;
						$check_supervisor = User::where('supervisor_id', $emp_id)->get();
						// $check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
						if(count($check_supervisor) > 0){
							$is_supervisor = true;
							$employees = $check_supervisor; // Employees List
							foreach($employees as $key=>$value){
								if(!empty($value)){
									$employees_array[] = array('emp_id'=>$value->id,'name'=>$value->name);
								}
							}
						}
						$leaveArray = array();
						$i = 0;
						foreach($employees_array as $key2=>$employeeId){
							$leave = Leave::with(['user','leave_details'])->where('emp_id', $employeeId['emp_id']);
							if(!empty($request->date_from) && !empty($request->date_to)){
								$date_from = $request->date_from;
								$date_to = $request->date_to;
								$leave->WhereHas('leave_details', function ($q) use ($date_from,$date_to) {
									$q->where('date', '>=', $date_from);
									$q->where('date', '<=', $date_to);
								});
							}
							
							$leave = $leave->get();
							// echo "<pre>";print_r($leave); die;
							if(!empty($leave)){
								$ii = 0;
								$leave_array = array();
								foreach($leave as $key=>$value){
									if(!empty($value->leave_details) && count($value->leave_details) > 0){
										foreach($value->leave_details as $key1=>$leavedetail){
											$leavedetails['emp_id'] = $value->emp_id;
											$leavedetails['leave_id'] = $value->id;
											$leavedetails['reason'] = $value->reason;
											$leavedetails['leave_detail_id'] = $leavedetail->id;
											$leavedetails['date'] = $leavedetail->date;
											$leavedetails['type'] = $leavedetail->type;
											$leavedetails['status'] = $leavedetail->status;
											
											$leave_array[$ii] = $leavedetails;
											$ii++;
										}
									}
								}
								
								if(!empty($leave_array)){
									$leaveArray[$i]['emp_id'] = $value->emp_id;
									$leaveArray[$i]['name'] = $value->user->name;
									$leaveArray[$i]['leave_array'] = $leave_array;
									$i++;
								}
							}
						}
						if(!empty($leaveArray)){
							$data['leave'] = $leaveArray;
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
					$resResult = false;
					if(!empty($request->date)){
						$leave_details = LeaveDetail::where('date',$request->date)->where('leave_id',$leave_id)->where('id','!=',$leave_detail_id)->get();
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
								$allTypes[] = $leaveVal->type;
							}
							
							if(!in_array('Full Day',$allTypes)){
								$leave_checked = LeaveDetail::where('date',$request->date)->where('leave_id',$leave_id)->where('type',$request->type)->get();												
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
						if($resResult){
							return $this->returnResponse(200, true, "Leave Updated Successfully");
						}
						else{
							return $this->returnResponse(200, false, "Already Leave Exists.");
						}
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
