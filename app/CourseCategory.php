<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    protected $table = 'course_category';
    protected $fillable = ['name','status','is_deleted'];
	
}
