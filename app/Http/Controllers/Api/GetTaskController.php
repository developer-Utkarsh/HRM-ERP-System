<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Timetable;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use Input;
use App\FacultyRelation;
use App\ApiNotification;
use App\Users_pending;
use App\Userdetails_pending;
use App\FacultyRelations_pending;
use App\Studio;
use App\Userbranches;
use App\Task;
use App\NewTask;
use App\TaskDetail;
use App\TaskHistory;
use DB;

use Illuminate\Support\Facades\Cache;

class GetTaskController extends Controller
{
    public function hrm_add_task(Request $request)
    {
       try{
			$assign_id = $request->assign_id;
            if(isset($assign_id) && !empty($assign_id)){
	            $task_array = $request->task_array;
			    $emp_array_id	= array();
			    $title_array	= array();
				foreach ($task_array as $key => $value) {
					if(!empty($value)){
						$data = array();
						$data['title']=$data['plan']=$data['emp_id']="";
						
						$data['assign_id']=$assign_id;
						$data['date']=date('Y-m-d');

						if(!empty($value['emp_id'])){
						  $data['emp_id'] = $value['emp_id'];
						  
						  $emp_array_id[]  = $value['emp_id'];
						}else{
						  $data['emp_id'] = $assign_id;
						}
						
						if(!empty($value['title'])){
							$data['title'] = $value['title'];
							$title_array   = $value['title'];
						}

						if(!empty($value['date'])){
							$data['date'] = date("Y-m-d",strtotime($value['date']));
						}

						if(!empty($value['plan'])){
							$data['plan'] = $value['plan'];
						}

						// if(!empty($value['description'])){
							// $data['description'] = $value['description'];
						// }

						$insertID = DB::table('task_new')->insertGetId($data);
						
						if(!empty($value['description'])){
							// print_r($value['description']); die();
							foreach ($value['description'] as $key ) {
								$data = array(
									'task_id'	  => $insertID,						
									'description' => $key,
								);
													
								DB::table('task_key_points')->insert($data);
							}
						}
					}
				}
						
				//Notification Send
				
				if($emp_array_id != $assign_id){
					$get_emp = User::where('id',$assign_id)->first();
					
					$employee_id[]   	 = $emp_array_id;
					$current_date 		 = date('Y-m-d');
					$current_time 		 = date('H:i:s');
					$inputs['sender_id'] = $assign_id;
					$inputs['date'] 	 = $current_date. ' ' .$current_time; 
					
					$inputs['title'] 	 	 = 'New task assign by '.$get_emp['name'];
					$inputs['description'] 	 = $title_array;
					
					if(!empty($employee_id[0])){
						$inputs['receiver_id'] = json_encode($employee_id);
					}
					
					$inputs['type'] = 'Task';		
					$notification = ApiNotification::create($inputs);
					
					
					$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $emp_array_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Task update!!";
					$load['description'] =	"New Task Assign By ".$get_emp['name']." . Please check in task details."; 
					$load['body'] 		 =	"New Task Assign By ".$get_emp['name']." . Please check in task details.";
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	$current_date;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'Task';
					
			 
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
					}

					$this->android_notification($token_android,$load,"Android");
					$this->android_notification($token_ios,$load,"IOS");
				}
					
			  
			    return $this->returnResponse(200, true, "Task Add Successfully");
			}else{
				return $this->returnResponse(200, true, "Task Empty.");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function hrm_get_task(Request $request)
    {
        try{
			// return $hrm_get_task = Cache::rememberForever('hrm_get_task', function () use($request) {
            $get_emp_id = $request->emp_id;
            $logged_id = $request->logged_id;
			$status 	= $request->status;
			$fdate 		= $request->fdate;
			$tdate 		= $request->tdate;
			$dtype 		= $request->dtype;
			
            if(isset($logged_id) && !empty($logged_id)){
				$user_check = User::where('id', $logged_id);
				$user 		= $user_check->first();
				$role_id 	= $user->role_id;
				$department_type 	= $user->department_type;
			
				if(!empty($user)){
					$whereCond =	"1 =1  ";			
					if ($get_emp_id=="Other") {			
						$whereCond .= " AND task_new.assign_id = '". $logged_id ."' AND task_new.emp_id != '".$logged_id."'";
					}else if($get_emp_id=="aOther"){
						$whereCond .= " AND task_new.emp_id = '". $logged_id ."' and task_new.assign_id != '". $logged_id ."'";
					}else if(!empty($get_emp_id)) {
						//$whereCond .= " AND (task_new.emp_id = '".$get_emp_id."' OR task_new.assign_id = '". $logged_id ."')";
						
						if($role_id==21 || $role_id==29){
							$whereCond .= " AND ((task_new.emp_id = '".$get_emp_id."' and task_new.assign_id = '". $logged_id ."') OR (task_new.emp_id = '". $logged_id ."' AND  task_new.assign_id = '". $get_emp_id ."') OR (task_new.emp_id = '". $get_emp_id."' AND  task_new.assign_id = '". $get_emp_id ."'))"; 
						}else{
							$whereCond .= " AND ((task_new.emp_id = '".$get_emp_id."' and task_new.assign_id = '". $logged_id ."') OR (task_new.emp_id = '". $logged_id ."' AND  task_new.assign_id = '". $get_emp_id ."'))"; 
						}
					}else{			
						if($role_id==21){
							$check_supervisor = User::where('status',1)->where('is_deleted','0')->where('department_type',$department_type)->get();
							if(count($check_supervisor)>0){
								$supervisorId = '';
								foreach($check_supervisor as $key=>$value){
									if(!empty($value)){
										$supervisorId .= $value->id.",";
									}
								}
							}
							$supervisorId .= $logged_id;
							
							$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
							
						}else if($role_id==29){
							$check_supervisor = User::where('status',1)->where('is_deleted','0')->orWhere('department_type', 21)->orWhere('supervisor_id', 'LIKE', '%' . $logged_id . '%')->get();
							if(count($check_supervisor)>0){
								$supervisorId = '';
								foreach($check_supervisor as $key=>$value){
									if(!empty($value)){
										$supervisorId .= $value->id.",";
									}
								}
							}
							$supervisorId .= $logged_id;
							
							$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
						}else{
							$whereCond .= " AND ((task_new.emp_id = '".$logged_id."' and task_new.assign_id = '". $logged_id ."') OR task_new.emp_id = '". $logged_id ."' OR  task_new.assign_id = '". $logged_id ."')";
						
							// echo $whereCond;
						}
					}
					
					if (!empty($status)) {  
						$whereCond .= " AND task_key_points.status = '". $status."'";
					}
					
					
					if (!empty($fdate) && !empty($tdate)) {  
						$whereCond .= " AND task_new.date >= '". date('Y-m-d' , strtotime($fdate))."' AND task_new.date <= '". date('Y-m-d' , strtotime($tdate))."'";
					}else{
						$whereCond .= " AND task_new.date <= '". date('Y-m-d') ."' AND task_new.date >= '". date('Y-m-d', strtotime(date('Y-m-d').'-15 days'))."' AND task_key_points.status != 'Completed'";
					}
										
					if (!empty($dtype)) {  
						$whereCond .= " AND A.department_type = '". $dtype."'";
					}
	                							  
					$date_wise = DB::table('task_new')
					->select('task_new.id','task_new.title','task_new.plan','task_new.emp_id','task_new.assign_id','task_new.spent','task_new.date','A.name as assign_name','B.name as emp_name',DB::raw('count(*) as total'))
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')					
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')					
					->whereRaw($whereCond)
					->where('task_key_points.status','!=','Deleted')				
					->orderby('task_new.created_at', 'DESC')
					->groupby('task_key_points.task_id')
					->get();
					
	                $taskArray = array();
					$i = 0;
					if(count($date_wise) > 0){
						
							foreach($date_wise as $key=>$val){
								//Task key points
								$key_points = array();
								
								// if (empty($fdate) && empty($tdate)) {  
									// $wherepoints = "`task_id` = '".$val->id."' AND (`status` = 'Assign' OR `status` = 'In Progress')";
								// }else{
									// $wherepoints = "`task_id` = '".$val->id."'";
								// }
								
								
								
								$key_result = DB::table('task_key_points')->select('id','task_id','description','remark','status')->where('task_id',$val->id)->where('status','!=','Deleted')->get();
								if(count($key_result) > 0){
									foreach($key_result as $key3=>$key_result_value){
										$key_points[$key3] = $key_result_value;
									}
								}

								$taskArray[$key]['id']= $val->id;
								$taskArray[$key]['title']= $val->title;
								$taskArray[$key]['plan']= $val->plan;
								$taskArray[$key]['spent']= $val->spent;
								$taskArray[$key]['emp_id']= $val->emp_id;
								$taskArray[$key]['assign_id']= $val->assign_id;
								$taskArray[$key]['date']= date('d-m-Y', strtotime($val->date));
								$taskArray[$key]['assign_name']= $val->assign_name;
								$taskArray[$key]['emp_name']= $val->emp_name;
								$taskArray[$key]['total_task']= $val->total;
								$taskArray[$key]['sub_task']= $key_points;
							}
							$data['tasks']= $taskArray;
						
						
						return $this->returnResponse(200, true, "Task Details", $data);
					}else{
						return $this->returnResponse(200, false, "Task Not Found");
					}
				}else{
					return $this->returnResponse(200, false, "User Id Not Found"); 
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }    
				
			// });
        }catch(\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch(ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

       
	public function hrm_task_history(Request $request){
		try{
			$task_id	=	$request->task_id;
            if(isset($task_id) && !empty($task_id)){
				$task = DB::table('task_new')
					->select('task_new.*','task_key_points.id as thid','task_key_points.description as thdescription','task_key_points.status as thstatus','A.name as assign_name','B.name as emp_name','task_key_points.remark as thremark')
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')
					->where('task_new.id', $task_id)
					->where('task_key_points.status', '!=', 'Deleted')
					->orderby('task_new.created_at', 'DESC')
					->get();
					
					$tasks = array();
					$ii	=	0;
					foreach($task as $key=>$value){
						if($value->status!="Deleted"){							
							$taskdetails['id']        	= $value->thid;
							$taskdetails['description'] = $value->thdescription;
							$taskdetails['status']      = $value->thstatus;
							$taskdetails['assigned_by'] = $value->assign_name;
							$taskdetails['assigned_to'] = $value->emp_name;
							$taskdetails['remark']    	= $value->thremark;
							$taskdetails['assigned_by_id']    	= $value->assign_id;
							$taskdetails['assigned_to_id']    	= $value->emp_id;
							
							$task_array[$ii] = $taskdetails;
							$ii++;
										
						}
				}
			
				if(!empty($task_array)){
					$data['tasks'] = $task_array;
					return $this->returnResponse(200, true, "Task Details", $data);
				}else{
					return $this->returnResponse(200, false, "Task Not Found");  
				}				
            }else{
                return $this->returnResponse(200, false, "Task Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
	}

	
	public function update_spent_hour(Request $request)
    {
       try{
			$task_id = $request->task_id;
			$spent = $request->spent;
            if(isset($task_id) && !empty($task_id)){	           
				$data = array(
					'spent'	  => $spent,						
				);
									
				DB::table('task_new')->where('id',$task_id)->update($data);
			
				
			    return $this->returnResponse(200, true, "Task Spent Hour Updated Successfully");
			}else{
				return $this->returnResponse(200, true, "Task ID missing.");
			}

        
		} catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
		 
    }
	
	public function hrm_status_update(Request $request)
    {
       try{
			$id 	= $request->id;
			$status = $request->status;
			$remark = $request->remark;
            if(!empty($id) && !empty($status)){	           
				$data = array(
					'status'	  => $status,						
					'remark'	  => $remark,						
				);
									
				DB::table('task_key_points')->where('id',$id)->update($data);
			
				
			    return $this->returnResponse(200, true, "Task Status Updated Successfully");
			}else{
				return $this->returnResponse(200, true, "Task ID missing.");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function hrm_delete_task(Request $request)
    { 
        try{
            if(!empty($request->id)){  
				$child = DB::table('task_key_points')->where('id', $request->id)->first();
			
				$t1 = date('Y-m-d H:i:s', strtotime( $child->created_at ));
				$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
				
				if($t1 > $t2){ 				
					$data=array( 'status' => 'Deleted');
					$taskDeleted=DB::table('task_key_points')->where('id', $request->id)->update($data);
					if($taskDeleted){
						return $this->returnResponse(200, true, "Task Deleted Successfully");
					}else{
						 return $this->returnResponse(200, false, "Task id is invalid");
					}	
				}else{
					return $this->returnResponse(200, false, "Tasks only can be deleted up to 48 hours.");
				}
			}else{
				return $this->returnResponse(200, false, "Task id is empty");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

	public function hrm_user_list(Request $request){
		try{	
			$check		=	User::where('id',$request->emp_id)->first();
			
			$role				=	$check->role_id;
			$department_type	=	$check->department_type;
			
			$emp_id				=	$request->emp_id;
			
			if($role == 29 || $role == 24){ 
				$users = DB::table('users')
					->select('users.id','users.name','users.name','users.register_id','userdetails.degination','users.department_type')
					->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
					->where('users.status', '1')
					->where('users.role_id', '!=','1')
					->where('users.register_id', '!=', NULL)
					->where('users.is_deleted', '0')
					->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
					->orderby('name','ASC')
					->get();
			}else if($role == 21){ 
				$users = DB::table('users')
					->select('users.id','users.name','users.name','users.register_id','userdetails.degination','users.department_type')
					->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
					->where('users.status', 1)
					->where('users.role_id', '!=',1)
					->where('users.register_id', '!=', NULL)
					->where('users.is_deleted', '0')
					->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
					->orderby('name','ASC')
					->get();
			}else{
				$users = DB::table('users')
					->select('users.id','users.name','users.name','users.register_id','userdetails.degination','users.department_type')
					->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
					->where('users.status', '1')
					->where('users.is_deleted', '0')
					->where('users.role_id', '!=','1')
					->where('users.register_id', '!=', NULL)
					->whereRaw('( users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
					->orderby('name','ASC')
					->get();
			}
			
			
			$taskArray = array();
			$i = 0;
			if(count($users) > 0){
				foreach($users as $key=>$val){
					$taskArray[]=$val;
				}
				$data['users']= $taskArray;
				return $this->returnResponse(200, true, "User List", $data);
			}else{
				return $this->returnResponse(200, false, "User Not Found");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
			
	}

	public function hrm_get_department(Request $request){
		try{				
			$departments = DB::table('departments')->select('id','name')->where('is_deleted', '0')->where('status', 'Active')->get();
			
			
			$taskArray = array();
			$i = 0;
			if(count($departments) > 0){
				foreach($departments as $key=>$val){
					$taskArray[]=$val;
				}
				$data['departments']= $taskArray;
				return $this->returnResponse(200, true, "Department List", $data);
			}else{
				return $this->returnResponse(200, false, "Department Not Found");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
	}
}
