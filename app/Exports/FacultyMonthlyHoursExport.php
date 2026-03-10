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

class FacultyMonthlyHoursExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct($get_faculty,$month){ 
		$this->get_faculty = $get_faculty;  
		$this->month = $month;   
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Name",
			"Month",
			"Year",
			"Schedule Time",
			"Spent Time"	
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
		$dataFound = 1;
		$get_faculty = $this->get_faculty;
		$month = $this->month;
		$explode_month = explode("-", $month);
		$month_res = $explode_month[1];
		$year_res = $explode_month[0];
		
		$export_data = [];
		$export_data_all = [];
		$dataFound = 0;
		$s_no = 0;
    		foreach($get_faculty as $key => $get_faculty_val){ 

				$whereCond  = ' 1=1';
				$get_total_time = DB::table('timetables')
								->select('timetables.from_time as start_time','timetables.to_time as end_time','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
								->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
								->where('timetables.faculty_id', $get_faculty_val->id)
								->where('timetables.time_table_parent_id', '0')
								->where('timetables.is_deleted', '0')
								->whereRaw($whereCond)
								->whereRaw(' MONTH(timetables.cdate) = "'.$month_res.'"')
								->get(); 	
				
										
				$duration            = "00 : 00 Hours"; 
				$schedule_duration   = "00 : 00 Hours"; 
				$base_time           = new DateTime('00:00');
				$total               = new DateTime('00:00');
				$total_schedule      = new DateTime('00:00');
				$total_base_schedule = new DateTime('00:00');
				
				
				if(count($get_total_time) > 0){
					foreach($get_total_time as $get_total_time_value){
						
						$first_date = new DateTime($get_total_time_value->start_classes_start_time);
						$second_date = new DateTime($get_total_time_value->start_classes_end_time);
						$interval = $first_date->diff($second_date);
						$duration = $interval->format('%H : %I Hours');
						$base_time->add($interval); 
								
								
						$first_time = new DateTime($get_total_time_value->start_time);
						$second_time = new DateTime($get_total_time_value->end_time);
						$schedule_interval = $first_time->diff($second_time);
						$schedule_duration = $schedule_interval->format('%H : %I Hours');
						$total_base_schedule->add($schedule_interval);  
					}
				}

					$s_no++;
					$export_data[$dataFound][] = $s_no ;

					if(isset($get_faculty_val->name) && !empty($get_faculty_val->name)){
						$export_data[$dataFound][] = $get_faculty_val->name;
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(isset($month_res) && !empty($month_res)){ 
						$export_data[$dataFound][] = $month_res; //dd(date("F", strtotime($month_res)));
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(isset($year_res) && !empty($year_res)){
						$export_data[$dataFound][] = $year_res;
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(!empty($base_time)){
						$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
						$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
						$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								
						$export_data[$dataFound][] =($totalDays*24)+$totalHours. ":" . $totalMinute . " Hours";
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(!empty($base_time)){
						$baseDays = $total->diff($base_time)->format("%a");
						$baseHours = $total->diff($base_time)->format("%H");
						$baseMinute = $total->diff($base_time)->format("%I");
								
						$export_data[$dataFound][] = ($baseDays*24)+$baseHours. ":" . $baseMinute . " Hours";
					} else{
						$export_data[$dataFound][] = '';
					}	
				

			
				$dataFound++;
    		}

    		return collect($export_data);	
    	
    }
}
