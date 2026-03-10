<?php

namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;

class NewTask extends Model
{
	protected $table = 'new_tasks';
	 
    protected $fillable = ['task_date','task_title','task_description','task_added_by','task_added_to','plan_hour','spent_hour','parent_id','task_type','task_file_link','task_priority','status','remark','is_deleted'];

    public function user() {
        return $this->Belongsto(User::class, 'task_added_to', 'id');
    }
	
	public static function getEmployeeByLogID($login_id,$api_name=null,$in_active_date=null){
		//$id_array = ['5453','5441'];
		$id_array = ['901'];
		$d_id_array = ['43','44'];
		
		$user            = User::where('status', 1)->where('id', $login_id);
		$user            = $user->first();
		$department_type = $user->department_type;
		if($user->role_id == 24 || $user->role_id == 1){
		    $employeeArray = array();
			$supervisorId = array();
			$i = 0;
			if($login_id==7107 || $login_id==6166 || $login_id==7413){
				$employees = User::with('user_details','role','user_branches.branch');
					$employees->WhereHas('user_branches.branch', function ($q) use ($login_id) {
						if($login_id==7107){
							$q->where('branch_location', 'jaipur');
						}
						else if($login_id==6166){
							$q->where('branch_location', 'prayagraj');
						}
						else if($login_id==7413){
							$q->where('branch_location', 'indore');
						}
						else{
							//$q->where('branch_location', 'jodhpur');
						}
													
					});	
			}
			else{
				$employees = User::with('user_details','role');
			}
			
			$employees->where('role_id', '!=', 1)->where('is_deleted', '0');
			
			if($api_name=='attendance' || $api_name=='leave'){
				
			}
			else{
				$employees->where('status', 1);
			}
			//$employees = $employees->whereNotIn('department_type', $d_id_array)->whereNotIn('id', $id_array)->whereRaw("(register_id > 1000 or register_id = 934)")->orderBy('id','desc')->get();
			
			$employees = $employees->whereNotIn('department_type', $d_id_array)->whereNotIn('id', $id_array)->orderBy('id','desc')->get();
			
		
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
						$supervisorId[] = $value->id;
						$employeeArray[$i]['id']  = $value->id;
						$employeeArray[$i]['name']    = $value->name;
						$employeeArray[$i]['register_id'] = $value->register_id;
						$employeeArray[$i]['role_name'] = $value->role->name??''??'';
						$employeeArray[$i]['status'] = $value->status;
						$i++;
					}
					
				}
			}
		}
		else if($user->role_id == 29){
							
			$employeeArray = array();
			$supervisorId = array();
			$supervisorId[] = $login_id;
			$i = 0;
			//echo '<pre>'; print_r($employeeArray);die;
			
			if($api_name=='attendance' || $api_name=='leave'){
				// $employees = User::with('user_details','role')->where('role_id', '!=', 1)->where('department_type', '!=', 34)->orderBy('id','desc')->get();
				$employees = User::with('user_details','role')->where('role_id', '!=', 1)->where('is_permanent', '=', '1')->orderBy('id','desc')->get();
			}
			else if($api_name=='create-attendance'){
				$employees = User::with('user_details','role')->where('is_deleted', '0')->where('role_id', '!=', 1)->where('register_id','!=',NUll)->orderBy('id','desc')->get();
			}
			else if($api_name=='all-employee'){
				$employees = User::with('user_details','role')->where('is_deleted', '0')->where('role_id', '!=', 1)->orderBy('id','desc')->get();
			}
			else if($api_name=='approved-emp'){
				$employees = User::with('user_details','role')->where('status', 1)->where('is_deleted', '0')->where('role_id', '!=', 1)->orderBy('id','desc')->get(); //->where('register_id','!=',NUll)
			}
			else{
				$employees = User::with('user_details','role')->where('status', 1)->where('id', $login_id)->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->orderBy('id','desc')->get();
			}
			
			
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					$conditon = true;
					/* if($value->status==0){ 
						
						$conditon = false;
						if(!empty($value->reason_date) && strtotime($in_active_date) <= strtotime(date('Y-m',strtotime($value->reason_date)))){
							$conditon = true;
						}
					} */
					if($conditon==true){
						if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id']  = $value->id;
							$employeeArray[$i]['name']    = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??''??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
						}
					}
					
				}
			}
		}
		else if($user->role_id==21){
			$employeeArray = array();
			$supervisorId = array();
			if($api_name=='with-login-id'){
				
			}
			else{
				$supervisorId[] = $login_id;
			}
			$i = 0;

			    /*$usrDepartmentTypebyRole = User::with('user_details','role')->where('role_id', '!=', 1)->where([['role_id', '=', 21]])->get();
				
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
				}*/
				
			if($api_name=='create-attendance'){	
				$employees = User::with('user_details','role')->where('status', 1)->where('is_deleted', '0')->where('id', $login_id)->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get(); 
				if(!empty($employees)){
					foreach($employees as $key=>$value){
						if(!empty($value)){
							//if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??''??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
							//}
						}
						
					}
				}
			}
			else if($api_name == 'department-emp'){
				$usrDepartmentType = User::with('user_details','role')->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
				if(!empty($usrDepartmentType)){
					foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
						if(!empty($usrDepartmentTypeValue)){
							if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
								$supervisorId[] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
								$employeeArray[$i]['register_id'] = $usrDepartmentTypeValue->register_id;
								$employeeArray[$i]['role_name'] = $usrDepartmentTypeValue->role->name;
								$employeeArray[$i]['status'] = $usrDepartmentTypeValue->status;
								$i++;
							}
						}
						
					}
				}
			}
			else if($api_name == 'location_wise'){
				if($login_id=='1522'){
					$usrDepartmentType = User::with('user_details','role','user_branches.branch');
					$usrDepartmentType->WhereHas('user_branches.branch', function ($q) use ($login_id) {	
						$q->where('branch_location', 'jodhpur');							
					});	
					$usrDepartmentType = $usrDepartmentType->where('is_deleted', '0')->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
					if(!empty($usrDepartmentType)){
						foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
							if(!empty($usrDepartmentTypeValue)){
								if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
									$supervisorId[] = $usrDepartmentTypeValue->id;
									$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
									$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
									$employeeArray[$i]['register_id'] = $usrDepartmentTypeValue->register_id;
									$employeeArray[$i]['role_name'] = $usrDepartmentTypeValue->role->name??'';
									$employeeArray[$i]['status'] = $usrDepartmentTypeValue->status;
									$i++;
								}
							}
							
						}
					}
					
					$employees = User::with('user_details','role','user_branches.branch');
					$employees->WhereHas('user_branches.branch', function ($q) use ($login_id) {	
						$q->where('branch_location', 'jodhpur');							
					});	
					$employees = $employees->whereRaw('( (id = "$login_id" and is_deleted = "0" and role_id != 1) or ( supervisor_id LIKE  \'%"'.$login_id.'"%\'  and is_deleted = "0" and role_id != 1))')->get(); 
					if(!empty($employees)){
						foreach($employees as $key=>$value){
							if(!empty($value)){
								if(!in_array($value->id,$supervisorId)){
								$supervisorId[] = $value->id;
								$employeeArray[$i]['id'] = $value->id;
								$employeeArray[$i]['name'] = $value->name;
								$employeeArray[$i]['register_id'] = $value->register_id;
								$employeeArray[$i]['role_name'] = $value->role->name??''??'';
								$employeeArray[$i]['status'] = $value->status;
								$i++;
								}
							}
							
						}
					}
				}
				else{
					$usrDepartmentType = User::with('user_details','role')->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
					if(!empty($usrDepartmentType)){
						foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
							if(!empty($usrDepartmentTypeValue)){
								if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
									$supervisorId[] = $usrDepartmentTypeValue->id;
									$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
									$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
									$employeeArray[$i]['register_id'] = $usrDepartmentTypeValue->register_id;
									$employeeArray[$i]['role_name'] = $usrDepartmentTypeValue->role->name??'';
									$employeeArray[$i]['status'] = $usrDepartmentTypeValue->status;
									$i++;
								}
							}
							
						}
					}
					
					$employees = User::with('user_details','role')->where('id', $login_id)->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get(); 
					if(!empty($employees)){
						foreach($employees as $key=>$value){
							if(!empty($value)){
								if(!in_array($value->id,$supervisorId)){
								$supervisorId[] = $value->id;
								$employeeArray[$i]['id'] = $value->id;
								$employeeArray[$i]['name'] = $value->name;
								$employeeArray[$i]['register_id'] = $value->register_id;
								$employeeArray[$i]['role_name'] = $value->role->name??''??'';
								$employeeArray[$i]['status'] = $value->status;
								$i++;
								}
							}
							
						}
					}
				}
			}
			else{
				$usrDepartmentType = User::with('user_details','role')->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
				if(!empty($usrDepartmentType)){
					foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
						if(!empty($usrDepartmentTypeValue)){
							if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
								$supervisorId[] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['id'] = $usrDepartmentTypeValue->id;
								$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
								$employeeArray[$i]['register_id'] = $usrDepartmentTypeValue->register_id;
								$employeeArray[$i]['role_name'] = $usrDepartmentTypeValue->role->name;
								$employeeArray[$i]['status'] = $usrDepartmentTypeValue->status;
								$i++;
							}
						}
						
					}
				}
				
				$employees = User::with('user_details','role')->where('id', $login_id)->where('is_deleted', '0')->where('status', 1)->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get(); 
				if(!empty($employees)){
					foreach($employees as $key=>$value){
						if(!empty($value)){
							if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??''??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
							}
						}
						
					}
				}
			}
				
		}
		else if($user->role_id==20){
			$employeeArray  = array();
			$supervisorId = array();
			if($api_name=='with-login-id'){
				
			}
			else{
				$supervisorId[] = $login_id;
			}
			$i = 0;
			$employees = User::with('user_details','role')->where('status', 1)->where('id', $login_id)->where('role_id', '!=', 1)->where('role_id', '=', 20)->where('register_id', "!=", null)->where('is_deleted', '0')->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??''??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
					}
					
				}
			}
			
			/*if($api_name != "task_history"){
				$usrDepartmentType = User::with('user_details','role')->where('role_id', '!=', 1)->where('role_id', '=', 20)->where([['department_type', '=', $department_type], ['department_type', '!=', NULL]])->get();
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
			}*/
		}
		else if($user->role_id==27){
			$employeeArray  = array();
			$supervisorId[] = $login_id;
			$i = 0;
			$employees = User::with('user_details','role')->where('id', $login_id)->where('is_deleted', '0')->where('role_id', '!=', 1)->where('role_id', '=', 27)->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
					}
					
				}
			}
		}
		else if($user->role_id==28){
			$branch_id = Auth::user()->user_branches[0]->branch_id;
			$employeeArray  = array();
			$supervisorId = array();
			$i = 0;
			if($api_name=='leave'){
				$employees = User::with('user_details','role','user_branches');
				$employees->WhereHas('user_branches', function ($q) use ($branch_id) {	
					$q->where('branch_id', '=', $branch_id);							
				});	
				$employees = $employees->whereRaw("(status = 1 and is_deleted = '0' and role_id != 1)")->get(); 
				if(!empty($employees)){
					foreach($employees as $key=>$value){
						if(!empty($value)){
							if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
							}
						}
						
					}
				}
			}
			
			$employees = User::with('user_details','role')->where('id', $login_id)->where('is_deleted', '0')->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
					}
					
				}
			}
		}
		else{
			$employeeArray  = array();
			//$supervisorId[] = $login_id;
			$supervisorId[] = '';
			$i = 0;
			
			
			$employees = User::with('user_details','role')->where('id', $login_id)->where('is_deleted', '0')->where('role_id', '!=', 1)->orWhereRaw('supervisor_id LIKE  \'%"'.$login_id.'"%\' ')->get();
			if(!empty($employees)){
				foreach($employees as $key=>$value){
					if(!in_array($value->id,$supervisorId)){
							$supervisorId[] = $value->id;
							$employeeArray[$i]['id'] = $value->id;
							$employeeArray[$i]['name'] = $value->name;
							$employeeArray[$i]['register_id'] = $value->register_id;
							$employeeArray[$i]['role_name'] = $value->role->name??'';
							$employeeArray[$i]['status'] = $value->status;
							$i++;
					}
					
				}
			}
			
		}	
		return $employeeArray;
	}
	
	
	public static function getEmployeeForDepartmentHead($id, $role, $type,$api_name=null){
		$employeeArray = array();
		$supervisorId = array();
		$i = 0;
		//['role_id', '=', 20],
		if($api_name=='leave_add'){
			$employees = User::with('user_details','role')->where([['department_type', '=', $type],['role_id', '!=', 1],['status', '=', 1]])->orderBy('id','desc')->get();
		}elseif($api_name=="leave_record"){
			$employees = User::with('user_details','role','user_branches.branch')->whereRaw(
			'( department_type = '.$type.' OR supervisor_id LIKE  \'%"'.$id.'"%\' ) AND role_id!= 1 AND status = 1')->orderBy('id','desc');
			
			
			if($id=='1522'){
				$employees->WhereHas('user_branches.branch', function ($q) use ($id) {	
					$q->where('branch_location', 'jodhpur');							
				});	
			}
			
			$employees = $employees->get();
		}
		else{
			$employees = User::with('user_details','role')->where([['department_type', '=', $type],['role_id', '!=', 1],['status', '=', 1],['id', '!=', $id]])->orderBy('id','desc')->get();
		}
		
		
		if(!empty($employees)){
			foreach($employees as $key=>$value){
				if(!in_array($value->id,$supervisorId)){
					$supervisorId[] = $value->id;
					$employeeArray[$i]['id']  = $value->id;
					$employeeArray[$i]['name']    = $value->name;
					$employeeArray[$i]['register_id'] = $value->register_id;
					$employeeArray[$i]['role_name'] = $value->role->name??'';
					$i++;
				}
				
			}
		}
		return $employeeArray;
	}	
}
