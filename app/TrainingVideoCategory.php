<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingVideoCategory extends Model
{
    protected $table = 'training_video_category';
    protected $fillable = ['name','status','is_deleted'];
	
}
