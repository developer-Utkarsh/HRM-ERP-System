<?php
namespace App\Http\Controllers\Admin;
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
use Excel;
use App\Exports\FacultyMonthlyHoursExport;
use DateTime;
use Illuminate\Support\Facades\Validator;


class FacultyReportsController extends Controller
{
	
  public function delayReason(Request $request,$id){
  	if(!empty($id)){
  		if(!empty($request->delay_faculty_reason)){
  		  DB::table('start_classes')
            ->where('timetable_id',$id)
            ->where('delay_type','Due to Faculty')
            ->where('sc_date','>=',date("Y-m-d",strtotime("-10 days")))
            ->update(['delay_faculty_reason'=>$request->delay_faculty_reason]);
        }

        $timetable=DB::table('timetables')
          ->select('timetables.id','timetables.faculty_id','timetables.cdate','timetables.from_time','timetables.to_time',
          	'batch.name as batch_name','timetables.updated_at','users.name as faculty_name',
          	'users.mobile','start_classes.start_time','start_classes.end_time',
          	'start_classes.delay_type','start_classes.id as start_id','start_classes.delay_faculty_reason')
         ->leftJoin('batch','batch.id','timetables.batch_id')
         ->leftJoin('users','users.id','timetables.faculty_id')
         ->leftJoin('start_classes','start_classes.timetable_id','timetables.id')
         ->where('timetables.id',$id)->first();

        return view('admin.faculty_reports.faculty-delay-reason',compact('timetable'));
    }
  }

	public function driver_timetable(){ 
        $driver_id      = Input::get('driver_id');
        $branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $branch_location   = Input::get('branch_location');
		
		$whereCond  = ' 1=1';
		$faculties_id = "0";
		
		if(empty($selectFromDate)){
			if(!empty(Auth::user()->id)){
				$selectFromDate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$selectFromDate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$selectFromDate = date('Y-m-d');
				}
			}
		}
		
		if(!empty($driver_id) && !empty($selectFromDate)){
			// $brnc_id   = implode(",", $branch_id); 
			// $whereCond = " id IN ($brnc_id) ";
			$driver_detail = DB::table('driver_faculties')
						  ->select('*')
						  ->whereRaw("driver_id = $driver_id AND DATE(assign_date) = '$selectFromDate'")
						  ->get();
			if(count($driver_detail)>0){
			$faculties_id = "";
				foreach($driver_detail as $driver_data){
					$faculties_id .= $driver_data->faculty_id.",";
				}
			}
			$faculties_id = rtrim($faculties_id,',');
        }
		else{
			echo "<h1>Something went wrong</h1>"; die;
		}
		
		$whereCond = " timetables.faculty_id IN ($faculties_id) ";
		
		if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'"';
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
			$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
		}
						  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
						  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
						  // ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')")
						  ->where('timetables.is_publish', '1')
						  // ->orderBy('timetables.id','desc')
						  ->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  // ->groupBy('timetables.assistant_id','timetables.faculty_id')
						  ->get();				  
        //echo "<pre>"; print_r($get_faculty); die;
		
		$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$drivers->where('register_id','!=',NUll);
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
		$drivers = $drivers->get();
		if(!empty(Auth::user()->id)){
			return view('admin.faculty_reports.without_login_faculty_driver', compact('get_faculty','selectFromDate','selectToDate','drivers'));
		}
		else{
			return view('admin.faculty_reports.without_login_faculty_driver', compact('get_faculty','selectFromDate','selectToDate','drivers'));
		}
        
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {	
      $branch_id      = Input::get('branch_id');
      $faculty_id     = Input::get('faculty_id');		
	    $selectFromDate = Input::get('fdate');
      $selectToDate   = Input::get('tdate');
      $branch_location   = Input::get('branch_location');
	    $batch_id    = Input::get('batch_id');
	    $status      = Input::get('status');
	    $assistant_id      = Input::get('assistant_id');
		
		  $whereCond  = ' 1=1';
		  $logged_id="";
		
		
		
			if(empty($selectFromDate)){
				if(!empty(Auth::user()->id)){
					$selectFromDate = date('Y-m-d');
				}else{
					if(date('H') > 17){ // 17 == 5PM
						$selectFromDate = date('Y-m-d',strtotime('+1 day'));
						$selectToDate   = date('Y-m-d',strtotime('+1 day'));
					}
					else{
						$selectFromDate = date('Y-m-d');
						$selectToDate = date('Y-m-d');
					}
				}
			}

			if(!empty($selectFromDate) && !empty($selectToDate)){
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
			}else if(!empty($selectFromDate)){
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectFromDate .'"';
			}else{
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
					//$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
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
			
			
			
			$studio_arr = array();		  
			$get_faculty = DB::table('timetables')
			  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
			  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
			  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
			  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
			  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
			  ->whereRaw($whereCond)
			  ->where('timetables.time_table_parent_id', '0')
			  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')")
			  ->where('users_faculty.status', '1')
			  ->where('users_faculty.is_deleted', '0');
			  
			  if(!empty($status) && $status == 'cancel'){
				 $get_faculty->where('timetables.is_cancel', '1');
			  }else{
				 $get_faculty->where('timetables.is_publish', '1');
			  }

			  if(empty($faculty_id) && empty($batch_id) && empty($branch_location) && $selectFromDate == date('Y-m-d') && Auth::user()->role_id == 29){ 
					$get_faculty->where("branches.id", 55);
			  }

			  if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
				  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
				  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
				  
				  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
			  }else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
				  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
				  $user_branch_id_multiple = array();
				  foreach(Auth::user()->user_branches as $vvvvvvv){
					$user_branch_id_multiple[] = $vvvvvvv->branch_id;
				  }

				  $studio_arr = Studio::whereIn('branch_id',$user_branch_id_multiple)->get()->pluck('id'); 
				  
				  
				  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
			  }else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
				 $get_faculty->where('timetables.assistant_id', Auth::user()->id);
			  }else if(!empty(Auth::user()->id) &&  Auth::user()->id == 5760){
				  $get_faculty->where('branches.branch_location', 'jaipur');
			  }
			  
			  $get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
			  ->groupBy('timetables.faculty_id')
			  ->get();

			$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
			$drivers->where('register_id','!=',NUll);
			$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
	                $q->where('degination', '=', 'DRIVER');
	            });
			$drivers = $drivers->get();
			
			
			$userLocationBranches = array();
			//$branch_location = Auth::user()->user_branches[0]->branch['branch_location'];
	        
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
			// echo "<pre>"; print_r($userLocationBranches); die;
			$get_assistant = DB::table('users')
				->select('users.*')
				->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
				->leftJoin('branches', 'userbranches.branch_id', '=', 'branches.id')
				->where(['users.status'=>1,'users.is_deleted'=>'0','users.role_id'=>3]);
			if(!empty($userLocationBranches)){
				$get_assistant->whereIn('branches.id', $userLocationBranches);
			}					
			$get_assistant = $get_assistant->get();
			
			
			if(!empty(Auth::user()->id)){
				return view('admin.faculty_reports.index', compact('get_faculty','selectFromDate','selectToDate','drivers','studio_arr','status','branch_id','get_assistant'));
			}
			else{
				//without_login_faculty_report
				return view('admin.faculty_reports.without_login_index', compact('get_faculty','selectFromDate','selectToDate','drivers','get_assistant'));
			}  
    }
	
   public function download_pdf() {
		$branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		$assistant_id      = Input::get('assistant_id');
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
		/* if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'"';
		} */
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		// echo $whereCond; die;
		if(!empty($branch_id)){
			$whereCond .= ' AND studios.branch_id = "'.$branch_id.'"';
		}
		if(!empty($batch_id)){
			$whereCond .= ' AND batch.id = "'.$batch_id.'"';
		}
		
		if(!empty($branch_location)){
			$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
		}
		
		if(!empty($assistant_id)){
			$whereCond .= ' AND timetables.assistant_id = "'.$assistant_id.'"';
		}
		
		if(!empty($faculty_id)){
			//$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
			$whereCond .= " AND timetables.faculty_id IN(".$faculty_id.")";
		}
		$studio_arr = array();		  				  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.id','timetables.faculty_id','timetables.assistant_id','users_faculty.name as faculty_name')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
						  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
						  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
						  // ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')")
						  ->where('users_faculty.status', '1')
						  ->where('users_faculty.is_deleted', '0');
						  
						  if(!empty($status) && $status == 'cancel'){
							// $get_faculty->where('timetables.is_publish', '0');
							$get_faculty->where('timetables.is_cancel', '1');
						  }
						  else{
							$get_faculty->where('timetables.is_publish', '1');
						  }
						  if(empty($faculty_id) && empty($batch_id) && empty($branch_location) &&  empty($selectFromDate) && empty($selectToDate) && Auth::user()->role_id == 29){ 
								$get_faculty->where("branches.id", 55);
						  }
						  if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
						  }
						  else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
						  }
						  else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
							$get_faculty->where('timetables.assistant_id', Auth::user()->id);
						  }
						  
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  ->get();	
	    
		return view('admin.faculty_reports.pdf_html', compact('get_faculty','selectFromDate','selectToDate','studio_arr', 'status'));
		
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
		$html = view('admin.faculty_reports.pdf_html', compact('get_faculty','selectFromDate','selectToDate'))->render();
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
        return view('admin.faculty_reports.subjects', compact('employees'));
    }
	
	public function editStartClass(Request $request){
		if(empty($request->tt_id)){ 
		 return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
		}else{
			$timetable_id = $request->tt_id;
			$s_time       = ''; 
			$t_time       = '';
			$t_topic_name = '';
			$remark       = '';
			$early_delay_reason = '';
			$res = '';
			$html = "";
			$subject_name = "";
			
			$timetable = DB::table('timetables')
				->select('timetables.id','timetables.batch_id','timetables.course_id','timetables.subject_id','timetables.chapter_id','subject.name as subject_name')
				->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
			  ->where('timetables.is_deleted', '0')
			  ->where('timetables.is_publish', '1')
			  ->where('timetables.id', $timetable_id)
			  //->where('timetables.cdate',date("Y-m-d",strtotime("- 2 days")))
				->first();
			if(empty($timetable)){
				return response(['status' => false, 'message' => 'Class not published Or Old date class can not be edited'], 200);
			}else{
				$ttid = $timetable->id;
				$subject_name = $timetable->subject_name;
				$batch_id = $timetable->batch_id;
				$subject_id = $timetable->subject_id;
				$course_id = $timetable->course_id;
				$start_class_check = DB::table('start_classes')->where('timetable_id', $timetable_id)->first();

				$get_chapter = DB::table('chapter')->select('*')->where('course_id',$course_id)->where('subject_id',$subject_id)->where('status', 1)->where('is_deleted','0')->get();

				$whereTopic="(timetable_id='".$timetable_id."' AND A.batch_id='".$batch_id."' AND A.subject_id='".$subject_id."')";
				$whereTopic.=" OR (A.batch_id='".$batch_id."' AND A.subject_id='".$subject_id."' AND (A.status=2 OR A.status=7))";

				$tt_topic= DB::table('timetable_topic as A')->selectRaw("A.topic_id,A.status,topic.chapter_id,chapter.name as chapter_name,topic.name as topic_name")
				->leftJoin("topic",'topic.id','A.topic_id')
				->leftJoin("chapter",'chapter.id','topic.chapter_id')
				->whereRAW($whereTopic)
				->groupBy('A.topic_id')
				->get();

				$html .= "<div class='row col-12'>";

        foreach($tt_topic as $val){
					$html .= "<div class='topic_row row col-md-12'>";
					$html .="<div class='col-md-4 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Topic Name</span>";
					$html .="<select class='form-control select-multiple1' name='chapter_id[]' style= 'width:100%;'>";
				    $html .="<option value='$val->chapter_id'>$val->chapter_name</option>";
				    $html .="</select>";
					$html .="</div>";
					$html .="</div>";
					
					$html .="<div class='col-md-4 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Sub Topic</span>";
					$t_topic_name .=$val->topic_name.",";
					$html .="<select class='form-control select-multiple1' name='topic_id[]' style= 'width:100%;'>";
				    $html .="<option value='$val->topic_id'>$val->topic_name</option>";
				    $html .="</select>";
					$html .="</div>";
					$html .="</div>";

					$html .="<div class='col-md-3 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Status</span>";
					$html .="<select class='form-control' name='status[]' style= 'width:100%;' required>";
					$html .="<option value=''>Select Status </option>";
					$selected=$val->status==1?'selected':'';
					$html .="<option value='1' $selected>Completed</option>";
					$selected=$val->status==2?'selected':'';
					$html .="<option value='2' $selected>Partially Completed</option>";
					$selected=$val->status==7?'selected':'';
					$html .="<option value='7'>Schedule for nextclass</option>";
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";

					$html.="<div class='col-md-1 col-12'>
                          <span class='mt-2 btn-sm btn btn-danger remove_topic'><i class='fa fa-minus' aria-hidden='true'></i></span>
					</div>";
					$html .="</div>";
				}

				$html .="</div>";

        if(count($get_chapter)){
					$html .= "<div class='topic_copy row col-12'>";
					$html .= "<div class='topic_row row col-md-12'>";
					$html .= "<div class='col-md-4 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Topic Name</span>";
					$html .="<select class='form-control chapter_data select-multiple1' name='chapter_id[]' style= 'width:100%;'>";
					$html .="<option value=''>Select Topic </option>";
				    foreach($get_chapter as $chapter_data){
						$html.="<option value='$chapter_data->id' data-chname='$chapter_data->name' data-subject_id='$subject_id' data-batch_id='$batch_id' data-course_id='$course_id'>$chapter_data->name </option>";
					}
					
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";

					$html .="<div class='col-md-4 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Sub Topic</span>";
					$html .="<select class='form-control topic_data' name='topic_id[]' style= 'width:100%;'>";
					$html .="<option value=''>Select Sub Topic </option>";
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";

					$html .="<div class='col-md-3 col-12'>";
					$html .="<div class='form-label-group'>";
					$html .="<span for='first-name-column'>Status</span>";
					$html .="<select class='form-control' name='status[]' style= 'width:100%;' required>";
					$html .="<option value=''>Select Status </option>";
					$html .="<option value='1'>Completed</option>";
					$html .="<option value='2'>Partially Completed</option>";
					$html .="<option value='7'>Schedule for nextclass</option>";
					$html .="</select>";
					$html .="</div>";
					$html .="</div>";

					$html.="<div class='col-md-1 col-12'>
	                          <span class='mt-2 btn-sm btn btn-danger remove_topic d-none'><i class='fa fa-minus' aria-hidden='true'></i></span>
						</div>";

					$html .="</div>";
					$html .="</div>";

					$html .= "<div class='add_topic_row row col-12'></div>";

					$html.= "<div class='row col-12'>
					<div class='col-md-12 text-right'>
					    <span class='btn-sm btn btn-primary addmore_topic'><i class='fa fa-plus' aria-hidden='true'></i></span>
					</div></div>";
				}
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

			$batch_id = $timetable->batch_id;
			$chapters= DB::table('topic')->selectRaw("topic.id,topic.chapter_id,tt.status,chapter.name as chapter_name,topic.name as topic_name,topic.duration")
				->leftJoin("chapter",'chapter.id','topic.chapter_id')
				// ->leftJoin("timetable_topic AS tt",'tt.topic_id','topic.id')
				->leftJoin('timetable_topic AS tt', function($join) use($batch_id)
					{
						$join->on('tt.topic_id', '=', 'topic.id');
						$join->on('tt.batch_id','=',DB::raw($batch_id));						
					})
				->where("topic.course_id",$timetable->course_id)
				->where("topic.subject_id",$timetable->subject_id)
				->where("topic.status",1)
				->where("topic.is_deleted",'0')
				->orderBy("topic.id",'asc')
				->groupBy("topic.id")
				->get();
			$chapters=json_encode($chapters);
				
			return response(['status' => true, 'start_time' => $s_time, 'end_time' => $t_time, 'topic_name' => $t_topic_name, 'remark' => $remark,'early_delay_reason' => $early_delay_reason, 'res' => $res,'total_spent_time'=>$total_spent_time,'html'=>$html,'subject_name'=>$subject_name,'chapters'=>$chapters], 200);
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

	    $timetable_detail = DB::table('timetables')->selectRaw('cdate,from_time,batch_id,subject_id')
	    ->where('id', $timetable_id)->first();
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

		$topic_name = $request->topic_name;
		if(empty($topic_name)){
			return response(['status' => false, 'message' => 'Sub Topic is requird.'], 200);
		}

		if(!empty($request->topic_id) && is_array($request->topic_id)){
			$status      =$request->status;
			$chapter_id  =$request->chapter_id;
			$topic_id    =$request->topic_id;

			$nextClassTopic=[];
			for($i=0;$i<count($topic_id);$i++){
			  if(empty($status[$i]) || empty($topic_id[$i]))
			  { continue;}

			  if($status[$i]==2 || $status[$i]==7){
          $nextClassTopic[]=$topic_id[$i];
			  }

        $tt_topic=DB::table("timetable_topic")
            ->where("timetable_id",$timetable_id)
            ->where("topic_id",$topic_id[$i])->first();
        if(empty($tt_topic)){
       	    $tt_topic=DB::table("timetable_topic")->insert(
       		[
        	 "timetable_id"=>$timetable_id,
        	 "topic_id"    =>$topic_id[$i],
        	 "chapter_id"  =>$chapter_id[$i],
        	 "status"      =>$status[$i]??0,
        	 "batch_id"    =>$timetable_detail->batch_id,
        	 "subject_id"  =>$timetable_detail->subject_id,
        	]);
        }else{
         $tt_topic=DB::table("timetable_topic")
            ->where("timetable_id",$timetable_id)
            ->where("topic_id",$topic_id[$i])
            ->update([
        	 "timetable_id"=>$timetable_id,
        	 "topic_id"    =>$topic_id[$i],
        	 "chapter_id"  =>$chapter_id[$i],
        	 "status"      =>$status[$i]??0,
        	 "batch_id"    =>$timetable_detail->batch_id,
        	 "subject_id"  =>$timetable_detail->subject_id,
        	]);
        }

        $tt_topic=DB::table("timetable_topic")
        ->where("batch_id",$timetable_detail->batch_id)
        ->where("subject_id",$timetable_detail->subject_id)
        ->where("topic_id",$topic_id[$i])->first();
        if(!empty($tt_topic) && $status[$i]==1){
       		$tt_topic=DB::table("timetable_topic")
          ->where("batch_id",$timetable_detail->batch_id)
          ->where("subject_id",$timetable_detail->subject_id)
          ->where("topic_id",$topic_id[$i])
          ->update(["status"=>$status[$i]]);
       	}
			}

			if(count($nextClassTopic)){
				$topic_mlt=implode(",",$nextClassTopic);
				$nextClass =DB::table('timetables')
				->where('batch_id', $timetable_detail->batch_id)
				->where('subject_id',$timetable_detail->subject_id)
				->where('id','>',$timetable_id)
				->orderBy('id','desc')->first();
				if(!empty($nextClass)){
				    DB::table('timetables')->where('id',$nextClass->id)
				    ->update(['topic_mlt'=>$topic_mlt]);
				    for($j=0;$j<count($nextClassTopic);$j++){
					  $tt_topic=DB::table("timetable_topic")->insert(
           		[
            	 "timetable_id"=>$nextClass->id,
            	 "topic_id"    =>$nextClassTopic[$j],
            	 "chapter_id"  =>0,
            	 "status"      =>0,
            	 "batch_id"    =>$nextClass->batch_id,
            	 "subject_id"  =>$nextClass->subject_id,
              ]
            );
					}
				}

			}
		}

		if(empty($request->delay_type)){
			$request->delay_type="";
		}

		if(empty($request->early_delay_reason)){
			$request->early_delay_reason="";
		}
    
    $timetableid=$timetable_id;
		$get_prev_data = DB::table('start_classes')->where('timetable_id', $timetableid)->first();
		if(empty($get_prev_data)){
			$start_res = DB::table('start_classes')->insertGetId([
				'timetable_id' => $timetableid,
				'start_time'   => date("H:i", strtotime($request->start_time)),
				'end_time'     => date("H:i", strtotime($request->end_time)),
				'sc_date'      => $timetable_detail->cdate,
				'status'       => 'End Class',
				'topic_name'   => $topic_name,
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
				'topic_name'   => $topic_name,
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
				'topic_name'   => $topic_name.'-'.$request->remark.'-'.$request->early_delay_reason.'-'.$request->delay_type,
				'assistant_id' => $asst_id,
				'log_id'       => $logged_id,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'   => date('Y-m-d H:i:s')
			]);
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
		
		return response(['status' => true, 'message' => 'Successfully Updated', 'spd_time' => $duration, 'spent_id' => $spent_id], 200);
	}
	
	public function facultyMonthlyHoursReports(){
		return view('admin.faculty_reports.faculty-monthly-hours-reports');
	}
	
	public function download_excel()
    {   
		$faculty_id     = Input::get('faculty_id');		
		$month          = Input::get('month');
		
		$whereCond  = "  status = '1' AND is_deleted ='0' AND role_id= '2'";
		
		if(!empty($faculty_id)){ 
			$whereCond .= " AND users.id IN(".$faculty_id.")";
		}
			
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($month)){					
			$get_faculty = DB::table('users')
								->select('users.id','users.name','users.mobile')
								->whereRaw($whereCond)
								->orderBy('name')
								->get();	
			
			if(count($get_faculty) > 0){  
				return Excel::download(new FacultyMonthlyHoursExport($get_faculty,$month), 'FacultyMonthlyHoursReportData.xlsx'); 

			} else{
				return redirect()->back()->with('error', 'Something Went Wrong!');
			}
		} 
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
									$start_res = DB::table('start_classes')->insertGetId([
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
	
	
	//faculty index_two
	public function index_two()
    {
    	
        $branch_id      = Input::get('branch_id');
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $branch_location   = Input::get('branch_location');
		$batch_id    = Input::get('batch_id');
		$status      = Input::get('status');
		
		$whereCond  = ' 1=1';
		
		
		
		if(empty($selectFromDate)){
			if(!empty(Auth::user()->id)){
				$selectFromDate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$selectFromDate = date('Y-m-d',strtotime('+1 day'));
					$selectToDate	= date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$selectFromDate = date('Y-m-d');
					$selectToDate   = date('Y-m-d');
				}
			}
		}
		/* if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		} */
		
		/* if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
			else{
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
			}
        } */
		
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			$whereCond .=  ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
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
		
		if(!empty(Auth::user()->id)){
			if(!empty($faculty_id) && is_array($faculty_id) > 0){
				//$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
				$whereCond .= " AND timetables.faculty_id IN('".implode("','",$faculty_id)."')";
			}
			else if(!empty($faculty_id)){
				return redirect()->route('admin.faculty-reports','faculty_id[]='.$faculty_id);
			}
		}
		else{
			if(!empty($faculty_id)){
				$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
			}
		}
		
		
		
		$studio_arr = array();		  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
						  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
						  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
						  // ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
						  
						  if(!empty($status) && $status == 'cancel'){
							$get_faculty->where('timetables.is_publish', '0');
							$get_faculty->where('timetables.is_cancel', '1');
						  }
						  else{
							$get_faculty->where('timetables.is_publish', '1');
						  }
						  if(empty($faculty_id) && empty($batch_id) && empty($branch_location) && $selectFromDate == date('Y-m-d') && Auth::user()->role_id == 29){ 
								$get_faculty->where("branches.id", 55);
						  }
						  if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
						  }
						  else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $get_faculty->whereIn('timetables.studio_id',$studio_arr);
						  }
						  else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
							$get_faculty->where('timetables.assistant_id', Auth::user()->id);
						  }
						  else if(!empty(Auth::user()->id) &&  Auth::user()->id == 5760){
							  $get_faculty->where('branches.branch_location', 'jaipur');
						  }
						  
						  // ->orderBy('timetables.id','desc')
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  // ->groupBy('timetables.assistant_id','timetables.faculty_id')
						  ->get();				  
        //echo "<pre>"; print_r($get_faculty); die;
		
		$drivers = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', 1)->orderBy('name');
		$drivers->where('register_id','!=',NUll);
		$drivers->WhereHas('user_details', function ($q) { // orWhereHas dk
                $q->where('degination', '=', 'DRIVER');
            });
		$drivers = $drivers->get();
		if(!empty(Auth::user()->id)){
			return view('admin.faculty_reports.index', compact('get_faculty','selectFromDate','selectToDate','drivers','studio_arr','status'));
		}
		else{
			//without_login_faculty_report
			return view('admin.faculty_reports.without_login_index_two', compact('get_faculty','selectFromDate','selectToDate','drivers'));
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
		
		
		return view('admin.faculty_reports.timetable_history_reports', compact('get_faculty','selectFromDate','selectToDate','status','branch_id','get_assistant'));
        
    }
	
	
}
