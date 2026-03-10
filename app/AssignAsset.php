<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignAsset extends Model
{
	protected $table="assign_asset";

	protected $fillable = ['asset_id','assigned_by','emp_id','qty','is_accepted','created_at','remark'];
	
	public function asset() {
        return $this->Belongsto(Asset::class);
    }


}
