<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TypistWorkReport;
use Input;
use Excel;
use DB;
use Auth;
use App\Exports\TypistExport;

class TypistReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emp_id    = Input::get('emp_id');
		$fdate     = Input::get('fdate');
        $tdate     = Input::get('tdate'); 

		 
    	$ddd = "111";
		$get_typist = TypistWorkReport::with(['employee'=>function ($q) use ($ddd) {
			//$q->where('role_id', 3);
			$q->where('status', 1);
		}
		]);
		$get_typist->orderBy('cdate', 'desc');
		if(!empty($emp_id)){
			$get_typist->where(['emp_id'=>$emp_id]);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_typist->where('typist_work_report.cdate', '>=', $fdate);
			$get_typist->where('typist_work_report.cdate', '<=', $tdate);
		}
		// else{
		// 	$get_typist->where('cdate', date('Y-m-d')); 
		// }
		$get_typist = $get_typist->get();
		//echo '<pre>'; print_r($get_typist); die;
		
		
        return view('admin.typist_work_report.index', compact('get_typist'));
    }
	
	public function download_excel()
    {
		$emp_id    = Input::get('emp_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		
		$ddd = "111";
		$get_typist = TypistWorkReport::with(['employee'=>function ($q) use ($ddd) {
			$q->where('status', 1);
		}
		]);
		$get_typist->orderBy('cdate', 'desc');
		if(!empty($emp_id)){
			$get_typist->where(['emp_id'=>$emp_id]);
		}
		if(!empty($fdate) && !empty($tdate)){
			$get_typist->where('typist_work_report.cdate', '>=', $fdate);
			$get_typist->where('typist_work_report.cdate', '<=', $tdate);
		}
		// else{
		// 	$get_typist->where('cdate', date('Y-m-d'));
		// }
		$get_typist = $get_typist->get();
		
		
		
        if(!empty($get_typist)){
            return Excel::download(new TypistExport($get_typist), 'TypistWorkData.xlsx'); 

        } else{
            return redirect()->route('admin.typist_work_report.index')->with('error', 'Something is wrong');
        }
    }
	
	
	 public function it_deo_work(Request $request)
    {
		$emp_id	=	$request->emp_id;
		$fdate	=	$request->fdate;
		
		$it_deo = DB::table('it_deo')->where('user_id',901);
		
		if(!empty($emp_id)){
			$it_deo->where('user_id',$emp_id);
		}
		
		if(!empty($fdate)){
			$it_deo->whereDate('created_at',$fdate);
		}else{
			$it_deo->whereDate('created_at',date('Y-m-d'));
		}
		
		$it_deo = $it_deo->get();
		
        return view('admin.typist_work_report.itdeo', compact('it_deo'));
    }
	
	public function add_it_deo_work_report(Request $request){
		return view('admin.typist_work_report.add-it-deo');
	}
	
	public function save_it_deo_work(Request $request){
		$question 	=	$request->question;
		$numberof 	=	$request->numberof;
		$papertype 	=	$request->papertype;
		$timeof		=	$request->timeof;
		
		$check = DB::table('it_deo')->where('user_id',Auth::user()->id)->whereDate('created_at',date('Y-m-d'))->first();
		if(empty($check)){
			for($i=0; $i<count($question); $i++){
				$data = array(
					"question"	=>	$question[$i],
					"number_of"	=>	$numberof[$i],
					"paper"		=>	$papertype[$i],
					"time"		=>	$timeof[$i],
					"user_id"	=>	Auth::user()->id,
				);
				
				DB::table('it_deo')->insert($data);
			}
			
			return redirect()->route('admin.it-deo-work-report')->with('success', 'Added Successfully');
		}else{
			return redirect()->route('admin.it-deo-work-report')->with('error', 'Already Added');
		}
	}
}
