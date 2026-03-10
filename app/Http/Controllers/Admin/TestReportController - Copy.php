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

class TestReportController extends Controller
{
	
    public function test_report()
    {
        $user_id     = Input::get('user_id');		
		$selectFromDate = Input::get('fdate');
		$batch_id = Input::get('batch_id');
		
		
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		if(!empty($selectFromDate)){
		}
		else{
			$selectFromDate = date('Y-m-d');
		}
		
		return view('admin.webview_reports.schedule_test', compact('user_id','selectFromDate','batch_id'));
		
    }
	
	public function test_report_view()
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
		if(!empty(Auth::user()->id)){
			die('Run in mobile app');
		}
		
		$validatedData = $request->validate([
			'q1' => 'required',
			'q2' => 'required',
			// 'q16' => 'required|mimes:jpeg,png,jpg,gif,svg|max:5120',     // 5MB Validation  //1024 = 1MB   
		],
		[
			// 'q1.required' => 'टेस्ट देने वालों की संख्या Required',
			// 'q16.required' => 'गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें Required',
			// 'q16.mimes' => 'गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें only jpeg,png,jpg,gif,svg',
			// 'q16.max' => 'गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें max 1MB',
		]);
		
		$inputs = $request->only('user_id','tt_id','q1','q2','q3','q4','q5','q6','q7','q8','q9','q10','q11','q12','q13','q14','q15','q16','q17','q18','q19','q20');
		
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
			$q->where('schedule_type','test');
			$q->where('is_deleted', '=', '0');
			$q->where('time_table_parent_id', '=', '0');
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
					$q->where('schedule_type','test');
					$q->where('is_deleted', '=', '0');
					$q->where('time_table_parent_id', '=', '0');
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
	
	
	

}
