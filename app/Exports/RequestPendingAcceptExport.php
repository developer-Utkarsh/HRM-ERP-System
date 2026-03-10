<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class RequestPendingAcceptExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"MRL Number",
			"User",								
			"Category",
			"Sub Category",										
			"Product",	
			"Qty",
			"Branch",
			"Created Date"
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
			foreach ($get_array as $key1 => $value)
			{				
				$i++;
				$export_data = []; 
								
				$export_data[$key1][] = isset($value['unique_no']) ?  'REQ-'.$value['unique_no'] : '';
				$export_data[$key1][] = isset($value['user_name']) ?  $value['user_name'] : '';
				$export_data[$key1][] = isset($value['cat_name']) ?  $value['cat_name'] : '';
				$export_data[$key1][] = isset($value['sub_name']) ?  $value['sub_name'] : '';
				$export_data[$key1][] = isset($value['pro_name']) ?  $value['pro_name'] : '';
				$export_data[$key1][] = isset($value['qty']) ?  $value['qty'] : '';
				$export_data[$key1][] = isset($value['brname']) ?  $value['brname'] : '';
				$export_data[$key1][] = isset($value['created_at']) ?  $value['created_at'] : '';				
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
