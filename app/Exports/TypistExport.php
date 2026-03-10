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

class TypistExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
		return ["S. No.","Name", "No Of Questions", "OCR/panel", "Arrange Correction", "Total Page", "Remark","Date"];
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
			
			$dataFound = 1;
			foreach ($get_data as $key => $value)
			{   //echo '<pre>'; print_r($value);die;
				$export_data = [];
							
				$export_data[$key][] = $dataFound;	

				if (isset($value->employee->name) && !empty($value->employee->name))
				{
					$export_data[$key][] = $value->employee->name;
				}
				else
				{
					$export_data[$key][] = '';
				}
							
				if (isset($value->number_of_questions) && !empty($value->number_of_questions))
				{
					$export_data[$key][] = $value->number_of_questions;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->ocr_panel) && !empty($value->ocr_panel))
				{
					$export_data[$key][] = $value->ocr_panel;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->arrange_correction) && !empty($value->arrange_correction))
				{
					$export_data[$key][] = $value->arrange_correction;
				}
				else
				{
					$export_data[$key][] = '';
				}


				if (isset($value->total_page) && !empty($value->total_page))
				{
					$export_data[$key][] = $value->total_page;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->remark) && !empty($value->remark))
				{
					$export_data[$key][] = $value->remark;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->cdate) && !empty($value->cdate))
				{
					$export_data[$key][] = date('d-m-Y',strtotime($value->cdate));
				}
				else
				{
					$export_data[$key][] = '';
				}

				

				
					
				$export_data_all[$key] = $export_data;
					
				$dataFound++;
			}
			
			// echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
		}
	}
}
        
