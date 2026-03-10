<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;
use App\AttendanceLock;
use App\User;
use Input;
use DB;
use Auth;

class FacultySmeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$request_id = $request->request_id;
		$req_status = $request->req_status;			
		$logged_id 	= Auth::user()->id;		
		
		$logged_id = is_array($logged_id) ? $logged_id : [$logged_id];

		$faculty_id = DB::table('faculty_subjects')
			->select(DB::raw("GROUP_CONCAT(DISTINCT user_id SEPARATOR ',') as user_id"))
			->where(function($query) use ($logged_id) {
				foreach ($logged_id as $id) {
					$query->orWhere('sme_id', 'LIKE', "%{$id}%");
				}
			})
			->value('user_id');

		
		$faculty_id = trim(preg_replace('/\s+/', '', $faculty_id));
		$faculty_id_array = $faculty_id ? explode(',', $faculty_id) : [];
		
		$single_logged_id = $logged_id[0]; // For subquery use

		$record = DB::table('faculty_sme_request')
			->select(
				'faculty_sme_request.*',
				'users.name',
				DB::raw("GROUP_CONCAT(faculty_subjects.subject_id SEPARATOR ', ') as subject_ids"),
				'faculty_sme_upload.file',
				'faculty_sme_assistant.request_id as assistant_used',
				DB::raw("(SELECT COUNT(*) FROM faculty_sme_chat 
						  WHERE faculty_sme_chat.request_id = faculty_sme_request.request_id 
							AND faculty_sme_chat.status = 2 
							AND faculty_sme_chat.user_id != $single_logged_id) as chat_status_2_count")
			)
			->leftJoin('faculty_sme_upload', 'faculty_sme_upload.request_id', '=', 'faculty_sme_request.id')
			->leftJoin('users', 'users.id', '=', 'faculty_sme_request.user_id')
			->leftJoin('faculty_subjects', 'faculty_subjects.user_id', '=', 'users.id')
			// ->leftJoin('faculty_sme_assistant', 'faculty_sme_assistant.request_id', '=', 'faculty_sme_upload.request_id')
			->leftJoin('faculty_sme_assistant', 'faculty_sme_assistant.request_id', '=', 'faculty_sme_request.request_id')
			->whereIn('faculty_sme_request.user_id', $faculty_id_array)
			->groupBy('faculty_sme_request.request_id', 'users.name')
			->orderBy('faculty_sme_request.id', 'desc');

		if (!empty($request_id)) {
			$record->where('faculty_sme_request.request_id', $request_id);
		}

		if (!empty($req_status)) {
			$record->where('faculty_sme_request.status', $req_status);
		}

		$record = $record->get();

	
        return view('admin.faculty_sme.index',compact('record'));
    }

    public function add()
    {
        return view('admin.attendance-lock.add');
    }

    public function show($id)
    {
        //
    }

    public function edit($id){    
		//
	}

    public function update(Request $request, $id)
    {
       //
    }
	
	public function faculty_sme_chat(){
		$request_id = Input::get('request_id');
		$user_id    = Auth::user()->id;
		
		$record = DB::table('faculty_sme_chat')
					->select('faculty_sme_chat.*','users.name')
					->leftjoin('users','users.id','faculty_sme_chat.user_id')
					->where('request_id',$request_id)
					->get();
					
		DB::table('faculty_sme_chat')->where('request_id',$request_id)->where('user_id','!=',$user_id)->update(['status'=> 1]);
					
		return view('admin.faculty_sme.chat',compact('record','request_id'));
	}
	
	public function faculty_sme_reuse(Request $request){
		$search	=	Input::get('search');
		$request_id	=	Input::get('request_id');
		
		if($search!=''){			
			$category			=	$request->category;		
			$subject			=	$request->subject;
			$chapter			=	$request->chapter;
			$no_of_question		=	$request->no_of_question;
			$mode				=	$request->mode;
			$level				=	$request->level;
			$requirement_for	=	$request->requirement_for;
			$language			=	$request->language;
			
			
			$record = DB::table('faculty_sme_upload')->orderby('id','desc');
			
			if(!empty($category)){
				$record->where('category',$category);
			}
			
			if (!empty($request->exam)) {
				$newexam = explode('$#',$request->exam);
				$record->where('exam', 'like', '%' . $newexam[0] . '%');
			}

			if(!empty($subject)){
				$newexam = explode('$#',$request->subject);
				$record->where('subject', 'like', '%' . $newexam[0] . '%');
			}
			
			if(!empty($chapter)){
				$newexam = explode('$#',$request->chapter);
				$record->where('topic', 'like', '%' . $newexam[0] . '%');
			}
			
			if(!empty($no_of_question)){
				$record->where('mode',$no_of_question);
			}
					
			if(!empty($mode)){
				$record->where('mode',$mode);
			}
			
			if(!empty($level)){
				$record->whereIn('level',$level);
			}
			
			if(!empty($requirement_for)){
				$record->where('requirement_for',$requirement_for);
			}
			
			if(!empty($language)){
				$record->where('language',$language);
			}
			$record = $record->get();
		}else{
			$record = array();
		}
		
		$all_courses	=	$this->getMainCategory();
		$all_courses  	=	$all_courses->data;
					
		return view('admin.faculty_sme.reuse',compact('all_courses','record','request_id'));
	}
	
	public function faculty_sme_chat_submit(Request $request){
		$user_id 	= Auth::user()->id;
		$request_id = $request->request_id;
		$chat_msg 	= $request->chat_msg;
				
		if($user_id!="" && $request_id!="" && $chat_msg!=""){
			$data = array(
				"user_id"		=>	$user_id,
				"request_id"	=>	$request_id,
				"message"		=>	$chat_msg,
				"created_at"	=>	date('Y-m-d\TH:i'),
			);
			
			DB::table('faculty_sme_chat')->insert($data);
			
			//Push Notification			
			$getUserID = DB::table('faculty_sme_request')->where('request_id',$request_id)->first();			
			$user = User::where('id',$getUserID->user_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();
			
			$load = array();
			$load['title'] 		 = 'Reply from the SME on your request ID :'.$request_id;
			$load['description'] = 'Reply from the SME on your request ID  :'.$request_id;
			$load['body'] 		 = 'Reply from the SME on your request ID  :'.$request_id;
			$load['image'] 		 = asset('laravel/public/images/test-image.png');
			$load['date'] 		 = date('d-m-Y');
			$load['status'] 	 = NULL;
			$load['type'] 		 = 'general';
	 
			$token = [];
			if(count($user) > 0){
				foreach ($user as $key => $value) {
					if(!empty($value->gsm_token)){
						$token[] = $value->gsm_token;
					}
				}
			}
			$this->android_notification($token,$load); 
			
			return back();
		}else{
			return back();
		}
	}
	
	public function faculty_sme_uploadfile(){
		$request_id = Input::get('request_id');
		$viewonly = Input::get('viewonly');
		$resue_by = Input::get('resue_by');
		$logged_id = Auth::user()->id;			
		
		
		$all_courses	=	$this->getMainCategory();
		$all_courses  	=	$all_courses->data;
		
		
		$get_details = DB::table('faculty_sme_upload')->where('request_id',$request_id)->first();
	
		return view('admin.faculty_sme.uploadfile',compact('all_courses','request_id','get_details','viewonly','resue_by'));
	}

	public function faculty_sme_uploadfile_submit(Request $request){
		$logged_id			=	Auth::user()->id;
		$reuqest_id			=	$request->reuqest_id;
		$category			=	$request->category;
		$resue_by			=	$request->resue_by;
		
		//Exam
		$examArray = [];
		foreach ($request->exam as $data) {
			list($name, $id) = explode("$#", $data);
			$examArray[] = [
				"name" => $name,
				"id"   => (int)$id
			];
		}
		
		//Subject
		$subjectArray = [];
		foreach ($request->subject as $data) {
			list($name, $id) = explode("$#", $data);
			$subjectArray[] = [
				"name" => $name,
				"id"   => (int)$id
			];
		}
		
		//Chapter
		$chapterArray = [];
		foreach ($request->chapter as $data) {
			list($name, $id) = explode("$#", $data);
			$chapterArray[] = [
				"name" => $name,
				"id"   => (int)$id
			];
		}
		
		$no_of_question		=	$request->no_of_question;
		$mode				=	$request->mode;
		$test_id			=	$request->test_id;
		$level				=	implode(',',$request->level);
		$requirement_for	=	$request->requirement_for;
		$language			=	$request->language;
		
		$record = DB::table('faculty_sme_upload')->where('request_id',$reuqest_id)->first();	
		
		
		if(!empty($resue_by)){
			if(!empty($reuqest_id) && !empty($resue_by)){						
				$data = array(
					"user_id"			=>	$logged_id,
					"request_id"		=>	$resue_by,
					"category"			=>	$category,
					"exam"				=>	json_encode($examArray, JSON_PRETTY_PRINT),
					"subject"			=>	json_encode($subjectArray, JSON_PRETTY_PRINT),
					"topic"				=>	json_encode($chapterArray, JSON_PRETTY_PRINT),
					"no_question"		=>	$no_of_question,
					"mode"				=>	$mode,
					"level"				=>	$level,
					"requirement_for"	=>	$requirement_for,
					"language"			=>	$language,
					"created_at"		=>	date('Y-m-d\TH:i'),
					"parent_request"	=>	$reuqest_id,
				);		

				if(!empty($request->test_id)){
					$data['test_id'] = $request->test_id;
				}
				
				if($files=$request->file('pdf_file')){	
					if(isset($files)){
						$iname = $files->getClientOriginalName();
						$iname = uniqid().'-'.$iname;
						$files->move('laravel/public/faculty_sme',$iname);
						$data['file']= $iname;					
					}
				}else{
					$data['file']= $record->file;	
				}
				
				DB::table('faculty_sme_upload')->insert($data);
				
				return redirect()->route('admin.faculty-sme.index')->with('success', "Form Submitted Successfully");
			}else{
				return redirect()->route('admin.faculty-sme.index')->with('error', "Required Filed Missing!!");
			}
		}else{
			if($logged_id!=""){
				$data = array(
					"user_id"		=>	$logged_id,
					"request_id"	=>	$reuqest_id,
					"category"		=>	$category,
					"exam"			=>	json_encode($examArray, JSON_PRETTY_PRINT),
					"subject"		=>	json_encode($subjectArray, JSON_PRETTY_PRINT),
					"topic"			=>	json_encode($chapterArray, JSON_PRETTY_PRINT),
					"no_question"	=>	$no_of_question,
					"mode"			=>	$mode,
					"level"			=>	$level,
					"requirement_for"	=>	$requirement_for,
					"language"		=>	$language,
					"created_at"	=>	date('Y-m-d h:i:s'),
				);
				
				if(!empty($request->test_id)){
					$data['test_id'] = $request->test_id;
				}
				
				if($files=$request->file('pdf_file')){	
					if(isset($files)){
						$iname = $files->getClientOriginalName();
						$iname = uniqid().'-'.$iname;
						$files->move('laravel/public/faculty_sme',$iname);
						$data['file']= $iname;					
					}
				}else{
					$data['file']= $record->file;	
				}
						
				if(empty($record->id)){
					DB::table('faculty_sme_upload')->insert($data);
					return redirect()->route('admin.faculty-sme.index')->with('success', "File Uploaded!!");
				}else{
					DB::table('faculty_sme_upload')->where('id', $record->id)->update($data);
					return redirect()->route('admin.faculty-sme.index')->with('success', "File Updated!!");
				}
			}else{
				return back()->with('error', "Required filed missing!!");
			}
		}
	}
	
	public function faculty_sme_request_pick(){
		$request_id = Input::get('request_id');
		
		if(!empty($request_id)){
			DB::table('faculty_sme_request')
			->where('request_id', $request_id) 
			->update(['status' => 3,'picked_id'=>Auth::user()->id]);
			
			return back()->with('success', "Task Picked!!");
		}else{
			return back()->with('error', "Required filed missing!!");
		}
	}
	
	public function faculty_sme_update_stauts(Request $request){
		$request_id 	= $request->request_id;
		$status 		= $request->status;
		$reject_reason  = $request->reject_reason;
		
		if(!empty($request_id) && !empty($status)){
			DB::table('faculty_sme_request')
			->where('id', $request_id) 
			->update([
				'status' => $status,
				'reason' => $reject_reason
			]);
			
			return back()->with('success', "Status Updated!!");
		}else{
			return back()->with('error', "Required filed missing!!");
		}
	}
	
	public function faculty_sme_assistant(){
		$logged_id = Auth::user()->id;
		$request_id = Input::get('request_id');
		
		$record = DB::table('faculty_sme_assistant')
					->select('faculty_sme_assistant.*','batch.name','timetables.from_time','timetables.to_time','faculty_sme_upload.file')
					->leftjoin('faculty_sme_upload','faculty_sme_upload.request_id','faculty_sme_assistant.request_id')
					->leftjoin('timetables','timetables.id','faculty_sme_assistant.timatable_id')
					->leftjoin('batch','batch.id','timetables.batch_id')
					->where('faculty_sme_assistant.user_id',$logged_id)
					->get();
					
		$today_class = DB::table('timetables')
						->select('timetables.id','timetables.from_time','timetables.to_time','batch.name')
						->leftjoin('batch','batch.id','timetables.batch_id')
						->where('assistant_id',$logged_id)
						->where('cdate',date('Y-m-d'))
						->get();
					
		return view('admin.faculty_sme.assistant',compact('request_id','record','today_class'));
	}
	
	public function faculty_sme_download_pdf(Request $request){
		$request_id = $request->request_id;
		$class_id 	= $request->class_id;
		$user_id 	= Auth::user()->id;
		
		if(!empty($request_id) && !empty($class_id) && !empty($user_id)){
			
			$pdf = DB::table('faculty_sme_upload')->where('request_id',$request_id)->first();
			if(!empty($pdf->file)){
				DB::table('faculty_sme_assistant')->insert(['request_id'=>$request_id,'timatable_id'=>$class_id,'user_id'=>$user_id]);
			
				$html = "<a href='".asset('laravel/public/faculty_sme/' . $pdf->file)."' download>Click here to download PDF</a>";
			
				return response(['status' => true, 'html' => $html], 200);
			}else{
				return response(['status' => false, 'html' => '','msg' => 'File Not Found!!'], 200);
			}
		}else{
			return response(['status' => false, 'html' => '','msg' => 'Something Went wrong!!'], 200);
		}
	}
	
	//Web View
	public function faculty_sme_request_index(){
		$user_id     = Input::get('user_id');
		
		// $logged_id = Auth::user()->id;
		// $record = DB::table('faculty_sme_request')		
					// ->where('user_id',$user_id)->orderby('id','desc')
					// ->get();
					
		$record = DB::table('faculty_sme_request')
					->select(
						'faculty_sme_request.*',
						DB::raw("(SELECT COUNT(*) FROM faculty_sme_chat 
								  WHERE faculty_sme_chat.request_id = faculty_sme_request.request_id 
									AND faculty_sme_chat.status = 2 AND faculty_sme_chat.user_id != ".$user_id.") as chat_status_2_count")
					)
					->where('user_id', $user_id)
					->orderBy('id', 'desc')
					->get();

		
		return view('admin.all_reports.faculty-sme.index',compact('user_id','record'));
	}
	
	public function faculty_sme_request_create(){
		$user_id     = Input::get('user_id');
		return view('admin.all_reports.faculty-sme.create',compact('user_id'));
	}
	
	public function faculty_sme_request_chat(){
		$user_id     = Input::get('user_id');
		$request_id     = Input::get('request_id');
		
		$record = DB::table('faculty_sme_chat')
					->select('faculty_sme_chat.*','users.name')
					->leftjoin('users','users.id','faculty_sme_chat.user_id')
					->where('request_id',$request_id)
					->get();
					
		
		DB::table('faculty_sme_chat')->where('request_id',$request_id)->where('user_id','!=',$user_id)->update(['status'=> 1]);
		
		return view('admin.all_reports.faculty-sme.chat',compact('user_id','request_id','record'));
	}
	
	public function faculty_sme_request_submit(Request $request){
		$user_id 	= $request->user_id;
		$req_msg 	= $request->req_msg;
		$req_date 	= $request->req_date;
		
		
		if($user_id!="" && $req_msg!=""){
			$req_no = DB::table('faculty_sme_request')->max('request_id') + 1;
			
			$data = array(
				"user_id"		=>	$user_id,
				"message"		=>	$req_msg,
				"date"			=>	$req_date,
				"request_id"	=>	$req_no,
				"created_at"	=>	date('Y-m-d\TH:i'),
			);
			
			DB::table('faculty_sme_request')->insert($data);
			
			return redirect()->route('faculty-sme-request-index', ['user_id' => $user_id])->with('success', 'Request Submitted Successfully');

		}else{
			return back()->with('error', "Request filed missing!!!");
		}
	}
	
	public function faculty_sme_request_chat_submit(Request $request){
		$user_id 	= $request->user_id;
		$request_id 	= $request->request_id;
		$chat_msg 	= $request->chat_msg;
		
		if($user_id!="" && $request_id!="" && $chat_msg!=""){
			$data = array(
				"user_id"		=>	$user_id,
				"request_id"	=>	$request_id,
				"message"		=>	$chat_msg,
				"created_at"	=>	date('Y-m-d\TH:i'),
			);
			
			DB::table('faculty_sme_chat')->insert($data);
			
			//Push Notification			
			$getUserID = DB::table('faculty_sme_request')->where('request_id',$request_id)->first();			
			$user = User::where('id',$getUserID->user_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();
			
			$load = array();
			$load['title'] 		 = 'Reply from the SME on your request ID :'.$request_id;
			$load['description'] = 'Reply from the SME on your request ID  :'.$request_id;
			$load['body'] 		 = 'Reply from the SME on your request ID  :'.$request_id;
			$load['image'] 		 = asset('laravel/public/images/test-image.png');
			$load['date'] 		 = date('d-m-Y');
			$load['status'] 	 = NULL;
			$load['type'] 		 = 'general';
	 
			$token = [];
			if(count($user) > 0){
				foreach ($user as $key => $value) {
					if(!empty($value->gsm_token)){
						$token[] = $value->gsm_token;
					}
				}
			}
			$this->android_notification($token,$load); 
			
			// return back()->with('success', "Chat Successfully Submitted");
			
			return back();
		}else{
			return back();
		}
	}
	
	//22-11-2023
	public function getMainCategory(){	
		$file_name = "/var/www/html/laravel/public/faculty-sme-category.txt";
		
		//getQuestionExams
		
		
		if(!file_exists($file_name)){
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://support.utkarshapp.com/getCatExams',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
				'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return json_decode($response, true);
		}else{
			$response=file_get_contents($file_name);
		
			return json_decode($response);
		}
	}
	
	public function getCategoryExam($category_id){					
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/getCatExams?cat_id='.$category_id,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// echo $response;

		return json_decode($response, true);

	}
	
	public function getExamSubject($exam_id){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getTestSubjects',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('course_ids' => $exam_id),
		  CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// echo $response;
		return json_decode($response, true);
	}
	
	public function getSubjectChapter($subject_id){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getTestChapters',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('subject_ids' => $subject_id),
		  CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// echo $response;
		return json_decode($response, true);
	}
	
	public function get_cat_exam(Request $request){
		$category_id = $request->category_name;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .= "<option value=''>Select</option>";
		$all_courses = $this->getCategoryExam($category_id);

		foreach ($all_courses['data'] as $val) {                       
			$html .= "<option value='".$val['title']."$#".$val['id']."' data-id='".$val['id']."'>".$val['title']."</option>";            
		}

		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function get_exam_subject(Request $request){
		$exam_id = $request->exam_id;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .= "<option value=''>Select</option>";
		$all_courses = $this->getExamSubject($exam_id);

		foreach ($all_courses['data'] as $val) {                       
			$html .= "<option value='".$val['title']."$#".$val['id']."' data-id='".$val['id']."'>".$val['title']."</option>";            
		}

		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function get_subject_chapter(Request $request){
		$subject_id = $request->subject_id;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .= "<option value=''>Select</option>";
		$all_courses = $this->getSubjectChapter($subject_id);

		// foreach ($all_courses['data'] as $val) {                       
			// $html .= "<option value='".$val['title']."$#".$val['id']."' data-id='".$val['id']."'>".$val['title']."</option>";            
		// }
		
		foreach ($all_courses['data'] as $val) {
			$title = htmlspecialchars($val['title'], ENT_QUOTES, 'UTF-8');
			$value = htmlspecialchars($val['title'] . '$#' . $val['id'], ENT_QUOTES, 'UTF-8');
			$id = htmlspecialchars($val['id'], ENT_QUOTES, 'UTF-8');

			$html .= "<option value='{$value}' data-id='{$id}'>{$title}</option>";
		}


		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function reuse_request(Request $request){
		$upload_id	=	$request->upload_id;
		$request_id	=	$request->request_id;
		if(!empty($upload_id) && !empty($request_id)){		
			$getRecord = DB::table('faculty_sme_upload')->where('id',$upload_id)->first();
			
			$data = array(
				"user_id"			=>	Auth::user()->id,
				"request_id"		=>	$request_id,
				"category"			=>	$getRecord->category,
				"exam"				=>	$getRecord->exam,
				"subject"			=>	$getRecord->subject,
				"topic"				=>	$getRecord->topic,
				"no_question"		=>	$getRecord->no_question,
				"mode"				=>	$getRecord->mode,
				"level"				=>	$getRecord->level,
				"requirement_for"	=>	$getRecord->requirement_for,
				"language"			=>	$getRecord->language,
				"file"				=>	$getRecord->file,
				"created_at"		=>	date('Y-m-d h:i:s'),
				"parent_request"	=>	$getRecord->request_id,
				"test_id"	=>	$getRecord->test_id,
			);
					
			
			DB::table('faculty_sme_upload')->insert($data);
			
			return response(['status' => true, 'message' => 'Your Request Successfully Reuse'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed mission!!'], 200);
		}
	}
	
	
	public function sme_visibility(Request $request){
		$search	=	Input::get('search');
		$request_id	=	Input::get('request_id');
		
		if($search!=''){			
			$category			=	$request->category;		
			$subject			=	$request->subject;
			$chapter			=	$request->chapter;
			$no_of_question		=	$request->no_of_question;
			$mode				=	$request->mode;
			$level				=	$request->level;
			$requirement_for	=	$request->requirement_for;
			$language			=	$request->language;
			
			
			$record = DB::table('faculty_sme_request')
						->select('faculty_sme_request.request_id as main_rid','faculty_sme_request.status as restatus','fsu.*','emp.name as sme_name','fac.name as faculty_name')
						->leftJoin('faculty_sme_upload as fsu','fsu.request_id','faculty_sme_request.request_id')
						->leftJoin('users as emp','emp.id','faculty_sme_request.picked_id')
						->leftJoin('users as fac','fac.id','faculty_sme_request.user_id')
						->orderby('faculty_sme_request.id','desc');
			
			if(!empty($category)){
				$record->where('fsu.category',$category);
			}
			
			if (!empty($request->exam)) {
				$newexam = explode('$#',$request->exam);
				$record->where('fsu.exam', 'like', '%' . $newexam[0] . '%');
			}

			if(!empty($subject)){
				$newexam = explode('$#',$request->subject);
				$record->where('fsu.subject', 'like', '%' . $newexam[0] . '%');
			}
			
			if(!empty($chapter)){
				$newexam = explode('$#',$request->chapter);
				$record->where('fsu.topic', 'like', '%' . $newexam[0] . '%');
			}
			
			if(!empty($no_of_question)){
				$record->where('fsu.mode',$no_of_question);
			}
					
			if(!empty($mode)){
				$record->where('fsu.mode',$mode);
			}
			
			if(!empty($level)){
				$record->whereIn('fsu.level',$level);
			}
			
			if(!empty($requirement_for)){
				$record->where('fsu.requirement_for',$requirement_for);
			}
			
			if(!empty($language)){
				$record->where('fsu.language',$language);
			}
			
			$employee = $request->employee;	
			if (!empty($employee)) {
				$record->where('faculty_sme_request.picked_id', $employee);
			}	

			$faculty = $request->faculty;	
			if (!empty($faculty)) {
				$record->where('faculty_sme_request.user_id', $faculty);
			}	
			
			$req_status = $request->req_status;	
		
			if (!empty($req_status)) {
				$record->where('faculty_sme_request.status', $req_status);
			}	
			
			$record = $record->get();
		}else{
			$record = array();
		}
		
		$all_courses	=	$this->getMainCategory();
		$all_courses  	=	$all_courses->data;
		
		
		
		$employee = User::where('department_type',4)->where('status',1)->where('is_deleted','0')->get();
		$faculty = User::where('role_id',2)->where('status',1)->where('is_deleted','0')->get();
		
					
		return view('admin.faculty_sme.sme_visibility',compact('all_courses','record','request_id','employee','faculty'));
	}
	
	
	
	public function index_copy(Request $request)
    {
		$request_id = $request->request_id;
		$req_status = $request->req_status;			
		$logged_id 	= Auth::user()->id;		
		
		$logged_subject_id = DB::table('faculty_subjects')
						->select(DB::raw("GROUP_CONCAT(subject_id SEPARATOR ', ') as subject_ids"))
						->where('user_id', $logged_id)
						->groupBy('user_id')
						->value('subject_ids');
		$logged_subject_id = trim(preg_replace('/\s+/', '', $logged_subject_id));
		
		$record = DB::table('faculty_sme_request')
			->select(
				'faculty_sme_request.*',
				'users.name',
				DB::raw("GROUP_CONCAT(faculty_subjects.subject_id SEPARATOR ', ') as subject_ids"),
				'faculty_sme_upload.file',
				'faculty_sme_assistant.request_id as assistant_used',
				DB::raw("(SELECT COUNT(*) FROM faculty_sme_chat 
						  WHERE faculty_sme_chat.request_id = faculty_sme_request.request_id 
							AND faculty_sme_chat.status = 2 AND faculty_sme_chat.user_id != ".$logged_id.") as chat_status_2_count")
			)
			->leftJoin('faculty_sme_upload', 'faculty_sme_upload.request_id', '=', 'faculty_sme_request.id')
			->leftJoin('users', 'users.id', '=', 'faculty_sme_request.user_id')
			->leftJoin('faculty_subjects', 'faculty_subjects.user_id', '=', 'users.id')
			->leftJoin('faculty_sme_assistant', 'faculty_sme_assistant.request_id', '=', 'faculty_sme_upload.request_id')
			->groupBy('faculty_sme_request.request_id', 'users.name')
			->havingRaw("SUM(FIND_IN_SET(faculty_subjects.subject_id, ?)) > 0", [$logged_subject_id])
			->orderBy('faculty_sme_request.id', 'desc');


		if (!empty($request_id)) {
			$record->where('faculty_sme_request.request_id', $request_id);
		}

		if (!empty($req_status)) {
			$record->where('faculty_sme_request.status', $req_status);
		}
		$record = $record->get();		
        return view('admin.faculty_sme.index',compact('record'));
    }
}
