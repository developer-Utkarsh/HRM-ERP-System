<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class TaskExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Date",
			"Task",
			"Task Description",
			"Plan Hour",
			"Status"		
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
						$emp_id = $value->emp_id;
						$employee_details = DB::table('users')->where('id', $emp_id)->first();
						$export_data = [];
						foreach($value->task_details as $key2=>$task_details){ 
							$i++;
							$export_data[$key2][] = $i;
							$export_data[$key2][] = isset($employee_details->register_id) ?  $employee_details->register_id : '';
							$export_data[$key2][] = isset($employee_details->name) ?  $employee_details->name : '';
							$export_data[$key2][] = isset($value->date)?date('d-m-Y',strtotime($value->date)):'';
							$export_data[$key2][] = $task_details->name;
							$export_data[$key2][] = $task_details->description;
							$export_data[$key2][] = $task_details->plan_hour;
							$export_data[$key2][] = $task_details->status;
						}
						
						$export_data_all[$ii] = $export_data; 
						$ii++;
				
			}
			// echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
		//echo '<pre>'; print_r($export_data_all);die;
    }
	
	}
}
