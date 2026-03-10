<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Input;
use DB;
use App\Appraisal;
use App\AppraisalQuestions;
use App\NewTask;
use Auth;
use App\ApiNotification;

class AppraisalController extends Controller
{
    
    public function index()
    { 
        $question = Input::get('question');
        $status   = Input::get('status');
        $whereCond = ' 1=1 ';

        if(!empty($question)){
            $whereCond .= " AND appraisal_questions.question LIKE '%$question%'";
        }

        if(!empty($status)){
            $whereCond .= " AND appraisal_questions.status = '$status'";
        }

        $appraisal_question_result = AppraisalQuestions::whereRaw($whereCond)->where('status', '!=', 'Deleted')->orderBy('id','desc')->get();

        //dd($appointment_result);
        return view('admin.appraisal.index', compact('appraisal_question_result'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		return view('admin.appraisal.add');
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $logged_id  = Auth::user()->id;	
		$validatedData = $request->validate([
            'question' => 'required'
        ]);

        $inputs = $request->only('question'); 
        $inputs['created_by']  =  $logged_id;     

        $appraisal_question = AppraisalQuestions::create($inputs);    

        if ($appraisal_question->save()) {
            return redirect()->route('admin.appraisal.index')->with('success', 'Appraisal Added Successfully');
        } else {
            return redirect()->route('admin.appraisal.index')->with('error', 'Something Went Wrong !');
        }
        //
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
    public function edit($id)
    {
        $appraisal_question = AppraisalQuestions::find($id);
        return view('admin.appraisal.edit', compact('appraisal_question'));
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
        $logged_id  = Auth::user()->id;

        $validatedData = $request->validate([
            'question' => 'required'
        ]);

        $appraisal_question = AppraisalQuestions::where('id', $id)->first();

        $inputs = $request->only('question');  
        $inputs['created_by']  =  $logged_id;      

        if ($appraisal_question->update($inputs)) {
			return redirect()->back()->with('success', 'Appraisal Updated Successfully');
        } else {
			return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appraisal_question = AppraisalQuestions::find($id);
		$inputs = array('status' => 'Deleted');
		
        if ($appraisal_question->update($inputs)) {
            return redirect()->back()->with('success', 'Appraisal Deleted Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }

    public function togglePublish($id) {
        $appraisal_question = AppraisalQuestions::find($id);
        if (is_null($appraisal_question)) {
            return redirect()->route('admin.appraisal.index')->with('error', 'Appraisal not found');
        }
		
		if($appraisal_question->status == 'Active'){
			$sts = 'Deactive';
		}
		else{
			$sts = 'Active';
		}
		
		$appraisal_question->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.appraisal.index')->with('success', 'Status Updated Successfully.');
    }

    public function appraisalUserList(){
        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
        $year_wise_month = Input::get('year_wise_month');
        $whereCond = ' 1=1 ';

        if(!empty($year_wise_month)){
            $month_year_to_days = explode('-',$year_wise_month);
            $y = $month_year_to_days[0];
            $m = $month_year_to_days[1];
        }
        else{
            $y = date('Y');
            $m = date('m');
        }

        $whereCond .= " AND ((MONTH(appraisal.from_date) = '$m' AND YEAR(appraisal.from_date) = '$y') OR (MONTH(appraisal.from_date) = '$m' AND YEAR(appraisal.from_date) = '$y'))";

        if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);


        $appraisal_user_result = Appraisal::select('appraisal.name as appraisal_name','users.name as user_name','appraisal_user.*')->leftJoin('appraisal_user','appraisal_user.appraisal_id','=','appraisal.id')->leftJoin('users','appraisal_user.user_id','=','users.id')->whereRaw($whereCond)->where('appraisal.status', '!=', 'Deleted')->whereIn('appraisal_user.user_id', $employeeArray)->orderBy('appraisal_user.id','desc')->get();

        //echo '<pre>'; print_r($appraisal_user_result);die;
        return view('admin.appraisal.appraisal-user-list', compact('appraisal_user_result'));
    }

    public function appraisalUserQuestionList($id,$emp_id){
        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
        if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);

        $appraisal_user_question_result = Appraisal::select('appraisal.name as appraisal_name','e.name as user_name','h.name as head_name','appraisal_user.*')->leftJoin('appraisal_user','appraisal_user.appraisal_id','=','appraisal.id')->leftJoin('users as e','appraisal_user.user_id','=','e.id')->leftJoin('users as h','appraisal_user.head_id','=','h.id')->where('appraisal_user.id', $id)->where('appraisal_user.user_id', $emp_id)->whereIn('appraisal_user.user_id', $employeeArray)->where('appraisal.status', '!=', 'Deleted')->orderBy('appraisal_user.id','desc')->first();


        $appraisal_question_result = Appraisal::select('appraisal_questions.question','appraisal_user_que_ans.*')->leftJoin('appraisal_user','appraisal_user.appraisal_id','=','appraisal.id')->leftJoin('appraisal_user_que_ans','appraisal_user_que_ans.appraisal_user_id','=','appraisal_user.id')->leftJoin('appraisal_questions','appraisal_questions.id','=','appraisal_user_que_ans.appraisal_que_id')->where('appraisal_user_que_ans.appraisal_user_id', $id)->whereIn('appraisal_user_que_ans.user_id', $employeeArray)->where('appraisal.status', '!=', 'Deleted')->orderBy('appraisal_user.id','desc')->get();

        return view('admin.appraisal.appraisal-user-question-list', compact('appraisal_user_question_result','appraisal_question_result'));
    }

    public function appraisalUserQuestionResponse($id,$emp_id){
        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
        if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);

        $appraisal_user_question_response_result = Appraisal::select('appraisal_user.*')->leftJoin('appraisal_user','appraisal_user.appraisal_id','=','appraisal.id')->where('appraisal_user.id', $id)->where('appraisal_user.user_id', $emp_id)->whereIn('appraisal_user.user_id', $employeeArray)->where('appraisal.status', '!=', 'Deleted')->orderBy('appraisal_user.id','desc')->first();


        $appraisal_question_response_result = Appraisal::select('appraisal_questions.question','appraisal_user_que_ans.*')->leftJoin('appraisal_user','appraisal_user.appraisal_id','=','appraisal.id')->leftJoin('appraisal_user_que_ans','appraisal_user_que_ans.appraisal_user_id','=','appraisal_user.id')->leftJoin('appraisal_questions','appraisal_questions.id','=','appraisal_user_que_ans.appraisal_que_id')->where('appraisal_user_que_ans.appraisal_user_id', $id)->whereIn('appraisal_user_que_ans.user_id', $employeeArray)->where('appraisal.status', '!=', 'Deleted')->orderBy('appraisal_user.id','desc')->get();

        //echo '<pre>'; print_r($appraisal_user_question_response_result);die;
        return view('admin.appraisal.appraisal-user-question-response', compact('appraisal_question_response_result','appraisal_user_question_response_result'));
    }

    public function storeAppraisalUserQuestionResponse(Request $request){ 
        $logged_id       = Auth::user()->id;
        if(count($request->id) > 0){
            foreach($request->id as $key=>$id_val){
                DB::table('appraisal_user_que_ans')->where('id', $id_val)->update([
                    'head_marks' => $request->head_marks[$key],
                    'head_remark' => $request->head_remark[$key]
                ]);
            }

            DB::table('appraisal_user')->where('id', $request->appraisal_id)->update([
                'head_overall_remark' => $request->head_overall_remark,
                'head_date' => date('Y-m-d'),
                'head_id'  => $logged_id,
                'is_submitted' => '1'
            ]);

            $appraisal_user_res = DB::table('appraisal_user')->where('id', $request->appraisal_id)->first(); 
            
            if(!empty($appraisal_user_res)){
                $sender_detail = DB::table('users')->where('id',$appraisal_user_res->head_id)->first();
                $recevier_detail = DB::table('users')->where('id',$appraisal_user_res->user_id)->first();

                if(!empty($sender_detail) && !empty($recevier_detail)){
                    $load['title']       = 'Appraisal';
					$load['description'] = 'Dear '.$recevier_detail->name.', '.$sender_detail->name.' response on your appraisal';
                    $load['sender_id']   = $sender_detail->id;
                    $load['receiver_id'] = '["'.$recevier_detail->id.'"]';
                    $load['type']        = 'General';
                    $load['date']        = date('Y-m-d H:i:s');
                    ApiNotification::create($load);

                    if(!empty($recevier_detail->gsm_token)){
                        $token[]=$recevier_detail->gsm_token;
                        $this->android_notification($token, $load);
                    }

                }
            }


            return back()->with('success', 'Successfully Submit.');
        }
        else{
            return back()->with('error', 'Something Went Wrong !');
        }
    }
	
}
