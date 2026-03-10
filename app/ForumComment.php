<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = 'forum_comment';
    protected $fillable = ['question_id','parent_id','by_reg_no','by_name','reply_to_name','comment','status','is_read','created_at'];
	
}
