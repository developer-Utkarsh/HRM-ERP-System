<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBased extends Model
{
    protected $table = 'knowledge_based';
    protected $fillable = ['emp_id','cat_id','title','description','reference_link','status','is_deleted'];
	
}
