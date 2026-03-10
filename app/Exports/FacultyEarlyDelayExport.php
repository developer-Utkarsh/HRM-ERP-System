<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use DB;
use Auth;

class FacultyEarlyDelayExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{

	public function registerEvents(): array
	{
		return [
			AfterSheet::class    => function(AfterSheet $event) {
				$cellRange = 'A1:W1';
				$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
			},
		];
	}

	public function __construct($get_faculty,$selectFromDate,$selectToDate,$delay_type){ 
		$this->get_faculty = $get_faculty;  
		$this->selectFromDate = $selectFromDate;  
		$this->selectToDate = $selectToDate;  
		$this->delay_type = $delay_type;  
	}

	public function headings(): array
	{
		return [
			//"S. No.",
			"Faculty Name",
			"Date",
			"Schedule From",
			"Schedule To",
			"Spent From",	
			"Spent To",	
			"Early",
			"Delay",
			"Delay Type",
			"Total Early Time",
			"Total Delay Time",
			"Early/Delay Reason",
			"Faculty Delay Reason",
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
		$dataFound = 1;
		$s_no = 0;
		$get_faculty = $this->get_faculty;  
		$selectFromDate = $this->selectFromDate;  
		$selectToDate = $this->selectToDate;  
		$delay_type = $this->delay_type;  
		
		$export_data = [];
		
		if (count($get_faculty) > 0) {
		foreach ($get_faculty as $key2=>$get_faculty_value) {
		$whereCond = '1=1';
		if(!empty($selectFromDate) || !empty($selectToDate)){
				$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}

		if(!empty($delay_type)){
			$whereCond .= ' AND start_classes.delay_type= "'.$delay_type.'"';
		}	
		
		if(!empty($get_faculty_value->faculty_name)){
		    $get_faculty_timetable = DB::table('timetables')
		    ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile','start_classes.early_delay_reason','start_classes.delay_type',
		  	  'start_classes.delay_status',
			  'start_classes.delay_faculty_reason')
		    ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
		    ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
		    ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
		    ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
		    ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
		    ->leftJoin('chapter', 'chapter.id', '=', 'timetables.chapter_id')
		    ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
		    ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
		   ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
		   ->where('timetables.time_table_parent_id', '0')
		   ->where('timetables.is_deleted', '0');
		   
		   if(Auth::user()->role_id == 3){
			$get_faculty_timetable->where('timetables.assistant_id', Auth::user()->id);
		   }						  
		    
		    $get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)
				->orderBy('timetables.cdate', 'ASC')
				->orderBy('timetables.from_time', 'ASC')->get();
								  
			//echo "<pre>"; print_r($get_faculty_timetable); die;
			$duration  = "00 : 00 Hours"; 
			$schedule_duration  = "00 : 00 Hours"; 
			
			
			$total_early_schedule = new DateTime('00:00');
			$total_base_early_schedule = new DateTime('00:00');
			
			$total_delay_schedule = new DateTime('00:00');
			$total_base_delay_schedule = new DateTime('00:00');
								
								
			if(count($get_faculty_timetable) > 0){ 					
	    		foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
					$early = '0';
					if(!empty($get_faculty_timetable_value->start_classes_end_time) && $get_faculty_timetable_value->to_time > $get_faculty_timetable_value->start_classes_end_time){
						$early_from_time         = new DateTime($get_faculty_timetable_value->to_time);
						$early_to_time           = new DateTime($get_faculty_timetable_value->start_classes_end_time);
						$early_schedule_interval = $early_from_time->diff($early_to_time); 
						$early       = $early_schedule_interval->format('%H : %I Hours');
						$total_base_early_schedule->add($early_schedule_interval); 
					}
					
					$delay = '0';
					if(!empty($get_faculty_timetable_value->start_classes_start_time) && $get_faculty_timetable_value->from_time < $get_faculty_timetable_value->start_classes_start_time){
						$delay_from_time         = new DateTime($get_faculty_timetable_value->start_classes_start_time);
						$delay_to_time           = new DateTime($get_faculty_timetable_value->from_time);
						$delay_schedule_interval = $delay_from_time->diff($delay_to_time); 
						$delay       = $delay_schedule_interval->format('%H : %I Hours');
						$total_base_delay_schedule->add($delay_schedule_interval); 
					}
				
					$s_no++;
					//$export_data[$dataFound][] = $s_no ;
					
					if(isset($get_faculty_value->faculty_name) && !empty($get_faculty_value->faculty_name)){
						$export_data[$dataFound][] = $get_faculty_value->faculty_name;
					} else{
						$export_data[$dataFound][] = '';
					}	

					if(isset($get_faculty_timetable_value->cdate) && !empty($get_faculty_timetable_value->cdate)){
						$export_data[$dataFound][] = date("d-m-Y", strtotime($get_faculty_timetable_value->cdate));
					} else{
						$export_data[$dataFound][] = '';
					}	

					
					if(isset($get_faculty_timetable_value->from_time) && !empty($get_faculty_timetable_value->from_time)){
						$export_data[$dataFound][] = date("h:i A", strtotime($get_faculty_timetable_value->from_time));
					} else{
						$export_data[$dataFound][] = '0';
					}
					
					if(isset($get_faculty_timetable_value->to_time) && !empty($get_faculty_timetable_value->to_time)){
						$export_data[$dataFound][] = date("h:i A", strtotime($get_faculty_timetable_value->to_time));
					} else{
						$export_data[$dataFound][] = '0';
					}
					
					
					if(isset($get_faculty_timetable_value->start_classes_start_time) && !empty($get_faculty_timetable_value->start_classes_start_time)){
						$export_data[$dataFound][] = date("h:i A", strtotime($get_faculty_timetable_value->start_classes_start_time));
					} else{
						$export_data[$dataFound][] = '0';
					}
					
					if(isset($get_faculty_timetable_value->start_classes_end_time) && !empty($get_faculty_timetable_value->start_classes_end_time)){
						$export_data[$dataFound][] = date("h:i A", strtotime($get_faculty_timetable_value->start_classes_end_time));
					} else{
						$export_data[$dataFound][] = '0';
					}	

					
					$export_data[$dataFound][] = !empty($early) ? $early : '0';
								
					$export_data[$dataFound][] = !empty($delay) ? $delay : '0';		
					$export_data[$dataFound][] = $get_faculty_timetable_value->delay_type;		
					
					$export_data[$dataFound][] = '';
					$export_data[$dataFound][] = '';
					$export_data[$dataFound][] = $get_faculty_timetable_value->early_delay_reason;
					$export_data[$dataFound][] = $get_faculty_timetable_value->delay_faculty_reason;
					$dataFound++;       
	    		}

	    		$totalDays = $total_early_schedule->diff($total_base_early_schedule)->format("%a");
				$totalHours = $total_early_schedule->diff($total_base_early_schedule)->format("%H");
				$totalMinute = $total_early_schedule->diff($total_base_early_schedule)->format("%I");
				
				$baseDays = $total_delay_schedule->diff($total_base_delay_schedule)->format("%a");
				$baseHours = $total_delay_schedule->diff($total_base_delay_schedule)->format("%H");
				$baseMinute = $total_delay_schedule->diff($total_base_delay_schedule)->format("%I");
				
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = '';
				$export_data[$dataFound][] = 'Total Early Time';
				$export_data[$dataFound][] = ($totalDays*24)+$totalHours. ":" . $totalMinute . 'Hours';
				$export_data[$dataFound][] = 'Total Delay Time';
				$export_data[$dataFound][] = ($baseDays*24)+$baseHours. ":" . $baseMinute . 'Hours';
				
				$dataFound++;
			}
	    } 
	}
	}

	return collect($export_data);	
    	
    }
}
