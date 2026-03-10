<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Auth;
use App\User;
use DB;
use App\Exports\FacultyHoursExport;
use App\Exports\MSGCeoExport;
use Excel;
use App\SupportCategory;


class AppenquiryController extends Controller
{

	public function employee_enquiry(Request $request){
		$user_id     = Input::get('user_id');
		$category_list = SupportCategory::where("is_deleted","0")->where("status","Active")->get();
		return view('admin.app_enquiry.employee-enquiry', compact('user_id','category_list'));
	}
	
	public function employee_enquiry_store(Request $request){
		$category_id			=	$request->category_id;
		$priority				=	$request->priority;
		$description			=	$request->description;
		$user_id				=	$request->user_id;
		
		
		if(!empty($user_id)){
			$login_user   = User::where([['id', '=', $user_id]])->first();
			if($login_user){
				if($category_id!="" && $priority!="" && $description!=""){ 
					$enquiryArr = array(
								'query_id'    => $user_id,
								'mobile_no'   => $login_user->mobile,
								'course_type' => 'Online',
								'course_name' => 'Utkarsh-Staff',
								'category_id' => $request->category_id,
								'priority'    => $request->priority,
								'status'      => 'pending',
								'date'        => date("Y-m-d"),
								"created_at"  => date("Y-m-d H:i:s"),
								"updated_at"  => date("Y-m-d H:i:s")
							);       

					$save_enquiry = DB::table("enquiry")->insertGetId($enquiryArr);
					if(!empty($save_enquiry)){
						DB::table("enquiry_description")->insertGetId(["enquiry_id" => $save_enquiry, "description" => $description, "user_id" => $user_id, "created_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")]);
						
						return back()->with('success', "Your query has been submitted. We will reply as soon possible.");
					}
					else{
						return back()->with('error', "Something went wrong. Please try again.");
					}
				}else{
					return back()->with('error', "Required Filed Missing!!");	
				}
			}
			else{
				return back()->with('error', "Something went wrong. Please try again.");
			}
		}else{
			return back()->with('error', "User ID Missing!!");	
		}
	}
	
	public function enquiry_history(Request $request, $user_id){
		$query = DB::table('enquiry')->where('query_id',$user_id)->orderby('id','DESC')->get();
		return view('admin.app_enquiry.employee-enquiry-history', compact('user_id','query'));
	}
	
	
}
