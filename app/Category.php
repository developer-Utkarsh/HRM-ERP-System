<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $table="category";

	protected $fillable = ['name','parent','is_deleted','created_at'];


}
