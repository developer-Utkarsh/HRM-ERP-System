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

class EnquiryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"S. No.","User Name","Number","Course","Query in Details","Department","Priority","Course Type","Status","Created At"
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
			foreach ($get_data as $key => $value)
			{  
				$i++;
				$export_data[$key][] = $i;	
				$export_data[$key][] = $value['name'];
				$export_data[$key][] = $value['mobile_no'];
				$export_data[$key][] = $value['course_name'];
				$export_data[$key][] = $value['description'];
				$export_data[$key][] =$value['cat_name'];
				$export_data[$key][] = $value['priority'];
				$export_data[$key][] = $value['course_type'];
				$export_data[$key][] = ucwords(str_replace("_"," ",$value['status']));
				$export_data[$key][] = date("d-m-Y H:i A",strtotime($value['created_at']));
			}
			
			return collect($export_data);
		}
    }
}
