<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table="subject";

    protected $fillable = ['user_id','course_id', 'name', 'status','is_deleted'];
	
	public function timetable() {
        return $this->hasMany(Timetable::class, 'subject_id');
    }
	
}
