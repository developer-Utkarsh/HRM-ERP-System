<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name','status','address','related','nickname','is_deleted','show_in_web','gallery','cover_image'];
	
	public function studio() {
        return $this->hasMany(Studio::class, 'branch_id');
    }
	
	public function user_branches() {
        return $this->hasMany(Userbranches::class, 'branch_id');
    }
}
