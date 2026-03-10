<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $table="employee_document";

    protected $fillable = ['emp_id','tenth_marksheet','twelth_marksheet','graduate','postgraduate','aadhar_card','created_at'];

    
}
