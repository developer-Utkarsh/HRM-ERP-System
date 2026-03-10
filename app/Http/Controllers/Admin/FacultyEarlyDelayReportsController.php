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
use App\Exports\FacultyEarlyDelayExport;
use DateTime;
use Illuminate\Support\Facades\Validator;


class FacultyEarlyDelayReportsController extends Controller
{
	
    public function index()
    {
    	
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		
		$whereCond  = ' 1=1';

		if(!empty($faculty_id) && count($faculty_id) > 0){
			$whereCond .= " AND timetables.faculty_id IN('".implode("','",$faculty_id)."')";
		}
		
		if(!empty($selectFromDate) || !empty($selectToDate)){
			if($selectFromDate!=$selectToDate && (empty($faculty_id) || count($faculty_id) > 10)){
				$get_faculty=[];
				return view('admin.faculty_early_delay_reports.index', compact('get_faculty','selectFromDate','selectToDate'));
               //return redirect()->back()->with('error', 'Either Select faculty or select single date rang.');
			}

			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
				  
		$get_faculty = DB::table('timetables')
		   ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
		  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
		  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
		  ->whereRaw($whereCond)
		  ->where('timetables.time_table_parent_id', '0')
		  ->where('timetables.is_deleted', '0')
		  ->where('timetables.is_publish', '1');
	    if(Auth::user()->role_id == 3){
		  $get_faculty->where('timetables.assistant_id', Auth::user()->id);
	    }
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
			->groupBy('timetables.faculty_id')->get();	  
        //echo "<pre>"; print_r($get_faculty); die;
		
		if(!empty($get_faculty)){
			return view('admin.faculty_early_delay_reports.index', compact('get_faculty','selectFromDate','selectToDate'));
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong!');
		}
        
    }
	
	public function download_excel()
    {   
							
		$faculty_id     = Input::get('faculty_id');	
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
        $delay_type   = Input::get('delay_type');
		
		$whereCond  = ' 1=1';
		
		if(!empty($selectFromDate) || !empty($selectToDate)){
			$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}
		
		if(!empty($faculty_id)){
			$whereCond .= " AND timetables.faculty_id IN('".str_replace(",","','",$faculty_id)."')";
		}
		
		
				  
		$get_faculty = DB::table('timetables')
						  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->where('timetables.is_deleted', '0')
						  ->where('timetables.is_publish', '1');
						  if(Auth::user()->role_id == 3){
							$get_faculty->where('timetables.assistant_id', Auth::user()->id);
						  }
		$get_faculty =	$get_faculty->orderBy('users_faculty.name','asc')
						  ->groupBy('timetables.faculty_id')
						  ->get();		
		
		if(count($get_faculty) > 0){  
			return Excel::download(new FacultyEarlyDelayExport($get_faculty,$selectFromDate,$selectToDate,$delay_type), 'FacultyEarlyDelayReportData.xlsx'); 

		} else{
			return redirect()->back()->with('error', 'Something Went Wrong!');
		}
		 
	}

	public function delayWhatsapp(Request $request){
		$rules = [
            'timetable_id' => 'required|numeric',
            'faculty_id' => 'required|numeric',
            'delay' => 'required',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data, 200);
        }

        DB::table('start_classes')->where('timetable_id',$request->timetable_id)
        ->update(['delay_status'=>1]);

        $this->whatsappForDelay($request->timetable_id,$request->delay);

        return response(['status'=>true,'message'=>'Class Approved Successfully!']);
	}

	public function ttChangeCounts(Request $request){
		$cdate = $request->cdate;
		
		if(empty($cdate)){
			if(date("H:i")>="17:00"){
			   $cdate=date('Y-m-d',strtotime(date("Y-m-d")." +1 day"));
			}else{
				$cdate=date('Y-m-d');
			}
		}
		

		$list=DB::select("SELECT b.branch_location,count(b.id) as record FROM branches as b Left Join timetables as tt ON tt.branch_id=b.id Where tt.cdate='$cdate' AND tt.change_after_publish>0 group by b.branch_location");

		return view('admin.faculty_early_delay_reports.change-counts', compact('list'));
	}
}
