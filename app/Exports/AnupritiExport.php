<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class AnupritiExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Reg. No.",
			"Student",
			"Category",
			"Batch Code",
			"Batch Name",
			"Percentage",
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
				$export_data[$key1][] = $i;
				$export_data[$key1][] = isset($value['reg_no']) ?  $value['reg_no'] : '';
				$export_data[$key1][] = isset($value['student']) ?  $value['student'] : '';
				$export_data[$key1][] = isset($value['cast']) ?  $value['cast'] : '';
				$export_data[$key1][] = isset($value['batch_id']) ?  $value['batch_id'] : '';
				$export_data[$key1][] = isset($value['batch']) ?  $value['batch'] : '';
				$export_data[$key1][] = isset($value['percentage']) ?  $value['percentage'] : '0';
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
