<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class IncompleteExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
		//$total_month_days = $this->get_array[0]['date'];
		$array = array();
		
		$array[] = "S. No.";
		$array[] = "Employee Code";
		$array[] = "Employee Name";
		$array[] = "Branch";
		$array[] = "Department Type";
		$array[] = "Designation";
		$array[] = "Date";
		
		$array[] = "RFID/App";
		$array[] = "Location";
		$array[] = "In Time";
		$array[] = "Out Time";
		$array[] = "Duration";
		return $array;
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
			
			
			foreach ($get_array as $key1 => $value){				
				$export_data = []; 
				$i++;
				
				$total_minute 	=	0;
				$time_count 	=	count($value['time']);
				$first_in_time 	=	"";
				$last_out_time 	=	"";
				
				foreach($value['time'] as $key=>$time_details){
					if(!empty($time_details['in_time'])){
						if($first_in_time ==""){
							$first_in_time =  date("h:i A", strtotime($time_details['in_time']));
						}
					}
					
					if(!empty($time_details['out_time'])){
						$last_out_time = date("h:i A", strtotime($time_details['out_time']));
					}
					
					if(!empty($time_details['in_time']) && !empty($time_details['out_time'])){
						$intime = new DateTime($time_details['in_time']);
						$outtime = new DateTime($time_details['out_time']);
						$interval = $intime->diff($outtime);
						$hours = $interval->format('%H');
						$minute = $interval->format('%I');
						$total_minute += ($hours*60)+$minute;
					}
					
				}  
				
				$total_hours = "00:00";
				if($total_minute > 0){
					$format = '%02d:%02d';
					$totalhours = floor($total_minute / 60);
					$totalminutes = ($total_minute % 60);
					$total_hours = sprintf($format, $totalhours, $totalminutes);
				}
				
				$export_data[$key1][] = $i;
				$export_data[$key1][] = isset($value['register_id']) ?  $value['register_id'] : '';
				$export_data[$key1][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key1][] = isset($value['branch']) ?  $value['branch'] : '';
				$export_data[$key1][] = isset($value['department']) ?  $value['department'] : '';
				$export_data[$key1][] = isset($value['designation']) ?  $value['designation'] : '';
				$export_data[$key1][] = isset($value['date']) ?  $value['date'] : '';
				$export_data[$key1][] = isset($value['table_name']) ?  $value['table_name'] : '';
				$export_data[$key1][] = isset($value['last_location']) ?  $value['last_location'] : '';
				$export_data[$key1][] = $first_in_time;
				$export_data[$key1][] = $last_out_time;
				$export_data[$key1][] = $total_hours ." Hour";
				
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
