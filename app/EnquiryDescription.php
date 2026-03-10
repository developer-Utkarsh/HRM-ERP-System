<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnquiryDescription extends Model
{
    protected $table = 'enquiry_description';
    protected $fillable = ['enquiry_id','description','user_id'];
	
}
