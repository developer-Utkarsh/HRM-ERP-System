<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackForm extends Model
{
    protected $table = 'feedbackforms';
    protected $fillable = ['form_id','form_name','form_description','department','question_ids','start_time','end_time','created_at','updated_at','deleted_at','is_deleted','is_comman_questions','is_infrastructure','is_status'];
	
}
