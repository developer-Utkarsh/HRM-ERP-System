<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $fillable = ['task_id','task_date','task_title','task_description','task_added_by','task_added_to','plan_hour','spent_hour','parent_id','task_type','task_file_link','task_priority','status','remark'];
}
