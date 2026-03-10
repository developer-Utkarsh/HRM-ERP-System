<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class BatchReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct($get_batches){ 
		$this->get_batches = $get_batches;  
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Batch Name",
			"ERP Course ID",
			"Branch Name",
			"Studio Name",
			"Date",
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
		$get_batches = $this->get_batches;

    		$export_data = [];
			$export_data_all = [];
			$dataFound = 0;
			
    		foreach($get_batches as $key2 => $batchArray){
				
				foreach ($batchArray->batch_timetables as $key => $value) {

					$schedule_duration  = "00 : 00 Hours"; 	
					$from_time         = new DateTime($value->from_time);
					$to_time           = new DateTime($value->to_time);
					$schedule_interval = $from_time->diff($to_time);
					$schedule_duration = $schedule_interval->format('%H : %I Hours');
					if(!empty($value->studio) && !empty($value->studio->branch->name)){
						$export_data[$dataFound][] = $dataFound + 1 ;

						if(isset($batchArray->name) && !empty($batchArray->name)){
							$export_data[$dataFound][] = $batchArray->name;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(isset($batchArray->erp_course_id) && !empty($batchArray->erp_course_id)){
							$export_data[$dataFound][] = $batchArray->erp_course_id;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						
						if(isset($value->studio->branch->name) && !empty($value->studio->branch->name)){
							$export_data[$dataFound][] = $value->studio->branch->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->studio->name) && !empty($value->studio->name)){
							$export_data[$dataFound][] = $value->studio->name;
						} else{
							$export_data[$dataFound][] = '';
						}
						
						if(isset($value->cdate) && !empty($value->cdate)){
							$export_data[$dataFound][] = $value->cdate;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->from_time) && !empty($value->from_time)){
							$export_data[$dataFound][] = date('h:i A', strtotime($value->from_time));
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->to_time) && !empty($value->to_time)){
							$export_data[$dataFound][] = date('h:i A', strtotime($value->to_time));
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($schedule_duration) && !empty($schedule_duration)){
							$export_data[$dataFound][] = $schedule_duration;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->faculty->name) && !empty($value->faculty->name)){
							$export_data[$dataFound][] = $value->faculty->name;
						} else{
							$export_data[$dataFound][] = '';
						}

						if(isset($value->subject->name) && !empty($value->subject->name)){
							$export_data[$dataFound][] = $value->subject->name;
						} else{
							$export_data[$dataFound][] = '';
						}
						

						//$export_data_all[$dataFound] = $export_data;
					
						$dataFound++;
					}
				}
    		}

    		return collect($export_data);	
    	
    }
}
