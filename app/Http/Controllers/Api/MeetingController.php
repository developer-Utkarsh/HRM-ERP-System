<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use Input;
use DB;
use Firebase\JWT\JWT;

class MeetingController extends Controller
{   
	

    public function index(Request $request)
    {	
		$api_key="Om7KPrlNfrFV6tYm4AvFL4Y5t9QDU8wtUcvY";//"RWHWkiM-Qmu_FmGog04Npg";
		$api_secret="yoQJSwR61gMWMg53tRZ4a4RBUKbvM9Md0p3g";//"VvG0SYCSskPnhc2upB5EJjBRbIV3NdwobgPL";
		
		$user_id ='admin@utkarsh.com'; //'dineshkumawat10@gmail.com';
		$api_url = "https://api.zoom.us/v2/users/";

    	try {
		
			$date = date('Y-m-d');
			if(!empty($request->date)){
				$date = $request->date;
			}
			// echo $date; die;
			if(!empty($date)){
				$tokenGet = DB::table('token')
								->select('*')
								// ->where('help_id', $helpResVal->id)
								->first();
				$get_db_toekn = $tokenGet->token;
				
				//$token = $this->get_token($api_url,$user_id,$get_db_toekn,$api_key,$api_secret);
				
				
				$allMeetings = DB::table('meetings')
								->select('*')
								// ->where('help_id', $helpResVal->id)
								->get();
				
				$responseArray = array();
				if(count($allMeetings) > 0){
					foreach($allMeetings as $key=>$val){
						$responseArray[$key]['topic'] = $val->topic;
						$responseArray[$key]['meeting_id'] = $val->meeting_id;
						$responseArray[$key]['meeting_password'] = $val->meeting_pass;
						$responseArray[$key]['date'] = $val->date;
						$responseArray[$key]['time'] = date('h:i A',strtotime($val->time));
						$responseArray[$key]['token'] = $get_db_toekn;
						$responseArray[$key]['start_url'] = $val->start_url;
					}
					
					$data['meetings'] = $responseArray;
					return $this->returnResponse(200, true, "Meetings", $data);
				}
				else{
					return $this->returnResponse(200, false, "Meeting Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Meeting Not Found"); 
			}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function get_token($api_url,$user_id,$get_db_toekn,$api_key,$api_secret)
    {
		$url = $api_url.$user_id."/token"; // Check token expire or not
		// return $url; die;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Content-Type: application/json",
		   "Authorization: Bearer ".$get_db_toekn,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($resp,true);
		// echo "<pre>"; print_R($resp); die;
		if(!empty($resp)){
			// Expire or invalid token
			require_once base_path('public/vendor/autoload.php'); 
			$token = array(
				"iss" => $api_key,
				// The benefit of JWT is expiry tokens, we'll set this one to expire in 1 minute // 3600 == 1 hour// 3600*48
				"exp" => time() + 172800
			);

			$token = JWT::encode($token, $api_secret);
			DB::table('token')
				->where('id', 1)
				->update(['token' => $token]);
			
			
		}
		else{
			// User Previous Token
			$token = $get_db_toekn;
		}
		return $token;
	}

}
