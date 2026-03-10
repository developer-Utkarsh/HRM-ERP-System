<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployeeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
			"STATUS",
			"EMPLOYEE CODE",
			"EMPLOYEE NAME",
			"FATHER/HUSBAND NAME",
			"GENDER",
			"DESIGNATION",
			"SUB FUN.",
            "DEPARTMENT",
			"DOB",
			"DOJ",
			"LOCATION",
			"AADHAR NO.",
			"NAME AS PER AADHAR",
			"PAN NO.",
			"NAME AS PER PAN",
			"CONTACT NO.",
			"OFFICIAL NO.",
			"EMERGENCY CONTACT NO.",
			"PRESENT ADDRESS",
			"PERMANENT ADDRESSS",
			"MARRITAL STATUS",
			"PREVIOUS EXPERIENCE",
			"ESIC NO.",
			"DOJ ESIC",
			"UAN NO.",
			"DOJ PF",
			"TIMING SHIFT",
			"BANK AC.NO",
			"IFSC CODE",
			"NAME AS PER BANK ",
			"EMAIL",
			"EMP.FILE NO.",
			"SALARY",
			"E.PF",
			"E.ESIC",
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
			
				$branch_name = '';
				foreach($value->user_branches as $user_branches_value){
					$branch_name  = $user_branches_value->branch->name;
				}
				
				
				if($value->status == "1"){
					$sts = 'Active';
				}
				else{
					$sts = 'Inactive';
				}
								
				$export_data = [];
							
				$export_data[$key][] = $dataFound;	
				
				if (isset($sts) && !empty($sts))
				{
					$export_data[$key][] = $sts;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->register_id) && !empty($value->register_id))
				{
					$export_data[$key][] = $value->register_id;
				}
				else
				{
					$export_data[$key][] = '';
				}
							
				if (isset($value->name) && !empty($value->name))
				{
					$export_data[$key][] = $value->name;
				}
				else
				{
					$export_data[$key][] = '';
				}

				if (isset($value->user_details->fname) && !empty($value->user_details->fname))
				{
					$export_data[$key][] = $value->user_details->fname;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				
				if (isset($value->user_details->gender) && !empty($value->user_details->gender))
				{
					$export_data[$key][] = $value->user_details->gender;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				
				if (isset($value->user_details->degination) && !empty($value->user_details->degination))
				{
					$export_data[$key][] = $value->user_details->degination;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				$export_data[$key][] = '';
				
				if (isset($value->department->name) && !empty($value->department->name))
				{
					$export_data[$key][] = $value->department->name;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->dob) && !empty($value->user_details->dob))
				{
					$export_data[$key][] = $value->user_details->dob;
				}
				else
				{
					$export_data[$key][] = '';
				}
				 
				if (isset($value->user_details->joining_date) && !empty($value->user_details->joining_date))
				{
					$export_data[$key][] = $value->user_details->joining_date;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				
				if (isset($branch_name) && !empty($branch_name))
				{
					$export_data[$key][] = $branch_name;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->aadhar_card_no) && !empty($value->user_details->aadhar_card_no))
				{
					$export_data[$key][] = $value->user_details->aadhar_card_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->aadhar_name) && !empty($value->user_details->aadhar_name))
				{
					$export_data[$key][] = $value->user_details->aadhar_name;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->pan_no) && !empty($value->user_details->pan_no))
				{
					$export_data[$key][] = $value->user_details->pan_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->pan_name) && !empty($value->user_details->pan_name))
				{
					$export_data[$key][] = $value->user_details->pan_name;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->mobile) && !empty($value->mobile))
				{
					$export_data[$key][] = $value->mobile;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->official_no) && !empty($value->user_details->official_no))
				{
					$export_data[$key][] = $value->user_details->official_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->alternate_contact_number) && !empty($value->user_details->alternate_contact_number))
				{
					$export_data[$key][] = $value->user_details->alternate_contact_number;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->c_address) && !empty($value->user_details->c_address))
				{
					$export_data[$key][] = $value->user_details->c_address;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->p_address) && !empty($value->user_details->p_address))
				{
					$export_data[$key][] = $value->user_details->p_address;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->material_status) && !empty($value->user_details->material_status))
				{
					$export_data[$key][] = $value->user_details->material_status;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->previous_experience) && !empty($value->user_details->previous_experience))
				{
					$export_data[$key][] = $value->user_details->previous_experience;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->esic_no) && !empty($value->user_details->esic_no))
				{
					$export_data[$key][] = $value->user_details->esic_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->esi_date) && !empty($value->user_details->esi_date))
				{
					$export_data[$key][] = $value->user_details->esi_date;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->uan_no) && !empty($value->user_details->uan_no))
				{
					$export_data[$key][] = $value->user_details->uan_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->pf_date) && !empty($value->user_details->pf_date))
				{
					$export_data[$key][] = $value->user_details->pf_date;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->timing_shift_in) && !empty($value->user_details->timing_shift_in) && isset($value->user_details->timing_shift_out) && !empty($value->user_details->timing_shift_out))
				{
					$export_data[$key][] = date("h:i A", strtotime($value->user_details->timing_shift_in)).'-'.date("h:i A", strtotime($value->user_details->timing_shift_out));
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->account_number) && !empty($value->user_details->account_number))
				{
					$export_data[$key][] = $value->user_details->account_number;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->ifsc_code) && !empty($value->user_details->ifsc_code))
				{
					$export_data[$key][] = $value->user_details->ifsc_code;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->bank_emp_name) && !empty($value->user_details->bank_emp_name))
				{
					$export_data[$key][] = $value->user_details->bank_emp_name;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->email) && !empty($value->email))
				{
					$export_data[$key][] = $value->email;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->emp_file_no) && !empty($value->user_details->emp_file_no))
				{
					$export_data[$key][] = $value->user_details->emp_file_no;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->net_salary) && !empty($value->user_details->net_salary))
				{
					$export_data[$key][] = $value->user_details->net_salary;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->pf_amount) && !empty($value->user_details->pf_amount))
				{
					$export_data[$key][] = $value->user_details->pf_amount;
				}
				else
				{
					$export_data[$key][] = '';
				}
				
				if (isset($value->user_details->esi_amount) && !empty($value->user_details->esi_amount))
				{
					$export_data[$key][] = $value->user_details->esi_amount;
				}
				else
				{
					$export_data[$key][] = '';
				}
				

					
				$export_data_all[$key] = $export_data;
					
				$dataFound++;
			}
			
			//echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
    }
}
