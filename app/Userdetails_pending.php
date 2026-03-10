<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userdetails_pending extends Model
{
	protected $table = 'userdetails_pending';
	
	protected $fillable = ['user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','branch_id','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds'];


	public function branch() {
        return $this->Belongsto(Branch::class, 'branch_id');
    }

    public function users_pending() {
        return $this->Belongsto(Users_pending::class);
    }
}
