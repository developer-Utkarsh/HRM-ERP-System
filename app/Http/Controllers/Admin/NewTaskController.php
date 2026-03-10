<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\NewTask;
use App\TaskHistory;
use App\TaskDetail;
use App\User;
use Input;
use Excel;
use App\Exports\TaskExport;
use Auth;
use DB;

class NewTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$logged_role_id = Auth::user()->role_id;
		$logged_id      = Auth::user()->id;
        $branch_id      = Input::get('branch_id');
        $assistant_id   = Input::get('assistant_id');
        $assign         = Input::get('assign');
		$fdate          = Input::get('fdate');
        $tdate          = Input::get('tdate');
		$search         = Input::get('search');
		$role           = Input::get('role_id');
		$designation    = Input::get('designation_name');
		
		$users        = NewTask::getEmployeeByLogID($logged_id);
		// print_r($users); die;
		//$employeeArray[] = $logged_id;
		$employeeArray = array();
		if($logged_role_id == 20){
			//Only Show Self Task
			$employeeArray[] = $logged_id;
		}
		else{
			/* foreach($users as $usersvalue){
				$employeeArray[] = $usersvalue['id'];
			}  */
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
		}
		
		 //echo "<pre>"; print_r($usr); die;
		$date_wise_task = NewTask::groupBy('task_date');
		if(!empty($fdate) && !empty($tdate)){
			$date_wise_task->where('task_date', '>=', $fdate);
			$date_wise_task->where('task_date', '<=', $tdate);
		}
		else{
			$date_wise_task->where('task_date', date('Y-m-d'));
		}
		$date_wise_task = $date_wise_task->orderBy('task_date', 'desc')->get();
		
		$taskDate = array();
		$j = 0;
		$cond = '';
		if(count($date_wise_task) > 0){ 
			foreach($date_wise_task as $key=>$valAtt){ 
				$date = $valAtt->task_date;
				$taskDate[$j]['date'] = $date;
				$i = 0;
				$taskArray = array();
				foreach($employeeArray as $key2=>$employeeId){
					$emp_id = $employeeId;
					
					$task   = NewTask::select('*');
					
					if(!empty($search)) {
						$task->WhereHas('user', function ($q) use ($search) { 
							return $q
							->where('name', 'LIKE', '%' . $search . '%')
							->orWhere('email', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('register_id', 'LIKE', '%' . $search);
						});
					}
					if(!empty($branch_id)) {
						$task->WhereHas('user.user_details', function ($q) use ($branch_id) { 
							$q->where('branch_id', '=', $branch_id);
						});
					} 
					
					if(!empty($role)) {
						$task->WhereHas('user', function ($q) use ($role) { 
							$q->where('role_id', '=', $role);
						});
					} 
					
					if(!empty($designation)) {
						$task->WhereHas('user.user_details.degination', function ($q) use ($designation) { 
							$q->where('degination', '=', $designation);
						});
					} 
					
					$task->where('task_date', $date);
					
					$task->where(function($query) use ($emp_id,$assign) {
						if(!empty($assign) && $assign == 'assign_to_self'){   
							// $query->where('task_added_to', $emp_id)->where('task_added_by', $emp_id)->where('parent_id', 0);
							$query->where('task_added_to', $emp_id)->where('task_added_by', $emp_id);
						}
						elseif(!empty($assign) && $assign == 'assign_to_other'){
							$query->where('task_added_by', $emp_id)->where('task_added_to', '!=', 0)->where('task_added_to', '!=', $emp_id);
						}
						elseif(!empty($assign) && $assign == 'assign_by_other'){
							$query->where('task_added_to', $emp_id)->where('task_added_by', '!=', $emp_id);
						}
						else{
							$query->whereRaw(" ((task_added_to ='$emp_id' and task_added_by = '$emp_id') or (task_added_by = 'emp_id' and task_added_to !=0 and task_added_to != '$emp_id') or (task_added_to = '$emp_id' and task_added_by != '$emp_id'))");
							// $query->where('task_added_to', $emp_id)->where('task_added_by', $emp_id)->where('parent_id', 0);
							// $query->orWhere('task_added_to', $emp_id);
							// $query->orWhere('task_added_by', $emp_id)->where('task_added_to', '!=', 0);
						}
						
					});

					// $task->where(function($query) use ($emp_id) {
						// $query->where('task_added_to', $emp_id)->where('task_added_by', $emp_id)->where('parent_id', 0);
						// $query->orWhere('task_added_to', $emp_id);
						// $query->orWhere('task_added_by', $emp_id)->where('task_added_to', '!=', 0);
					// });
					
					
					
					$Deleted = "Deleted";
					$task->where('status', '!=', $Deleted);
					
					$task = $task->get();
					//echo '<pre>'; print_r($task);die;
					if(count($task) > 0){
						$task_array = array();
						$statusArray = array();
						$ii = 0;
						foreach($task as $key=>$value){
							
							if(!empty($value->parent_id)){
								$p_id = $value->parent_id;
							}
							else{
								$p_id = $value->id; 
							}
							$task_history_status = DB::table('new_tasks')->where('id',$value->id)->orWhere('id',$p_id)->orWhere('parent_id',$p_id)->get();
							
							if(count($task_history_status) > 0){
								foreach($task_history_status as $task_history_status_value){
									array_push($statusArray, $task_history_status_value->status);
								}
							}
							if(in_array('Completed', $statusArray)){
								$sts = 'Completed';
							}
							else{
								$sts = $value->status;
							}
				
				
							$created_by_task =  $value->task_added_by;
							
							$created_task_user = User::where('id', $created_by_task)->first();
							$role_id = null;
							if(!empty($created_task_user)){
								$role_id = $created_task_user->role_id;
							}
							
							if($value->task_added_by == $emp_id && $value->task_added_to == $emp_id){
								$cond = 'Assign By Self';
							}
							elseif($value->task_added_to == $emp_id){
								$cond = 'Assign By Other';
							}
							else{
								$cond = 'Assign To Other';
							}
							
							if($value->status!="Deleted"){
								
								$taskdetails['task_id']        = $value->id;
								$taskdetails['task_title']     = $value->task_title;
								$taskdetails['plan_hour']      = $value->plan_hour;
								$taskdetails['spent_hour']     = $value->spent_hour;
								$taskdetails['status']         = $sts;
								$taskdetails['description']    = $value->task_description;
								$taskdetails['dropped_reason'] = $value->dropped_reason;
								$taskdetails['task_date']      = $value->date;
								$taskdetails['role_id']        = $role_id;
								$taskdetails['task_file_link'] = url('/').'/laravel/public/'.$value->task_file_link;
								$taskdetails['parent_id'] 	   = $value->parent_id;
								$taskdetails['is_transferred'] 	   = $value->is_transferred;
								
								$assigned_userid = NULL;
								$assigned_username = NULL;
								
								
								if($cond == 'Assign To Other'){
									$assig_id = $value->task_added_to;
								}
								else{ 
									$assig_id = $value->task_added_by;
								}
								$assigned_userid = $assig_id;
							
								$addedname = User::where('id', $assigned_userid)->first();
								if(!empty($addedname)){
									$assigned_username = $addedname->name;
								}
								
								
								$taskdetails['assigned_userid'] = "$assigned_userid";
								$taskdetails['assigned_username'] = $assigned_username;
								$taskdetails['condition'] = $cond;
								
								$task_array[$ii] = $taskdetails;
								$ii++;
											
							}
							$statusArray = array();
						}
						if(!empty($task_array)){
							if($value->emp_id==$emp_id){
								$taskArray[$i]['emp_id'] = $value->emp_id;
							}
							else{
								$taskArray[$i]['emp_id'] = $emp_id;
							}
							
							$taskArray[$i]['task_array'] = $task_array;
							$i++;
						}
					}
					
					$taskDate[$j]['employees'] = $taskArray;
					
				}
				$j++;
				
			}
			
			
		}
        $task = $taskDate;
		//echo '<pre>'; print_r($task);die;
        return view('admin.newtask.index', compact('task'));
    }
	
	
	/* public function getEmployeeByLogID($login_id){
		$user            = User::where('status', 1)->where('id', $login_id);
		$user            = $user->first();
		$department_type = $user->department_type;
		
		if($user->role_id == 29 || $user->role_id == 1){
							
			$employeeArray = array();
			$supervisorId = array();
			$supervisorId[] = $login_id;
			$i = 0;
			//echo '<pre>'; print_r($employeeArray);die;
			$employees = User::with('user_details','role')->where('id', $login_id)->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->orderBy('id','desc')->get();
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){ 
					if(!in_array($value->id,$supervisorId)){
						$supervisorId[] = $value->id;
						$employeeArray[$i]['id']  = $value->id;
						$employeeArray[$i]['name']    = $value->name;
						$employeeArray[$i]['register_id'] = $value->register_id;
						$employeeArray[$i]['role_name'] = $value->role->name;
						$i++;
					}
					
				}
			}
		}
		else if($user->role_id==21){
			$employeeArray = array();
			$supervisorId[] = $login_id;
			$i = 0;
				
				
				$usrDepartmentTypebyRole = User::with('user_details','role')->where('role_id', '!=', 1)->where([['role_id', '=', 21]])->get();
				
				if(!empty($usrDepartmentTypebyRole)){
					foreach($usrDepartmentTypebyRole as $key=>$usrDepartmentTypebyRoleValue){
						if(!empty($usrDepartmentTypebyRoleValue)){
							if(!in_array($usrDepartmentTypebyRoleValue->id,$supervisorId)){
							$supervisorId[] = $usrDepartmentTypebyRoleValue->id;
							$employeeArray[$i]['id'] = $usrDepartmentTypebyRoleValue->id;
							$employeeArray[$i]['name'] = $usrDepartmentTypebyRoleValue->name;
							$employeeArray[$i]['register_id'] = $usrDepartmentTypebyRoleValue->register_id;
							$employeeArray[$i]['role_name'] = $usrDepartmentTypebyRoleValue->role->name;
							$i++;
							}
						}
						
					}
				}
				
				
				$usrDepartmentType = User::with('user_details','role')->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
				if(!empty($usrDepartmentType)){
					foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
						if(!empty($usrDepartmentTypeValue)){
							if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
								$supervisorId[] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
								$employeeArray[$i]['register_id'] = $usrDepartmentTypeValue->register_id;
								$employeeArray[$i]['role_name'] = $usrDepartmentTypeValue->role->name;
								$i++;
							}
						}
						
					}
				}
				
				$employees = User::with('user_details','role')->where('role_id', '!=', 1)->where('id', $login_id)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get(); 
				if(!empty($employees)){
					foreach($employees as $key=>$value){
						if(!empty($value)){
							if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name;
							$i++;
							}
						}
						
					}
				}
		}
		else{
			$employeeArray  = array();
			$supervisorId[] = $login_id;
			$i = 0;
			
			
			$employees = User::with('user_details','role')->where('role_id', '!=', 1)->where('id', $login_id)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name;
							$i++;
					}
					
				}
			}
			
			
			$usrDepartmentType = User::with('user_details','role')->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			if(!empty($usrDepartmentType)){
				foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
					if(!empty($usrDepartmentTypeValue)){
							if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
								$supervisorId[] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
								$employeeArray[$i]['register_id'] = $value->register_id;
								$employeeArray[$i]['role_name'] = $value->role->name;
								$i++;
							}
					}
					
				}
			}
		}	
		return $employeeArray;
	} */
	
	public function getEmployeeOpenTaskByLogID($login_id){
		$user            = User::where('status', 1)->where('id', $login_id);
		$user            = $user->first();
		$department_type = $user->department_type;
		
		if($user->role_id == 1){
			$taskArray = array();
			$i = 0;
			
			$employees = User::get();
			if(!empty($employees)){
				foreach($employees as $key => $value){
					if(!empty($value)){ 
					
					$selfData = Self::openTask($value->id);
						
						if(!empty($selfData)){
							foreach($selfData as $taskkey => $taskvalue){
								if(!empty($taskvalue)){
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
		}
		else if($user->role_id == 29){
			$taskArray = array();
			$i = 0;
			
			$employees = User::whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key => $value){
					if(!empty($value)){ 
					
					$selfData = Self::openTask($value->id);
						
						if(!empty($selfData)){
							foreach($selfData as $taskkey => $taskvalue){
								if(!empty($taskvalue)){
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
			
			
		}
		else if($user->role_id==21){
			$taskArray = array();
			$i = 0;
			
			$departmentHeadByRole = User::where([['role_id', '=', 21]])->get();  
			if(count($departmentHeadByRole) > 0){ 
				foreach($departmentHeadByRole as $key => $departmentHeadByRoleValue){ 
					if(!empty($departmentHeadByRoleValue)){ 
					
					
						$selfData = Self::openTask($departmentHeadByRoleValue->id); 
					  
						
						if(count($selfData) >0){  
							foreach($selfData as $taskkey=>$taskvalue){ 
								if(!empty($taskvalue)){ 
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
			
			$usrDepartmentType = User::where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			if(!empty($usrDepartmentType)){
				foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
					if(!empty($usrDepartmentTypeValue)){
							
							
						$selfData = Self::openTask($usrDepartmentTypeValue->id);
					  
						
						if(count($selfData) >0){  
							foreach($selfData as $taskkey=>$taskvalue){ 
								if(!empty($taskvalue)){ 
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
						
						
					}
					
				}
			}
			
			
			$employees = User::whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!empty($value)){
						
						$selfData = Self::openTask($value->id);
					  
						
						if(count($selfData) >0){  
							foreach($selfData as $taskkey=>$taskvalue){ 
								if(!empty($taskvalue)){ 
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
		}
		else{
			$taskArray = array();
			$i = 0;
			
			if($user->role_id == 20){
				$employees = User::where('role_id', 20)->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			}
			else{
				$employees = User::whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			}
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!empty($value)){
						
						$selfData = Self::openTask($value->id);
					  
						
						if(count($selfData) >0){  
							foreach($selfData as $taskkey=>$taskvalue){ 
								if(!empty($taskvalue)){ 
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
			
			if($user->role_id == 20){
				$usrDepartmentType = User::where([['role_id', '=', 20], ['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			}
			else{
				$usrDepartmentType = User::where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			}
			
			
			if(!empty($usrDepartmentType)){
				foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
					if(!empty($usrDepartmentTypeValue)){
						
						$selfData = Self::openTask($usrDepartmentTypeValue->id);
						//echo '<pre>'; print_r($selfData);
					  
						
						if(count($selfData) >0){  
							foreach($selfData as $taskkey=>$taskvalue){ 
								if(!empty($taskvalue)){ 
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
			
									$i++;
									
								}
							}
						}
						
					}
					
				}
			}
		}
		return $taskArray;
	}
	
	public function openTask($emp_id){
		$task = NewTask::with(['user'])->orderBy('task_date', 'desc');
				$Deleted = "Deleted";
				
				$task->where('task_added_to',  0);
				$task->where('status', '!=', $Deleted);
				$task = $task->where('task_added_by', $emp_id)->get(); 
				return $task;
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role_id      = Auth::user()->role_id;
		$login_id     = Auth::user()->id;
		$users        = NewTask::getEmployeeByLogID($login_id);
		$open_ask     = Self::getEmployeeOpenTaskByLogID($login_id);
		
		//echo '<pre>'; print_r($users);die;
		
        return view('admin.newtask.add', compact('users','open_ask'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
		if(!empty(Auth::user()->id)){
			 //echo '<pre>'; print_r($request['task_file_link']);die;
			$emp_id = Auth::user()->id;
			$user = User::where('id', $emp_id)->first();
			if(!empty($user)){
				if($user->status=='1'){
					
					if(count($request->task_added_to) > 0){
							foreach($request->task_added_to as $key => $task_added_to_value){
								
								$open_task_id = $request['parent_id'][$key];
								$saveData = array(
										'task_date' => $request['task_date'],
										'task_title' => $request['task_title'][$key],
										'task_description' => $request['description'][$key],
										'task_added_by' => $emp_id,
										'plan_hour' => $request['plan_hour'][$key],
										'spent_hour' => $request['spent_hour'][$key],
										'parent_id' => $open_task_id,
										//'task_type' => $request['file_type'][$key],
										'task_priority' => $request['task_priority'][$key],
										'status' =>  $request['status'][$key],
										'task_added_to' =>  $task_added_to_value,
										);
								
								/* if(!empty($request['parent_id'][$key])){
									$parent_id = $request['parent_id'][$key];
									
									$parent_data = NewTask::where('id', $parent_id)->first();
									
									$saveData = array(
											'task_date' => $request['task_date'],
											'task_title' => $parent_data->task_title,
											'task_description' => $parent_data->task_description,
											'task_added_by' => $emp_id,
											'plan_hour' => $parent_data->plan_hour,
											'spent_hour' => $parent_data->spent_hour,
											'parent_id' => $parent_id,
											'task_priority' => $parent_data->task_priority,
											'status' =>  $parent_data->status,
											);
											
									if(!empty($task_added_to_value)){
										$saveData['task_added_to'] = $task_added_to_value;
									}
								}
								else{
									$parent_id = 0;
									
									$saveData = array(
											'task_date' => $request['task_date'],
											'task_title' => $request['task_title'][$key],
											'task_description' => $request['description'][$key],
											'task_added_by' => $emp_id,
											'plan_hour' => $request['plan_hour'][$key],
											'spent_hour' => $request['spent_hour'][$key],
											'parent_id' => $parent_id,
											//'task_type' => $request['file_type'][$key],
											'task_priority' => $request['task_priority'][$key],
											'status' =>  $request['status'][$key],
											);
											
									if(!empty($task_added_to_value)){
										$saveData['task_added_to'] = $task_added_to_value;
									}
								} */
								
								
								$task = NewTask::create($saveData);
								if($open_task_id > 0){
									$task_details  = NewTask::where('id', $open_task_id)->first();
									if($task_details->task_added_to == 0){
										$open_task_update = NewTask::find($open_task_id);
										$open_task_update->update(['task_added_to'=>NULL]);
									}
								}
								
								
								if(!empty($task->id)){
										$saveData['task_id'] = $task->id;
										$task_history = TaskHistory::create($saveData);
								}

								// if(!empty($request->task_added_by) && !empty($tsk_value['task_added_to']) && $request->task_added_by != $tsk_value['task_added_to']){

									// $task_added_to_detail = User::where('id', $tsk_value['task_added_to'])->first();

									// if($task_added_to_detail){
										// if($task_added_to_detail->gsm_token){
											// $gsm_name = $user->name;
											// $gsm_status = 'Pending';
											// $load = array();
											// $load['title'] = 'Task Assigned';
											// $load['description'] = "Task Assigend By $gsm_name";
											// $load['status'] = $gsm_status;
											// $load['type'] = 'Task';
											
											// $token = $task_added_to_detail->gsm_token;

											// $this->android_notification($token, $load);
										// }   
									// }

								// }
							}
						}
						
						
					return redirect()->route("admin.newtask.index")->with('success', 'Task Add Successfully');
					
				}
				else{
					return redirect()->route('admin.newtask.index')->with('error', 'User Not Active');
				}
				
			}
			else{
				return redirect()->route('admin.newtask.index')->with('error', 'User Id Not Found');
			}
		
		}
		else{
			return redirect()->route('admin.newtask.index')->with('error', 'Add By Id Not Found');
		}
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
    public function edit($user_id, $date)
    {   
		$date = date('Y-m-d',$date);
		$login_id     = Auth::user()->id;
	    $users        = NewTask::getEmployeeByLogID($login_id);
		$task_details         = NewTask::with('user','user.user_details')->where(['task_added_to'=>$user_id, 'task_date'=>$date,'is_transferred'=>0])->get();
        return view('admin.newtask.edit', compact('task_details','users','user_id','date'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
	public function update(Request $request, $user_id){
		$login_id     = Auth::user()->id;
		// echo $user_id; die;	
        // $task_id       = $id;				
        /* if(!empty($task_id)){			
            $get_task_added_by = NewTask::where('id', $task_id)->first();
            $task_added_by     = $get_task_added_by->task_added_by;	
        } */
			//echo '<pre>'; print_r($request->task_added_to);die;
			$user = User::where('id', $user_id)->first();
			if(!empty($user)){
    			if($user->status=='1'){
						$task_arr = $request->task_array;
						if(!empty($task_arr)){
							foreach($task_arr as $key=>$tsk_value){
								$task_id       = $tsk_value['task_id'];
								$task_details  = NewTask::where('id', $task_id)->first();
								$task_detailss = NewTask::find($task_id);
								if(!empty($task_details)){
									$data = array();
									if($tsk_value['status'] == "In Progress" || $tsk_value['status'] == "Not Started"){

										if(!empty($tsk_value['task_date']) && $tsk_value['task_date'] == date('Y-m-d')){
											$next_day_date = date("Y-m-d", strtotime("+ 1 day"));		
											$day = date('D', strtotime($next_day_date));
											if(strtolower($day)=="sun"){
												$next_day_date = date("Y-m-d", strtotime("+ 2 day"));
											}
										}
										else{
											$next_day_date = $tsk_value['task_date'];
										}
									}
									else{
										$next_day_date = $tsk_value['task_date'];
									}
									
									if($tsk_value['task_added_to']==$task_details->task_added_to){
										$data['task_added_by'] = $task_details->task_added_by;
									}
									else{
										$data['task_added_by'] = $login_id;
									}

									$data['task_date'] = $next_day_date;
									$data['task_title'] = $tsk_value['task_title'];
									$data['task_description'] = $tsk_value['task_description'];
									
									$data['task_added_to'] = $tsk_value['task_added_to'];
									$data['plan_hour'] = $tsk_value['plan_hour'];
									$data['spent_hour'] = $tsk_value['spent_hour'];
									$data['task_priority'] = $tsk_value['task_priority'];
									$data['status'] = $tsk_value['status'];
									
									if($tsk_value['status'] == "Completed"){
											$task_detailss->update($data);
											if(!empty($task_details->id)){
												$task_history = TaskHistory::create([
														'task_id'   => $task_details->id,
														'task_date' => $tsk_value['task_date'],
														'task_title' => $tsk_value['task_title'],
														'task_description' => $tsk_value['task_description'],
														'task_added_by' => $data['task_added_by'],
														'task_added_to' => $tsk_value['task_added_to'],
														'plan_hour' => $tsk_value['plan_hour'],
														'spent_hour' => $tsk_value['spent_hour'],
														'parent_id' => $task_details->parent_id,
														'task_priority' => $tsk_value['task_priority'],
														'status' => $tsk_value['status'],
														//'remark' => $tsk_value['remark']

												]);
											} 
									}
									else{
										if($tsk_value['task_added_to'] != $task_details->task_added_to){
											//is_transferred
											$task_detailss->update(['is_transferred'=>1]);
										}
										
										if($task_details->parent_id==0){
											$data['parent_id'] = $task_details->id;
										}
										else{
											$data['parent_id'] = $task_details->parent_id;
										}
										$insert_task = $task_details->create($data);

										if(!empty($tsk_value['task_id'])){
											$task_history = TaskHistory::create([
													'task_id'   => $insert_task->id,
													'task_date' => $tsk_value['task_date'],
													'task_title' => $tsk_value['task_title'],
													'task_description' => $tsk_value['task_description'],
													'task_added_by' => $data['task_added_by'],
													'task_added_to' => $tsk_value['task_added_to'],
													'plan_hour' => $tsk_value['plan_hour'],
													'spent_hour' => $tsk_value['spent_hour'],
													'parent_id' => $data['parent_id'],
													'task_priority' => $tsk_value['task_priority'],
													'status' => $tsk_value['status'],
													//'remark' => $tsk_value['remark']

											]);
										}
									}

									

								}
							}
						}
									
    				     
    					return redirect()->route("admin.newtask.index")->with('success', 'Task Update Successfully');
    			}
    			else{
    				return redirect()->route('admin.newtask.index')->with('error', 'User Not Active');
    			}
        	}
        	else{
				return redirect()->route('admin.newtask.index')->with('error', 'Something is Worng');
			}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function task_delete($id)
    {
        $newtask = NewTask::find($id);
        $inputs = array('is_deleted' => '1');
        if ($newtask->update($inputs)) {
            return redirect()->back()->with('success', ' New Task Deleted Successfully');
        } else {
            return redirect()->route('admin.newtask.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function task_detail_delete($id,$task_id)
    {
        $taskDetail = TaskDetail::find($id);

        if ($taskDetail->delete()) {
            return redirect()->back()->with('success', 'Task Detail Deleted Successfully');
        } else {
            return redirect()->route("admin.task.view.$task_id")->with('error', 'Something Went Wrong !');
        }
    }
	
	public function view($task_id)
    {
		$newtask = NewTask::with('user','user.user_details')->where('id',$task_id)->first();
		 //echo "<pre>"; print_r($newtask); die;
		return view('admin.newtask.view', compact('newtask'));
	}
	
	public function task_history($task_id)
    {
		$task_history_get = array();
		$task_details = NewTask::where('id',$task_id)->first();
		$p_id = $task_details->parent_id;
		if($p_id != 0){
			$task_history_get = NewTask::where('id',$p_id)->orWhere('parent_id',$p_id)->orderBy('id', 'asc')->get();
			// $task_history_get = NewTask::whereRaw("(id = $p_id or parent_id = $p_id)")->orderBy('id', 'asc')->get();

			// echo "<pre>"; print_r($task_history_get); die;
		}
		else{
			$task_history_get = NewTask::where('id',$task_id)->orWhere('parent_id',$task_id)->orderBy('id', 'asc')->get();
		}
		
		// echo  "<pre>"; print_r($task_history_get); die;
		// $task_history_get = NewTask::with('user')->where('id',$task_id)->orWhere('id',$p_id)->orWhere('parent_id',$p_id)->orWhere('task_added_to',$task_add_to)->get();
		return view('admin.newtask.task_history', compact('task_history_get'));
	}
	
	public function get_branchwise_employee(Request $request){

        $branch_id = $request->branch_id;

        $userdeatils = Userbranches::with([
            'user' => function($q){
                $q->where('role_id', '!=', '1')->where('status', '1')->where('register_id', '!=', NULL);
            }
        ])->where('branch_id', $branch_id)->get();
        
        if (!empty($userdeatils)) {
			$res = "<option value=''> Select Employee </option>";
            foreach ($userdeatils as $key => $value) {
                if(!empty($value->user->name) && !empty($value->user->name)){
                    $res .= "<option value='". $value->user->id ."'>" . $value->user->name ." (".$value->user->register_id.")"."</option>"; 
				}
           }
		   echo $res;
           exit();
		} 
		else{
			echo $res = "<option value=''> Employee Not Found </option>";
			die();
		}
	}

    public function download_excel()
    { 
	
		$allTark        = array();
		$logged_role_id = Auth::user()->role_id;
		$logged_id      = Auth::user()->id;
        $branch_id      = Input::get('branch_id');
        $assistant_id   = Input::get('assistant_id');
        $assign         = Input::get('assign');
		$fdate          = Input::get('fdate');
        $tdate          = Input::get('tdate');
		$search         = Input::get('search');
		$role           = Input::get('role_id');
		$designation    = Input::get('designation_name');
		
		$users        = NewTask::getEmployeeByLogID($logged_id);
		$employeeArray = array();
		if($logged_role_id == 20){
			$employeeArray[] = $logged_id;
		}
		else{
			$usr             = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
			$employeeArray   = explode(',',$usr);
		}
		
		$i = 0;
		$task_array = array();
		foreach($employeeArray as $key2=>$employeeId){
			$emp_id = $employeeId;
			
			$task   = NewTask::select('*');
			
			if(!empty($search)) {
				$task->WhereHas('user', function ($q) use ($search) { 
					return $q
					->where('name', 'LIKE', '%' . $search . '%')
					->orWhere('email', 'LIKE', '%' . $search . '%')
					->orWhere('mobile', 'LIKE', '%' . $search . '%')
					->orWhere('register_id', 'LIKE', '%' . $search);
				});
			}
			if(!empty($branch_id)) {
				$task->WhereHas('user.user_details', function ($q) use ($branch_id) { 
					$q->where('branch_id', '=', $branch_id);
				});
			} 
			
			if(!empty($role)) {
				$task->WhereHas('user', function ($q) use ($role) { 
					$q->where('role_id', '=', $role);
				});
			} 
			
			if(!empty($designation)) {
				$task->WhereHas('user.user_details.degination', function ($q) use ($designation) { 
					$q->where('degination', '=', $designation);
				});
			} 
			
			if(!empty($fdate) && !empty($tdate)){
				$task->where('task_date', '>=', $fdate);
				$task->where('task_date', '<=', $tdate);
			}
			else{
				$task->where('task_date', date('Y-m-d'));
			}
			
			$task->where(function($query) use ($emp_id,$assign) {
				if(!empty($assign) && $assign == 'assign_to_self'){   
					$query->where('task_added_to', $emp_id)->where('task_added_by', $emp_id);
				}
				elseif(!empty($assign) && $assign == 'assign_to_other'){
					$query->where('task_added_by', $emp_id)->where('task_added_to', '!=', 0)->where('task_added_to', '!=', $emp_id);
				}
				elseif(!empty($assign) && $assign == 'assign_by_other'){
					$query->where('task_added_to', $emp_id)->where('task_added_by', '!=', $emp_id);
				}
				else{
					$query->whereRaw(" ((task_added_to ='$emp_id' and task_added_by = '$emp_id') or (task_added_by = 'emp_id' and task_added_to !=0 and task_added_to != '$emp_id') or (task_added_to = '$emp_id' and task_added_by != '$emp_id'))");
				}
				
			});
			
			
			
			$Deleted = "Deleted";
			$task->where('status', '!=', $Deleted);
			
			$task = $task->get();
			
			
			if(count($task) > 0){
				// $task_array = array();
				$statusArray = array();
				$ii = 0;
				foreach($task as $key=>$value){
					
					if(!empty($value->parent_id)){
						$p_id = $value->parent_id;
					}
					else{
						$p_id = $value->id; 
					}
					$task_history_status = DB::table('new_tasks')->where('id',$value->id)->orWhere('id',$p_id)->orWhere('parent_id',$p_id)->get();
					
					if(count($task_history_status) > 0){
						foreach($task_history_status as $task_history_status_value){
							array_push($statusArray, $task_history_status_value->status);
						}
					}
					if(in_array('Completed', $statusArray)){
						$sts = 'Completed';
					}
					else{
						$sts = $value->status;
					}
		
		
					$created_by_task =  $value->task_added_by;
					
					$created_task_user = User::where('id', $created_by_task)->first();
					$role_id = null;
					if(!empty($created_task_user)){
						$role_id = $created_task_user->role_id;
						$register_id = $created_task_user->register_id;
					}
					
					if($value->task_added_by == $emp_id && $value->task_added_to == $emp_id){
						$cond = 'Assign By Self';
					}
					elseif($value->task_added_to == $emp_id){
						$cond = 'Assign By Other';
					}
					else{
						$cond = 'Assign To Other';
					}
					
					if($value->status!="Deleted"){
						
						$taskdetails['task_id']        = $value->id;
						$taskdetails['task_title']     = $value->task_title;
						$taskdetails['plan_hour']      = $value->plan_hour;
						$taskdetails['status']         = $sts;
						$taskdetails['task_date']      = $value->task_date;
						$taskdetails['role_id']        = $role_id;
						$taskdetails['register_id']    = $register_id;
						
						$assigned_userid = NULL;
						$assigned_username = NULL;
						
						
						if($cond == 'Assign To Other'){
							$assig_id = $value->task_added_to;
						}
						else{ 
							$assig_id = $value->task_added_by;
						}
						$assigned_userid = $assig_id;
					
						$addedname = User::where('id', $assigned_userid)->first();
						if(!empty($addedname)){
							$assigned_username = $addedname->name;
						}
						
						
						$taskdetails['assigned_userid'] = "$assigned_userid";
						$taskdetails['assigned_username'] = $assigned_username;
						$taskdetails['condition'] = $cond;
						
						$task_array[$i] = $taskdetails;
						$i++;
									
					}
					$statusArray = array();
				} 
				/* if(!empty($task_array)){
					
					$taskArray[$i] = $task_array;  
					$i++;
				} */
			}
		}		
		//echo '<pre>'; print_r($task_array);die;
		
		
        if(count($task_array) > 0){
            return Excel::download(new TaskExport($task_array), 'TaskData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function open_task(){
		$role_id         = Auth::user()->role_id;
		$login_id        = Auth::user()->id;
		$department_type = Auth::user()->department_type;
		$fdate           = Input::get('fdate');
        $tdate           = Input::get('tdate');
		$open_task       = array();
		if($role_id==1){
			$taskArray = array();
			$i = 0;
			
			$employees = User::get();
			if(!empty($employees)){
				foreach($employees as $key => $value){
					if(!empty($value)){ 
					
					$selfData = Self::selfTask($value->id,$fdate,$tdate);
						if(!empty($selfData)){
							foreach($selfData as $taskkey => $taskvalue){
								if(!empty($taskvalue)){
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_date']        = $taskvalue->task_date;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
									$taskArray[$i]['task_description'] = $taskvalue->task_description;
									$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
									$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
									$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
									$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
									$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
									$taskArray[$i]['task_type']        = $taskvalue->task_type;
									$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
									$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
									$taskArray[$i]['status']           = $taskvalue->status;
									$taskArray[$i]['remark']           = $taskvalue->remark;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
			$open_task = $taskArray;
		}
		else if($role_id==29){
			$taskArray = array();
			$i = 0;
			
			$employees = User::where('role_id', '!=', 20)->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key => $value){
					if(!empty($value)){ 
					
					$selfData = Self::selfTask($value->id,$fdate,$tdate);
						if(!empty($selfData)){
							foreach($selfData as $taskkey => $taskvalue){
								if(!empty($taskvalue)){
									
									$taskArray[$i]['id']               = $taskvalue->id;
									$taskArray[$i]['task_date']        = $taskvalue->task_date;
									$taskArray[$i]['task_title']       = $taskvalue->task_title;
									$taskArray[$i]['task_description'] = $taskvalue->task_description;
									$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
									$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
									$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
									$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
									$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
									$taskArray[$i]['task_type']        = $taskvalue->task_type;
									$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
									$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
									$taskArray[$i]['status']           = $taskvalue->status;
									$taskArray[$i]['remark']           = $taskvalue->remark;
			
									$i++;
									
								}
							}
						}
					}
					
				}
			}
			$open_task = $taskArray;
		}
		
		else if($role_id==21){
			$taskArray = array();
			$i = 0;
			
			$employees_already = array();
			
			$departmentHeadByRole = User::where([['role_id', '=', 21],['role_id', '!=', 20]])->get();  
			if(count($departmentHeadByRole) > 0){ 
				foreach($departmentHeadByRole as $key => $departmentHeadByRoleValue){ 
					if(!empty($departmentHeadByRoleValue)){ 
					
						if(!in_array($departmentHeadByRoleValue->id,$employees_already)){
							$employees_already[] = $departmentHeadByRoleValue->id;
							
							$selfData = Self::selfTask($departmentHeadByRoleValue->id,$fdate,$tdate); 
						  
							
							if(count($selfData) >0){  
								foreach($selfData as $taskkey=>$taskvalue){ 
									if(!empty($taskvalue)){ 
										
										$taskArray[$i]['id']               = $taskvalue->id;
										$taskArray[$i]['task_date']        = $taskvalue->task_date;
										$taskArray[$i]['task_title']       = $taskvalue->task_title;
										$taskArray[$i]['task_description'] = $taskvalue->task_description;
										$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
										$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
										$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
										$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
										$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
										$taskArray[$i]['task_type']        = $taskvalue->task_type;
										$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
										$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
										$taskArray[$i]['status']           = $taskvalue->status;
										$taskArray[$i]['remark']           = $taskvalue->remark;
				
										$i++;
										
									}
								}
							}
						}
					}
					
				}
			}
			
			$usrDepartmentType = User::where([['role_id', '!=', 20], ['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			if(!empty($usrDepartmentType)){ 
				foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
					if(!empty($usrDepartmentTypeValue)){
							
						if(!in_array($usrDepartmentTypeValue->id,$employees_already)){
							$employees_already[] = $usrDepartmentTypeValue->id;	
							$selfData = Self::selfTask($usrDepartmentTypeValue->id,$fdate,$tdate);
						  
							
							if(count($selfData) >0){  
								foreach($selfData as $taskkey=>$taskvalue){ 
									if(!empty($taskvalue)){ 
										
										$taskArray[$i]['id']               = $taskvalue->id;
										$taskArray[$i]['task_date']        = $taskvalue->task_date;
										$taskArray[$i]['task_title']       = $taskvalue->task_title;
										$taskArray[$i]['task_description'] = $taskvalue->task_description;
										$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
										$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
										$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
										$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
										$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
										$taskArray[$i]['task_type']        = $taskvalue->task_type;
										$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
										$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
										$taskArray[$i]['status']           = $taskvalue->status;
										$taskArray[$i]['remark']           = $taskvalue->remark;
				
										$i++;
										
									}
								}
							}
						}
						
						
					}
					
				}
			}
			
			
			$employees = User::where('role_id', '!=', 20)->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!empty($value)){
						if(!in_array($value->id,$employees_already)){
							$employees_already[] = $value->id;
							$selfData = Self::selfTask($value->id,$fdate,$tdate);
						  
							
							if(count($selfData) >0){  
								foreach($selfData as $taskkey=>$taskvalue){ 
									if(!empty($taskvalue)){ 
										
										$taskArray[$i]['id']               = $taskvalue->id;
										$taskArray[$i]['task_date']        = $taskvalue->task_date;
										$taskArray[$i]['task_title']       = $taskvalue->task_title;
										$taskArray[$i]['task_description'] = $taskvalue->task_description;
										$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
										$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
										$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
										$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
										$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
										$taskArray[$i]['task_type']        = $taskvalue->task_type;
										$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
										$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
										$taskArray[$i]['status']           = $taskvalue->status;
										$taskArray[$i]['remark']           = $taskvalue->remark;
				
										$i++;
										
									}
								}
							}
						}
					}
					
				}
			}
			
				
				
			$open_task = $taskArray;
		}
		else{ 
			$taskArray = array();
			$i = 0;
			$employees_already = array();
			
			$usrDepartmentType = User::where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
			
			
			 
			if(!empty($usrDepartmentType)){
				foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
					if(!empty($usrDepartmentTypeValue)){
							
						if(!in_array($usrDepartmentTypeValue->id,$employees_already)){
							$employees_already[] = $usrDepartmentTypeValue->id;	
							$selfData = Self::selfTask($usrDepartmentTypeValue->id,$fdate,$tdate);
						  
							
							if(count($selfData) >0){  
								foreach($selfData as $taskkey=>$taskvalue){ 
									if(!empty($taskvalue)){ 
										
										$taskArray[$i]['id']               = $taskvalue->id;
										$taskArray[$i]['task_date']        = $taskvalue->task_date;
										$taskArray[$i]['task_title']       = $taskvalue->task_title;
										$taskArray[$i]['task_description'] = $taskvalue->task_description;
										$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
										$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
										$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
										$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
										$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
										$taskArray[$i]['task_type']        = $taskvalue->task_type;
										$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
										$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
										$taskArray[$i]['status']           = $taskvalue->status;
										$taskArray[$i]['remark']           = $taskvalue->remark;
				
										$i++;
										
									}
								}
							}
						}
						
						
					}
					
				}
			}
			
			$employees = User::whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!empty($value)){
						if(!in_array($value->id,$employees_already)){
							$employees_already[] = $value->id;
							$selfData = Self::selfTask($value->id,$fdate,$tdate);
						  
							
							if(count($selfData) >0){  
								foreach($selfData as $taskkey=>$taskvalue){ 
									if(!empty($taskvalue)){ 
										
										$taskArray[$i]['id']               = $taskvalue->id;
										$taskArray[$i]['task_date']        = $taskvalue->task_date;
										$taskArray[$i]['task_title']       = $taskvalue->task_title;
										$taskArray[$i]['task_description'] = $taskvalue->task_description;
										$taskArray[$i]['task_added_by']    = $taskvalue->task_added_by;
										$taskArray[$i]['task_added_to']    = $taskvalue->task_added_to;
										$taskArray[$i]['plan_hour']        = $taskvalue->plan_hour;
										$taskArray[$i]['spent_hour']       = $taskvalue->spent_hour;
										$taskArray[$i]['parent_id']        = $taskvalue->parent_id;
										$taskArray[$i]['task_type']        = $taskvalue->task_type;
										$taskArray[$i]['task_file_link']   = $taskvalue->task_file_link;
										$taskArray[$i]['task_priority']    = $taskvalue->task_priority;
										$taskArray[$i]['status']           = $taskvalue->status;
										$taskArray[$i]['remark']           = $taskvalue->remark;
				
										$i++;
										
									}
								}
							}
						}
					}
					
				}
			}
			
				
				
			$open_task = $taskArray;
		}
						
		
         //echo "<pre>"; print_r($open_task); die;
        return view('admin.newtask.opentask', compact('open_task'));
	}
	
    public function selfTask($emp_id,$date_from,$date_to){
		$task = NewTask::with(['user'])->orderBy('task_date', 'desc');
				if(!empty($date_from) && !empty($date_to)){
					$task->where('task_date', '>=', $date_from);
					$task->where('task_date', '<=', $date_to);
				}
				else{
					$task->whereMonth('task_date', '=', date('m'));
				}
				$Deleted = "Deleted";
				
				$task->where('task_added_to',  0);
				$task->where('status', '!=', $Deleted);
				$task = $task->where('task_added_by', $emp_id)->get(); 
				return $task;
	}
	
	public function get_open_task_detail(Request $request){
		//echo '<pre>'; print_r($request->parent_id);die;
		if(!empty($request->parent_id)){
			$parent_data = NewTask::where('id', $request->parent_id)->first();
			
			if(!empty($parent_data)){
				echo json_encode($parent_data);
			}else{
				echo json_encode($parent_data);
			}
		}
		else{
			echo json_encode(array());
		}
	}

}
