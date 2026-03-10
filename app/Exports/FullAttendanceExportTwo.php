<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class FullAttendanceExportTwo implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
		$fdate = $this->get_array[0]['fdate'];
		$tdate = $this->get_array[0]['tdate'];
		
		if($fdate==""){
			$fdate = date('Y-m-d');
			$tdate = date('Y-m-d');
		}
		
		
		$array = array();
		$array[] = "S. No.";
		$array[] = "Employee Code";
		$array[] = "Employee Name";
		$array[] = "Branch";
		$array[] = "Department";
		$array[] = "Designation";
		for ($x = $fdate; $x <= $tdate; $x++) {
		   $array[] = date('d-m-Y',strtotime($x));
		}	
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
			
			$fdate = $this->get_array[0]['fdate'];
			$tdate = $this->get_array[0]['tdate'];
			
			if($fdate==""){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
		
			$export_data_all = [];
			
			$i = 0;
			$ii = 0;
			foreach ($get_array as $key1 => $value)
			{
				$i++;
				$export_data = []; 
				$export_data[$key1][] = $i;
				$export_data[$key1][] = isset($value['register_id']) ?  $value['register_id'] : '';
				$export_data[$key1][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key1][] = isset($value['branch_name']) ?  $value['branch_name'] : '';
				$export_data[$key1][] = isset($value['departments_name']) ?  $value['departments_name'] : '';
				$export_data[$key1][] = isset($value['designation_name']) ?  $value['designation_name'] : '';
				
				$total_minute = 0;
				$time_count = $value['date_array'];
				
				
				
				if(count($time_count) > 0){					
					$time_array = array();
					$in_time = "";
					$out_time = "";
					$j	= 0;	
					foreach($time_count as $key2 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						
						if(!empty($AttendanceDetail['date'])){
							$date	  = $AttendanceDetail['date'];
						}else{
							$date	  = date('Y-m-d');
						}
						
						//echo '<pre>'; print_r($date); die;
						if(empty($time_array[$date]['in_time'])){
							$time_array[$date]['in_time'] = "";
						}
						if(empty($time_array[$date]['out_time'])){
							$time_array[$date]['out_time'] = "";
						}
						
						$in_time  = $AttendanceDetail['in_time'];
						
						if($AttendanceDetail['out_time']==0){
							$out_time = "";
							$oTime	  = $out_time;
						}else{
							$out_time = $AttendanceDetail['out_time'];
							$oTime	  = date("h:i A", strtotime($out_time));
						}
						
						if(empty($time_array[$date]['in_time'])){
							$time_array[$date]['in_time'] =  !empty($in_time) ? date("h:i A", strtotime($in_time)) : '';
							$time_array[$date]['out_time'] =  $oTime;
							
							$time_array[$date]['date'] =  $date;
						}
						$j++;
						
						// $export_data[$key1][] = $time_array[$key1]['in_time'].' - '.$time_array[$key1]['out_time'];
					}
					
					for ($x = $fdate; $x <= $tdate; $x++) {
						if(!empty($time_array[$x]['date'])){
							if($x==$time_array[$x]['date']){
								$export_data[$key1][] = $time_array[$x]['in_time'].' - '.$time_array[$x]['out_time'];
							}
							else{
								$export_data[$key1][] = "A";
							}
						}
						else{
							$export_data[$key1][] = "A";
						}
					}
				}
				
				$export_data_all[$ii] = $export_data;
				$ii++;
				
			}
			
			return collect($export_data_all);
		}
    }
}
