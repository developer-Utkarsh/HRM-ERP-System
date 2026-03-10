<?php

namespace App\Exports;

use App\Branch;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LeaveExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct( $leave_array){
		$this->leave_array = $leave_array;
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Employee Code",
			"Employee Name",
			"Branch Name",
			"Department",
			"Designation",
			"Date",
			"Category",
			"Type",
			"Reason",
			"Status"		
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if (!empty($this->leave_array))
		{
			$leave_array = $this->leave_array;
			$export_data_all = [];
			
			$i = 0;
			$ii = 0;
			foreach($leave_array as $value){
				/* foreach ($leavArray as $key1 => $value)
				{ */ 
					$export_data = [];
					$branch_name = "";
					if(isset($value->user->user_branches) && !empty($value->user->user_branches)){
						$user_branches = $value->user->user_branches;
						foreach($user_branches as $branchDetails){
							$branch_data = \App\Branch::where('id', $branchDetails->branch_id)->first();
							if(!empty($branch_data)){
								$branch_name .= $branch_data->name.", ";
							}
							
						}
						$branch_name = rtrim($branch_name,", ");
					}
					
					$department = \App\Department::where('id', $value->user->department_type)->first();
					
					if(!empty($value->user->id)){
						if(!empty($value->leave_details) && count($value->leave_details) > 0){
							foreach($value->leave_details as $key=>$leave_details){  
								$i++;
								$export_data[$key][] = $i;
								$export_data[$key][] = isset($value->user->register_id) ?  $value->user->register_id : '';
								$export_data[$key][] = isset($value->user->name) ?  $value->user->name : '';
								$export_data[$key][] = $branch_name;
								$export_data[$key][] = isset($department->name)?$department->name:'';
								$export_data[$key][] = isset($value->user_details->degination) ?  $value->user_details->degination : '';
								$export_data[$key][] = isset($leave_details->date) ?  date("d-m-Y", strtotime($leave_details->date)) : '';
								$export_data[$key][] = $leave_details->category;
								$export_data[$key][] = $leave_details->type;
								$export_data[$key][] = $value->reason;
								$export_data[$key][] = $leave_details->status;
							}
						}
					}
					
					$export_data_all[$ii] = $export_data;
					$ii++;
					
				//}
			}
			 //echo '<pre>'; print_r($export_data_all);die;
			return collect($export_data_all);
		}
    }
}
