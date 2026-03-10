<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'enquiry';
    protected $fillable = ['query_id','course_type','course_name','mobile_no','category_id','status','date'];
	
}
