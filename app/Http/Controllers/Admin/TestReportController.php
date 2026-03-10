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
use App\Batch;
use DB;
use Excel;
use App\Exports\FacultyMonthlyHoursExport;
use DateTime;
use PDF;

class TestReportController extends Controller
{
	
    public function test_report()
    {
        $user_id     = Input::get('user_id');		
		$selectFromDate = Input::get('fdate');
		$batch_id = Input::get('batch_id');
		
		if(empty($user_id)){
			die('User id required');
		}
		
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		if(!empty($selectFromDate)){
		}
		else{
			$selectFromDate = date('Y-m-d');
		}

		$user_branch_id = 0;
		$batches = array();
		$login_user   = User::with(['user_details','user_branches'])->where([['id', '=', $user_id]])->first();
		if($login_user->user_details->degination == 'CENTER HEAD' || $login_user->user_details->degination == 'ASSISTANT CENTER HEAD' ){
			$user_branch_id = $login_user->user_branches[0]->branch_id;

			$whereCond  = ' 1=1';
			$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'"';
			$whereCond .= ' AND timetables.online_class_type = "Test"';
			$whereCond .= ' AND timetables.branch_id = "'.$user_branch_id.'"';
			$batches = DB::table('timetables')
						  ->select('timetables.batch_id','batch.name as batch_name')
						  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
			$batches =	$batches->groupBy('timetables.batch_id')->get();
			
			// print_R($get_batches); die;
			
		}
		else if($user_id==5453 || $user_id==1647 || $user_id==6328 || $user_id==1237){
			$user_branch_id = 55;
		}

		return view('admin.webview_reports.schedule_test', compact('user_id','selectFromDate','batch_id','batches','user_branch_id'));
		
    }
	
	public function test_report_update()
    {
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		
        $user_id     = Input::get('user_id');		
		$tt_id = Input::get('tt_id');
		
		$get_detail = DB::table('test_report')->where('tt_id',$tt_id)->first();
		
		
		
		
		return view('admin.webview_reports.schedule_test_report_update', compact('user_id','tt_id','get_detail'));
		
    }
	
	public function test_report_save(Request $request)
    {
		$fdate = $request->fdate;
		$user_id = $request->user_id;
		$tt_id = $request->tt_id;
		$q21 = $request->q21;
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		if($q21=='Offline'){
			$validatedData = $request->validate([
				'q21' => 'required',
				'q6' => 'required',
				'q6_1' => 'required',
				'q8' => 'required',
			],
			[
				'q21.required' => 'टेस्ट का प्रकार is required',
				'q6.required' => ' टेस्ट का समय व प्रश्नों की संख्या is required',
				'q6_1.required' => ' टेस्ट का समय व प्रश्नों की संख्या is required',
				'q8.required' => ' टेस्ट का रिज़ल्ट कौनसी तारीख़ को घोषित करेंगे is required',
			]);
		}
		else{
			$validatedData = $request->validate([
				'q21' => 'required',
			],
			[
				'q21.required' => 'टेस्ट का प्रकार is required',
			]);
		}
		
		
		$inputs = $request->only('user_id','tt_id','q1','q2','q3','q4','q5','q6','q6_1','q7','q8','q9','q10','q11','q12','q13','q14','q15','q16','q17','q18','q19','q19_1','q20','q21');
		
		$drive = public_path(DIRECTORY_SEPARATOR . 'timetable_test' . DIRECTORY_SEPARATOR);
		$files = array();
		$file = Input::file('q16');
		if(!empty($file)){
			foreach($file as $fileval){
				
				$extension = $fileval->getClientOriginalExtension();
				$filename = uniqid().'-'.time() . '.' . $extension;    
				$newImage = $drive . $filename;
				$imgResource = $fileval->move($drive, $filename);
				$files[] = $filename;
			}
			$inputs['q16'] = json_encode($files);
		}
		
		// echo "<pre>"; print_R($files); die;
		
		
		// $filename
		
		$get_detail = DB::table('test_report')->where('tt_id',$tt_id)->first();
		if(!empty($get_detail)){
			DB::table('test_report')->where('id', $get_detail->id)->update($inputs);
		}
		else{
			
			DB::table('test_report')->insert($inputs);
		}
		
		
		
		
		
		return redirect("test-report?user_id=$user_id&fdate=$fdate")->with('success', 'Save Successfully');
		
    }
	
	public function batch_test_report()
    { 
		
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_id    = Input::get('batch_id');
		
		 
		 
		$conditions   = array();
		$whereCond    = '1=1 ';
		$get_batches = Batch::with(['batch_timetables'=> function ($q) use ($fdate, $tdate)
		{
			$q->where('online_class_type','Test');
			$q->where('is_deleted', '=', '0');
			// $q->where('time_table_parent_id', '=', '0');
			$q->orderBy('cdate');
			$q->groupBy('id');
		}
		,'batch_timetables.studio' => function ($q) use ($studio_id, $assistant_id,$type)
		{
			if (!empty($studio_id))
			{
				$q->where('id', $studio_id);
			}
			
			if (!empty($assistant_id))
			{
				$q->where('assistant_id', $assistant_id);
			}
			
			if (!empty($type))
			{
				$q->where('type', $type);
			}
			$q->whereNotNull('branch_id');
			$q->orderBy('order_no', 'asc');
			
		},'batch_timetables.studio.branch'=> function ($q) use ($branch_id)
		{
			if (!empty($branch_id))
			{
				$q->whereIn('id', $branch_id);
			}
			
		},'batch_timetables.studio.assistant','batch_timetables.topic','batch_timetables.faculty','batch_timetables.course','batch_timetables.subject','batch_timetables.chapter','batch_timetables.assistant']);
			
		$get_batches->WhereHas('batch_timetables', function ($q) {
					$q->where('online_class_type','Test');
					$q->where('is_deleted', '=', '0');
					// $q->where('time_table_parent_id', '=', '0');
					$q->orderBy('cdate');
					$q->groupBy('id');
			});

		if (!empty($branch_id)){	
			$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_id) {
				$q->whereIn('id', $branch_id);
			});
		}

		
		
		if(!empty($batch_id)){
			$get_batches->where('id',$batch_id);
		}
		else{
			$get_batches->where('id',0);
		}
		$get_batches = $get_batches->first();
	
		// echo '<pre>'; print_r($get_batches->batch_timetables);die;
		return view('admin.test_report.index', compact('get_batches','fdate','tdate'));
        
    }
	
	public function test_report_view()
    {
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		
        $user_id     = Input::get('user_id');		
		$tt_id = Input::get('tt_id');
		
		$get_detail = DB::table('test_report')->where('tt_id',$tt_id)->first();
		
		
		
		
		return view('admin.webview_reports.schedule_test_report_view', compact('user_id','tt_id','get_detail'));
		
    }
	
	public function test_report_download()
    {
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		
        $user_id     = Input::get('user_id');		
		$tt_id = Input::get('tt_id');
		
		$get_detail = DB::table('test_report')->where('tt_id',$tt_id)->first();
		
		
		
		// $html = view('admin.webview_reports.schedule_test_report_download', compact('user_id','tt_id','get_detail'))->render();echo $html; die;
		$pdf = PDF::loadView('admin.webview_reports.schedule_test_report_download', ['user_id' => $user_id,'tt_id' => $tt_id,'get_detail' => $get_detail]);
		// echo $pdf; die;
		//return $pdf->Output('admit_card.pdf', 'D');    
		return $pdf->stream('document.pdf');
		return $pdf->download('document.pdf');
		
		
		exit;
		
		return view('admin.webview_reports.schedule_test_report_view', compact('user_id','tt_id','get_detail'));
		
    }
	
	//New Report Count Wise
	public function batch_test_report_new(Request $request){
		$branch_location	=	$request->branch_location;
		$branch_id			=	$request->branch_id;
		$batch_id			=	$request->batch_id;
		$fdate				=	$request->fdate;
		$tdate				=	$request->tdate;
		
		
		if(!empty($branch_location)){
			$getbatch	=	Batch::select(DB::raw("batch.*,count(timetables.batch_id) as total_qty,branches.branch_location as location,branches.name as bname"))
								->leftJoin('timetables','timetables.batch_id','batch.id')
								->leftJoin('branches','branches.id','timetables.branch_id')
								->where('timetables.online_class_type','Test')
								->where('timetables.is_deleted', '=', '0')
								->groupBy('batch.id');
			
			if(!empty($branch_location)){
				$getbatch->where('branches.branch_location',$branch_location);
			}
			
			if(!empty($branch_id)){
				$getbatch->where('timetables.branch_id',$branch_id);
			}
			
			if(!empty($batch_id)){
				$getbatch->where('batch.id',$batch_id);
			}
			
			if (!empty($fdate) && !empty($tdate)) {			
				$getbatch->whereDate('timetables.cdate', '>=', $fdate)->whereDate('timetables.cdate', '<=', $tdate);
			} elseif (!empty($fdate)) {
				$getbatch->whereDate('timetables.cdate', 'LIKE', '%' . $fdate . '%');
			} elseif (!empty($tdate)) {
				$getbatch->whereDate('timetables.cdate', 'LIKE', '%' . $tdate . '%');
			}
								
			$getbatch = $getbatch->get();
		}else{
			$getbatch	=	array();
		}
			
		return view('admin.test_report.test_report_new',compact('getbatch'));
	}
	
	public function send_faculty_pdf_view(Request $request){
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		$name = Input::get('name');
        $cdate = Input::get('cdate');
        $todate = Input::get('todate');
        // $todate = '2023-09-08';
		
		$fpdf = DB::table('faculty_pdf')->orderBy('created_at','DESC');
		if (!empty($name)){
            $fpdf->where('name', $name);
        }
		if (!empty($cdate)){
            // $fpdf->whereRaw("cdate >= '$cdate' and cdate <= '$todate'");
			$fpdf->whereDate('created_at', '>=', $cdate)->whereDate('created_at', '<=', $todate);
        }
		$fpdf = $fpdf->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		$users	=	DB::table('users')
						->select('users.id','users.name as uname','users.mobile as umobile','subject.name as sname')
						->leftJoin('faculty_subjects','faculty_subjects.user_id','users.id')
						->leftJoin('subject','subject.id','faculty_subjects.subject_id')
						->where('users.role_id', 2)
						->where('users.status', '1')
						->where('users.is_deleted', '0')
						->get();
		return view('admin.test_report.send-faculty-pdf',compact('fpdf','pageNumber','params','users'));
	}
	
	public function send_faculty_pdf_add(Request $request){
		$id 	 = $request->user_id;		
				
		if(!empty($id) && !empty(Input::file('attachment'))){	
			$imgName = $this->uploadImage(Input::file('attachment'));
			
			$user 	=	User::where('id',$id)->first();
			
		
			$data = array(
				"mobile"		=>	$user->mobile,
				"name"			=>	$user->name,
				"attachment"	=>	$imgName,
			);
			
			$id = DB::table('faculty_pdf')->insertGetId($data);
			
			
			
			//Whatsapp
			$mobile		=	$user->mobile;
			$url		=	"http://15.207.232.85/laravel/public/faculty_pdf/".$imgName;	
			$pdfname	=	"Class Comments";	
			$appid		=	'a_158521380743240260';	
			
			$varible	 =	'"variable1":"'.$user->name.'",';
			$varible	.=	'"variable2":"Kindly contact Mr. Suresh on this number 8696017109.",';
			$data='{ 
				"appid": "'.$appid.'", 
				"deliverychannel":"whatsapp", 
				"message":{
					"template":"3543882642532039", 
					"parameters":{
						'.$varible.'
						"document": { "link": "'.$url.'", "filename": "'.$pdfname.'" } 
					}
				},"destination":[{"waid":["91'.$mobile.'"]}] 
			}';
			
			// print_r($data);
			// die();
			
			$this->sendmsg($data);
			return redirect()->back()->with('success', 'Comment PDF Send Successfully');
		}else{
			return redirect()->back()->with('error', 'Required filed missing!!');
		}
	}	
	
	public function sendmsg($data){
		$url="https://api.imiconnect.in/resources/v1/messaging";   
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','key:6c4e98d0-7047-11ea-9da9-025282c394f2'));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data); 
		$result=curl_exec($ch);
		curl_close($ch);  
		return $result;
	} 
	
	public function uploadImage($image){
       $drive = public_path(DIRECTORY_SEPARATOR . 'faculty_pdf' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
	}

	public function mentor_report_batch_list()
    {
        $mentor_id     = Input::get('mentor_id');
		
		if(empty($mentor_id)){
			die('Mentor id required');
		}
		
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}

		$mentor_batch_list = array();
		$batches = array();
		$batches = DB::table('users')
					  ->select('batch.id as batch_id','batch.name as batch_name','batch.mentor_id','users.name as user_name')
					  ->leftJoin('batch', 'batch.mentor_id', '=', 'users.id')
					  ->where('users.id', $mentor_id)
					  ->where('users.status', 1);
		$batches =	$batches->get();
		
		if(count($batches) > 0){
			$mentor_batch_list = json_decode(json_encode($batches),true);
		}
		else if($mentor_id==5453){
			$mentor_batch_list = array('mentor_id'=>$mentor_id,'batch_id'=>3,'batch_name'=>'Demo Batch','user_name'=>'Dinesh');
		}
		
		// echo "<pre>"; print_R($mentor_batch_list); die;

		return view('admin.webview_reports.mentor_report_batch_list', compact('mentor_id','mentor_batch_list'));
		
    }
	
	public function mentor_report_batch_detail()
    {
        $mentor_id     = Input::get('mentor_id');
        $batch_id     = Input::get('batch_id');
		
		if(empty($mentor_id)){
			die('Mentor id required');
		}
		else if(empty($batch_id)){
			die('Batch id required');
		}
		
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}

		$mentor_batch_list = array();
		$batches = DB::table('batch')
					->where('batch.id', $batch_id);
		$batche_detail =	$batches->first();
		// echo "<pre>"; print_r($batche_detail); die;
		
		//All 
		$no_of_hour = DB::table('batchrelations')->where('batch_id', $batch_id)->sum('no_of_hours');
		
		//Spent Hour
		$spent_no_of_hour = DB::select("select SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(to_time, from_time)))) AS totalhours FROM timetables WHERE `batch_id` = '".$batch_id."'");

		$get_batches = Batch::with(['batch_timetables'=> function ($q)
		{
			$q->where('online_class_type','Test');
			$q->where('is_deleted', '=', '0');
			$q->orderBy('cdate');
			$q->groupBy('id');
		}
		,'batch_timetables.studio' => function ($q)
		{
			$q->whereNotNull('branch_id');
			$q->orderBy('order_no', 'asc');
			
		},'batch_timetables.studio.branch'=> function ($q)
		{
			
		},'batch_timetables.studio.assistant','batch_timetables.topic','batch_timetables.faculty','batch_timetables.course','batch_timetables.subject','batch_timetables.chapter','batch_timetables.assistant']);
			
		$get_batches->WhereHas('batch_timetables', function ($q) {
					$q->where('online_class_type','Test');
					$q->where('is_deleted', '=', '0');
					$q->orderBy('cdate');
					$q->groupBy('id');
			});

		
		
		$get_batches->where('id',$batch_id);
		
		$test_status = $get_batches->first();
		
		// echo "<pre>";print_R($test_status); die;
		
		$batch_code = $batche_detail->batch_code;
		
		$inventory_header = DB::table('batch_inventory')
					->select('inventory_type','batch_code')
					->where('status','1')
					->groupby('inventory_type')
					->where("batch_code", $batch_code)
					->get();
					
		
		// $inventory = DB::table('batch_inventory')
					// ->select(DB::raw("SUM(quantity) as total_qty"),'batch_inventory.*')
					// ->where('status','1')
					// ->groupby('name','inventory_type')
					// ->where("batch_code", $batch_code)
					// ->get();
		
		
		$stdattendance 	= 	DB::connection('mysql2')->table("tbl_registration")
					->select(DB::raw("count(batch) as total_admission"),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id and gender="male") as total_male'),'tbl_registration.batch_id','tbl_batch.batch_name','tbl_registration.reg_number')
					->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id')
					->where("tbl_registration.batch_id", $batch_code)
					->where("tbl_batch.batch_running_status", 'Running')
					->get();
			
		// print_r($query);

		return view('admin.webview_reports.mentor_report_batch_detail', compact('batche_detail','test_status','batch_id','stdattendance','inventory_header','no_of_hour','spent_no_of_hour'));
		
    }
	
	public function employee_leave_detail(){
		$user_id     = Input::get('user_id');
		
		$users	=	DB::table('users')->select('users.register_id')->where('users.id', $user_id)->where('users.status', '1')->where('users.is_deleted', '0')->first();
		if(!empty($users)){
			$year = date('Y');
			$reg_id = $users->register_id;
			$user_leave_manual	=	DB::table('emp_leave_manual')->select('*')->where('emp_code', $reg_id)->get();
			// $user_leave_manual	=	DB::table('emp_leave_manual')->select('*')->whereRaw("YEAR(date) = '$year'")->where('emp_code', $reg_id)->get();
		}
		else{
			die('User id invalid');
		}
		
		return view('admin.webview_reports.employee_leave_detail', compact('user_id','user_leave_manual'));
		
	}
	
}
