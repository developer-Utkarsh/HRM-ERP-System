<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSubjectRelation extends Model
{
    protected $fillable = ['course_id', 'subject_id'];

    public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}
}
