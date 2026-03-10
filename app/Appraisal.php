<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
	protected $table = 'appraisal';
    protected $fillable = ['name','from_date','hquestion','to_date','status'];
	
}
