<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveDetail extends Model
{
    protected $fillable = ['leave_id','date','type','status','leave_reason','category','updated_by'];
}
