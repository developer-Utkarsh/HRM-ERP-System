<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Swap extends Model
{
	protected $table="swap";

	protected $fillable = ['timetable_id', 'swap_with_faculty_id','swap_timetable_id','status'];

	public function s_timetable(){
		return $this->belongsTo('App\Timetable','timetable_id');
	}

	public function faculty(){
		return $this->belongsTo('App\User','swap_with_faculty_id');
	}

	public function swap_timetable(){
		return $this->belongsTo('App\Timetable','swap_timetable_id');
	}
}
