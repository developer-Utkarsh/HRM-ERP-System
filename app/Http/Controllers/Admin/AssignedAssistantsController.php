<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use Input;
use Excel;
use DB;
use App\Exports\FreeAssistantExport;
use Auth;
use App\Exports\AssignedAssistantExport;

class AssignedAssistantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assigned_assistants()
    {
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		
		$userLocationBranches = array();
		$branch_location = Auth::user()->user_branches[0]->branch['branch_location'];
		$get_location_branches = DB::table('branches')->select('id')->where(['branch_location'=>$branch_location])->get();
		foreach($get_location_branches as $userLocationBranchesVal){
			$userLocationBranches[] = $userLocationBranchesVal->id;
		}
		// echo "<pre>"; print_r($userLocationBranches); die;
		$login_brances = array();
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){	
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
		}
		else if(Auth::user()->user_details->degination == 'STUDIO INCHARGE'){
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
		}
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
		$studio_id        = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		if(empty($fdate)){
			$fdate = date('Y-m-d');
		}

		
		
		$get_data = DB::table('timetables')
				->leftJoin('studios','timetables.studio_id','=','studios.id')
				->leftJoin('branches','studios.branch_id','=','branches.id')
				->leftJoin('users','timetables.assistant_id','=','users.id')
				->select('users.name as assistant_name','users.id as assistant_id','branches.name as branch_name','branches.id as branch_id','studios.name as studio_name','studios.id as studio_id')
				->where(['timetables.cdate'=>"$fdate"]);
		if(!empty($login_brances)){
			$get_data->whereIn('branches.id', $login_brances);
		}
		if(!empty($branch_id)){
			$get_data->where('branches.id', $branch_id);
		}
		if(!empty($assistant_id)){
			$get_data->where('users.id', $assistant_id);
		}
		if(!empty($studio_id)){
			$get_data->where('studios.id', $studio_id);
		}
		
		$get_data = $get_data->groupBy('timetables.studio_id')->get();
		
		$get_assistant = DB::table('users')
							->select('users.*')
							->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
							->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')
							->where(['users.status'=>1,'users.is_deleted'=>'0','users.role_id'=>3]);
		if(!empty($userLocationBranches)){
			$get_assistant->whereIn('branches.id', $userLocationBranches);
		}					
		$get_assistant = $get_assistant->get();
		
        return view('admin.assigned_assistants.index', compact('get_data','get_assistant'));
    }
	
	public function assigned_assistant_update(Request $request)
    {
		$studio_id = $request->studio_id;
		$assistant_id = $request->assistant_id;
		$date = $request->fdate;
		if(empty($date)){
			$date = date('Y-m-d');
		}
		if(!empty($studio_id)){
			$check_studios = DB::table('timetables')
					->where('studio_id',$studio_id)
					->whereRaw("DATE(cdate) = '$date'")
					->get();
			if(!empty($check_studios)){
				$studio_update=DB::table('studios')->where('id', $studio_id)->update([ 'assistant_id' => $assistant_id]);

				$update = DB::table('timetables')->where('studio_id', $studio_id)->whereRaw("DATE(cdate) >= '$date'")->update([ 'assistant_id' => $assistant_id]);
				if($update || $studio_update) {				
					return response(['status' => true, 'message' => 'Assign successfully.'], 200);
				} else {
					return response(['status' => false, 'message' => 'No any change.'], 200);
				}
			}
		}
		else{
			return response(['status' => false, 'message' => 'Studio required.'], 200);
		}
		
    }
	
	public function download_excel()
    {   
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		// echo $logged_role_id; die;
		$login_brances = array();
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){	
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
			// echo "<pre>"; print_r($login_brances); die;
		}
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
		$studio_id        = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		if(empty($fdate)){
			$fdate = date('Y-m-d');
		}

		
		
		$get_data = DB::table('timetables')
				->leftJoin('studios','timetables.studio_id','=','studios.id')
				->leftJoin('branches','studios.branch_id','=','branches.id')
				->leftJoin('users','timetables.assistant_id','=','users.id')
				->select('users.name as assistant_name','users.id as assistant_id','branches.name as branch_name','branches.id as branch_id','studios.name as studio_name','studios.id as studio_id')
				->where(['timetables.cdate'=>"$fdate"]);
		if(!empty($login_brances)){
			$get_data->whereIn('branches.id', $login_brances);
		}
		if(!empty($branch_id)){
			$get_data->where('branches.id', $branch_id);
		}
		if(!empty($assistant_id)){
			$get_data->where('users.id', $assistant_id);
		}
		if(!empty($studio_id)){
			$get_data->where('studios.id', $studio_id);
		}
		
		$get_data = $get_data->groupBy('timetables.studio_id')->get();
        if(count($get_data) > 0){  
            return Excel::download(new AssignedAssistantExport($get_data), 'AssignedAssistantData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
	
	public function assigned_incharge()
    {
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE' || Auth::user()->user_details->degination == 'PRODUCTION MANAGER' || Auth::user()->id == 1069 || Auth::user()->id == 1207 || Auth::user()->id == 7431){	
			 
		}
		else{
			die('Not Access You.');
		}
		
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		
		$branch_id    = Input::get('branch_id');
        $search = Input::get('search'); 
		if(empty($fdate)){
			$fdate = date('Y-m-d');
		}
		
		$userLocationBranches = array();
		$userLocationBranchesName = array();
		$branch_location = Auth::user()->user_branches[0]->branch['branch_location'];
		
		$get_location_branches = DB::table('branches')->select('id','name')->where('status',1)->where('is_deleted','0');
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){
			$get_location_branches->where(['branch_location'=>$branch_location]);
		}
		
		$get_location_branches = $get_location_branches->get();
		
		foreach($get_location_branches as $userLocationBranchesVal){
			$userLocationBranches[] = $userLocationBranchesVal->id;
			$userLocationBranchesName[$userLocationBranchesVal->id] = $userLocationBranchesVal->name;
		}
		
		if(!empty($branch_id) && !in_array($branch_id,$userLocationBranches)){
			$userLocationBranches = array();
			$userLocationBranches[] = 0;
		}
		// echo "<pre>"; print_r($userLocationBranchesName); die;
		$login_brances = array();
		
		
		$degination_ids = array(81,117); // 81(STUDIO ASSISTANT),83(PRODUCTION INCHARGE),117(STUDIO INCHARGE),217(PRODUCTION MANAGER)
		$degination_name = array();
		$designations = DB::table('designations')->select('id','name')->whereIn('id',$degination_ids)->get();
		foreach($designations as $dval){
			$degination_name[$dval->id] = $dval->name;
		}
		// echo "<pre>"; print_r($degination_name); die;
		$production_employee = DB::table('users')
							->select('users.*','userdetails.degination','userbranches.branch_id')
							->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
							->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
							->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')
							->where(['users.status'=>1,'users.is_deleted'=>'0'])
							->whereIn('userdetails.degination', $degination_name)			
							->whereIn('branches.id', $userLocationBranches);
		if(!empty($branch_id)){
			$production_employee->where('branches.id', $branch_id);
		}
		
		if(!empty($search)){
			$production_employee->whereRaw("(users.name LIKE '%$search%' OR users.email LIKE '%$search%' OR users.mobile LIKE '%$search%' OR users.email LIKE '%$search%' OR users.register_id LIKE '%$search%' )");
		}
		$production_employee = $production_employee->get();
		// echo "<pre>"; print_r($production_employee); die;
		
        return view('admin.assigned_assistants.assigned_incharge', compact('production_employee','userLocationBranchesName','degination_name'));
    }
	
	public function assigned_incharge_update(Request $request)
    {
		$user_id = $request->user_id;
		$branch_id = $request->branch_id;
		$designation_name = $request->designation_name;
		if(!empty($user_id) && !empty($branch_id) && !empty($designation_name)){
			$check_studios = DB::table('users')
					->select('users.id','userbranches.id as userbranche_id')
					->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
					->where('users.id',$user_id)
					->first();
			// echo "<pre>"; print_r($check_studios); die;
			if(!empty($check_studios)){
				$userbranche_id = $check_studios->userbranche_id;
				$update = DB::table('userbranches')->where('id', $userbranche_id)->update([ 'branch_id' => $branch_id]);
				$update1 = DB::table('userdetails')->where('user_id', $user_id)->update([ 'degination' => $designation_name]);
				$status = false;
				$msg = '';
				if($update) {
					$status = true;
					$msg .= "Branch change successfully";
				}
				else{
					$msg .= "No any change in Branch";
				}
				if($update1) {
					$status = true;
					$msg .= " and Designation change successfully.";
				}
				else{
					$msg .= " and no any change in Designation.";
				}
				
				if($status) {
					return response(['status' => true, 'message' => $msg], 200);
				}
				else {
					return response(['status' => false, 'message' => 'No any change.'], 200);
				}
				
				
			}
		}
		else{
			return response(['status' => false, 'message' => 'Something went wrong.'], 200);
		}
		
    }
	
}
