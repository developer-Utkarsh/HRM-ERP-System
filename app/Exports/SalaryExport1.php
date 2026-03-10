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
			"Sub Department",
			"Department",
			"Last month pending Sunday/Arrears days",
			"ESIC",
			"PF",
			"ESIC NO.",
			"UAN NO.",
			"NEW BASICS",
			"OLD SALARY",
			"Increment",
			"Paid Days",
			"Extra Days",
			"Gross Salary",
			"Incentive",
			"Arrear",
			"Incentive+Arrear",
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
				$increment_result = \App\SalaryIncrement::where('user_id',$value['id'])->where('date',$_GET['year_wise_month'])->orderBy('date','DESC')->first();
				
				
				if(!empty($increment_result)){

					$old_result = \App\SalaryIncrement::where('user_id',$value['id'])->where('id','<',$increment_result->id)->orderBy('id','DESC')->first();
					
					//$increment_amount = $increment_result->increment_amount;
					$loan_amount      = $increment_result->loan_amount;
					//$arrear_amount    = $increment_result->arrear_amount;
					$tds_amount       = $increment_result->tds_amount;
					$new_salary       = ($increment_result->salary + $increment_result->increment_amount);
					if(!empty($old_result)){
						$old_salary       = ($old_result->salary + $old_result->increment_amount);
					}
					else{
						$old_salary = $value['net_salary'];
					}
				}
				else{
					$get_last_month_result = \App\SalaryIncrement::where('user_id',$value['id'])->where('date', '<',  $_GET['year_wise_month'])->orderBy('id','DESC')->first();
					
					if(!empty($get_last_month_result)){
						$new_sal_data = ($get_last_month_result->increment_amount + $get_last_month_result->salary);
						$old_sal_data = ($get_last_month_result->increment_amount + $get_last_month_result->salary);
					}
					else{
						$new_sal_data = $value['net_salary'];
						$old_sal_data = $value['net_salary'];
					}
					//$increment_amount = 0;
					$loan_amount      = 0;
					//$arrear_amount    = 0;
					$tds_amount       = 0;
					$new_salary       = $new_sal_data;
					$old_salary       = $old_sal_data;
				}

				$total_present_half = $value['total_present_half'];
				$total_present = $value['total_present'];
				$total_absent = $value['total_absent'];
				$total_holiday_working = $value['total_holiday_working'];
				$total_week_off = $value['total_week_off'];
				// $salary = $value['salary'];
				$leave_balance = $value['leave_balance'];
				$paid_day = $value['actual_paid'];
				// $adjustment_amount = $value['adjustment_amount'];
				// $final_salary = $salary + $adjustment_amount;
				$i++;

				if($value['is_extra_working_salary'] == 1){
					$extra_work = $value['total_holiday_working'];
					$paid_day = $paid_day - $value['total_holiday_working'];
					
				}
				else{
					$extra_work = 0;
				}

				$last_month_pending_sunday = 0;
				$new_basic = $new_salary;
				// $paid_day = $total_present+$value['total_present_half']+$value['total_week_off']+$value['total_pl']+$value['total_cl']+$value['total_sl']+$value['total_co'];
				$gross_salary = sprintf("%.2f", ($new_basic/$value['total_month_days'])*$paid_day);
				$incentive = sprintf("%.2f", ($new_basic/$value['total_month_days'])*$extra_work);
				$arrear = sprintf("%.2f", ($new_basic/$value['total_month_days'])*$last_month_pending_sunday);
				$increment_amount = sprintf("%.2f", ($new_basic - $old_salary));
				if($value['is_esi'] == 'Yes'){
					$esi_amount = sprintf("%.2f", ((($gross_salary+($incentive+$arrear)) * 0.75)/100));
				}
				else{
					$esi_amount = 0;
				}

				if($value['is_pf'] == 'Yes'){
					$pf_amount = sprintf("%.2f", ($gross_salary * 12)/100);
					$arrear_pf = sprintf("%.2f", ($arrear * 12)/100);
				}
				else{
					$pf_amount = 0;
					$arrear_pf = 0;
				}

				$total_pf = sprintf("%.2f", ($pf_amount + $arrear_pf));
				$final_amount = sprintf("%.2f", (($gross_salary+($incentive+$arrear)) - $esi_amount - $total_pf -$loan_amount - $tds_amount));
				$incentive_arrear = $incentive+$arrear;
				$gf_inc = $gross_salary+($incentive+$arrear);
				$gf_arr = $gross_salary+$arrear;

				$export_data[$key][] = $i;	
				$export_data[$key][] = $value['register_id'];
				$export_data[$key][] = $value['name'];
				$export_data[$key][] = $value['fname'];
				$export_data[$key][] = $value['dob'];
				$export_data[$key][] = $value['joining_date'];
				$export_data[$key][] = $value['reason_date'];
				$export_data[$key][] = $value['branch_name'];
				$export_data[$key][] = $value['designation_name'];
				$export_data[$key][] = '--';
				$export_data[$key][] = $value['departments_name'];
				$export_data[$key][] = "$last_month_pending_sunday";
				$export_data[$key][] = $value['is_esi'];
				$export_data[$key][] = $value['is_pf'];
				$export_data[$key][] = $value['esic_no'];
				$export_data[$key][] = $value['uan_no'];
				$export_data[$key][] = $new_basic;
				$export_data[$key][] = $old_salary;
				$export_data[$key][] = $increment_amount;
				$export_data[$key][] = "$paid_day";
				$export_data[$key][] = "$total_holiday_working";
				$export_data[$key][] = "$gross_salary";
				$export_data[$key][] = "$incentive";
				$export_data[$key][] = "$arrear";
				$export_data[$key][] = "$incentive_arrear";
				$export_data[$key][] = "$gf_inc";
				$export_data[$key][] = "$gf_arr";
				$export_data[$key][] = $gross_salary+($incentive+$arrear);
				$export_data[$key][] = "$esi_amount";
				$export_data[$key][] = "$total_pf";
				$export_data[$key][] = "$pf_amount";
				$export_data[$key][] = "$arrear_pf";
				$export_data[$key][] = "$loan_amount";
				$export_data[$key][] = "$tds_amount";
				$export_data[$key][] = $final_amount;
				$export_data[$key][] = $final_amount;
				$export_data[$key][] = '0';
				$export_data[$key][] = $value['account_number'];
				$export_data[$key][] = $value['ifsc_code'];
				// $export_data[$key][] = "$total_present";
				// $export_data[$key][] = "$total_absent";
				// $export_data[$key][] = "$total_holiday_working";
				// $export_data[$key][] = "$total_week_off";
				// $export_data[$key][] = "$salary";
				// $export_data[$key][] = "$leave_balance";
				// $export_data[$key][] = "$adjustment_amount";
				// $export_data[$key][] = "$final_salary";
			}
			
			return collect($export_data);
		}
    }
}
