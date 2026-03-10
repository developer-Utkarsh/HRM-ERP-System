<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TrainingPdfExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Employee Name",
			"Category Name",
			"Title",
			"Date",
			"PDF",
			"Description",
			"Status",
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
				
				if (isset($value->user_name) && !empty($value->user_name))
				{
					$export_data[$key][] = $value->user_name;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->cat_name) && !empty($value->cat_name))
				{
					$export_data[$key][] = $value->cat_name;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->title) && !empty($value->title))
				{
					$export_data[$key][] = $value->title;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->date) && !empty($value->date))
				{
					$export_data[$key][] = date('d-m-Y',strtotime($value->date));
				}
				else
				{
					$export_data[$key][] = '';
				}


				if (isset($value->pdf_url) && !empty($value->pdf_url))
				{
					$export_data[$key][] = asset('laravel/public/training_pdf/').'/'.$value->pdf_url;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->description) && !empty($value->description))
				{
					$export_data[$key][] = $value->description;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->status) && !empty($value->status))
				{
					$export_data[$key][] = $value->status;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				
				// if (isset($value->created_at) && !empty($value->created_at))
				// {
				// 	$export_data[$key][] = $value->created_at->format('d-m-Y');
				// }
				// else
				// {
				// 	$export_data[$key][] = '';
				// }
				
				$export_data_all[$key] = $export_data;
					
				$dataFound++;
			}
			
			return collect($export_data_all);
    }
}
