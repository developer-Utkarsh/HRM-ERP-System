<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBasedCategory extends Model
{
    protected $table = 'knowledge_based_category';
    protected $fillable = ['name','status','is_deleted'];
	
}
