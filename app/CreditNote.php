<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $table="credit_note";

    protected $fillable = ['name','invoice_no','order_number','payment_id','state','status','captured','description','email','contact','taxable_amount','igst','cgst','sgst','amount','date','c_date','created_at'];

    
}
