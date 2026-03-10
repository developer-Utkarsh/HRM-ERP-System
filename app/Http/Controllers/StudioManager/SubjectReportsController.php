<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Subject;
use Input;
use Excel;
use App\Exports\SubjectExport;

class SubjectReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = Input::get('branch_id');
        $subject_id = Input::get('subject_id');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$conditions = array();
		
		if (!empty($subject_id)){
			$conditions['id'] = $subject_id;
        }

        $search    = Input::get('search');
		$get_data=array();
		if(!empty($search)){
			$get_data = Subject::with([
				'timetable'=>function ($q) use ($fdate,$tdate) {
					if(!empty($fdate) && !empty($tdate)){
						$q->where('cdate', '>=', $fdate);
						$q->where('cdate', '<=', $tdate);
					}
					else{
						$q->where('cdate', '=', date('Y-m-d'));
					}
				},
				'timetable.studio'=>function ($q) use ($branch_id) {
					if(!empty($branch_id)){
						$q->where('branch_id', $branch_id);
					}
				},
				'timetable.studio.branch',
				'timetable.faculty', 'timetable.subject'])->where($conditions);
				
			if(!empty($faculty_id)){
				$get_data->WhereHas('timetable', function ($q) use ($faculty_id) {
					$q->where('id', $faculty_id);
				});
			}
			
			if(!empty($branch_id)){
				$get_data->WhereHas('timetable.studio.branch', function ($q) use ($branch_id) {
					$q->where('branch_id', $branch_id);
				});
			}
			
			$get_data = $get_data->where('status',1)->orderBy('id', 'desc')->get();
		}
		
        return view('studiomanager.subject_reports.index', compact('get_data'));
    }
	
	public function download_excel()
    {
		$branch_id = Input::get('branch_id');
        $subject_id = Input::get('subject_id');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$conditions = array();
		
		if (!empty($subject_id)){
			$conditions['id'] = $subject_id;
        }
		
		$get_data = Subject::with([
			'timetable'=>function ($q) use ($fdate,$tdate) {
				if(!empty($fdate) && !empty($tdate)){
					$q->where('cdate', '>=', $fdate);
					$q->where('cdate', '<=', $tdate);
				}
			},
			'timetable.studio'=>function ($q) use ($branch_id) {
				if(!empty($branch_id)){
					$q->where('branch_id', $branch_id);
				}
			},
			'timetable.studio.branch',
			'timetable.faculty', 'timetable.batch', 'timetable.course', 'timetable.subject', 'timetable.chapter', 'timetable.topic'])->where($conditions);
			
		if(!empty($faculty_id)){
			$get_data->WhereHas('timetable', function ($q) use ($faculty_id) {
				$q->where('id', $faculty_id);
			});
		}
		
		if(!empty($branch_id)){
			$get_data->WhereHas('timetable.studio.branch', function ($q) use ($branch_id) {
				$q->where('branch_id', $branch_id);
			});
		}
		
		$get_data = $get_data->where('status',1)->orderBy('id', 'desc')->get();
		
		
        if(!empty($get_data)){
            return Excel::download(new SubjectExport($get_data), 'SubjectWiseData.xlsx'); 

        } else{
            return redirect()->route('studiomanager.timetables.export')->with('error', 'Please Select Checkbox');
        }
    }
}
