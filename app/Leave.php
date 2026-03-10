<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
	protected $table = 'leave';
	
    protected $fillable = ['emp_id','reason'];

    public function leave_details() {
        return $this->hasMany(LeaveDetail::class, 'leave_id');
    }
	
	public function user() {
        return $this->Belongsto(User::class, 'emp_id', 'id');
    }
	
	public function user_details() {
        return $this->Belongsto(Userdetails::class, 'emp_id', 'user_id');
    }
}
