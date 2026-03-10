<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceLock extends Model
{
	protected $table = 'attendance_lock';
    protected $fillable = ['month','status','created_at','updated_at'];
	
}
