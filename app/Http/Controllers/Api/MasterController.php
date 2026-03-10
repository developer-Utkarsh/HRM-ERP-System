<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Branch;
use App\Designation;
use App\Role;
use DB;

use Illuminate\Support\Facades\Cache;


class MasterController extends Controller
{   

	public function master_data(Request $request){
        try {
			$user_id = 0;
			if(!empty($request->user_id)){
				$user_id = $request->user_id;
			}


			$data=Cache::remember('master_data',600,function(){
			    $branches    = Branch::where('status', '=', '1')->get();
			    $designation = Designation::where('status', '=', 'Active')->get();
			    $roles       = Role::where('status', '=', '1')->get();
			    $users       = User::where([['role_id', '=', '21'],['role_id', '!=', '1'],['status', '=', '1']])->get();
	            

		        $get_branch_data = [];
                if(!empty($branches)){ 
                	foreach($branches as $branchkey=>$branchvalues){
                		$temp['id']      = $branchvalues->id;
                		$temp['name']    = $branchvalues->name;
                		$get_branch_data[] = $temp;
                		
                	}
                }
				$data['branches'] = $get_branch_data;

                $get_designation_data = [];
                if(!empty($designation)){ 
                	foreach($designation as $designationkey=>$designationvalues){
                		$temp1['id']      = $designationvalues->id;
                		$temp1['name']    = $designationvalues->name;
                		$get_designation_data[] = $temp1;
                		
                	}
                	$data['designation'] = $get_designation_data;
                }

                $get_role_data = [];
                if(!empty($roles)){ 
                	foreach($roles as $rolekey=>$rolevalues){
                		$temp2['id']      = $rolevalues->id;
                		$temp2['name']    = $rolevalues->name;
                		$get_role_data[] = $temp2;
                		
                	}
                }
				$data['roles'] = $get_role_data;

                $get_department_head_data = [];
                if(!empty($users)){ 
                	foreach($users as $userkey=>$uservalues){
                		$temp3['id']                = $uservalues->id;
                		$temp3['name']              = $uservalues->name;
                		$get_department_head_data[] = $temp3;
                		
                	}
                }
				$data['department_head'] = $get_department_head_data;
					
				$holidays = array();
				$get_holiday_data = array();
				$all_holidays = DB::table('holidays')->whereRaw("is_deleted = '0' AND status = '1' AND location = 0")->whereYear('date',date('Y'))->orderBy('date','asc')->get();
				if(!empty($all_holidays)){
					foreach($all_holidays as $val){
						$temp4['date']      =  date("d-m-Y", strtotime($val->date));
                		$temp4['title']    = $val->title;
                		$temp4['type']    =  $val->type;
                		$get_holiday_data[] = $temp4;
					}					
				}
				$data['holidays'] = $get_holiday_data;

				return $data;
			});
				


				$app_config = array();
				$app_config['is_report'] = 1;
				$login_user   = User::with(['user_details'])->where('id', '=', $user_id)->where('status', 1)->where('is_deleted', '0')->first();
				if(!empty($login_user)){
					$role_id = $login_user->role_id;
					
					if($role_id==2){
						$app_config['is_report'] = 1;
					}
					else if($login_user->user_details->degination == 'CONTENT + FACULTY'){
						$app_config['is_report'] = 1;
					}
					else if($login_user->user_details->degination == 'CENTER HEAD' || $login_user->user_details->degination == 'ASSISTANT CENTER HEAD'){
						$app_config['is_report'] = 1;
					}
					else if($user_id == 1647 || $user_id == 5453 || $user_id == 6328 || $user_id == 1237 ){
						$app_config['is_report'] = 1;
					}

					if($role_id==28 || $role_id==3){
		              $app_config['is_report'] = 1;
					}
					
					
					$app_config['is_emp_complaint'] = 1;
				}
				else{
					$data = array();
					return $this->returnResponse(200, true, "Inactive User",$data); die;
				}
				
				
				
				$data['app_config'] = $app_config;
				
				$getVersion = array();
				$getVersion['version'] = "29";	
				$getVersion['url'] = "https://play.google.com/store/apps/details?id=com.utkarsh.employee";	
				$data['app_version'] = $getVersion;

				$data['home_banner']=DB::table("api_notifications")->selectraw('title,description,image')->where('type','banner')->where('is_deleted','0')->orderby('id','desc')->first();
			
                //echo '<pre>'; print_r(count($branches));die;
                return $this->returnResponse(200, true, "Master Data", $data);

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}

	public function branches(Request $request){
		$city=$request->city??''; 
		$data=Cache::remember('master_data_branches_1226'.$city,1,function()use($city){
			$branches= Branch::where('status', '=', '1')
			->selectraw('id,name,address,latitude,longitude,gallery,cover_image')
			->where('branch_location',$city)
			->where('show_in_web',1)
			->where('is_deleted','0')
			->whereNotNull('gallery')
			->whereNotNull('cover_image')
			->get();
			return $data['branches'] = $branches;
		});

		return response(['status'=>true,"data"=>$data],200);
	}
	
 }
