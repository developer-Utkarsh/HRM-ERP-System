<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
Use DB;

class Batch extends Model
{
    protected $table="batch";

    protected $fillable = ['user_id','course_id','name','start_date','end_date','branch','status','is_deleted','type','batch_code','venue','nickname','capacity','total_test','mentor_id','chopal_agent_id','category_head','course_planer_enable','erp_course_id','master_planner'];

    public function batch_relations() {
        return $this->hasMany(Batchrelation::class, 'batch_id');
    }

    public function course(){
		return $this->belongsTo('App\Course','course_id');
	}
	
	
	public function batch_timetables() {
        return $this->hasMany(Timetable::class, 'batch_id');
    }
	

    public function categoryhead() {
        //DB::table('users')->where('id',$this->category_head)
        return $this->Belongsto(User::class, 'category_head');
    }

    public function mentor() {
        //DB::table('users')->where('id',$this->category_head)
        return $this->Belongsto(User::class, 'mentor_id');
    }
}
