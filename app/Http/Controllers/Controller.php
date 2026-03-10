<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Department;
use App\Branch;
use Illuminate\Http\Request;
use DB;
use DateTime;


class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function userLogout(Request $request) {
		$request->session()->forget('salary_access');
		Auth::logout();
		return redirect()->route('login');
	}

	/**
     * Return api success status
     * @param $status, $data, $message
     * @return json
     */
	protected function returnResponse($status = 200, $statusText = true, $message = "Good", $data = [], $validation_error = null)
	{
		$data['message'] = $message;
		$data['status'] = $statusText;
		if (!is_null($validation_error)) {
			$data['validation_error'] = $validation_error;
		}
		return response($data, $status);
	}

	public function android_notification($token, $load,$device_type="Android") {

		$url = 'https://fcm.googleapis.com/fcm/send';
        
		if($device_type=='Android'){
			if(is_array($token)){
				//$fields = array('registration_ids' => $token,'data' => $load); //registration_ids
				$fields = array('registration_ids' => $token,'data' => $load); 
			}else{
				$fields = array('to' => $token,'data' => $load); //to
			}
		}else{
			if(is_array($token)){
				$fields = array('registration_ids' => $token,'notification' => $load); 
			}else{
				$fields = array('to' => $token,'notification' => $load); //to
			}
		}
		
		
        $headers = array('Authorization: key=AAAAzXMHxrA:APA91bHeQ42qyK13smPid98oyc3IQd3aTNYezXI3fl-3K5rzGWWscxKqK45joS5pVfwlytzOHDIUxDgezUDF6BtAYw3nDE91XjjyeCaOT0wD9bw1mxlpxnVdbWralCQqNDsgm0m07ogN', 'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, true));
        $result = curl_exec($ch);
        if ($result === FALSE) { die('Curl failed: ' . curl_error($ch)); }
        curl_close($ch);
        return $result;
    }
	
    public function smsbulk($mbl,$message_content,$templateid){
    	$message_content=urlencode($message_content);
    	$url="http://sms.smsinsta.in/vb/apikey.php?apikey=81623126665543173541&senderid=UTKRSH&templateid=$templateid&route=3&unicode=2&number=91$mbl&message=$message_content";
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		curl_close($ch);
		return true; 
	}
	
	public function current_app_version() {
		return "24";
	}
	
	public function allDepartmentTypes() {
			
		$departmentTypes = array();
		$i = 0;
		$department = Department::where('status', 'Active')->orderBy('id','asc')->get();
		if(!empty($department)){
			foreach($department as $key=>$value){
				$departmentTypes[$i]['id']  = $value->id;
				$departmentTypes[$i]['name']    = $value->name;
				$i++;
			}
		}
		return $departmentTypes;
		
	}
	
	public function allBranches() {
			
		$allBranches = array();
		$i = 0;
		$branches = Branch::where('status', '1')->orderBy('id','desc')->get();
		if(!empty($branches)){
			foreach($branches as $key=>$value){
				$allBranches[$i]['id']  = $value->id;
				$allBranches[$i]['name']    = $value->name;
				$i++;
			}
		}
		return $allBranches;
		
	}
	
	public function notificationDeviceWise($user,$load){
		$token_ios= [];
		$token_android= [];
		if(count($user) > 0){
			foreach ($user as $key => $value) {
				if(!empty($value->gsm_token)){
					if($value->device_type=='IOS'){
					  $token_ios[] = $value->gsm_token;
					}else{
					  $token_android[] = $value->gsm_token;
					}
				}
			}
			
			$this->android_notification($token_android,$load,"Android");
			$this->android_notification($token_ios,$load,"IOS");
		}	
	}

	public function whatsappForDelay($timetable_id,$delay=0){
		$timetable=DB::table('timetables')
          ->select('timetables.cdate','timetables.from_time','timetables.to_time','batch.name as batch_name','timetables.updated_at','users.name as faculty_name',
          	'users.mobile','start_classes.start_time','start_classes.end_time','start_classes.delay_type','start_classes.id as start_id')
         ->leftJoin('batch','batch.id','timetables.batch_id')
         ->leftJoin('users','users.id','timetables.faculty_id')
         ->leftJoin('start_classes','start_classes.timetable_id','timetables.id')
         ->where('timetables.id',$timetable_id)->first();
            
        $delay_from_time         = new DateTime($timetable->start_time);
		$delay_to_time           = new DateTime($timetable->from_time);
		$delay_schedule_interval = $delay_from_time->diff($delay_to_time); 
		//$delay                   = $delay_schedule_interval->format('%H : %I Hours');
		$delay                   = $delay_schedule_interval->format('%I Minutes');
		
		if($delay_schedule_interval->format('%I')<=4){
            return 'Time less than 4 Minutes';
		}


    	$var1=$timetable->faculty_name;
    	$var2=date("d-m-Y",strtotime($timetable->cdate));
        $var3=mb_substr($timetable->batch_name,0,30);
    	$var4=$delay;

    	if($timetable->delay_type=='Due to Faculty'){
            $fput="\n\n Date".date('Y-m-d h:i A').'-'.$timetable_id."-".$var1."-".$var2."-".$var3."-".$var4;
    		file_put_contents("/var/www/html/laravel/public/logs/delay.txt",$fput,FILE_APPEND);

    		$url="https://hrm.utkarshupdates.com/index.php/faculty-reports/delay-reason/".$timetable_id;
    		$url="http://15.207.232.85/index.php/faculty-reports/delay-reason/".$timetable_id;
            $url=$this->tiny_url($url);
            $var5=$url;

	    	$url = "https://api.imiconnect.in/resources/v1/messaging";
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);
			$headers = array(
			   "Content-Type: application/json",
			   "key: 0b08bf38-6dd9-11ea-9da9-025282c394f2",
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //,"91'.$timetable->mobile.'"
			//"template": "727429742872158",
			$data = '{
			    "appid": "a_158521380743240260",
			    "deliverychannel": "whatsapp",
			    "message": {
			        "template": "1320613681947052",
			        "parameters": {
			          "variable1": "'.$var1.'",
			          "variable2": "'.$var2.'",
			          "variable3": "'.$var3.'",
			          "variable4": "'.$var4.'",
			          "variable5": "'.$var5.'"
			        }
			    },
			    "destination": [{
			            "waid": ["917014151588","91'.$timetable->mobile.'"]
			        }
			    ]}';
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			$resp = curl_exec($curl);
			curl_close($curl);
			
			
			//Gupsup Message
			$params = [];
			$params['input']   		 = [$var1,$var2,$var3,$var4,$var5];
			$params['template_id']   = '274a0813-f7fe-41b2-8d62-380a01c9e674';
			$params['mobile']   	 = $timetable->mobile;
			$this->gupsup_msg($params);
		}
	}

	public function tiny_url($url){
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='. $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);	
		return $data;  
	}

	public function  utkarshAppApi($endPoint,$data,$method){
		//print_r($data);//die();
		$url="https://support.utkarshapp.com/".$endPoint;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
		    'Content-Type: application/x-www-form-urlencoded'
		  ),
		));

		echo $response = curl_exec($curl);
		curl_close($curl);
		return $result=json_decode($response);
	}
	
	
	public function getMainCategory(){	
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getReportCategories',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		// $response=file_put_contents($file_name,$response);
		
		// $response=file_get_contents($file_name);
		
		return json_decode($response);
	}
	
	
	public function gupsup_msg($params) {
		$url = "https://api.gupshup.io/wa/api/v1/template/msg";
		
		$mobiles = explode(',', $params['mobile']);

		$formattedMobiles = array_map(function ($mobile) {
			$mobile = trim($mobile);

			// Add country code if missing
			if (strpos($mobile, '91') !== 0) {
				$mobile = '91' . $mobile;
			}
			return $mobile;
		}, $mobiles);

		$destination = implode(',', $formattedMobiles);

		$postData = http_build_query([
			"template"   => json_encode([
				"id"     => $params['template_id'],
				"params" => $params['input']
			]),
			"source"     => "918905987713",
			"src.name"   => "Test",
			// "destination"=> "91" . $params['mobile'],
			"destination" => $destination,
			"channel"    => "whatsapp"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"accept: application/json",
			"apikey: dsxjdnwo0zxxwz0owkvcr6tvozvozats",
			"content-type: application/x-www-form-urlencoded"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

		$response = curl_exec($ch);
		$error    = curl_error($ch);

		curl_close($ch);

		if ($error) {
			return ["status" => "error", "message" => $error];
		} else {
			return ["status" => "success", "response" => $response];
		}
	}
}
