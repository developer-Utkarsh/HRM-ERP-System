<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class TimetableTopic extends Model
{
    
    protected $table = 'timetable_topic';
    protected $fillable = ['timetable_id','topic_id','chapter_id','status','batch_id','subject_id','source','created_at','updated_at'];	
}
