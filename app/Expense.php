<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expense';
    protected $fillable = ['user_id','title','cat_id','amount','remark','file_name','status','is_deleted','edate'];
	
}
