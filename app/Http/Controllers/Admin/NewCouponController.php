<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CouponMail;
use App\User;
use Input;
use DB;
use App\Appraisal;
use App\AppraisalQuestions;
use App\NewTask;
use Auth;
use App\ApiNotification;

class NewCouponController extends Controller
{
    
    public function index()
    {  
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://support.utkarshapp.com/support_model/coupon/coupon_list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept-Charset: UTF-8',
                'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
                'Accept: application/json',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $responseData = json_decode($response, true);
        
        $coupons = $responseData['data'] ?? [];
        
        // print_r($coupons); die('s');
        return view('admin.newcoupon.index', compact('coupons'));
    }

    public function addcoupon(Request $request){
        $request->validate([
            'coupon' => 'required',
            'categaory_name'    => 'required',
            'sub_category'    => 'required',
            'coupon_title'    => 'required',
            'start_date'    => 'required',
            'end_date'    => 'required',
            'discount_type'    => 'required',
            'coupon_value'    => 'required',
            'max_discount'    => 'required',
            'max_usage'    => 'required',
            'coupon_type'    => 'required',
            'coupon_mode'    => 'required',
            'course_type'    => 'required',
        ]); 

		$idata  = array(
			"coupon_id"			=>	0,
			"categaory_name"	=>	$request->categaory_name,
			"sub_category"		=>	$request->sub_category,
			"coupon_title"		=>	$request->coupon_title,
			"start_date"		=>	$request->start_date,
			"end_date"			=>	$request->end_date,
			"coupon_value"		=>	$request->coupon_value,
			"discount_type"		=>	$request->discount_type,
			"max_discount"		=>	$request->max_discount,
			"max_usage"			=>	$request->max_usage,
			"coupon_type"		=>	$request->coupon_type,
			"coupon_mode"		=>	$request->coupon_mode,
			"course_type"		=>	$request->course_type,
			"data_link"			=>	$request->data_link,
			"reason"			=>	$request->reason,
			"remark"			=>	$request->remark??'',
			"reject_remark"		=>	$request->reject_remark??'',
			"created_by"  		=> Auth::user()->id,
		);
		
		$id = DB::table('erp_coupon_approval')->insertGetId($idata);
		
		$idata['type'] = 'new_coupon';
		$idata['approver_name'] = 'Arpit Parik Sir';
		$idata['sender_name'] = Auth::user()->name;
		$email = 'arpit.pareek@utkarsh.com'; // arpit.pareek@utkarsh.com
		$fromName = 'Coupon Approval Request';
		Mail::to($email)
			->send(
				(new CouponMail($idata))
					->from(env('MAIL_FROM_ADDRESS'), $fromName)
			);
		
		echo json_encode(['status' => 'success',  'message' => 'success','insert_id' => $id]);
		
	}

    public function addRemark(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required',
            'coupon_title' => 'required',
            'remark'    => 'required|string|max:500',
			'categaory_name'    => 'required',
            'sub_category'    => 'required',
        ]);

        $couponId = $request->coupon_id;
        $remark   = $request->remark;

        if($couponId){
			$idata  = array(
				"coupon_id"			=>	$couponId,
				"categaory_name"		=>	$request->categaory_name,
				"sub_category"		=>	$request->sub_category,
				"coupon_title"		=>	$request->coupon_title,
				"start_date"		=>	date('Y-m-d H:i:s',$request->start_date),
				"end_date"			=>	date('Y-m-d H:i:s',$request->end_date),
				"coupon_value"		=>	$request->coupon_value,
				"discount_type"		=>	$request->discount_type,
				"max_discount"		=>	$request->max_discount,
				"max_usage"			=>	$request->max_usage,
				"coupon_type"		=>	$request->coupon_type,
				"coupon_mode"		=>	$request->coupon_mode,
				"course_type"		=>	$request->course_type,
				"data_link"			=>	$request->data_link??'',
				"reason"			=>	$request->reason??'',
				"reject_remark"		=>	$request->reject_remark??'',
				"remark"			=>	$request->remark??'',
				"created_by"  		=> Auth::user()->id,
			);

            $id = DB::table('erp_coupon_approval')->insertGetId($idata);
			
			$idata['type'] = 'remark';
			$idata['sender_name'] = Auth::user()->name;
			$idata['approver_name'] = 'Arpit Parik Sir';
			$email = 'arpit.pareek@utkarsh.com'; // arpit.pareek@utkarsh.com // mahendragehlot.utkarsh@gmail.com
			$fromName = 'Coupon Edit/User Assign Request – Approval Needed';
            Mail::to($email)
                ->send(
                    (new CouponMail($idata))
                        ->from(env('MAIL_FROM_ADDRESS'), $fromName)
                );
				
            echo json_encode(['status' => 'success',  'message' => 'success','insert_id' => $id]);
		}else{
			echo json_encode(['status' => 'false', 'message' => 'etorr']);
		}
    }
    
    public function updateStatus(Request $request)
    {
		if($request->action=='approved_by'){
			$request->validate([
				'id' => 'required|exists:erp_coupon_approval,id',
				'status' => 'required|in:0,1,2',
				'reject_remark' => 'nullable|string|max:255'
			]);
			
			$data = [
			    'status' => $request->status,
			    'status_updated_by' => Auth::user()->id,
			    'updated_at' => now()
			];

			if ($request->status == 2) {
				$data['reject_remark'] = $request->reject_remark;
			}
			
			$updated = DB::table('erp_coupon_approval')->where('id', $request->id)->update($data);
			if ($updated) {
				
				if ($request->status == 1) {
					
					$coupon_detail = DB::table('erp_coupon_approval')->where('id', $request->id)->first();
			
					$idata  = array(
						"coupon_id"			=>	$coupon_detail->id,
						"categaory_name"	=>	$coupon_detail->categaory_name,
						"sub_category"		=>	$coupon_detail->sub_category,
						"coupon_title"		=>	$coupon_detail->coupon_title,
						"start_date"		=>	$coupon_detail->start_date,
						"end_date"			=>	$coupon_detail->end_date,
						"coupon_value"		=>	$coupon_detail->coupon_value,
						"discount_type"		=>	$coupon_detail->discount_type,
						"max_discount"		=>	$coupon_detail->max_discount,
						"max_usage"			=>	$coupon_detail->max_usage,
						"coupon_type"		=>	$coupon_detail->coupon_type,
						"coupon_mode"		=>	$coupon_detail->coupon_mode,
						"course_type"		=>	$coupon_detail->course_type,
						"data_link"			=>	$coupon_detail->data_link,
						"reason"			=>	$coupon_detail->reason,
						"remark"			=>	$coupon_detail->remark,
						"reject_remark"		=>	$coupon_detail->reject_remark
					);
					
					$idata['sender_name'] = Auth::user()->name;
					$email = 'archana.bohra@utkarsh.com'; // archana.bohra@utkarsh.com
					// $cc_email = 'vishal.dourwal@utkarsh.com'; // vishal.dourwal@utkarsh.com
					$cc_email = ['vishal.dourwal@utkarsh.com', 'arpit.pareek@utkarsh.com'];
					if($coupon_detail->id > 0 ){
						$idata['type'] = 'approved_update_coupon';
						$fromName = 'Approved – Coupon Edit/User Assign Request';
						Mail::to($email)->cc($cc_email)
							->send(
								(new CouponMail($idata))
									->from(env('MAIL_FROM_ADDRESS'), $fromName)
							);
					}
					else{
						$idata['type'] = 'approved_new_coupon';
						$fromName = 'Approved – Proceed with Coupon Creation in ERP';
						Mail::to($email)->cc($cc_email)
							->send(
								(new CouponMail($idata))
									->from(env('MAIL_FROM_ADDRESS'), $fromName)
							);
					}
				}
				
				return response()->json([
					'status' => 'success',
					'message' => 'Status updated successfully'
				]);
			} else {
				return response()->json([
					'status' => 'error',
					'message' => 'Something went wrong'
				]);
			}
			
			
		}
		else if($request->action=='mark_on_erp'){
			$updated = DB::table('erp_coupon_approval')->where('id', $request->id)
			->update([
				'erp_mark_status' => 1,
				'erp_mark_updated_by' => Auth::user()->id
			]);
			if ($updated) {
				return response()->json([
					'status' => 'success',
					'message' => 'Status updated successfully'
				]);
			} else {
				return response()->json([
					'status' => 'error',
					'message' => 'Something went wrong'
				]);
			}
		}
    }
    
   public function historyList(Request $request) {
    $query = DB::table('erp_coupon_approval as c')
        ->leftJoin('users as u1', 'c.created_by', '=', 'u1.id')
        ->leftJoin('users as u2', 'c.status_updated_by', '=', 'u2.id')
        ->leftJoin('users as u3', 'c.erp_mark_updated_by', '=', 'u3.id')
        ->select(
            'c.*',
            'u1.name as created_by_name',
            'u2.name as status_updated_by_name',
            'u3.name as erp_mark_updated_by_name'
        );

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('coupon_id', 'like', "%{$search}%")
              ->orWhere('coupon_title', 'like', "%{$search}%")
              ->orWhere('remark', 'like', "%{$search}%")
              ->orWhere('u1.name', 'like', "%{$search}%")
              ->orWhere('u2.name', 'like', "%{$search}%");
        });
    }
    $coupons = $query->orderBy('id','desc')->get();
    return view('admin.newcoupon.coupon-history', compact('coupons'));
    }
}
