<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
	protected $table="transfer";

	protected $fillable = ['product_id','transfer_from','transfer_to','qty','is_deleted','created_at'];


}
