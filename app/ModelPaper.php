<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelPaper extends Model
{
    protected $table="model_paper";

    protected $fillable = ['user_id','name','course_id','end_date','model_paper_count','status','is_deleted','type'];

    public function model_paper_relations() {
        return $this->hasMany(ModelPaperRelation::class, 'model_paper_id');
    }

    public function course(){
		return $this->belongsTo('App\Course','course_id');
	}
}
