<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users_pending extends Authenticatable
{
    
	protected $table = 'users_pending';
	
    protected $fillable = ['user_id','role_id','register_id','name', 'email','mobile','image','password','gsm_token','status'];

    public function user_details_pending() {
        return $this->hasOne(Userdetails_pending::class, 'user_id', 'user_id');
    }

    public function role() {
        return $this->Belongsto(Role::class, 'role_id');
    }

    public function faculty_relations_pending() {
        return $this->hasMany(FacultyRelations_pending::class, 'user_id', 'user_id');
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
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
