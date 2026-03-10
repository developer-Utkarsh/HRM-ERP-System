<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StaffmovementsystemExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct( $get_data){
		$this->get_data = $get_data;
	}

	public function headings(): array
	{
		return [
			"SR. NO.",
			"Name",
			"From Time",
			"To Time",
			"Reason",
			"Status",
			"Date",
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$get_data = $this->get_data;
			$export_data_all = [];
			
			$dataFound = 1;
			
			
				
			foreach ($get_data as $key => $value)
			{  
				$export_data = [];
							
				$export_data[$key][] = $dataFound;
				
				if (isset($value->employee->name) && !empty($value->employee->name))
				{
					$export_data[$key][] = $value->employee->name;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->from_time) && !empty($value->from_time))
				{
					$export_data[$key][] = $value->from_time;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				
				if (isset($value->to_time) && !empty($value->to_time))
				{
					$export_data[$key][] = $value->to_time;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->reason) && !empty($value->reason))
				{
					$export_data[$key][] = $value->reason;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->status) && !empty($value->status))
				{
					$export_data[$key][] = $value->status;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->cdate) && !empty($value->cdate))
				{
					$export_data[$key][] = date('d-m-Y',strtotime($value->cdate));
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				$export_data_all[$key] = $export_data;
					
				$dataFound++;
			}
			
			return collect($export_data_all);
    }
}
