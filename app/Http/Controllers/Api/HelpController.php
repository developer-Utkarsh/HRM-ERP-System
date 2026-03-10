<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use Input;
use DB;

class HelpController extends Controller
{   
	
	public function getHelp(Request $request){
		try {
			if(!empty($request->emp_id)){
				
				$whereCond = ' 1=1 ';
				
				if(!empty($request->status)){
					$whereCond .= ' AND help.status = "'.$request->status.'"';
				}
				
			    $whereCond .= ' AND help.emp_id = "'.$request->emp_id.'"';
				
				$helpRes = DB::table('help')->select('help.*','users.name')->join('users', 'users.id', '=', 'help.emp_id')->whereRaw($whereCond)->get();
				$responseArray = array();
				//echo '<pre>'; print_r($helpRes);die;
				if(count($helpRes) > 0){
					foreach($helpRes as $key => $helpResVal){
						$helpResponseArray = array();
						$helpDescResult  = DB::table('help_description')->select('help_description.*','users.name')->join('users', 'users.id', '=', 'help_description.emp_id')->where('help_id', $helpResVal->id)->get();
							if(count($helpDescResult) > 0){
								foreach($helpDescResult as $key1 => $helpDescResultVal){
									$helpResponseArray[$key1]['name']           = $helpDescResultVal->name;
									$helpResponseArray[$key1]['description']    = $helpDescResultVal->description;
								}
							}	
						
						$responseArray[$key]['id']                 = $helpResVal->id;
						$responseArray[$key]['emp_id']             = $helpResVal->emp_id;
						$responseArray[$key]['emp_name']           = $helpResVal->name;
						$responseArray[$key]['problem_type']       = $helpResVal->problem_type;
						$responseArray[$key]['description_array']  = $helpResponseArray;
						$responseArray[$key]['date']               = $helpResVal->date;
						$responseArray[$key]['status']             = $helpResVal->status;
						$responseArray[$key]['created_at']         = $helpResVal->created_at;
						$responseArray[$key]['updated_at']         = $helpResVal->updated_at;
					}
					
					$data['help'] = $responseArray;
					return $this->returnResponse(200, true, "Help Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Help Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Employee ID Required"); 
			}
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function addHelp(Request $request){
		try {
			
			if(!empty($request->emp_id)){
				if(!empty($request->description)){
					$helpResult = DB::table('help')->insertGetId([
											'emp_id'       => $request->emp_id,
											'problem_type' => $request->problem_type,
											'description'  => '',
											'date'         => $request->date,
											'status'       => $request->status,
											'created_at'   => date('Y-m-d H:i:s'),
										]);
					
					
					if($helpResult){
						
						DB::table('help_description')->insertGetId([
											'help_id'     => $helpResult,
											'emp_id'      => $request->emp_id, 
											'description' => $request->description,
											'created_at'  => date('Y-m-d H:i:s'),
										]);
						return $this->returnResponse(200, true, "Help Successfully Added");
					}
					else{
						return $this->returnResponse(200, false, "Something is wrong"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Description Required"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Employee ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function addHelpDescription(Request $request){
		try {
			
			if(!empty($request->help_id)){
				if(!empty($request->description)){
					if(!empty($request->emp_id)){
						$helpDesResult = DB::table('help_description')->insertGetId([
												'help_id'     => $request->help_id,
												'emp_id'      => $request->emp_id, 
												'description' => $request->description,
												'created_at'  => date('Y-m-d H:i:s'),
											]);
						
						
						if($helpDesResult){
							return $this->returnResponse(200, true, "Help Description Successfully Added");
						}
						else{
							return $this->returnResponse(200, false, "Something is wrong"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "Empolyee ID Required"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Description Required"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Help ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
