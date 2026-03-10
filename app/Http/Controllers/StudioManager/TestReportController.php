<?php

namespace App\Http\Controllers\StudioManager;

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
		return view('studiomanager.test_report.index', compact('get_batches','fdate','tdate'));
        
    }
	
	
	

}
