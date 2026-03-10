<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use Input;
use DB;
use Auth;
use App\ApiNotification;
use Validator;
use Excel;


class MultiCoursePlannerController extends Controller
{ 
    public function plannerrequestview(Request $request){
		$logged_id       = Auth::user()->id; 
		$department_type = Auth::user()->department_type;  
		$name       	 = Auth::user()->name;   
		$register_id   	 = Auth::user()->register_id;
		$role       	 = Auth::user()->role_id;
		$user_branch	 = Auth::user()->user_branches[0]->branch_id;
		
		$name	   = $request->name;
		$status	   = $request->status;		
		$branch_location	   = $request->branch_location;		
		  
		$record = DB::table('planner_request as pr') 
				->select('pr.*','course.name as cname','users.name as uname','cpsr.faculty_id')
				->leftjoin('course','course.id','pr.course_id')
				->leftjoin('course_planner_sme_relation as cpsr','cpsr.req_id','pr.id')
				->leftjoin('users','users.id','pr.user_id')
				->orderby('id','desc');
		
		//Logged IN Person Request View 
		if($department_type!=4 && $role!=21 && $role!=27 && $logged_id!=8232 && $logged_id!=8866){
			$record->where('pr.user_id',Auth::user()->id);
		}
		
		//SME Request View
		if($department_type==4 && $role!=21 && $role!=27 && $logged_id!=8232 && $logged_id!=8866){
			$record->where('cpsr.sme_id',Auth::user()->id);
		}
				
		//Timetable Incharge view
		if($role==27 && $logged_id!=8232 && $logged_id!=8866){
			$record->where('cpsr.is_subject',1);
		} 
		
		//Academic Team (Static ID use) and Content Incharge View all request  
		
		if(!empty($name)){
			$record->where('pr.planner_name',$name);
		}
		 
		if(!empty($status)){
			$record->where('pr.status',$status);
		}
		
		if(!empty($branch_location)){
			$record->where('pr.city',$branch_location);
		}
		
		$record = $record->groupby('pr.id')->get();
		
		return view('admin.multi-course-planner.request_view',compact('record'));
	}
	
    public function multiplannerrequest()
    { 
		$plantext = 'Request';
        return view('admin.multi-course-planner.planner_request',compact('plantext'));
    }
    
	public function editmultiplannerrequest(Request $request,$id){
		$plantext = 'Edit';
		$getRequest = DB::table('planner_request')->where('id',$id)->first();
		
		return view('admin.multi-course-planner.planner_request',compact('getRequest','plantext'));
	}
	
	public function save_planner_request(Request $request){
		$edit_id			=	$request->edit_id;
		$validatedData = $request->validate([
			'planner_name' => [
				'required',
				Rule::unique('planner_request')->where(function ($query) use ($request, $edit_id) {
					$query->where('course_id', $request->course_id);
					if (!empty($edit_id)) {
						// Exclude the current record (edit) from the unique check
						$query->where('id', '!=', $edit_id);
					}
					return $query;
				}),
			],
			'course_id' => 'required',
		], [
			'planner_name.required' => 'Planner name is required.',
			'planner_name.unique' => 'The planner name already exists for this course.',
			'course_id.required' => 'Course is required.',
		]);

		
		$course_id		=	$request->course_id;
		$planner_name	=	$request->planner_name;
		$branch_location=	$request->branch_location;
		$duration		=	$request->duration;
		$mode			=	$request->mode;
		$timeline		=	$request->timeline;
		$remark			=	$request->remark;
		
		
		if(!empty($course_id)){
			$data = array(
				"user_id"		=>	Auth::user()->id,
				"course_id"		=>	$course_id,
				"planner_name"	=>	$planner_name,
				"city"			=>	$branch_location,
				"duration"		=>	$duration,
				"mode"			=>	$mode,
				"timelines"		=>	$timeline,
				"remark"		=>	$remark
			);
			
			if(!empty($edit_id)){
				$data['status'] = 1;
				$data['reason'] = NULL;
				DB::table('planner_request')->where('id',$edit_id)->update($data);
				
				$this->maintain_history(Auth::user()->id, 'planner_request', $edit_id, 'Reopen_planner_request', json_encode($data));
				return redirect()->route('admin.multi-course-planner.planner-request-view')->with('success', 'Resubmit Request Successfully!!');
			}else{
				$lastId = DB::table('planner_request')->insertGetId($data);
				$this->maintain_history(Auth::user()->id, 'planner_request', $lastId, 'add_planner_request', json_encode($data));
				return redirect()->route('admin.multi-course-planner.planner-request-view')->with('success', 'Request Added!!');
			}
		}else{
			return redirect()->route('admin.multi-course-planner.multi-planner-request')->with('error', 'Required Filed Missing');
		}
	}
	
	public function subject_assign(Request $request, $id){
		$record = DB::table('planner_request')->where('id',$id)->first();
		
		$subject = DB::table('subject as s')
					->join('topic_master as tm', 'tm.subject_id', '=', 's.id')
					->where('s.status', 1)
					->select('s.id', 's.name')
					->groupBy('s.id', 's.name')
					->get();
		
		$sme 	 = DB::table('users')->where('status',1)->where('is_deleted','0')->where('department_type',4)->get();
		$faculty = DB::table('users')->where('status',1)->where('is_deleted','0')->where('role_id',2)->get();
		
		$sme_relation = DB::table('course_planner_sme_relation')->where('req_id',$id);
		
		if(Auth::user()->role_id==27 && Auth::user()->id!=8866){
			$sme_relation->where('is_subject',1);
		}
		
		$sme_relation = $sme_relation->get();
		
		return view('admin.multi-course-planner.subject_assign',compact('record','subject','sme','faculty','sme_relation','id'));
	}
	
	
	public function save_subject_assign(Request $request){
		$course_id  = $request->course_id;
		$req_id     = $request->req_id;
		$status     = $request->status;
		$reason     = $request->reason;

		$subject_ids = $request->subject_id;
		$sme_ids     = $request->sme_id;   
		$faculty_id  = $request->faculty_id;   
		$remarks     = $request->remark;  
		$faculty_remark     = $request->faculty_remark;  
		$edates      = $request->edate; 
		$sstatus      = $request->sstatus; 
		$sr_ids      = $request->sr_id; 
		$is_academic      = $request->is_academic; 
		$tt_remark      = $request->tt_remark; 
		

		if(!empty($course_id) && !empty($req_id)){
			if(Auth::user()->role_id==21){
				DB::table('planner_request')->where('id', $req_id)->update(['status' => $status,'reason' => $reason]);
			}
			
			if (!empty($subject_ids) && is_array($subject_ids)) {
				$count = count($subject_ids);

				for ($i = 0; $i < $count; $i++) {
					if (empty($subject_ids[$i])) {
						continue;
					}
					
					$data = [
						"course_id"    => $course_id,
						"req_id"       => $req_id,
						"subject_id"   => $subject_ids[$i]??0,
						"sme_id"  	   => $sme_ids[$i]??0,
						"faculty_id"   => $faculty_id[$i]??0,
						"sme_remark"   => $remarks[$i]??'-',
						"date"         => $edates[$i],
						"is_subject"   => $sstatus[$i]??0,
						"fassign"	   => date('Y-m-d h:i:s'),
						"faculty_remark" => $faculty_remark[$i]??'-',
						"tt_remark"    => $tt_remark[$i]??'-',
					];
					
					if (!empty($sr_ids[$i])) {
						
						DB::table('course_planner_sme_relation')->where('id', $sr_ids[$i])->update($data);
						
						$this->maintain_history(Auth::user()->id, 'course_planner_sme_relation', $sr_ids[$i], 'Update_Subject Assign', json_encode($data));
					} else {
						$lastId  = DB::table('course_planner_sme_relation')->insertGetId($data);
						
						$this->maintain_history(Auth::user()->id, 'course_planner_sme_relation', $lastId, 'Subject Assign', json_encode($data));
					}
				}
			}
			return redirect()->route('admin.multi-course-planner.planner-request-view')->with('success', 'Request Updated!!');
		}else{
			return redirect()->route('admin.multi-course-planner.subject-assign',[$req_id])->with('error', 'Required Filed Missing!!');
		}
	}
	
	public function multiplannersummary(Request $request,$req_id){
		$logged_id = Auth::user()->id;
		$department_type = Auth::user()->department_type;
		$role_id = Auth::user()->role_id;
		
		if($role_id==29 || $department_type==13 || $logged_id!=8866 || ($department_type==50 && $logged_id!=8232)){
			$record	= array();
			$topic	= array();
				
			$topic_relation = DB::table('course_planner_topic_relation as cptr')
				->select(
					'cptr.*',
					'tm.name as topic_name',
					'tm.en_name as topic_en_name',
					// 'stm.name as sub_topic_name',
					// 'stm.en_name as sub_topic_en_name',
					'sm.name as subject_name',
					'tm.subject_id',
					'users.name as sme_name',
					'cpsr.faculty_remark'
				)
				->leftJoin('topic_master as tm', 'tm.id', '=', 'cptr.topic_id')					
				// ->leftJoin('sub_topic_master as stm', 'stm.id', '=', 'cptr.sub_topic_id')
				->leftJoin('subject as sm', 'sm.id', '=', 'tm.subject_id')
				->leftJoin('course_planner_sme_relation as cpsr', function($join) {
					$join->on('cpsr.req_id', '=', 'cptr.req_id')
						 ->on('cpsr.subject_id', '=', 'cptr.subject_id'); // Ensure subject match
				})
				->leftjoin('users','users.id','cpsr.sme_id')
				->where('cpsr.req_id', $req_id)
				->where('cpsr.is_subject', 1)
				->where('cptr.fstatus', 1)
				->get()
				->groupBy('subject_id');

		}else{
			//Subject ID Get
			$record = DB::table('planner_request')
						->select('planner_request.*','cpsr.subject_id','cpsr.sme_remark','cpsr.date','subject.name as subject_name')
						->leftjoin('course_planner_sme_relation as cpsr','cpsr.req_id','planner_request.id')
						->leftjoin('subject','subject.id','cpsr.subject_id')
						->where('planner_request.id',$req_id);
						
			if($role_id!=21 && $logged_id!=8232 && $logged_id!=8866){	
				$record->where('cpsr.sme_id',Auth::user()->id);
			}
			
			$record = $record->get();
			
			$subjectIds = $record->pluck('subject_id')->unique()->toArray();
						
			//Topic Master Get
			$topic = DB::table('topic_master')->orderby('id','desc');
			if($role_id!=21 && $logged_id!=8232 && $logged_id!=8866){
				
				$topic->whereIn('subject_id',$subjectIds);
			}
			$topic = $topic->where('status',1)->get();
			
			
			
			//Uploaded Topic & Sub Topic
			$topic_relation = DB::table('course_planner_sme_relation as cpsr')
				->select(
					'cpsr.*',
					'subject.name as subject_name',
					'cptr.status as topic_sme_status',
					'users.name as sme_name'
				)
				->leftjoin('subject','subject.id','cpsr.subject_id')
				->leftjoin('users','users.id','cpsr.sme_id')
				->leftJoin('course_planner_topic_relation as cptr', function($join) use ($req_id) {
					$join->on('cptr.subject_id', '=', 'cpsr.subject_id')
						 ->where('cptr.req_id', '=', $req_id);
				});
				
			if ($role_id != 21 && $logged_id != 8232 && $logged_id!=8866) {
				$topic_relation->where('sme_id',Auth::user()->id);
			}
			
			$topic_relation = $topic_relation->where('cpsr.req_id',$req_id)
				->groupby('subject_id')
				->get();
		}

		return view('admin.multi-course-planner.multi_planner_summary',compact('topic','record','topic_relation','req_id'));
	}
	
	public function save_planner_summary(Request $request){
		
		$req_id		=	$request->req_id;
		$course_id	=	$request->course_id;
		$topic_id	=	$request->topic_id;
		$sub_topic	=	$request->sub_topic;
		$sr_ids		=	$request->sr_id;
		$subject_ids = $request->subject_id;
		$duration	 = $request->duration;
		

		if(!empty($req_id) && !empty($course_id)){
			if (!empty($topic_id) && is_array($topic_id)) {
				$count = count($topic_id);

				for ($i = 0; $i < $count; $i++) {
					if (empty($topic_id[$i])) continue;	
					
					$subject_id = $subject_ids[$i] ?? 0;
					$data = [
						"course_id"     => $course_id,
						"req_id"        => $req_id,
						"topic_id"   	=> $topic_id[$i]??0,
						"sub_topic_id"  => $sub_topic[$i]??0,
						"subject_id"    => $subject_id,
						"status"  		=> $request->submit_type
					];
					
					
					if(Auth::user()->id==8232 || Auth::user()->id==8866){
						$data['fstatus'] = 1;
 					}
					
					if (!empty($sr_ids[$i])) {
						$existing = DB::table('course_planner_topic_relation')->where('id', $sr_ids[$i])->first();
						$new_duration = $duration[$i] ?? null;

						if (!empty($existing->duration)) {
							$data['duration'] = $existing->duration;
						} 
					}else{
						$data['duration'] = $duration[$i] ?? 0;
					}
					

																
					if (!empty($sr_ids[$i])) {
						DB::table('course_planner_topic_relation')->where('id', $sr_ids[$i])->update($data);
					}else{
						$existing = DB::table('course_planner_topic_relation')
							->where([
								['course_id', '=', $data['course_id']],
								['req_id', '=', $data['req_id']],
								['topic_id', '=', $data['topic_id']],
								['sub_topic_id', '=', $data['sub_topic_id']],
							])
							->exists();
							
						if (!$existing) {
							DB::table('course_planner_topic_relation')->insertOrIgnore($data);
						}
					}
				}
			}
			
			return response(['status' => true, 'message' => 'Saved'], 200);
		}else{
			return redirect()->route('admin.multi-course-planner.planner-request-view')->with('error', 'Required Filed Missing!!');
		}
	}
	
	//Topic
	public function topic(Request $request,$id){
		$name	=	$request->name;
		$status	=	$request->status;
		$record = DB::table('topic_master')->select('topic_master.*','subject.name as subject_name')->leftjoin('subject','subject.id','topic_master.subject_id')->where('subject_id',$id)->orderby('id','desc');
		
		if(!empty($name)){
			$record->where('topic_master.name',$name);
		}
		
		if(!empty($status)){
			$record->where('topic_master.status',$status);
		}else{
			$record->where('topic_master.status',1);
		}
		
		$record = $record->get();
		return view('admin.multi-course-planner.topic',compact('record','id'));
	}
	
	public function add_topic(Request $request,$id){
		return view('admin.multi-course-planner.add_topic',compact('id'));
	}
	
	public function edit_topic($id,$subject_id){
		$record = DB::table('topic_master')->where('id',$id)->first();
				
		return view('admin.multi-course-planner.add_topic',compact('record','id','subject_id'));
	}	
	
	public function save_topic_master(Request $request){
		$tname 	= $request->tname;
		$tenname 	= $request->tenname;
		$subject_id	= $request->subject_id;
		$tid 	= $request->tid;
		
		if(!empty($tname)){
			if(!empty($tid)){
				DB::table('topic_master')->where('id',$tid)->update(['subject_id'=>$subject_id,'name'=>$tname,'en_name'=>$tenname]);
				return redirect()->back()->with('success', 'Topic Updated!!');

			}else{
				DB::table('topic_master')->insert(['subject_id'=>$subject_id,'name'=>$tname,'en_name'=>$tenname]);
				return redirect()->back()->with('success', 'Topic Added!!');

			}
		}else{
			return redirect()->route('admin.multi-course-planner.topic')->with('error', 'Something Went Wrong !');
		}
	}
	
	public function delete_topic(Request $request,$id){
		$inputs['status'] = 3;   
		$updated = DB::table('topic_master')->where('id', $id)->update($inputs);

		if ($updated) {
			return redirect()->back()->with('success', 'Topic Master Deleted Successfully');
		} else {
			return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong!');
		}
	}
	
	//Sub Topic
	public function sub_topic(Request $request,$id){
		$record = DB::table('sub_topic_master')
					->select('sub_topic_master.*','topic_master.name as topic_name','subject.name as subject_name')
					->leftjoin('topic_master','topic_master.id','sub_topic_master.topic_id')
					->leftjoin('subject','subject.id','topic_master.subject_id')
					->where('sub_topic_master.status',1)
					->where('topic_master.subject_id',$id)
					->get();
		return view('admin.multi-course-planner.sub_topic',compact('record','id'));
	}
	
	public function add_sub_topic(Request $request,$id){
		return view('admin.multi-course-planner.add_sub_topic',compact('id'));
	}
	
	public function edit_sub_topic($id){
		$record = DB::table('sub_topic_master')
					->select('sub_topic_master.*','topic_master.id as topic_id')
					->leftjoin('topic_master','topic_master.id','sub_topic_master.topic_id')
					->where('sub_topic_master.id',$id)
					->first();
		
		return view('admin.multi-course-planner.add_sub_topic',compact('record'));		
	}
	
	public function save_sub_topic_master(Request $request){
		$topic_id 	= $request->topic_id;
		$stname 	= $request->stname;
		$status 	= $request->status;
		$tsid 	= $request->tsid;
		
		if(!empty($stname)){
			if(!empty($tsid)){
				DB::table('sub_topic_master')->where('id',$tsid)->update(['name'=>$stname,'topic_id'=>$topic_id,'status'=>$status]);
				return redirect()->back()->with('success', 'Sub Topic Updated!!');
			}else{
				DB::table('sub_topic_master')->insert(['name'=>$stname,'topic_id'=>$topic_id,'status'=>$status]);
				return redirect()->back()->with('success', 'Sub Topic Added!!');
			}
		}else{
			return redirect()->route('admin.multi-course-planner.sub-topic')->with('error', 'Something Went Wrong !');
		}
	}
	
	public function delete_sub_topic(Request $request,$id){
		$inputs['status'] = 3;   
		$updated = DB::table('sub_topic_master')->where('id', $id)->update($inputs);

		if ($updated) {
			return redirect()->back()->with('success', 'Sub Topic Master Deleted Successfully');
		} else {
			return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong!');
		}
	}
	
	public function get_sub_topic(Request $request){
		$subCatData = DB::table('sub_topic_master')->where('topic_id', $request->topic_id)->where('status',1)->get();
		
		if (count($subCatData) > 0)
        {
            echo $res = "<option value=''> Select Sub Topic </option>";
            foreach ($subCatData as $key => $value)
            {
                if (!empty($value->id))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . " || ".$value->en_name."</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Sub Topic Not Found </option>";
            die();
        }
	}
	
	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('all_history')->insert($history_data);
	}
	
	public function show_work_status(Request $request){
		$req_id = $request->request_id;

		// Step 1: Get base dataset
		$work_status = DB::table('course_planner_sme_relation as cpsr')
			->where('req_id', $req_id)
			->get();

		// Step 2: Basic counts
		$totalSubjects = $work_status->count();
		$contentApproved = $work_status->where('is_subject', 1)->count();
		$assignToFaculty = $work_status->where('is_subject', 1)->where('faculty_id', '!=', 0)->count();

		// Step 3: Get subject_ids with is_subject = 1 and faculty_id != 0
		$subjectIds = $work_status->where('is_subject', 1)->where('faculty_id', '!=', 0)->pluck('subject_id')->unique();

		// Step 4: Join to course_planner_topic_relation and count status
		$topic_status = DB::table('course_planner_topic_relation as cptr')
			->whereIn('cptr.subject_id', $subjectIds)
			->where('cptr.req_id', $req_id)
			->select('cptr.subject_id', 'cptr.fstatus')
			->get()
			->groupBy('subject_id');

		// Initialize counts
		$pendingAtFaculty = 0;
		$complete = 0;

		// Step 5: Count subjects based on fstatus
		foreach ($topic_status as $subject_id => $topics) {
			$fstatuses = $topics->pluck('fstatus')->unique();

			if ($fstatuses->contains(1)) {
				$complete++;
			} elseif ($fstatuses->contains(0) || $fstatuses->contains(2)) {
				$pendingAtFaculty++;
			}
		}

		// Final result (example output)
		echo "<tr>
					<td>{$totalSubjects}</td>
					<td>{$contentApproved}</td>
					<td>{$assignToFaculty}</td>
					<td>{$pendingAtFaculty}</td>
					<td>{$complete}</td>
				</tr>";
	}
	
	public function upload_planner(Request $request){
		$request_id		=	$request->request_id;
		DB::table('planner_request')->where('id',$request_id)->update(['status'=>2]);
		
		$file		= $request->file('planner_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validator = Validator::make($request->all(), [
			   'planner_file' => 'required|mimes:xlsx,xls,csv',
			]);
			if ($validator->fails()){
				$messages = $validator->errors(); 
				return redirect()->back()->with('error', $messages->first('planner_file'));
			}
		}
		
		$path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		
		$prequest = DB::table('planner_request')->where('id',$request_id)->first();
		$data = array();
		
		foreach ($import[0] as $key => $value) {
			$value[0]=trim($value[0]);
			$value[1]=trim($value[1]);
			$value[2]=trim($value[2]);
			
			$subject_id   = $value[0];
			$topic_id     = $value[1];
			$duration	  = $value[2];			
			$course_id	  =	$prequest->course_id;
			
			
			//Insert 
			$checkTopic = DB::table('course_planner_topic_relation')->where('req_id',$request_id)->where('topic_id',$topic_id)->count();
			if($checkTopic==0){
				if(!empty($topic_id)){
					$record = array();
					$record['req_id']		= $request_id;
					$record['subject_id']   = $subject_id;				
					$record['topic_id']		= $topic_id;
					$record['duration'] 	= $duration;
					$record['course_id']	= $course_id;
					$record['created_at']	= date('Y-m-d H:i:s');			
					$record['status']		= 1;			
					$record['fstatus']		= 1;			
					$record['sub_topic_id']		= 0;			
								
					DB::table('course_planner_topic_relation')->insert($record);
				}
			}
			
			
			$checkSubject = DB::table('course_planner_sme_relation')->where('req_id',$request_id)->where('subject_id',$subject_id)->count();
			
			if($checkSubject==0){
				$data = array(
					"req_id"		=> $request_id,
					"course_id"		=> $course_id,
					"subject_id"	=> $subject_id,
					"sme_id"		=> 0,
					"faculty_id"	=> 0,
					"is_subject"	=> 1,
					"status"		=> 1,
					"date"			=> date('Y-m-d'),
					"sme_remark"	=> '-',
				);	
				DB::table('course_planner_sme_relation')->insert($data);
			}
		}		
		return redirect()->back()->with('success', 'Planner Uploaded!!');
	}
	
	
	public function upload_topics(Request $request){
		$subject_id		=	$request->subject_id;
		
		$file		= $request->file('topics_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validator = Validator::make($request->all(), [
			   'topics_file' => 'required|mimes:xlsx,xls,csv',
			]);
			if ($validator->fails()){
				$messages = $validator->errors(); 
				return redirect()->back()->with('error', $messages->first('topics_file'));
			}
		}
		
		$path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		

		$data = array();
		
		foreach ($import[0] as $key => $value) {
			$value[0]=trim($value[0]);
			$value[1]=trim($value[1]);
			
			$chapter_name   = $value[0];
			$enchapter_name   = $value[1];
						
			//Insert 
			$checkTopic = DB::table('topic_master')->where('subject_id',$subject_id)->where('name',$chapter_name)->where('en_name',$enchapter_name)->count();
			if($checkTopic==0){
				$record = array();
				$record['subject_id']	= $subject_id;
				$record['name']   		= $chapter_name??'-';				
				$record['en_name']		= $enchapter_name??'-';
				$record['created_at']	= date('Y-m-d H:i:s');			
				$record['status']		= 1;			
							
				DB::table('topic_master')->insert($record);
			}
		}		
		return redirect()->back()->with('success', 'Topics Uploaded!!');
	}
}
