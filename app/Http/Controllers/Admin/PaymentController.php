<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Input;
use DB;
use Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public $rz_key="";
    public $rz_secret="";
    public $uc_token="";
    public function __construct(){
        //$this->rz_key="rzp_test_xCIdvSfCxpgHmw";
        //$this->rz_secret="98PeaGgNfb94shOQjMrj6QJr";
        
        //app live
        $this->rz_key="rzp_live_vLkWUZkSGpABUz";
        $this->rz_secret="r1InMXyFiwnDBtgKuWi1ZQnP";

        $this->uc_token="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0";
    }

    public function index()
    {  
		$query = DB::table('payment_links')
				->select('payment_links.*', 'users.name')
				->leftJoin('users', 'users.id', '=', 'payment_links.agent_id');

			if (!empty(request()->input('mobile'))) {
				$query->where("payment_links.mobile", request()->input('mobile'));
			}

			if (!empty(request()->input('status'))) {
				$query->where("payment_links.status", request()->input('status'));
			}

			if (Auth::user()->id != 7322 && Auth::user()->id !=7509 && Auth::user()->id != 901) {
				$query->where('payment_links.agent_id', Auth::user()->id);
			}

			$record = $query->where('payment_links.id', '>', 47)
				->orderBy('payment_links.id', 'desc')
				->get();

			return view('admin.payment.index', compact('record'));

    }

    public function create(Request $request)
    { 
        return view('admin.payment.coupon');
		if(!empty($request->coupon)){
          return view('admin.payment.coupon');
		}else{
		  return view('admin.payment.add');
		}
    }
	
	public function npf_student_details(Request $request){
		$npfid =	$request->npfid??'';
		$type =	$request->type??'';
		
		$url = "https://support.utkarshapp.com/index.php/getUserDetails?meritto_id=".$npfid."&type=".$type;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   'Accept-Charset: UTF-8',
		   'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
		   'Accept: application/json',
		   'Content-Type: application/json'
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$data = '';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);		
		curl_close($curl);

		return $resp;
	}
	
	public function npf_course_details(Request $request){
		$course_id =	$request->course_id??'';
		$url = "https://support.utkarshapp.com/index.php/getCourseDetail?course_id=".$course_id;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   'Accept-Charset: UTF-8',
		   'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
		   'Accept: application/json',
		   'Content-Type: application/json'
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$data = '';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);		
		curl_close($curl);

		return $resp;
	}

	public function sendpaymentlink(Request $request){
        $rules = [
            'contact_no' => 'required|numeric|digits:10',
            'is_fbt_restricted' => 'required',
            'plan_type' => 'required|numeric',
            'course_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'npf_id' => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        $auth=Auth::User();

        $course=$this->npf_course_details($request);
        $course=json_decode($course);

        if(empty($course->Status) && $course->Status!="Ok"){
           return response(['status'=>false,"message"=>'Course is not availble.'],200);
        }

        $course=$course->data;
        if($request->plan_type==0){
        	//Standarrd
        	$course_sp=$course->course_sp??'';
        }else if($request->plan_type==1){
           //prime
        	$course_sp=$course->pro_course_sp??'';
        }

        $description=$course->title;

        $expire_by=strtotime(date("Y-m-d",strtotime("+1 days")));

        $input=[
        	"agent_id"=>$auth->id,
        	"course_id"=>$request->course_id,
        	"npf_id"=>$request->npf_id,
        	"user_id"=>$request->user_id,
        	"mobile"=>$request->mobile,
        	"course_title"=>$course->title,
        	"plan_type"=>$request->plan_type,
        	"course_sp"=>$course_sp,
        	"is_fbt"=>$request->is_fbt_restricted,
        	"address_details"=>json_encode($request->address??[]),
        	"product_id"=>$request->product_id??0,
        	"payment_link"=>'',
        	"status"=>'created',
        	"remark"=>$request->remark,
        ];

        if(!empty($request->coupon_id)){
          $input['coupon_id']=$request->coupon_id;
        }

        if(!empty($request->discount)){
          $input['discount']=$request->discount;
        }

        $hrm_id=DB::table('payment_links')->insertGetId($input);

        $amount=$course_sp;
        if(!empty($request->coupon_id)){
          $amount=round($amount-$request->discount);
        }

        $reference_id="UC-".$hrm_id.'-'.time();
        $post_params = [
		    "amount" => $amount*100,
		    "currency" => "INR",
		    "accept_partial" => false,
		    "expire_by" => $expire_by,
		    "reference_id" => $reference_id,
		    "description" => $description,
		    "customer" => [
		        "name" => "Student",
		        "contact" => "+91" . $request->mobile,
		        "email" => $request->email
		    ],
		    "notify" => [
		        "sms" => true,
		        "email" => true
		    ],
		    "reminder_enable" => true,
		    "notes" => [
		        "Course Name" => $course->title,
		        "course_id" => $course->id,
		        "mobile" => $request->mobile,
		        "plan_type" =>$request->plan_type,
		        "hrm_id" =>$hrm_id,
		        "source" =>'Payment Links Hrm',
		    ],
		    "callback_url" => "https://form.utkarsh.com/uploads/2025/payment_link/index.php",
		    "callback_method" => "get",
		];
		$post_params = json_encode($post_params);

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payment_links");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, "10");
        curl_setopt($ch, CURLOPT_TIMEOUT, "10");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Basic " . base64_encode($this->rz_key . ':' . $this->rz_secret),
            "Content-Type: application/json"
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $response=json_decode($response,true);
        if(!empty($response) && $response['status']=='created'){
        	$payment_link=$response['short_url']??'';
        	$payment_link_id=$response['id']??'';
        	DB::table('payment_links')->where('id',$hrm_id)->update(['payment_link_id'=>$payment_link_id,'payment_link'=>$payment_link]);
        }

        return response(['status'=>true,"message"=>"Payment Link Sent to user. \n\n $payment_link"],200);
	}

	public function npfCallBack(Request $request){
	    $list=DB::table('payment_links')->where('status','created');
        if(!empty($_GET['payment_link_id'])){
	      $list->where('payment_link_id',$_GET['payment_link_id']);
	      $list=$list->limit(1)->get();
	    }else{
	    	// $list=$list->limit(5)->get();
	    	$list=$list->get();
	    }

	    $payment_status="";
		
        foreach ($list as $k=> $val) {
			DB::table('payment_links')->where('id',$val->id)->where('payment_link_id','-')->where('payment_link',' ')->update(['status'=>'expired']);
			
			$url="https://api.razorpay.com/v1/payment_links/$val->payment_link_id";
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	        curl_setopt($ch, CURLOPT_USERPWD,$this->rz_key . ':' . $this->rz_secret);
	        $response = curl_exec($ch);
	        curl_close($ch);
	        $response=json_decode($response);
			

	        if(!empty($response)){
	        	//update status & Add Course
				$payment_link_id=$response->id??'0';
	        	$payment_id=$response->payments[0]->payment_id??'-';
	        	//$payment_id=$payment_link_id;
	        	$payment_status=$response->status??'created';
			
				$ttt=DB::table('payment_links')->where('payment_link_id',$payment_link_id)
		        ->update(['status'=>$payment_status,'payment_id'=>$payment_id]);
				
				$file_name = "laravel/public/zoho-agent/payment_link.txt";
                file_put_contents($file_name,"\n\nRazorpay: ".json_encode($response),FILE_APPEND);

		        if($ttt && $payment_status=="paid"){
		        	$detail=DB::table('payment_links')->where('payment_link_id',$payment_link_id)->first();
                    $params=[
	                  	'userName'=>$detail->mobile,
	                  	'courseId'=>$detail->course_id,
	                  	'paymentId'=>$payment_id,
	                  	'remarks'=>"Hrm Payment Link :".$detail->payment_link_id,
	                  	'amount'=> round(($detail->course_sp-$detail->discount),2),
	                  	'pay_via'=>'HRM_Razorpay_Link',
	                  	//'pay_via'=>'CSC',
	                  	'is_pro'=>$detail->plan_type,
	                  	'coupon_id'=>$detail->coupon_id
	                ];

                    $resp=$this->addCourse($params);
                    $result = json_decode($resp);
					//echo "<pre>"; print_r($result);die;

					$file_name = "laravel/public/zoho-agent/payment_link.txt";
                    file_put_contents($file_name,"\n\n Course Add: ".$resp."\n\n".json_encode($params),FILE_APPEND);

					if(!empty($result->status) && $result->status == 'Ok'){
						if($detail->plan_type==1 && $detail->is_fbt==1){
							$address_array=json_decode($detail->address_details,true);
							$address_array['txn_id'] = $payment_id;
							$address_array['product_id'] = $detail->product_id;

							$file_name = "laravel/public/zoho-agent/payment_link.txt";
                            file_put_contents($file_name,"\n\n Address : ".json_encode($address_array),FILE_APPEND);

							$this->physical_address_save_api($detail->mobile,$detail->course_id,$address_array);
						}

						DB::table('payment_links')->where('id',$detail->id)->update(['course_status'=>1]);
					}
		        }
	        }
	    }

        return response(['status'=>true,'message'=>$payment_status]);
	}

	public function addCourse($params){
		$params['remarks']=urlencode($params['remarks']);
		$url = "https://support.utkarshapp.com/index.php/addBooktoUser?userName={$params['userName']}&courseId={$params['courseId']}&siteId=1&paymentId={$params['paymentId']}&remarks={$params['remarks']}&amount={$params['amount']}&pay_via={$params['pay_via']}&is_pro={$params['is_pro']}&coupon_id={$params['coupon_id']}";
		$file_name = "laravel/public/zoho-agent/payment_link.txt";
       file_put_contents($file_name,"\n\n".$url,FILE_APPEND);
		// echo $url=urlencode($url);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   'Accept-Charset: UTF-8',
		   'X-Auth-Token:'.$this->uc_token,
		   'Accept: application/json',
		   'Content-Type: application/json'
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$data = '';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$resp = curl_exec($curl);
		
		curl_close($curl);
		return $resp;
	}

	public function physical_address_save_api($contact_no,$course_id,$address_array){
		$product_id=$address_array['product_id']??0;
		$address_id = "";

		if(!empty($address_array)){
			$ch = curl_init();
			// Define the URL
			$url = "https://support.utkarshapp.com/index.php/addUpdateAddress?username=$contact_no";
			// Define the headers
			$headers = [
				'X-Auth-Token: '.$this->uc_token,
				'Content-Type: application/x-www-form-urlencoded'
			];
			// Define the data
			$data = http_build_query([
				'address' => json_encode([
					'city'     => $address_array['city']??'',
					'city_id'  => '',
					'country'  => 'INDIA',
					'district' => $address_array['district']??'',
					'houseNo'  => $address_array['houseNo']??'',
					'landMark' => $address_array['landMark']??'',
					'name'     => $address_array['name']??'',
					'phone'    => $contact_no,
					'pincode'  => $address_array['pincode']??'',
					'roadName' => $address_array['roadName']??'',
					'state'    => $address_array['state']??'',
					'state_id' => '',
					'product_id' => $address_array['product_id']??0
				])
			]);

			// Set cURL options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			if($response){
				$response = json_decode($response,true);
				if(!empty($response['status']) && $response['status']){
					$address_id =  $response['data']['id'];
				}
			}
			curl_close($ch);

			$file_name = "laravel/public/zoho-agent/payment_link.txt";
            file_put_contents($file_name,"\n\n Address Add: ".json_encode($response),FILE_APPEND);
		}
		
		if(!empty($address_id)){
			$txn_id = $address_array['txn_id'];
			$url = "https://support.utkarshapp.com/index.php/createNotesOrder?username=$contact_no&courseId=$course_id&txnId=$txn_id&addressId=$address_id&product_id=$product_id";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true); // This sets the request method to POST
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'X-Auth-Token: '.$this->uc_token,
				'Content-Type: application/x-www-form-urlencoded'
			));

			$response = curl_exec($ch);
			curl_close($ch);
			$file_name = "laravel/public/zoho-agent/payment_link.txt";
            file_put_contents($file_name,"\n\n Physical Order Create : ".$response,FILE_APPEND);
			return true;
		}
		
		return false;
	}

	public function apply_coupon(Request $request){
		$data = [
		    "course_id"    => $request->course_id ?? '',
		    "user_id"      => $request->user_id ?? '',
		    "coupon_code"  => $request->coupon_code ?? '',
		    "is_pro"       => $request->plan_type ?? 0,
		];
		
		$url = "https://support.utkarshapp.com/index.php/apply_coupon";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   'Accept-Charset: UTF-8',
		   "X-Auth-Token: $this->uc_token",
		   'Accept: application/json',
		   'Content-Type: application/x-www-form-urlencoded'
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//$data = '';
		$data = http_build_query($data);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);		
		curl_close($curl);
		return $resp;
	}
}
