<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batchrelation extends Model
{
    protected $fillable = ['batch_id', 'subject_id','faculty_id','no_of_hours','subject_status','complete_date'];

    public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}

	public function batch(){
		return $this->belongsTo('App\Batch','batch_id');
	}

	public function chapter(){
		return $this->belongsTo('App\Chapter','subject_id');
	}

	public function topic(){
		return $this->belongsTo('App\Topic','subject_id');
	}
	
	public function user(){
		return $this->belongsTo('App\User','faculty_id');
	}
}
