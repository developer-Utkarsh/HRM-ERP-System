<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
	protected $table="course";

	protected $fillable = ['user_id','name','category','status','is_deleted'];

	public function course_subjects() {
		return $this->belongsToMany('App\Subject', 'course_subject_relations', 'course_id', 'subject_id');
	}
}
