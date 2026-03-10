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
		//echo '<pre>'; print_r('ss');die;
		//$this->send_link_all("link","1089","7014155376","ddd");
		//die();
		$faculties = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$faculties->whereRaw("register_id IS NOT NULL");
		//$faculties->whereRaw("(mobile = 8104001734 OR mobile = 7014155376)");
		$employee_ids = $faculties->get();
		// echo count($employee_ids); die;
		if(count($employee_ids) > 0){
		  foreach($employee_ids as $emp_detail){
			$emp_id = $emp_detail->id;
			$today_link = $emp_detail->send_link;
			$mobile = $emp_detail->mobile;
			$token = $emp_detail->gsm_token;	
			$this->send_link_all($today_link,$emp_id,$mobile,$token);
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
				$today_link = $emp_detail->send_link;
				echo $mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				$this->send_link_all($today_link,$emp_id,$mobile,$token);
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
				$today_link = $emp_detail->send_link;
				echo $mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				
				$this->send_link_all($today_link,$emp_id,$mobile,$token);
				
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
				$today_link = $emp_detail->send_link;
				echo $mobile = $emp_detail->mobile;
				$token = $emp_detail->gsm_token;
				
				$this->send_link_all($today_link,$emp_id,$mobile,$token);
				
			}
			
		}
		exit;
        
	}
	
	public function send_link_all($today_link,$emp_id,$mobile,$token){
		// die('wwww');
		/* Notification */ 
		$today_date_name = "";
		$tomorrow_date_name = "";
		$tomorrow_link = "";
$msg="नमस्ते,

टाईमटेबल देखने के लिए नीचे दिए गए लिंक पर क्लिक करें:
$today_date_name $today_link
$tomorrow_date_name $tomorrow_link

- धन्यवाद 
टीम उत्कर्ष";
		$employee_id[] = "$emp_id";
		$inputs = array();
		$inputs['title'] = 'Timetable';
		$inputs['sender_id'] = 901;
		$inputs['date'] = date('Y-m-d').' '. date('H:i:s');
		$inputs['description'] = $msg;
		$inputs['receiver_id'] = json_encode($employee_id);
		$inputs['type'] = 'General';
		$notification = ApiNotification::create($inputs);
		
		$load = array();
		$load['title'] = $notification->title;
		$load['description'] = $notification->description;
		$load['date'] = $notification->date;
		$load['status'] = NULL;
		$load['type'] = 'general';
		$this->android_notification($token, $load);
		
		// echo "Test Notification"; die;
		
		
		$mbl=$mobile;
		$message_content=urlencode($msg);
		
		// $url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=LNG&DR=Y";
		
		$url = "http://sms.messageindia.in/v2/sendSMS?username=utkarsh&message=$message_content&sendername=UTKRSH&smstype=TRANS&numbers=91$mbl&apikey=51363c03-4c0f-40d5-a695-c0c0b31d5018&peid=1701158072985103391&templateid=1207163652431012612";
		
		// echo $url; die;
		
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		return true;
		
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
			

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
	
 }
