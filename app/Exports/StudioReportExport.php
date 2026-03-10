<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class StudioReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct($get_studios){ 
		$this->get_studios = $get_studios;  
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Branch Name",
			"Studio Name",
			"Batch Name",
			"Start Time",
			"End Time",
			"Schedule Time",
            "Faculty Name",
			"Subject Name",		
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
		$get_studios = $this->get_studios;

    		$export_data = [];
			$dataFound = 0;
			
    		foreach ($get_studios as $branchArray) {
				
				foreach ($branchArray->studio as $value) {

					foreach($value->timetable as $key => $timetable){

						$schedule_duration  = "00 : 00 Hours"; 	
						$from_time         = new DateTime($timetable->from_time);
						$to_time           = new DateTime($timetable->to_time);
						$schedule_interval = $from_time->diff($to_time);
						$schedule_duration = $schedule_interval->format('%H : %I Hours');

						
						$export_data[$dataFound][] = $dataFound + 1 ;

						if(isset($branchArray->name) && !empty($branchArray->name)){
							$export_data[$dataFound][] = $branchArray->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->name) && !empty($value->name)){
							$export_data[$dataFound][] = $value->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($timetable->batch->name) && !empty($timetable->batch->name)){
							$export_data[$dataFound][] = $timetable->batch->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($timetable->from_time) && !empty($timetable->from_time)){
							$export_data[$dataFound][] = date('h:i A', strtotime($timetable->from_time));
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($timetable->to_time) && !empty($timetable->to_time)){
							$export_data[$dataFound][] = date('h:i A', strtotime($timetable->to_time));
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($schedule_duration) && !empty($schedule_duration)){
							$export_data[$dataFound][] = $schedule_duration;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($timetable->faculty->name) && !empty($timetable->faculty->name)){
							$export_data[$dataFound][] = $timetable->faculty->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($timetable->subject->name) && !empty($timetable->subject->name)){
							$export_data[$dataFound][] = $timetable->subject->name;
						} else{
							$export_data[$dataFound][] = '';
						}
						
					
						$dataFound++;
						

					}
				}
    		}

    		return collect($export_data);	
    	
    }
}
