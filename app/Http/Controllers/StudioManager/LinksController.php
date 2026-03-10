<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Input;
use App\FacultyRelation;
use App\Userdetails;
use App\Subject;
use DB;
use App\Userbranches;
use App\Studio;
use App\Users_pending;
use Auth;
use Excel;
use App\Exports\EmployeeExport;
use App\AttendanceNew;
use App\Attendance;
use App\Exports\LateEmployeeExport;
use App\ApiNotification;

class LinksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$faculties = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$faculties->whereRaw("register_id IS NOT NULL");
		$faculties = $faculties->get();
		
		$studiomanager = User::whereRaw("(role_id = 4 OR role_id = 27)")->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$studiomanager->whereRaw("register_id IS NOT NULL");
		$studiomanager = $studiomanager->get();
		
		$assistants = User::where('role_id','3')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$assistants->whereRaw("register_id IS NOT NULL");
		$assistants = $assistants->get();
		
		$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$drivers->where('register_id','!=',NUll);
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
		$drivers = $drivers->get();
		
		
		return view('studiomanager.links.index', compact('faculties','studiomanager','assistants','drivers')); 
    }

  
	 public function faculty_link(Request $request)
	{
		$logged_id     = Auth::user()->id;
		$employee_ids = $request->employee_id;
		// print_R($faculty_ids); die;
		
		if(!empty($employee_ids)){
			if(count($employee_ids) > 0){
				foreach($employee_ids as $emp_id){
					$emp_detail = DB::table('users')->where('id',$emp_id)->first();
					$name = $emp_detail->name;
					if(!empty($emp_detail->send_link)){
						
						
						$send_link = $emp_detail->send_link;
						$mobile = $emp_detail->mobile;
						$token = $emp_detail->gsm_token;
						$today_link = $send_link;

						
						//$tomorrow_link = url('/')."/studio-reports?fdate=$tomorrow_date";
						if(empty($today_link)){
						   $today_link=$this->get_tiny_url("http://15.207.232.85/index.php/faculty-reports?faculty_id=".$emp_id);
					    }

					    $this->whatsapp_msg($name,$today_link,$mobile);
						$this->txt_msg($name,$today_link,$mobile);
	                    
	                    $msg="Dear $name, Your Time Table is published.. Please check";
						//$this->notification_msg($token,$emp_id,$msg,$logged_id);

                        //$this->whatsapp_msg($name,$today_link,"7014155376");
						//$this->txt_msg($name,$today_link,"7014155376");
						$this->notification_msg("cfzLqg3wSfu7w11mDorEAq:APA91bFBPsQ3QrsaB_aig_WuJwWFwCPpXS9udq5luLH_yPudeeVsPyrYlyNV7VF68ukNAxAGExcmbfFmrYBg4x6oHhLSna1ULp_5EqvAyRM2jfOPRo99s3VcsVkUSU-gm4UjZdvZNKpI","1089",$msg,$logged_id);
						
						//Gupsup Message
						
						echo $mobile;
						echo $today_link;
						
						die();
						$params = [];
						$params['input']   		 = [$today_link];
						$params['template_id']   = '5032b745-5c68-4c8d-8da3-73fcec95fcc6';
						$params['mobile']   	 = $mobile;
						$this->gupsup_msg($params);
	                    
				    }
				}

				return redirect()->back()->with('success', 'Sent Successfully');
			}
		}else{
			return redirect()->back()->with('error', 'Select Faculty Name.');
		}
		
    }
	
	
	public function all_send($type=null)
    {
		$employees = array();
		$logged_id     = Auth::user()->id;
		if(empty($type)){
			return redirect()->back()->with('error', 'Something Went Wrong.');
		}
		else if($type=='faculty'){
			$faculties = User::where('role_id','2')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
			$faculties->whereRaw("register_id IS NOT NULL");
			$employees = $faculties->get();
		}
		else if($type=='studio_manager'){
			$studiomanager = User::whereRaw("(role_id = 4 OR role_id = 27)")->where('is_deleted', '0')->where('status', 1)->orderBy('name');
			$studiomanager->whereRaw("register_id IS NOT NULL");
			$employees = $studiomanager->get();
		}
		else if($type=='studio_assistant'){
			$assistants = User::where('role_id','3')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
			$assistants->whereRaw("register_id IS NOT NULL");
			$employees = $assistants->get();
		}else if($type=='drivers'){
			$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
			$drivers->where('register_id','!=',NUll);
			$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
					$q->where('degination', '=', 'DRIVER');
				});
			$employees = $drivers->get();
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong.');
		}
		    // echo "<pre>"; print_r(count($employees)); die;
			if(count($employees) > 0){
				foreach($employees as $emp_detail){
					$emp_id = $emp_detail->id;
					$name = $emp_detail->name;
					if(!empty($emp_detail->send_link)){
						$send_link = $emp_detail->send_link;
						$mobile = $emp_detail->mobile;
						$token = $emp_detail->gsm_token;
						$today_link = $send_link;

						if(empty($today_link)){
						   $today_link=$this->get_tiny_url("http://15.207.232.85/index.php/faculty-reports?faculty_id=".$emp_id);
					    }

					    $this->whatsapp_msg($name,$today_link,$mobile);
						$this->txt_msg($name,$today_link,$mobile);
						
				    }
			    }
				
				return redirect()->back()->with('success', 'Sent Successfully');
			}
			else{
				return redirect()->back()->with('error', 'Something Went Wrong.');
			}
		
		die('dddd');
		
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

   
}
