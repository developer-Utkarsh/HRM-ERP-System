<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
	protected $table="material_form";

	protected $fillable = ['user_id','item_description','unit','remark','status','date'];


}
