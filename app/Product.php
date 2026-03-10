<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $fillable = ['name','cat_id','sub_cat_id','status','created_at','pcode'];


}
