<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryIncrement extends Model
{
	protected $table = 'salary_increment';
	protected $fillable = ['user_id','emp_code','increment_amount','loan_amount','arrear_amount','arrear_day','tds_amount','date','created_by','salary','increment_remark','deduction_remark'];
}
