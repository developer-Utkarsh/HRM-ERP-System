<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypistWorkReport extends Model
{
	protected $table = 'typist_work_report';
	
    protected $fillable = ['emp_id','number_of_questions','ocr_panel','arrange_correction','total_page','remark','cdate'];
	
	public function employee(){
		return $this->belongsTo('App\User','emp_id');
	}
}
