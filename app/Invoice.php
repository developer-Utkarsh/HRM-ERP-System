<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table="invoice";

    protected $fillable = ['name','invoice_no','order_number','payment_id','state','status','captured','description','email','contact','taxable_amount','igst','cgst','sgst','amount','date','c_date','created_date'];

    
}
