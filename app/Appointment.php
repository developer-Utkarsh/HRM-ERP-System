<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $fillable = ['user_id','title','description','branch_id','meeting_place_id','appointment_date','start_time','end_time','type','url','other_city','other_place','is_group','key_points','cancel_reason','status','is_deleted','created_at','updated_at','private_key_point','event_id'];
	
}
