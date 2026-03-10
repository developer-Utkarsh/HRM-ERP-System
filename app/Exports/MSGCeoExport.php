<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MSGCeoExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct($get_data){
		$this->get_data = $get_data;
	}

	public function headings(): array
	{
		return [
			"SR. NO.",
			"Name",
			"Department",
			"Branch",
			"Mobile",
			"Msg",
			"Reply",
			"Department Head Name",
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
				$export_data[$key][] = $value->uname;
				$export_data[$key][] = $value->dname;	
				$export_data[$key][] = $value->branch;	
				$export_data[$key][] = $value->mobile;
				$export_data[$key][] = $value->message;
				$export_data[$key][] = $value->reply;
				$export_data[$key][] = $value->head_name;
				
				$export_data_all[$key] = $export_data;
					
				$dataFound++;
			}

		return collect($export_data_all);
    }
}
