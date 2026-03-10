<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userbranches extends Model
{
	protected $fillable = ['user_id','branch_id','is_deleted'];
	
	public function branch() {
        return $this->Belongsto(Branch::class, 'branch_id');
    }
	
	public function user() {
        return $this->Belongsto(User::class);
    }
	
	public function studio() {
        return $this->hasMany(Studio::class, 'branch_id', 'branch_id');
    }
}
