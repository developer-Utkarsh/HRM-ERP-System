<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
	protected $table="asset";

	protected $fillable = ['product_id','qty','is_deleted','created_at'];


}
