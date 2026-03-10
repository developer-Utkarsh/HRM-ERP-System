<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	protected $table = 'attendance';
    protected $fillable = ['emp_id','type','date','time','image','latitude','longitude','admin_id','updated_by','location','reason','for_reason'];
	
	public function user() {
        return $this->Belongsto(User::class, 'emp_id', 'id');
    }
	
}
