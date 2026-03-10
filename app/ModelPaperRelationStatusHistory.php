<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelPaperRelationStatusHistory extends Model
{
	protected $table="model_paper_status_history";
	
    protected $fillable = ['user_id','model_paper_relations_id','status','document','remark','type'];

	public function modelpaperrelation(){
		return $this->belongsTo('App\ModelPaperRelation','model_paper_relation_id');
	}
}
