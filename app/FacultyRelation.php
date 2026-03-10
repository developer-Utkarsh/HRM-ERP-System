<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacultyRelation extends Model
{
    protected $fillable = ['user_id','from_time','to_time'];
}
