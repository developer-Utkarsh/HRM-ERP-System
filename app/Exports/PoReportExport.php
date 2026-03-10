<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class PoReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"Request Type",
			"Req. No.",
			"Po. No.",
			"Po. Date",
			"Particulars",
			"UOM",
			"Qty",
			"Rate / Rs.",
			"Amount / Rs.",
			"GST Rate",
			"GST Amount",
			"Total Amount / Rs.",
			"Location",
			"City",
			"Vendor",
			"Employee Remark",
			"PO Narration",
			"Material Category"
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
								
				$export_data[$key1][] = isset($value['request_type']) ?  $value['request_type'] : '';
				$export_data[$key1][] = isset($value['unique_no']) ?  $value['unique_no'] : '';
				$export_data[$key1][] = isset($value['po_no']) ?  $value['po_no'] : '';
				$export_data[$key1][] = isset($value['pdate']) ?  $value['pdate'] : '';
				$export_data[$key1][] = isset($value['item']) ?	$value['item']: '';
				$export_data[$key1][] = isset($value['uom']) ?	$value['uom']: '';
				$export_data[$key1][] = isset($value['qty']) ?	$value['qty']: '';
				$export_data[$key1][] = isset($value['rate']) ?	$value['rate']: '';
				$export_data[$key1][] = isset($value['amount']) ?	$value['amount']: '';
				$export_data[$key1][] = isset($value['gst_rate']) ?	$value['gst_rate']: '';
				$export_data[$key1][] = isset($value['gst_amt']) ?	$value['gst_amt']: '';
				$export_data[$key1][] = isset($value['total']) ?	$value['total']: '';
				
				$export_data[$key1][] = isset($value['bname']) ?  $value['bname'] : '';
				$export_data[$key1][] = isset($value['branch_location']) ?  ucfirst($value['branch_location']) : '';
				$export_data[$key1][] = isset($value['vendor']) ?  ucfirst($value['vendor']) : '';
				$export_data[$key1][] = isset($value['emp_remark']) ?  ucfirst($value['emp_remark']) : '';
				$export_data[$key1][] = isset($value['narration']) ?  ucfirst($value['narration']) : '';
				$export_data[$key1][] = isset($value['mcategory']) ?  ucfirst($value['mcategory']) : '';
				
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
