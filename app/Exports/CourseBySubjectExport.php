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

class CourseBySubjectExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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

	public function __construct($row)
	{
		$this->get_data = $row;
	}

	public function headings():
	array
	{
		return ["Subject Name","Chapter Name","Duration"];
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if (!empty($this->get_data))
		{
			$get_data = $this->get_data;
			$export_data_all = [];
			
			$dataFound = 0; 
			foreach ($get_data as $key1 => $value)
			{ 
				$export_data = [];
				$export_data[] = $value[0];
				$export_data[] = '';
				$export_data[] = '';
				// $export_data[] = ''; // "Topic Name",
				$export_data_all[$key1] = $export_data;
			}
			
			 //echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
		}
	}
}
        
