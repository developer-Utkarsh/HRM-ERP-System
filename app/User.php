<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id','register_id','name', 'email','mobile','nominee_name','image','password','gsm_token','status','admin_approval','supervisor_id','department_type','sub_department_type','is_deleted','reason','reason_date','inactive_date','delete_date','login_otp','nickname','total_time','delete_id','edit_id','is_extra_working_salary','comp_off_start_date','course_category','agreement','committed_hours','device_type','agreement_start_date','agreement_end_date','email_verified_at','asset_requirement','online_discount','offline_discount','last_password_update','darwin_code'];

    public function user_details() {
        return $this->hasOne(Userdetails::class, 'user_id');
    }

    public function role() {
        return $this->Belongsto(Role::class, 'role_id');
    }

    public function faculty_relations() {
        return $this->hasMany(FacultyRelation::class, 'user_id');
    }

    public function batchrelations() {
        return $this->hasMany(Batchrelation::class, 'faculty_id');
    }

    public function timetable() {
        return $this->hasMany(Timetable::class, 'faculty_id');
    }
	
	public function user_branches() {
        return $this->hasMany(Userbranches::class, 'user_id');
    }
	
	public function faculty_subjects() {
        return $this->hasMany(FacultySubject::class, 'user_id');
    }
	
	public function department() {
        return $this->Belongsto(Department::class, 'department_type');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
        // 'email_verified_at' => 'datetime',
    // ];
}
