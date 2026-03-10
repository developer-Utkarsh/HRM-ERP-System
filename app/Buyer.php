<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
	protected $table="buyer";

	protected $fillable = ['name','contact_no','address','gst_no','email','is_deleted','created_at','beneficiary','account','bank_name','ifsc','bank_address','msme_uam_file','msme_uam_no','bank_proof','declaration_form','pan_no','status','pincode','credit_day','type','gst_img','pan_img','bank_proof_2','aggrement','msme_category'];
}
