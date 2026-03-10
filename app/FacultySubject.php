<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacultySubject extends Model
{
    protected $fillable = ['user_id','subject_id'];
	
	public function subject() {
        return $this->Belongsto(Subject::class, 'subject_id');
    }
}
