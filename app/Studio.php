<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable = ['user_id','name','assistant_id','studio_slot','branch_id','floor','status','order_no','type','capacity','is_deleted','is_obs'];

    public function assistant() {
        return $this->Belongsto(User::class, 'assistant_id');
    }

    public function branch() {
        return $this->Belongsto(Branch::class, 'branch_id');
    }

    public function timetable() {
        return $this->hasMany(Timetable::class, 'studio_id');
    }
}
