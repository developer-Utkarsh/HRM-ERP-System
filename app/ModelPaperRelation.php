<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelPaperRelation extends Model
{
	protected $table="model_paper_relations";
	
    protected $fillable = ['model_paper_id','model_paper_name','subject_id','faculty_id','content_writer_id','proof_reader_id','typist_id','no_of_question','from_question','to_question','status_by_content_writer','content_writer_document','content_writer_remark','proof_reader_document','proof_reader_remark','status_by_proof_reader','typist_remark','status_by_typist'];

    public function subject(){
		return $this->belongsTo('App\Subject','subject_id');
	}

	public function modelpaper(){
		return $this->belongsTo('App\ModelPaper','model_paper_id');
	}
	
	public function user(){
		return $this->belongsTo('App\User','faculty_id');
	}
}
