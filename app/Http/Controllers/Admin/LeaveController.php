<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Leave;
use App\LeaveDetail;
use App\NewTask;
use App\User;
use App\Holiday;
use Input;
use Excel;
use App\Exports\LeaveExport;
use Auth;
use DB;
use DateTime;
use App\Attendance;
use App\AttendanceNew;
use App\AttendanceLock;

 
class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logged_department_type = Auth::user()->department_type;
		$logid           = array();
		$logid_dh        = array();
        $users           = NewTask::getEmployeeByLogID($logged_id,'leave'); 
        $branch_id = Input::get('branch_id');
        $name = Input::get('name');
        $status = Input::get('status');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
        $department_type = Input::get('department_type');
		
		$date = date('Y-m-d');
		$leave = Leave::with(['user','user.user_branches',
			'leave_details'=>function ($q) use ($status,$date,$fdate,$tdate) {
			
				if (!empty($status)){
					$q->where('status', $status);
				}
				
				if(!empty($fdate) && !empty($tdate)){
					$q->where('date', '>=', $fdate);
					$q->where('date', '<=', $tdate);
				}
				else{
					$q->where('date', $date);
				}
			}
		]);
			
		$leave->WhereHas('leave_details', function ($q) use ($status,$date,$fdate,$tdate) {
			
			if (!empty($status)){
				$q->where('status', $status);
			}
			
			if(!empty($fdate) && !empty($tdate)){
				$q->where('date', '>=', $fdate);
				$q->where('date', '<=', $tdate);
			}
			else{
				$q->where('date', $date);
			}
		}); 

		if (!empty($name) || !empty($department_type)){
			$leave->WhereHas('user', function ($q) use ($name,$department_type) { // orWhereHas dk
				if(!empty($name)){
					$q->whereRaw("(name LIKE '%$name%' OR register_id LIKE '%$name')");
				}
				if(!empty($department_type)){
					$q->where('department_type', $department_type);
				}
			});
		}

		if (!empty($branch_id)){
			$leave->WhereHas('user.user_branches', function ($q) use ($branch_id) { // orWhereHas dk
				$q->where('branch_id', '=', $branch_id);
			});
		}
		
	
		// if($logged_role_id == 21){
			// $leave->WhereHas('user', function ($q) use ($logged_id) {
				// $q->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
			// });
		// }
		
		if($logged_role_id == 20){
			$leave->whereIn('emp_id', [$logged_id]);
		}
		else if($logged_role_id == 21){ 
			$users_dh        = NewTask::getEmployeeForDepartmentHead($logged_id, $logged_role_id, $logged_department_type,'leave_record');
			/* if(!empty($users_dh)){
				foreach($users_dh as $users_dh_value){
					$logid_dh[] = $users_dh_value['id'];
				} 
			} */
			
			$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users_dh));
			// echo "<pre>"; print_r($usr); die;
			$leave->whereIn('emp_id', explode(',',$usr));
		}
		else{
			$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			/* echo "<pre>"; print_r($usr); die;
			$logid[]         = $logged_id; 
			if(!empty($users)){
				foreach($users as $usersvalue){
					$logid[] = $usersvalue['id'];
				} 
			} */
			$leave->whereIn('emp_id', explode(',',$usr));	
		}
		$leave_array = $leave->get();
		
		// echo count($leave_array); die;
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
        // echo "<pre>"; print_r($allDepartmentTypes); die;
        return view('admin.leave.index', compact('leave_array','allDepartmentTypes'));
    }
	
	public function create(){
		// if(date('Y-m-d') >= '2024-05-25'){
			// echo "<h3 style='color:red;'>Please mark attendance and apply for leaves using the DawinBox app only.<h3>";  die;
		// }
			
		$login_id               = Auth::user()->id;
		$logged_role_id         = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		
		// if($logged_role_id != 29 && $logged_role_id != 24){
			// if(date('Y-m-d') >= '2023-10-01'){
				// echo "<h3 style='color:red;'>From 1 October attendance is only accepted by Darwinbox application. If you are facing any issues, contact the HR department.<h3>";  die;
			// }
		// }
		
		
		if($logged_role_id == 20){
			$users        = User::where('id', $login_id)->get();
		}
		else if($logged_role_id == 21){
			$users = NewTask::getEmployeeForDepartmentHead($login_id, $logged_role_id, $logged_department_type,'leave_add');
		}
		else{
			$users        = NewTask::getEmployeeByLogID($login_id,'leave');
		}
		return view('admin.leave.add', compact('users'));
	}
	
	public function update_manual_emp_id(Request $request){
		$all_levae_details = DB::table('leave_details')->whereRaw('emp_id IS NULL')->limit(500)->get();
		// echo count($all_levae_details); die;
		foreach($all_levae_details as $val){
			$leave_id = $val->leave_id;
			$levae_details = DB::table('leave')->where('id',$leave_id)->first();
			if(!empty($levae_details)){
				DB::table('leave_details')->where('id', $val->id)->update([ 'emp_id' => $levae_details->emp_id]);
			}
		}
	}
	
	
	public function check_pl(Request $request){
		echo $this->check_leaves_records($request->emp_id,$request->leave_date );
	}
	
	public function check_leaves_records($emp_id=null,$leave_date=null,$category=null){
		if(!empty($emp_id) && !empty($leave_date)){
			$session = date('Y',strtotime($leave_date));
			
			$pl = 0;
			$cl = 0;
			$paternity_leave = 0;
			$get_leave_records = DB::table('leave_records')->whereRaw("user_id = $emp_id AND session = $session")->first();
			if(!empty($get_leave_records)){
				$pl = $get_leave_records->pl;
				$cl = $get_leave_records->cl;
				$paternity_leave = $get_leave_records->paternity_leave;
			}
			$res = "<option value=''> Select </option>";
			
			$all_leave_details = DB::table('leave_details')->whereRaw("emp_id = $emp_id AND YEAR(date) = $session and status='Approved' and category='PL'")->count();
			if($pl > $all_leave_details){
				$selected = "";
				if($category=='PL'){
					$selected = "selected";
				}
				$res .="<option value='PL' $selected> PL </option>";
			}
			
			$all_leave_details = DB::table('leave_details')->whereRaw("emp_id = $emp_id AND YEAR(date) = $session and status='Approved' and category='CL'")->count();
			if($cl > $all_leave_details){
				$selected = "";
				if($category=='CL'){
					$selected = "selected";
				}
				$res .="<option value='CL' $selected> CL </option>";
			}
			
			$all_leave_details = DB::table('leave_details')->whereRaw("emp_id = $emp_id AND YEAR(date) = $session and status='Approved' and category='Paternity Leave'")->count();
			if($paternity_leave > $all_leave_details){
				$selected = "";
				if($category=='Paternity Leave'){
					$selected = "selected";
				}
				$res .="<option value='Paternity Leave' $selected> Paternity Leave </option>";
			}
			$selected = "";
			if($category=='LWP'){
				$selected = "selected";
			}
			$res .="<option value='LWP' $selected> LWP </option>";
			
			return $res;
			exit;
		}
		else{
			return $res = "<option value=''> Select </option>";
			exit;
		}
	}
	
	public function save(Request $request){
		// echo '<pre>'; print_r($request->post()); die;
		$logged_role_id         = Auth::user()->role_id;
		
		if($logged_role_id==2){
			return response(['status' => false, 'message' => 'You can not apply for leave'], 200);
		}else{
			if(!empty($request->emp_id)){
				$from_date 	=	$request->from_date;
				$to_date 	=	$request->to_date;
				$session 	=	date('Y',strtotime($from_date));
				$month 		=	date('m',strtotime($from_date));
				if(1){
					$mont_year = date('Y-m',strtotime($from_date));
					$alock = AttendanceLock::where('month', $mont_year)->first();
					if(!empty($alock)){
						if($alock->status=='1'){
							return response(['status' => false, 'message' => 'Leave is locked.'], 200);
						}
					}
					
					$mont_year = date('Y-m',strtotime($to_date));
					$alock = AttendanceLock::where('month', $mont_year)->first();
					if(!empty($alock)){
						if($alock->status=='1'){
							return response(['status' => false, 'message' => 'Leave is locked.'], 200);
						}
					}
				}
					
				$user_id 	=	$request->emp_id;
								
				$date 		=	$request->date;
				
				if(strtotime($date[0]) <= strtotime("2023-12-25")){					
					return response(['status' => false, 'message' => "From 26 December 2023 attendance is only accepted. If you are facing any issues, contact the HR department."], 200);exit;
				}
				
				$category 	=	$request->category;
				$type 		=	$request->type;
				
				//Chetan //Error befare 90Days apply leave
				$userData 		= 	User::select('users.total_time','users.id','userdetails.probation_from','userdetails.joining_date','branches.id as branch_id','branches.branch_location','users.is_extra_working_salary');
									$userData->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
									$userData->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
									$userData->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
				$userData = $userData->where('users.id',$user_id)->first();
				$is_extra_working_salary = $userData->is_extra_working_salary;
				$total_time 	=	$userData->total_time;
				$probation_from 	=	$userData->probation_from;
				
				/* if($is_extra_working_salary=='1'){
					if(in_array('Comp Off',$category)){
						return response(['status' => false, 'message' => "You can't apply Comp Off."], 200);
					}
				} */
				
				if($probation_from==""){
					$joining_date	=	$userData->joining_date;;							
					$probation_from  	= 	date ("Y-m-d", strtotime ($joining_date ."+90 days")); 
				}
				
				$mindate 	 	= 	date('Y-m-d');						
				
				if($mindate < $probation_from){
					$a1 = array(0=>'Comp Off',1=>'LWP'); // Comp Off or LWP alava type aata h to false 
					// if(count(array_diff($category,$a1)) > 0){
						// return response(['status' => false, 'message' => 'You are ineligible for any paid leaves as your probation period is still ongoing...'], 200);
					// }
				}
				if(1){
					$holiday_array = array();
					$today_date = date('Y',strtotime($date[0]));
					$get_holiday = Holiday::select('date','branch_id','type')->where('status', '1')->where('is_deleted', '0')->whereRaw("YEAR(date) = '$today_date' ")->get();				
					if(count($get_holiday) > 0){
						foreach($get_holiday as $get_holiday_val){
							$holiday_branch = json_decode($get_holiday_val->branch_id);
							if($get_holiday_val->type =='Employee'){
								if(!empty($userData->id) && !empty($holiday_branch) && in_array($userData->id, $holiday_branch)){
									array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
								}
							}
							else{
								if(!empty($userData->branch_id) && !empty($holiday_branch) && in_array($userData->branch_id, $holiday_branch)){
									array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
								}
							}
						}
						foreach($date as $key => $date_value){
							if(array_search($date_value, array_column($holiday_array, 'date')) !== false){
								$date_value = date('d-m-Y',strtotime($date_value));
								return response(['status' => false, 'message' => "$date_value is a holiday."], 200);exit;
							}
						}
						
					}
					
					foreach($date as $key => $date_value){
						$leave_checked = DB::table('leave_details')->whereRaw("emp_id = $user_id AND DATE(date) = '$date_value' and type ='$type[$key]' AND status != 'Rejected'")->get();																
						if(count($leave_checked) > 0){
							return response(['status' => false, 'message' => "$date_value $type[$key] already leave request send."], 200);exit;
						}
						
						$already_date_leave = DB::table('leave_details')->whereRaw("emp_id = $user_id AND DATE(date) = '$date_value' AND status != 'Rejected'")->get();
						if(count($already_date_leave) > 0){
							$allTypes = array();
							foreach($already_date_leave as $leaveVal){
								$allTypes[] = $leaveVal->type;
							}
							if(!in_array('Full Day',$allTypes)){
								if(in_array('1st Half',$allTypes)){
									if($type[$key] == "Full Day"){
										return response(['status' => false, 'message' => "$date_value 1st Half already leave request send."], 200);exit;
									}
								}
								else if(in_array('2nd Half',$allTypes)){
									if($type[$key] == "Full Day"){
										return response(['status' => false, 'message' => "$date_value 2nd Half already leave request send."], 200);exit;
									}
								}
							}
							else{
								return response(['status' => false, 'message' => "$date_value Full Day already leave request send."], 200);exit;
							}
						}
						
						$check_present = DB::select(DB::raw("SELECT date,emp_id, MAX(time) as min_time, MIN(time) as max_time FROM ( SELECT date,emp_id, time FROM attendance where emp_id=$user_id and date(date) ='$date_value' UNION ALL SELECT date,emp_id, time FROM attendance_new where emp_id=$user_id and date(date) ='$date_value' ) as subQuery where emp_id=$user_id and date(date) ='$date_value'"));
						if(!empty($check_present[0]->min_time)){
							$in_time =  $check_present[0]->min_time;
							$out_time = $check_present[0]->max_time;
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
								if($total_minute < 50){  
									//A
								}else if($total_minute < 88.88){
									//PH
									if($type[$key]=='Full Day'){
										return response(['status' => false, 'message' => "You are present half day on $date_value. If you want half day then select type half day."], 200);exit;
									}
								}else{
									//P
									return response(['status' => false, 'message' => "You are present on $date_value."], 200);exit;
								}
								
							}
							
							
						}
						
					}
					
					$take_pl = 0;
					$take_cl = 0;
					$take_paternity_leave = 0;
					$take_month_pl = 0;
					$take_month_cl = 0;
					$take_month_paternity_leave = 0;
					$take_month_comp_off = 0;
					$condition_check_leave = false;
					$month_array = array();
					$leave_type_array = array();
					$check_month = 0;
					$check_year = 0;
					foreach($date as $key => $date_value){
						$check_month = date('m',strtotime($date_value));
						$check_year = date('Y',strtotime($date_value));
						if(date('d',strtotime($date_value)) >= 27){
							$check_month =  date('m', strtotime('+1 month', strtotime($date_value)));
							$check_year =  date('Y', strtotime('+1 month', strtotime($date_value)));
						}
						
						if(!in_array($check_month,$month_array)){
							$month_array[] = $check_month;
						}
						
						if(!in_array($category[$key],$leave_type_array)){
							$leave_type_array[] = $category[$key];
						}
						
						if($category[$key] != 'LWP'){
							$condition_check_leave = true;
						}
						if($category[$key]=='PL'){
							if($type[$key] == 'Full Day'){
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
						else if($category[$key]=='CL'){
							if($type[$key] == 'Full Day'){
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
						else if($category[$key]=='Paternity Leave'){
							if($type[$key] == 'Full Day'){
								$add_paternity_leave = 1;
							}
							else{
								$add_paternity_leave = 0.5;
							}
							$take_paternity_leave += $add_paternity_leave;
							if($check_month == $month){
								$take_month_paternity_leave += $add_paternity_leave;
							}
						}
						else if($category[$key]=='Comp Off'){
							if($type[$key] == 'Full Day'){
								$take_month_comp_off += 1;
							}
							else{
								$take_month_comp_off += 0.5;
							}
							/* if($check_month != $month){
								return response(['status' => false, 'message' => "You can't other month comp off."], 200);
								exit;
							} */
						}
					}
					
					if(count($month_array) > 1){
						return response(['status' => false, 'message' => "You can apply for leave only for this month at a time."], 200);exit;
					}
					
					
					if($condition_check_leave){
						
						$probation_year =  date('Y',strtotime($probation_from));
						$probation_month =  date('m',strtotime($probation_from));
						
						if($check_year==$probation_year){
							$check_month = ($check_month - $probation_month) + 1;
						}
					
						$url = env('APP_URL')."api/users/leave_types";
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
							$is_extra_working_salary = $pending_leaves->data->is_extra_working_salary;
							$pl = $pending_leaves->data->pending_pl;
							$cl = $pending_leaves->data->pending_cl;
							$paternity_leave = $pending_leaves->data->pending_paternity_leave;
							$last_year_pl = $pending_leaves->data->last_year_pl;
							$pending_comp_off = $pending_leaves->data->pending_comp_off;
							$pl_already = $pending_leaves->data->pl_already;
							$cl_already = $pending_leaves->data->cl_already;
							$paternity_leave_already = $pending_leaves->data->paternity_leave_already;
							$total_already = $pl_already + $cl_already + $paternity_leave_already;
							$total_current_leave = $total_already + $take_pl + $take_cl;
							if($user_id == 5762){
								//$total_current_leave = 0;
							}
							$total_month_according = ($check_month * 2.5) + $last_year_pl;
							$comp_key = array(0=>'Comp Off');
							if($is_extra_working_salary=='1' && $pending_comp_off > 0 && count(array_diff($leave_type_array,$comp_key)) > 0){
								return ['status' => false, 'message' => "Please select leave type Comp Off, Because your remaining Compensation Off $pending_comp_off"]; exit;
							}
							else if(count($leave_type_array)==1 && in_array('Comp Off',$leave_type_array)){
								if( $take_month_comp_off > $pending_comp_off){
									return response(['status' => false, 'message' => "Your remaining Comp Off $pending_comp_off"], 200); exit;
								}
							}
							else if($total_current_leave > $total_month_according){
								if($logged_role_id==29){
									return response(['status' => false, 'message' => "Can't apply leave. Please contact to HR Department."], 200);exit;
								}else{
									return response(['status' => false, 'message' => "Can't apply leave. Please contact to HR Department2."], 200);exit;
								}
								
							}
							else if($take_pl > $pl){
								return response(['status' => false, 'message' => "Your remaining PL $pl"], 200);exit;
							}
							else if($take_cl > $cl){
								return response(['status' => false, 'message' => "Your remaining CL $cl"], 200);exit;
							}
							else if($take_paternity_leave > $paternity_leave){
								return response(['status' => false, 'message' => "Your remaining Paternity Leave $paternity_leave"], 200);exit;
							}
							else if( $take_month_comp_off > $pending_comp_off){
								//return response(['status' => false, 'message' => "Your remaining Comp Off $pending_comp_off"], 200); exit;
							}
						}
						else{
							return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
							exit;
						}
					}
					
					//$leave_id = DB::table('leave')->insertGetId([ 'emp_id' => $user_id, 'reason' => $request->reason ]);
					$leaveArray = array('emp_id' => $user_id, 'reason' => $request->reason);
					$leave_id = DB::table('leave')->insertGetId($leaveArray);
					
					$this->maintain_history(Auth::user()->id, 'leave', $leave_id, 'insert_leave', json_encode($leaveArray));
					
					if($leave_id){
						foreach($date as $key => $date_value){
							// DB::table('leave_details')->insertGetId([ 
								// 'emp_id' => $user_id,
								// 'leave_id' => $leave_id,
								// 'date' => $date_value,
								// 'category' => $category[$key],
								// 'type' => $type[$key]
							// ]);
							
							$leaveDetailArray = array(
													'emp_id' => $user_id,
													'leave_id' => $leave_id,
													'date' => $date_value,
													'category' => $category[$key],
													'type' => $type[$key]
												);
							$leave_details_id = DB::table('leave_details')->insertGetId($leaveDetailArray);

							$this->maintain_history(Auth::user()->id, 'leave_details', $leave_details_id, 'insert_leave_details', json_encode($leaveDetailArray));
						}
					}
					return response(['status' => true, 'message' => 'Leave Added Successfully'], 200);
				}else{
					return response(['status' => false, 'message' => 'You are ineligible for any paid leaves as your probation period is still ongoing.'], 200);
				}
			}
			else{
				return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
			}
		}
	}
	 
	
	public function edit_leave(Request $request){
		
		if(!empty($request->id)){
			$login_id               = Auth::user()->id;
			$logged_role_id         = Auth::user()->role_id;
			$logged_department_type = Auth::user()->department_type;
			if($logged_role_id == 20){
				$users        = User::where('id', $login_id)->get();
			}
			else if($logged_role_id == 21){
				$users = NewTask::getEmployeeForDepartmentHead($login_id, $logged_role_id, $logged_department_type);
			}
			else{
				$users        = NewTask::getEmployeeByLogID($login_id,'leave');
			}
		
			//$leave_data = Leave::with('leave_details')->where('id',$request->id)->first();
			$leave_details_id = $request->id;
			$leave_data = Leave::with(['leave_details' => function ($q) use ($leave_details_id){
                if (!empty($leave_details_id)){
                    $q->where('id', $leave_details_id);
                }
            }]);

			
			$leave_data->WhereHas('leave_details', function ($q) use ($leave_details_id) {
				if (!empty($leave_details_id)){
                    $q->where('id', $leave_details_id);
                }
			});
		    
			$leave_data = $leave_data->first();
			
			$emp_id = $leave_data->leave_details[0]->emp_id;
			$leave_date = $leave_data->leave_details[0]->date;
			
			if($logged_role_id != 24 && $logged_role_id!=29){
				$mont_year = date('Y-m',strtotime($leave_date));
				$alock = AttendanceLock::where('month', $mont_year)->first();
				if(!empty($alock)){
					if($alock->status=='1'){
						return redirect()->back()->with('error', 'Leave is locked.');
					}
				}
			}
			
			$category = $leave_data->leave_details[0]->category;
			$options =  $this->check_leaves_records($emp_id,$leave_date,$category);
			return view('admin.leave.edit', compact('leave_data','users','options'));
		}
		else{
			return redirect()->route("admin.leave.index")->with('error', 'Something Went Wrong !');
		}
	}
	
	public function edit_leave_store(Request $request){
		//echo '<pre>';print_r($request->post());die;
		if(!empty($request->emp_id)){
			$login_id  = Auth::user()->id;
			//DB::table('leave')->where('id', $request->leave_id)->update([ 'emp_id' => $request->emp_id, 'reason' => $request->reason ]);
			$editLeaveArray = array('emp_id' => $request->emp_id, 'reason' => $request->reason);
			DB::table('leave')->where('id', $request->leave_id)->update($editLeaveArray);

			$leave_id = DB::table('leave')->where('id', $request->leave_id)->first();
			$this->maintain_history(Auth::user()->id, 'leave', $leave_id->id, 'update_leave', json_encode($editLeaveArray));
			
			$leaveStatus =	LeaveDetail::where('id', $request->leave_detail_id)->first();
			if($request->status!=""){
				$sUpdate	=	$request->status;
			}else{
				$sUpdate	=	$leaveStatus['status'];
			}
			
			//DB::table('leave_details')->where('id', $request->leave_detail_id)->update([ 'date' => $request->date, 'status' => $sUpdate, 'type' => $request->type, 'category' => $request->category, 'updated_by' => $login_id ]);
			//return redirect()->route('admin.leave.index')->with('success', 'Updated Successfully');
			
			$editLeaveDetailArray = array('date' => $request->date, 'status' => $sUpdate, 'type' => $request->type, 'category' => $request->category, 'updated_by' => $login_id);
			DB::table('leave_details')->where('id', $request->leave_detail_id)->update($editLeaveDetailArray);

			$leave_detail_id = DB::table('leave_details')->where('id', $request->leave_detail_id)->first();
			$editLeaveDetailArray['emp_id'] = $request->emp_id;
			$this->maintain_history(Auth::user()->id, 'leave_details', $leave_detail_id->id, 'update_leave_details', json_encode($editLeaveDetailArray));
			
			$check = $_SERVER['QUERY_STRING'];
			if(!empty($check)){
				$nCheck	=	"?".$check;
			}else{
				$nCheck	=	"";
			}
			return redirect('admin/leave'.$nCheck)->with('success', 'Updated Successfully');
		}
		else{
			return redirect()->route("admin.leave.index")->with('error', 'Something Went Wrong !');
		}
	}
	
	public function delete_leave(Request $request){
		if(!empty($request->id)){
			$leave_details = DB::table('leave_details')->select('*')->where('id', $request->id)->first();
			// DB::table('leave')->where('id', $request->id)->delete();
			DB::table('leave_details')->where('id', $request->id)->delete();
			$this->maintain_history(Auth::user()->id, 'leave_details', $request->id, 'delete_leave_details', json_encode($leave_details));
			return redirect()->route('admin.leave.index')->with('success', 'Delete Successfully');
		}
		else{
			return redirect()->route("admin.leave.index")->with('error', 'Something Went Wrong !');
		}
	}
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
 
	public function view($leave_id,$task_detail_id)
    {
		$leave = DB::table('leave')
			  ->select('leave.*','users.id as u_id','users.name','users.email','users.image','users.register_id','users.mobile','leave_details.status','leave_details.id as leave_detail_id','leave_details.leave_reason','leave_details.category','leave_details.type','leave_details.date as leave_date')
			  ->join('users', 'users.id', '=', 'leave.emp_id')
			  ->join('userdetails', 'userdetails.user_id', '=', 'users.id')
			  ->join('leave_details', 'leave_details.leave_id', '=', 'leave.id')
			  ->where('leave.id', $leave_id)
			  ->where('leave_details.id', $task_detail_id)
			  ->first();
		if(!empty($leave)){
			$leave_details = DB::table('leave_details')->select('*')->where('leave_id', $leave_id)->get();
			
			$user_id = $leave->u_id;
			$url = env('APP_URL')."api/users/leave_types";
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
			
			$session = date('Y');
			$month = date('m',strtotime($leave->leave_date));
			$full_name_month = date('F',strtotime($leave->leave_date));
			$total_leave = DB::table('leave_details')->whereRaw("emp_id = $user_id AND MONTH(date) = $month and status='Approved' AND YEAR(date) = $session")->get();
			$total_leaves = 0;
			if(!empty($total_leave)){
				foreach($total_leave as $vv){
					if($vv->type == 'Full Day'){
						$total_leaves += 1;
					}
					else{
						$total_leaves += 0.5;
					}
				}
			}
			$total_this_month_leave = $total_leaves;
			
			return view('admin.leave.view', compact('leave','leave_details','pending_leaves','total_this_month_leave','full_name_month'));
		}
		else{
			return redirect()->route('admin.leave.index')->with('error', 'something went wrong');
		}
		
	}
	
	public function approval(Request $request, $id)
    {
		
        $validatedData = $request->validate([
			'leave_approval' => 'required'
        ]);
		
		$inputs = $request->only('leave_approval');
		
		
		$leave_details = LeaveDetail::where('id', $id)->first();
		if(!empty($leave_details)){
			/* if($leave_details->status==$request->leave_approval){
				return redirect()->route('admin.leave.index')->with('error', 'Already '.$request->leave_approval);
			} */
			$leave_id = $request->leave_id;
			$update_data['status'] = $request->leave_approval;
			$update_data['leave_reason'] = $request->leave_reject_reason;
			
			DB::table('leave_details')->where('leave_id', $leave_id)->update($update_data);
			
			return redirect()->route('admin.leave.index')->with('success', 'Updated Successfully');
		}
		else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
		
        
    }
	
	public function approve_one_by(Request $request){
		$login_id = Auth::user()->id;
		$logged_role_id         = Auth::user()->role_id;
		$leave_detail_id = $request->leave_detail_id;
		$leave_details = LeaveDetail::where('id', $leave_detail_id)->first();
		if(!empty($leave_details)){
			if($leave_details->status==$request->leave_status){
				return response(['status' => false, 'message' => 'Already '.$request->leave_status], 200);
			}
			
			if($request->leave_status=='Rejected'){
				$update_data['status'] = $request->leave_status;
				$update_data['category'] = $request->leave_category;
				$update_data['updated_by'] = $login_id;
				if($leave_details->update($update_data)) {
					$update_data['emp_id'] = $leave_details->emp_id;
					$this->maintain_history(Auth::user()->id, 'leave_details', $leave_details->id, 'reject_leave', json_encode($update_data));
					
					return response(['status' => true, 'message' => 'Updated Successfully'], 200);
				} else {
					return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
				} 
			}
			
			$leave_date = $leave_details->date;
			$mont_year = date('Y-m',strtotime($leave_date));
			if(1){
				$alock = AttendanceLock::where('month', $mont_year)->first();
				if(!empty($alock)){
					if($alock->status=='1'){
						return response(['status' => false, 'message' => 'Leave is locked. You can only reject leave'], 200);
					}
				}
			}
			
			$user_id = $leave_details->emp_id;

			$usData 		= 	Userdetails::where('user_id',$user_id)->first();
			$probation_from	=	$usData['probation_from'];
			if($probation_from==""){
				$joining_date	=	$usData['joining_date'];							
				$probation_from  	= 	date ("Y-m-d", strtotime ($joining_date ."+90 days")); 
			}
			$user 		= 	User::where('id',$user_id)->first();
			if($user->status=='0'){
				$reason_date = $user->reason_date;
				$reason_date_user = date("Y-m-d", strtotime($reason_date));
				if(strtotime($leave_details->date) > strtotime($reason_date_user)){
					return response(['status' => false, 'message' => "You can't leave approve becase employee leaving date ". date('d-m-Y',strtotime($reason_date))], 200);
					exit;
				}
			}
			$mindate = date('Y-m-d');
			$categoryArray = array(0=>$request->leave_category);
			
			if($mindate < $probation_from){
				$a1 = array(0=>'Comp Off',1=>'LWP'); // Comp Off or LWP alava type aata h to false 
				if(count(array_diff($categoryArray,$a1)) > 0){
					return response(['status' => false, 'message' => "You are ineligible for any paid leaves as your probation period is still ongoing..."], 200); exit;
					exit;
				}
			}

			$url = env('APP_URL')."api/users/leave_types";
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Content-Type: application/x-www-form-urlencoded",
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$data = "user_id=$user_id&action=approve_reject";

			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$resp = curl_exec($curl);
			curl_close($curl);
			$pending_leaves =  json_decode($resp);
			
			$pending_pl = !empty($pending_leaves->data) ? $pending_leaves->data->pending_pl : '0';
			$pending_cl = !empty($pending_leaves->data) ? $pending_leaves->data->pending_cl : '0';
			$pending_paternity_leave = !empty($pending_leaves->data) ? $pending_leaves->data->pending_paternity_leave : '0';
			$pending_sl = '0';
			$pending_comp_off = !empty($pending_leaves->data) ? $pending_leaves->data->pending_comp_off : '0';
			if($request->leave_category =='PL'){
				if($pending_pl <= 0){
					return response(['status' => false, 'message' => "Your remaining PL $pending_pl"], 200); exit;
				}
			}
			else if($request->leave_category =='SL'){
				if($pending_sl <= 0){
					return response(['status' => false, 'message' => "Your remaining SL $pending_sl"], 200); exit;
				}
			}
			else if($request->leave_category =='CL'){
				if($pending_cl <= 0){
					return response(['status' => false, 'message' => "Your remaining CL $pending_cl"], 200); exit;
				}
			}
			else if($request->leave_category =='Paternity Leave'){
				if($pending_paternity_leave <= 0){
					return response(['status' => false, 'message' => "Your remaining Paternity Leave $pending_paternity_leave"], 200); exit;
				}
			}
			else if($request->leave_category =='Comp Off'){
				if($pending_comp_off <= 0){
					return response(['status' => false, 'message' => "Your remaining Comp Off $pending_comp_off"], 200); exit;
				}
			}
			else if($request->leave_category =='LWP'){
				
			}
			else{
				return response(['status' => false, 'message' => "Something Went Wrong. Please contact to HR."], 200); exit;
			}

			$update_data['status'] = $request->leave_status;
			$update_data['category'] = $request->leave_category;
			$update_data['updated_by'] = $login_id;
			// $update_data['leave_reason'] = $request->leave_reject_reason;
			// print_r($update_data); die;
			if($leave_details->update($update_data)) {
				$update_data['emp_id'] = $leave_details->emp_id;
				$this->maintain_history(Auth::user()->id, 'leave_details', $leave_details->id, $request->leave_status.'_leave', json_encode($update_data));
				
				return response(['status' => true, 'message' => 'Updated Successfully'], 200);
			} else {
				return response(['status' => false, 'message' => 'Something Went Wrong !'], 200);
			} 
		}
		else{
			return response(['status' => false, 'message' => 'Leave Added Successfully'], 200);
		}		
		
	}
    
	public function download_excel()
    {
		$logged_role_id         = Auth::user()->role_id;
		$logged_id              = Auth::user()->id;
		$logged_department_type = Auth::user()->department_type;
		$logid                  = array();
		$logid_dh               = array();
        $users                  = NewTask::getEmployeeByLogID($logged_id,'leave'); 
        $branch_id              = Input::get('branch_id');
        $name                   = Input::get('name');
        $status                 = Input::get('status');
		$fdate                  = Input::get('fdate');
        $tdate                  = Input::get('tdate');
        $department_type                  = Input::get('department_type');
		
		$date = date('Y-m-d');
		$leave = Leave::with(['user','user.user_branches',
			'leave_details'=>function ($q) use ($status,$date,$fdate,$tdate) {
			
				if (!empty($status)){
					$q->where('status', $status);
				}
				
				if(!empty($fdate) && !empty($tdate)){
					$q->where('date', '>=', $fdate);
					$q->where('date', '<=', $tdate);
				}
				else{
					$q->where('date', $date);
				}
			}
		]);
		
		$leave->WhereHas('leave_details', function ($q) use ($status,$date,$fdate,$tdate) {
			
			if (!empty($status)){
				$q->where('status', $status);
			}
			
			if(!empty($fdate) && !empty($tdate)){
				$q->where('date', '>=', $fdate);
				$q->where('date', '<=', $tdate);
			}
			else{
				$q->where('date', $date);
			}
		}); 
		
		if (!empty($name) || !empty($department_type)){
			$leave->WhereHas('user', function ($q) use ($name,$department_type) { // orWhereHas dk
				if(!empty($name)){
					$q->whereRaw("(name LIKE '%$name%' OR register_id LIKE '%$name')");
				}
				if(!empty($department_type)){
					$q->where('department_type', $department_type);
				}
			});
		}

		if (!empty($branch_id)){
			$leave->WhereHas('user.user_branches', function ($q) use ($branch_id) { // orWhereHas dk
				$q->where('branch_id', '=', $branch_id);
			});
		}
		
		
		if($logged_role_id == 20){
			$leave->whereIn('emp_id', [$logged_id]);
		}
		else if($logged_role_id == 21){ 
			/*$users_dh        = NewTask::getEmployeeForDepartmentHead($logged_id, $logged_role_id, $logged_department_type);
			
			$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users_dh));
			$leave->whereIn('emp_id', explode(',',$usr));*/
			
			$users_dh        = NewTask::getEmployeeForDepartmentHead($logged_id, $logged_role_id, $logged_department_type,'leave_record');
			$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users_dh));
			$leave->whereIn('emp_id', explode(',',$usr));

		}
		else{
			$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$leave->whereIn('emp_id', explode(',',$usr));	
		}
		$leave_array = $leave->get();
		//echo '<pre>'; print_r($leave_array);die;
		
        if(!empty($leave_array)){
            return Excel::download(new LeaveExport($leave_array), 'LeaveData.xlsx'); 

        } else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }	
	
	public function append_leave_date(Request $request){
		$emp_id = $request->emp_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
		if(!empty($emp_id)){
			if($to_date < $from_date){
				return response(['status' => false, 'message' => 'From date less then To date'], 200);
				exit;
			}
			
			
			//Chetan
			$user_id = $emp_id;			

			$url = env('APP_URL')."api/users/leave_types";
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
			
			$pending_pl = $pending_leaves->data->pending_pl;
			$pending_cl = $pending_leaves->data->pending_cl;
			$pending_comp_off = $pending_leaves->data->pending_comp_off;
			$pending_paternity_leave = $pending_leaves->data->pending_paternity_leave;
			//End
			
			$begin = new DateTime($from_date);
			$end = new DateTime($to_date);
			$html = "";
			for($i = $begin; $i <= $end; $i->modify('+1 day')){
				$selected_date = $i->format("Y-m-d");
				$get_day = $i->format("D");
				if($get_day != "Sun"){
					$html .="<div class='col-md-4 col-12 date_div'>";
					$html .="<div class='form-group'>";
					$html .="<label for='first-name-column'>Date</label>";
					$html .="<input type='date' class='form-control leave_date' placeholder='Date' name='date[]' value='$selected_date' required readonly>";
					$html .="</div>";
					$html .="</div>";
					$html .="<div class='col-md-4 col-12 category_div'>";
					$html .="<div class='form-group'>";
					$html .="<label for=''>Category</label>";
					$html .="<select class='form-control category' id='category' name='category[]' required>";
					
					$html .="<option value=''> Select</option>";
					if($pending_pl > 0){ $html .="<option value='PL'> PL</option>"; }
					if($pending_cl > 0){ $html .="<option value='CL'> CL</option>"; }
					if($pending_comp_off > 0){ $html .="<option value='Comp Off'> Comp Off</option>"; }
					if($pending_paternity_leave > 0){ $html .="<option value='Paternity Leave'> Paternity Leave</option>"; }
					$html .="<option value='LWP'> LWP</option>";
					
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";
					$html .="<div class='col-md-4 col-12'>";
					$html .="<div class='form-group'>";
					$html .="<label for=''>Type</label>";
					$html .="<select class='form-control type' id='type' name='type[]' required>";
					$html .="<option value=''> Select</option>";
					$html .="<option value='1st Half'> 1st Half</option>";
					$html .="<option value='2nd Half'> 2nd Half</option>";
					$html .="<option value='Full Day'> Full Day</option>";
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";
				}
			}	
			
			return response(['status' => true, 'html' => $html], 200);
			exit;
		}
		else{
			return response(['status' => false, 'message' => 'Employee is required'], 200);
			exit;
		}
		
		
	}
	
	
	public function manual_leave_records(Request $request){
		die('qqqqq');
		$leave_month = DB::table('leave_month')->orderBy('month','ASC')->get();
		// echo "<pre>"; print_r($leave_month); die;
		$all_emp = DB::table('users')->select('users.id','userdetails.probation_from','userdetails.joining_date')
			->leftJoin('userdetails','userdetails.user_id','users.id')
			->where('users.role_id','!=',2)
			->where('users.status','=',1)
			// ->where('users.id','=','6331')
			->whereRaw("probation_from is not null")
			->orderBy('id','asc')->skip(0)->take(20000)->get();
		// echo "<pre>"; print_r($all_emp); die;
		// echo count($all_emp); die;
		$ii = 0;
		foreach($all_emp as $val){
			$user_id = $val->id;
			// echo $user_id;
			$probation_from = $val->probation_from;
			if(!empty($probation_from)){
				$year = date('Y',strtotime($probation_from));
				if($year <= 2022){
					$month = 1;
					$date = 1;
				}
				else{
					$month = date('n',strtotime($probation_from));
					$date = date('d',strtotime($probation_from));
				}
				// echo $month; die;
				
				// echo "<pre>"; print_r($leave_data); die;
				if($date > 15){
					$month = $month + 1;
				}
				if($month > 12){
					$month = 1;
				}
				
				$leave_data = $leave_month[$month-1];
				
				$_details = DB::table('leave_records')->where('user_id',$user_id)->where('session',2023)->first();
				if(empty($_details)){
					$ii++;
					DB::table('leave_records')->insertGetId([ 
							'user_id' => $user_id,
							'session' => 2023,
							'pl' => $leave_data->pl,
							'cl' => $leave_data->cl
						]);
				}
			}
		}
		echo $ii; die;
	}

	public function leavecount__old(Request $request)
    {
		
		$users=User::
		where('status',1)
		->where('role_id','!=',2);
		$empID =$request->empCode;
		if($empID!=""){
			$users->where('register_id',$empID);
		}

		$users=$users->paginate(100);
		$leave=[];
		foreach($users as $k=>$userKey){
			$uName	=	$userKey->name;
			$register_id	=	$userKey->register_id;
			$user_id	=	$userKey->id;
			
			$url = env('APP_URL')."api/users/leave_types";
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
			$leaves =  json_decode($resp,true);
			if(!empty($leaves) && $leaves['status']){
				$leaves['data']['uName']=$uName;
	            $leaves['data']['register_id']=$register_id;
	            $leave[]=$leaves['data'];
	            //echo json_encode($leaves['data']);die();
				//print_r($leaves);die();
	        }
		}

		$pending_leaves=[]; 
		$uName='';
		$register_id='';
		//echo json_encode($leave);
		//echo "<pre>";print_r($leave);die();	

		$leave=json_decode(json_encode($leave));
		
		return view('admin.leave.leavecount', compact('users','pending_leaves', 'uName','register_id','leave'));
    }
	
	public function get_pending_comp_off_all_users(Request $request)
    {
		$users=User::where('status',1)->where('role_id','!=',2);
		$empID =$request->empCode;
		if($empID!=""){
			$users->where('register_id',$empID);
		}

		$users=$users->get();
		$leave=[];
		$session = date('Y');
		$leaves = array();
		$data = array();
		foreach($users as $k=>$userKey){
			$uName	=	$userKey->name;
			$register_id	=	$userKey->register_id;
			$user_id	=	$userKey->id;
			
			if(isset($user_id) && !empty($user_id)){
				
				$user_check = User::query();
				$user_check->select(\DB::raw("users.*, branches.name as branch_name,branches.branch_location as branch_location,branches.id as branch_id, userdetails.joining_date, userdetails.probation_from"));
				$user_check->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
				$user_check->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
				$user_check->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
				$user_check->whereRaw("users.id = $user_id");
				$user_check->groupBy(['userbranches.user_id']);
				$user = $user_check->first();
				// echo "<pre>"; print_r($user); die;
				
				if(!empty($user)){
					if(1){
						$probation_from	=	$user->probation_from;
						if($probation_from==""){
							$joining_date	=	$user->joining_date;							
							$probation_date  	= 	date ("Y-m-d", strtotime ($joining_date ."+90 days")); 
						}
						else{
							$probation_date		=	$probation_from;
						}
						$leave_array = array();
						$pl = 0;
						$cl = 0;
						$last_year_pl = 0;
						$total_holiday_working = 0;	
						if(date('Y-m-d') >= $probation_date){
							$get_leave_records = DB::table('leave_records')->whereRaw("user_id = $user_id AND session = $session")->first();
							if(!empty($get_leave_records)){
								$pl = $get_leave_records->pl;
								$cl = $get_leave_records->cl;
								$pl = $get_leave_records->last_year_pl + $pl;
								$total_holiday_working = $get_leave_records->last_year_co;
								$last_year_pl = $get_leave_records->last_year_pl;
							}
						}
						
						$pl_already = 0;
						$cl_already = 0;
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
									else if($val->category=="Comp Off"){
										$comp_off_already_month += 0.5;
									}
								}
							}
							$pl = $pl - $pl_already;
							$cl = $cl - $cl_already;
							
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
						else if($user->branch_location=='prayagraj'){
							$holiday_location = 4;
						}
						$today_date = date('Y-m-d');
						
						$holiday_array = array();					
						$get_holiday = Holiday::select('date','branch_id','type')->where('status', '1')->where('is_deleted', '0')->whereRaw("DATE(date) <= '$today_date' AND (location = 0 OR location = $holiday_location  )")->get();
						if(count($get_holiday) > 0){
							foreach($get_holiday as $get_holiday_val){
								if(!empty($get_holiday_val->branch_id)){
									$holiday_branch = json_decode($get_holiday_val->branch_id);
									if($get_holiday_val->type =='Employee'){
										if(!empty($user->id) && !empty($holiday_branch) && in_array($user->id, $holiday_branch)){
											array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
										}
									}
									else{
										if(!empty($user->branch_id) && !empty($holiday_branch) && in_array($user->branch_id, $holiday_branch)){
											array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
										}	
									}
								}
								else{
									array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
								}
								
							}
						}
						
						$months = array();
						$current_month = date('m');
						// $current_month = 12;
						
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
							
							$yr = $session;
							foreach($months as $key=>$mt){
								$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
								$first_date = strtotime($yr.'-'.$mt.'-01');
								$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);	
								
								$attendance_array = array();
									
								$first_date_get = date('Y-m-d',$first_date);
								$last_date_get = date('Y-m-d',$last_date);
								
								if(1){ /* For session 2023 */
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
													if($total_minute < 38.88){
														//44.44%
														//Absent
													}
													else if($total_minute < 66.66){
														$total_holiday_working +=0.5;
													}
													else{
														$total_holiday_working++;
													}
												}
												else if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
													$array_index = array_search($add_get_date, array_column($holiday_array, 'date'));
													if($total_minute < 38.88){
														
													}
													else if($total_minute < 66.66){
														$total_holiday_working +=0.5;
													}
													else{
														$total_holiday_working++;
													}
												}
												
											}				
											$first_date += 86400; 
											$getWorkSunday--;
										}
									}
									
								}
							}
							$total_holiday_working = $total_holiday_working - $comp_off_already_month;
						}
						else{
							$total_holiday_working = 0;
						}
						
						
						
						$leave_array['pl_already'] = $pl_already;
						$leave_array['cl_already'] = $cl_already;
						$leave_array['pending_pl'] = $pl;
						$leave_array['pending_cl'] = $cl;
						$leave_array['pending_sl'] = 0;
						$leave_array['pending_comp_off'] = $total_holiday_working;
						$leave_array['is_comp_off'] = $is_comp_off;
						$leave_array['last_year_pl'] = $last_year_pl;
						// $leave_array['is_comp_off'] = 0;

						// add by pc
						$leave_array['pending_sl'] = 'inactive';
						// $leave_array['pending_comp_off'] = 'inactive';
						
						$leaves['data']['uName']=$uName;
						$leaves['data']['pending_pl']=$pl;
						$leaves['data']['pending_cl']=$cl;
						$leaves['data']['register_id']=$register_id;
						$leaves['data']['pending_comp_off']=$total_holiday_working;
						$leave[]=$leaves['data'];
						// $data['data'] = $leave_array;
						
						
					}
				}			
            }
			
		}
		
		$pending_leaves=[]; 
		$uName='';
		$register_id='';
		//echo json_encode($leave);
		//echo "<pre>";print_r($leave);die();	

		$leave=json_decode(json_encode($leave));
		// print_r($leave); die;
		
		return view('admin.leave.get_pending_comp_off_all_users', compact('users','pending_leaves', 'uName','register_id','leave'));
		
		// print_R($leave); die();
		
	}
	public function leavecount(Request $request)
    {
		$logged_id    = Auth::user()->id;
		//Leave Calculation	
		$empID  =	$request->empCode;
		if($empID!=""){
			$userKey	=	User::where('register_id', "$empID")->first();
		}else{
			$userKey	=	User::where('id', $logged_id)->first();
		}
		
		
		if(!empty($userKey)){
			$uName	=	$userKey['name'];
			$register_id	=	$userKey['register_id'];
			$user_id	=	$userKey['id'];
			
			$url = env('APP_URL')."api/users/leave_types";
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
			// print_R($pending_leaves); die;

			$leave=[];
			
			return view('admin.leave.leavecount', compact('pending_leaves', 'uName','register_id','leave'));
		}else{
			//$pending_leaves = array();
			//$uName = "";
			//return view('admin.leave.leavecount', compact('pending_leaves', 'uName','register_id'));
			return redirect()->route('admin.leave.leavecount')->with('error', 'Exployee ID not exist');
		}
    }
	
	public function leavecountall(Request $request)
    {
		die('Not Used');
		$logged_id    = Auth::user()->id;
		//Leave Calculation	
		
		$userData	=	User::whereRaw('department_type != 34  AND register_id!="" AND status = "1" AND is_deleted = "0" ')->get();
		// echo "<pre>"; print_r($userData); die;
		if(count($userData) > 0 ){
			$allUsers = array();
			$all_user = "";
			foreach($userData as $key=>$userDetail){
				$user_id	=	$userDetail->id;
				$all_user .=$userDetail->id.",";
			}
			// echo $all_user; die;
				// $uName	=	$userDetail->name;
				// $register_id	=	$userDetail->register_id;
				
				// echo $user_id; die;
				$url = env('APP_URL')."api/users/leave_types_all";
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				$headers = array(
				   "Content-Type: application/x-www-form-urlencoded",
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

				$data = "user_id=$all_user";
				// print_r($data); die;

				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

				$resp = curl_exec($curl);
				curl_close($curl);
				$pending_leaves =  json_decode($resp);
				// echo "<pre>"; print_r($pending_leaves); die;
				if(!empty($pending_leaves)){
					
					if(!empty($pending_leaves->data)){
						foreach($pending_leaves->data as $key=>$user_data_all){
							$is_minues = false;
							if($user_data_all->pending_pl < 0){
								$is_minues = true;
							}
							if($user_data_all->pending_cl < 0){
								$is_minues = true;
							}
							
							if($is_minues){
								$allUsers[] = array(
												'register_id'=>$key,
												'leaves'=>$user_data_all
											);
											
								// DB::table('leave_records')->where('session', 2022)->update([ 'user_id' => $user_data_all->user_id]);
							}
						}
					}
					
				}
			
			
			return view('admin.leave.leavecountall', compact('allUsers'));
		}else{
			$allUsers = array();
			$uName = "";
			return view('admin.leave.leavecountall', compact('allUsers'));
			//return redirect()->route('admin.leave-count-view')->with('error', 'Something Went Wrong !');
		}
    }
	
	public function approved_leave(Request $request){
		die('Not Working');
		$login_id               = Auth::user()->id;
		return view('admin.leave.approved_leave');
	}

	public function update_approved_leave(Request $request){
		die('Working On');
		$login_id   = Auth::user()->id;
		$validatedData = $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
            'status' => 'required',
        ]);

		$emp_code_result = LeaveDetail::whereNotNull('category')->whereBetween('date',[$request->from_date, $request->to_date])->get();

		if(count($emp_code_result) > 0){
			foreach($emp_code_result as $emp_code_result_val){
				LeaveDetail::where('id', $emp_code_result_val->id)->update([ 'status' => $request->status,'updated_by'=>$login_id]);
				$this->maintain_history(Auth::user()->id, 'leave_details', $emp_code_result_val->id, $request->status.'_leave', json_encode([ 'status' => $request->status]));
			}
			return redirect()->route('admin.leave.approved-leave')->with('success', 'Leave Update Successfully');
        } else {
            return redirect()->route('admin.leave.approved-leave')->with('error', 'No Record Found');
        }
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
	
	public function leaveWages(Request $request)
    {
		return view('admin.leave.leave-wages');
	}
	
	public function leaveWagesTab(Request $request){
		$emp_id = Input::get('emp_id');
		$fdate = Input::get('fdate');
		$tdate = Input::get('tdate');
		
		$leave_wage = [];
		
		if(!empty($emp_id) || !empty($fdate) || !empty($tdate)){
			
			$validatedData = $request->validate([
				'emp_id' => 'required',
				'fdate' => 'required',
				'tdate' => 'required',
			]);
		
			$leave_wage = User::with('user_details')->where('users.status', '1')->where('users.is_deleted', '0');
			if(!empty($emp_id)){
				$leave_wage->where('users.id', $emp_id);
			}
			
			$leave_wage = $leave_wage->first(); 
		}
		else{
			 return redirect()->route('admin.leave.leave-wages')->with('error', 'All Fields Are Required');
		}
		return view('admin.leave.leave-wages-tab', compact('leave_wage','fdate','tdate'));
	}
	
	
	
	public function leave_full_detail(Request $request)
    {
		$search = Input::get('search');
		$department_id        = Input::get('department_id');
		$branch_id    = Input::get('branch_id');
		$session        = Input::get('session');
		$from_date        = Input::get('from_date');
		$to_date        = Input::get('to_date');
		
		// $from_date        = '2022-11-01';
		// $to_date        = '2022-11-30';
		
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		if($logged_role_id==29){
			
		}
		else if($logged_role_id == 21){
			$department_type  = Auth::user()->department_type;
			$department_id = $department_type;
		}
		else{
			echo "You can't access"; die;
		}
		
        
		
		
		if(empty($session)){
			$session = date('Y');
			// $session = 2022;
		}
		/* $user_id = 5453;
		$request->user_id = 5453;
		$result = app('App\Http\Controllers\Api\LeaveController')->leave_types($request);
		if(!empty($result->original['data'])){
			$total_pl = $result->original['data']['pending_pl'];
		} */
		$leaves = DB::table('users as u')
			->select('branches.id as branch_id','branches.branch_location as branch_location','u.total_time','u.comp_off_start_date','u.is_extra_working_salary','u.name','u.id','u.mobile','u.register_id','lr.pl','lr.cl','lr.last_year_pl','lr.last_year_co','lr.user_id','ld.emp_id',DB::raw("SUM(CASE WHEN category = 'PL' and type = 'Full Day' THEN 1 WHEN category = 'PL' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_pl,SUM(CASE WHEN category = 'CL' and type = 'Full Day' THEN 1 WHEN category = 'CL' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_cl,SUM(CASE WHEN category = 'Comp Off' and type = 'Full Day' THEN 1 WHEN category = 'Comp Off' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_co,ud.joining_date,ud.probation_from,u.inactive_date,u.status"))
			->leftJoin('userdetails as ud', 'u.id', '=', 'ud.user_id')
			->leftJoin('leave_records as lr', 'u.id', '=', 'lr.user_id')
			->leftJoin('leave_details as ld', 'ld.emp_id', '=', 'u.id')
			->leftJoin('userbranches', 'u.id', '=', 'userbranches.user_id')
			->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
			if(!empty($from_date) && !empty($to_date)){
				$leaves->whereRaw("(date(ud.joining_date) >= '$from_date' and date(ud.joining_date) <= '$to_date')");
			}
		
			if(!empty($branch_id)){
				$leaves->where('userbranches.branch_id', $branch_id);
			}
			if(!empty($department_id)){
				$leaves->where('u.department_type', $department_id);
			}
			
			// $leaves->whereRaw("month(ld.date) <= 5 "); // for manualy months only
			
			$leaves->where('lr.session', $session)
			->where('u.is_deleted', '0')
			->where('u.status', '1')
			// ->where('ld.emp_id', '1723')
			->whereRaw(" (u.name LIKE '%$search%' OR u.register_id LIKE '%$search%' OR mobile LIKE '%$search%') and year(ld.date) = $session and (ld.status='Approved' or ld.status='Pending') and userbranches.is_deleted='0' and userbranches.branch_id = (SELECT MAX(branch_id) FROM userbranches where user_id = u.id and is_deleted = '0' )")
			->groupBy('ld.emp_id');
		$get_data = $leaves->get();
		
		// echo "<pre>";print_r($get_data); die;
		
        return view('admin.leave.leave_full_detail', compact('get_data','session','logged_role_id'));
    }
	
	public function leave_full_detail_history(Request $request)
    {
		$user_id = $request->user_id;
		$session = $request->session;
		if(empty($session)){
			$session = date('Y');
			// $session = 2022;
		}
		if(!empty($user_id) && !empty($session)){
			$leaves = DB::table('users as u')
			->select('u.name','u.id','u.mobile','u.register_id','lr.pl','lr.cl','lr.user_id','ld.emp_id',DB::raw("SUM(CASE WHEN category = 'PL' and type = 'Full Day' THEN 1 WHEN category = 'PL' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_pl,SUM(CASE WHEN category = 'CL' and type = 'Full Day' THEN 1 WHEN category = 'CL' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_cl,SUM(CASE WHEN category = 'Comp Off' and type = 'Full Day' THEN 1 WHEN category = 'Comp Off' and type != 'Full Day' THEN 0.5 ELSE 0 END) as taken_co,MONTH(ld.date) month"))
			->leftJoin('leave_records as lr', 'u.id', '=', 'lr.user_id')
			->leftJoin('leave_details as ld', 'ld.emp_id', '=', 'u.id')
			->where('lr.session', "$session")
			->where('ld.emp_id', $user_id)
			->whereRaw("year(ld.date) = $session and (ld.status='Approved' or ld.status='Pending')")
			->groupBy('month');
			$get_data = $leaves->get();
			$html = "";
			$empcode = "";
			if(count($get_data) > 0){
				$empcode = $get_data[0]->name . " ( ".$get_data[0]->register_id.")";
				foreach($get_data as $val){
					$html .="<tr>";			
					$html .="<td>$val->month</td>";				
					$html .="<td><table style='width: 100%;'><tr><td style='width: 50%;'>PL</td><td>$val->taken_pl</td></tr><tr><td style='width: 50%;'>CL</td><td>$val->taken_cl</td></tr><tr><td style='width: 50%;'>CO</td><td>$val->taken_co</td></tr></table></td>";
					$html .="</tr>";
				}
			}
			return response(['status' => true, 'html' => $html,'empcode'=>$empcode], 200);
		}
		else{
			return response(['status' => false, 'message' => 'Something went wrong'], 200);
		}
		
		
    }
	
	public function paternity_leave(Request $request){
		
        $employees_list = User::where('status','1')->where('is_deleted','0')->get();
		
        return view('admin.leave.paternity_leave', compact('employees_list'));
	
	}
	
	
	public function storPaternityLeave(Request $request){
		 $logged_id       = Auth::user()->id;
		 
	 // dd($logged_id);
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'paternity_leave' => 'required',
        ]);
		
		
		
		
		  $paternity_leave = DB::table('leave_records')->select('user_id','session')->where('user_id',$request->employee_id)->where('session',$request->session)->first();
		  
		  
		  if(isset($paternity_leave)){
			  
				$data = array(
					"paternity_leave"  => $request['paternity_leave'],
					"updated_at" => date('Y-m-d H:i:s')
				);
				
				$response = DB::table('leave_records')->where('user_id',$request->employee_id)->where('session',$request->session)->update($data);
				
					$data1 = array(
			
			
						'login_id' => $logged_id,
						'employee_id' =>$request->employee_id,
						'session'=>$request->session,
						'paternity_leave'  => $request->paternity_leave,
						);
						
						// dd($data1);
						DB::table('paternity_leave_history')->insert($data1);
				
				
			  
				return redirect("admin/paternity-leave")->with('success', 'Paternity Leave updated successfully!');
			  
		  }
		  else{
			  
			    return redirect("admin/paternity-leave")->with('error', 'Woops, Something is wrong!');
			  
		  }
    }
	
	public function paternity_leave_list(){
		
		$paternity_records = DB::table('leave_records')
            ->join('users', 'users.id', '=', 'leave_records.user_id')
            ->where('leave_records.paternity_leave', '>', 0 )
			->orderBy('leave_records.id', 'DESC')
            ->get();
				
        return view('admin.leave.paternity_leave_list', compact('paternity_records'));
	
	}
	
}
