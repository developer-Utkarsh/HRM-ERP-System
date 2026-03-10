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
use Input;
use Excel;
use App\Exports\LeaveExport;
use Auth;
use DB;
use DateTime;
use App\Attendance;
use App\AttendanceNew;

 
class ManualController extends Controller
{
    public function update_manual_send_link(Request $request){
		// $all_levae_details = DB::table('users')->whereRaw('send_link IS NULL')->limit(5)->get();
		
		// 1. Faculties
		
		$faculties = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$faculties->whereRaw("register_id IS NOT NULL AND send_link IS NULL");
		$faculties->limit(100);
		$faculties = $faculties->get();
		
		foreach($faculties as $val){
			$emp_id = $val->id;
			$link = url('/')."/faculty-reports?faculty_id=$emp_id";
			$link = $this->get_tiny_url($link);
			DB::table('users')->where('id', $val->id)->update([ 'send_link' => $link]);
		}
		
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
	
	
	public function check_remaining_leave_manual(){
		
		die('www');
		
		$all_users = DB::table('check_pending_leaves')->get();
		foreach($all_users as $vvvvv){
			$u_di = $vvvvv->user_id;
			$all_users11 = DB::table('users')
						->where('id',$u_di)->first();
			$register_id = $all_users11->register_id;
			
			DB::table('check_pending_leaves')->where('id', $vvvvv->id)->update([ 'emp_id' => $register_id]);
			
		}
		die;
		/*$all_users = DB::table('users')
						->where('id','1796')
						->whereRaw("is_deleted = '0' and status = '1'")
						// ->offset(100)->limit(100)
						->get();*/
		$all_users = DB::table('leave_details')
						->where('status','Approved')
						->whereRaw("category IS NOT NULL")
						->offset(450)->limit(50)
						->groupBy('emp_id')
						->get();
		// print_R(count($all_users)); die;
		if(count($all_users)> 0 ){
			foreach($all_users as $val){
				$user_id = $val->emp_id;
				// $user_id = $val->id;
				// $register_id = $val->register_id;
				
				$url = "http://15.207.232.85/index.php/api/users/leave_types";
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
				if(!empty($resp)){
					$pending_leaves =  json_decode($resp);
					// print_R($pending_leaves); die;
					if(!empty($pending_leaves->data)){
						$pending_pl = $pending_leaves->data->pending_pl;
						$pending_sl = $pending_leaves->data->pending_sl;
						$pending_cl = 0;
						$pending_comp_off = 0;
						$pending_minus = false;
						if($pending_pl < 0){
							$pending_minus = true;
						}
						else if($pending_cl < 0){
							$pending_minus = true;
						}
						else if($pending_sl < 0){
							$pending_minus = true;
						}
						else if($pending_comp_off < 0){
							$pending_minus = true;
						}
						// echo $pending_minus; die;
						// if($pending_minus){
							$leave_id = DB::table('check_pending_leaves')->insertGetId(
									[ 	
										'user_id' => $user_id, 
										// 'emp_id' => $register_id,
										'pending_pl' => $pending_pl,
										'pending_cl' => $pending_cl,
										'pending_sl' => $pending_sl,
										'pending_comp_off' => $pending_comp_off
									]
								);
						// }
					}
				}
				
			}
		}
		
	}
	
	public function is_extra_working_salary(){ 
	
	die('qqq');
		$file_name = asset('laravel/public/data-NY.csv');
		
		$ext  = pathinfo($file_name, PATHINFO_EXTENSION);
		if($ext=='csv'){
			$file     = fopen($file_name, "r");
			$jj       = 0;

			while(($filesop=fgetcsv($file, 10000, ",")) !== FALSE) {
				$jj++;
				if($jj!=1){  
					// echo '<pre>'; print_r($filesop);die;
					
					$reg_id    = $filesop[0];
					$emp_id    = $filesop[1];
					// echo $reg_id .'/'. $emp_id; die;
					if($emp_id == 'YES'){
						User::where('register_id', "$reg_id")->update(['is_extra_working_salary' => '1']);
					}
					else{
						User::where('register_id', "$reg_id")->update(['is_extra_working_salary' => '0']);
					}
				}
			}
			
			die('Success');
		}
   }
	
	
	public function update_manual_leave_record_leaves(){ 
	
	die('qqq');
		$file_name = asset('laravel/public/Leave-Carryforward-2024-new--------.csv');
		
		$ext  = pathinfo($file_name, PATHINFO_EXTENSION);
		if($ext=='csv'){
			$file     = fopen($file_name, "r");
			$jj       = 0;

			while(($filesop=fgetcsv($file, 10000, ",")) !== FALSE) {
				$jj++;
				if($jj!=1){  
					// echo '<pre>'; print_r($filesop);die;
					
					$emp_id    = $filesop[0];
					// $emp_id = "2324"; 
					// echo $emp_id; die;
					$reamining_co  = $filesop[2];
					$reamining_pl  = $filesop[4];
					$reamining_cl  = $filesop[6];
					$earn_pl_2024  = $filesop[5];
					// echo $reamining_cl; die;
					if(!empty($emp_id)){
						$user = User::where('is_deleted', '0')->where('status', 1)->whereRaw("(register_id LIKE '%$emp_id%') and role_id != 2")->first();
						// echo "<pre>"; print_r($user); die('dddddd');
						if(!empty($user)){
							$user_id = $user->id;
							
							
							DB::table('leave_records')->where('user_id', $user_id)->where('session', 2024)->whereRaw("cl != 12 ")->update([ 'pl' => $earn_pl_2024,'cl' => $reamining_cl,'last_year_pl'=>$reamining_pl,'last_year_co' => $reamining_co]);
							
							$leave_id = DB::table('leave_earn')->insertGetId(
									[ 	
										'user_id' => $user_id, 
										'month' => 1,
										'year' => 2024,
										'earn_pl' => $earn_pl_2024,
										'earn_cl' => $reamining_cl,
										'cron_date' => "2024-01-31",
									]
							);
								
							echo $user_id.'/';
							
						}
						
						// die('dddd');
						
					}
				}
			}
			
			die('Success');
		}
   }
	
}
