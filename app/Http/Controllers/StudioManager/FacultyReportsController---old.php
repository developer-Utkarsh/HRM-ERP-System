<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Timetable;
use Input;
use Auth;
use App\User;
use DB;
use DateTime;
use Illuminate\Support\Facades\Validator;

class FacultyReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$logged_id       = Auth::user()->id;
        $branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		
		if(empty($branch_location) && $logged_id==7074){
			$branch_location = "jaipur";
		}
		
		$whereCond  = ' 1=1';
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
			else{
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
			}
        }
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
		if(!empty($branch_id)){
			$whereCond .= ' AND studios.branch_id = "'.$branch_id.'"';
		}
		
		if(!empty($branch_location)){
			$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
		}
		
		if(!empty($batch_id)){
			$whereCond .= ' AND batch.id = "'.$batch_id.'"';
		}
		
		if(!empty($faculty_id) && count($faculty_id) > 0){
			//$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
			$whereCond .= " AND timetables.faculty_id IN('".implode("','",$faculty_id)."')";
		}

		$search    = Input::get('search');
		$get_faculty=array();
		if(!empty($search)){
			$get_faculty = DB::table('timetables')
			   ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name')
			  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
			  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
			  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
			  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
			  // ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
			  ->whereRaw($whereCond);
			
				if(empty($batch_id)){
					$get_faculty->where('timetables.time_table_parent_id', '0');
				}
				
				if($logged_id==5126){
					$get_faculty->where('users_faculty.department_type', '!=', 27);
				}
				 //$get_faculty->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')"); //commented by PC
	            $get_faculty->where('users_faculty.status', '1')
				->where('users_faculty.is_deleted', '0');
				  
			if(!empty($status) && $status == 'cancel'){
				$get_faculty->where('timetables.is_cancel',1);
				// $get_faculty->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
				$get_faculty->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
			}else{
			  	$get_faculty->whereRaw("timetables.is_deleted='0'");
			 }
							  
			// ->orderBy('timetables.id','desc')
			$get_faculty = $get_faculty->orderBy('users_faculty.name','asc')
				->groupBy('timetables.faculty_id')
			// ->groupBy('timetables.assistant_id','timetables.faculty_id')
			->get();				  
	        //echo "<pre>"; print_r($get_faculty); die;
	    }
        return view('studiomanager.faculty_reports.index', compact('get_faculty','selectFromDate','selectToDate','status','branch_id','batch_id'));
    }

   public function download_pdf() {
		$logged_id       = Auth::user()->id;
		$branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$status      = Input::get('status');
		$batch_id    = Input::get('batch_id');
		
		$whereCond  = ' 1=1';
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
			else{
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
			}
        }
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
		if(!empty($branch_id)){
			$whereCond .= ' AND studios.branch_id = "'.$branch_id.'"';
		}
		
		
		if(!empty($branch_location)){
			$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
		}
		
		if(!empty($faculty_id)){
			//$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
			$whereCond .= " AND timetables.faculty_id IN(".$faculty_id.")";
		}
		
		if(!empty($batch_id)){
			$whereCond .= ' AND batch.id = "'.$batch_id.'"';
		}
						  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.id','timetables.faculty_id','timetables.assistant_id','users_faculty.name as faculty_name')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
						  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
						  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
						  // ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
						  ->whereRaw($whereCond);
						  if(empty($batch_id)){
							$get_faculty->where('timetables.time_table_parent_id', '0');
						  }
						if($logged_id==5126){
							$get_faculty->where('users_faculty.department_type', '!=', 27);
						}
		$get_faculty = $get_faculty->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')")
						  ->where('users_faculty.status', '1')
						  ->where('users_faculty.is_deleted', '0');
						  if(!empty($status) && $status == 'cancel'){
							$get_faculty->where('timetables.is_cancel', '1');
						  }
		$get_faculty = $get_faculty->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  ->get();	
	    
		return view('studiomanager.faculty_reports.pdf_html', compact('get_faculty','selectFromDate','selectToDate','status','branch_id','batch_id'));

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
		$html = view('studiomanager.faculty_reports.pdf_html', compact('get_faculty','selectFromDate','selectToDate'))->render();
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
        $pdf->Output('faculty_report_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
   }
	
	public function subjects()
    {
        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		
		$search = Input::get('search');
        $branch_id = Input::get('branch_id');

        // $employees = User::with(['user_details.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');
        $employees = User::with(['user_branches','faculty_subjects'])->where('role_id','2')->where('is_deleted', '0')->orderBy('id','desc');// dk
		$employees->where('register_id','!=',NUll);
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }

        if(!empty($branch_id)) {
			
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if($logged_role_id == 21){
            $employees = $employees->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
        }
		
        $employees = $employees->get();
        
		// echo '<pre>'; print_r($employees); die;
        return view('studiomanager.faculty_reports.subjects', compact('employees'));
    }
	
	public function editStartClass(Request $request){
		if(!empty($request->tt_id)){
			$timetable_id = $request->tt_id;
			$s_time = ''; $t_time = '';$t_topic_name = '';$remark = '';$early_delay_reason = '';$res = '';
			$html = "";
			$subject_name = "";
			$get_timetable = DB::table('timetables')
						->select('timetables.id','timetables.batch_id','timetables.course_id','timetables.subject_id','timetables.chapter_id','subject.name as subject_name')
						->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
						->where('timetables.is_deleted', '0')
						->whereRaw("(timetables.id = $timetable_id or timetables.time_table_parent_id='$timetable_id')")
						->groupBy('timetables.course_id')
						->groupBy('timetables.subject_id')
						->get();
			// echo count($get_timetable); die;
			if(!empty($get_timetable)){
				foreach($get_timetable as $tval){
					$ttid = $tval->id;
					$subject_name = $tval->subject_name;
					$start_class_check = DB::table('start_classes')->where('timetable_id', $ttid)->first();
					$topic_name = "";
					if(!empty($start_class_check)){
						$topic_name = $start_class_check->topic_name;
					}
					$chapter_id = $tval->chapter_id;
					$html .= "<div class='row col-md-12'>";
					$html .= "<div class='col-md-6 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Topic Name</span>";
					$html .="<select class='form-control chapter_data select-multiple1' name='chapter_id[$ttid]' style= 'width:100%;'>";
					$html .="<option value=''>Select Topic </option>";
					
					$course_id = $tval->course_id;
					$subject_id = $tval->subject_id;
					$get_chapter = DB::table('chapter')->select('*')->where('course_id', $course_id)->where('subject_id', $subject_id)->where('status', 1)->where('is_deleted','0')->get();
					if(!empty($get_chapter)){
						foreach($get_chapter as $chapter_data){
							$selected = "";
							if($chapter_id==$chapter_data->id){
								$selected = "selected";
							}
							$html .="<option value='$chapter_data->id' data-chname='$chapter_data->name' $selected>$chapter_data->name </option>";
						}
					}
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";
					$html .="<div class='col-md-6 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Sub Topic Name</span>";
					$html .="<input type='text' class='form-control topic_name' placeholder='Sub Topic Name' name='topic_name[$ttid]' autocomplete='off' value='$topic_name' >";
					$html .="</div>";
					$html .="</div>";
					$html .="</div>";
					
				}
				
				// echo "<pre>"; print_r($get_chapter); die;
			}
			else{
				return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
			}
			$start_class_data = DB::table('start_classes')->where('timetable_id', $request->tt_id)->first();
			if(!empty($start_class_data)){
				if(!empty($start_class_data->start_time)){
					$s_time = date("h:i A", strtotime($start_class_data->start_time));
				}
				if(!empty($start_class_data->end_time)){
					$t_time = date("h:i A", strtotime($start_class_data->end_time));
				}
				if(!empty($start_class_data->topic_name)){
					$t_topic_name = $start_class_data->topic_name;
				}
				if(!empty($start_class_data->remark)){
					$remark = $start_class_data->remark;
				}
				if(!empty($start_class_data->early_delay_reason)){
					$early_delay_reason = $start_class_data->early_delay_reason;
				}
				if(!empty($start_class_data->assistant_id)){
					
					$user_branch_id = Auth::user()->user_branches[0]->branch_id;
									
					$userdeatils = \App\Userbranches::with([
						'user' => function($q){
							$q->where('role_id', '3')->where('status', '1')->where('is_deleted', '0');
						}
					]);

					$userdeatils->WhereHas('user', function ($q) {
									 $q->where('role_id', '3')->where('status', '1')->where('is_deleted', '0');
								});

					$userdeatils = $userdeatils->where('branch_id', $user_branch_id)->get();
					
					if (!empty($userdeatils)) {                         
							$res .= "<option value=''> Select Assistant </option>";
							foreach ($userdeatils as $key => $value) {
								if(!empty($value->user->id) && !empty($value->user->name)){
									if($value->user->id == $start_class_data->assistant_id){
										$res .= "<option value='". $value->user->id ."' selected='selected'>" . $value->user->name ."</option>";
									}else{
									   $res .= "<option value='". $value->user->id ."'>" . $value->user->name ."</option>"; 
									}
							   }
						   }
					 } 
					 else {
						$res .= "<option value=''> Assiatant Not Found </option>";
					 }
		
				}
			}
			else{
				$timetable_detail = DB::table('timetables')->select('from_time','to_time')->where('id', $request->tt_id)->first();
				$s_time = date("h:i A", strtotime($timetable_detail->from_time));
				$t_time = date("h:i A", strtotime($timetable_detail->to_time));
			}
			
			$intime = new DateTime($s_time);
			$outtime = new DateTime($t_time);
			$interval = $intime->diff($outtime);
			$total_spent_time = $interval->format('%H:%I');
				
			return response(['status' => true, 'start_time' => $s_time, 'end_time' => $t_time, 'topic_name' => $t_topic_name, 'remark' => $remark,'early_delay_reason' => $early_delay_reason, 'res' => $res,'total_spent_time'=>$total_spent_time,'html'=>$html,'subject_name'=>$subject_name], 200);
		}
		else{
			 return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
		}
	}  
	
	public function updateStartClass(Request $request){ 
	    $rules = [
            'timetable_id' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data, 200);
        }

		$logged_id       = Auth::user()->id;
		$asst_id = '';
		if(!empty($request->assistant_id)){
			$asst_id = $request->assistant_id;
		}
		$timetable_id = $request->timetable_id;
		$start_time = strtotime($request->start_time);
		$end_time = strtotime($request->end_time);

		if($start_time>=$end_time){
			return response(['status' => false, 'message' => 'Start time must be less then to End Time.'], 200);
		}

	    $timetable_detail = DB::table('timetables')->select('cdate','from_time')->where('id', $timetable_id)->first();
	    if(strtotime($timetable_detail->from_time) > $start_time){
          return response(['status' => false, 'message' => 'Start time must be greater then or equal of Schedule Time'], 200);
	    }

		
		$s_date = new DateTime($request->start_time);
		$e_date = new DateTime($request->end_time);
		$interval = $s_date->diff($e_date);
		$total_hours = $interval->format('%H');

		if($total_hours > 4){
          return response(['status' => false, 'message' => 'Class max spent time is 4 hours. If class more then 4 hours please contact to your head.'], 200);
		}

		if(empty($request->chapter_id)){
          return response(['status' => false, 'message' => 'Please contact to timetable manager.'], 200);
		}

		$chapter_ids = $request->chapter_id;
		$topic_name = $request->topic_name;
		$empty = false;
		foreach($chapter_ids as $timetableid => $chapter_id){
			if(empty($topic_name[$timetableid])){
			  $empty = true;
			}
		}

		if($empty){
			return response(['status' => false, 'message' => 'Sub Topic is requird.'], 200);
		}

		$chapter_ids = $request->chapter_id;
		$topic_name = $request->topic_name;
		foreach($chapter_ids as $timetableid => $chapter_id){
			$get_prev_data = DB::table('start_classes')->where('timetable_id', $timetableid)->first();
			
			if(!empty($chapter_id)){
				DB::table('timetables')->where('id', $timetableid)->update([
					'chapter_id'   => $chapter_id
				]);
			}

			if(empty($request->delay_type)){
				$request->delay_type="";
			}

			if(empty($request->early_delay_reason)){
				$request->early_delay_reason="";
			}
			
			if(empty($get_prev_data)){
				$start_res = DB::table('start_classes')->insertGetId([
					'timetable_id' => $timetableid,
					'start_time'   => date("H:i", strtotime($request->start_time)),
					'end_time'     => date("H:i", strtotime($request->end_time)),
					'sc_date'      => $timetable_detail->cdate,
					'status'       => 'End Class',
					'topic_name'   => $topic_name[$timetableid],
					'remark'   	   => $request->remark,
					'early_delay_reason'=> $request->early_delay_reason,
					'delay_type'=> $request->delay_type,
					'assistant_id' => $asst_id,
					'created_at'   => date('Y-m-d H:i:s'),
					'updated_at'   => date('Y-m-d H:i:s')
				]);
			}else{
				$start_res = DB::table('start_classes')->where('timetable_id', $timetableid)->update([
					'start_time'   => date("H:i", strtotime($request->start_time)),
					'end_time'     => date("H:i", strtotime($request->end_time)),
					'topic_name'   => $topic_name[$timetableid],
					'remark'       => $request->remark,
					'early_delay_reason'=> $request->early_delay_reason,
					'delay_type'=> $request->delay_type,
					'assistant_id' => $asst_id,
					'updated_at'   => date('Y-m-d H:i:s')	
				]);
			}

			if($start_res){
				DB::table('start_classes_history')->insertGetId([
					'timetable_id' => $timetableid,
					'start_time'   => date("H:i", strtotime($request->start_time)),
					'end_time'     => date("H:i", strtotime($request->end_time)),
					'topic_name'   => $topic_name[$timetableid].'-'.$request->remark.'-'.$request->early_delay_reason.'-'.$request->delay_type,
					'assistant_id' => $asst_id,
					'log_id'       => $logged_id,
					'created_at'   => date('Y-m-d H:i:s'),
					'updated_at'   => date('Y-m-d H:i:s')
				]);
			}
		}

		$duration  = "00 : 00 Hours"; 
		$spd_time = '';
		$get_update_data = DB::table('start_classes')->where('timetable_id', $timetable_id)->first();
		if(!empty($get_update_data)){
			$duration  = "00 : 00 Hours"; 
			
			$first_date = new DateTime($get_update_data->start_time);
			$second_date = new DateTime($get_update_data->end_time);
			$interval = $first_date->diff($second_date);
			$duration = $interval->format('%H : %I Hours');
		}
		
		$spent_id = $request->spent_id;
		DB::table('timetables')->where('id', $timetable_id)->update(['is_publish' => '1', 'is_cancel' => '0', 'is_deleted' => '0']);

		if($request->delay_type=='Due to Faculty'){
			$this->whatsappForDelay($timetableid);
		}

		return response(['status' => true, 'message' => 'Successfully Update', 'spd_time' => $duration, 'spent_id' => $spent_id], 200);
	}
	
	public function updateCancelClass(Request $request){   
		 $logged_id       = Auth::user()->id;
		 if(!empty($request->timetable_id)){
			 if(!empty($request->start_time)){
				if(!empty($request->end_time)){
					$asst_id = ''; $topic_nam = '';
					if(!empty($request->assistant_id)){
						$asst_id = $request->assistant_id;
					}
					if(!empty($request->topic_name)){
						$topic_nam = $request->topic_name;
					}
					
					$start_time = strtotime($request->start_time);
					$end_time = strtotime($request->end_time);
					if($end_time >= $start_time){
						$timetable_detail = DB::table('timetables')->select('cdate','from_time')->where('id', $request->timetable_id)->first();
						if($start_time >= strtotime($timetable_detail->from_time)){
							$s_date = new DateTime($request->start_time);
							$e_date = new DateTime($request->end_time);
							$interval = $s_date->diff($e_date);
							$total_hours = $interval->format('%H');
							if($total_hours < 4){
								$get_prev_data = DB::table('start_classes')->where('timetable_id', $request->timetable_id)->first();
								if(!empty($get_prev_data)){
								/* if(!empty($get_prev_data->start_time) && !empty($get_prev_data->end_time) && ($get_prev_data->start_time != $request->start_time || $get_prev_data->end_time != $request->end_time)){ */
									
									$start_res = DB::table('start_classes')->where('timetable_id', $request->timetable_id)->update([
														'start_time'   => date("H:i", strtotime($request->start_time)),
														'end_time'     => date("H:i", strtotime($request->end_time)),
														'topic_name'   => $topic_nam,
														'remark'   => $request->remark,
														'early_delay_reason'   => $request->early_delay_reason,
														'assistant_id' => $asst_id,
												 ]);
									if($start_res){
										DB::table('start_classes_history')->insertGetId([
												'timetable_id' => $request->timetable_id,
												'start_time'   => $get_prev_data->start_time,
												'end_time'     => $get_prev_data->end_time,
												'topic_name'   => $topic_nam.'-'.$request->remark.'-'.$request->early_delay_reason,
												'assistant_id' => $asst_id,
												'log_id'       => $logged_id
										]);
									}
									
								}
								else if(empty($get_prev_data->start_time) && empty($get_prev_data->end_time)){
									$start_res =DB::table('start_classes')->insertGetId([
											'timetable_id' => $request->timetable_id,
											'start_time'   => date("H:i", strtotime($request->start_time)),
											'end_time'     => date("H:i", strtotime($request->end_time)),
											'sc_date'      => $timetable_detail->cdate,
											'status'       => 'End Class',
											'topic_name'   => $topic_nam,
											'remark'   => $request->remark,
											'early_delay_reason'   => $request->early_delay_reason,
											'assistant_id' => $asst_id,
											'created_at'   => date('Y-m-d H:i:s'),
											'updated_at'   => date('Y-m-d H:i:s')
									 ]);
									 
									if($start_res){
										DB::table('start_classes_history')->insertGetId([
												'timetable_id' => $request->timetable_id,
												'start_time'   => $get_prev_data->start_time,
												'end_time'     => $get_prev_data->end_time,
												'topic_name'   => $topic_nam.'-'.$request->remark.'-'.$request->early_delay_reason,
												'assistant_id' => $asst_id,
												'log_id'       => $logged_id
										]);
									}
								}
								else{
									$start_res = DB::table('start_classes')->where('timetable_id', $request->timetable_id)->update([
											'start_time'   => $request->start_time,
											'end_time'     => $request->end_time,
											'topic_name'   => $topic_nam,
											'remark'   => $request->remark,
											'early_delay_reason'   => $request->early_delay_reason,
											'assistant_id' => $asst_id,
									 ]);
									 
									if($start_res){
										DB::table('start_classes_history')->insertGetId([
												'timetable_id' => $request->timetable_id,
												'start_time'   => $get_prev_data->start_time,
												'end_time'     => $get_prev_data->end_time,
												'topic_name'   => $topic_nam.'-'.$request->remark.'-'.$request->early_delay_reason,
												'assistant_id' => $asst_id,
												'log_id'       => $logged_id
										]);
									}
								}
								
								// $spd_time = '';
								// $get_update_data = DB::table('start_classes')->where('timetable_id', $request->timetable_id)->first();
								// if(!empty($get_update_data)){
									// $duration  = "00 : 00 Hours"; 
									
									// $first_date = new DateTime($get_update_data->start_time);
									// $second_date = new DateTime($get_update_data->end_time);
									// $interval = $first_date->diff($second_date);
									// $duration = $interval->format('%H : %I Hours');
								// }
								$duration = 'cancel';
								$spent_id = $request->spent_id;
								
								DB::table('timetables')->where('id', $request->timetable_id)->update(['is_publish' => '0', 'is_cancel' => '1', 'is_deleted' => '2']);
								
								DB::table('timetables')->where('time_table_parent_id', $request->timetable_id)->update(['is_publish' => '0', 'is_cancel' => '1', 'is_deleted' => '2']);
								
								return response(['status' => true, 'message' => 'Successfully Update', 'spd_time' => $duration, 'spent_id' => $spent_id], 200);
							}
							else{
								return response(['status' => false, 'message' => 'Class max spent time is 4 hours. If class more then 4 hours please contact to your head.'], 200);
							}
						}
						else{
							return response(['status' => false, 'message' => 'Start time must be greater then or equal of Schedule Time'], 200);
						}
					}
					else{
						return response(['status' => false, 'message' => 'Start time must be less then to End Time.'], 200);
					}
				}
				else{
				  return response(['status' => false, 'message' => 'End Time Required'], 200);
				}
			 }
			 else{
				 return response(['status' => false, 'message' => 'Start Time Required'], 200);
			 }
		 }
		 else{
			 return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
		 }
	}

	public function timetable_history_reports()
    {
    	
        $branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('fdate');
        $branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		$assistant_id      = Input::get('assistant_id');
		
		$whereCond  = ' 1=1';
		$logged_id="";
		
		
		
		if(empty($selectFromDate)){
			$selectFromDate = date('Y-m-d');
			$selectToDate = date('Y-m-d');
		}
		if(!empty($selectFromDate) && !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectFromDate .'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
		}
		
		if(!empty($branch_id)){
			$whereCond .= ' AND studios.branch_id = "'.$branch_id.'"';
		}
		
		if(!empty($branch_location)){
			$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
		}
		
		if(!empty($batch_id)){
			$whereCond .= ' AND batch.id = "'.$batch_id.'"';
		}
		
		if(!empty($assistant_id)){
			$whereCond .= ' AND timetables.assistant_id = "'.$assistant_id.'"';
		}
		
		if(!empty(Auth::user()->id)){
			$logged_id=Auth::user()->id;

			if(!empty($faculty_id) && is_array($faculty_id) > 0){
				$whereCond .= " AND timetables.faculty_id IN('".implode("','",$faculty_id)."')";
			}
			else if(!empty($faculty_id)){
				return redirect()->route('admin.faculty-reports','faculty_id[]='.$faculty_id);
			}
		}else{
			$logged_id=$faculty_id;
			if(!empty($faculty_id)){
				$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
			}
		}
		
		// print_r($whereCond);

$get_faculty = array();	
if(!empty($selectFromDate) && !empty($branch_location)){
		$get_faculty = DB::table('timetables')
					  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','subject.name as subject_name','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile','users_faculty.name as faculty_name')
					  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
					  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
					  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
					  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
					  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
					  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
					  ->whereRaw($whereCond)
					  ->where('timetables.time_table_parent_id', '0')
					  ->whereRaw("(timetables.is_deleted='0')")
					->where('timetables.is_publish', '1');
						   
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')->get();				  
        // echo "<pre>"; print_r(count($get_faculty)); die;
		
}
		
		
		$userLocationBranches = array();
        
        // add pc
		$get_braches = DB::table('userbranches')->where(['user_id'=>$logged_id])->first();
		$branch_id = $get_braches->branch_id;
		$brach_data = DB::table('branches')->where(['id'=>$branch_id])->first();
		if(!empty($brach_data)){
			$branch_location= $brach_data->branch_location;
		}
		//end pc code

		$get_location_branches = DB::table('branches')->select('id')->where(['branch_location'=>$branch_location])->get();
		foreach($get_location_branches as $userLocationBranchesVal){
			$userLocationBranches[] = $userLocationBranchesVal->id;
		}
		
		$get_assistant = DB::table('users')
							->select('users.*')
							->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
							->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')
							->where(['users.status'=>1,'users.is_deleted'=>'0','users.role_id'=>3]);
		if(!empty($userLocationBranches)){
			$get_assistant->whereIn('branches.id', $userLocationBranches);
		}					
		$get_assistant = $get_assistant->get();
		
		
		return view('studiomanager.faculty_reports.timetable_history_reports', compact('get_faculty','selectFromDate','selectToDate','status','branch_id','get_assistant'));
        
    }



}
