<?php
namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Request;
use DB;

class DueStudentExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{

	public function registerEvents(): array
	{
		return [
			AfterSheet::class    => function(AfterSheet $event) {
				/*$cellRange = 'A1:AJ3';
				$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
				// $event->sheet->getDelegate()->getStyle('A1:AM1')->getFont()->setSize(14);
				
				$event->sheet->getDelegate()->getStyle('A3:AJ3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('91DDA1');

				$event->sheet->getDelegate()->setMergeCells(['A1:AJ1','A2:AJ2']);*/
			},
		];
	}

	public function __construct( $get_array){
		$this->get_array = $get_array;
	}

	public function headings(): array
	{ 
		return [
			/*[
				'Utkarsh Classes & Edutech Private Limited',
			],
			[
				'Enquiry Sheet ',
			],*/
			[
			"S. No.","Category","Batch Name","Batch ID","Student ID","Student Name","Student Contact No.","Joining Date","Total Amount Due","Days Since Due","Last Fees Paid Date","Online Access Status","Batch Status"
			],	
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if(!empty($this->get_array))
		{   
			$i = 0;
			$get_data = $this->get_array;
			$export_data = [];
			
			$thirtdayback=date("Y-m-d",strtotime(date("Y-m-d")." -30 days"));
			foreach ($get_data as $key => $value)
			{  
				
														
				$i++;
				$export_data[$key][] = $i;	
				$export_data[$key][] = $value->category;
				$export_data[$key][] = $value->batch;
				$export_data[$key][] = $value->batch_id;
				$export_data[$key][] = $value->reg_number;
				$export_data[$key][] =$value->s_name;
				$export_data[$key][] = $value->contact;
				$export_data[$key][] = date("d-m-Y",strtotime($value->reg_date));				
				$export_data[$key][] = $value->due_amount;
				$export_data[$key][] = $value->due_days;
				$export_data[$key][] = $value->receipt_date;
				//$export_data[$key][] = '';
				//$export_data[$key][] = '';
				$export_data[$key][] = $value->course_status;
				$export_data[$key][] = $value->batch_running_status;
				
			}
			
			return collect($export_data);
		}
    }
}
