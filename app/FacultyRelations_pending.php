<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacultyRelations_pending extends Model
{
	protected $table = 'faculty_relations_pending';
	
    protected $fillable = ['user_id','from_time','to_time'];
}
