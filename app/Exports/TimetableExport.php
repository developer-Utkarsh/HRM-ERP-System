<?php
namespace App\Exports;

use App\Studio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TimetableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function headings():
	array
	{
		return ["S. No.", "Branch", "Studio", "Floor", "Assistant Name", "Assistant Mobile", "Faculty", "Faculty Mobile", "Batch", "Course", "Subject", "Chapter", "Topic", "From Time", "To Time", "Date", ];
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if (is_array($this->id) && !empty($this->id))
		{

			$data = Studio::with(['branch', 'assistant', 'timetable.faculty', 'timetable.batch', 'timetable.course', 'timetable.subject', 'timetable.chapter', 'timetable.topic'])->whereIn('branch_id', $this->id)
				->get();

			$export_data_all = [];
			

			foreach ($data as $key1 => $value)
			{
				$export_data = [];
				if (count($value->timetable) > 0)
				{
					foreach ($value->timetable as $key => $timetable)
					{

						$export_data[$key][] = $key + 1;

						if (isset($value
							->branch
							->name) && !empty($value
							->branch
							->name))
						{
							$export_data[$key][] = $value
								->branch->name;
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

						if (isset($value->floor) && !empty($value->floor))
						{
							$export_data[$key][] = $value->floor;
						}
						else
						{
							$export_data[$key][] = '';
						}

						if (isset($value
							->assistant
							->name) && !empty($value
							->assistant
							->name))
						{
							$export_data[$key][] = $value
								->assistant->name;
						}
						else
						{
							$export_data[$key][] = '';
						}

						if (isset($value
							->assistant
							->mobile) && !empty($value
							->assistant
							->mobile))
						{
							$export_data[$key][] = $value
								->assistant->mobile;
						}
						else
						{
							$export_data[$key][] = '';
						}

						if (isset($timetable
							->faculty
							->name) && !empty($timetable
							->faculty
							->name))
						{
							$export_data[$key][] = $timetable
								->faculty->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable
							->faculty
							->mobile) && !empty($timetable
							->faculty
							->mobile))
						{
							$export_data[$key][] = $timetable
								->faculty->mobile;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->batch) && !empty($timetable->batch))
						{
							$export_data[$key][] = $timetable
								->batch->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->course) && !empty($timetable->course))
						{
							$export_data[$key][] = $timetable
								->course->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->subject) && !empty($timetable->subject))
						{
							$export_data[$key][] = $timetable
								->subject->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->chapter) && !empty($timetable->chapter))
						{
							$export_data[$key][] = $timetable
								->chapter->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->topic) && !empty($timetable->topic))
						{
							$export_data[$key][] = $timetable
								->topic->name;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->from_time) && !empty($timetable->from_time))
						{
							$export_data[$key][] = $timetable->from_time;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->to_time) && !empty($timetable->to_time))
						{
							$export_data[$key][] = $timetable->to_time;
						}
						else
						{
							$export_data[$key][] = '';
						}
						if (isset($timetable->cdate) && !empty($timetable->cdate))
						{
							$export_data[$key][] = date('d-m-Y', strtotime($timetable->cdate));
						}
						else
						{
							$export_data[$key][] = '';
						}
						
					}
					
					$export_data_all[$key1] = $export_data;
					// echo "<pre>"; print_r($export_data_all); die;
				}
			}
			
			// echo "<pre>"; print_r($export_data_all); die;
			return collect($export_data_all);
		}
	}
}
        
