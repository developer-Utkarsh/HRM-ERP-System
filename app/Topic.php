<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table="topic";

    protected $fillable = ['user_id','course_id','subject_id','chapter_id','name','duration','status','is_deleted'];

    public function course(){
		return $this->belongsTo('App\Course','course_id');
	}

	public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}

	public function chapter(){
		return $this->belongsTo('App\Chapter','chapter_id');
	}
}
