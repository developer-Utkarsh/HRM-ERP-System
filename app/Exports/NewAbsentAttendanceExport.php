<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class NewAbsentAttendanceExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Contact No",
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
			
			$i = 1;
			foreach ($get_array as $key => $value)
			{
				$export_data = []; 
				
				$export_data[$key][] = $i;
				$export_data[$key][] = isset($value['register_id']) ?  $value['register_id'] : '';
				$export_data[$key][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key][] = isset($value['branch_name']) ?  $value['branch_name'] : '';
				$export_data[$key][] = isset($value['departments_name']) ?  $value['departments_name'] : '';
				$export_data[$key][] = isset($value['email']) ?  $value['email'] : '';
				$export_data[$key][] = isset($value['mobile']) ?  $value['mobile'] : '';	
				
				$export_data_all[$i] = $export_data;
			$i++;	
			}
			
			return collect($export_data_all);
		}
    }
}
