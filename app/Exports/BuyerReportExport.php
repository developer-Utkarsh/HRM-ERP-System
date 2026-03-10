<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class BuyerReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Code",
			"Name",
			"Contact Number",
			"Email",
			"Address",
			"Pan Number",
			"GST Number",
			"Beneficiary",
			"Account",
			"Bank",
			"IFSC",
			"Bank Address",
			"MSME Category",
			"MSME/UAM File",
			"MSME/UAM No",
			"Declaration",
			"Created",
			"Status",
			"Pincode",
			"Credit Days",
			"Vendor Type",
			"GST Doc",
			"Pan Doc",
			"Bank 1 Doc",
			"Bank 2 Doc",
			"Aggrement Doc",
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
								
				$export_data[$key1][] = isset($value['id']) ?  'V'.$value['id'] : '';
				$export_data[$key1][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key1][] = isset($value['contact_no']) ?  $value['contact_no'] : '';
				$export_data[$key1][] = isset($value['email']) ?  $value['email'] : '';
				$export_data[$key1][] = isset($value['address']) ?  $value['address'] : '';
				$export_data[$key1][] = isset($value['pan_no']) ?  $value['pan_no'] : '';
				$export_data[$key1][] = isset($value['gst_no']) ?  $value['gst_no'] : '';
				$export_data[$key1][] = isset($value['beneficiary']) ?  $value['beneficiary'] : '';
				$export_data[$key1][] = '="'.$value['account'].'"';


				$export_data[$key1][] = isset($value['bank_name']) ?  $value['bank_name'] : '';
				$export_data[$key1][] = isset($value['ifsc']) ?  $value['ifsc'] : '';
				$export_data[$key1][] = isset($value['bank_address']) ?  $value['bank_address'] : '';
				$export_data[$key1][] = isset($value['msme_category']) ?  $value['msme_category'] : '';
				$export_data[$key1][] = isset($value['msme_uam_file']) ?  $value['msme_uam_file'] : '';
				$export_data[$key1][] = isset($value['msme_uam_no']) ?  $value['msme_uam_no'] : '';
				$export_data[$key1][] = isset($value['declaration_form']) ?  $value['declaration_form'] : '';
				$export_data[$key1][] = isset($value['created_at']) ?  $value['created_at'] : '';
				$export_data[$key1][] = isset($value['status']) ?  $value['status'] : '';
				$export_data[$key1][] = isset($value['pincode']) ?  $value['pincode'] : '';
				$export_data[$key1][] = isset($value['credit_day']) ?  $value['credit_day'] : '';
				$export_data[$key1][] = isset($value['type']) ?  $value['type'] : '';
				$export_data[$key1][] = isset($value['gst_img']) ?  $value['gst_img'] : '';
				$export_data[$key1][] = isset($value['pan_img']) ?  $value['pan_img'] : '';
				$export_data[$key1][] = isset($value['bank_proof']) ?  $value['bank_proof'] : '';
				$export_data[$key1][] = isset($value['bank_proof_2']) ?  $value['bank_proof_2'] : '';
				$export_data[$key1][] = isset($value['aggrement']) ?  $value['aggrement'] : '';
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
