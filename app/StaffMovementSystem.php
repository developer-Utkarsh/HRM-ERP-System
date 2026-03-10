<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffMovementSystem extends Model
{
	protected $table = 'staff_movement_system';
	
    protected $fillable = ['emp_id','from_time','to_time','reason','type','status','cdate','approved_by'];
	
	public function employee(){
		return $this->belongsTo('App\User','emp_id');
	}
}
