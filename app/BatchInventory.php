<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchInventory extends Model
{
	protected $table = 'batch_inventory';
    protected $fillable = ['created_by','batch_code','batch_name','name','status','type','quantity','inventory_type'];
	
	
}
