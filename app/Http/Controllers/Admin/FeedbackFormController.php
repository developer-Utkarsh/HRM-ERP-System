<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FeedbackForm;
use App\Department;
use Input;
use DB;

class FeedbackFormController extends Controller
{
  
    public function index()
    {
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page   = Input::get('page');
		
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		
		$form = FeedbackForm::where('is_deleted','0')->orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.feedback_form.index', compact('form','params','pageNumber'));
    }
	
	 public function form_add()
    {
		$url = url('/admin/feedback-form-store');
		$heading = "Add";
		
        return view('admin.feedback_form.add',compact('url','heading'));
    }
	
	public function feedback_form_store(Request $request){
		$form_name			=	$request->form_name;
		$form_description	=	$request->form_description;
		$department			=	$request->department;
		$start_time			=	$request->start_time;
		$end_time			=	$request->end_time;
		$question_ids		=	$request->question_ids;
		
		

		$q_s = FeedbackForm::where('form_name', 'like', '%' . $form_name . '%')->count();
		if($q_s==0){
			$data = array(
				"form_name"			=> 	$form_name,
				"form_description"	=> 	$form_description,		
				"department"		=>	$department,	
				"start_time"		=>	$start_time,	
				"end_time"			=>	$end_time,	
				"question_ids"		=>	rtrim(implode(',', $question_ids), ','),	
			);
			
			DB::table('feedbackforms')->insert($data);
			
			return back()->with('success', "Feedback Question Added");			
		}else{
			return back()->with('error', "Form Already Added");
		}
	} 
	
	
	public function form_edit(Request $request, $id){
		$url = url('/admin/feedback-form-update').'/'.$id;
		$heading = "Edit";
		
		$form  = FeedbackForm::where('form_id',$id)->first();
		
		return view('admin.feedback_form.add', compact('form','url','heading'));
	}	
	
	public function form_update(Request $request, $id){
		$form_name			=	$request->form_name;
		$form_description	=	$request->form_description;
		$department			=	$request->department;
		$start_time			=	$request->start_time;
		$end_time			=	$request->end_time;
		$question_ids		=	$request->question_ids;


		if(!empty($id)){
			$data = array(
				"form_name"			=> 	$form_name,
				"form_description"	=> 	$form_description,		
				"department"		=>	$department,	
				"start_time"		=>	$start_time,	
				"end_time"			=>	$end_time,	
				"question_ids"		=>	rtrim(implode(',', $question_ids), ','),	
			);
			
			DB::table('feedbackforms')->where('form_id',$id)->update($data);
			
			return back()->with('success', "Feedback Question Updated");			
		}else{
			return back()->with('error', "form Already Added");
		}
	
	}
	
	public function form_destroy(Request $request, $id){
		if(!empty($id)){
			$data = array(
				"is_deleted"	=> 	'1',
			);
			
			DB::table('feedbackforms')->where('form_id',$id)->update($data);
			return back()->with('success', "Form deleted successfully!!");
		}else{
			return back()->with('error', "Form ID Missing");
		}
	}
	
	public function employee_complaint_view(){
		$query = DB::table('emp_complaint')->select('emp_complaint.*','users.name as uname')->leftjoin('users','users.id','emp_complaint.user_id')->get();
        return view('admin.complaint.complaint', compact('query'));
	}

}
