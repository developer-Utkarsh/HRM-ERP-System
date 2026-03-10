<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

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
				$export_data = [];
				if(!empty($value->task_details) && count($value->task_details) > 0){
				  
					foreach($value->task_details as $key=>$task_details){
						$i++;
						$export_data[$key][] = $i;
						$export_data[$key][] = isset($value->user->register_id) ?  $value->user->register_id : '';
						$export_data[$key][] = isset($value->user->name) ?  $value->user->name : '';
						$export_data[$key][] = isset($value->date)?$value->date:'';
						$export_data[$key][] = $task_details->name;
						$export_data[$key][] = $task_details->plan_hour;
						$export_data[$key][] = $task_details->status;
					} 
				}
				
				$export_data_all[$ii] = $export_data;
				$ii++;
				
			}
			
			return collect($export_data_all);
		}
    }
}
