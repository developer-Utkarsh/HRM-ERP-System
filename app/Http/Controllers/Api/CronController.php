<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Attendance;   
use App\Leave; 
use App\LeaveDetail;
use App\ApiNotification;
use DB;

class CronController extends Controller
{   

	public function notPunchLeave(){ 
        try {
			$attendance_array=array();$attendance_array2 = array();$attendance_array3 = array();
			$new_attendance=DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance_new WHERE date="'.date('Y-m-d').'" AND type="In") AND role_id != 1 AND status = 1 AND users.register_id>1001');
			if(count($new_attendance) > 0){
				foreach($new_attendance as $new_attendance_val){
						$attendance_array[] = $new_attendance_val->id;
				}
			} 
			   
			$attendance=DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance WHERE date="'.date('Y-m-d').'" AND type="In") AND role_id != 1 AND status = 1 AND users.register_id>1001');
			if(count($attendance) > 0){
				foreach($attendance as $attendance_val){
					$attendance_array2[] = $attendance_val->id;
				}
			} 
			
			//$attd_intersect = array_intersect($attendance_array, $attendance_array2);
			$emp_data = 0;
			$empInLeave = DB::select("SELECT emp_id FROM `leave` WHERE DATE(created_at)='".date('Y-m-d')."' AND emp_id>1001");
			if(count($empInLeave) > 0){
				foreach($empInLeave as $empInLeaveVal){
					$emp_data .= $empInLeaveVal->emp_id.',';
				}
				$emp_data = rtrim($emp_data, ", ");
			}
			
			$leave_data  = DB::select("SELECT users.id FROM users WHERE users.id NOT IN ($emp_data) AND role_id != 1 AND status = 1 AND register_id>1001");

			if(count($leave_data) > 0){
				foreach($leave_data as $leave_val){
					$attendance_array3[] = $leave_val->id;
				}
			} 
			 
			$not_add_attd_data = array_intersect($attendance_array, $attendance_array2, $attendance_array3);
			
			//echo '<pre>'; print_r($json_attd_data);die; 
			if(count($not_add_attd_data)>0){
				$json_attd_data='["'.implode('","',$not_add_attd_data).'"]';
			    //$json_attd_data=json_encode(array_values($not_add_attd_data));
				$load=array();
				$load['sender_id']   = '901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder ! You have to Punch-In attendance yet';
				$load['description'] = 'Dear Employee, you have to mark your morning attendance. Kindly mark your attendance otherwise half day will be marked for today. - Utkarsh Classes प्रिय कर्मचारी, आपने अपनी सुबह की उपस्थिति अंकित नहीं की है। अपनी उपस्थिति दर्ज करें अन्यथा आधे दिन की अनुपस्थिति मानी जाएगी। ';
				//$load['image']       =asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				ApiNotification::create($load);
				$mbl="";
				$token = [];
				foreach($not_add_attd_data as $not_add_attd_data_val){
					$emp_detail = DB::table('users')->where('id',$not_add_attd_data_val)->first();
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[]=$emp_detail->gsm_token;
					} 
				}
                unset($load['sender_id']);unset($load['receiver_id']);
                $this->android_notification($token, $load);
                $message_content="Dear Employee, you have to mark your morning attendance. Kindly mark your attendance otherwise half day will be marked for today. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}
             
			return $this->returnResponse(200, true, "Notification Punch Send Successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
	
	public function firstHalfLeave(){
		 try {
			$attendance_array = array();$attendance_array2 = array();
			$new_attendance       = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance_new WHERE date="'.date('Y-m-d').'" AND type="In") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($new_attendance) > 0){
				foreach($new_attendance as $new_attendance_val){
						$attendance_array[] = $new_attendance_val->id;
				}
			} 
			   
			$attendance   = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance WHERE date="'.date('Y-m-d').'" AND type="In") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($attendance) > 0){
				foreach($attendance as $attendance_val){
					$attendance_array2[] = $attendance_val->id;
				}
			} 
		
			 
			$not_add_half_attd_data = array_intersect($attendance_array, $attendance_array2);
		
			if(count($not_add_half_attd_data)>0){
				$json_attd_data='["'.implode('","',$not_add_half_attd_data).'"]';
				//$json_attd_data      = json_encode(array_values($not_add_half_attd_data));
				$load                = array();
				$load['sender_id']   = '901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder ! you have not punch-In attendance yet we mark full day absent';
				$load['description'] = 'Dear Employee, you have not marked post-lunch attendance. Mark your attendance or you shall be marked absent for a full day. - Utkarsh Classes';
				//$load['image']       = asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				
				
				ApiNotification::create($load);
				$mbl="";
				$token=[];
				foreach($not_add_half_attd_data as $not_add_half_attd_data_val){
					$emp_detail = DB::table('users')->where('id',$not_add_half_attd_data_val)->first();
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[] = $emp_detail->gsm_token;
					}	
				}
                unset($load['sender_id']);unset($load['receiver_id']);
                $this->android_notification($token, $load);
                $message_content= "Dear Employee, you have not marked post-lunch attendance. Mark your attendance or you shall be marked absent for a full day. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}
               
			return $this->returnResponse(200, true, "Notification 1st half leave and not in yet send successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
	public function notPunchOutYesterday(){
		try {
			$attendance_array = array();$attendance_array2 = array();
			$new_attendance       = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance_new WHERE date="'.date('Y-m-d',strtotime("-1 days")).'" AND type="Out") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($new_attendance) > 0){
				foreach($new_attendance as $new_attendance_val){
						$attendance_array[] = $new_attendance_val->id;
				}
			} 
			   
			$attendance   = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance WHERE date="'.date('Y-m-d',strtotime("-1 days")).'" AND type="Out") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($attendance) > 0){
				foreach($attendance as $attendance_val){
					$attendance_array2[] = $attendance_val->id;
				}
			} 
		
			 
			$not_punch_out_yesterday_attd_data = array_intersect($attendance_array, $attendance_array2);
			
			if(count($not_punch_out_yesterday_attd_data)>0){
				$json_attd_data='["'.implode('","',$not_punch_out_yesterday_attd_data).'"]';
				//$json_attd_data      = json_encode(array_values($not_punch_out_yesterday_attd_data));
				$load                = array();
				$load['sender_id']   = '901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder ! Not punch out yesterday';
				$load['description'] = 'Dear Employee, you have missed marking the exit yesterday. Kindly contact your HOD & HRD to update the same. - Utkarsh Classes';
				//$load['image']       = asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				
				ApiNotification::create($load);
				$token = [];
				$mbl="";
				foreach($not_punch_out_yesterday_attd_data as $not_punch_yesterday_attd_data_val){
					$emp_detail = DB::table('users')->where('id',$not_punch_yesterday_attd_data_val)->first();
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[] = $emp_detail->gsm_token;
					}
				}

                unset($load['sender_id']);unset($load['receiver_id']);
				$this->android_notification($token, $load);
                $message_content= "Dear Employee, you have missed marking the exit yesterday. Kindly contact your HOD & HRD to update the same. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}
               
			return $this->returnResponse(200, true, "Notification not punch out yesterday send successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
	public function notPunchLeaveYesterday(){
		 try {
			$attendance_array = array();$attendance_array2 = array();$attendance_array3 = array();
			$new_attendance       = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance_new WHERE date="'.date('Y-m-d',strtotime("-1 days")).'" AND type="In") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($new_attendance) > 0){
				foreach($new_attendance as $new_attendance_val){
						$attendance_array[] = $new_attendance_val->id;
				}
			} 
			   
			$attendance   = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM attendance WHERE date="'.date('Y-m-d',strtotime("-1 days")).'" AND type="In") AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($attendance) > 0){
				foreach($attendance as $attendance_val){
					$attendance_array2[] = $attendance_val->id;
				}
			} 
			
			$emp_data = 0;
			$empInLeave = DB::select("SELECT emp_id FROM `leave` WHERE DATE(created_at)='".date('Y-m-d',strtotime("-1 days"))."' AND emp_id>1001");
			if(count($empInLeave) > 0){
				foreach($empInLeave as $empInLeaveVal){
					$emp_data .= $empInLeaveVal->emp_id.',';
				}
				$emp_data = rtrim($emp_data, ", ");
			}
			
			$leave_data  = DB::select("SELECT users.id FROM users WHERE users.id NOT IN ($emp_data) AND role_id != 1 AND status = 1 AND register_id>1001");

			if(count($leave_data) > 0){
				foreach($leave_data as $leave_val){
					$attendance_array3[] = $leave_val->id;
				}
			} 
			 
			$not_add_attd_yesterday_data = array_intersect($attendance_array, $attendance_array2, $attendance_array3);
			   
			if(count($not_add_attd_yesterday_data)>0){
				$json_attd_data='["'.implode('","',$not_add_attd_yesterday_data).'"]';
				//$json_attd_data      = json_encode(array_values($not_add_attd_yesterday_data));
				$load                = array();
				$load['sender_id']   = '901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder! Your Absent has been marked for Yesterday';
				$load['description'] = 'Dear Employee, you have missed marking IN nor OUT (attendance) for yesterday. - Utkarsh Classes';
				//we have marked your leave for yesterday.
				//$load['image']       = asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				
				
				ApiNotification::create($load);
				$mbl="";
				$token=[];
				foreach($not_add_attd_yesterday_data as $not_add_attd_yesterday_data_val){
					$emp_detail = DB::table('users')->where('id',$not_add_attd_yesterday_data_val)->first();
					
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[] = $emp_detail->gsm_token;
					}
					
				}
                unset($load['sender_id']);unset($load['receiver_id']);
				$this->android_notification($token, $load);
				//we have marked your leave for yesterday.
                $message_content= "Dear Employee, you have missed marking IN nor OUT (attendance) for yesterday. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}  
			return $this->returnResponse(200, true, "Notification no in-out and no leave yesterday send successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
	public function notAddTask(){
		die('code working fine but it seems unwanted');
		try {
			$tark_array = array();
			$task_details = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM task WHERE date="'.date('Y-m-d').'" Group By emp_id) AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($task_details) > 0){
				foreach($task_details as $task_details_val){
						$tark_array[] = $task_details_val->id;
				}
			} 
			  	 
			if(count($tark_array)>0){
				$json_attd_data='["'.implode('","',$tark_array).'"]';
				//$json_attd_data      = json_encode(array_values($tark_array));
				
				$load=array();
				$load['sender_id']   ='901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder! Today task not updated yet';
				$load['description'] = 'Dear Employee, this is a reminder to let you know that you have not updated your task yet. Kindly update them. - Utkarsh Classes';
				//$load['image']       = asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				
				ApiNotification::create($load);
				$mbl="";$token=[];
				foreach($tark_array as $tark_array_val){
					$emp_detail=DB::table('users')->where('id',$tark_array_val)->first();
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[] = $emp_detail->gsm_token;
					}
					 
				}
				unset($load['sender_id']);unset($load['receiver_id']);
                $this->android_notification($token, $load);
                $message_content= "Dear Employee, this is a reminder to let you know that you have not updated your task yet. Kindly update them. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}  
			return $this->returnResponse(200, true, "Notification not added your task yet send successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
	public function notAddTaskYesterday(){
		die('code working fine but it seems unwanted');
		try {
			$tark_array = array();
			$task_details = DB::select('SELECT users.* FROM users WHERE users.id NOT IN (SELECT emp_id FROM task WHERE date="'.date('Y-m-d',strtotime("-1 days")).'" Group By emp_id) AND role_id != 1 AND status = 1 AND register_id>1001');
			if(count($task_details) > 0){
				foreach($task_details as $task_details_val){
						$tark_array[] = $task_details_val->id;
				}
			} 
				 
			if(count($tark_array)>0){
				$json_attd_data='["'.implode('","',$tark_array).'"]';
				//$json_attd_data      = json_encode(array_values($tark_array));
				$load                = array();
				$load['sender_id']   = '901';
				$load['receiver_id'] = $json_attd_data;
				$load['title']       = 'Reminder! Yesterday task not added';
				$load['description'] = 'You have not added your task yesterday. Add task immediately if task not updated then half-day will be marked for yesterday. - Utkarsh Classes';
				//$load['image']       = asset('laravel/public/images/test-image.png');
				$load['type']        = 'General';
				$load['date']        = date('Y-m-d');
				
				
				ApiNotification::create($load);
				$mbl="";$token=[];
				foreach($tark_array as $tark_array_val){
					$emp_detail = DB::table('users')->where('id',$tark_array_val)->first(); 
					//Send SMS
					$mbl.=$emp_detail->mobile.",";
					//Send Notification
					if(!empty($emp_detail->gsm_token)){
						$token[] = $emp_detail->gsm_token;
					}
					
				}
                unset($load['sender_id']);unset($load['receiver_id']);
				$this->android_notification($token, $load);
                $message_content= "You have not added your task yesterday. Add task immediately if task not updated then half-day will be marked for yesterday. - Utkarsh Classes";
				$mbl=rtrim($mbl,",");
				$templateid="1707161942001979704";
				//$this->smsbulk($mbl,$message_content,$templateid);
			}  
			return $this->returnResponse(200, true, "Notification not added your task yesterday send successfully");

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
 }
