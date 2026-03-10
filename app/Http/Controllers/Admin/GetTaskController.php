<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Task;
use App\TaskDetail;
use App\User;
use Input;
use Excel;
use App\Exports\TaskExport;
use Auth;
use DB;
use Image;
use App\ApiNotification;



class GetTaskController extends Controller
{    
	
	public function task_add(){
		return view('admin.get_task.add');
	}

  
    public function view_task(request $Request)
    {
		$logged_id  = Auth::user()->id;
		$role_id	= Auth::user()->role_id;
		$department_type	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		
		$emp_id 	= Input::get('emp_id');
		$status 	= Input::get('status');
		$fdate 		= Input::get('fdate');
        $tdate 		= Input::get('tdate');
        $d_type 	= Input::get('department_type');
				
		$whereCond =	"1 =1 AND task_new.status != 'Deleted' ";			
		if($emp_id=="Self"){
			$whereCond .= " AND task_new.assign_id = '". Auth::user()->id ."' AND task_new.emp_id = '".Auth::user()->id."'";
		}else if ($emp_id=="Other") {			
			$whereCond .= " AND task_new.assign_id = '". Auth::user()->id ."' AND task_new.emp_id != '".Auth::user()->id."'";
        }else if($emp_id=="aOther"){
			$whereCond .= " AND task_new.emp_id = '". Auth::user()->id ."' and task_new.assign_id != '". Auth::user()->id ."'";
		}else if(!empty($emp_id)) {
			//$whereCond .= " AND (task_new.emp_id = '".$emp_id."' OR task_new.assign_id = '". Auth::user()->id ."')";
			
			if($role_id==21 || $role_id==29 || $role_id==24 || $department_type==51){
				$whereCond .= " AND ((task_new.emp_id = '".$emp_id."' and task_new.assign_id = '". Auth::user()->id ."') OR (task_new.emp_id = '". Auth::user()->id ."' AND  task_new.assign_id = '". $emp_id ."') OR (task_new.emp_id = '". $emp_id ."' AND  task_new.assign_id = '". $emp_id ."'))"; 
			}else{
				$whereCond .= " AND ((task_new.emp_id = '".$emp_id."' and task_new.assign_id = '". Auth::user()->id ."') OR (task_new.emp_id = '". Auth::user()->id ."' AND  task_new.assign_id = '". $emp_id ."'))"; 
			}
		}else{			
			if($role_id==21 && $department_type!=51){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->where('department_type',$department_type)->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
				
			}else if($role_id==29 || $role_id==24 || $department_type==51){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->orWhere('department_type', 21)->orWhere('supervisor_id', 'LIKE', '%' . $logged_id . '%')->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
			}else{
				$whereCond .= " AND (task_new.emp_id = '".Auth::user()->id."' and task_new.assign_id = '". Auth::user()->id ."') OR (task_new.emp_id = '". Auth::user()->id ."' OR  task_new.assign_id = '". Auth::user()->id ."')";
			}
		}
		
		
		if (!empty($status)) {  
			$whereCond .= " AND task_key_points.status = '". $status."'";
		}
		
		if (!empty($fdate) && !empty($tdate)) {  
			$whereCond .= " AND task_new.date >= '". $fdate."' AND task_new.date <= '". $tdate."'";
        }else{
			$whereCond .= " AND task_new.date <= '". date('Y-m-d') ."' AND task_new.date >= '". date('Y-m-d', strtotime(date('Y-m-d').'-3 days'))."' AND task_key_points.status != 'Completed'";
		}
		
		if (!empty($d_type)) {  
			$whereCond .= " AND A.department_type = '". $d_type."'";
		}
		
		$task = DB::table('task_new')
					->select('task_new.*','A.name as assign_name','B.name as emp_name',DB::raw('count(*) as total'))
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')					
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')					
					->whereRaw($whereCond)
					->where('task_key_points.status','!=','Deleted')
					->orderby('task_new.date', 'DESC')
					->groupby('task_key_points.task_id');
					// ->tosql();
		
		// print_r($task);
		
		$task = $task->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
			
		
        return view('admin.get_task.index', compact('task','allDepartmentTypes','pageNumber','params'));
    }
	
	public function view_task_history(request $Request, $id)
    {
		$logged_id  = Auth::user()->id;
		$role_id	= Auth::user()->role_id;
		$department_type	= Auth::user()->department_type;
		
		
		$task = DB::table('task_new')
					->select('task_new.*','task_key_points.id as thid','task_key_points.description as thdescription','task_key_points.status as thstatus','A.name as assign_name','B.name as emp_name','task_key_points.remark as thremark')
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')
					->where('task_new.id', $id)
					->where('task_key_points.status', '!=', 'Deleted')
					->orderby('task_new.created_at', 'DESC')
					->get();
					
		
		
        return view('admin.get_task.view_task', compact('task'));
    }
	

   
    public function task_store(Request $request)
    {		
					
		$login_id = Auth::user()->id;
		
		if(!empty($request->emp_id)){			
			$tDate 			=	$request->date;
			$tEmp			=	$request->emp_id;
			$tTitle			=	$request->title;
			$tPlan 			=	$request->plan_hour;
			$tdescription	=	array_values($request->description);
			

            for($i = 0; $i < count($request->emp_id); $i++){
				$tdata = array(
					'date'		 => $tDate[$i],
					'assign_id'	 => $login_id,
					'emp_id'	 => $tEmp[$i],
					'title' 	 => $tTitle[$i],
					'plan' 		 => $tPlan[$i],
				);
									
				$insertID = DB::table('task_new')->insertGetId($tdata);
				
				
				if(!empty($tdescription)){
					$description=array_values($tdescription[$i]);
					for($j = 0; $j < count($description); $j++){					
						$data = array(
							'task_id'	  => $insertID,						
							'description' => $description[$j],
						);
											
						DB::table('task_key_points')->insert($data);
					}
				}
				
				
				
				//Notification Send
				if($request->emp_id != $login_id){
					$get_emp = User::where('id',$login_id)->first();
					
					$employee_id[]   	 = $tEmp[$i];
					$current_date 		 = date('Y-m-d');
					$current_time 		 = date('H:i:s');
					$inputs['sender_id'] = $login_id;
					$inputs['date'] 	 = $current_date. ' ' .$current_time; 
					
					$inputs['title'] 	 	 = 'New task assign by '.$get_emp['name'];
					$inputs['description'] 	 = $tTitle[$i];
					
					if(!empty($employee_id[0])){
						$inputs['receiver_id'] = json_encode($employee_id);
					}
					
					$inputs['type'] = 'General';		
					$notification = ApiNotification::create($inputs);
					
					
					$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $request->emp_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Task update!!";
					$load['description'] =	"New Task Assign By ".$get_emp['name']." . Please check in task details."; 
					$load['body'] 		 =	"New Task Assign By ".$get_emp['name']." . Please check in task details.";
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	$current_date;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
			 
					$this->notificationDeviceWise($user, $load);
				}
				
				
			}
			return response(['status' => true, 'message' => 'Task Added'], 200);
		
		}
		else{ 
			return response(['status' => false, 'message' => 'Employee ID not found'], 200);
		}
		
    }

    public function edit_task(request $request)
    {
		$task_id = $request->task_id;
		$data 	 = DB::table('task_key_points')->where('id',$task_id)->get();
			
		return $data;
    }
	
	public function update_spent_task(Request $request){
		$spent		=	$request->spent_hour;	
		$task_id	=	$request->task_id;
		
		if($spent!="" && $task_id!=""){
			$data = array(
				'spent'   => $spent,
			);	
					
			DB::table('task_new')->where('id', $task_id)->update($data);
			
			return back()->with('success', "Spent Hour Updated Successfully");
		}else{
			return back()->with('error', "Required filed missing!!");
		}
	}
	
	public function update_task(Request $request){
		$status		=	$request->status;
		$remark		=	$request->remark;
		$task_id	=	$request->task_id;
		
		
		$data = array(
			'status'  => $status,
			'remark'  => $remark,
		);	
		
		//print_r($data); die();
		
		DB::table('task_key_points')->where('id', $task_id)->update($data);
		
		return back()->with('success', "Task Updated Successfully");
	}
	
	
	public function download_pdf() {
		$logged_id  = Auth::user()->id;
		$role_id	= Auth::user()->role_id;
		$department_type	= Auth::user()->department_type;
		$emp_id 	= Input::get('emp_id');
		// $status 	= Input::get('status');
		$fdate 		= Input::get('fdate');
        $tdate 		= Input::get('tdate');
		
		
				
		$whereCond =	"1 =1 AND task_new.status != 'Deleted' ";			
		if ($emp_id=="Other") {			
			$whereCond .= " AND task_new.assign_id = '". Auth::user()->id ."' AND task_new.emp_id != '".Auth::user()->id."'";
        }else if($emp_id=="aOther"){
			$whereCond .= " AND task_new.emp_id = '". Auth::user()->id ."' and task_new.assign_id != '". Auth::user()->id ."'";
		}else if(!empty($emp_id)) {
			$whereCond .= " AND (task_new.emp_id = '".$emp_id."' OR task_new.assign_id = '". Auth::user()->id ."')";
		}else{			
			if($role_id==21){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->where('department_type',$department_type)->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
			}else if($role_id==29){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->orWhere('department_type', 21)->orWhere('supervisor_id', 'LIKE', '%' . $logged_id . '%')->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
			}else{
				$whereCond .= " AND ((task_new.emp_id = '".Auth::user()->id."' and task_new.assign_id = '". Auth::user()->id ."') OR task_new.emp_id = '". Auth::user()->id ."')";
			}
		}
		
		
		// if (!empty($status)) {  
			// $whereCond .= " AND task_new.status = '". $status."'";
		// }
		
		if (!empty($fdate) && !empty($tdate)) {  
			$whereCond .= " AND task_new.date >= '". $fdate."' AND task_new.date <= '". $tdate."'";
        }else{
			$whereCond .= " AND task_new.date <= '". date('Y-m-d') ."' AND task_new.date >= '". date('Y-m-d', strtotime(date('Y-m-d').'-7 days'))."' AND task_key_points.status != 'Completed'";
		}
		
		$task = DB::table('task_new')
					->select('task_new.*','A.name as assign_name','B.name as emp_name',DB::raw('count(*) as total'))
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')					
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')					
					->whereRaw($whereCond)
					->where('task_key_points.status','!=','Deleted')
					->orderby('task_new.created_at', 'DESC')
					->groupby('task_key_points.task_id')
					->get();
		
					
       		
		return view('admin.get_task.pdf_html', compact('task'));
	   
		require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	    
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Task Report');
        $pdf->custom_title = 'Task Report';
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
        // $title = 'Task Report';
		$html = view('admin.get_task.pdf_html', compact('task'))->render();
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
        $pdf->Output('task_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
	}
	
	
	public function task_history($task_id)
    {
		$task_history_get = DB::table('task_new')
					->select('task_new.*','A.name as assign_name','B.name as emp_name')
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')
					->where('task_new.id',$task_id)
					->first();
					
					
		return view('admin.get_task.task_history', compact('task_history_get'));
	}
	
	public function destroy($id){
		$data = array(
			'status'  => 'Deleted',
		);	
		
		DB::table('task_key_points')->where('id', $id)->update($data);
		
		return back()->with('success', "Task Deleted Successfully");
	}
	
	
	///////Webview Part	
	public function mobile_task_add(){
		$emp_id = Input::get('emp_id');
		
		$user 	= User::where('id',$emp_id)->first();
		$role 	= $user['role_id'];
		$department_type 	= $user['department_type'];
		
		return view('admin.get_task.mobile_add',compact('role','emp_id','department_type'));
	}
	
	public function mobile_view_task(request $Request)
    {
		//$logged_id     = Auth::user()->id;
		
		$logged_id = Input::get('logged_id');
		
		
		$emp_id = Input::get('emp_id');
		// $status = Input::get('status');
		$fdate 	= Input::get('fdate');
        $tdate 	= Input::get('tdate');
		
		//Role ID
		$user 	= User::where('id',$logged_id)->first();
		$role 	= $user['role_id'];
		$department_type 	= $user['department_type'];
		
		
		$whereCond =	"1 =1 AND task_new.status != 'Deleted' ";			
		if ($emp_id=="Other") {			
			$whereCond .= " AND task_new.assign_id = '". $logged_id ."' AND task_new.emp_id != '".$logged_id."'";
        }else if (!empty($emp_id)) {
			$whereCond .= " AND (task_new.emp_id = '".$emp_id."' and task_new.assign_id = '". $logged_id ."')";
		}else{			
			if($role==21){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->where('department_type',$department_type)->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
			}else if($role==29){
				$check_supervisor = User::where('status',1)->where('is_deleted','0')->orWhere('department_type', 21)->orWhere('supervisor_id', 'LIKE', '%' . $logged_id . '%')->get();
				if(count($check_supervisor)>0){
					$supervisorId = '';
					foreach($check_supervisor as $key=>$value){
						if(!empty($value)){
							$supervisorId .= $value->id.",";
						}
					}
				}
				$supervisorId .= $logged_id;
				
				$whereCond .= " AND task_new.emp_id IN ($supervisorId)";
			}else{
				//$whereCond .= " AND ((task_new.emp_id = '".$logged_id."' and task_new.assign_id = '". $logged_id ."') OR task_new.emp_id = '". $logged_id ."')";
				
				$whereCond .= " AND ((task_new.emp_id = '".$logged_id."' and task_new.assign_id = '". $logged_id ."') OR task_new.emp_id = '". $logged_id ."' OR  task_new.assign_id = '". $logged_id ."')";
			}
		}
		
		// if (!empty($status)) {  
			// $whereCond .= " AND task_new.status = '". $status."'";
		// }
		
		if (!empty($fdate) && !empty($tdate)) {  
			$whereCond .= " AND task_new.date >= '". date('Y-m-d', strtotime($fdate))."' AND task_new.date <= '". date('Y-m-d', strtotime($tdate))."'";
        }else{
			$whereCond .= " AND task_new.date = '".date('Y-m-d')."'";
		}
		
		//echo $whereCond;
		
		$task = DB::table('task_new')
					->select('task_new.*','A.name as assign_name','B.name as emp_name',DB::raw('count(*) as total'))
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')					
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')					
					->whereRaw($whereCond)
					->where('task_key_points.status','!=','Deleted')
					->orderby('task_new.created_at', 'DESC')
					->groupby('task_key_points.task_id')
					->get();
		
		$allDepartmentTypes  = $this->allDepartmentTypes();
		
        return view('admin.get_task.mobile_view', compact('task','logged_id','role','department_type','allDepartmentTypes'));
    }
	
	
	public function mobile_view_task_history($id, $logged_id)
    {
		//print_r($id); die();
		
		$task = DB::table('task_new')
					->select('task_new.*','task_key_points.id as thid','task_key_points.description as thdescription','task_key_points.status as thstatus','A.name as assign_name','B.name as emp_name','task_key_points.remark as thremark')
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')
					->leftjoin('task_key_points','task_key_points.task_id', 'task_new.id')
					->where('task_new.id', $id)
					->where('task_key_points.status', '!=', 'Deleted')
					->orderby('task_new.created_at', 'DESC')
					->get();
					
		
		
        return view('admin.get_task.mobile_view_history', compact('task','logged_id'));
    }
	
	function mobile_task_store(Request $request)
    {		
		if(!empty($request->employee_id)){			
			$tDate 			=	$request->date;
			$login_id		=	$request->logged_id;
			$tEmp			=	$request->employee_id;
			$tTitle			=	$request->title;
			$tPlan 			=	$request->plan_hour;
			$tdescription	=	array_values($request->description);
			
            for($i = 0; $i < count($request->employee_id); $i++){
				
				$tdata = array(
					'date'		 => $tDate[$i],
					'assign_id'	 => $login_id,
					'emp_id'	 => $tEmp[$i],
					'title' 	 => $tTitle[$i],
					'plan' 		 => $tPlan[$i],
				);
									
				$insertID = DB::table('task_new')->insertGetId($tdata);
				
				
				if(!empty($tdescription)){
					$description=array_values($tdescription[$i]);				
					for($j = 0; $j < count($description); $j++){				
						$data = array(
							'task_id'	  => $insertID,						
							'description' => $description[$j],
						);
											
						DB::table('task_key_points')->insert($data);
					}
				}
				
				
				
				//Notification Send
				if($request->employee_id != $login_id){
					$get_emp = User::where('id',$login_id)->first();
					
					$employee_id[]   	 = $tEmp[$i];
					$current_date 		 = date('Y-m-d');
					$current_time 		 = date('H:i:s');
					$inputs['sender_id'] = $login_id;
					$inputs['date'] 	 = $current_date. ' ' .$current_time; 
					
					$inputs['title'] 	 	 = 'New task assign by '.$get_emp['name'];
					$inputs['description'] 	 = $tTitle[$i];
					
					if(!empty($employee_id[0])){
						$inputs['receiver_id'] = json_encode($employee_id);
					}
					
					$inputs['type'] = 'General';		
					$notification = ApiNotification::create($inputs);
					
					
					$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $request->employee_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Task update!!";
					$load['description'] =	"New Task Assign By ".$get_emp['name']." . Please check in task details."; 
					$load['body'] 		 =	"New Task Assign By ".$get_emp['name']." . Please check in task details.";
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	$current_date;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
			 
					$this->notificationDeviceWise($user, $load);
				}
				
				
			}
		
			return response(['status' => true, 'message' => 'Task Added'], 200);
		
		}
		else{ 
			return response(['status' => false, 'message' => 'Employee ID not found'], 200);
		}
		
    }
	
	
	public function mobile_edit_task(request $request)
    {
		$task_id = $request->task_id;		
		$data 	 = DB::table('task_key_points')->where('id',$task_id)->get();
			
		return $data;
    }
	
	public function mobile_update_task(Request $request){
		$status		=	$request->status;
		$remark		=	$request->remark;
		$task_id	=	$request->task_id;
		
		
		$data = array(
			'status'  => $status,
			'remark'  => $remark,
		);	
		
		//print_r($data); die();
		
		DB::table('task_key_points')->where('id', $task_id)->update($data);
		
		return back()->with('success', "Task Updated Successfully");
	}
	
	
	public function mobile_task_history($task_id)
    {
		$task_history_get = DB::table('task_new')
					->select('task_new.*','A.name as assign_name','B.name as emp_name')
					->leftjoin('users as A','A.id', 'task_new.assign_id')
					->leftjoin('users as B','B.id', 'task_new.emp_id')
					->where('task_new.id',$task_id)
					->first();
					
					
		return view('admin.get_task.mobile_task_history', compact('task_history_get'));
	}
	
	public function mobile_destroy($id){
		$data = array(
			'status'  => 'Deleted',
		);	
		
		DB::table('task_new')->where('id', $id)->update($data);
		
		return back()->with('success', "Task Deleted Successfully");
	}
	
	
	public function mobile_update_spent_task(Request $request){
		$spent		=	$request->spent_hour;	
		$task_id	=	$request->task_id;
		
		if($spent!="" && $task_id!=""){
			$data = array(
				'spent'   => $spent,
			);	
					
			DB::table('task_new')->where('id', $task_id)->update($data);
			
			return back()->with('success', "Spent Hour Updated Successfully");
		}else{
			return back()->with('error', "Required filed missing!!");
		}
	}
	
}
