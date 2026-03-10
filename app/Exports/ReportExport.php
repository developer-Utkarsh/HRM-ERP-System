<?php

namespace App\Exports;

use App\StartClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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

	public function __construct( $id){
		$this->id = $id;
	}

	public function headings(): array
	{
		return [
			"S. No.",
			"Faculty",
			"Branch",
			"Batch",
			"Course",
			"Subject",
			"Chapter",
			"Topic",
            "Topic Duration",
			"Start Time",
			"End Time",
			"Working Hours",
			"Date",
			"Status",			
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	if(is_array($this->id) && !empty($this->id)){

    		$data = StartClass::with('timetable.faculty','timetable.studio.branch','timetable.batch','timetable.course','timetable.subject','timetable.chapter','timetable.topic')->whereIn('id',$this->id)->get();

    		$export_data = [];

    		foreach($data as $key => $value){

    			$export_data[$key][] = $key + 1 ;

    			if(isset($value->timetable->faculty) && !empty($value->timetable->faculty)){
    				$export_data[$key][] = $value->timetable->faculty->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->studio->branch) && !empty($value->timetable->studio->branch)){
    				$export_data[$key][] = $value->timetable->studio->branch->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->batch) && !empty($value->timetable->batch)){
    				$export_data[$key][] = $value->timetable->batch->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->course) && !empty($value->timetable->course)){
    				$export_data[$key][] = $value->timetable->course->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->subject) && !empty($value->timetable->subject)){
    				$export_data[$key][] = $value->timetable->subject->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->chapter) && !empty($value->timetable->chapter)){
    				$export_data[$key][] = $value->timetable->chapter->name;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->timetable->topic) && !empty($value->timetable->topic)){
    				$export_data[$key][] = $value->timetable->topic->name;
    			} else{
    				$export_data[$key][] = '';
    			}

                if(isset($value->timetable->topic) && !empty($value->timetable->topic)){
                    $export_data[$key][] = $value->timetable->topic->duration;
                } else{
                    $export_data[$key][] = '';
                }

    			if(isset($value->start_time) && !empty($value->start_time)){
    				$export_data[$key][] = $value->start_time;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->end_time) && !empty($value->end_time)){
    				$export_data[$key][] = $value->end_time;
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->start_time) && !empty($value->start_time)){
    				$minutes =  round(abs(strtotime($value->start_time) - strtotime($value->end_time)) / 60,2);
    				$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
    				$export_data[$key][] = $hours.' hours';
    			}else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->sc_date) && !empty($value->sc_date)){
    				$export_data[$key][] = date('d-m-Y', strtotime($value->sc_date));
    			} else{
    				$export_data[$key][] = '';
    			}

    			if(isset($value->status) && !empty($value->status)){
    				$export_data[$key][] = $value->status;
    			} else{
    				$export_data[$key][] = '';
    			}
    		}

    		return collect($export_data);	
    	}
    }
}
