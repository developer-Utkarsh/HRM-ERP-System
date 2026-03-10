<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
	protected $table="chapter";

	protected $fillable = ['user_id','course_id','subject_id','name','duration', 'status','is_deleted'];

	public function course(){
		return $this->belongsTo('App\Course','course_id');
	}

	public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}
}
