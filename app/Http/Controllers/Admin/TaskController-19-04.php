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
		$team_users = User::where('status', 1)->where('register_id', "!=", null);
		$team_users->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ');
		$team_users = $team_users->get();
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		//$task->whereMonth('date', '=', date('m'));
        $task = Task::with('user', 'task_details','user.user_branches');

        if (!empty($name)){
            //$task->where('users.name', 'LIKE', '%' . $name . '%');
			$task->WhereHas('user', function ($q) use ($name) { // orWhereHas dk
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }

        if (!empty($branch_id)){
            $task->WhereHas('user.user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		if (!empty($fdate) && !empty($tdate)) {
            $task->where('date', '>=', $fdate)->where('date', '<=', $tdate);
        }
		else{
			$task->where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d'));
		}
		/* elseif (!empty($fdate)) {
            $task->where('date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $task->where('date', '<=', $tdate);
        } */
        if($role_id == 21){
        	$task->WhereHas('user', function ($q) use ($login_id) {
        		$q->whereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ');
        	});
        }
		
        $task = $task->orderBy('date', 'desc')->get(); 
         //echo "<pre>"; print_r($task); die;
        return view('admin.task.index', compact('task'));
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
						$inputs['emp_id'] = $emp_id;
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
		// echo "<pre>"; print_r($request->task_detail_id); die;
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
									TaskDetail::where('parent_task_detail_id', $task_detail_id)->where('task_id', $next_day_task->id)->delete();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function task_delete($id)
    {
        $task = Task::find($id);

        if ($task->delete()) {
			TaskDetail::where('task_id', $id)->delete();
            return redirect()->back()->with('success', 'Task Deleted Successfully');
        } else {
            return redirect()->route('admin.task.index')->with('error', 'Something Went Wrong !');
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
		$branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		//$task->whereMonth('date', '=', date('m'));
        $task = Task::with('user', 'task_details','user.user_branches')->orderBy('date', 'desc');

        if (!empty($name)){
            //$task->where('users.name', 'LIKE', '%' . $name . '%');
			$task->WhereHas('user', function ($q) use ($name) { // orWhereHas dk
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }

        if (!empty($branch_id)){
            $task->WhereHas('user.user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }
		
		if (!empty($fdate) && !empty($tdate)) {
            $task->where('date', '>=', $fdate)->where('date', '<=', $tdate);
        }
		else{
			$task->where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d'));
		}
		/* elseif (!empty($fdate)) {
            $task->where('date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $task->where('date', '<=', $tdate);
        } */
		
        $task = $task->get();
		
        if(count($task) > 0){
            return Excel::download(new TaskExport($task), 'TaskData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
   

}
