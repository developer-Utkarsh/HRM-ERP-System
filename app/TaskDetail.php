<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskDetail extends Model
{
    protected $fillable = ['parent_task_detail_id','task_id','name','plan_hour','spent_hour','status','description','assigned_userid','assigned_date','dropped_reason','is_deleted'];
}
