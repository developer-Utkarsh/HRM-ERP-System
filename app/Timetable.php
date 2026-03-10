<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Timetable extends Model
{
    protected $fillable = ['branch_id','user_id','studio_id','faculty_id','batch_id','course_id','subject_id','chapter_id','topic_id','from_time','to_time','cdate','assistant_id','time_table_parent_id','class_type','is_deleted','online_class_type','remark','schedule_type','topic_mlt','erp_json'];

    public function batch(){
		return $this->belongsTo('App\Batch','batch_id');
	}

	public function course(){
		return $this->belongsTo('App\Course','course_id');
	}

    public function faculty(){
		return $this->belongsTo('App\User','faculty_id');
	}
	public function assistant(){
		return $this->belongsTo('App\User','assistant_id');
	}

	public function studio(){
		return $this->belongsTo('App\Studio','studio_id');
	}

	public function topic(){
		return $this->belongsTo('App\Topic','topic_id');
	}

	public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}

	public function chapter(){
		return $this->belongsTo('App\Chapter','chapter_id');
	}

	public function reschedule(){
		return $this->hasMany(Reschedule::class);
	}

	public function swap(){
		return $this->hasMany(Swap::class);
	}

	public function cancelclass(){
		return $this->hasMany('App\CancelClass');
	}

	public function startclass(){
		return $this->hasMany(StartClass::class);
	}	
}
