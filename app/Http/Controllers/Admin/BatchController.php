<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\CourseSubjectRelation;
use App\Batch;
use App\Batchrelation;
use Input;
use DB;
use App\Timetable;
use App\StartClass;
use App\Subject;
use App\Chapter;
use App\Topic;
use Validator;
use Excel;
use Auth;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $course_id = Input::get('course_id');
        $fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
        $name = Input::get('name');
        $branch = Input::get('branch');

        $status = Input::get('status');
        $type = Input::get('type');
		$is_chanakya = Input::get('is_chanakya');
		$cPlanner = Input::get('cPlanner');
		$bcode = Input::get('bcode');
		
		$params = array();
		$page   = Input::get('page');
		$res    = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}

        $batches = Batch::with('course','batch_relations')->where('is_deleted', '0')->orderBy('id','desc');

        if (!empty($course_id)){
            $batches->where('course_id', $course_id);
        }
		
		if (!empty($name)){
            //$batches->where('name', 'LIKE', '%' . $name . '%');
            $batches->whereRAW("(name LIKE '%".$name."%' OR erp_course_id='$name')");
        }

        if (!empty($branch)){
            $batches->where('branch', 'LIKE', '%' . $branch . '%');
        }
		

        if(!empty($status)){
            if($status == 'Inactive'){
                $batches->where('status', '=', '0');
            }else if($status == 'Completed'){
                $batches->where('status', '=', '2');
            }else{
                $batches->where('status', '=', '1');
            }
        }
		
		if(!empty($type)){
            $batches->where('type', '=', $type);
        }
		
		if($is_chanakya=="Yes"){
			$batches->where('mentor_id', '!=', 0)->whereNotNull('mentor_id');
		}elseif($is_chanakya=="No"){
			$batches->whereNull('mentor_id')->orWhere('mentor_id','=', 0);
		}

        if (!empty($fdate) && !empty($tdate)) {
            $batches->where('start_date', '>=', $fdate)->where('start_date', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $batches->where('start_date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $batches->where('start_date', '<=', $tdate);
        }
		
		
		if($cPlanner==1){
            $batches->where('course_planer_enable', 1);
        }else if($cPlanner==2){
			$batches->where('course_planer_enable','!=', 1);
		}
		
		if (!empty($bcode)){
            $batches->where('batch_code', $bcode);
        }
		
		
        // $batches = $batches->get();
		// print_r(json_encode($batches,true));
		// die();
		
		$batches = $batches->paginate(20);
		$pageNumber = 1;
		if(isset($page)){
			$pageNumber = (20*($page-1));
			$pageNumber = $pageNumber +1;
		}

        return view('admin.batch.index', compact('batches','pageNumber','params'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faculty = User::where('role_id', '2')->where('status', '1')->orderBy('id','desc')->get();
        return view('admin.batch.add', compact('faculty'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required',            
            'name' => 'required',
            'nickname' => 'required',
            'erp_course_id'=>'required|numeric|digits:5'
        ]);

        // echo $request->name; 
         // print_r($_POST);

        $batchAlready=Batch::where("batch_code",$request->batch_code)->first();
        if(!empty($batchAlready)){
          return redirect()->route('admin.batch.index')->with('error', 'Batch Already Added');
        }

        $request['nickname']=$request->name;
        
        $inputs = $request->only('course_id','name','start_date','end_date','branch','capacity','status','type','batch_code','venue','nickname','mentor_id','chopal_agent_id','course_planer_enable','erp_course_id');
        //die();
		
		if(!empty($request->total_test)){
			$inputs['total_test'] = $request->total_test;
		}
		
		$inputs['master_planner']=$request->planner_id;

        $batch = Batch::create($inputs);
		$insertId = $batch->id;
        $course = $request->course;      
        if(isset($course) && is_array($course)){               
            foreach($course['subject_id'] as $key => $value){
                if(!empty($value)){
					$check = DB::table('batchrelations')->where('batch_id', $insertId)->where('subject_id', $value)->where('is_deleted', '0')->get();
					if(count($check) ==0){
						if(!empty($course['no_of_hours'][$key])){
							$no_of_hours = $course['no_of_hours'][$key];
						}
						else{
							$no_of_hours = 0;
						}
						
						if(!empty($course['faculty_id'][$key])){
							//$faculty_id = $course['faculty_id'][$key];
						}
						else{
							$faculty_id = 0;
						}
						$data = array(                  
							'subject_id'=>$value,
							'faculty_id'=>$faculty_id,
							'no_of_hours'=>$no_of_hours
						);            
						$batch->batch_relations()->create($data);
					}
                }
            }
        }

        if ($batch->save()) {
            return redirect()->route('admin.batch.index')->with('success', 'Batch Added Successfully');
        } else {
            return redirect()->route('admin.batch.index')->with('error', 'Something Went Wrong !');
        }
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
		//$batch = Batch::with('batch_relations.subject')->where('is_deleted', '0')->find($id); 
		$batch = Batch::with(['batch_relations'=>function ($query) {
	        $query->where('is_deleted', '0');
	    },'batch_relations.subject'])->find($id);

		$course_id = Batch::where('id', $id)->where('is_deleted', '0')->first();
		//echo '<pre>'; print_r($course_id->course_id);die;
        $faculty = User::where('status', '1')->where('role_id',2)->orderBy('id','desc')->get();
        $users = User::where('status', '1')->orderBy('id','desc')->get();


        return view('admin.batch.edit', compact('batch', 'faculty','course_id','users'));
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
        $validatedData = $request->validate([
            'course_id' => 'required',            
            'name' => 'required',
			'erp_course_id'=>'required|numeric|digits:5'
        ]);

        $batch = Batch::with('batch_relations')->where('id', $id)->first();

       $inputs = $request->only('start_date','end_date','status','nickname','capacity','mentor_id','chopal_agent_id','category_head','course_planer_enable','erp_course_id','type');  

		if(!empty($request->total_test)){
			$inputs['total_test'] = $request->total_test;
		}
		
		$inputs['master_planner']=$request->planner_id;

        if(is_array($request->course) && !empty($request->course)) {
            //Batchrelation::where('batch_id', $id)->delete();
			//DB::table('batchrelations')->where('batch_id', $id)->update(['is_deleted' => '1']);
            $updateSubjects=array();
            $course = $request->course;
            foreach ($course['subject_id'] as $key => $value) {
                if(!empty($value)){
					if(!empty($course['no_of_hours'][$key])){
						$check = DB::table('batchrelations')->where('batch_id', $id)->where('subject_id', $value)->get();
						$data=array(                  
								'subject_id'=>$value,
								'no_of_hours'=>$course['no_of_hours'][$key],
								'is_deleted'=>'0'
							);
						$updateSubjects[]=$value;
						if(count($check)==0){
							$batch->batch_relations()->create($data);
						}else{
							DB::table('batchrelations')->where('batch_id', $id)->where('subject_id', $value)->update($data);
						}
					}
                }
            }

            DB::table('batchrelations')->where('batch_id', $id)->whereNotIn('subject_id',$updateSubjects)->update(['is_deleted' => '1']);
        }       

        if($batch->update($inputs)) {
			
			if($request->status==0){
				$feeStatus = 'Pause';
			}else if($request->status==2){
				$feeStatus = 'Stop';
			}else{
				$feeStatus = 'Running';
			}
			
			DB::connection('mysql2')->table("tbl_batch")
							->where("Bat_id", $request->batch_code)
							->update(['batch_running_status' => $feeStatus]);

            return redirect()->route('admin.batch.index')->with('success', 'Batch Updated Successfully');
        } else {
            return redirect()->route('admin.batch.index')->with('error', 'Something Went Wrong !');
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
        $batch = Batch::find($id);
		$inputs = array('is_deleted' => '1');
        //Batchrelation::where('batch_id', $id)->delete();

        if ($batch->update($inputs)) {
            return redirect()->back()->with('success', 'Batch Deleted Successfully');
        } else {
            return redirect()->route('admin.batch.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function get_batch_subject(Request $request){

        $course_id = $request->course_id;

        $subjects = CourseSubjectRelation::with('subject')->where('course_id', $course_id)->get();
        //print_r($subjects->toArray()); die;

        if (!empty($subjects)) {                         
            echo $res = "<option value=''> Select Subject </option>";
            foreach ($subjects as $key => $value) {
                if(!empty($value->subject->name) && !empty($value->subject->name)){
                	$duration=0;
					$topic = DB::table('topic')->select('*')
					    ->where('course_id', $course_id)->where('subject_id', $value->subject->id)
					    ->where('status', 1)->get();
					if(!empty($topic)){
						foreach ($topic as $Tdetails){
							$duration+= $Tdetails->duration;
						}
					}
					$duration=intdiv($duration, 60);

                    echo $res = "<option value='". $value->subject->id ."' data-duration='".$duration."'>" . $value->subject->name ."</option>";
                }
            }
            exit();
        } else {
            echo $res = "<option value='No state'> Subject Not Found </option>";
            die();
        }
    }
	
	public function get_batch_subject_data(Request $request){
		
		
        $course_id = $request->course_id;
		
        $subjects = CourseSubjectRelation::with('subject')->where('course_id', $course_id)->where('is_deleted', '0')->get();
        //print_r($subjects->toArray()); die;
		
		$res = "";
        if (!empty($subjects)) {                         
            foreach ($subjects as $key => $value) {
                if(!empty($value->subject->name) && !empty($value->subject->name)){
					
					$res .= "<div class='col-md-6 mb-2'><div class='input-group'><select class='form-control' name='course[subject_id][]'><option value='".$value->subject->id."'>".$value->subject->name."</option></select></div></div><div class='col-md-6 col-12 mb-2'><div class='input-group'><input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' step=0.01></div></div>";
                }
            }
			echo $res;
            exit();
        } else {
            echo $res = "<option value='No state'> Subject Not Found </option>";
            die();
        }
    }
	
	
	public function view($batch_id)
    {
		if(Auth::user()->user_details->degination=='CENTER HEAD'){
			die('No Permission');
		}
		$send_array = array();
		// $batch = Batch::with('course','batch_relations','batch_relations.user','batch_relations.subject')->where('id',$batch_id)->orderBy('batch_relations.subject_status','asc')->first();
		
		$batch = Batch::with([
			'course',
			'batch_relations' => function ($query) {
				$query->orderBy('subject_status', 'desc');
			},
			'batch_relations.user',
			'batch_relations.subject'
		])
		->where('id', $batch_id)
		->first();


		if(!empty($batch)){
			$i = 0;
			$course_id = $batch->course->id;
			foreach ($batch->batch_relations as $batchDetail){
				if(!empty($batchDetail)){
					
					if($batchDetail->is_deleted=='0'){
						$subject_name = $batchDetail->subject->name;
						$subject_id = $batchDetail->subject_id;
						
						
						$topic = DB::table('topic')
									->select(DB::raw("SUM(topic.duration)/60 AS duration"))
									->where('subject_id',$subject_id)->where('course_id',$course_id)->where('status',1)
									->first();
						
						
						$i++;
						$row = array();
						$row['s_no'] = $i;
						$row['batch_relation_id'] = $batchDetail->id;
						$row['faculty_name'] = isset($batchDetail->user->name)?$batchDetail->user->name:'';
						$row['subject_name'] = $subject_name;
						$row['chapter_name'] = '';
						$row['topic_name'] = '';
						$row['duration'] = round($topic->duration,0);
						$row['schedule_date'] = '';
						$row['spent_hour'] = '';
						$row['status'] = '';
						$row['remark'] = '';
						$row['subject_status'] = $batchDetail->subject_status;
						$row['complete_date'] = $batchDetail->complete_date;
						$row['batch_id'] = $batchDetail->batch_id;
						$row['subject_id'] = $subject_id;
						$row['course_id'] = $course_id;
						$send_array[] = $row;
					}
				}
				
			}			
		}
		// echo "<pre>"; print_r($batch); die;
		return view('admin.batch.view', compact('batch','send_array'));
	}
	
	public function batch_subject_status_update(Request $request)
    {
		$batch_relation_id = $request->batch_relation_id;
		$subject_status = $request->status;
		if($subject_status=='Complete'){
			$complete_date = $request->complete_date;
			if(empty($complete_date)){
				return response(['status' => false, 'message' => 'Date is required'], 200);
			}
		}
		else{
			$complete_date = NULL;
		}
		if(empty($batch_relation_id)){
			return response(['status' => false, 'message' => 'Something Went Wrong.'], 200);
		}
		
		
		$check_b_re = DB::table('batchrelations')
				->where('id',$batch_relation_id)
				->first();
		if(!empty($check_b_re)){
			$update=DB::table('batchrelations')->where('id', $batch_relation_id)->update([ 'subject_status' => $subject_status,'complete_date'=>$complete_date]);
			if($update) {				
				return response(['status' => true, 'message' => 'Update successfully.'], 200);
			} else {
				return response(['status' => false, 'message' => 'No any change.'], 200);
			}
		}
		
    }
	
	public function import_chapter(Request $request){
		
		if(!in_array(Auth::user()->id,[5126])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }

		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validator = Validator::make($request->all(), [
			   'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
			if ($validator->fails()){
				$messages = $validator->errors(); 
				return response(['status' => false, 'message' => $messages->first('import_file')], 200);
			}
		}
			
		
		$subjects_name = array();
        $course_id = $request->course_id;
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->where('is_deleted', '0')
                    ->get();
		if(!empty($relation_subjects)){
			foreach ($relation_subjects as $details) 
			{
			    $subjects_name[] = $details->subject_id;
			}
		}

        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) {
				if (empty($value)) {
					continue;
				}
				//echo '<pre>'; print_r($value[0]);die;
				$newArray = [
					'subject_name' => $value[0],
					'chapter_name' => $value[1],
					'topic_name' => $value[2],
					'duration' => $value[3]
				];
				array_push($result, $newArray);
				
				if(!in_array($value[0],$subjects_name)){
					$conditions = false;
					$errors_row .= "\n Subject not exists rows: ".($key+1).", ";
				}

				if(mb_strlen($value[1])>250){
					$conditions = false;
					$errors_row .= "\n Chapter Name Big at:".($key+1).", ";
				}

				if(mb_strlen(trim($value[2]))>250){
					$conditions = false;
					$errors_row .= "\n Topic Name Big at:".($key+1).", ".$value[2];
				}

				/*if($value[3]>600){
					$conditions = false;
					$errors_row .= "Topic Duration issue at (mx:600 minute):".($key+1).", ";
				}*/
				
			}
			$errors_row = rtrim($errors_row,", ");  
			// echo $conditions; die;
			
			if(!$conditions){
				return response(['status' => false, 'message' => $errors_row], 200);
			}
		}else{
			return response(['status' => false, 'message' => 'Something went wrong !'], 200);
		}

		//die('sss');
		
		$not_inserted_row_msg = "";
		
		foreach ($import[0] as $key => $value) {
			$value[0]=trim($value[0]);
			$value[1]=trim($value[1]);
			$value[2]=trim($value[2]);
			$value[3]=trim($value[3]);

			$subject_id = $value[0];
			
			$chk_chapter = Chapter::where('course_id', $course_id)->where('subject_id', $subject_id)->where('name', $value[1])->first();
			if(!empty($chk_chapter)){
				$chapter_id = $chk_chapter->id;
			}else{
				$chapter = Chapter::create([
					'course_id'  => $course_id,
					'subject_id' => $subject_id,
					'name'       => $value[1],
					'status'     => 1,
				]);
				$chapter_id = $chapter->id;
			}
			
			$chk_topic = Topic::where('course_id', $course_id)->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)->where('name', $value[2])->first();
			if(!empty($chk_topic)){
				$topic_id = $chk_topic->id;
			}else{
				$topic = Topic::create([
					'course_id'  => $course_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'name'       => $value[2],
					'duration'   => $value[3],
					'status'     => 1,
				]);
				
			}
		}    
 
		return response(['status' => true, 'message' => 'Excel Data Imported successfully.'], 200);
	}

	public function import_chapter_old(Request $request){
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validator = Validator::make($request->all(), [
			   'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
			if ($validator->fails()){
				$messages = $validator->errors(); 
				return response(['status' => false, 'message' => $messages->first('import_file')], 200);
			}
		}
			
		
		$subjects_name = array();
        $course_id = $request->course_id;
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->get();
		if(!empty($relation_subjects)){
			foreach ($relation_subjects as $details) 
			{
				if(!empty($details)){
					$subjects = DB::table('subject')
						->select('*')
						->where('id', $details->subject_id)
						->where('status', 1)
						->first();
					if(!empty($subjects)){
						$subjects_name[] = $subjects->name;
					}
				}
			}
		}			
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) {
				if (empty($value)) {
					continue;
				}
				//echo '<pre>'; print_r($value[0]);die;
				$newArray = [
					'subject_name' => $value[0],
					'chapter_name' => $value[1],
					'topic_name' => $value[2],
					'duration' => $value[3]
				];
				array_push($result, $newArray);
				
				if(!in_array($value[0],$subjects_name)){
					$conditions = false;
					$errors_row .= ($key+1).", ";
				}
				
			}
			$errors_row = rtrim($errors_row,", ");  
			// echo $conditions; die;
			
			if(!$conditions){
				return response(['status' => false, 'message' => 'Subject not exists rows "'.$errors_row.'" !'], 200);
			}
		}else{
			return response(['status' => false, 'message' => 'Something went wrong !'], 200);
		}
		
		$not_inserted_row_msg = "";
		foreach ($import[0] as $key => $value) {
			
			$chk_subject = Subject::where('name', $value[0])->first();
			$subject_id = $chk_subject->id;
			
			$chk_chapter = Chapter::where('course_id', $course_id)->where('subject_id', $subject_id)->where('name', $value[1])->first();
			if(!empty($chk_chapter)){
				$chapter_id = $chk_chapter->id;
			}else{
				$chapter = Chapter::create([
					'course_id' => $course_id,
					'subject_id' => $subject_id,
					'name' => $value[1],
					'status' => 1,
				]);
				$chapter_id = $chapter->id;
			}
			
			/* $chk_topic = Topic::where('course_id', $course_id)->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)->where('name', $value[2])->first();
			if(!empty($chk_topic)){
				$topic_id = $chk_topic->id;
			}else{
				$topic = Topic::create([
					'course_id' => $course_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'name' => $value[2],
					'duration' => $value[3],
					'status' => 1,
				]);
				
			} */

		}    
 
		return response(['status' => true, 'message' => 'Excel Data Imported successfully.'], 200);
	}
	
	
	
	//Subject Topic PDF
	public function batch_subject_topic_pdf() {
		$logged_id       = Auth::user()->id;
		$subject_id      = Input::get('subject_id');
		$course_id      = Input::get('course_id');
		$subject_name      = Input::get('subject_name');
		$batch_name      = Input::get('batch_name');
		$course_name      = Input::get('course_name');
		$start_date      = Input::get('start_date');
		$batch_id      = Input::get('batch_id');
		
      
						  
		$get_topic = DB::table('chapter')
						->select('chapter.*','topic.name as topic_name','topic.duration as topic_duration','timetable_topic.created_at as tdate')
						->leftjoin('topic','topic.chapter_id','chapter.id')
						->leftJoin('timetable_topic', function ($q) use ($batch_id, $subject_id) {
							$q->on('timetable_topic.topic_id', '=', 'topic.id')
							  ->where('timetable_topic.batch_id', '=', $batch_id)
							  ->where('timetable_topic.subject_id', '=', $subject_id);
						})
						->where('chapter.course_id',$course_id)
						->where('chapter.subject_id',$subject_id)
						->get();	
	   
		
		return view('admin.batch.pdf_html', compact('get_topic','subject_id','subject_name','batch_name','course_name','start_date'));

		require_once base_path('vendor/tcpdf/Pdf.php'); 
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Faculty Report');
        $pdf->custom_title = 'Faculty Report';
        //$pdf->SetSubject('Report generated using Codeigniter and TCPDF');
        //$pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('freesans', '', 12);

        // ---------------------------------------------------------
        // $title = 'Studio Report';
		$html = view('admin.batch.pdf_html', compact('get_topic','subject_id','subject_name','batch_name','course_name','start_date'))->render();
        //echo $html; //exit();
        //Generate HTML table data from MySQL - end
        // add a page
        $pdf->AddPage();
		// echo $html; die;
		// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
 
        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        $pdf->Output('subject_topic_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
   }

}
