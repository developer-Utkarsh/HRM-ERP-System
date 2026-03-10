<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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


class BatchInventoryController extends Controller
{
	public function index(){		
		$logged_id  = Auth::user()->id;	
		$role_id    = Auth::user()->role_id;	
		
	
		$name = Input::get('name');
        $status = Input::get('status');

        $inventory = BatchInventory::where('status',"!=", '2')->orderBy('id', 'desc');

        if (!empty($name)){
            $inventory->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $inventory->where('status', '=', '0');
            }else{
                $inventory->where('status', '=', '1');
            }
        }

        $inventory = $inventory->get();
		
		return view('studiomanager.batchinventory.index', compact('inventory')); 
	}
	
    public function add(){		
		$logged_id       = Auth::user()->id;
		$batchs = Batch::where('status', '1')->where('is_deleted', '0')->get();
		
		return view('studiomanager.batchinventory.add',compact('batchs'));		
	}
	
	public function save(Request $request){
		$logged_id       = Auth::user()->id;
				
		$validatedData = $request->validate([
            'name' => 'required',
            // 'batch_code' => 'required',
            'type' => 'required',
            'quantity' => 'required',
        ]);
		
        $inputs = $request->only('name','type','quantity','inventory_type');
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
		$inputs['created_by'] = $logged_id;
		
		$batch_inventory = BatchInventory::create($inputs);    

		if ($batch_inventory->save()) {
			return redirect()->route('studiomanager.batchinventory.index')->with('success', 'Added Successfully');
		} else {
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	public function edit($id)
    {
        $inventory = BatchInventory::find($id);
		$batchs = Batch::where('status', '1')->where('is_deleted', '0')->get();
        return view('studiomanager.batchinventory.edit', compact('inventory','batchs'));
    }
	
	public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:batch_inventory,name,'.$id,
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
            return redirect()->route('studiomanager.batchinventory.index')->with('success', 'Updated Successfully');
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
		$fdate		=	Input::get('fdate');
		$tdate		=	Input::get('tdate');
		$batch_id	=	Input::get('batch_id');
		$branch_id	=	Input::get('branch_id');
		$allbatch_ids= Input::get('allbatch_ids');
		
		// $branch = Branch::where('status',1)->get();		
		/*
		$data = array();
		if(!empty($request->search)){
			if(!empty($request->batch_id)){
				$get_batch  = Batch::where('id', $request->batch_id)->first();
				if(!empty($get_batch->batch_code)){
					$batch_code = $get_batch->batch_code;
					$batch_code = explode(',',$get_batch->batch_code);
					 print_r($batch_code); 
					
					$query = DB::connection('mysql2')->table("tbl_registration")->select(DB::raw("count(batch) as total_admission"))->where("batch_id", $batch_code)->get();
				}
			}
		}
		*/
		
		$query	= array();
		if(!empty($branch_id)){
			$query = DB::connection('mysql2')->table("tbl_registration")
				->select(DB::raw("count(batch) as total_admission"),DB::raw('(select count(batch) from tbl_registration where batch_id=tbl_batch.Bat_id and gender="male") as total_male'),'tbl_registration.batch_id','tbl_batch.batch_name')
				->leftJoin('tbl_batch','tbl_batch.Bat_id','tbl_registration.batch_id');
			if(!empty($allbatch_ids) AND empty($batch_id)){
			   $batch_id=explode(",",$allbatch_ids);
              $query->whereIN("tbl_registration.batch_id", $batch_id);
			}else if(!empty($request->batch_id)){
				$query->where("tbl_registration.batch_id", $batch_id);
			}
			else{
				$query->where("tbl_batch.batch_running_status", 'Running');
			}
			
			$query->groupby('tbl_registration.batch_id');			
			$query = $query->get();
		}else{
	      $msg="Select Branch";
		}			
			
		
		return view('studiomanager.batchinventory.attendance-dashboard', compact('query','fdate','tdate','branch_id','allbatch_ids'));
	}

	public function studentSearch(Request $request){
		$reg_no=$request->reg_no;
		$month=$request->month;
		$data=[];
		$status=false;
		$msg='No data found';
		if(!empty($reg_no) && !empty($month)){
			$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory')->where('reg_number',$reg_no)->first();
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

               $st_inventory=[];
               $assign_inventory=$student->assign_inventory;
               if($assign_inventory!=""){
                 $assign_inventory=explode(",",$assign_inventory);
               }
               $inventory = DB::table('batch_inventory')
                    ->select('*')
					->where('status','1')
					->where('batch_code',$student->batch_id)
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
				    $st_inventory[]=$val;
				}
                
				$data['student']=$student;
				$data['attendance']=$attendance;
				$data['st_inventory']=$st_inventory;
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
		$batch = Batch::where('status','1')->get();
		$batch_id = $request->batch_id;
		
		if(!empty($batch_id)){
			$inventory = DB::table('batch_inventory')
					->where('status','1')
					->whereRaw("(type = 'all' OR batch_code = ".$batch_id.")")
					->get();
		}else{
			$inventory = array();
		}
		
		return view('studiomanager.batchinventory.inventory-dashboard',compact('batch','inventory','batch_id'));
	}
	
	public function student_attendance_view(Request $request,$batch_id,$fdate=null,$type){		
		
		$attendance = DB::table('student_attendance')
						->select('student_attendance.*','users.name as uname')
						->leftjoin('users','users.id','student_attendance.user_id')
						->where('batch_id',$batch_id);		
		if (!empty($fdate)) {  
			$attendance->whereRaw(DB::raw("DATE(date) = '".$fdate."'"));
		}else{
			$attendance->whereRaw(DB::raw("DATE(date) = '".date('Y-m-d')."'"));
		}		
		$attendance = $attendance->get();
		
		// $attendance = DB::connection('mysql2')->table("tbl_registration")
			// ->select('reg_number','batch_id','s_name','contact',)->where("tbl_registration.batch_id", $batch_id)->get();	
		
		return view('studiomanager.batchinventory.student-attendance-view',compact('attendance','fdate','type','batch_id'));	
	}
	
	public function getBatch(Request $request){
		$ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
		$ttdate=date('Y-m-d');
		$batch = DB::table('batch')
					->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
					->leftjoin('timetables as tt','tt.batch_id','batch.id')
					->where('tt.is_deleted', '0')
					->where('tt.is_publish', '1')
					->where('tt.is_cancel', 0)
					->where('tt.cdate','=',$ttdate)
					->where('batch.batch_code','!=',0)
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
		
		return view('studiomanager.batchinventory.student-inventory-view',compact('inventory','batch_id','id','type'));	
	}
}
