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

class FacultyHoursExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct($get_faculty,$selectFromDate,$selectToDate,$branch_location,$online_class_type){ 
		$this->get_faculty = $get_faculty;  
		$this->selectFromDate = $selectFromDate;  
		$this->selectToDate = $selectToDate;  
		$this->branch_location = $branch_location;  
		$this->online_class_type = $online_class_type;  
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Name",
			"Contact No",
			"Department",
			"Subject",
			"A. Time",
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
		$selectFromDate = $this->selectFromDate;
		$selectToDate = $this->selectToDate;
		$branch_location = $this->branch_location;
		$online_class_type = $this->online_class_type;

    		$export_data = [];
			$export_data_all = [];
			$dataFound = 0;
			$s_no = 0;
    		foreach($get_faculty as $key => $get_faculty_val){  
				
				$f_date = date('Y-m-d'); $t_date = date('Y-m-d');
				if(!empty($selectFromDate)){
					$f_date = $selectFromDate;
				}
				if(!empty($selectToDate)){
					$t_date = $selectToDate;
				}

				$whereCond  = ' 1=1';
									
				if(!empty($branch_location)){
					$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
				}
				
				if(!empty($online_class_type)){
					$whereCond .= ' AND timetables.online_class_type = "'.$online_class_type.'"';
				}
				
				$get_total_time = DB::table('timetables')
								->select('timetables.from_time as start_time','timetables.to_time as end_time','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
								->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
								->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
								->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
								->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
								->where('timetables.faculty_id', $get_faculty_val->id)
								->where('timetables.time_table_parent_id', '0')
								->where('timetables.is_deleted', '0')
								->whereRaw($whereCond)
								->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
								->get(); 	

								
								$base_time2          = new DateTime('00:00');
								$base_time          = new DateTime('00:00');
								$total              = new DateTime('00:00');
								$total2              = new DateTime('00:00');
								$subject_arr        = array();
								$schedule_total_tt           = "00 : 00 Hours"; 
								$total_tt           = "00 : 00 Hours"; 
								if(count($get_total_time) > 0){
									foreach($get_total_time as $get_total_time_value){
										array_push($subject_arr, $get_total_time_value->name);
										$first_time = new DateTime($get_total_time_value->start_time);
										$second_time = new DateTime($get_total_time_value->end_time);
										$interval = $first_time->diff($second_time);
										$base_time->add($interval);


										$first_date = new DateTime($get_total_time_value->start_classes_start_time);
										$second_date = new DateTime($get_total_time_value->start_classes_end_time);
										$interval = $first_date->diff($second_date);
										$base_time2->add($interval); 											
									}
									
									$baseDays = $total->diff($base_time)->format("%a");
									$baseHours = $total->diff($base_time)->format("%H");
									$baseMinute = $total->diff($base_time)->format("%I");
									
									$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
									
									$totalDays = $total2->diff($base_time2)->format("%a");
									$totalHours = $total2->diff($base_time2)->format("%H");
									$totalMinute = $total2->diff($base_time2)->format("%I");
									
									$total_tt = ($totalDays*24)+$totalHours. ":" . $totalMinute;
								}

				if(!empty($branch_location) ){
					if(count($get_total_time) > 0){
						$s_no++;
						$export_data[$dataFound][] = $s_no ;

						if(isset($get_faculty_val->name) && !empty($get_faculty_val->name)){
							$export_data[$dataFound][] = $get_faculty_val->name;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(isset($get_faculty_val->mobile) && !empty($get_faculty_val->mobile)){
							$export_data[$dataFound][] = $get_faculty_val->mobile;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(isset($get_faculty_val->department_name) && !empty($get_faculty_val->department_name)){
							$export_data[$dataFound][] = $get_faculty_val->department_name;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(count($subject_arr) > 0){
							$export_data[$dataFound][] = implode(",", array_unique($subject_arr));
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(!empty($schedule_total_tt)){
							$export_data[$dataFound][] = $schedule_total_tt. ' Hours';
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(!empty($total_tt)){
							$export_data[$dataFound][] = $total_tt. ' Hours';
						} else{
							$export_data[$dataFound][] = '';
						}
						
					}
				}
				else{
					$s_no++;
					$export_data[$dataFound][] = $s_no ;

					if(isset($get_faculty_val->name) && !empty($get_faculty_val->name)){
						$export_data[$dataFound][] = $get_faculty_val->name;
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(isset($get_faculty_val->mobile) && !empty($get_faculty_val->mobile)){
						$export_data[$dataFound][] = $get_faculty_val->mobile;
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(isset($get_faculty_val->department_name) && !empty($get_faculty_val->department_name)){
						$export_data[$dataFound][] = $get_faculty_val->department_name;
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(count($subject_arr) > 0){
						$export_data[$dataFound][] = implode(",", array_unique($subject_arr));
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(isset($get_faculty_val->committed_hours) && !empty($get_faculty_val->committed_hours)){
						if($get_faculty_val->agreement=='Yes'){
							$export_data[$dataFound][] = $get_faculty_val->committed_hours;
						}
						else{
							$export_data[$dataFound][] = "-";
						}
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(!empty($schedule_total_tt)){
						$export_data[$dataFound][] = $schedule_total_tt. ' Hours';
					} else{
						$export_data[$dataFound][] = '';
					}
					
					if(!empty($total_tt)){
						$export_data[$dataFound][] = $total_tt. ' Hours';
					} else{
						$export_data[$dataFound][] = '';
					}	
				}

			
				$dataFound++;
    		}

    		return collect($export_data);	
    	
    }
}
