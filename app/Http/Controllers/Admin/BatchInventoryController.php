<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Asset;
use App\AssignAsset;
use App\User;
use Excel;
use App\AssetRequest;
use App\ApiNotification;
use App\AssetRequestNotification;
use Input;
use Validator;
use DataTables;
use DB;
use Auth;
use App\Category;
use App\Buyer;
use App\Branch;
use App\Batch;
use Image;
use App\Inventory;
use App\BatchInventory;
use App\Exports\PoReportExport;
use App\Exports\PoPaymentExport;
use App\Exports\AnupritiExport;


class BatchInventoryController extends Controller
{
	public function index(){	
		$logged_id  = 	Auth::user()->id;	
		$role_id    = 	Auth::user()->role_id;	
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		
		$name 		=	Input::get('name');
        $status 	=	Input::get('status');
        $itype 		=	Input::get('itype');
        $batch_name =	Input::get('batch_name');
        $uname		=	Input::get('uname');
        $created_date =	Input::get('created_date');

        $inventory 	= 	BatchInventory::select('batch_inventory.*','batch.name as bname','users.name as userName')->leftjoin('batch','batch.batch_code','batch_inventory.batch_code')->leftjoin('users','users.id','batch_inventory.created_by')->where('batch_inventory.status',"!=", 2)->where('batch.is_deleted', '0')->orderBy('id', 'desc');

        if (!empty($name)){
			$inventory->whereRaw("(batch_inventory.name = '".$name."' OR batch_inventory.batch_code = '".$name."')");
        }
		
        if(!empty($status)){
            if($status == 'Inactive'){
                $inventory->where('status', '=', '0');
            }else{
                $inventory->where('status', '=', '1');
            }
        }
		
		if (!empty($batch_name)){
            $inventory->where('batch_inventory.batch_name', 'LIKE', '%' . $batch_name . '%');
        }
		
		if (!empty($uname)){
            $inventory->where('users.name', 'LIKE', '%' . $uname . '%');
        }
		
		if ($itype=='all'){
			$inventory->where('batch_inventory.batch_code',0)->groupby('batch_inventory.name');
		}
		
		
		if (!empty($created_date)){
			$inventory->whereDate('batch_inventory.created_at',$created_date);
		}

        // $inventory = $inventory->get();
		
		$inventory = $inventory->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		return view('admin.batchinventory.index', compact('inventory','pageNumber','params')); 
	}
	
    public function add(){		
		$logged_id       = Auth::user()->id;
		$batchs = Batch::where('status', '1')->where('is_deleted', '0')->get();
		
		return view('admin.batchinventory.add',compact('batchs'));		
	}
	
	public function save(Request $request){
		$logged_id       = Auth::user()->id;
						
		$validatedData = $request->validate([
            'name' 		=> 'required',
            'batch_id' 	=> 'required',
            'type' 		=> 'required',
            'quantity' 	=> 'required',
        ]);
		
        // $inputs = $request->only('name','type','quantity','inventory_type');
		// if($inputs['type']=='all'){
			// $inputs['batch_code']=0;
			// $inputs['batch_name']=0;
		// }
		// else{
			// $batch_explode = $request->batch_code;
			// $batch_explode = explode('&&&',$batch_explode);
			// $inputs['batch_code'] = $batch_explode[0];
			// $inputs['batch_name'] = $batch_explode[1];
		// }
		// $inputs['created_by'] = $logged_id;
		
		for($i = 0; $i < count($request->inventory_type); $i++){
			$record = array();
			
			$record['inventory_type']	=	$request->inventory_type[$i];
			$record['name']				=	$request->name[$i];
			$record['quantity']			=	$request->quantity[$i];
			$record['created_by']		=	$logged_id;
			$record['type']				=	$request->type;
			$record['batch_code']		=	$request->batch_id;	

			
			$batch_inventory = BatchInventory::create($record); 
		}
		
		
		   

		if ($batch_inventory->save()) {
			return redirect()->route('admin.batchinventory.index')->with('success', 'Added Successfully');
		} else {
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	public function edit($id)
    {
        $inventory = BatchInventory::find($id);
		$batchs = Batch::where('status', '1')->where('is_deleted', '0')->get();
        return view('admin.batchinventory.edit', compact('inventory','batchs'));
    }
	
	public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            // 'name' => 'required|unique:batch_inventory,name,'.$id,
            'name' => 'required',
			// 'batch_code' => 'required',
			'type' => 'required',
			'quantity' => 'required',
        ]);

        $batch_inventory = BatchInventory::where('id', $id)->first();

        $inputs = $request->only('name','status','type','quantity'); 
		if($inputs['type']=='all'){
			$inputs['batch_code']=0;
			$inputs['batch_name']=0;
		}
		else{
			$batch_explode = $request->batch_code;
			$batch_explode = explode('&&&',$batch_explode);
			$inputs['batch_code'] = $batch_explode[0];
			$inputs['batch_name'] = $batch_explode[1];
		}	

        if ($batch_inventory->update($inputs)) {
            return redirect()->route('admin.batchinventory.index')->with('success', 'Updated Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function destroy($id)
    {
        $role = BatchInventory::find($id);
		
		if(!empty($role)){
			$role->update([
                'status' => '2',
            ]);
			
            return redirect()->back()->with('success', 'Deleted Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function attendance_dashboard(Request $request){
		
		
		$msg="";
		$logged_id 	=	Auth::user()->id;
		$fdate		=	Input::get('fdate');
		$tdate		=	Input::get('tdate');
		$batch_id	=	Input::get('batch_id');
		$branch_id	=	Input::get('branch_id');
		$allbatch_ids= Input::get('allbatch_ids');
		$branch_location= Input::get('branch_location');
		$search= Input::get('search');
		$anuprati_id= Input::get('anuprati_id');

		if(empty($batch_id)){
		  $batch_id=array();
		}
		
		if(!empty($anuprati_id)){
			$fee_where = " AND tbl_registration.cast = '".$anuprati_id."'";
		}else{
			$fee_where = " ";
		}

		$query	= array();
		if(Auth::user()->role_id == 32){
			$cipBatch = "(".Auth::user()->email_verified_at.")";
			
			$query = DB::connection('mysql2')->table("tbl_registration")
					->select(DB::raw("count(batch) as total_admission"),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id '.$fee_where.' and s_type="Approved") as total_admission'),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id '.$fee_where.' and gender="male" and s_type="Approved") as total_male'),'tbl_registration.batch_id','tbl_batch.batch_name','tbl_registration.reg_number')
					->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id');
			
			$query->whereRAW("tbl_registration.batch_id IN ".$cipBatch);
			$query->where("tbl_batch.batch_running_status", 'Running');
			$query->where("tbl_registration.s_type", 'Approved');
			$query->groupby('tbl_registration.batch_id');			
			$query = $query->get();
		}else{
			if(!empty($search)){
				$query = DB::connection('mysql2')->table("tbl_registration")
					->select(DB::raw("count(batch) as total_admission"),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id '.$fee_where.' and gender="male" and s_type="Approved") as total_male'),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id '.$fee_where.' and (cast="Anuprati Yojana" OR cast="Anupriti Yojna-2022-23" OR cast="Anupriti Yojna-2023-24" OR cast="Anupriti Yojna-2023-24 (Ph-2)" OR cast="Anuprati Yojna-2024-25")) as total_anuprati'),'tbl_registration.batch_id','tbl_batch.batch_name','tbl_registration.reg_number')
					->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id');
					
				if(!empty($branch_location)){
					$query->where("tbl_batch.branch", $branch_location);
				}
							
				if(!empty($anuprati_id)){
					$query->where("tbl_registration.cast", $anuprati_id);
				}

				if(!empty($allbatch_ids) AND empty($batch_id)){
				   $allbatch_ids=explode(",",$allbatch_ids);
				   $query->whereIN("tbl_registration.batch_id", $allbatch_ids);
				}else if(!empty($request->batch_id)){
					$query->whereIN("tbl_registration.batch_id", $batch_id);
				}
				
				// else{
					// $query->where("tbl_batch.batch_running_status", 'Running');
				// }
				
				$query->where("tbl_registration.s_type", 'Approved');				
				$query->groupby('tbl_registration.batch_id');			
				$query = $query->get();
			}else{
			  $msg="Select Branch";
			}			
			
		}		
			
		return view('admin.batchinventory.attendance-dashboard', compact('query','fdate','tdate','branch_id','allbatch_ids','batch_id','anuprati_id'));
	}

	public function studentSearch(Request $request){
		$reg_no=$request->reg_no;
		$month=$request->month;
		$data=[];
		$status=false;
		$msg='No data found';
		if(!empty($reg_no) && !empty($month)){
			$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no')->where('reg_number',$reg_no)->first();
			if(!empty($student)){
				$attendance = DB::table("student_attendance")->select('*',DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as pdate"))->where('reg_no',$reg_no)->whereRAW("date like '".$month."%'")->get();

				$month = explode('-',$month);
				$yr = $month[0];
				$mt = $month[1];

				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
				$i=0;
				$st_attendance=[];
				$attendance=json_decode(json_encode($attendance),true);
				while($daysInMonth>0){
					$add_get_date  = date('Y-m-d', $first_date);
				    $st_attendance[$i]['pdate']=$add_get_date;
				    $st_attendance[$i]['status']='Absent';
				    $st_attendance[$i]['date']='-';

					$daysInMonth--;
					$first_date += 86400; 
					$atdIndex=array_search($add_get_date,array_column($attendance,'pdate'));
					if($atdIndex!== false){ 
				     $st_attendance[$i]['date']=$attendance[$atdIndex]['date'];
				     $st_attendance[$i]['status']='Present';
					}
					$i++;
				}

               $st_inventory_pending=[];
               $st_inventory_assign=[];
               $assign_inventory=$student->assign_inventory;
               if($assign_inventory!=""){
                 $assign_inventory=explode(",",$assign_inventory);
               }
               $inventory = DB::table('batch_inventory')
                    ->select("*",DB::raw('CONCAT(batch_code,name,inventory_type) as demo'))
					->where('status','1')
					->where('batch_code',$student->batch_id)
					->groupby('demo')
					//->whereRaw("id IN (".$assign_inventory.")")
					->get();

				foreach($inventory as $key=>$val){
					$val->is_assgined="Pending";
				    if($assign_inventory!="" && count($assign_inventory)){
					  	for($i=0;$i<count($assign_inventory);$i++){
					  		if($assign_inventory[$i]==$val->id){
					  			$val->is_assgined="Assigned";
					  		}
					  	}
						
					}

							
					$st_inventory_pending[]=$val;
				}
                
				$data['student']=$student;
				$data['attendance']=$attendance;
				$data['st_inventory_pending']=$st_inventory_pending;
				$data['st_attendance']=$st_attendance;
				$status=true;
		        $msg='data found';

				
				//print_r($data);die();
			}else{
				$msg='No student found';
			}

		}
		return response(['status'=>$status,'message'=>$msg,'data'=>$data]);
		die('dddd');
	}
	
	public function inventory_dashboard(Request $request){
		$batch 			= 	Batch::where('status','1')->get();
		$batch_id		=	Input::get('batch_id');
		$allbatch_ids	= 	Input::get('allbatch_ids');
		$branch_location= 	Input::get('branch_location');
		$branch_id		=	Input::get('branch_id');
		
		if(empty($batch_id)){
			$batch_id=array();
		}
		
		$inventory = array();
		if(!empty($branch_location)){
			$inventory = DB::table('batch_inventory')->select(DB::raw("SUM(quantity) as total_qty"),'batch_inventory.*')->where('status','1')->groupby('name','inventory_type')->orderby('batch_code');
				
			if(!empty($allbatch_ids) AND empty($batch_id)){
				$allbatch_ids=explode(",",$allbatch_ids);
				$inventory->whereIN("batch_code", $allbatch_ids);
			}else if(!empty($batch_id)){
				$inventory->whereIN("batch_code", [$batch_id,0]);
			}
			
			$inventory = $inventory->get();
		}

		
		return view('admin.batchinventory.inventory-dashboard',compact('batch','inventory','batch_id','branch_id'));
	}
	
	public function student_attendance_view(Request $request,$batch_id,$fdate=null,$type){		
		
		$attendance = DB::table('student_attendance')
						->select('student_attendance.*','users.name as uname')
						->leftjoin('users','users.id','student_attendance.user_id')
						->where('batch_id',$batch_id)
						->groupby('reg_no');		
		if (!empty($fdate)) {  
			$attendance->whereRaw(DB::raw("DATE(date) = '".$fdate."'"));
		}else{
			$attendance->whereRaw(DB::raw("DATE(date) = '".date('Y-m-d')."'"));
		}		
		$attendance = $attendance->get();
		
		// $attendance = DB::connection('mysql2')->table("tbl_registration")
			// ->select('reg_number','batch_id','s_name','contact',)->where("tbl_registration.batch_id", $batch_id)->get();	
		
		return view('admin.batchinventory.student-attendance-view',compact('attendance','fdate','type','batch_id'));	
	}
	
	public function getBatch(Request $request){
		// $ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
		// $ttdate=date('Y-m-d');
		
		$fdate = $request->fdate;
		$tdate = $request->tdate;
		
		$batch = DB::table('batch')
					->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
					->leftjoin('timetables as tt','tt.batch_id','batch.id')
					->where('tt.is_deleted', '0')
					->where('tt.is_publish', '1')
					->where('tt.is_cancel', 0);
					
		if (!empty($fdate) && !empty($tdate)) {  
			$batch->whereRaw(DB::raw("DATE(tt.cdate) >= '".$fdate."' AND DATE(tt.cdate) <= '".$tdate."'"));
		}else{
			$batch->whereRaw(DB::raw("DATE(tt.cdate) = '".date('Y-m-d')."'"));
		}
		
		// where('tt.cdate','=',$ttdate)
		
		$batch = $batch->where('batch.batch_code','!=',0)
					->where('tt.branch_id',$request->branch_id)
					->groupby('batch.batch_code')
					->get();
	    
	    $msg=$batches=$allbatch_ids="";
		if (!empty($batch))
        {
            $batches="<option value=''> Select Batch </option>";
            $allbatch_ids="";
            foreach ($batch as $key => $value)
            {
               $allbatch_ids.=$value->batch_code.",";
               $batches.= "<option value='" . $value->batch_code . "'>" . $value->name . "</option>";
            }
            
            //$allbatch_ids= '<input type="hidden" name="allbatch_ids" value="'.$allbatch_ids.'"/>';
        }else
        {
            $batches = "<option value=''>Batch Not Found </option>";
        }

        return response(['status'=>true,'batches'=>$batches,'allbatch_ids'=>$allbatch_ids,'msg'=>$msg],200);
	}
	
	public function student_inventory_view(Request $request,$batch_id,$id=null,$type){
		$reg_no = Input::get('reg_no');
			
		
		
		$inventory = DB::connection('mysql2')->table("tbl_registration")
			->select('s_name','reg_number','contact')
			->where('batch_id',$batch_id);
		
		if($type==1){
			$inventory->whereRaw('FIND_IN_SET('.$id.',assign_inventory)');
		}else{
			$inventory->whereRaw('NOT FIND_IN_SET('.$id.',assign_inventory)');
		}
		
		if(!empty($reg_no)){
			$inventory->where('reg_number',$reg_no);
		}
		
		$inventory = $inventory->get();
		
		return view('admin.batchinventory.student-inventory-view',compact('inventory','batch_id','id','type'));	
	}
	
	public function get_batch_inventory(Request $request){
		$batch_code = $request->batch_code;

        $batchInventory = BatchInventory::where('status',1)->where('batch_code', $batch_code)->get();

        if (!empty($batchInventory))
        {
            echo $res = "";
            foreach ($batchInventory as $key => $value)
            {
                if (!empty($value->name))
                {
                    echo $res = "<option value='" . $value->name . "'>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Inventory Not Found </option>";
            die();
        }
	}
	
	public function studnet_attendance_notification(Request $request){
		$student_noti = DB::table('student_attendance_notification')->select('student_attendance_notification.*','batch.name','users.name as uname')->leftjoin('batch','batch.batch_code','student_attendance_notification.batch_code')->leftjoin('users','users.id','student_attendance_notification.user_id')->get();
		
		return view('admin.batchinventory.studnet-attendance-notification', compact('student_noti'));	
	}
	
	public function save_attendance_notification(Request $request){
		$logged_id  = 	Auth::user()->id;
		$batch_code	=	$request->batch_id;
		$sdate		=	$request->sdate;
		
		if($logged_id!="" && $batch_code!="" && $sdate!=""){		
			//Send Notification 
			$attendance = DB::table('student_attendance')
						->select('student_attendance.*','users.name as uname')
						->leftjoin('users','users.id','student_attendance.user_id')
						->where('batch_id',$batch_code)		
						->whereRaw(DB::raw("DATE(date) = '".$sdate."'"))
						->get();
			
			$present=[];
			foreach($attendance as $a){
				$present[]=$a->reg_no;
			}
			
			if(count($present)){
				$query = DB::connection('mysql2')->table("tbl_registration")->select('s_name','contact','reg_number','guardianmobile')
									   ->whereNotIN('reg_number',$present)->where('rfid_no','!=','0')->where('batch_id',$batch_code)->get();
				
				$reg_no = '';
				foreach($query as $data){	
					$reg_no .= 	$data->reg_number.',';
					$mobile	=	$data->guardianmobile;
					$msg	=	"प्रिय अभिभावक, उत्कर्ष संस्थान में अध्यनरत विद्यार्थी गत दिवस अनुपस्थित थे, आगामी भर्ती परीक्षा को ध्यान में रखते हुए विद्यार्थी को नियमित उपस्थित रहने के लिए प्रेरित करें।  - धन्यवाद";
					$message_content=urlencode($msg);
					
					$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mobile}&message={$message_content}&sid=UTKRSH&mtype=LNG&DR=Y";
					$ch=curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_exec($ch);
					curl_close($ch);
				}
				
				
				$data = array(
					"batch_code"	=>	$batch_code,
					"date"			=>	$sdate,
					"user_id"		=>	$logged_id,
					"reg_no"		=>	$reg_no,
				);
				
				DB::table('student_attendance_notification')->insert($data);
				
				return redirect()->route('admin.studnet-attendance-notification')->with('success', 'Notification Send Successfully');
			}else{
				return redirect()->route('admin.studnet-attendance-notification')->with('error', 'No single student persent in class..May be class not scheduled or attendance not taken ');
			}
		}else{
			return redirect()->route('admin.studnet-attendance-notification')->with('error', 'Required filed missing');
		}
	}
	
	
	public function attendencerecord(Request $request){
		$mt = date('m');
		//$mt="02";
		$yr = date('Y');
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
			$year_wise_month = explode('-',$params['year_wise_month']);
			
			$yr = $year_wise_month[0];
			$mt = $year_wise_month[1];
		}
		// print_r($params); die;
		
		$getWorkSunday 			= 	cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
		
		$logged_role_id  		=	Auth::user()->role_id;
		$logged_id       		=	Auth::user()->id;
		
		$logid 					=	array();
        $allDepartmentTypes  	=	$this->allDepartmentTypes();
        $allBranches  			=	$this->allBranches();
		return view('admin.batchinventory.studnet-attendancerecord', compact('allDepartmentTypes','allBranches','getWorkSunday'));
	}
	
	
	public function studnet_attendencerecorddetail(Request $request){		
		$logged_role_id     = Auth::user()->role_id;
		$logged_id          = Auth::user()->id;
		$reg_no				= $request->name;
		$batch_id           = $request->batch_id;
        $year_wise_month    = $request->year_wise_month;
        $anuprati_id     	= $request->anuprati_id;
        
		if($logged_role_id==32){
			$cipBatch = "(".Auth::user()->email_verified_at.")";
						
			$responseArray = $this->calculate_attendance($reg_no,$cipBatch,$year_wise_month,'','32');
		}else{
			if(!empty($batch_id) || !empty($reg_no)){
				$responseArray = $this->calculate_attendance($reg_no,$batch_id,$year_wise_month,$anuprati_id,'');
			}else{
				$responseArray = array();
			}
		}
		
		//echo "<pre>";print_R($responseArray); die;
		return DataTables::of($responseArray)->make(true);

		
	}
	
	public function calculate_attendance($reg_no,$batch_id,$month,$anuprati_id,$role_id){
		$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no','cast');
		
		if($role_id==32){
			if(!empty($batch_id)){
				$student->whereRAW("tbl_registration.batch_id IN ".$batch_id);
			}
		}else{
			if(!empty($batch_id)){
				$student->where('batch_id',$batch_id);
			}
		}
		
		if(!empty($reg_no)){
			$student->where('reg_number',$reg_no);
		}
		
		if($anuprati_id=='Yes'){
			$student->whereRaw(DB::raw("cast like '%Anupriti Yojna-2023-24%'"));
		}
		
		$student=$student->get();
		// echo $batch_id;
		// print_r($student);
		// die();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			foreach($student as $key => $val){
				$attendance = DB::table("student_attendance")->select('*',DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as pdate"))->where('reg_no',$val->reg_number)->whereRAW("date like '".$month."%'");
				
				if($anuprati_id=='Yes'){
					$attendance->whereRaw(DB::raw("cast like '%Anupriti Yojna-2023-24%'"));
				}
				
				if(Auth::user()->id!=901){
					$attendance->where('user_id','!=',901);
				}
				
				
				$attendance = $attendance->get();
				
				
				$cmonth = explode('-',$month);
				$yr = $cmonth[0];
				$mt = $cmonth[1];

				
				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
				$i=1;
				$totalDays=$daysInMonth;
				$total_present = 0;
				$attendance=json_decode(json_encode($attendance),true);
				while($daysInMonth>0){
					$data[$i]='A';
					$add_get_date  = date('Y-m-d', $first_date);

					$daysInMonth--;
					$first_date += 86400; 
					$atdIndex=array_search($add_get_date,array_column($attendance,'pdate'));
					if($atdIndex!== false){ 
					 $data[$i]='P';
					 $total_present++;
					}
					$i++;
				}
				
				
				$data['s_name']=$val->s_name;
				$data['s_regnumber']=$val->reg_number;
				$data['total_present']=$total_present;
				$data['total_absent']=$totalDays-$total_present;
				
				$data_f[]=$data;
			}
			
			return $data_f;
		}else{
			return $data_f;
		}
					
	}
	
	public function student_get_inventory(Request $request){
		$reg_no = $request->reg_no;
		
		$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no','cast')
				->where('reg_number',$reg_no)
				->first();
					
		return view('admin.batchinventory.student-get-inventory',compact('student'));
	}
	
	public function student_assign_inventory(Request $request,$reg_no,$id){
		if(!empty($reg_no) && !empty($id)){
			$previous_inventory = DB::connection('mysql2')->table("tbl_registration")
				->select('assign_inventory','batch_id','reg_number','rfid_no')
				->where('reg_number',$reg_no)
				->first();
				
		
			$new_assign  = $previous_inventory->assign_inventory.','.$id;
			
			$data = array(
				"assign_inventory"	=>	$new_assign
			);
			
			DB::connection('mysql2')->table("tbl_registration")->where('reg_number',$reg_no)->update($data);
			
			$iData = array(
				"user_id"			=>	Auth::user()->id,
				"batch_id"			=>	$previous_inventory->batch_id,
				"registration_no"	=>	$previous_inventory->reg_number,
				"inventory_id"		=>	$id,
				"rfid_no"			=>	$previous_inventory->rfid_no,
				"type"				=>	'web',
			);
			DB::table('given_inventory')->insert($iData);
			
			return redirect()->back()->with('success', 'Inventory Assign Successfully');
		}else{			
			return redirect()->route('admin.student-get-inventory')->with('error', 'Required filed missing!!');		
		}
	}
	
	public function anuprati_dashboard(Request $request){
		$branch_location	=	$request->branch_location;
		$reg_no				=	$request->reg_no;
		$batch_id			=	$request->batch_id;
		$allbatch_ids		=	$request->allbatch_ids;
		$fdate				=	Input::get('fdate');
		$tdate				=	Input::get('tdate');
		
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','rfid_no','cast','contact','reg_date','batch_date','cast')
				->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id')
				->where('tbl_registration.cast','Anupriti Yojna-2023-24')
				// ->whereRaw("(tbl_registration.cast = 'Anupriti Yojna-2023-24' OR tbl_registration.cast = 'Anuprati Yojana' OR tbl_registration.cast = 'Anupriti Yojna-2022-23' OR tbl_registration.cast = 'Anupriti Yojna-2023-24 (Ph-2)')")
				->orderby('tbl_registration.batch_id');
				
		if(!empty($branch_location)){
			$student->where('tbl_batch.branch',$branch_location);
		}
		
		if(!empty($reg_no)){
			$student->where('tbl_registration.reg_number',$reg_no);
		}
		
		if(!empty($allbatch_ids)){
			$allbatch_ids=explode(",",$allbatch_ids);
			$student->whereIN("tbl_registration.batch_id", $allbatch_ids);
		}else if(!empty($batch_id)){
			$student->where('tbl_registration.batch_id',$batch_id);
		}
		$student = $student->paginate(50);
					
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		return view('admin.batchinventory.anuprati',compact('student','pageNumber','params'));
	}
	
	
	public function anuprati_report_excel(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
	
		$reg_no 			= Input::get('reg_no');
		$branch_location 	= Input::get('branch_location');
		$allbatch_ids	 	= Input::get('allbatch_ids');
		$branch_id 			= Input::get('branch_id');
		$batch_id 			= Input::get('batch_id');
		
		$comman_result	=	DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','rfid_no','cast','contact','reg_date','batch_date','cast')
				->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id')
				// ->where('tbl_registration.cast','Anupriti Yojna-2023-24')
				->whereRaw("(tbl_registration.cast = 'Anupriti Yojna-2023-24' OR tbl_registration.cast = 'Anuprati Yojana' OR tbl_registration.cast = 'Anupriti Yojna-2022-23' OR tbl_registration.cast = 'Anupriti Yojna-2023-24 (Ph-2)')")
				->orderby('tbl_registration.batch_id');
				
		if(!empty($branch_location)){
			$comman_result->where('tbl_batch.branch',$branch_location);
		}
		
		if(!empty($reg_no)){
			$comman_result->where('tbl_registration.reg_number',$reg_no);
		}
		
		if(!empty($allbatch_ids)){
			$allbatch_ids=explode(",",$allbatch_ids);
			$comman_result->whereIN("tbl_registration.batch_id", $allbatch_ids);
		}else if(!empty($batch_id)){
			$comman_result->where('tbl_registration.batch_id',$batch_id);
		}
		
		$comman_result = $comman_result->get();
		
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 	
				$query = 'SELECT  count(DISTINCT DATE_FORMAT(date, "%Y-%m-%d"))  as total_present FROM `student_attendance` where batch_id='.$valAtt->batch_id.' AND reg_no='.$valAtt->reg_number.' group BY reg_no';
				$persent = DB::select($query);
				
			
				$reg_date	=	date('Y-m-d', strtotime($valAtt->reg_date));
				$batch_date	=	date('Y-m-d', strtotime($valAtt->batch_date));
				
				if($reg_date > $batch_date){
					$batch_date = $reg_date;
				}
				
				$query = 'SELECT  batch_id,count(DISTINCT DATE_FORMAT(date, "%Y-%m-%d"))  as total_present FROM `student_attendance` where batch_id='.$valAtt->batch_id.' group BY batch_id';
				$total = DB::select($query);
				
				if(count($total) > 0 && count($persent) > 0){
					$total=$total[0]->total_present;
					$persent=$persent[0]->total_present;
					$percent = ceil(($persent*100)/$total);
				}else{
					$percent = 0;
				}
				
				$responseArray[$key]['reg_no'] 		= $valAtt->reg_number;
				$responseArray[$key]['student'] 	= $valAtt->s_name;
				$responseArray[$key]['cast'] 		= $valAtt->cast;
				$responseArray[$key]['batch_id'] 	= $valAtt->batch_id;
				$responseArray[$key]['batch'] 		= $valAtt->batch;
				$responseArray[$key]['percentage'] 	= $percent;
			}
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new AnupritiExport($responseArray), 'AnupritiExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	} 
	
	public function stu_invlid_punch(Request $request){
		$sdate =	$request->sdate;
		
		if(!empty($sdate)){
			$where = $sdate;
		}else{
			$where = date('Y-m-d');
		}
		
		$getpunch = DB::select("SELECT count(`branch_id`) as countbranch,`branch_id`,branches.name  FROM `student_attendance` left join branches ON branches.id = student_attendance.branch_id WHERE `date` LIKE '%".$where."%' AND `branch_id` != 0  group by branch_id  
ORDER BY countbranch DESC");
		
		return view('admin.batchinventory.student-invalid-punch',compact('getpunch'));
	}

	public function student_inventory_track(Request $request){
		$reg_no = $request->reg_no;
		
		$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no','cast')
				->where('reg_number',$reg_no)
				->first();
					
		return view('admin.batchinventory.student-inventory-track',compact('student','reg_no'));
	}
}
