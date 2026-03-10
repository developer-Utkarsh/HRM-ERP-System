<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
	protected $fillable = ['user_id','description','status','is_deleted'];

	public function user() {
        return $this->Belongsto(User::class);
    }
}
