<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use App\User;
use App\StaffMovementSystem;
use DateTime;

class StaffmovementsystemController extends Controller
{
    public function add_staff_movement_system(Request $request)
    {
    	try { 

    		$emp_id = $request->emp_id;
    		$from_time = $request->from_time;
    		$to_time = $request->to_time;
    		$reason = $request->reason;
    		$type = $request->type;
			if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$inputs = $request->only('emp_id','from_time','to_time','reason','type');
						
						$inputs['cdate'] = date('Y-m-d');
						if($type==1){
						if($from_time >= date('H:i') && $to_time > $from_time){
							$save = StaffMovementSystem::create($inputs);
							if($save->save()){
								return $this->returnResponse(200, true, "SMS Saved Successfully");
							}else{
								return $this->returnResponse(200, false, "Something went wrong.");
							}
						}
						else{
							return $this->returnResponse(200, false, "Start Time should be greater than current time and End Time should be greater than Start Time.");
						}}else{
                            $save = StaffMovementSystem::create($inputs);
							if($save->save()){
								return $this->returnResponse(200, true, "SMS Saved Successfully");
							}else{
								return $this->returnResponse(200, false, "Something went wrong.");
							}
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
				return $this->returnResponse(200, false, "Employee Id Required Found");
			}

    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }
	
	public function update_staff_movement_system(Request $request)
    {
    	try {

    		$id = $request->id;
    		$emp_id = $request->emp_id;
    		$from_time = $request->from_time;
    		$to_time = $request->to_time;
    		$reason = $request->reason;
    		$status = $request->status;
			if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						if(!empty($id)){
							$staff = StaffMovementSystem::find($id);
							if(!empty($staff)){
								if(!empty($emp_id)){
									$update_data['emp_id'] = $emp_id;
								}
								if(!empty($from_time)){
									$update_data['from_time'] = $from_time;
								}
								if(!empty($to_time)){
									$update_data['to_time'] = $to_time;
								}
								if(!empty($reason)){
									$update_data['reason'] = $reason;
								}
								if(!empty($status)){
									$update_data['status'] = $status;
								}
								if(!empty($update_data)){
									$update = $staff->update($update_data);
									if($update){
										return $this->returnResponse(200, true, "SMS updated successfully");
									}
									else{
										return $this->returnResponse(200, false, "Staff Movement System not update. Please try again.");
									}
								}
								else{
									return $this->returnResponse(200, false, "Staff Movement System not update. Please try again.");
								}								
								
							}
							else{
								return $this->returnResponse(200, false, "Id is not valid.");
							}
							
						}
						else{
							return $this->returnResponse(200, false, "Something went wrong 2.");
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
				return $this->returnResponse(200, false, "Employee Id Required Found");
			}

    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }
	
	public function get_staff_movement_system(Request $request)
    {
        try{

            $emp_id  = $request->emp_id;
			
            if(isset($emp_id) && !empty($emp_id)){
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){						
						$responseArray = array();
						$i = 0; 
						$report = StaffMovementSystem::with('employee')->where('status','!=','Deleted')->orderBy('cdate', 'desc');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$report->where('cdate', '>=', $request->date_from);
							$report->where('cdate', '<=', $request->date_to);
						}
						else{
							$report->where('cdate', date('Y-m-d'));
						}
						// $report->whereIn('emp_id', $logid);
						$report = $report->get();
						 //echo count($report); die;
						if(count($report) > 0){
							foreach($report as $key=>$value){
								$report_array = array(); 
								if(!empty($value)){
									
									$my_emp = 'No';
									if($user->role_id==29){
										$check_my_emp = User::whereRaw('id = "'.$value->emp_id.'" and supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->first();
										if(!empty($check_my_emp)){
											$my_emp = 'Yes';
										}
									}
									else if($user->role_id==21){
										if(isset($value->employee)){
											if($user->department_type==$value->employee->department_type){
												$my_emp = 'Yes';
											}
										}
									}
									
									//echo '<pre>'; print_r($check_my_emp);die;
									$reportdetails['id'] = $value->id;
									$reportdetails['emp_id'] = $value->emp_id;
									$reportdetails['emp_name'] = isset($value->employee->name)?$value->employee->name:'';
									$reportdetails['from_time'] = $value->from_time;
									$reportdetails['to_time'] = $value->to_time;
									$reportdetails['reason'] = $value->reason;
									$reportdetails['type'] = $value->type;
									$reportdetails['status'] = $value->status;
									$reportdetails['cdate'] = $value->cdate;
									$reportdetails['approved_by'] = $value->approved_by;
									$reportdetails['my_employee'] = $my_emp;
									
									$responseArray[$i] = $reportdetails;
									$i++;
								}
							}
							
						}
						
						if(!empty($responseArray)){
							$data['data'] = $responseArray;
							return $this->returnResponse(200, true, "Staff Movement System Details", $data);
						}
						else{
							return $this->returnResponse(200, false, "Staff Movement System Not Found");
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
	
	public function delete_staff_movement_system(Request $request)
    {
        try{
            $id = $request->id;

            if(isset($id) && !empty($id)){ 
				$find_id = StaffMovementSystem::find($id);
				if(!empty($find_id)){
					// StaffMovementSystem::where('id', $id)->delete();
					$updateData['status'] = "Deleted";
					StaffMovementSystem::where('id', $id)->update($updateData);
					
					return $this->returnResponse(200, true, "Delete Successfully");
				}
				else{
					return $this->returnResponse(200, false, "Staff Movement System Id invalid");  
				}
						 
            }else{
                return $this->returnResponse(200, false, "Staff Movement System Id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
}
