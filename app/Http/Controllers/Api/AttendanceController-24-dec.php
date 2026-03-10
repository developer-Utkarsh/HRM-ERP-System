<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use App\User;
use App\Attendance;

class AttendanceController extends Controller
{
    public function add_attendance(Request $request)
    {
    	try {

    		$emp_id = $request->emp_id;
    		$type = $request->type;

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
						if (Input::hasfile('image')){
							$inputs['image'] = $this->uploadImage(Input::file('image'),$emp_id);
						}
						
						$inputs['emp_id'] = $emp_id;
						$inputs['date'] = date('Y-m-d');
						$inputs['time'] = date('H:i');
						$attendance = Attendance::create($inputs);
						if($attendance->save()){
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
								->orWhere('register_id', 'LIKE', '%' . $name);
							});
						}
						$user_data =  $user_check->first();
						if(!empty($user_data)){
							$employees_array[] = array('emp_id'=>$emp_id,'name'=>$user_data->name); //Self	
						}
						
						$is_supervisor = false;
						$check_supervisor = User::where('supervisor_id', $emp_id);
						if(!empty($request->name)){
							$name = $request->name;
							$check_supervisor->where(function($query) use ($name) {
								$query->where('name', 'LIKE', '%' . $name . '%')
								->orWhere('register_id', 'LIKE', '%' . $name);
							});
						}
						$check_supervisor = $check_supervisor->get();
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
						
						$date_wise_attendance = Attendance::groupBy('date');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_attendance->where('date', '>=', $request->date_from);
							$date_wise_attendance->where('date', '<=', $request->date_to);
						}
						else{
							$last_seven_days = date('Y-m-d', strtotime('-7 days'));
							if($is_supervisor){
								$last_seven_days = date('Y-m-d');
							}
							
							$date_wise_attendance->where('date', '>=', $last_seven_days);
							$date_wise_attendance->where('date', '<=', date('Y-m-d'));
						}
						$date_wise_attendance = $date_wise_attendance->get();
						
						if(count($date_wise_attendance) > 0){
							foreach($date_wise_attendance as $key=>$valAtt){
								$responseArray[$key]['date'] = $valAtt->date;
								$i = 0;
								$employee_attendance = array();
								foreach($employees_array as $key2=>$employeeId){
									$get_attendance = Attendance::where('emp_id', $employeeId['emp_id'])->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
									$time_array = array();
									$ii=0;
									if(count($get_attendance) > 0){
										foreach($get_attendance as $key1 => $AttendanceDetail){
											$in_time = "";
											$out_time = "";
											if(empty($time_array[$ii]['in_time'])){
												$time_array[$ii]['in_time'] = "";
											}
											if(empty($time_array[$ii]['out_time'])){
												$time_array[$ii]['out_time'] = "";
											}
											
											if($AttendanceDetail->type=="In"){
												$in_time = date("h:i A", strtotime($AttendanceDetail->time));
												if(empty($time_array[$ii]['in_time'])){
													$time_array[$ii]['in_time'] = $in_time;
												}
												else{
													$ii++;
													$time_array[$ii]['in_time'] = $in_time;
													$time_array[$ii]['out_time'] = "";
												}
											}
											else if($AttendanceDetail->type=="Out"){
												$out_time = date("h:i A", strtotime($AttendanceDetail->time));
												if(empty($time_array[$ii]['out_time'])){
													$time_array[$ii]['out_time'] = $out_time;
													$ii++;
												}
											}
										}
										// print_r($time_array); die;
										$i++;
									}
									$employee_attendance[$key2]['emp_id'] = $employeeId['emp_id'];
									$employee_attendance[$key2]['name'] = $employeeId['name'];
									$employee_attendance[$key2]['total_hours'] = "00";
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
	
	
}
