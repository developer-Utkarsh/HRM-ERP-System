<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FeedbackQuestion;
use Input;
use DB;

class FeedbackQuestionController extends Controller
{
  
    public function index()
    {
		$question  = FeedbackQuestion::where('is_deleted','0')->orderby('created_at', 'DESC')->get();
        return view('admin.feedback_question.index', compact('question'));
    }

	 public function question_add()
    {
		$url = url('/admin/feedback-question-store');
		$heading = "Add";
        return view('admin.feedback_question.add',compact('url','heading'));
    }
	
	public function feedback_store(Request $request){
		$question		=	$request->question;
		$options		=	$request->options;
		$question_type	=	$request->question_type;


		$q_s = FeedbackQuestion::where('question', 'like', '%' . $question . '%')->count();
		if($q_s==0){
			$data = array(
				"question"	=> 	$question,
				"options"	=> 	$options,		
				"qtype"		=>	$question_type,	
			);
			
			DB::table('feedbackquestion')->insert($data);
			
			return back()->with('success', "Feedback Question Added");			
		}else{
			return back()->with('error', "Question Already Added");
		}
	} 
	
	public function question_edit(Request $request, $id){
		$url = url('/admin/feedback-question-update').'/'.$id;
		$heading = "Edit";
		
		$question  = FeedbackQuestion::where('qid',$id)->first();
		
		return view('admin.feedback_question.add', compact('question','url','heading'));
	}	
	
	public function question_update(Request $request, $id){
		$question		=	$request->question;
		$options		=	$request->options;
		$question_type	=	$request->question_type;


		if(!empty($id)){
			$data = array(
				"question"	=> 	$question,
				"options"	=> 	$options,		
				"qtype"		=>	$question_type,	
			);
			
			DB::table('feedbackquestion')->where('qid',$id)->update($data);
			
			return back()->with('success', "Feedback Question Updated");			
		}else{
			return back()->with('error', "Question Already Added");
		}
	
	}
	
	public function question_destroy(Request $request, $id){
		if(!empty($id)){
			$data = array(
				"is_deleted"	=> 	'1',
			);
			
			DB::table('feedbackquestion')->where('qid',$id)->update($data);
			return back()->with('success', "Question deleted successfully!!");
		}else{
			return back()->with('error', "Question ID Missing");
		}
	}
}
