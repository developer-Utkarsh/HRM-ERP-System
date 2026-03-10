<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class AttendanceRecordExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
		$total_month_days = $this->get_array[0]['total_month_days'];
		$start_date = new DateTime($this->get_array[0]['start_date']);
		$end_date = new DateTime($this->get_array[0]['end_date']);
		$array = array();
		$array[] = "S. No.";
		$array[] = "ID";
		$array[] = "Employee Code";
		$array[] = "Employee Name";
		$array[] = "Branch";
		$array[] = "Department";
		$array[] = "Designation";
		$array[] = "Mobile";
		$array[] = "DOJ";
		$array[] = "DOL";
		for($i = $start_date; $i <= $end_date; $i->modify('+1 day')){
			$array[] = $i->format("d-M");
		}
		/* for ($x = 1; $x <= $total_month_days; $x++) {
		   $array[] = $x;
		} */
		// $array[] = 'Total Present Half';
		$array[] = 'Actual Present';
		$array[] = 'Week Off + Holiday';
		$array[] = 'Holiday Working';
		$array[] = 'PL';
		$array[] = 'CL';
		$array[] = 'SL';
		$array[] = 'CO';
		$array[] = 'Actual Paid';
		$array[] = 'Absent';
		return $array;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if (!empty($this->get_array))
		{
			$get_array = $this->get_array;
			$total_month_days = $this->get_array[0]['total_month_days'];
			$export_data_all = [];
			
			$i = 0;
			$ii = 0;
			foreach ($get_array as $key1 => $value)
			{
				$export_data = []; 
				$i++;
				$export_data[$key1][] = $i;
				$export_data[$key1][] = isset($value['id']) ?  $value['id'] : '';
				$export_data[$key1][] = isset($value['register_id']) ?  $value['register_id'] : '';
				$export_data[$key1][] = isset($value['name']) ?  $value['name'] : '';
				$export_data[$key1][] = isset($value['branch_name']) ?  $value['branch_name'] : '';
				$export_data[$key1][] = isset($value['departments_name']) ?  $value['departments_name'] : '';
				$export_data[$key1][] = isset($value['designation_name']) ?  $value['designation_name'] : '';
				$export_data[$key1][] = isset($value['mobile']) ?  $value['mobile'] : '';
				$export_data[$key1][] = isset($value['joining_date']) ?  $value['joining_date'] : '';
				$export_data[$key1][] = isset($value['reason_date']) ?  $value['reason_date'] : '';
				for ($x = 1; $x <= $total_month_days; $x++) {
				   $export_data[$key1][] = $value[$x];
				}
				// $export_data[$key1][] = $value['total_present_half'];
				$export_data[$key1][] = $value['total_present'];
				$export_data[$key1][] = $value['total_week_off'];
				$export_data[$key1][] = $value['total_holiday_working'];
				$export_data[$key1][] = $value['total_pl'];
				$export_data[$key1][] = $value['total_cl'];
				$export_data[$key1][] = $value['total_sl'];
				$export_data[$key1][] = $value['total_co'];
				$export_data[$key1][] = $value['actual_paid'];
				$export_data[$key1][] = $value['total_absent'];
					
				
				$export_data_all[$ii] = $export_data;
				$ii++;
				
			}
			
			return collect($export_data_all);
		}
    }
}
