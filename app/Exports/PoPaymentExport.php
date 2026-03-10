<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class PoPaymentExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"MRL Number",
			"MRL Date",
			"PO No.",
			"PO Date",
			"Invoice Date",
			"Invoice Number",
			"Vendor",
			"Branch",
			"Location",
			"Total Amount / Rs.",
			"Handed over to A/cs",
			"UTR Number",
			"Payment Status",
			"Remark",
			"Empployee GRN"
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
				if($value['status']==1){
					$status = "Completed";
				}else{
					$status = "Pending";
				}
				
				if(!empty($value['po_location'])){
					$po = 'UTKPO-'.$value['po_location'].'-'.$value['po_no'].'-'.$value['po_month'];
				}else{
					$po = 'UTKPO-'.$value['po_no'];
				}
				
				if($po=='UTKPO-0'){
					$poText = "Below 5000";
				}else{
					$poText = $po;
				}
				
				$i++;
				$export_data = []; 
								
				$export_data[$key1][] = isset($value['unique_no']) ?  'REQ-'.$value['unique_no'] : '';
				$export_data[$key1][] = isset($value['mrl_date']) ?  $value['mrl_date'] : '';
				$export_data[$key1][] = $poText;
				$export_data[$key1][] = isset($value['po_date']) ?  $value['po_date'] : '';
				$export_data[$key1][] = isset($value['date_of_invoice']) ?  $value['date_of_invoice'] : '';
				$export_data[$key1][] = isset($value['invoice_no']) ?  $value['invoice_no'] : '';
				$export_data[$key1][] = isset($value['vendor']) ?  ucfirst($value['vendor']) : '';
				$export_data[$key1][] = isset($value['bname']) ?  ucfirst($value['bname']) : '';
				$export_data[$key1][] = isset($value['branch_location']) ?  ucfirst($value['branch_location']) : '';
				$export_data[$key1][] = isset($value['total']) ?	$value['total']: '';
				$export_data[$key1][] = isset($value['handover_accounts']) ?  $value['handover_accounts'] : '';
				$export_data[$key1][] = isset($value['utr_no']) ?  $value['utr_no'] : '';
				$export_data[$key1][] = $status;
				$export_data[$key1][] = isset($value['remark']) ?  $value['remark'] : '';
				$export_data[$key1][] = $value['emp_grn'];
				
				$export_data_all[$key1] = $export_data; 
			}
			
			return collect($export_data_all);
		}
    }
}
