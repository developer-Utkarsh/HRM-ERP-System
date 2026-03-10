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
			// $year_wise_month = explode('-',$params['year_wise_month']);
			
			// $yr = $year_wise_month[0];
			// $mt = $year_wise_month[1];
		}
		if(!empty($request->start_date) && !empty($request->end_date)){
			$start_date = $request->start_date;
			$end_date = $request->end_date;
			$start_from = new DateTime($start_date);
			$end_to = new DateTime($end_date);
			
			$interval = $start_from->diff($end_to);
			$getWorkSunday =  $interval->days;
		}
		else{
			$start_date = $yr.'-'.$mt.'-01';
			$start_date =  date('Y-m-27', strtotime('-1 month', strtotime($start_date)));
			
			// $end_date = date("Y-m-t", strtotime($start_date));
			$end_date =  date("Y-m-26",strtotime($yr.'-'.$mt.'-26'));
			$start_from = new DateTime($start_date);
			$end_to = new DateTime($end_date);
			
			$interval = $start_from->diff($end_to);
			$getWorkSunday =  $interval->days;
		}
		
		$getWorkSunday = $getWorkSunday + 1;
		
		if($getWorkSunday > 32){ die;
			return redirect()->back()->with('error', 'Date difference should not be greater than 31 days.');
		}
		else if($getWorkSunday < 28){
			return redirect()->back()->with('error', 'Date difference should be minimum 1 month.');
		}
		
		
		
		// $getWorkSunday 			= 	cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		// print_r($getWorkSunday); die;
		
		$logged_role_id  		=	Auth::user()->role_id;
		$logged_id       		=	Auth::user()->id;
		
		$logid 					=	array();
        $allDepartmentTypes  	=	$this->allDepartmentTypes();
        $allBranches  			=	$this->allBranches();
		return view('admin.attendance.attendancerecord', compact('allDepartmentTypes','allBranches','getWorkSunday','start_from','end_to','start_date','end_date'));
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
		$start_date              = $request->start_date;
		$end_date              = $request->end_date;
        
		$responseArray = $this->calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status,$start_date,$end_date);
		
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
		$start_date              = $request->start_date;
		$end_date              = $request->end_date;
        
		$responseArray = $this->calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status,$start_date,$end_date);
		
		// echo '<pre>'; print_r($responseArray);die;
        if(count($responseArray) > 0){
            return Excel::download(new AttendanceRecordExport($responseArray), 'AttendanceRecordData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function calculate_attendance($logged_role_id,$logged_id,$name,$department_type,$branch_id,$year_wise_month,$status=NULL,$start_date,$end_date){
		
		
		/* $month_year_to_days = explode('-',$year_wise_month);
		
		$yr = $month_year_to_days[0];
		$mt = $month_year_to_days[1];
		
		$getWorkSunday1 = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		$first_date_of_month = $yr.'-'.$mt.'-01';
		$last_date_of_month = $yr.'-'.$mt.'-31'; */
		
		$first_date_of_month = date('Y-m-d',strtotime($start_date));
		$last_date_of_month = date('Y-m-d',strtotime($end_date));
		
		$users= NewTask::getEmployeeByLogID($logged_id,'attendance',$year_wise_month);
        
		// $usr          = User::where('status', 1);
		
		//(users.register_id>=1001 or users.register_id=0934) AND 
		$new_whereCond    = 'users.register_id!=""';
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
				// $CurrentMonth = date('n',strtotime($year_wise_month));
				$new_whereCond .= " AND DATE(users.reason_date) >= '$first_date_of_month'";
			}
		}
		else{
			// $CurrentMonth = date('n',strtotime($year_wise_month));
			$new_whereCond .= " AND (users.status = '1' OR DATE(users.reason_date) >= '$first_date_of_month')";
		}
		
		
		// $CurrentMonth = date('n',strtotime($year_wise_month));
		$new_whereCond .= " AND (users.is_deleted = '0' OR DATE(users.delete_date) >= '$first_date_of_month')";
		
		$new_whereCond .= " AND DATE(userdetails.joining_date) <= '$last_date_of_month'";
		
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
		$usr	= $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$new_whereCond .= " AND (users.id IN ($usr))";
		//$new_whereCond .= " AND (users.id IN (5441))";
		// echo $new_whereCond; die;
		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.register_id,users.name as name,users.email as email,users.mobile as mobile, branches.id as branch_id, branches.name as branch_name,branches.branch_location as branch_location, departments.name as departments_name,userdetails.degination as designation_name,userdetails.net_salary,userdetails.joining_date,users.is_extra_working_salary,users.total_time,userdetails.fname,userdetails.dob,users.reason_date,users.inactive_date,users.status as user_status,userdetails.esi_amount,userdetails.is_esi,userdetails.is_pf,userdetails.esic_no,userdetails.uan_no,userdetails.pf_amount,userdetails.tds,userdetails.account_number,userdetails.ifsc_code,userdetails.pan_no,attendance_additional_days.days as additional_days"));
		// $attendance->select(\DB::raw("users.id as id,users.name as name,users.email as email,users.mobile as mobile, branches.name as branch_name, departments.name as departments_name, (SELECT COUNT(*) FROM attendance WHERE attendance.emp_id=users.id $whereCondAtt) AS present"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		$attendance->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id');
		$attendance->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id');
		//$attendance->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		$attendance->leftJoin('departments', 'users.department_type', '=', 'departments.id');
		$attendance->leftJoin('attendance_additional_days', function($join) use ($year_wise_month) {
			$join->on('attendance_additional_days.user_id', '=', 'users.id')->where('monthyear','=', "$year_wise_month");
		});
		$attendance->whereRaw($new_whereCond);
		$attendance->orderBy('name');
		$attendance->groupBy(['userbranches.user_id']);
		$array1 = $attendance->get();
		// echo count($array1); die;	 			
		$comman_result = array();
		
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
		// $cMonth = date('m');
		
		if(count($array1) > 0){
			foreach($array1 as $val){
				$start_from = new DateTime($first_date_of_month);
				$end_to = new DateTime($last_date_of_month);
				
				$interval = $start_from->diff($end_to);
				$getWorkSunday =  $interval->days + 1;
				$getWorkSunday1 =  $interval->days + 1;
				// $filter_year_month = $yr.'-'.$mt;
				// $getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				
				// $first_date = strtotime($yr.'-'.$mt.'-01');
				// $last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);
				
				$first_date = strtotime($first_date_of_month);
				$last_date = strtotime($last_date_of_month);
				
				
				
				
				//Chetan				
				
				/* if($cMonth==$mt){
					$last_date_get = date('Y-m-d');
				}else{
					$last_date_get = date('Y-m-d',$last_date);
				} */
				$user_id = $val->id;
				$leave_balance = 0;
				
				//$previous_year_month = date("Y-m", strtotime ( '-1 month' , ( $first_date ) )) ;
				/* $previous_year_month = date("Y-m", $first_date);
				
				$current_month_wise = strtotime(date('Y-m').'-'.cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'))); // date('n') = month without 0
				
				if(strtotime($yr.'-'.$mt.'-'.date('d')) == $current_month_wise){ 
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
				$user_array['branch_location'] = $val->branch_location;
				
				$user_array['is_extra_working_salary'] = $val->is_extra_working_salary;
				$user_array['fname'] = $val->fname;
				$user_array['dob'] = $val->dob;
				$user_array['joining_date'] = $val->joining_date;
				$user_array['reason_date'] = $val->reason_date;
				$user_array['esi_amount'] = $val->esi_amount;
				$user_array['pf_amount'] = $val->pf_amount;
				$user_array['is_esi'] = $val->is_esi;
				$user_array['is_pf'] = $val->is_pf;
				$user_array['esic_no'] = $val->esic_no;
				$user_array['uan_no'] = $val->uan_no;
				$user_array['tds'] = $val->tds;
				$user_array['account_number'] = $val->account_number;
				$user_array['ifsc_code'] = $val->ifsc_code;
				// $user_array['filter_year_month'] = $filter_year_month;
				$user_array['pan_no'] = $val->pan_no;
				$user_array['additional_days'] = ($val->additional_days) ? $val->additional_days : 0;
				$inactive_date = $val->inactive_date;
				$reason_date = $val->reason_date;
				$user_status = $val->user_status;
				
				$attendance_array = array();
				
				$first_date_get = date('Y-m-d',$first_date);
				$last_date_get = date('Y-m-d',$last_date);
				
				
				

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
					
					 // echo "<pre>"; print_r($attendance_array); die;
				
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
				$total_co = 0; //Comp Off		
				
				// if(count($comman_result1) > 0){
					
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
					
					$users_id_jan_2022 = array(934,933,1551,1555,1564,1565,1566,1567,1568,1569,1572,1573,1576,1577,1578,1580,1581,1584,1645,1656,1678,1716,1723,5119,1791,1885,1887,1889,1892,1893,1908,1911,5106,5107,5130,5422,5436,5437,5442,5443,1886,1819);
					
					
					
					$users_id_10_nov_2023 = array(5501,1621,1749,1752,1833,5549,5554,5557,5581,5648,5695,5699,5747,5755,5758,5816,5818,5820,5844,5845,5847,5852,5863,5902,5907,5939,5967,5968,5979,5984,5994,6000,6001,6056,6062,6096,6097,6098,6099,6128,6135,6212,6213,6215,6219,6221,6223,6224,6226,6227,6233,6242,6256,6385,6445,6585,6592,6595,6597,6662,6663,6664,6701,6703,6719,6755,6759,6927,6932,6998,7004,7006,7007,7016,7127,7252,7319,7356,7428,7504,7505,7556,7577,7625,7626,7676,7723,7804,7810,7829,7830,7831,7837,7843,7856,7955,7986,7987,7998,8014,8035,8040,1067,1728,5424,5460,6109,6307,6435,6473,6483,6484,6499,6541,6606,6742,6770,6783,6869,6870,7192,7306,7370,7491,7569,7693,7726,7821,7845,7923,7932,8063,1011,1012,1036,1040,1052,1054,1064,1145,1575,1577,1610,5447,1698,1719,1728,1784,1796,1800,1803,1946,5067,5174,5402,5284,5312,5328,5343,5419,5460,5639,5656,5698,6049,6051,6115,6119,6137,6152,6173,6176,6225,6250,6252,6296,6355,6358,6378,6402,6458,6459,6477,6607,6642,6709,6861,6875,7077,7260,7263,7298,7317,7330,7370,7462,7604,7606,7630,7646,7648,7686,7691,7693,7798,7799,7820,7844,7857,7875,7892,7923,7932,7933,7937,7939,8056,8076,8087,4871,2654,4692,1012,4834,2602,4754,4530,3014,3003,2111,1036,3235,3333,4643,4748,2013,2056,3018,1040,2219,3315,2236,4747,3498,3258,3294,3331,4821,1052,2149,4108,4758,2918,4306,1575,4525,3256,3460,2254,1064,2526,2976,1698,1978,2206,3987,4655,1803,2566,2300,3323,4074,1636,4175,3716,4884,2304,1800,3212,1643,3164,4920,4760,2313,4622,4759,1719,3153,3077,2510,4895,2095,1784,3063,3463,1577,3037,3111,1728,4162,3109,2745,3597,4448,4751,3589,4749,3215,4642,1607,4105,4746,4215,4911,2511,1145,4490);
							
					$users_id_15_nov_2023 = array(5501,1621,1749,1752,1833,5549,5554,5557,5581,5648,5695,5699,5747,5755,5758,5816,5818,5820,5844,5845,5847,5852,5863,5902,5907,5939,5967,5968,5979,5984,5994,6000,6001,6056,6062,6096,6097,6098,6099,6128,6135,6212,6213,6215,6219,6221,6223,6224,6226,6227,6233,6242,6256,6385,6445,6585,6592,6595,6597,6662,6663,6664,6701,6703,6719,6755,6759,6927,6932,6998,7004,7006,7007,7016,7127,7252,7319,7356,7428,7504,7505,7556,7577,7625,7626,7676,7723,7804,7810,7829,7830,7831,7837,7843,7856,7955,7986,7987,7998,8014,8035,8040,1067,1728,5424,5460,6109,6307,6435,6473,6483,6484,6499,6541,6606,6742,6770,6783,6869,6870,7192,7306,7370,7491,7569,7693,7726,7821,7845,7923,7932,8063,1012,1036,1040,1052,1064,1145,1575,1577,1607,1636,5447,1698,1719,1728,1784,1800,1803,5067,5006,5135,5174,5221,5181,5312,5328,5343,5361,5451,5419,5428,5639,5641,5656,5698,5734,5786,5877,6051,6109,6137,6148,6152,6176,6204,6218,6250,6252,6296,6307,6355,6358,6378,6399,6402,6438,6459,6467,6475,6477,6604,6607,6642,6734,6742,6861,7140,7229,7260,7263,7317,7330,7370,7462,7604,7646,7681,7686,7778,7798,7799,7820,7857,7925,7926,7927,7928,7930,7933,7937,7938,7939,8006,8019,8056,8076,8087,8103,8112);
					

					//Chetan 
					// $usData 		= 	Userdetails::where('user_id',$user_id)->first();				
					$join_date 		=	$val->joining_date;
					if(!empty($join_date)){
						$join_date = date('Y-m-d',strtotime($join_date));
					}
					$net_salary 	=	$val->net_salary;								
					$mindate 	 	= 	date('Y-m-d');
					
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
					
					if($val->is_extra_working_salary=="1"){
						$actual_paid = $actual_paid + $total_holiday_working  - $total_co;
					}
				
				$user_array['date_array'] 				=	$date_array;
				$user_array['total_present'] 			=	($total_present+$total_present_half);
				$user_array['total_absent'] 			=	($total_absent);
				$user_array['total_present_half'] 		=	$total_present_half;
				$user_array['total_holiday_working'] 	=	"$total_holiday_working";
				$user_array['total_week_off'] 			=	$total_week_off + $holiday;
				$user_array['total_month_days'] 		=	$getWorkSunday1;
				$user_array['leave_balance']    		=	$leave_balance;
				$user_array['actual_paid']    			=	$actual_paid;
				$user_array['total_approved_leaves']    =	$total_pl + $total_cl + $total_paternity_leave + $total_sl + $total_co;
				$user_array['total_pl']    				=	"$total_pl";
				$user_array['total_cl']    				=	"$total_cl";
				$user_array['total_paternity_leave']    =	"$total_paternity_leave";
				$user_array['total_sl']    				=	"$total_sl";
				$user_array['total_co']    				=	"$total_co";
				$comman_result[] 						=	$user_array;
				
				// echo  "<pre>"; print_r($comman_result); die;
				
				
			}
		}
		
		// print_r($comman_result); die;
		$responseArray = array();
		 
		if(count($comman_result) > 0){
			$user_count = 0;
			foreach($comman_result as $key=>$valAtt){
				$user_count++;
				$responseArray[$key]['user_count']   			= $user_count;
				$responseArray[$key]['id']   					= $valAtt['id'];
				$responseArray[$key]['name']   					= $valAtt['name'];
				$responseArray[$key]['register_id']  			= $valAtt['register_id'];
				$responseArray[$key]['branch_name']      		= $valAtt['branch_name'] ? $valAtt['branch_name'] : '';
				$responseArray[$key]['departments_name'] 		= $valAtt['departments_name'] ? $valAtt['departments_name'] : '';
				$responseArray[$key]['designation_name']  		= $valAtt['designation_name'];
				$responseArray[$key]['mobile'] 					= $valAtt['mobile'];
				
				$responseArray[$key]['total_present'] 			= $valAtt['total_present'];
				$responseArray[$key]['total_absent'] 			= $valAtt['total_absent'];
				$responseArray[$key]['total_present_half'] 		= $valAtt['total_present_half'];
				$responseArray[$key]['total_holiday_working'] 	= $valAtt['total_holiday_working'];
				$responseArray[$key]['total_week_off'] 			= $valAtt['total_week_off'];
				$responseArray[$key]['leave_balance']			= $valAtt['leave_balance']; 
				$responseArray[$key]['actual_paid'] 			= $valAtt['actual_paid'] + $valAtt['additional_days']; 
				$responseArray[$key]['total_approved_leaves']   = $valAtt['total_approved_leaves']; 
				$responseArray[$key]['total_pl']   				= $valAtt['total_pl']; 
				$responseArray[$key]['total_cl']   				= $valAtt['total_cl']; 
				$responseArray[$key]['total_paternity_leave']   = $valAtt['total_paternity_leave']; 
				$responseArray[$key]['total_sl']   				= $valAtt['total_sl']; 
				$responseArray[$key]['total_co']   				= $valAtt['total_co'];
				
				$responseArray[$key]['net_salary']            = $valAtt['net_salary'];
				$responseArray[$key]['is_extra_working_salary']  = $valAtt['is_extra_working_salary'];
				$responseArray[$key]['fname']                 = $valAtt['fname'];
				$responseArray[$key]['dob']                   = $valAtt['dob'];
				$responseArray[$key]['joining_date']          = $valAtt['joining_date'];
				$responseArray[$key]['reason_date']           = $valAtt['reason_date'];
				$responseArray[$key]['is_esi']                = $valAtt['is_esi'];
				$responseArray[$key]['is_pf']                 = $valAtt['is_pf'];
				$responseArray[$key]['esic_no']               = $valAtt['esic_no'];
				$responseArray[$key]['uan_no']                = $valAtt['uan_no'];
				$responseArray[$key]['tds']                   = $valAtt['tds'];
				$responseArray[$key]['account_number']        = $valAtt['account_number'];
				$responseArray[$key]['ifsc_code']             = $valAtt['ifsc_code'];
				$responseArray[$key]['branch_location']       = $valAtt['branch_location'];
				$responseArray[$key]['pan_no']                = $valAtt['pan_no'];
				$responseArray[$key]['additional_days']       = $valAtt['additional_days'];
				
				if(count($valAtt['date_array'])){
					foreach($valAtt['date_array'] as $key1=>$dateVal){
						$responseArray[$key][$key1] = $dateVal;
					}
				}
				$responseArray[$key]['total_month_days'] = $valAtt['total_month_days'];
				$responseArray[$key]['start_date'] = $start_date;
				$responseArray[$key]['end_date'] = $end_date;
				
			}
				
		}
		return $responseArray;
	}
	
	public function getBranch(Request $request){
		$subBranch = DB::table('branches')->where('branch_location', $request->branch_id)->get();
		
		if (!empty($subBranch))
        {
            echo $res = "<option value=''> Select Branch </option>";
            foreach ($subBranch as $key => $value)
            {
                if (!empty($value->id))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Sub Branch Not Found </option>";
            die();
        }
	}
    
}
