<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Attendance;   
use App\Leave; 
use App\LeaveDetail;
use App\ApiNotification;
use App\Mail\Facultylink;
use Excel;
use App\Holiday;
use App\AttendanceNew; 
use DB; 
use DateTime;

class CronJobController extends Controller
{   

	public function cron_job(){ //echo '<pre>'; print_r('ss');die;
        try {
			
			
                
                $get_user = User::where([['status','=', 1],['role_id' , '!=', 1]])->get();
				
				$date = date('Y-m-d ', strtotime(' -1 day'));
				foreach($get_user as $get_user_row){
					
                    $chk_attendence = Attendance::where([['emp_id','=', $get_user_row->id],['date' , '=', $date]])->get();
                        
                    if(count($chk_attendence) == 0){
						$leave_checked = Leave::with(['leave_details'])->where('emp_id', $get_user_row->id);
						$leave_checked->WhereHas('leave_details', function ($q) use ($date) {
							$q->where('date', $date);
						});
						$leave_checked = $leave_checked->get();
						if(count($leave_checked)==0){

                                $leave = Leave::create([
                                    'emp_id' => $get_user_row->id,
                                    'reason' => 'Due to not fill attendance.'
                                ]);
                                
                                if(!empty($leave->id)){
                                        $leave_detail = LeaveDetail::create([
                                                'leave_id'   => $leave->id,
                                                'date'       => $date,
                                                'type'       => 'Full Day',
                                                'status'     => 'Approved'

                                        ]);
                                }

                        }
					}
                        
                }
                
                return $this->returnResponse(200, true, "Successfully Attendence Add");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
	
	public function send_faculty_link(){ 
		// echo '<pre>'; print_r('ss');die;
		//$this->send_link_all("Dinesh","https://tinyurl.com/2abvca6e","5453","8104001734","czUz4lyiQaO7RGrg8yYGEA:APA91bGGSOCIdarETIxd48x_b9Gqt6ZWbYeeKPyqGpTfo4uwjar-G6ahUUt94hZE23N-VcvXFbg_pwRX0E3znt3hc9MW18jrH700_dUYO6ZTlnIvFsltDN73y0UeKTYHIcAX5rau9FBu");
		//die();
		$tomorrow = date('Y-m-d',strtotime('+1 days'));
		$faculties = DB::table('users')
			->select('users.*','timetables.cdate as class_date')
			->leftJoin('timetables', 'timetables.faculty_id', '=', 'users.id')
			->where('users.role_id', '2')
			->where('users.is_deleted', '0')
			->where('users.status', 1)
			->whereRaw("users.register_id IS NOT NULL and DATE(timetables.cdate) = '$tomorrow'")
			->groupBy("timetables.faculty_id")
			->orderBy('users.name');
		$employee_ids = $faculties->get();
		// $employee_ids = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name')->whereRaw("register_id IS NOT NULL")->get();
		// echo "<pre>"; print_r($employee_ids); die;
		// echo count($employee_ids); die;
		if(count($employee_ids) > 0){
		  foreach($employee_ids as $emp_detail){
			$emp_id = $emp_detail->id;
			$name = $emp_detail->name;
			$today_link = $emp_detail->send_link;
			$mobile = $emp_detail->mobile;
			$token = $emp_detail->gsm_token;	
			$this->send_link_all($name,$today_link,$emp_id,$mobile,$token);
			//\Mail::to($emp_detail->email)->queue(new Facultylink(['subject' => 'Utkarsh Timetable', 'today_link'=>$today_link]));
		  }
			
		}
		exit;
	}
	
	public function send_manager_link(){ 
		// echo '<pre>'; print_r('ss');die;
		$studiomanager = User::whereRaw("(role_id = 4 OR role_id = 27)")->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$studiomanager->whereRaw("register_id IS NOT NULL");
		$employee_ids = $studiomanager->get();
		// echo count($employee_ids); die;
		if(count($employee_ids) > 0){
			foreach($employee_ids as $emp_detail){
				$emp_id = $emp_detail->id;
				$name = $emp_detail->name;
				$today_link = $emp_detail->send_link;
				$mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				$this->send_link_all($name,$today_link,$emp_id,$mobile,$token);
			}
			
		}
		exit;
        
	}
	
	public function send_assistant_link(){
		// echo '<pre>'; print_r('ss');die;
		$assistants = User::where('role_id','3')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$assistants->whereRaw("register_id IS NOT NULL");
		$employee_ids = $assistants->get();
		// echo count($employee_ids); die;
		if(count($employee_ids) > 0){
			foreach($employee_ids as $emp_detail){
				$emp_id = $emp_detail->id;
				$name = $emp_detail->name;
				$today_link = $emp_detail->send_link;
				$mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				
				$this->send_link_all($name,$today_link,$emp_id,$mobile,$token);
				
			}
			
		}
		exit;
        
	}
	
	public function send_driver_link(){
		// echo '<pre>'; print_r('ss');die;
		$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$drivers->where('register_id','!=',NUll);
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
		$employee_ids = $drivers->get();
		// echo count($employee_ids); die;
		if(count($employee_ids) > 0){
			foreach($employee_ids as $emp_detail){
				$emp_id = $emp_detail->id;
				$name = $emp_detail->name;
				$today_link = $emp_detail->send_link;
				$mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				
				$this->send_link_all($name,$today_link,$emp_id,$mobile,$token);
				
			}
			
		}
		exit;
        
	}
	
	public function send_link_all($name,$today_link,$emp_id,$mobile,$token){
		
		$this->whatsapp_msg($name,$today_link,$mobile);
		$this->txt_msg($name,$today_link,$mobile);
		$msg="Dear $name, Your Time Table is published.. Please check";
		$this->notification_msg($token,$emp_id,$msg,901);
		
		
		//Gupsup Message
		$params = [];
		$params['input']   		 = [$today_link];
		$params['template_id']   = '5032b745-5c68-4c8d-8da3-73fcec95fcc6';
		$params['mobile']   	 = $mobile;
		$this->gupsup_msg($params);
		
		return true;
		
	}
	
	function whatsapp_msg($name,$today_link,$mobile){
		$url = "https://api.imiconnect.in/resources/v1/messaging";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   "Content-Type: application/json",
		   "key: 0b08bf38-6dd9-11ea-9da9-025282c394f2",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = '{
		    "appid": "a_158521380743240260",
		    "deliverychannel": "whatsapp",
		    "message": {
		        "template": "292198432928949",
		        "parameters": {
		          "variable1": "'.$name.'",
				  "variable2": "'.$today_link.'"
		          
		        }
		    },
		    "destination": [{
		            "waid": ["91'.$mobile.'"]
		        }
		    ]}';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$resp = curl_exec($curl);
		curl_close($curl);
		//var_dump($resp);
		return "ok";
	}
	
	function txt_msg($name,$today_link,$mobile){
		$msg="Dear $name,\nTo view the timetable click on the link given below: $today_link \nThank you \n-Team Utkarsh";
		$message_content=urlencode($msg);
		$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mobile}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		//print_r($result); die;
		return "ok";
	}
	
	function notification_msg($token,$emp_id,$msg,$logged_id){
		$employee_id[] = $emp_id;
		$inputs = array();
		$inputs['title'] = 'Timetable';
		$inputs['sender_id'] = $logged_id;
		$inputs['date'] = date('Y-m-d').' '. date('H:i:s');
		$inputs['description'] = $msg;
		$inputs['receiver_id']=json_encode($employee_id);
		$inputs['type'] = 'General';
		$notification = ApiNotification::create($inputs);
		
		$load = array();
		$load['title'] = $notification->title;
		$load['description'] = $notification->description;
		$load['date'] = $notification->date;
		$load['status'] = NULL;
		$load['type'] = 'general';
		$this->android_notification($token, $load);
		//echo "Test Notification"; die;
		return "ok";
	}
	
	public function is_extra_working_salary(){
		
		die('aaaaa');
		// $file = $request->file('is_extra_working_salary');
		
		$file = asset('laravel/public/data-NY.xlsx');

		// $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
                $stArr = $import[0][0];
                $result = [];
		$errors_row = "";
		echo "<pre>"; print_r($import[0]); die;
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) {
				if($key != 0){
					if($value[2] == 'Y'){
						//echo '<pre>'; print_r($value[1]);die;
						$ddd = $value[1];
						User::where('register_id', "$ddd")->update(['is_extra_working_salary' => '1']);
					}
				} 
			}	
			
		}
	}
	
	public function auto_reject_leave_arvind(){ 
                try { 
			$prev_date = date('Y-m-d',strtotime("-1 days")); 
			
			$leave_detail  = LeaveDetail::select('leave_details.*','userbranches.branch_id','users.total_time')->leftJoin('users','users.id','=','leave_details.emp_id')->leftJoin('userbranches','userbranches.user_id','=','users.id')->where('date',$prev_date)->orderBy('id','desc')->get();
			
			if(count($leave_detail) > 0){
				foreach($leave_detail as $leave_detail_val){
					$check_holiday  = Holiday::select('type','branch_id')->whereRaw('json_contains(branch_id, \'["' . $leave_detail_val->branch_id . '"]\')')->whereRaw("DATE(date) = '$leave_detail_val->date'")->where('status', '1')->where('is_deleted', '0')->first();
					
					if(!empty($check_holiday)){
						LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
					}
					else{
						$emp_id_data = $leave_detail_val->emp_id;
						//$emp_id_data = '5593';
						$attendance= Attendance::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						$attendancenew= AttendanceNew::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						$comman = $attendancenew->union($attendance);
						$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->get();
						//echo '<pre>'; print_r($comman_result);die;

						$time_array = array();
						$ii=0;
						$total_minute = 0;						
						if(count($comman_result) > 0){
							foreach($comman_result as $AttendanceDetail){ 
								$in_location = "";
								$out_location = "";
								$in_time = "";
								$out_time = "";
								if(empty($time_array[$ii]['in_time'])){
									$time_array[$ii]['in_time'] = "";
									$time_array[$ii]['in_location'] =  "";
								}
								if(empty($time_array[$ii]['out_time'])){
									$time_array[$ii]['out_time'] = "";
									$time_array[$ii]['out_location'] =  "";
								}
								
								if($AttendanceDetail->type=="In"){
									$in_time = $AttendanceDetail->time;
									if(empty($time_array[$ii]['in_time'])){
										$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
										$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
									}
									else{
										$ii++;
										$time_array[$ii]['in_time'] =  date("h:i A", strtotime($in_time));
										$time_array[$ii]['out_time'] = "";
										$time_array[$ii]['in_location'] =  $AttendanceDetail->location;
										$time_array[$ii]['out_location'] =  "";
									}
								}
								else if($AttendanceDetail->type=="Out"){
									$out_time = $AttendanceDetail->time;
									if(empty($time_array[$ii]['out_time'])){
										$time_array[$ii]['out_time'] =  date("h:i A", strtotime($out_time));
										$time_array[$ii]['out_location'] =  $AttendanceDetail->location;
										$ii++;
									}
								}
								
								
								$j = 1;
						
								if(count($time_array) > 0){
									foreach($time_array as $time_array_value){ 
										if(count($time_array) == $j){
											if(!empty($time_array_value['in_time']) && !empty($time_array_value['out_time'])){
												$intime = new DateTime($time_array_value['in_time']); 
												$outtime = new DateTime($time_array_value['out_time']); 
												$interval = $intime->diff($outtime);
												$hours = $interval->format('%H');
												$minute = $interval->format('%I');
												$total_minute += ($hours*60)+$minute;
											}	
										}
										$j++;
									}
								}
						
								
							}
							$totalMin	=	($total_minute*100)/$leave_detail_val->total_time;  
							if($totalMin <= 22.22){
								
							}
							else if($totalMin <= 66.66){ 
								if($leave_detail_val->type == 'Full Day'){ 
									LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
								}
							}
							else{  
								LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
							}
							
							//return $this->returnResponse(200, true, "Successfully Leave Reject");
						}
						else{
							//return $this->returnResponse(200, false, "No Data Found");
						}
						   
					}
				}
				return $this->returnResponse(200, true, "Successfully Leave Reject");
			}
			else{
				return $this->returnResponse(200, false, "No Data Found");
			}
			

	        } catch (\Illuminate\Database\QueryException $ex) {
		        return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
		        return $this->returnResponse(500, false, $ex->getMessage());
		}        
	}
	
	public function auto_reject_leave(){ 
                try { 
			$prev_date = date('Y-m-d',strtotime("-1 days")); 
			
			$leave_detail  = LeaveDetail::select('leave_details.*','userbranches.branch_id','users.total_time')
			->leftJoin('users','users.id','=','leave_details.emp_id')
			->leftJoin('userbranches','userbranches.user_id','=','users.id')
			->where('date',$prev_date)
			->whereRaw("leave_details.status != 'Rejected'")
			// ->where('emp_id',5795)
			->orderBy('id','desc')->get();
			// echo count($leave_detail); die;
			$i = 0;
			if(count($leave_detail) > 0){
				foreach($leave_detail as $leave_detail_val){
					$check_holiday  = Holiday::select('type','branch_id')
					->whereRaw('JSON_CONTAINS(branch_id, \'["'.$leave_detail_val->branch_id.'"]\')')
					->whereRaw("DATE(date) = '$leave_detail_val->date'")->where('status', '1')->where('is_deleted', '0')->first();
					
					if(!empty($check_holiday)){
						LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
						$i++;
					}
					else{
						$leave_date = $leave_detail_val->date;
						$emp_id_data = $leave_detail_val->emp_id;
						//$emp_id_data = '5593';
						// $attendance= Attendance::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						// $attendancenew= AttendanceNew::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						$attendance = Attendance::query();
						$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE Null END) AS in_time"));
						$attendance->where('emp_id', $emp_id_data);
						$attendance->whereRaw('date >= "'.$leave_date.'" AND date <= "'.$leave_date.'"');
						$attendance = $attendance->groupBy('date');
						
						$attendancenew = AttendanceNew::query();
						$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE Null END) AS in_time"));
						$attendancenew->where('emp_id', $emp_id_data);
						$attendancenew->whereRaw('date >= "'.$leave_date.'" AND date <= "'.$leave_date.'"');
						$attendancenew = $attendancenew->groupBy('date');
						
						$comman = $attendancenew->union($attendance);
						$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->groupBy('comman.emp_id')
										   ->groupBy('comman.date')
										   ->get();
						$comman_result = json_decode(json_encode($comman_result),true);  
						// echo '<pre>'; print_r($comman_result);die;

						$time_array = array();
						$ii=0;
						$total_minute = 0;
						$total_time = $leave_detail_val->total_time;  
						if(count($comman_result) > 0){
							foreach($comman_result as $attendance_array){
								$total_minute = 0;
								$in_time = $attendance_array['in_time'];
								$out_time = $attendance_array['out_time']; 
								
								if(!empty($in_time) && !empty($out_time)){
									$in_time = date("h:i A", strtotime($in_time));
									$out_time = date("h:i A", strtotime($out_time));
									$intime = new DateTime($in_time);
									$outtime = new DateTime($out_time);
									$interval = $intime->diff($outtime);
									$hours = $interval->format('%H');
									$minute = $interval->format('%I');
									$total_minute = ($hours*60)+$minute;
									$total_minute	=	($total_minute*100)/$total_time; // Total time in percentage
								}
						
								
							}
							// echo $total_minute; die;
							if($total_minute <= 22.22){
								
							}
							else if($total_minute <= 66.66){ 
								if($leave_detail_val->type == 'Full Day'){ 
									LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
									$i++;
								}
							}
							else{  
								LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
								$i++;
							}
							
							//return $this->returnResponse(200, true, "Successfully Leave Reject");
						}
						else{
							//return $this->returnResponse(200, false, "No Data Found");
						}
						   
					}
				}
				return $this->returnResponse(200, true, "Successfully Leave Reject total $i");
			}
			else{
				return $this->returnResponse(200, false, "No Data Found");
			}

	        } catch (\Illuminate\Database\QueryException $ex) {
	           return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
		        return $this->returnResponse(500, false, $ex->getMessage());
		}        
	}
	
	
	public function auto_reject_leave_with_date($date){ 
                try { 
			if(!empty($date)){
				$prev_date = $date;
			}
			else{
				$prev_date = "1970-01-01";
			}
			// echo $prev_date; die;
			// $prev_date = date('Y-m-d',strtotime("-1 days")); 
			
			$leave_detail  = LeaveDetail::select('leave_details.*','userbranches.branch_id','users.total_time')
			->leftJoin('users','users.id','=','leave_details.emp_id')
			->leftJoin('userbranches','userbranches.user_id','=','users.id')
			->where('date',$prev_date)
			->whereRaw("leave_details.status != 'Rejected'")
			// ->where('emp_id',5795)
			->orderBy('id','desc')->get();
			// echo count($leave_detail); die;
			$i = 0;
			if(count($leave_detail) > 0){
				foreach($leave_detail as $leave_detail_val){
					$check_holiday  = Holiday::select('type','branch_id')
					->whereRaw('JSON_CONTAINS(branch_id, \'["'.$leave_detail_val->branch_id.'"]\')')
					->whereRaw("DATE(date) = '$leave_detail_val->date'")->where('status', '1')->where('is_deleted', '0')->first();
					
					if(!empty($check_holiday)){
						LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
						$i++;
					}
					else{
						$leave_date = $leave_detail_val->date;
						$emp_id_data = $leave_detail_val->emp_id;
						//$emp_id_data = '5593';
						// $attendance= Attendance::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						// $attendancenew= AttendanceNew::selectRaw("id,emp_id,date,time,type,location,'App' as table_name")->where('emp_id',$emp_id_data)->where('date', '=',$leave_detail_val->date);
						
						$attendance = Attendance::query();
						$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE Null END) AS in_time"));
						$attendance->where('emp_id', $emp_id_data);
						$attendance->whereRaw('date >= "'.$leave_date.'" AND date <= "'.$leave_date.'"');
						$attendance = $attendance->groupBy('date');
						
						$attendancenew = AttendanceNew::query();
						$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$emp_id_data' AND date >= '$leave_date' AND date <= '$leave_date' THEN time ELSE Null END) AS in_time"));
						$attendancenew->where('emp_id', $emp_id_data);
						$attendancenew->whereRaw('date >= "'.$leave_date.'" AND date <= "'.$leave_date.'"');
						$attendancenew = $attendancenew->groupBy('date');
						
						$comman = $attendancenew->union($attendance);
						$comman_result = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->groupBy('comman.emp_id')
										   ->groupBy('comman.date')
										   ->get();
						$comman_result = json_decode(json_encode($comman_result),true);  
						// echo '<pre>'; print_r($comman_result);die;

						$time_array = array();
						$ii=0;
						$total_minute = 0;
						$total_time = $leave_detail_val->total_time;  
						if(count($comman_result) > 0){
							foreach($comman_result as $attendance_array){
								$total_minute = 0;
								$in_time = $attendance_array['in_time'];
								$out_time = $attendance_array['out_time']; 
								
								if(!empty($in_time) && !empty($out_time)){
									$in_time = date("h:i A", strtotime($in_time));
									$out_time = date("h:i A", strtotime($out_time));
									$intime = new DateTime($in_time);
									$outtime = new DateTime($out_time);
									$interval = $intime->diff($outtime);
									$hours = $interval->format('%H');
									$minute = $interval->format('%I');
									$total_minute = ($hours*60)+$minute;
									$total_minute	=	($total_minute*100)/$total_time; // Total time in percentage
								}
						
								
							}
							// echo $total_minute; die;
							if($total_minute <= 22.22){
								
							}
							else if($total_minute <= 66.66){ 
								if($leave_detail_val->type == 'Full Day'){ 
									LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
									$i++;
								}
							}
							else{  
								LeaveDetail::where('id', $leave_detail_val->id)->update([ 'status' => 'Rejected','is_cron'=>'1' ]);
								$i++;
							}
							
							//return $this->returnResponse(200, true, "Successfully Leave Reject");
						}
						else{
							//return $this->returnResponse(200, false, "No Data Found");
						}
						   
					}
				}
				return $this->returnResponse(200, true, "Successfully Leave Reject total $i");
			}
			else{
				return $this->returnResponse(200, false, "No Data Found");
			}
			

	        }catch (\Illuminate\Database\QueryException $ex) {
		        return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
		        return $this->returnResponse(500, false, $ex->getMessage());
		}        
	}
	
	
	public function update_manual_send_link(Request $request){
		//die();
		// $all_levae_details = DB::table('users')->whereRaw('send_link IS NULL')->limit(5)->get();
		
		// 1. Faculties
		$faculties = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$faculties->whereRaw("(register_id !='' OR register_id IS NOT NULL) AND (send_link ='' OR send_link IS NULL)");
		$faculties->limit(100);
		$faculties = $faculties->get();
		
		foreach($faculties as $val){
			$emp_id = $val->id;
			$link = url('/')."/faculty-reports?faculty_id=$emp_id";
			//$link = "https://hrm.utkarshupdates.com/index.php"."/faculty-reports?faculty_id=$emp_id";
			$link = $this->get_tiny_url($link);
			DB::table('users')->where('id', $val->id)->update([ 'send_link' => $link]);
		}
		// echo $emp_id.'/'.$link; die;
		
		// 2. Studio Manager
		
		/* $link = url('/')."/studio-reports";
		$link = $this->get_tiny_url($link);
		DB::table('users')->whereRaw("(role_id = 4 OR role_id = 27)")->where('is_deleted', '0')->where('status', 1)
					->whereRaw("register_id IS NOT NULL AND send_link IS NULL")
					->update([ 'send_link' => $link]); */
					
		
		// 3. Assistants
		
		/* $assistants = User::where('role_id','3')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$assistants->whereRaw("register_id IS NOT NULL AND send_link IS NULL");
		$assistants->limit(100);
		$assistants = $assistants->get();
		
		foreach($assistants as $val){
			$emp_id = $val->id;
			$link = url('/')."/studio-reports-assistant?assistant_id=$emp_id";
			$link = $this->get_tiny_url($link);
			DB::table('users')->where('id', $val->id)->update([ 'send_link' => $link]);
		} */
		
		// 4. Drivers 
		
		/* $drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$drivers->whereRaw("register_id IS NOT NULL AND send_link IS NULL");
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
		$drivers = $drivers->get();
		foreach($drivers as $val){
			$emp_id = $val->id;
			$link = url('/')."/faculty-reports-driver?driver_id=$emp_id";
			$link = $this->get_tiny_url($link);
			DB::table('users')->where('id', $val->id)->update([ 'send_link' => $link]);
		} */
		
		die('Done');
		
	}
	
	function get_tiny_url($url) {
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='. $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);	
		return $data;  
	}
	
	public function faculty_leave(){
		$insert_array = [];
		$faculty_ids = array();
		$today_date = date('Y-m-d');
		// $today_date = "2022-11-24";
		$check_already = DB::table('faculty_leave')->select('faculty_id')->where('date', $today_date)->get();
		if(count($check_already) > 0){
			foreach($check_already as $val){
				$faculty_ids[] = $val->faculty_id;
			}
		}
		$get_leave_faculty = DB::select("SELECT id FROM `users` where id NOT IN (select faculty_id from timetables where cdate = '$today_date' and is_deleted = '0' and time_table_parent_id = 0 and is_publish ='1' and is_cancel =0) and status = 1 and is_deleted = '0' and role_id=2");
		if(count($get_leave_faculty) > 0){
			foreach($get_leave_faculty as $val){
				if(!in_array($val->id,$faculty_ids)){
					$insert_array[] = array('faculty_id'=>$val->id,'date'=>$today_date);
				}					
			}
			DB::table('faculty_leave')->insert($insert_array);
		}
		// echo "<pre>"; print_r($insert_array);
		echo count($insert_array); exit;
		
		
	}

	public function earn_leave(){
		
		// die('Testing');
		$user_id = 0;
		if(!empty($_GET['user_id'])){
			$user_id = $_GET['user_id'];
		}
		else{
			//die('dddd');
		}
		
		$today_check = date('d');
		if($today_check > 29){
			return false;
		}

		$date = date('Y-m-d');
		$mt = date('m');
		$yr = date('Y');
		$last_month = date('m', strtotime('-1 month'));
		$first_date_of_month = $yr.'-'.$last_month.'-27';
		$last_date_of_month = $yr.'-'.$mt.'-26'; 
		if($last_month==2){
			$first_date_of_month = $yr.'-'.$last_month.'-25';
			$last_date_of_month = $yr.'-'.$mt.'-26'; 
		}
		


		//For Testing month year change wise
		/*$mt = date('m', strtotime('-1 month'));
		$yr = date('Y');
		$last_month = date('Y-m', strtotime('-2 month'));
		$first_date_of_month = $last_month.'-27';
		$last_date_of_month = $yr.'-'.$mt.'-26'; */
		

		$sql = "select `users`.*, `branches`.`id` as `branch_id`, `branches`.`branch_location` as `branch_location`, `userdetails`.`joining_date`, `users`.`status` as `user_status`, `userdetails`.`probation_from` 
		from `users` 
		left join `userdetails` on `users`.`id` = `userdetails`.`user_id` 
		left join `userbranches` on `users`.`id` = `userbranches`.`user_id` 
		left join `branches` on `userbranches`.`branch_id` = `branches`.`id` 
		left join `leave_earn` on `leave_earn`.`user_id` = `users`.`id` and leave_earn.month = $mt and leave_earn.year = $yr and leave_earn.cron_date = '$date'
		where `users`.`role_id` != 1 and `users`.`role_id` != 2 and `users`.`status` = 1 and `users`.`is_deleted` = '0' and `users`.`is_permanent` = '1' 
		and leave_earn.user_id IS NULL ";
		
		if(!empty($user_id)){
			$sql .=" and `users`.`id` = $user_id ";
		}

		$sql .=" group by userbranches.user_id limit 100 ";
		// echo $sql; die;
		$users=DB::select($sql);
		//and `users`.`id` in (5453, 1647, 8173) 
		
		// echo "<pre>"; print_R($users); die;
		$insert_update_count = 0;
		if(count($users) > 0){
			foreach($users as $val){
				$start_from = new DateTime($first_date_of_month);
				$end_to = new DateTime($last_date_of_month);
				$interval = $start_from->diff($end_to);
				$getWorkSunday =  $interval->days + 1;
				$getWorkSunday1 =  $interval->days + 1;

				$first_date = strtotime($first_date_of_month);
				$last_date = strtotime($last_date_of_month);

				$user_id = $val->id;
				$reason_date = $val->reason_date;
				$user_status = $val->user_status;
				
				$first_date_get = date('Y-m-d',$first_date);
				$last_date_get = date('Y-m-d',$last_date);
				$attendance_array = array();

				$attendance = Attendance::query();
				$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,time"));
				$attendance->where('emp_id', $user_id);
				$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
				// $attendance = $attendance->groupBy('date');
				
				
				$attendancenew = AttendanceNew::query();
				$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,time"));
				$attendancenew->where('emp_id', $user_id);
				$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
				// $attendancenew = $attendancenew->groupBy('date');				
				
				$comman = $attendancenew->union($attendance);
				$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   // ->groupBy('comman.emp_id')
						   // ->groupBy('comman.date')
						   ->orderBy('comman.date','asc')
						   ->orderBy('comman.time','asc')
						   ->get();
				$attendance_array = json_decode(json_encode($comman_result1),true);

				$date_array    = array();
				$holiday_array = array();
				$i = 1;
				$total_present 				= 0;
				$total_absent 				= 0;
				$total_present_half 		= 0;
				$total_holiday_working 		= 0;
				$total_week_off 			= 0;
				$count_week_days 			= 0;
				$monday_to_sunday_present 	= 0;
				$actual_paid 				= 0;
				$day_without_sunday 		= 0;
				$holiday 					= 0;
				$optional_holiday 			= 0;
				$total_pl 					= 0;
				$total_cl 					= 0;
				$total_paternity_leave 		= 0;
				$total_sl 					= 0;
				$total_co = 0; 

				$holiday_location = 0;
				if($val->branch_location=='jodhpur'){
					$holiday_location = 1;
				}
				else if($val->branch_location=='jaipur'){
					$holiday_location = 2;
				}
				else if($val->branch_location=='delhi'){
					$holiday_location = 3;
				}
				else if($val->branch_location=='prayagraj'){
					$holiday_location = 4;
				}
				$today_date = date('Y-m-d');
				$get_holiday = Holiday::select('date','branch_id','type')->where('status', '1')->where('is_deleted', '0')->whereRaw("DATE(date) <= '$today_date' AND (location = 0 OR location = $holiday_location  )")->get();				
				if(count($get_holiday) > 0){
					foreach($get_holiday as $get_holiday_val){
						$holiday_branch = json_decode($get_holiday_val->branch_id);
						if($get_holiday_val->type =='Employee'){
							if(!empty($val->id) && !empty($holiday_branch) && in_array($val->id, $holiday_branch)){
								array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
							}
						}
						else{
							if(!empty($val->branch_id) && !empty($holiday_branch) && in_array($val->branch_id, $holiday_branch)){
								array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
							}
						}
						
					}
				}

				$join_date 		=	$val->joining_date;
				$probation_from =	$val->probation_from;
				
				if(!empty($join_date)){
					$join_date = date('Y-m-d',strtotime($join_date));
				}	

				if($probation_from==""){
					$probation_from  	= 	date ("Y-m-d", strtotime ($join_date ."+90 days")); 
				}
				
				$is_probation = 0;
				if(date('Y-m-d') < $probation_from){
					$is_probation = 1;
				}
				
				//echo $is_probation; die;
				
				$total_time 		=	$val->total_time;
				$holiday_key=-1;
				while($getWorkSunday> 0){
					$total_minute = 0;
					$workday = date("D", $first_date);
					$add_get_date  = date('Y-m-d', $first_date);
					
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
					
						$ii = $i++;
						
						if(count($comman_result1) == 0){
							$date_array[$ii] = 'A';
							$total_absent++;
						}
						else{  
							if(strtotime($add_get_date) <= strtotime(date('Y-m-d'))){
							
								if($workday=="Mon"){
									
									$count_week_days = 1;
									
									//DK new add for if monday on holiday then 0
									if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
										$count_week_days = 0;
									}
									
								}
								else{
									// if(in_array($add_get_date, $holiday_array)){
									if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
										
									}
									else{
										$count_week_days++;
									}
								}
								$reason_date_user = date("Y-m-d", strtotime($reason_date));
								if($add_get_date < $join_date){
									$date_array[$ii] = 'A';
									$total_absent++;
									$workday1 = date("D", strtotime($join_date));
									if($workday1 != "Mon"){
										$count_week_days = 0;
									}
									
								}
								else if(strtotime($add_get_date) > strtotime($reason_date_user) && $user_status == 0 ){
									$date_array[$ii] = 'A';							
									$total_absent++;
								}						
								else{
									if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
										$monday_to_sunday_present++;
										
										$in_time = $min_time;
										$out_time = $max_time; 
										if($add_get_date==date('Y-m-d')){ //2021-07-31
											if(!empty($in_time)){								
												$date_array[$ii] = 'P';
												$total_present++;
											}else{
												$date_array[$ii] = 'A';
												$total_absent++;
											}
										}else{
											if(!empty($in_time) && !empty($out_time)){
												$in_time = date("h:i A", strtotime($in_time));
												$out_time = date("h:i A", strtotime($out_time));
												$intime = new DateTime($in_time);
												$outtime = new DateTime($out_time);
												$interval = $intime->diff($outtime);
												$hours = $interval->format('%H');
												$minute = $interval->format('%I');
												$total_minute = ($hours*60)+$minute;
												$total_minute	=	($total_minute*100)/$total_time; // Total time in percentage
											}
											
											if($workday == "Sun"){								
												if($total_minute < 38.88){  // 210 Mints == 3.30 hour == 38.88% 
													if($count_week_days >= 6){
														if($monday_to_sunday_present >= 3){
															$date_array[$ii] = 'WO';							
															$total_week_off++;
														}
														else{
															$date_array[$ii] = 'A';							
															$total_absent++;
														}
													}
													else{
														$date_array[$ii] = 'WO';							
														$total_week_off++;
													}
												}
												else if($total_minute < 66.66){ // 360 Mints == 6 hour == 38.88%
													if($count_week_days >= 6){
														if($monday_to_sunday_present >= 3){
															$date_array[$ii] = 'WO+HW/2';							
															$total_week_off++;
															$total_holiday_working +=0.5;
														}
														else{
															// Not eligible for week-off
															$date_array[$ii] = 'HW/2';
															$total_holiday_working +=0.5;
														}
													}
													else{
														$date_array[$ii] = 'WO+HW/2';							
														$total_week_off++;
														$total_holiday_working +=0.5;
													}
												}
												else{													
													if($count_week_days >= 6){
														if($monday_to_sunday_present >=3 ){							
															$date_array[$ii] = 'WO+HW';
															$total_week_off++;
															$total_holiday_working++;
														}
														else{
															$date_array[$ii] = 'HW';
															$total_holiday_working++;
														}
													}else{
														$date_array[$ii] = 'WO+HW';
														$total_week_off++;
														$total_holiday_working++;
													}
												}
												
												$monday_to_sunday_present = 0;								
											}
											else if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
												
												$array_index = array_search($add_get_date, array_column($holiday_array, 'date'));
												$is_optional 	= false;
												if($holiday_array[$array_index]['type']=="Optional"){
													$is_optional = true;
												}
												if($total_minute < 38.88){
													if($count_week_days >= 6){
														if($monday_to_sunday_present >=3 ){
															$date_array[$ii] = "H";
															$holiday++;
														}
														else{
															$date_array[$ii] = 'A';							
															$total_absent++;
														}
													}
													else{
														$date_array[$ii] = "H";
														$holiday++;
													}
												}
												else if($total_minute < 66.66){
													$total_holiday_working +=0.5;
													if($count_week_days >= 6){
														if($monday_to_sunday_present >= 3){
															$date_array[$ii] = "H+HW/2";							
															$holiday++;
														}
														else{
															// Not eligible for Holiday
															$date_array[$ii] = 'HW/2';
														}
													}
													else{
														$date_array[$ii] = "H+HW/2";							
														$holiday++;
													}
												}
												else{
													$total_holiday_working++;
													if($count_week_days >= 6){
														if($monday_to_sunday_present >=3 ){							
															$date_array[$ii] = "H+HW";
															$holiday++;
														}
														else{
															$date_array[$ii] = 'HW';
														}
													}else{
														$date_array[$ii] = "H+HW";
														$holiday++;														
													}
												}
											}
											else{ 
												if($total_minute < 50){  //270 Mints = 4.30 Hour = 50%
													$date_array[$ii] = 'A';
													$total_absent++;
													$monday_to_sunday_present--;
													
												}else if($total_minute < 88.88){  // 480 Mints = 8 hour == 88.88%
													$date_array[$ii] = 'PH/A';
													$total_present_half++;
													$total_absent += 0.5;
													
													$check_approved = DB::table('leave_details')->where('status','Approved')->where('emp_id',$user_id)->where('date',$add_get_date)->first();
													if(!empty($check_approved)){
														if($check_approved->category=="PL"){
															if($check_approved->type=='1st Half'){
																$date_array[$ii] ="PL/PH";
															}
															else if($check_approved->type=='2nd Half'){
																$date_array[$ii] ="PH/PL";
															}
															$total_pl +=0.5;
															$total_absent -= 0.5;
														}
														else if($check_approved->category=="CL"){
															if($check_approved->type=='1st Half'){
																$date_array[$ii] ="CL/PH";
															}
															else if($check_approved->type=='2nd Half'){
																$date_array[$ii] ="PH/CL";
															}
															$total_cl +=0.5;
															$total_absent -= 0.5;
														}
														else if($check_approved->category=="Paternity Leave"){
															if($check_approved->type=='1st Half'){
																$date_array[$ii] ="Paternity Leave/PH";
															}
															else if($check_approved->type=='2nd Half'){
																$date_array[$ii] ="PH/Paternity Leave";
															}
															$total_paternity_leave +=0.5;
															$total_absent -= 0.5;
														}
														else if($check_approved->category=="Comp Off"){
															if($check_approved->type=='1st Half'){
																$date_array[$ii] ="CO/PH";
															}
															else if($check_approved->type=='2nd Half'){
																$date_array[$ii] ="PH/CO";
															}
															$total_co +=0.5;
															$total_absent -= 0.5;
														}
													}
												}else{
													$date_array[$ii] = 'P';
													$total_present++;
												}
											}
										}
									}
									else{
										if($workday == "Sun"){							
											if($count_week_days >= 6){
												if($monday_to_sunday_present >= 3){
													$date_array[$ii] = 'WO';							
													$total_week_off++;
												}
												else{
													$date_array[$ii] = 'A';							
													$total_absent++;
													
													
													if($holiday_key>0){
														// $total_absent++;
														// $holiday--;
														//$date_array[$holiday_key]='A';
													}
													$holiday_key=-1;
													
												}
											}
											else{
												$date_array[$ii] = 'WO';							
												$total_week_off++;
											}
											
											$monday_to_sunday_present = 0;
											
										}
										else if($add_get_date=='2022-01-01' && in_array($user_id,$users_id_jan_2022)){
											$date_array[$ii] = "H";
											$holiday++;
										}
										else if($add_get_date=='2023-11-10' && in_array($user_id,$users_id_10_nov_2023)){
											$date_array[$ii] = "H";
											$holiday++;
										}
										else if($add_get_date=='2023-11-15' && in_array($user_id,$users_id_15_nov_2023)){
											$date_array[$ii] = "H";
											$holiday++;
										}
										else if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
											$reason_date_user = date("Y-m-d", strtotime($reason_date));
											if(strtotime($add_get_date) > strtotime($reason_date_user) && $user_status == 0 ){
												$date_array[$ii] = 'A';							
												$total_absent++;
											}
											else{
												$date_array[$ii] = "H";
												$holiday++;
												
												$holiday_key=$ii;
												

											}
										}
										else{
											$date_array[$ii] = 'A';
											$total_absent++;
										}
									}
								}
							
								if($date_array[$ii]=='A'){
									$check_approved_leave = DB::table('leave_details')->where('status','Approved')->where('emp_id',$user_id)->where('date',$add_get_date)->get();
									if(count($check_approved_leave) > 0){
										if(count($check_approved_leave)==1){
											foreach($check_approved_leave as $key=>$check_approved){
												if($check_approved->category=="PL"){
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "PL/A";
														$total_absent -= 0.5;
														$total_pl +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "A/PL";
														$total_absent -= 0.5;
														$total_pl +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] = "PL";
														$total_absent--;
														$total_pl++;
														$count_week_days--;
													}											
												}
												else if($check_approved->category=="CL"){
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "CL/A";
														$total_absent -= 0.5;
														$total_cl +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "A/CL";
														$total_absent -= 0.5;
														$total_cl +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="CL";
														$total_absent--;
														$total_cl++;
														$count_week_days--;
													}
												}
												else if($check_approved->category=="Paternity Leave"){
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "Paternity Leave/A";
														$total_absent -= 0.5;
														$total_paternity_leave +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "A/Paternity Leave";
														$total_absent -= 0.5;
														$total_paternity_leave +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="Paternity Leave";
														$total_absent--;
														$total_paternity_leave++;
														$count_week_days--;
													}
												}
												else if($check_approved->category=="Comp Off"){
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "CO/A";
														$total_absent -= 0.5;
														$total_co +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "A/CO";
														$total_absent -= 0.5;
														$total_co +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="CO";
														$total_absent--;
														$total_co++;
														$count_week_days--;
													}											
												}
												else if($check_approved->category=="LWP"){
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "LWP/A";
														// $count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "A/LWP";
														// $count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="LWP";
														// $count_week_days--;
													}											
												}
											}
										}
										else{
											$leave0 = "A";
											$leave1 = "A";
											foreach($check_approved_leave as $key=>$check_approved){
												if($check_approved->category=="PL"){
													${'leave' . $key} = "PL";
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_pl +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_pl +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] = "PL";
														$total_absent--;
														$total_pl++;
														$count_week_days--;
													}											
												}
												else if($check_approved->category=="CL"){
													${'leave' . $key} = "CL";
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_cl +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_cl +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="CL";
														$total_absent--;
														$total_cl++;
														$count_week_days--;
													}
												}
												else if($check_approved->category=="Paternity Leave"){
													${'leave' . $key} = "Paternity Leave";
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_paternity_leave +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_paternity_leave +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="Paternity Leave";
														$total_absent--;
														$total_paternity_leave++;
														$count_week_days--;
													}
												}
												else if($check_approved->category=="Comp Off"){
													${'leave' . $key} = "CO";
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_co +=0.5;
														$count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "$leave0/$leave1";
														$total_absent -= 0.5;
														$total_co +=0.5;
														$count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="CO";
														$total_absent--;
														$total_co++;
														$count_week_days--;
													}											
												}
												else if($check_approved->category=="LWP"){
													${'leave' . $key} = "LWP";
													if($check_approved->type=='1st Half'){
														$date_array[$ii] = "$leave0/$leave1";
														// $count_week_days -= 0.5;
													}
													else if($check_approved->type=='2nd Half'){
														$date_array[$ii] = "$leave0/$leave1";
														// $count_week_days -= 0.5;
													}
													else{
														$date_array[$ii] ="LWP";
														// $count_week_days--;
													}											
												}
											}
										}
										
										
									}
								}					
							
							}
							else{
								$date_array[$ii] = 'A';
							}
							
							if($workday != "Sun"){
								$day_without_sunday++;
							}
						}
						
					
					$first_date += 86400; 
					$getWorkSunday--;
				}

				if($total_present_half > 0){
					$total_present_half = ($total_present_half/2);
				}

				$actual_paid = $total_present + $total_present_half + $total_week_off + $holiday + $total_pl + $total_cl + $total_paternity_leave + $total_sl + $total_co;
				//echo $actual_paid; die;
				
				$add_pl = 0;
				$add_cl = 0;
				$check_already_earn = DB::table('leave_earn')->select('*')->where('user_id', $user_id)->where('year', $yr)->where('month', $mt)->first();
				if(!empty($check_already_earn)){
					$is_probation = $check_already_earn->is_probation;
				}
				
				if($is_probation){
					if($actual_paid > 20){
						$add_pl = 1;
					}
				}
				else{
					if($actual_paid > 20){
						$add_pl = 2;
						$add_cl = 0.5;
					}
					else if($actual_paid > 11){
						$add_pl = 1;
					}
				}

				//echo $add_pl; die;

				
				if(!empty($check_already_earn)){
					//print_R($check_already_earn); die;
					$insert_update_count++;
					$earn_pl = $check_already_earn->earn_pl;
					$earn_cl = $check_already_earn->earn_cl;
					$add_earn_pl_cl = ($add_pl + $add_cl - $earn_pl - $earn_cl);
					if($add_earn_pl_cl > 0){
						$_details = DB::table('leave_records')->where('user_id',$user_id)->where('session',$yr)->first();
						if(!empty($_details)){
							$add_earn_pl = ($add_pl - $earn_pl);
							$add_earn_cl = ($add_cl - $earn_cl);
							$updateLeaveUser = array();
							if($add_earn_pl > 0){
								$total_pl = $_details->pl + $add_earn_pl;
								$updateLeaveUser['pl'] = $total_pl;
							}
							if($add_earn_cl > 0){
								$total_cl = $_details->cl + $add_earn_cl;
								$updateLeaveUser['cl'] = $total_cl;
							}
							if(count($updateLeaveUser) > 0){
								DB::table('leave_records')->where('id', $_details->id)->update($updateLeaveUser);
							}
							
						}
						else{
							/*DB::table('leave_records')->insertGetId([ 
								'user_id' => $user_id,
								'session' => $yr,
								'pl' => $add_earn_pl
							]);*/
						}
						
					}

					$earnLeaveArray = array('earn_pl' => $add_pl,'earn_cl' => $add_cl, 'cron_date' => $date);
					DB::table('leave_earn')->where('id', $check_already_earn->id)->update($earnLeaveArray);
					
					
				}
				else{
					$insert_update_count++;
					$earnLeaveArray = array('user_id' => $user_id, 'month' => $mt, 'year' => $yr, 'earn_pl' => $add_pl,'earn_cl' => $add_cl, 'cron_date' => $date, 'is_probation' => $is_probation);
					$insett_id = DB::table('leave_earn')->insertGetId($earnLeaveArray);
					
					$_details = DB::table('leave_records')->where('user_id',$user_id)->where('session',$yr)->first();
					if(!empty($_details)){						
						$updateLeaveUser = array();
						if($add_pl > 0){
							$total_pl = $_details->pl + $add_pl;
							$updateLeaveUser['pl'] = $total_pl;
						}
						if($add_cl > 0){
							$total_cl = $_details->cl + $add_cl;
							$updateLeaveUser['cl'] = $total_cl;
						}
						if(count($updateLeaveUser) > 0){
							DB::table('leave_records')->where('id', $_details->id)->update($updateLeaveUser);
						}
						
					}
					else{
						DB::table('leave_records')->insertGetId([ 
							'user_id' => $user_id,
							'session' => $yr,
							'pl' => $add_pl,
							'cl' => $add_cl
						]);
					}
				}

			}
		}
		echo $insert_update_count; die('-Done');

	}

	
 }
