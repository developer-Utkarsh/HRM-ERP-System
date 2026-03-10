<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingVideo extends Model
{
    protected $table = 'training_video';
    protected $fillable = ['user_id','cat_id','title','date','video_id','video_url','pdf_url','image_url','description','status','type','is_deleted','department_id'];
	
}
