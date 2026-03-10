<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use Input;

class AnniversaryController extends Controller
{   

    public function get_anniversary(Request $request)
    {
    	try {
		
			$date = date('Y-m-d');
			
			//$newDate = DATE_FORMAT('$date', '%m-%d');
			
			
			if(!empty($date)){
				$allUsers = User::with(['user_details'])
				->where('status',1)->where('role_id','!=',1);
				$allUsers->WhereHas('user_details', function ($q) use ($date) {
					$q->whereRaw("DATE_FORMAT(joining_date, '%m-%d') = DATE_FORMAT('$date', '%m-%d')");
				});
				$allUsers = $allUsers->get();
				//echo '<pre>'; print_r($allUsers);die;
				$responseArray = array();
				if(count($allUsers) > 0){
					foreach($allUsers as $key=>$val){ 
						$branch_res = User::select('branches.name')->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->leftJoin('userbranches', 'userdetails.user_id', '=', 'userbranches.user_id')->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')->where('users.id',$val->id)->first();

						$responseArray[$key]['id'] = $val->id;
						$responseArray[$key]['name'] = $val->name;
						$responseArray[$key]['designation'] = $val->user_details->degination;
						$responseArray[$key]['branch'] = !empty($branch_res) ? $branch_res->name : '';
					}
					
					$data['anniversary'] = $responseArray;
					return $this->returnResponse(200, true, "Anniversary", $data);
				}
				else{
					return $this->returnResponse(200, false, "Anniversary Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Anniversary Not Found"); 
			}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function send_anniversary(Request $request){
		try{ 

			if(!empty($request->sender_id) && !empty($request->receiver_id)){
				$sender_users = User::where([['id', '=', $request->sender_id]])->first();
				if(!empty($sender_users->name)){

					$receiver_users = User::where([['id', '=', $request->receiver_id]])->first();
                    
                    if(!empty($receiver_users->gsm_token)){
						
						$inputs['sender_id']   = $request->sender_id;  
						$inputs['receiver_id'] = json_encode(array($request->receiver_id));
						$inputs['type']        = 'Anniversary';    
						$inputs['title']       = 'Happy Anniversary';  
						$inputs['description'] = 'Wish you a very happy anniversary by '.$sender_users->name;  
						$inputs['date']        = date('Y-m-d H:i:s');        

						$birthday_notification = ApiNotification::create($inputs);


						$load                = array();
						$load['title']       = $birthday_notification->title;
						$load['description'] = $birthday_notification->description;
						$load['status']      = NULL;
						$load['type']        = 'Anniversary';
						
						$token = $receiver_users->gsm_token;
						$this->android_notification($token, $load);
						
						$birthday_notification->save();
						
						return $this->returnResponse(200, true, "Anniversary Notification Has Sended");
				    }
				    else{
				    	return $this->returnResponse(200, false, "Anniversary Notification Has Not Sended. GSM TOKEN not found");
				    }
		            
	            }
	            else{
	            	return $this->returnResponse(200, false, "Sender Not Found");
	            }
            }
            else{
            	return $this->returnResponse(200, false, "Please fill all fields.");
            }

		}catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
