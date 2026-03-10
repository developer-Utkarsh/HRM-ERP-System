<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingPlace extends Model
{
    protected $table = 'meeting_place';
    protected $fillable = ['branch','name','status','is_deleted'];
	
}
