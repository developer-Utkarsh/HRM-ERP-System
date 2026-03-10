<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class NewAttendanceExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct( $get_array){
		$this->get_array = $get_array;
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Employee Code",
			"Employee Name",
			"Branch",
			"Department Type",
			"Date",
			"In Time",
			"Out Time",
			"Duration"
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if (!empty($this->get_array))
		{
			$get_array = $this->get_array;
			$export_data_all = [];
			
			$i = 0;
			$ii = 0;
			foreach ($get_array as $key1 => $value)
			{
				$export_data = []; 
				
				$total_minute = 0;
				$time_count = count($value['time']);
				foreach($value['time'] as $key=>$time_details){
					$i++;
					$export_data[$key][] = $i;
					$export_data[$key][] = isset($value['register_id']) ?  $value['register_id'] : '';
					$export_data[$key][] = isset($value['name']) ?  $value['name'] : '';
					$export_data[$key][] = isset($value['branch']) ?  $value['branch'] : '';
					$export_data[$key][] = isset($value['department']) ?  $value['department'] : '';
					$export_data[$key][] = isset($value['date']) ?  $value['date'] : '';
					if(!empty($time_details['in_time'])){
						$export_data[$key][] =  date("h:i A", strtotime($time_details['in_time']));
					}
					else{
						$export_data[$key][] = "";
					}
					if(!empty($time_details['out_time'])){
						$export_data[$key][] = date("h:i A", strtotime($time_details['out_time']));
					}
					else{
						$export_data[$key][] = "";
					}
					$total_minute = 0;
					if(!empty($time_details['in_time']) && !empty($time_details['out_time'])){
						$intime = new DateTime($time_details['in_time']);
						$outtime = new DateTime($time_details['out_time']);
						$interval = $intime->diff($outtime);
						$hours = $interval->format('%H');
						$minute = $interval->format('%I');
						$total_minute = ($hours*60)+$minute;
					}
					
					$total_hours = "00:00";
					if($total_minute > 0){
						$format = '%02d:%02d';
						$totalhours = floor($total_minute / 60);
						$totalminutes = ($total_minute % 60);
						$total_hours = sprintf($format, $totalhours, $totalminutes);
					}
					$export_data[$key][] = $total_hours ." Hour";
					
					/* if($time_count == ($key+1)){
						$total_hours = "00:00";
						if($total_minute > 0){
							$format = '%02d:%02d';
							$totalhours = floor($total_minute / 60);
							$totalminutes = ($total_minute % 60);
							$total_hours = sprintf($format, $totalhours, $totalminutes);
						}
						$export_data[$key][] = $total_hours ." Hour";
					}
					else{
						$export_data[$key][] = "";
					} */
				}  
				
				$export_data_all[$ii] = $export_data;
				$ii++;
				
			}
			
			return collect($export_data_all);
		}
    }
}
