<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	protected $table = 'task';
	
    protected $fillable = ['emp_id','date','assigned_users','is_deleted'];

    public function task_details() {
        return $this->hasMany(TaskDetail::class, 'task_id');
    }
	
	public function user() {
        return $this->Belongsto(User::class, 'emp_id', 'id');
    }
	
}
