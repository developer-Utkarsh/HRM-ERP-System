<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StaffMovementSystem;
use Input;
use DB;
use App\NewTask;
use Auth;
use Excel;
use App\Exports\StaffmovementsystemExport;


class StaffmovementsystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
		$logged_id       = Auth::user()->id;
		$logged_role_id       = Auth::user()->role_id;
        $logid           = array(); 
            //$users           = NewTask::getEmployeeByLogID($logged_id); 		
	    $emp_id = Input::get('emp_id');
		$fdate  = Input::get('fdate');
		$tdate  = Input::get('tdate');
		
		$staff_report = StaffMovementSystem::with('employee');
		
		if(!empty($emp_id)){
			$staff_report->where('emp_id',$emp_id);		
		}
		
		if (!empty($fdate) && !empty($tdate)){				
			$staff_report->where('cdate', '>=', $fdate);					
			$staff_report->where('cdate', '<=', $tdate);
		}
		else{
			$staff_report->where('cdate', date('Y-m-d'));
		}
		// else{
			// $staff_report->where('cdate', date('Y-m-d'));
		// }
		// $logid[] = $logged_id;
		// foreach($users as $usersvalue){	
	        // $logid[] = $usersvalue['id'];
	    // } 	
	    // $staff_report->whereIn('emp_id', $logid);	

        if($logged_role_id != 29 && $logged_role_id != 24){ 
			$staff_report->WhereHas('employee', function ($q) use ($logged_id) {				
				$q->whereRaw('(id = "'.$logged_id.'" OR supervisor_id LIKE  \'%"'.$logged_id.'"%\')'); 
			});
		}
		
		$staff_report = $staff_report->where('status','!=','Deleted')->orderBy('cdate', 'desc')->get();
		//echo '<pre>'; print_r($staff_report);die;
		return view('admin.staffmovementsystem.index', compact('staff_report'));		
    }
	
	public function download_excel()
    {    
	    $emp_id = Input::get('emp_id');
		$fdate  = Input::get('fdate');
		$tdate  = Input::get('tdate');
		
		$staff_report = StaffMovementSystem::with('employee');
		
		if(!empty($emp_id)){
			$staff_report->where('emp_id',$emp_id);		
		}
		
		if (!empty($fdate) && !empty($tdate)){				
			$staff_report->where('cdate', '>=', $fdate);					
			$staff_report->where('cdate', '<=', $tdate);
		}
		else{
			$staff_report->where('cdate', date('Y-m-d'));
		}
			
		$staff_report = $staff_report->where('status','!=','Deleted')->orderBy('cdate', 'desc')->get();
	
		
        if(count($staff_report) > 0){
            return Excel::download(new StaffmovementsystemExport($staff_report), 'StaffmovementsystemData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }

	public function togglePublish(Request $request, $id = ""){ 
        if ($request->newStatus) {

			$staff_result = StaffMovementSystem::where('id', $id)->update([ 'status' => $request->newStatus]);

			if($staff_result){
				return redirect()->back()->with('success', 'Staff Updated Successfully');
			}
			else {
				return redirect()->back()->with('error', 'Something Went Wrong !');
			}
            
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
}
