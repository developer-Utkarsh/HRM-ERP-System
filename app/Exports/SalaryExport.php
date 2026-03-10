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

class SalaryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{

	public function registerEvents(): array
	{
		return [
			AfterSheet::class    => function(AfterSheet $event) {
				$cellRange = 'A1:AM3';
				$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
				// $event->sheet->getDelegate()->getStyle('A1:AM1')->getFont()->setSize(14);
				
				$event->sheet->getDelegate()->getStyle('A3:AM3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('91DDA1');

				$event->sheet->getDelegate()->setMergeCells(['A1:AM1','A2:AM2']);
			},
		];
	}

	public function __construct( $get_array){
		$this->get_array = $get_array;
	}

	public function headings(): array
	{ 
		return [
			[
				'Utkarsh Classes & Edutech Private Limited',
			],
			[
				'Salary Sheet '.date('M-Y', strtotime($_GET['year_wise_month'].'-01')),
			],
			[
			"S. No.",
			"Employee ID",
			"Employee Name",
			"Father Name",
			"DOB",
			"DOJ",
			"DOL",
			"Branch Name",
			"Designation",
			"Department",
			"Sub Department",
			"Last Month HW/Arrears Days",
			"ESIC",
			"PF",
			"ESIC NO.",
			"UAN NO.",
			"NEW BASICS",
			"OLD BASICS",
			"Increment",
			"Paid Days",
			"Extra Days",
			"Gross Salary",
			"Extra Paid Days",
			"Arrear",
			//"Incentive+Arrear",
			"GF+Inc (N+O+P)",
			"GF+Arr (N+P)",
			"Grand Total",
			"ESI",
			"Total PF",
			"PF",
			"Arrear PF",
			"Loan / Advance",
			"TDS",
			"Final Amount",
			"Paid",
			"Due",
			"Account Number",
			"IFSC  Code",
			// "Actual Present",		
			// "Total Absent",		
			// "Total Holiday Working",		
			// "Total Week Off",		
			// "Salary",		
			// "Previous Month Leave Balance",		
			// "Adjustment Amount",		
			// "Final Salary",	
			],	
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if (!empty($this->get_array))
		{   
			$i = 0;
			$get_data = $this->get_array;
			$export_data = [];
			foreach ($get_data as $key => $value)
			{  
				$i++;

				$export_data[$key][] = $i;	
				$export_data[$key][] = $value['register_id'];
				$export_data[$key][] = $value['name'];
				$export_data[$key][] = $value['fname'];
				$export_data[$key][] = $value['dob'];
				$export_data[$key][] = $value['joining_date'];
				$export_data[$key][] = $value['reason_date'];
				$export_data[$key][] = $value['branch_name'];
				$export_data[$key][] = $value['designation_name'];
				$export_data[$key][] = $value['departments_name'];
				$export_data[$key][] = $value['sub_department'];
				$export_data[$key][] = $value['last_month_pending_sunday'];
				$export_data[$key][] = $value['is_esi'];
				$export_data[$key][] = $value['is_pf'];
				$export_data[$key][] = $value['esic_no'];
				$export_data[$key][] = $value['uan_no'];
				$export_data[$key][] = $value['new_basic'];
				$export_data[$key][] = $value['old_salary'];
				$export_data[$key][] = $value['increment_amount'];
				$export_data[$key][] = $value['paid_day'];
				$export_data[$key][] = $value['total_holiday_working'];
				$export_data[$key][] = $value['gross_salary'];
				$export_data[$key][] = $value['incentive'];
				$export_data[$key][] = $value['arrear'];
				//$export_data[$key][] = $value['incentive_arrear'];
				$export_data[$key][] = $value['gf_inc'];
				$export_data[$key][] = $value['gf_arr'];
				$export_data[$key][] = $value['grand_total'];
				$export_data[$key][] = $value['esi_amount'];
				$export_data[$key][] = $value['total_pf'];
				$export_data[$key][] = $value['pf_amount'];
				$export_data[$key][] = $value['arrear_pf'];
				$export_data[$key][] = $value['loan_amount'];
				$export_data[$key][] = $value['tds_amount'];
				$export_data[$key][] = $value['final_amount'];
				$export_data[$key][] = $value['paid'];
				$export_data[$key][] = $value['due'];
				$export_data[$key][] = $value['account_number'];
				$export_data[$key][] = $value['ifsc_code'];
				// $export_data[$key][] = $value['total_present'];
				// $export_data[$key][] = $value['total_absent'];
				// $export_data[$key][] = $value['total_holiday_working'];
				// $export_data[$key][] = $value['total_week_off'];
				// $export_data[$key][] = $value['salary'];
				// $export_data[$key][] = $value['leave_balance'];
				// $export_data[$key][] = $value['adjustment_amount'];
				// $export_data[$key][] = $value['final_salary'];
			}
			
			return collect($export_data);
		}
    }
}
