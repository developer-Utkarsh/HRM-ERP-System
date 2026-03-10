<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    protected $table="reschedule";

	protected $fillable = ['timetable_id','from_time','to_time','faculty_reason', 'admin_reason','studio_id','status'];
}
