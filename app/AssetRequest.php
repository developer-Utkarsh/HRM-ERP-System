<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
	protected $table= "asset_request";

	protected $fillable = ['user_id','category','scategory','qty','type','title','requirement','updated_at','created_at','is_deleted','delete_id','unique_no','product_status','image','product_id','remark','request_type','emp_grn','inventory_status','inventory_grn'];
	
}
