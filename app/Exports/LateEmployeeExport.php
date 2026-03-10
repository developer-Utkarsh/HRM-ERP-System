<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LateEmployeeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"EMPLOYEE CODE",
			"EMPLOYEE NAME",
			"BRANCH",
			"CONTACT NO.",
			"DEPARTMENT",
			"In TIme",
			"DATE",
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
				if(!empty($value['intime'])){
					$export_data = [];
								
					$export_data[$key][] = $dataFound;	
					

					if (isset($value['register_id']) && !empty($value['register_id']))
					{
						$export_data[$key][] = $value['register_id'];
					}
					else
					{
						$export_data[$key][] = '';
					}
								
					if (isset($value['name']) && !empty($value['name']))
					{
						$export_data[$key][] = $value['name'];
					}
					else
					{
						$export_data[$key][] = '';
					}

					if (isset($value['branch_name']) && !empty($value['branch_name']))
					{
						$export_data[$key][] = $value['branch_name'];
					}
					else
					{
						$export_data[$key][] = '';
					}

					if (isset($value['mobile']) && !empty($value['mobile']))
					{
						$export_data[$key][] = $value['mobile'];
					}
					else
					{
						$export_data[$key][] = '';
					}
					
					
					if (isset($value['department']) && !empty($value['department']))
					{
						$export_data[$key][] = $value['department'];
					}
					else
					{
						$export_data[$key][] = '';
					}
					
					
					
					if (isset($value['intime']) && !empty($value['intime']))
					{
						$export_data[$key][] = $value['intime'];
					}
					else
					{
						$export_data[$key][] = '';
					}

					if (isset($value['date']) && !empty($value['date']))
					{
						$export_data[$key][] = $value['date'];
					}
					else
					{
						$export_data[$key][] = '';
					}
					
						
					$export_data_all[$key] = $export_data;
						
					$dataFound++;
				}
			}
			
			//echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
    }
}
