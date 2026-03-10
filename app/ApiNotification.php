<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiNotification extends Model
{
    protected $fillable = ['title','description','image','date','sender_id','receiver_id','type','is_deleted','appointment_id'];
}
