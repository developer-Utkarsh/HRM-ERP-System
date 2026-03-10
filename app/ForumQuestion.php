<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumQuestion extends Model
{
    protected $table = 'forum_question';
    protected $fillable = ['reg_no','mobile_no','student_name','course_name','batch_id','batch_code','location','branch_id','studio_id','assistant_id','status','question','description'];


    public function batch(){
        return $this->Belongsto(Batch::class, 'batch_id');
    }


    public function readPending(){
        return $this->hasMany(ForumComment::class,'question_id','id')->where('is_read',0);
    }
	
}
