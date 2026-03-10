<?php
namespace App\Exports;

use App\Studio;
use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use DB;

class FreeFacultyExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function registerEvents():
	array
	{
		return [AfterSheet::class => function (AfterSheet $event)
		{
			$cellRange = 'A1:W1';
			$event
				->sheet
				->getDelegate()
				->getStyle($cellRange)->getFont()
				->setSize(14);
		}
		, ];
	}

	public function __construct($get_data)
	{
		$this->get_data = $get_data;
	}

	public function headings():
	array
	{
		return ["S. No.","Faculty", "Email", "Mobile No", "From Free Time", "To Free Time"];
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if (!empty($this->get_data))
		{
			$get_array = $this->get_data;
			$export_data_all = [];
			
			$i = 0;$j = 0 ;
			foreach ($get_array as $key => $get_array_value)
			{  	
				if(count($get_array_value) > 0){
					
					foreach($get_array_value as $key2=>$get_array_value2){
						$j++;
						$export_data[$i][] = $j;
						$export_data[$i][] = !empty($get_array_value2['name']) ? $get_array_value2['name'] : '';
						$export_data[$i][] = !empty($get_array_value2['email']) ? $get_array_value2['email'] : '';
						$export_data[$i][] = !empty($get_array_value2['mobile']) ? $get_array_value2['mobile'] : '';
						$export_data[$i][] = !empty($get_array_value2['from_time']) ? $get_array_value2['from_time'] : '';
						$export_data[$i][] = !empty($get_array_value2['to_time']) ? $get_array_value2['to_time'] : '';
						$i++;  
					}	
				}
			  
			}
			//$export_data_all[$key] = $export_data;
			 //echo "<pre>"; print_r($export_data); die;
			return collect($export_data);
		}
	}
}
        
