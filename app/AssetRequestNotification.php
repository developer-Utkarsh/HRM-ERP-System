<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetRequestNotification extends Model
{
	protected $table= "asset_request_notification";
    protected $fillable = ['sender_id','request_id','receiver_id','message','reason','status','purchase_status','purchase_reason','dm_status','it_status','company','address','gstin','phone','pdate','po_no','location','approved','narration','terms','advance','dh_approved','final_amt','advance_amt','po_important','it_updated','pt_updated','pt_approve','po_approve','po_month','po_location'];
} 
