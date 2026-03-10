<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportCategory extends Model
{
    protected $table = 'support_category';
    protected $fillable = ['name','status','is_deleted'];
	
}
