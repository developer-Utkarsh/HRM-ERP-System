<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    protected $table = 'feedbackquestion';
    protected $fillable = ['qid','question','question_keyword','qtype','options','qfor','status','deleted_at','created_at','updated_at'];
	
}
