<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class WarehouseToBranchReport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Category",
			"Sub Category",
			"Product",
			"Branch Name",
			"Transfer Quantity",
			"Total Branch Stock",
			"Date",
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
								
				$export_data[$key1][] = isset($value['category_name']) 		?  $value['category_name'] 		: '';
				$export_data[$key1][] = isset($value['sub_category_name'])  ?  $value['sub_category_name']  : '';
				$export_data[$key1][] = isset($value['product_name']) 		?  $value['product_name'] 		: '';
				$export_data[$key1][] = isset($value['branch_name']) 		?  $value['branch_name'] 		: '';
				$export_data[$key1][] = isset($value['transfer_qty']) 		?  $value['transfer_qty'] 		: '';
				$export_data[$key1][] = isset($value['total_branch_stock']) ?  $value['total_branch_stock'] : '';
				$export_data[$key1][] = isset($value['created_at']) 		?  $value['created_at'] 		: '';
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
