<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancelClass extends Model
{
	protected $table="cancel_class";

	protected $fillable = ['timetable_id','days','faculty_reason','admin_reason','status'];
}
