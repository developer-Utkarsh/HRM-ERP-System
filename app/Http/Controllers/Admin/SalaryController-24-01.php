<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Userdetails;
use Auth;
use Hash;
use Input;
use Excel;
use App\Exports\SalaryExport;
use DB;
use App\Attendance;
use App\AttendanceNew;
use DataTables;
use DateTime;
use App\SalaryIncrement;
 
class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if(in_array(Auth::user()->email,array('admin@gmail.com','anandkarwa2010@gmail.com','projectmanager@utkarsh.com','hr@utkarsh.com','jay+1@gmail.com'))){
			// $request->session()->put('key', 'tets');
			// $request->session()->forget('key');
			// echo $value = $request->session()->get('key'); die;
			if($request->session()->get('salary_access')==true){
				
			}
			else{
				// die('ddd');
				/*$mbl = Auth::user()->mobile;
				// $mbl = 8104001734;
				$otp_gen=substr(str_shuffle("0123456789"), 0, 6);
				//$msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
				$msg="<#>Use ".$otp_gen. " as one time password (OTP) to activate your Utkarsh App account. DLqWdT0kDp0	";
				$msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
				$message_content=urlencode($msg);
				$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=6K7Kcj7C2U9A&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
				$ch=curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_exec($ch);
				curl_close($ch);
				
				DB::table('users')->where('id', Auth::user()->id)->update([
					'salary_access_otp' => $otp_gen
				]);*/		


				return view('admin.salary.otp');
			}
			
			$params = array();
			$res = parse_url($_SERVER['REQUEST_URI']);
			if(!empty($res['query'])){
				parse_str($res['query'], $params);
			}
			
			$month       = Input::get('month');
			$search      = Input::get('search');
			
			if(!empty($params['year_wise_month']) && substr($params['year_wise_month'], 5, 2) == date('m')){ 
				return redirect()->back()->with('error', 'You have not access of current month');
			}
			
			
			$whereCond   = '1=1 ';
			if(!empty($search)){ 
				$whereCond .= " AND (users.name LIKE '%$search%' OR users.email LIKE '%$search%' OR users.mobile LIKE '%$search%')";
			}
			
			$get_emp = User::with('user_details')->where([['role_id', '!=', 1], ['status', '=', 1]])->whereRaw($whereCond)->get();
			//echo '<pre>'; print_r($month);die;
			$pageNumber = 1;
			/* if(isset($page)){
				//$page = Input::get('page');
				$pageNumber = (20*($page-1));
				
				$pageNumber = $pageNumber +1;
			} */
			return view('admin.salary.index', compact('params','pageNumber','get_emp'));
		}
		else{
			echo "<h1>Not access you.</h1>"; die;
		}
    }
	
	public function salaryrecorddetail(Request $request){	
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$year_wise_month = $request->year_wise_month;
		$search          = $request->search;
		
		$responseArray = $this->salary_calculate($search, $year_wise_month);
		//echo "<pre>";print_R($responseArray); die;
		return DataTables::of($responseArray)->make(true);

		
	}
	
	public function download_salary_excel()
    {
		$search     = Input::get('search');
        $year_wise_month    = Input::get('year_wise_month');
		$responseArray = $this->salary_calculate($search, $year_wise_month);
		if(count($responseArray) > 0){
            return Excel::download(new SalaryExport($responseArray), 'salary.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function salary_calculate($search,$year_wise_month)
    {		
		// Start Same Copy of salaryrecorddetail function 
		
		$new_whereCond   = 'users.status = 1 AND users.is_deleted = "0" AND users.register_id>1001 AND users.register_id!=""';
	
		
		if(!empty($search)){
			$new_whereCond .= " AND ((users.name LIKE '%$search%' or users.email LIKE '%$search%' or users.mobile LIKE '%$search%' or users.register_id LIKE '%$search%'))";
		}

		
		
		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.register_id,users.name as name,users.email as email,users.mobile as mobile,userdetails.esi_amount,userdetails.pf_amount,userdetails.net_salary,userdetails.is_esi,userdetails.is_pf"));
		
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id');
		
		$attendance->whereRaw($new_whereCond);
		$array1 = $attendance->get();
		//echo '<pre>'; print_r($array1);die;	 			
		$comman_result = array();
		
		$year_wise_month = explode('-',$year_wise_month);
		
		$yr = $year_wise_month[0];
		$mt = $year_wise_month[1];
		$total_month_days = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		if(count($array1) > 0){
			foreach($array1 as $val){
				$filter_year_month = $yr.'-'.$mt;
				$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);
				$user_id = $val->id;
				//$previous_year_month = date("Y-m", strtotime ( '-1 month' , ( $first_date ) )) ;
				$previous_year_month = date("Y-m", $first_date);
				$leave_balance = 0;
				$adjustment_amount = 0;
				
				
				$user_array = array(); 
				$user_array['id'] = $user_id;
				$user_array['name'] = $val->name;
				$user_array['register_id'] = $val->register_id;
				$user_array['email'] = $val->email;
				$user_array['mobile'] = $val->mobile;
				$user_array['esi_amount'] = $val->esi_amount;
				$user_array['pf_amount'] = $val->pf_amount;
				$user_array['net_salary'] = $val->net_salary;
				$user_array['is_esi'] = $val->is_esi;
				$user_array['is_pf'] = $val->is_pf;
				//$user_array['leave_balance'] = $leave_balance;
				$user_array['filter_year_month'] = $filter_year_month;
				//$user_array['adjustment_amount'] = $adjustment_amount;
				
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
				
				$i = 1;
				$total_present = 0;
				$total_absent = 0;
				$total_present_half = 0;
				$total_holiday_working = 0;
				$total_week_off = 0;
				$count_week_days = 0;
				$monday_to_sunday_present = 0;
				$day_without_sunday = 0;
				$actual_paid = 0;
				
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
					if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) {
						$monday_to_sunday_present++;
						
						$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
						$in_time = $attendance_array[$array_index]['in_time'];
						if($add_get_date=="1990-07-31"){ //2021-07-31
							if(!empty($in_time)){
								$total_present++;
							}
						}else{
							$out_time = $attendance_array[$array_index]['out_time'];
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
									$total_absent++;
									/* if($monday_to_sunday_present>=3){							
										$total_week_off++;
									}
									else{						
										$total_absent++;
									} */
								}
								else{
									$total_holiday_working++;
									$total_week_off++;
									/* if($monday_to_sunday_present>=4){							
										$total_week_off++;
									} */
									 
								}
								
								$monday_to_sunday_present = 0;
								
							}
							else{
								if($total_minute <= 120){
									//120 Mint = 2 hour
									$total_absent++;
								}else if($total_minute <= 360){
									//360 Mint = 6 hour
									$total_present_half++;
								}else{
									$total_present++;
								}
							}
						}
						
						
						
					}
					else{
						if($workday == "Sun"){
							$total_week_off++;
							/* if($count_week_days==7){
								if($monday_to_sunday_present>=3){							
									$total_week_off++;
								}
								else{						
									$total_absent++;
								}
							}
							else{							
								$total_week_off++;
							} */
							
							$monday_to_sunday_present = 0;
							
						}
						else{
							$total_absent++;
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
				
				
				if(!empty($val->net_salary) && $val->net_salary > 20000){
				    $previouse_leave_balance = DB::table('leave_balance')
								->where('user_id', $user_id)
								->where('year_month', $previous_year_month)
								->first();
					if(!empty($previouse_leave_balance)){
						$leave_balance = $previouse_leave_balance->extend_leave;
					}
					
					$current_leave_balance = DB::table('leave_balance')
								->where('user_id', $user_id)
								->where('year_month', $filter_year_month)
								->first();
					if(!empty($current_leave_balance)){
						$adjustment_amount = $current_leave_balance->adjustment_amount;
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
							
							/* $actual_paid = ($total_present+($total_absent+$total_present_half));
							$leave_balance = ($leave_balance-($total_absent+$total_present_half)); */
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
					$actual_paid = $total_present+$total_holiday_working;
				}
				
				
				
				
				
				$user_array['total_present'] = ($total_present+$total_present_half);
				$user_array['total_absent'] = ($total_absent+$total_present_half);
				$user_array['total_present_half'] = $total_present_half;
				$user_array['total_holiday_working'] = $total_holiday_working;
				$user_array['total_week_off'] = $total_week_off;
				$user_array['total_month_days'] = $total_month_days;
				$user_array['leave_balance']    = $leave_balance;
				$user_array['actual_paid']    = $actual_paid;
				$user_array['leave_balance'] = $leave_balance;
				$user_array['adjustment_amount'] = $adjustment_amount;
				$comman_result[] = $user_array;
				
				// echo  "<pre>"; print_r($comman_result); die;
				
			}
		}
		
		 
		$responseArray = array();
		 
		if(count($comman_result) > 0){ //getWorkSunday
			foreach($comman_result as $key=>$valAtt){
				$previouse_leave_balance = $valAtt['leave_balance'];
				$filter_year_month = $valAtt['filter_year_month'];
				// echo "<pre>"; print_r($valAtt); die;
				$esi = 0;
				$pf = 0;
				$days = 0;
				$total_leave_balance = 0;
				$total_days  = $valAtt['total_present'] + $valAtt['total_holiday_working'] + $valAtt['total_week_off'];
				if($total_days  >= $total_month_days){					
					$total_leave_balance = $previouse_leave_balance +($total_days - $total_month_days);					
					$days      = $total_month_days;
				}
				else{
					if($previouse_leave_balance > 0){
						$absent_days = $total_month_days - $total_days;
						if($previouse_leave_balance >=$absent_days){
							$total_leave_balance = $previouse_leave_balance-$absent_days; //adjust_leave
							$total_days += $absent_days;
						}
						else{
							$total_leave_balance = 0;
							$total_days += $previouse_leave_balance;
						}
					}
					$days      = $total_days;
				}
				if(strtotime($filter_year_month."-01") > strtotime("2021-06-01") ){
					$check_leave_balance = DB::table('leave_balance')
								->where('user_id', $valAtt['id'])
								->where('year_month', $filter_year_month)
								->first();
					if(!empty($check_leave_balance)){
						DB::table('leave_balance')
								->where('id', $check_leave_balance->id)
								->update(['extend_leave' => $total_leave_balance]);
					}
					else{
						DB::table('leave_balance')->insertGetId([
							'user_id'       => $valAtt['id'],
							'year_month'    => $filter_year_month,
							'extend_leave' 		=> $total_leave_balance
						]);
					}
				}
				$net_salary = 0;
				if(!empty($valAtt['net_salary']) && is_numeric($valAtt['net_salary'])==1){
					$net_salary = $valAtt['net_salary'];
				}
				
				$perday_salary = $net_salary / $valAtt['total_month_days'];
				$cal_salary    = $perday_salary * $days;
				
				if($net_salary <= 21000){
					if(!empty($valAtt['is_esi']) && $valAtt['is_esi'] == 'Yes'){
						$esi = ($net_salary * 0.75)/100;
					}
				}
				
				if($net_salary <= 15000){
					if(!empty($valAtt['is_pf']) && $valAtt['is_pf'] == 'Yes'){
						$pf = $cal_salary * 12 /100;
					}
				}
				
				
				if(!empty($valAtt['net_salary']) && $valAtt['net_salary'] <= 20000){
					$salary        = round(($cal_salary - $esi - $pf) + ($perday_salary*$valAtt['total_holiday_working']),2);
				}
				else{
					$salary        = round($cal_salary - $esi - $pf,2);
				}
				
				
				$responseArray[$key]['user_id']               = $valAtt['id'];
				$responseArray[$key]['name']                  = $valAtt['name'];
				$responseArray[$key]['net_salary']            = $valAtt['net_salary'];
				$responseArray[$key]['register_id']           = $valAtt['register_id'];
				$responseArray[$key]['mobile']                = $valAtt['mobile'];
				$responseArray[$key]['esi_amount']            = round($esi,2);
				$responseArray[$key]['pf_amount']             = round($pf,2);
				$responseArray[$key]['total_present']         = $valAtt['total_present'];
				$responseArray[$key]['total_absent']          = $valAtt['total_absent'];
				$responseArray[$key]['total_present_half']    = $valAtt['total_present_half'];
				$responseArray[$key]['total_holiday_working'] = $valAtt['total_holiday_working'];
				$responseArray[$key]['total_week_off']        = $valAtt['total_week_off'];
				$responseArray[$key]['total_month_days']      = $valAtt['total_month_days'];
				$responseArray[$key]['salary']                = $salary;
				//$responseArray[$key]['leave_balance']         = $previouse_leave_balance;
				$responseArray[$key]['adjustment_amount']     = $valAtt['adjustment_amount'];
				$responseArray[$key]['leave_balance']         = $valAtt['leave_balance']; 
				$responseArray[$key]['actual_paid']           = $valAtt['actual_paid']; 
				
				/* $user_idasdfas = $valAtt['id'];
				$asfjjsflkd = $valAtt['total_present'] + $valAtt['total_holiday_working'] + $valAtt['total_week_off'];
				$des = "आपके जुलाई माह मे कुल $asfjjsflkd दिनों की उपस्थिति दर्ज की गई हैं इसी के आधार पर आपको जुलाई माह का वेतन मिलेगा इसमें अगर किसी प्रकार की त्रुटि हो तो आप अभी 5  बजे से पहले अपने डिपार्टमेंट हेड को बताए और HRMS app में update करवाए।";
				DB::table('api_notifications')->insertGetId([
					'sender_id'       => 901,
					'receiver_id'    => '["'.$user_idasdfas.'"]',
					'title' 		 => 'Month July-2021 Attadance Update!!',
					'description'    =>  $des,
					'type' => 'General',
					'date' => date('Y-m-d H:i:s')
				]); */
				
			}
				
		}
		
		// End Same Copy of salaryrecorddetail function 
		
        return $responseArray;
    }
	
	
	public function download_excel(){  
		$year_wise_month = Input::get('year_wise_month');
		$get_emp         = array();
		if(!empty($year_wise_month)){
			$get_emp = User::with('user_details')->where([['role_id', '!=', 1], ['status', '=', 1]])->get();
		}
		
		
		//echo '<pre>'; print_r($get_emp);die;
        if(count($get_emp) > 0){
            return Excel::download(new SalaryExport($get_emp), 'SalaryData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
	
	public function salary_send_otp(Request $request)
    {
		//$mbl = Auth::user()->mobile;
		// $mbl = 7849906562;
		$mbl = 9079238949;  // Pradeep 
		// $mbl = 8104001734;  // Dinesh
		// $otp_gen='1212';
		$otp_gen=substr(str_shuffle("0123456789"), 0, 6);
		//$msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
		// $msg="<#>Use ".$otp_gen. " as one time password (OTP) to activate your Utkarsh App account. DLqWdT0kDp0	";
		$msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
		$message_content=urlencode($msg);
		// $url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=6K7Kcj7C2U9A&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
		// $url="http://sms.smsinsta.in/vb/apikey.php?apikey=81623126665543173541&senderid=UTKRSH&templateid=1707161580023473180&route=3&unicode=2&number=91$mbl&message=$message_content";
		
		$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		
		DB::table('users')->where('id', Auth::user()->id)->update([
			'salary_access_otp' => $otp_gen
		]);
		
		return response(['status' => true, 'message' => $otp_gen]);
    }
	
	public function salary_access_otp(Request $request)
    {
		$otp = $request->otp;
		
		if(!empty($otp)){
			if($request->ajax()){ 
				$check_user = DB::table('users')->where('id', Auth::user()->id)->first();
				if(!empty($check_user) && $check_user->salary_access_otp==$otp){
					$request->session()->put('salary_access', true);
					return response(['status' => true]);
				}
				else{
					return response(['status' => false, 'message' => 'OTP Invalid']);
				}
				// $request->session()->put('key', 'tets');
				// $request->session()->forget('key');
				// echo $value = $request->session()->get('key'); die;
			}
			else{
				return response(['status' => false, 'message' => 'OTP Invalid']);
			}
		}
		else{
			return response(['status' => false, 'message' => 'OTP Invalid']);
		}
    }
	
	public function import_salary(){
		return view('admin.salary.import_salary');
	}
	
	public function storeSalary(Request $request){
		$validatedData = $request->validate([
            'import_file' => 'required|mimes:xlsx,xls',
        ]);
		$file       = $request->file('import_file');
		$path       = $file->path();
		$conditions = true;
		$import     = Excel::toArray(null, $file);
        //$stArr      = $import[0][0];
        unset($import[0][0]);
        
		
		
		foreach ($import[0] as $key => $value) {
			$is_esi = ''; $is_pf = '';
			if($value[2] == ''){ $is_esi = 'No'; }else{ $is_esi = 'Yes'; }
			if($value[3] == ''){ $is_pf = 'No'; }else{ $is_pf = 'Yes'; }
			$chk_salary = User::where('register_id', 'LIKE', '%' . $value[0] . '%')->first();
			//echo '<pre>'; print_r($chk_salary);die;
			if(!empty($chk_salary->id)){
				$update_salary = Userdetails::where('user_id',$chk_salary->id)->update([
									'net_salary' => $value[1],
									'esi_amount' => $value[2],
									'pf_amount'  => $value[3],
									'govt_pf'    => $value[4],
									'is_esi'     => $is_esi,
									'is_pf'      => $is_pf
								]);
			}

		} 
		
		if ($update_salary) {
            return redirect()->route('admin.import-salary')->with('success', 'Salary Update Successfully');
        } else {
            return redirect()->route('admin.import-salary')->with('error', 'Something Went Wrong !');
        }
	}
	
	public function submit_salary_adjusment(Request $request){
		$adjustment_amount = $request->adjustment_amount;
		$search_year_month = $request->search_year_month;
		$i = 0;
		if(!empty($adjustment_amount)){
			foreach($adjustment_amount as $userId=>$val){
				if(!empty($val)){
					$i++;
					$check_leave_balance = DB::table('leave_balance')
								->where('user_id', $userId)
								->where('year_month', $search_year_month)
								->first();
					if(!empty($check_leave_balance)){
						DB::table('leave_balance')
								->where('id', $check_leave_balance->id)
								->update(['adjustment_amount' => $val]);
					}
					
				}
			}
			if($i > 0){
				return response(['status' =>true,'msg'=>'Adjusment successfully save.'], 200);
			}
			else{
				return response(['status' =>false,'msg'=>'No any amount save'], 200);
			}
		}
		else{
			return response(['status' =>false,'msg'=>'Something  went wrong'], 200);
		}
		// echo "<pre>"; print_r($request->adjustment_amount);
		
	}
	
	
	public function download_pdf() { 
		$emp_id    = Input::get('id');
		$year_wise_month = Input::get('year_wise_month');
		$new_whereCond   = 'users.status = 1 AND users.is_deleted = "0" AND users.register_id>1001 AND users.register_id!="" AND users.id = "'.$emp_id.'"';

		$attendance = User::query();
		$attendance->select(\DB::raw("users.id as id,users.register_id,users.name as name,departments.name as departments_name,userdetails.degination,userdetails.dob,userdetails.joining_date,userdetails.esi_amount,userdetails.pf_amount,userdetails.net_salary,userdetails.is_esi,userdetails.is_pf,userdetails.account_number,userdetails.pan_no,userdetails.tds,userdetails.esic_no,userdetails.uan_no,branches.branch_location"));
		$attendance->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->leftJoin('departments', 'users.department_type', '=', 'departments.id')->leftJoin('branches', 'userdetails.branch_id', '=', 'branches.id');
		
		$attendance->whereRaw($new_whereCond);
		$array1 = $attendance->get();
		//echo '<pre>'; print_r($array1);die;	 			
		$comman_result = array();
		
		$year_wise_month = explode('-',$year_wise_month);
		
		$yr = $year_wise_month[0];
		$mt = $year_wise_month[1];
		$total_month_days = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		if(count($array1) > 0){
			foreach($array1 as $val){
				$filter_year_month = $yr.'-'.$mt;
				$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);
				$user_id = $val->id;
				$previous_year_month = date("Y-m", $first_date);
				$leave_balance = 0;
				$adjustment_amount = 0;
				
				
				$user_array = array(); 
				$user_array['id'] = $user_id;
				$user_array['name'] = $val->name;
				$user_array['register_id'] = $val->register_id;
				$user_array['departments_name'] = $val->departments_name;
				$user_array['degination'] = $val->degination;
				$user_array['dob'] = $val->dob;
				$user_array['joining_date'] = $val->joining_date;
				$user_array['esi_amount'] = $val->esi_amount;
				$user_array['pf_amount'] = $val->pf_amount;
				$user_array['net_salary'] = $val->net_salary;
				$user_array['is_esi'] = $val->is_esi;
				$user_array['is_pf'] = $val->is_pf;
				$user_array['filter_year_month'] = $filter_year_month;
				$user_array['account_number'] = $val->account_number;
				$user_array['pan_no'] = $val->pan_no;
				$user_array['tds']    = $val->tds;
				$user_array['esic_no']    = $val->esic_no;
				$user_array['uan_no']    = $val->uan_no;
				$user_array['branch_location']    = $val->branch_location;
				
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
				
				$i = 1;
				$total_present = 0;
				$total_absent = 0;
				$total_present_half = 0;
				$total_holiday_working = 0;
				$total_week_off = 0;
				$count_week_days = 0;
				$monday_to_sunday_present = 0;
				$day_without_sunday = 0;
				$actual_paid = 0;
				
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
					if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) {
						$monday_to_sunday_present++;
						
						$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
						$in_time = $attendance_array[$array_index]['in_time'];
						if($add_get_date=="1990-07-31"){ //2021-07-31
							if(!empty($in_time)){
								$total_present++;
							}
						}else{
							$out_time = $attendance_array[$array_index]['out_time'];
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
									$total_absent++;
								}
								else{
									$total_holiday_working++;
									$total_week_off++;
								}
								
								$monday_to_sunday_present = 0;
								
							}
							else{
								if($total_minute <= 120){
									//120 Mint = 2 hour
									$total_absent++;
								}else if($total_minute <= 360){
									//360 Mint = 6 hour
									$total_present_half++;
								}else{
									$total_present++;
								}
							}
						}
						
						
						
					}
					else{
						if($workday == "Sun"){
							$total_week_off++;
							$monday_to_sunday_present = 0;
							
						}
						else{
							$total_absent++;
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
				
				
				if(!empty($val->net_salary) && $val->net_salary > 20000){
				    $previouse_leave_balance = DB::table('leave_balance')
								->where('user_id', $user_id)
								->where('year_month', $previous_year_month)
								->first();
					if(!empty($previouse_leave_balance)){
						$leave_balance = $previouse_leave_balance->extend_leave;
					}
					
					$current_leave_balance = DB::table('leave_balance')
								->where('user_id', $user_id)
								->where('year_month', $filter_year_month)
								->first();
					if(!empty($current_leave_balance)){
						$adjustment_amount = $current_leave_balance->adjustment_amount;
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
					$actual_paid = $total_present+$total_holiday_working;
				}
				
				
				
				
				
				$user_array['total_present'] = ($total_present+$total_present_half);
				$user_array['total_absent'] = ($total_absent+$total_present_half);
				$user_array['total_present_half'] = $total_present_half;
				$user_array['total_holiday_working'] = $total_holiday_working;
				$user_array['total_week_off'] = $total_week_off;
				$user_array['total_month_days'] = $total_month_days;
				$user_array['leave_balance']    = $leave_balance;
				$user_array['actual_paid']    = $actual_paid;
				$user_array['leave_balance'] = $leave_balance;
				$user_array['adjustment_amount'] = $adjustment_amount;
				$comman_result[] = $user_array;
				
			}
		}
		
		 
		$responseArray = array();
		 
		if(count($comman_result) > 0){ 
			foreach($comman_result as $key=>$valAtt){
				$previouse_leave_balance = $valAtt['leave_balance'];
				$filter_year_month = $valAtt['filter_year_month'];
				$esi = 0;
				$pf = 0;
				$days = 0;
				$total_leave_balance = 0;
				$total_days  = $valAtt['total_present'] + $valAtt['total_holiday_working'] + $valAtt['total_week_off'];
				if($total_days  >= $total_month_days){					
					$total_leave_balance = $previouse_leave_balance +($total_days - $total_month_days);					
					$days      = $total_month_days;
				}
				else{
					if($previouse_leave_balance > 0){
						$absent_days = $total_month_days - $total_days;
						if($previouse_leave_balance >=$absent_days){
							$total_leave_balance = $previouse_leave_balance-$absent_days; //adjust_leave
							$total_days += $absent_days;
						}
						else{
							$total_leave_balance = 0;
							$total_days += $previouse_leave_balance;
						}
					}
					$days      = $total_days;
				}
				if(strtotime($filter_year_month."-01") > strtotime("2021-06-01") ){
					$check_leave_balance = DB::table('leave_balance')
								->where('user_id', $valAtt['id'])
								->where('year_month', $filter_year_month)
								->first();
					if(!empty($check_leave_balance)){
						DB::table('leave_balance')
								->where('id', $check_leave_balance->id)
								->update(['extend_leave' => $total_leave_balance]);
					}
					else{
						DB::table('leave_balance')->insertGetId([
							'user_id'       => $valAtt['id'],
							'year_month'    => $filter_year_month,
							'extend_leave' 		=> $total_leave_balance
						]);
					}
				}
				$net_salary = 0;
				if(!empty($valAtt['net_salary']) && is_numeric($valAtt['net_salary'])==1){
					$net_salary = $valAtt['net_salary'];
				}
				
				$perday_salary = $net_salary / $valAtt['total_month_days'];
				$cal_salary    = $perday_salary * $days;
				
				if($net_salary <= 21000){
					if(!empty($valAtt['is_esi']) && $valAtt['is_esi'] == 'Yes'){
						$esi = ($net_salary * 0.75)/100;
					}
				}
				
				if($net_salary <= 15000){
					if(!empty($valAtt['is_pf']) && $valAtt['is_pf'] == 'Yes'){
						$pf = $cal_salary * 12 /100;
					}
				}
				
				
				if(!empty($valAtt['net_salary']) && $valAtt['net_salary'] <= 20000){
					$salary        = round(($cal_salary - $esi - $pf) + ($perday_salary*$valAtt['total_holiday_working']),2);
				}
				else{
					$salary        = round($cal_salary - $esi - $pf,2);
				}
				
				
				$responseArray[$key]['user_id']               = $valAtt['id'];
				$responseArray[$key]['name']                  = $valAtt['name'];
				$responseArray[$key]['net_salary']            = $valAtt['net_salary'];
				$responseArray[$key]['register_id']           = $valAtt['register_id'];
				$responseArray[$key]['departments_name']      = $valAtt['departments_name'];
				$responseArray[$key]['degination']            = $valAtt['degination'];
				$responseArray[$key]['dob']                   = $valAtt['dob'];
				$responseArray[$key]['joining_date']          = $valAtt['joining_date'];
				$responseArray[$key]['esi_amount']            = round($esi,2);
				$responseArray[$key]['pf_amount']             = round($pf,2);
				$responseArray[$key]['total_present']         = $valAtt['total_present'];
				$responseArray[$key]['total_absent']          = $valAtt['total_absent'];
				$responseArray[$key]['total_present_half']    = $valAtt['total_present_half'];
				$responseArray[$key]['total_holiday_working'] = $valAtt['total_holiday_working'];
				$responseArray[$key]['total_week_off']        = $valAtt['total_week_off'];
				$responseArray[$key]['total_month_days']      = $valAtt['total_month_days'];
				$responseArray[$key]['salary']                = $salary;
				$responseArray[$key]['adjustment_amount']     = $valAtt['adjustment_amount'];
				$responseArray[$key]['leave_balance']         = $valAtt['leave_balance']; 
				$responseArray[$key]['actual_paid']           = $valAtt['actual_paid']; 
				$responseArray[$key]['net_salary']            = $valAtt['net_salary'];
				$responseArray[$key]['word_net_salary']       = $this->getIndianCurrency($salary);
				$responseArray[$key]['account_number']        = $valAtt['account_number'];;
				$responseArray[$key]['pan_no']                = $valAtt['pan_no'];;
				$responseArray[$key]['tds']                   = $valAtt['tds'];;
				$responseArray[$key]['esic_no']               = $valAtt['esic_no'];;
				$responseArray[$key]['uan_no']                = $valAtt['uan_no'];;
				$responseArray[$key]['branch_location']       = $valAtt['branch_location'];;
			}
				
		}
		
		//echo "<pre>"; print_r($responseArray); die;
		$date_salary = Input::get('year_wise_month');
		//return view('admin.salary.pdf_html', compact('responseArray','date_salary'));

		require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Salary Slip');
        $pdf->custom_title = 'Salary Slip';
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('freesans', '', 12);
		$html = view('admin.salary.pdf_html', compact('responseArray','date_salary'))->render();
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $pdf->Output('Salary_slip' . md5(time()) . '.pdf', 'D');
	   die('ddd');
   }
	
	
   public function getIndianCurrency(float $number)
   {
	   $decimal = round($number - ($no = floor($number)), 2) * 100;
	   $hundred = null;
	   $digits_length = strlen($no);
	   $i = 0;
	   $str = array();
	   $words = array(0 => '', 1 => 'one', 2 => 'two',
		   3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
		   7 => 'seven', 8 => 'eight', 9 => 'nine',
		   10 => 'ten', 11 => 'eleven', 12 => 'twelve',
		   13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
		   16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
		   19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
		   40 => 'forty', 50 => 'fifty', 60 => 'sixty',
		   70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
	   $digits = array('', 'hundred','thousand','lakh', 'crore');
	   while( $i < $digits_length ) {
		   $divider = ($i == 2) ? 10 : 100;
		   $number = floor($no % $divider);
		   $no = floor($no / $divider);
		   $i += $divider == 10 ? 1 : 2;
		   if ($number) {
			   $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			   $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			   $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
		   } else $str[] = null;
	   }
	   $Rupees = implode('', array_reverse($str));
	   $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	   return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
   }
   
   public function add_increment(){ 
		$emp_code        = Input::get('emp_code');
        $year_wise_month = Input::get('year_wise_month');

        $salary_increment = SalaryIncrement::select('users.name','salary_increment.*')->join('users','users.id','=','salary_increment.user_id');

        if (!empty($emp_code)){
            $salary_increment->where('salary_increment.emp_code',$emp_code);
        }

		if (!empty($year_wise_month)){
            $salary_increment->where('salary_increment.date',$year_wise_month);
        }

        $salary_increment = $salary_increment->orderBy('salary_increment.id', 'desc')->get();

		return view('admin.salary.add_increment', compact('salary_increment'));
	}

	public function store_increment(Request $request){
		//echo '<pre>'; print_r($request->post());die;
		$logged_id       = Auth::user()->id;
		$validatedData = $request->validate([
			'import_file' => 'required',
			'year_wise_month' => 'required',
		]);

		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}

        $path = $file->path();		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
        $stArr = $import[0][0];
        $result = [];
		$errors_row = "";
		if(!empty($import[0])){ 
			foreach ($import[0] as $key => $value) {  
				if($key > 0){ 
					$user_result = User::where('register_id', $value[0])->first();
					if(!empty($user_result)){
						$check_result = SalaryIncrement::where([['user_id', $user_result->id],['emp_code', $value[0]],['date', $request->year_wise_month]])->first();
						if(empty($check_result)){
							$add_arr = array(
								'user_id'          => !empty($user_result) ? $user_result->id : '',
								'emp_code'         => $value[0],
								'increment_amount' => $value[1],
								'loan_amount'      => $value[2],
								'arrear_amount'    => $value[3],
								'tds_amount'       => $value[4],
								'date'             => $request->year_wise_month,
								'created_by'       => $logged_id
							); 
							$salary_res = SalaryIncrement::create($add_arr);

							$this->maintain_history(Auth::user()->id, 'salary_increment', $salary_res->id, 'add_salary_increment', json_encode($add_arr));
						}
						else{
							$update_arr = array(
								'user_id'          => !empty($user_result) ? $user_result->id : '',
								'emp_code'         => $value[0],
								'increment_amount' => $value[1],
								'loan_amount'      => $value[2],
								'arrear_amount'    => $value[3],
								'tds_amount'       => $value[4],
								'date'             => $request->year_wise_month,
								'created_by'       => $logged_id
							);
							SalaryIncrement::where([['user_id', $user_result->id],['emp_code', $value[0]],['date',$request->year_wise_month]])->update($update_arr);
							
							$this->maintain_history(Auth::user()->id, 'salary_increment', $check_result->id, 'update_salary_increment', json_encode($update_arr));
						}	
					}
				}
			}	
		}
		else{
			return redirect()->route('admin.salary.add-increment')->with('error', "Something went wrong !");
		}
        return back()->with('success', 'Increment Excel Data Imported successfully.'); 
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

}
