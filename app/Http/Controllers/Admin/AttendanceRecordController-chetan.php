<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use Input;
use Excel;
use App\NewTask;
use App\Leave;
use App\LeaveDetail;
use App\Exports\AttendanceRecordExport;
use Auth;
use DB;
use Illuminate\Pagination\Paginator;
use App\AttendanceNew;
use DataTables;
use DateTime;
use App\Holiday;
use App\Userdetails;

class AttendanceRecordController extends Controller
{
	
	public function attendencerecord(Request $request){
		$mt = date('m');
		//$mt="02";
		$yr = date('Y');
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
			$year_wise_month = explode('-',$params['year_wise_month']);
			
			$yr = $year_wise_month[0];
			$mt = $year_wise_month[1];
		}
		// print_r($params); die;
		
		$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$logid = array();
        $allDepartmentTypes  = $this->allDepartmentTypes();
        $allBranches  = $this->allBranches();
		return view('admin.attendance.attendancerecord', compact('allDepartmentTypes','allBranches','getWorkSunday'));
	}
	
	public function attendencerecorddetail(Request $request){		
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		// $fdate               = $request->fdate;
        // $tdate               = $request->tdate;
        $year_wise_month     = $request->year_wise_month;
		$status              = $request->status;
        
		$responseArray = $this->calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status);
		
		//echo "<pre>";print_R($responseArray); die;
		return DataTables::of($responseArray)->make(true);

		
	}
	
	
	public function attendencerecord_download_excel(Request $request){
		$logged_role_id      = Auth::user()->role_id;
		$logged_id           = Auth::user()->id;
		$name                = $request->name;
		$department_type     = $request->department_type;
		$branch_id           = $request->branch_id;
		// $fdate               = $request->fdate;
        // $tdate               = $request->tdate;
        $year_wise_month     = $request->year_wise_month;
		$status              = $request->status;
        
		$responseArray = $this->calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status);
		
		// echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new AttendanceRecordExport($responseArray), 'AttendanceRecordData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status=NULL){
		
		$users= NewTask::getEmployeeByLogID($logged_id,'attendance',$year_wise_month);
        
		// $usr          = User::where('status', 1);
		
		$new_whereCond    = 'users.register_id>=1001 AND users.register_id!=""';
		$new_whereCondAtt = '';
		$whereCondAtt = '';
		if(!empty($name) && !empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type' and (users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($name)){
			$new_whereCond .= " AND ((users.name LIKE '%$name%' or users.register_id LIKE '%$name%'))";
		}
		else if(!empty($department_type)){
			$new_whereCond .= " AND (users.department_type = '$department_type')";
		}
		if(!empty($branch_id)){
			//$new_whereCond .= " AND (userdetails.branch_id = '$branch_id')";
			$new_whereCond .= " AND (userbranches.branch_id = '$branch_id')";
		}
		
		if($status != ''){
			$new_whereCond .= " AND (users.status = '$status')";
			
			if($status == 0){
				$CurrentMonth = date('n',strtotime($year_wise_month));
				$new_whereCond .= " AND MONTH(users.inactive_date)= $CurrentMonth";
			}
		}
		else{
			$CurrentMonth = date('n',strtotime($year_wise_month));
			$new_whereCond .= " AND (users.status = '1' OR MONTH(users.inactive_date)= $CurrentMonth)";
		}
		$CurrentMonth = date('n',strtotime($year_wise_month));
		$new_whereCond .= " AND (users.is_deleted = '0' OR MONTH(users.delete_date)= $CurrentMonth)";
		
		/* if(!empty($fdate)){
			$new_whereCondAtt .= ' AND attendance_new.date >= "'.$fdate.'" AND attendance_new.date <= "'.$tdate.'"';
			$whereCondAtt .= ' AND attendance.date = "'.$fdate.'" AND attendance.date = "'.$tdate.'"';
		}
		else{
			$today = date("Y-m-d");
			$new_whereCondAtt .= ' AND attendance_new.date >= "'.$today.'" AND attendance_new.date <= "'.$today.'"';
			$whereCondAtt .= ' AND attendance.date >= "'.$today.'" AND attendance.date <= "'.$today.'"';
		} */
		
		$employeeArray= array();
		$usr= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		//$new_whereCond .= " AND (users.id IN (5441))";
		
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.register_id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name,userdetails.degination as designation_name,userdetails.net_salary"));
		// $attendance->select(\DB::raw("users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance WHERE attendance.emp_id=users.id $whereCondAtt) AS present"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
		$attendance->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
		//$attendance->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->whereRaw($new_whereCond);
		$attendance->orderBy('name');
		$attendance->groupBy(['userbranches.user_id']);
		$array1 = $attendance->get();
		
		// echo "<pre>"; print_R($array1); die;		
		$comman_result = array();
		
		$year_wise_month = explode('-',$year_wise_month);
		
		$yr = $year_wise_month[0];
		$mt = $year_wise_month[1];
		$getWorkSunday1 = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		/* $month_with_day = array(
							'1' => cal_days_in_month(CAL_GREGORIAN, 1, date('Y')),
							'2' => cal_days_in_month(CAL_GREGORIAN, 2, date('Y')),
							'3' => cal_days_in_month(CAL_GREGORIAN, 3, date('Y')),
							'4' => cal_days_in_month(CAL_GREGORIAN, 4, date('Y')),
							'5' => cal_days_in_month(CAL_GREGORIAN, 5, date('Y')),
							'6' => cal_days_in_month(CAL_GREGORIAN, 6, date('Y')),
							'7' => cal_days_in_month(CAL_GREGORIAN, 7, date('Y')),
							'8' => cal_days_in_month(CAL_GREGORIAN, 8, date('Y')),
							'9' => cal_days_in_month(CAL_GREGORIAN, 9, date('Y')),
							'10' => cal_days_in_month(CAL_GREGORIAN, 10, date('Y')),
							'11' => cal_days_in_month(CAL_GREGORIAN, 11, date('Y')),
							'12' => cal_days_in_month(CAL_GREGORIAN, 12, date('Y')),
							); */
		
		if(count($array1) > 0){
			foreach($array1 as $val){
				$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);
				$user_id = $val->id;
				//$previous_year_month = date("Y-m", strtotime ( '-1 month' , ( $first_date ) )) ;
				$previous_year_month = date("Y-m", $first_date);
				$leave_balance = 0;
				$current_month_wise = strtotime(date('Y-m').'-'.cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'))); // date('n') = month without 0
				
				/* if(strtotime($yr.'-'.$mt.'-'.date('d')) == $current_month_wise){ 
					$previouse_leave_balance = DB::table('leave_balance')
												->selectRaw("SUM(extend_leave) as total_extend_leave")
												->where('user_id', $user_id)
												->where('year_month', $previous_year_month)
												->orWhere('year_month', date('Y-m'))
												->first();
							
					//echo '<pre>'; print_r($previouse_leave_balance);die;
					if(!empty($previouse_leave_balance)){
						$leave_balance = $previouse_leave_balance->total_extend_leave;
					}		
				}
				else{
					$previouse_leave_balance = DB::table('leave_balance')
							->where('user_id', $user_id)
							->where('year_month', $previous_year_month)
							->first();
					if(!empty($previouse_leave_balance)){
						$leave_balance = $previouse_leave_balance->extend_leave;
					}		
					
				} */
				
				
				
				$user_array = array(); 
				$user_array['id'] = $user_id;
				$user_array['name'] = $val->name;
				$user_array['register_id'] = $val->register_id;
				$user_array['email'] = $val->email;
				$user_array['mobile'] = $val->mobile;
				$user_array['branch_name'] = $val->branch_name;
				$user_array['departments_name'] = $val->departments_name;
				$user_array['designation_name'] = $val->designation_name;
				$user_array['net_salary'] = $val->net_salary;
				
				
				$attendance_array = array();
				
				$first_date_get = date('Y-m-d',$first_date);
				$last_date_get = date('Y-m-d',$last_date);

				$attendance = Attendance::query();
				$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
				$attendance->where('emp_id', $user_id);
				$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
				$attendance = $attendance->groupBy('date');
				
				
				$attendancenew = AttendanceNew::query();
				$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
				$attendancenew->where('emp_id', $user_id);
				$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
				$attendancenew = $attendancenew->groupBy('date');				
				
				$comman = $attendancenew->union($attendance);
				$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
						   ->mergeBindings($comman->getQuery())
						   ->groupBy('comman.emp_id')
						   ->groupBy('comman.date')
						   ->get();
				
				if(count($comman_result1) > 0){
					$attendance_array = json_decode(json_encode($comman_result1),true);
				}
				// echo "<pre>"; print_r($attendance_array); die;
				$date_array    = array();
				$holiday_array = array();
				$i = 1;
				$total_present = 0;
				$total_absent = 0;
				$total_present_half = 0;
				$total_holiday_working = 0;
				$total_week_off = 0;
				$count_week_days = 0;
				$monday_to_sunday_present = 0;
				$actual_paid = 0;
				$day_without_sunday = 0;
				$holiday = 0;
				$optional_holiday = 0;
				$total_pl = 0;
				$total_cl = 0;
				$total_sl = 0;
				$total_co = 0; //Comp Off
				
				$get_holiday = Holiday::select('date')->where('status', '1')->where('is_deleted', '0')->get();
				if(count($get_holiday) > 0){
					foreach($get_holiday as $get_holiday_val){
						array_push($holiday_array, $get_holiday_val->date);
					}
				}


				//Chetan 
				$usData 		= 	Userdetails::where('user_id',$user_id)->first();				
				$join_date 	=	$usData['joining_date'];								
				$net_salary 	=	$usData['net_salary'];								
				$mindate 	 	= 	date('Y-m-d');
				
				
				// echo "<pre>"; print_R($holiday_array); die;
				
				while($getWorkSunday> 0){
					$total_minute = 0;
					$workday = date("D", $first_date);
					
					if($workday=="Mon"){
						$count_week_days = 1;
						
					}
					else{
						$count_week_days++;
					}
					
					$ii = $i++;
					$add_get_date  = date('Y-m-d', $first_date);  
					
					//Holiday					
					$oLeave 		= 	Holiday::where('type', 'Optional')->get()->count();				
					if($add_get_date < $join_date){
						$date_array[$ii] = 'A';
						$total_absent++;
					}else{			
						if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
							$monday_to_sunday_present++;
							
							$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
							
							$in_time = $attendance_array[$array_index]['in_time'];
							$out_time = $attendance_array[$array_index]['out_time'];
							if($add_get_date==date('Y-m-d')){ //2021-07-31
								if(!empty($in_time)){
									
									$date_array[$ii] = 'P';
									$total_present++;
								}
								else{
									$date_array[$ii] = 'A';
									$total_absent++;
								}
							}
							else{
								if(!empty($in_time) && !empty($out_time)){
									$in_time = date("h:i A", strtotime($in_time));
									$out_time = date("h:i A", strtotime($out_time));
									$intime = new DateTime($in_time);
									$outtime = new DateTime($out_time);
									$interval = $intime->diff($outtime);
									$hours = $interval->format('%H');
									$minute = $interval->format('%I');
									$total_minute = ($hours*60)+$minute;
								}
								
								if($workday == "Sun"){
									
									if($total_minute <= 240){
										//240 Mint = 4 hour
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
									else{
										$total_holiday_working++;
										$date_array[$ii] = 'HW';
										
										if($count_week_days >= 6){
											if($monday_to_sunday_present >=3 ){							
												$total_week_off++;
											}
										}
										else{							
											$total_week_off++;
										}
									}
									
									$monday_to_sunday_present = 0;
									
								}
								else if(in_array($add_get_date, $holiday_array)){
									
									$check_holiday  = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
									$is_optional 	= false;
									$is_public 		= false;
									
									if($check_holiday->type=="Optional"){
										$is_optional = true;
									}
									
									if($check_holiday->type=="Public"){
										$is_public = true;
									}
									
									if($total_minute >= 360){
										if($is_optional){
											$date_array[$ii] = 'P';
											$total_present++;
										}
										else{
											$total_holiday_working++;
											$date_array[$ii] = 'HW';
											
											if($count_week_days >= 6){
												if($monday_to_sunday_present >=3 ){							
													$holiday++;
												}
											}
											else{							
												$holiday++;
											}
										}
										
									}
									else{
										if($is_optional){										
											/*
											if($count_week_days >= 6){
												if($monday_to_sunday_present >=3 ){
													$date_array[$ii] = 'OH';
													$holiday++;
												}
												else{
													$date_array[$ii] = 'A';							
													$total_absent++;
												}
											}
											else{
												$date_array[$ii] = 'OH';
												$holiday++;
											}
											*/
											if($oLeave > 2){									
												$date_array[$ii] = 'A';							
												$total_absent++;
											}else{
												$date_array[$ii] = "OH";
												$holiday++;
											}
											
										}else if($is_public){
											if($net_salary > 20000){
												$date_array[$ii] =	"Comp Off";
											}else{
												$date_array[$ii] = 'P';
												$total_present++;
											}
										}else{
											if($count_week_days >= 6){
												if($monday_to_sunday_present >=3 ){
													$date_array[$ii] = 'H';
													$holiday++;
												}
												else{
													$date_array[$ii] = 'A';							
													$total_absent++;
												}
											}
											else{
												$date_array[$ii] = 'H';
												$holiday++;
											}
										}
									}
								}
								else{
									if($total_minute <= 120){
										//120 Mint = 2 hour
										$date_array[$ii] = 'A';
										$total_absent++;
									}
									else if($total_minute <= 360){
										//360 Mint = 6 hour
										$date_array[$ii] = 'PH';
										$total_present_half++;
										
										$check_approved = DB::table('leave_details')->where('status','Approved')->where('emp_id',$user_id)->where('date',$add_get_date)->first();
										if(!empty($check_approved)){
											if($check_approved->category=="PL"){
												$date_array[$ii] = 'PH/PL';
												$total_pl +=0.5;
											}
											else if($check_approved->category=="CL"){
												$date_array[$ii] ="PH/CL";
												$total_cl +=0.5;
											}
											else if($check_approved->category=="SL"){
												$date_array[$ii] ="PH/SL";
												$total_sl +=0.5;
											}
											else if($check_approved->category=="Comp Off"){
												$date_array[$ii] ="PH/CO";
												$total_co +=0.5;
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
									}
								}
								else{
									$date_array[$ii] = 'WO';							
									$total_week_off++;
								}
								
								$monday_to_sunday_present = 0;
								
							}						
							else if(in_array($add_get_date, $holiday_array)){
								if($oLeave > 2){									
									$date_array[$ii] = 'A';							
									$total_absent++;
								}else{
									$date_array[$ii] = "OH";
									$holiday++;
								}
								
								
								/*
								$check_holiday = Holiday::select('type')->whereRaw("DATE(date) = '$add_get_date'")->where('status', '1')->where('is_deleted', '0')->first();
								$day_type = "H";
								$is_optional = false;
								if($check_holiday->type=="Optional"){
									$day_type = "OH";
								}							
								$date_array[$ii] = "$day_type";
								$holiday++;
								
								if($count_week_days >= 6){
									if($monday_to_sunday_present >=3 ){
										$date_array[$ii] = "$day_type";
										$holiday++;
									}
									else{
										$date_array[$ii] = 'A';							
										$total_absent++;
									}
								}
								else{
									$date_array[$ii] = "$day_type";
									$holiday++;
								} */
							}
							else{
								if($add_get_date == '2021-08-30'){
									$date_array[$ii] = 'OH';
									$holiday++;
								}
								else{
									$date_array[$ii] = 'A';
									$total_absent++;
								}
							}
						}
					}
					
					if($date_array[$ii]=='A'){
						// echo $add_get_date; die;
						$check_approved = DB::table('leave_details')->where('status','Approved')->where('emp_id',$user_id)->where('date',$add_get_date)->first();
						if(!empty($check_approved)){
							if($check_approved->category=="PL"){
								$date_array[$ii] = "PL";
								$total_absent--;
								$total_pl++;
							}
							else if($check_approved->category=="CL"){
								$date_array[$ii] ="CL";
								$total_absent--;
								$total_cl++;
							}
							else if($check_approved->category=="SL"){
								$date_array[$ii] ="SL";
								$total_absent--;
								$total_sl++;
							}
							else if($check_approved->category=="Comp Off"){
								$date_array[$ii] ="CO";
								$total_absent--;
								$total_co++;
							}
							else if($check_approved->category=="LWP"){
								$date_array[$ii] ="LWP";
							}
							
						}
					}					
					
					if($workday != "Sun"){
						$day_without_sunday++;
					}	
					
					
					$first_date += 86400; 
					$getWorkSunday--;
					
					
					
				}
				
				
			
				
				if($total_present_half > 0){
					$total_present_half = ($total_present_half/2);
				}
				
				
				/*if(!empty($val->net_salary) && $val->net_salary > 20000){ 
				
					$previouse_leave_balance = DB::table('leave_balance')
								->where('user_id', $user_id)
								->where('year_month', $previous_year_month)
								->first();
					if(!empty($previouse_leave_balance)){
						$leave_balance = $previouse_leave_balance->extend_leave;
					}
				
				
					if($total_present == $day_without_sunday){
						if(!empty($total_holiday_working)){
							$actual_paid = $total_present;
							$leave_balance = $leave_balance+$total_holiday_working;
						}
						else{
							$actual_paid = $total_present;
						}
						
					}
					else if($total_present != $day_without_sunday){
						if($leave_balance >= ($total_absent+$total_present_half)){
							
							if(!empty($total_holiday_working)){
								$actual_paid = ($total_present+($total_absent+$total_present_half));
								$leave_balance = ($leave_balance-($total_absent+$total_present_half))+$total_holiday_working;
							}
							else{
								$actual_paid = ($total_present+($total_absent+$total_present_half));
								$leave_balance = ($leave_balance-($total_absent+$total_present_half));
							}
							
						}
						else if($leave_balance < ($total_absent+$total_present_half)){
							
							if(!empty($total_holiday_working)){ 
								$actual_paid = ($total_present+$leave_balance)+$total_holiday_working;
								$leave_balance = $leave_balance-($total_absent+$total_present_half) > 0 ? $leave_balance-($total_absent+$total_present_half)+$total_holiday_working : 0;
							}
							else{
								$actual_paid = ($total_present+$leave_balance);
								$leave_balance = $leave_balance-($total_absent+$total_present_half) > 0 ? $leave_balance-($total_absent+$total_present_half) : 0;
							}
							
							
						}
					}
				}
				else{
					$actual_paid = $total_present+$total_holiday_working+$holiday;
				}*/
				
				$actual_paid = $total_present + $total_present_half + $total_holiday_working + $total_week_off + $holiday + $total_pl + $total_cl + $total_sl;
				
				$user_array['date_array'] = $date_array;
				$user_array['total_present'] = ($total_present+$total_present_half);
				$user_array['total_absent'] = ($total_absent+$total_present_half);
				$user_array['total_present_half'] = $total_present_half;
				$user_array['total_holiday_working'] = $total_holiday_working;
				$user_array['total_week_off'] = $total_week_off + $holiday;
				$user_array['total_month_days'] = $getWorkSunday1;
				$user_array['leave_balance']    = $leave_balance;
				$user_array['actual_paid']    = $actual_paid;
				$comman_result[] = $user_array;
				
				// echo  "<pre>"; print_r($comman_result); die;
				
			}
		}
		
		// print_r($comman_result); die;
		$responseArray = array();
		 
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){
				$responseArray[$key]['name']   = $valAtt['name'];
				$responseArray[$key]['register_id']   = $valAtt['register_id'];
				$responseArray[$key]['branch_name']      = $valAtt['branch_name'] ? $valAtt['branch_name'] : '';
				$responseArray[$key]['departments_name'] = $valAtt['departments_name'] ? $valAtt['departments_name'] : '';
				$responseArray[$key]['designation_name']  = $valAtt['designation_name'];
				$responseArray[$key]['mobile'] = $valAtt['mobile'];
				$responseArray[$key]['total_present'] = $valAtt['total_present'];
				$responseArray[$key]['total_absent'] = $valAtt['total_absent'];
				$responseArray[$key]['total_present_half'] = $valAtt['total_present_half'];
				$responseArray[$key]['total_holiday_working'] = $valAtt['total_holiday_working'];
				$responseArray[$key]['total_week_off'] = $valAtt['total_week_off'];
				$responseArray[$key]['leave_balance'] = $valAtt['leave_balance']; 
				$responseArray[$key]['actual_paid'] = $valAtt['actual_paid']; 
				
				if(count($valAtt['date_array'])){
					foreach($valAtt['date_array'] as $key1=>$dateVal){
						$responseArray[$key][$key1] = $dateVal;
					}
				}
				$responseArray[$key]['total_month_days'] = $valAtt['total_month_days'];
				
			}
				
		}
		return $responseArray;
	}
	
    
}
