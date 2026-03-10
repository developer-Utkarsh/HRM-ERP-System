<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportUser extends Model
{
    protected $table = 'support_user';
    protected $fillable = ['user_id','role','category_id','status','is_deleted'];
	
}
