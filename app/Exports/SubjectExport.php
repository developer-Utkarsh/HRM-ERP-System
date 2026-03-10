<?php
namespace App\Exports;

use App\Studio;
use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;

class SubjectExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function registerEvents():
	array
	{
		return [AfterSheet::class => function (AfterSheet $event)
		{
			$cellRange = 'A1:W1';
			$event
				->sheet
				->getDelegate()
				->getStyle($cellRange)->getFont()
				->setSize(14);
		}
		, ];
	}

	public function __construct($get_data)
	{
		$this->get_data = $get_data;
	}

	public function headings():
	array
	{
		return ["S. No.","Subject", "Branch", "Faculty", "From Time", "To Time", "Duration", "Status", "Date" ];
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if (!empty($this->get_data))
		{
			$get_data = $this->get_data;
			$export_data_all = [];
			
			$dataFound = 0;
			foreach ($get_data as $key1 => $value)
			{
				$export_data = [];
				if (count($value->timetable) > 0)
				{
					foreach ($value->timetable as $key => $timetable)
					{
						if(!empty($timetable->studio->branch->name)){
							$dataFound++;
							
							$status = "";
							$duration = "00 : 00 Hours";
							$startClass = \App\StartClass::where('timetable_id', $timetable->id)->first();
							if(!empty($startClass)){
								$status = $startClass->status;
								if(!empty($startClass->start_time) && !empty($startClass->end_time)){
									$first_date = new DateTime($startClass->start_time);
									$second_date = new DateTime($startClass->end_time);
									$interval = $first_date->diff($second_date);
									$duration = $interval->format('%H : %I Hours'); // H Hour I minut
									
									//$interval->format('%Y years %M months and %D days %H hours %I minutes and %S seconds.');
								}
							}
							
							$export_data[$key][] = $dataFound;

							if (isset($value->name) && !empty($value->name))
							{
								$export_data[$key][] = $value->name;
							}
							else
							{
								$export_data[$key][] = '';
							}
							
							if (isset($timetable->studio->branch->name) && !empty($timetable->studio->branch->name))
							{
								$export_data[$key][] = $timetable->studio->branch->name;
							}
							else
							{
								$export_data[$key][] = '';
							}
							
							if (isset($timetable->faculty->name) && !empty($timetable->faculty->name))
							{
								$export_data[$key][] = $timetable->faculty->name;
							}
							else
							{
								$export_data[$key][] = '';
							}
							
							if (isset($timetable->from_time) && !empty($timetable->from_time))
							{
								$export_data[$key][] = date('h:i A', strtotime($timetable->from_time));
							}
							else
							{
								$export_data[$key][] = '';
							}
							
							if (isset($timetable->to_time) && !empty($timetable->to_time)){
								$export_data[$key][] = date('h:i A', strtotime($timetable->to_time));
							}
							else{
								$export_data[$key][] = '';
							}
							
							$export_data[$key][] = $duration;
							
							$export_data[$key][] = $status;
							
							if (isset($timetable->cdate) && !empty($timetable->cdate)){
								$export_data[$key][] = $timetable->cdate;
							}
							else{
								$export_data[$key][] = '';
							}
						}
						
					}
					
					$export_data_all[$key1] = $export_data;
					
				}
			}
			
			// echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
		}
	}
}
        
