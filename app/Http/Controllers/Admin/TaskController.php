<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Task;
use App\TaskDetail;
use App\User;
use Input;
use Excel;
use App\Exports\TaskExport;
use Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$role_id = Auth::user()->role_id;
		$login_id = Auth::user()->id;
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $search = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
        $department_type = Input::get('department_type');
		
		$employeeArray   = array(); 
		$empArray   = array(); 
		$supervisorId[]  = $login_id;
        $i               = 0;	

		
		$employeeArray2 = array();
		if($role_id == 20){
			$department_type = Input::get('department_type');
			$consitions = "";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}
			
			$usr = "SELECT id FROM users WHERE status = '1' AND register_id IS NOT NULL AND (supervisor_id LIKE  '%$login_id%' OR id = $login_id )$consitions";
			
			// print_r($usr);
		}
		else if($role_id == 29 || $role_id == 24){
			$department_type = Input::get('department_type');
			$consitions = ""; 
			$consitions .=" status = 1";
			$consitions .=" AND role_id != 1";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}

			$usr = "SELECT id FROM users WHERE $consitions";

		}else{			
			$user            = User::where('status', 1)->where('id', $login_id);
			$user            = $user->first();
			$department_type = $user->department_type;

			$usr = "SELECT id FROM users WHERE role_id != '1' AND department_type = $department_type AND department_type IS NOT NULL";
			
		}
	    
		
        $task = Task::with(['user'=>function ($q) use ($usr) {
			$q->whereRaw("id IN($usr)");
		}, 'task_details']);

		if(!empty($search)) {
			$task->whereHas('user', function ($q) use ($search,$usr) { 
				$q->where('name', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->orWhere('mobile', 'LIKE', '%' . $search . '%')->orWhere('register_id', 'LIKE', '%' . $search)->whereRaw("id IN($usr)");
			});
		}
		else{
			$task->whereHas('user', function ($q) use ($usr) { 
				$q->whereRaw("id IN($usr)");
			});
		}
		if(!empty($department_type)) {
			$task->whereHas('user', function ($q) use ($department_type,$usr) { 
				$q->where('department_type', $department_type)->whereRaw("id IN($usr)");
			});
		}
		if(!empty($branch_id)) {
			$task->whereHas('user.user_branches.branch', function ($q) use ($branch_id) {  
				$q->where('id', '=', $branch_id);
			});
		} 

		if (!empty($fdate) && !empty($tdate)) {  
			$task->where('date', '>=', $fdate)->where('date', '<=', $tdate);
        }
		else{ 
			$task->where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d'));
	}

        $taskDate = $task->orderBy('date','desc')->get(); 
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
        return view('admin.task.index', compact('taskDate','allDepartmentTypes'));

		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.task.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$login_id = Auth::user()->id;
        $validatedData = $request->validate([
            'emp_id' => 'required',
            'date' => 'required',
        ]);
		// print_r($request->date); die;
		if(!empty($request->emp_id)){
			$emp_id = $request->emp_id;
			$user = User::where('id', $emp_id)->first();
			if(!empty($user)){
				if($user->status=='1'){
					$task = Task::where('emp_id', $emp_id)->where('date', $request->date)->first();
					if(!empty($task)){
						
						$assigned_users_already = json_decode($task->assigned_users);  
						if(!empty($assigned_users_already)){
							$assigned_users_update = (array_unique(array_merge($assigned_users_already,array($request->emp_id)))); 
							$assigned_users_update = array_values($assigned_users_update); 
						}
						else{
							$assigned_users_update = json_encode(array($emp_id));
						}
						$updateData['assigned_users'] = json_encode($assigned_users_update);
						$task->update($updateData);
						
						
						if (is_array($request->name) && !empty($request->name)) {
							$name = $request->name;
							$plan_hour = $request->plan_hour;
							$spent_hour = $request->spent_hour;
							$status = $request->status;
							$description = $request->description;
							// print_r($request->task_array); die;
							foreach ($name as $key => $value) {
								if(!empty($value)){
									$data = array();
									$data['assigned_userid'] = $emp_id;
									$data['assigned_date'] = $request->date;
									$data['name'] = $value;
									$data['plan_hour'] = $plan_hour[$key];
									$data['spent_hour'] = $spent_hour[$key];
									$data['status'] = $status[$key];
									$data['description'] = $description[$key];
									
									$data['task_id'] = $task->id;
									TaskDetail::create($data);
								}
							}
							return redirect()->route('admin.task.index')->with('success', 'Task Added Successfully');
						}
						else{
							return redirect()->route('admin.task.index')->with('error', 'Task Empty.');
						}
						
					}
					else{
						$inputs['emp_id'] = $login_id;
						//$inputs['emp_id'] = $emp_id;
						$inputs['assigned_users'] = json_encode(array($emp_id));
						$inputs['date'] = $request->date;
						$taskSave = Task::create($inputs);
						if($taskSave){
							if (is_array($request->name) && !empty($request->name)) {
								$name = $request->name;
								$plan_hour = $request->plan_hour;
								$spent_hour = $request->spent_hour;
								$status = $request->status;
								$description = $request->description;
								foreach ($name as $key => $value) {
									if(!empty($value)){
										$data = array();
										$data['assigned_userid'] = $emp_id;
										$data['assigned_date'] = $request->date;
										$data['name'] = $value;
										$data['plan_hour'] = $plan_hour[$key];
										$data['spent_hour'] = $spent_hour[$key];
										$data['status'] = $status[$key];
										$data['description'] = $description[$key];
										
										$data['task_id'] = $taskSave->id;
										TaskDetail::create($data);
									}
								}
								return redirect()->route('admin.task.index')->with('success', 'Task Added Successfully');
							}
							else{
								return redirect()->route('admin.task.index')->with('error', 'Task Empty.');
							}
						}
						else{
							return redirect()->route('admin.task.index')->with('error', 'Something went wrong !');
						}
					}
				}
				else{
					return redirect()->route('admin.task.index')->with('error', 'User Not Active');
				}
				
			}
			else{
				return redirect()->route('admin.task.index')->with('error', 'User Id Not Found');
			}
		}
		else{
			return redirect()->route('admin.task.index')->with('error', 'User Id Not Found');
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
    public function edit($task_id)
    {
		$task = Task::with('user','user.user_details', 'task_details')->where('id',$task_id)->first();
		//echo "<pre>"; print_r($task); die;
        return view('admin.task.edit', compact('task'));
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
        $validatedData = $request->validate([
            'date' => 'required',
            // 'name' => 'required|max:150',
        ]);
		if(!empty($request->assigned) && $request->assigned = 'assigned'){  
		    TaskDetail::where('id',$request->task_details_id)->update([ 'status' => $request->task_array_status, 'description' => $request->old_description.' '.$request->task_array_description]);
			return redirect()->route("admin.task.index")->with('success', 'Task Updated Successfully');
		}
		else{
			$task = Task::where('id', $id)->first();
			
			//$inputs = $request->only('name','assistant_id','studio_slot','branch_id','floor','status');       
			
			if(!empty($task))
			{
				$emp_id = $task->emp_id;
				$task_date = $task->date;
				if (is_array($request->task_array) && !empty($request->task_array)) {
					$task_array = $request->task_array;
					// echo "<pre>"; print_r($task_array); die;
					$assigned_users = array();
					foreach ($task_array as $key => $value) {
						// print_r($value); die;
						if(!empty($value)){
							$task_detail_id = $value['task_detail_id'];
							$task_details = TaskDetail::find($task_detail_id);
							
							$data = array();
							if(!empty($value['name'])){
								$data['name'] = $value['name'];
							}
							if(!empty($value['plan_hour'])){
								$data['plan_hour'] = $value['plan_hour'];
							}
							if(!empty($value['spent_hour'])){
								$data['spent_hour'] = $value['spent_hour'];
							}
							if(!empty($value['status'])){
								$data['status'] = $value['status'];
							}
							if(!empty($value['description'])){
								$data['description'] = $value['description'];
							}
							if(!empty($value['assigned_userid'])){
								$data['assigned_userid'] = $value['assigned_userid'];
								$assigned_users[] = $value['assigned_userid'];
							}
							if(!empty($value['dropped_reason'])){
								$data['dropped_reason'] = $value['dropped_reason'];
							}
							$task_details->update($data);
							
							if(!empty($value['status'])){
								$next_day_date = date("Y-m-d", strtotime($task_date . "+1 day"));
								
								$day = date('D', strtotime($next_day_date));
								if(strtolower($day)=="sun"){
									$next_day_date = date("Y-m-d", strtotime($task_date . "+ 2 day"));
								}
								
								if($value['status']=="In Progress" || $value['status']=="Not Started"){
									$next_day_task = Task::where('emp_id', $emp_id)->where('date', $next_day_date)->first();
									if(!empty($next_day_task)){
										if(!empty($data)){
											$nextDatTaskDetailCheck = TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->first();
											if(!empty($nextDatTaskDetailCheck)){
												$data['plan_hour'] = 0;
												$nextDatTaskDetailCheck->update($data);
											}
											else{
												$data['parent_task_detail_id'] = $task_detail_id;
												$data['task_id'] = $next_day_task->id;
												$data['plan_hour'] = 0;
												TaskDetail::create($data);
											}
										}
									}
									else{
										$inputs['emp_id'] = $emp_id;
										$inputs['date'] = $next_day_date;
										$nextDatTaskSave = Task::create($inputs);
										if($nextDatTaskSave){
											$data['parent_task_detail_id'] = $task_detail_id;
											$data['task_id'] = $nextDatTaskSave->id;
											$data['plan_hour'] = 0;
											TaskDetail::create($data);
										}
									}
								}
								else{
									$next_day_task = Task::where('emp_id', $emp_id)->where('date', $next_day_date)->first();
									if(!empty($next_day_task)){
										$taskdetails = TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->first();
										
										$inputs = array('is_deleted' => '1'); 
										$taskdetails->update($inputs);

										//TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->delete();
									}
								}
							}
						}
					}
					
					if(!empty($assigned_users)){
						/*$assigned_users_already = json_decode($task->assigned_users);
						if(!empty($assigned_users_already)){
							$assigned_users_update = (array_unique(array_merge($assigned_users_already,$assigned_users)));
							$assigned_users_update = array_values($assigned_users_update);
						}
						else{
							$assigned_users_update = $assigned_users;
						}*/
						$assigned_users_update = $assigned_users;
						// print_r($assigned_users_update); die;
						$updateData['assigned_users'] = json_encode($assigned_users_update);
						$task->update($updateData);
					}
				}
				
				if(isset($request->date) && !empty($request->date)){
					$data_update['date'] = $request->date;
					$task->update($data_update);
					
					return redirect()->route("admin.task.index")->with('success', 'Task Updated Successfully');
				}
				else{
					return redirect()->route("admin.task.index")->with('error', 'Something Went Wrong !');
				}
				
				
				
			}
			else {
				return redirect()->route("admin.task.index")->with('error', 'Something Went Wrong !');
			}
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
        $task = Task::find($id);
		$inputs = array('is_deleted' => '1');
			
        if ($task->update($inputs)) {
			$taskDetail = TaskDetail::where('task_id', $id);
			$inputs2 = array('is_deleted' => '1');
			$taskDetail->update($inputs2);
		
			// TaskDetail::where('task_id', $id)->delete();
            return redirect()->back()->with('success', 'Task Deleted Successfully');
        } else {
            return redirect()->route('admin.task.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function task_detail_delete($id,$task_id)
    {
        $taskDetail = TaskDetail::find($id);
		$inputs = array('is_deleted' => '1');

        if ($taskDetail->update($inputs)) {
            return redirect()->back()->with('success', 'Task Detail Deleted Successfully');
        } else {
            return redirect()->route("admin.task.view.$task_id")->with('error', 'Something Went Wrong !');
        }
    }
	
	public function view($task_id)
    {
		$task = Task::with('user','user.user_details', 'task_details')->where('id',$task_id)->first();
		// echo "<pre>"; print_r($task); die;
		return view('admin.task.view', compact('task'));
	}
	
	public function task_history($task_id,$task_detail_id)
    {
		$task_history_get = array();
		$task = Task::with('user','task_details')->where('id',$task_id)->first();
		$task_history = TaskDetail::where('id',$task_detail_id)->first();
		if(!empty($task_history)){
			$parent_task_detail_id = $task_history->parent_task_detail_id;
			if($parent_task_detail_id!=0){
				$task_history_get = TaskDetail::where('id',$parent_task_detail_id)->orWhere('parent_task_detail_id',$parent_task_detail_id)->orderBy('id', 'asc')->get();
			}
			else{
				$task_history_get = TaskDetail::where('id',$task_detail_id)->orWhere('parent_task_detail_id',$task_detail_id)->orderBy('id', 'asc')->get();
			}
		}
		return view('admin.task.task_history', compact('task','task_history_get'));
	}
	
	public function get_branchwise_employee(Request $request){

        $branch_id = $request->branch_id;
		if(!empty($branch_id)){
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
		else{
			$userdeatils = Userbranches::with([
				'user' => function($q){
					$q->where('role_id', '!=', '1')->where('status', '1')->where('register_id', '!=', NULL);
				}
			])->get();
			
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
		}	
	}

    public function download_excel()
    {
		$role_id = Auth::user()->role_id;
		$login_id = Auth::user()->id;
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $search = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
        $department_type = Input::get('department_type');
		
		$employeeArray   = array(); 
		$empArray   = array(); 
		$supervisorId[]  = $login_id;
        $i               = 0;	

		
		$employeeArray2 = array();
		if($role_id == 20){
			$department_type = Input::get('department_type');
			$consitions = "";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}

			$usr = "SELECT id FROM users WHERE status = '1' AND register_id IS NOT NULL AND supervisor_id LIKE  '%$login_id%' $consitions";
		}
		else if($role_id == 29 || $role_id == 24){
			$department_type = Input::get('department_type');
			$consitions = ""; 
			$consitions .=" status = 1";
			$consitions .=" AND role_id != 1";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}

			$usr = "SELECT id FROM users WHERE $consitions";

		}else{			
			$user            = User::where('status', 1)->where('id', $login_id);
			$user            = $user->first();
			$department_type = $user->department_type;

			$usr = "SELECT id FROM users WHERE role_id != '1' AND department_type = $department_type AND department_type != NULL";
			
		}
	
	
        $task = Task::with(['user'=>function ($q) use ($usr) {
			$q->whereRaw("id IN($usr)");
		}, 'task_details']);

		if(!empty($search)) {
			$task->whereHas('user', function ($q) use ($search,$usr) { 
				$q->where('name', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->orWhere('mobile', 'LIKE', '%' . $search . '%')->orWhere('register_id', 'LIKE', '%' . $search)->whereRaw("id IN($usr)");
			});
		}
		else{
			$task->whereHas('user', function ($q) use ($usr) { 
				$q->whereRaw("id IN($usr)");
			});
		}
		if(!empty($department_type)) {
			$task->whereHas('user', function ($q) use ($department_type,$usr) { 
				$q->where('department_type', $department_type)->whereRaw("id IN($usr)");
			});
		}
		if(!empty($branch_id)) {
			$task->whereHas('user.user_branches.branch', function ($q) use ($branch_id) {  
				$q->where('id', '=', $branch_id);
			});
		} 

		if (!empty($fdate) && !empty($tdate)) {  
			$task->where('date', '>=', $fdate)->where('date', '<=', $tdate);
        }
		else{ 
			$task->where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d'));
	}

        $taskDate = $task->orderBy('date','desc')->get(); 
        if(count($taskDate) > 0){
            return Excel::download(new TaskExport($taskDate), 'TaskData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function download_pdf() {
		$role_id = Auth::user()->role_id;
		$login_id = Auth::user()->id;
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $search = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$employeeArray   = array(); 
		$empArray   = array(); 
		$supervisorId[]  = $login_id;
        $i               = 0;	

		
		$employeeArray2 = array();
		if($role_id == 20){
			$department_type = Input::get('department_type');
			$consitions = "1=1";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}
			$team_users = User::where('status', 1)->where('register_id', "!=", null);
			$team_users->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ');
			$team_users->whereRaw($consitions);
			$team_users = $team_users->get();
			
			$teamArray   = array();
			if(!empty($team_users)){
				foreach($team_users as $key=>$team_usersValue){
					if(!empty($team_usersValue)){
						//if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
							$supervisorId[] = $team_usersValue->id;
							$teamArray[$i]['id'] = $team_usersValue->id;
							$teamArray[$i]['name'] = $team_usersValue->name;
							$teamArray[$i]['register_id'] = $team_usersValue->register_id;
							$teamArray[$i]['role_name'] = $team_usersValue->role->name;
							$i++;
						//}
					}
					
				}
			}
			$usr= $login_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $teamArray));
			$employeeArray2   = array_filter(explode(',',$usr));

			//$employeeArray2[] = $login_id;
		}
		else if($role_id == 29 || $role_id == 24){
			$department_type = Input::get('department_type');
			$consitions = "1=1"; 
			$consitions .=" AND status = 1";
			$consitions .=" AND role_id != 1";
			if(!empty($department_type)){
				$consitions .=" AND department_type = '$department_type'";
			}
			$employees = User::with('user_details','role')
				->whereRaw($consitions)
				->orderBy('id','desc')
				->get();
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
						$supervisorId[] = $value->id;
						$empArray[$i]['id']  = $value->id;
						$empArray[$i]['name']    = $value->name;
						$empArray[$i]['register_id'] = $value->register_id;
						$empArray[$i]['role_name'] = $value->role->name;
						$i++;
					}
					
				}
			}
			$usr= $login_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $empArray));
			$employeeArray2   = array_filter(explode(',',$usr));
		}else{			
			$user            = User::where('status', 1)->where('id', $login_id);
			$user            = $user->first();
			$department_type = $user->department_type;
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
			$usr= $login_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $employeeArray));
			$employeeArray2= array_filter(explode(',',$usr));
		}
		//echo '<pre>'; print_r($employeeArray2);die;
		
        $task = Task::with('user', 'task_details','user.user_branches');

        
		
		if (!empty($fdate) && !empty($tdate)) {
            $task->where('date', '>=', $fdate)->where('date', '<=', $tdate);
        }
		else{
			$task->where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d'));
		}
        $task = $task->orderBy('date', 'desc')->groupBy('date')->get();

		$taskDate = array();
		$j = 0;
		$cond = ''; 
		if(count($task) > 0){  
			foreach($task as $key=>$valAtt){ 
				$date = $valAtt->date;
				$taskDate[$j]['date'] = $date;
				$i = 0;
				$taskArray = array();
				
				foreach($employeeArray2 as $key2=>$employeeId){
					$emp_id2 = $employeeId;
					
					$task2   = Task::with('user', 'task_details','user.user_branches');
					
					if(!empty($search)) {
						$task2->WhereHas('user', function ($q) use ($search) { 
							return $q
							->where('name', 'LIKE', '%' . $search . '%')
							->orWhere('email', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('register_id', 'LIKE', '%' . $search);
						});
					}
					if(!empty($branch_id)) {
						$task2->WhereHas('user.user_details', function ($q) use ($branch_id) { 
							$q->where('branch_id', '=', $branch_id);
						});
					} 
					
					/* if(!empty($role)) {
						$task->WhereHas('user', function ($q) use ($role) { 
							$q->where('role_id', '=', $role);
						});
					} 
					
					if(!empty($designation)) {
						$task->WhereHas('user.user_details.degination', function ($q) use ($designation) { 
							$q->where('degination', '=', $designation);
						});
					}  */
					
					//$task2->where('date', $date)->where('emp_id', $emp_id2);
					$task2->where('date', $date)->whereRaw("(emp_id = $emp_id2 OR assigned_users LIKE '%$emp_id2%')");
					
					$task2 = $task2->get();
					//echo "<pre>"; print_r($task2); die;
					if(count($task2) > 0){
						$task_array = array();
						$statusArray = array();
						$ii = 0;
						foreach($task2 as $key=>$value){ 
							if(count($value->task_details) > 0){
								if(!empty($value->task_details->parent_id)){
									$p_id = $value->task_details->parent_id;
								}
								else{
									$p_id = $value->id; 
								}
							
								$task_history_status = Task::with('task_details')->where('id',$value->id)->orWhere('id',$p_id);
								if(!empty($p_id)) {
									$task_history_status->WhereHas('task_details', function ($q) use ($p_id) { 
										$q->orwhere('parent_task_detail_id', '=', $p_id);
									});
								} 
								
								$task_history_status = $task_history_status->get();
								
								if(count($task_history_status) > 0){ 
									foreach($task_history_status as $st=>$task_history_status_value){ 
										array_push($statusArray, $task_history_status_value->task_details[$st]->status);
									}
								}
								
								foreach($value->task_details as $task_details_value){  
									if(in_array('Completed', $statusArray)){
									$sts = 'Completed';
									}
									else{
										$sts = $task_details_value->status;
									}
									
									
									if($value->status!="Deleted"){
										$taskdetails['task_id']        = $value->id;
										$taskdetails['task_detail_id'] = $task_details_value->id;
										$taskdetails['task_title']     = $task_details_value->name;
										$taskdetails['plan_hour']      = $task_details_value->plan_hour;
										$taskdetails['spent_hour']     = $task_details_value->spent_hour;
										$taskdetails['status']         = $sts;
										$taskdetails['description']    = $task_details_value->description;
										$taskdetails['dropped_reason'] = $task_details_value->dropped_reason;
										$taskdetails['task_date']      = $task_details_value->assigned_date;
										$taskdetails['role_id']        = $role_id;
										$taskdetails['parent_id'] 	   = $task_details_value->parent_task_detail_id;
										
										$task_array[$ii]               = $taskdetails;
										$ii++;
										
												
									}
								}
								
								$statusArray = array();  
							}
							
							if(!empty($task_array)){
								if($value->emp_id==$emp_id2){
									$taskArray[$i]['emp_id'] = $value->emp_id;
								}
								else{ 
									//$taskArray[$i]['emp_id'] = $emp_id2;
									$taskArray[$i]['emp_id'] = $value->emp_id;
								}
								
								$taskArray[$i]['task_array'] = $task_array;
								$task_array = array();
								$i++;
							}
						}
						
					}
						
					$taskDate[$j]['employees'] = $taskArray;
					 
				}
				
				$j++;
				
			}
			
			
		} 
         //echo "<pre>"; print_r($taskDate); die;
		$allDepartmentTypes  = $this->allDepartmentTypes();
       		
		return view('admin.task.pdf_html', compact('taskDate','allDepartmentTypes'));
	   
		require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	    
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Task Report');
        $pdf->custom_title = 'Task Report';
        //$pdf->SetSubject('Report generated using Codeigniter and TCPDF');
        //$pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('freesans', '', 12);

        // ---------------------------------------------------------
        // $title = 'Studio Report';
		$html = view('admin.task.pdf_html', compact('taskDate'))->render();
        //echo $html; //exit();
        //Generate HTML table data from MySQL - end
        // add a page
        $pdf->AddPage();
		// echo $html; die;
		// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        $pdf->Output('Timetable_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
	}

}
