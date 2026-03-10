<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use Input;

class BirthdaysController extends Controller
{   

	public function send_wish(Request $request){
		try{ 

			if(!empty($request->sender_id) && !empty($request->receiver_id)){
				$sender_users = User::where([['id', '=', $request->sender_id]])->first();
				if(!empty($sender_users->name)){

					$receiver_users = User::where([['id', '=', $request->receiver_id]])->first();
                    
                    if(!empty($receiver_users->gsm_token)){
						
						$inputs['sender_id']   = $request->sender_id;  
						$inputs['receiver_id'] = json_encode(array($request->receiver_id));
						$inputs['type']        = 'Birthday';    
						$inputs['title']       = 'Happy Birthday';  
						$inputs['description'] = 'Wish you a very happy birthday by '.$sender_users->name;  
						$inputs['date']        = date('Y-m-d H:i:s');        

						$birthday_notification = ApiNotification::create($inputs);


						$load                = array();
						$load['title']       = $birthday_notification->title;
						$load['description'] = $birthday_notification->description;
						$load['status']      = NULL;
						$load['type']        = 'Birthday';
						
						$token = $receiver_users->gsm_token;
						$this->android_notification($token, $load);
						
						$birthday_notification->save();
						
						return $this->returnResponse(200, true, "Birthday Notification Has Sended");
				    }
				    else{
				    	return $this->returnResponse(200, false, "Birthday Notification Has Not Sended. GSM TOKEN not found");
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

    public function get_birthdays(Request $request)
    {
    	try {
		
			$date = date('Y-m-d');
			if(!empty($request->date)){
				$date = $request->date;
			}
			// echo $date; die;
			if(!empty($date)){
				$allUsers = User::with(['user_details'])
				->where('status',1)->where('role_id','!=',1);
				$allUsers->WhereHas('user_details', function ($q) use ($date) {
					$q->whereRaw("DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT('$date', '%m-%d')");
				});
				$allUsers = $allUsers->get();
				
				$responseArray = array();
				if(count($allUsers) > 0){
					foreach($allUsers as $key=>$val){
						
						$branch_res = User::select('branches.name')->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->leftJoin('userbranches', 'userdetails.user_id', '=', 'userbranches.user_id')->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')->where('users.id',$val->id)->first();
						
						$responseArray[$key]['id'] = $val->id;
						$responseArray[$key]['name'] = $val->name;
						$responseArray[$key]['designation'] = $val->user_details->degination;
						$responseArray[$key]['branch'] = !empty($branch_res) ? $branch_res->name : '';
					}
					
					$data['birthdays'] = $responseArray;
					return $this->returnResponse(200, true, "Birthdays", $data);
				}
				else{
					return $this->returnResponse(200, false, "Birthdays Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Birthdays Not Found"); 
			}


    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
