<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StartClass extends Model
{
	protected $fillable = ['timetable_id','start_time','end_time','sc_date','status'];

	public function timetable(){
		return $this->belongsTo('App\Timetable','timetable_id');
	}
}
