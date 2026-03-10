<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchSubject extends Model
{
    protected $table="batch_subject";

    protected $fillable = ['subject_id','batch_id','faculty_id'];
}
