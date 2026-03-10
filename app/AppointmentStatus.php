<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends Model
{
    protected $table = 'appointment_status';
    protected $fillable = ['appointment_id','emp_id','status','remark','is_deleted'];
	
}
