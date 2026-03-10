<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
	protected $fillable = ['title','date','user_id','status','type','branch_id','is_deleted','created_at'];
}
