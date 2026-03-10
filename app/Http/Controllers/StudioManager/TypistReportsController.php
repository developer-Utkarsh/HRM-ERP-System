<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TypistWorkReport;
use Input;
use Excel;
use DB;
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
		
		
        return view('studiomanager.typist_work_report.index', compact('get_typist'));
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
            return redirect()->route('studiomanager.typist_work_report.index')->with('error', 'Something is wrong');
        }
    }
}
