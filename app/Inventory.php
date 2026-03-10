<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = 'inventory';
	protected $fillable = ['product_id','product_date','maintains','warranty','warranty_period','expiry_date','is_consumer','qty','measurement','price','buyer_id','bill_no','bill_file','type','status','is_parent','is_deleted','created_at','location','mode','remark','inventory_location','product_one','product_two','model_no','serial_no'];


}
