<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class AbsentFullAttendanceExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Designation",
			"Mobile",
			"Date",
			"Status",
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
				$i++;
				$export_data[$key1][] = $i;
				$export_data[$key1][] = isset($value['register_id']) ?  $value['register_id'] : '';
				$export_data[$key1][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key1][] = isset($value['branch_name']) ?  $value['branch_name'] : '';
				$export_data[$key1][] = isset($value['departments_name']) ?  $value['departments_name'] : '';
				$export_data[$key1][] = isset($value['designation_name']) ?  $value['designation_name'] : '';
				$export_data[$key1][] = isset($value['mobile']) ?  $value['mobile'] : '';
				$export_data[$key1][] = isset($value['date']) ?  $value['date'] : '';
				$export_data[$key1][] = isset($value['status']) ?  $value['status'] : '';
					
				
				$export_data_all[$ii] = $export_data;
				$ii++;
				
			}
			
			return collect($export_data_all);
		}
    }
}
