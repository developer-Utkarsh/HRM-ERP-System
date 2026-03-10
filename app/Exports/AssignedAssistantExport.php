<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class AssignedAssistantExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"S. No.",
			"Branch Name",
			"Studio Name",
			"Assistant Name",	
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
		$get_data = $this->get_data;

    		$export_data = [];
			$dataFound = 0;
			
    		foreach ($get_data as $getDataArray) {

				
				$export_data[$dataFound][] = $dataFound + 1 ;

				if(isset($getDataArray->branch_name) && !empty($getDataArray->branch_name)){
					$export_data[$dataFound][] = $getDataArray->branch_name;
				} else{
					$export_data[$dataFound][] = '';
				}

				if(isset($getDataArray->studio_name) && !empty($getDataArray->studio_name)){
					$export_data[$dataFound][] = $getDataArray->studio_name;
				} else{
					$export_data[$dataFound][] = '';
				}

				
				$export_data[$dataFound][] = $getDataArray->assistant_name;
				
				
				$dataFound++;
						
				
    		}

    		return collect($export_data);	
    	
    }
}
