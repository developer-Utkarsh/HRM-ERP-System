<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubDepartment extends Model
{
	protected $fillable = ['department_id','name','status','is_deleted'];

	public function department() {
        return $this->Belongsto(Department::class);
    }
}
