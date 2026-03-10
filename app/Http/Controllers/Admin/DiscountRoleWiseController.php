<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Leave;
use App\LeaveDetail;
use App\NewTask;
use App\User;
use App\Holiday;
use Input;
use Excel;
use App\Exports\LeaveExport;
use Auth;
use DB;
use DateTime;
use App\Attendance;
use App\AttendanceNew;
use App\AttendanceLock;

 
class DiscountRoleWiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
	public function index(){
		
		$discount_list = DB::table('discount_role_category_wise as drcw')
			->select('drcw.*','roles.name as role_name')
            ->join('roles', 'roles.id', '=', 'drcw.role_id')
			->orderBy('drcw.id', 'DESC')
            ->get();
        return view('admin.discountApprovel.discount_role_wise', compact('discount_list'));
	
	}
	
	
	public function discount_category_role_add(Request $request){
		
		$role_list = DB::table('roles')->where('status', 1)->get();
        return view('admin.discountApprovel.discount_category_role_add', compact('role_list'));
	
	}
	
	
	public function store_discount_category_role(Request $request){
		$logged_id       = Auth::user()->id;
		
		$paternity_leave = DB::table('discount_role_category_wise')->select('id','role_id')->where('role_id',$request->role_id)->where('category',$request->category)->first();
		if(empty($paternity_leave)){
			$data1 = array(		
				'created_by' => $logged_id,
				'role_id' =>$request->role_id,
				'category'=>$request->category,
				'online'=>$request->online,
				'offline'=>$request->offline
			);
			// dd($data1);
			DB::table('discount_role_category_wise')->insert($data1);
			return redirect("admin/discount-role-wise")->with('success', 'Added successfully!');
			  
		}
		else{
			return redirect("admin/discount-role-wise")->with('error', 'Woops, Already added discount for this role and category!');
		}
    }
	
	
}
