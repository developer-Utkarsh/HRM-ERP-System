<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userdetails extends Model
{
	protected $fillable = ['user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','branch_id','joining_date','probation','probation_to','probation_from','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds','pf_amount','pf_date','is_pf','esi_amount','esi_date','is_esi','aadhar_card_no','aadhar_name','pan_no','pan_name','official_no','previous_experience','esic_no','uan_no','timing_shift_in','timing_shift_out','bank_emp_name','emp_file_no','pl','cl','sl','anniversary_date'];


	public function branch() {
        return $this->Belongsto(Branch::class, 'branch_id');
    }

    public function user() {
        return $this->Belongsto(User::class);
    }
	
	public function degination() {
        return $this->Belongsto(Designation::class, 'degination', 'name');
    }
}
