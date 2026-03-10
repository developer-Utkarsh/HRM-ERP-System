<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppraisalQuestions extends Model
{
	protected $table = 'appraisal_questions';
    protected $fillable = ['question','marks','created_by','status'];
	
}
