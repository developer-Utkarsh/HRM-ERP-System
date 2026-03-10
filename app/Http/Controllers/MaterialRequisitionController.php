<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Material;
use Input;
use Validator;
use DB;
use App\Category;
use App\Buyer;
use App\Branch;
use App\AssetRequest;
use App\ApiNotification;
use App\AssetRequestNotification;
use App\Userbranches;

class MaterialRequisitionController extends Controller
{
    
    public function index()
    {
    	return view('material');
    }

    public function materialSendOtp(Request $request){
        //echo '<pre>'; print_r($request->post());die;
        $validator = Validator::make($request->all(), [ 'mobile_no' => 'required|numeric|digits:10' ]);
		if ($validator->fails()){
			$messages = $validator->errors();  
			return response(['status' => false, 'message' => $messages->first('mobile_no')], 200);
		}
		else{
            $check_no_exit = User::where('status','1')->where('is_deleted','0')->where('mobile',$request->mobile_no)->first();
            
            if(!empty($check_no_exit)){
                $mbl = $request->mobile_no;
                //$otp_gen = 1212;
                $otp_gen=substr(str_shuffle("0123456789"), 0, 6);

                $msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
		        $message_content=urlencode($msg);

                $url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
                $ch=curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                curl_close($ch);

                DB::table('users')->where('id', $check_no_exit->id)->update([
                    'login_otp' => $otp_gen
                ]);
                
                return response(['status' => true, 'message' => $otp_gen]);
            }
            else{
                return response(['status' => false, 'message' => 'Mobile No Not Exits'], 200);
            }
        }
    }

    public function materialAccessOtp(Request $request){ 
		$otp = $request->otp;
		$mobile = $request->mobile_no;
		if(!empty($otp)){
			if($request->ajax()){ 
				$check_user = DB::table('users')->where('mobile', $mobile)->first();
				if(!empty($check_user) && $check_user->login_otp==$otp){
					$request->session()->put('material_access_id', $check_user->id);
					return response(['status' => true, 'material_id' => $check_user->id]);
				}
				else{
					return response(['status' => false, 'message' => 'OTP Invalid']);
				}
			}
			else{
				return response(['status' => false, 'message' => 'OTP Invalid']);
			}
		}
		else{
			return response(['status' => false, 'message' => 'OTP Invalid']);
		}
    }

    public function employeeDetails(Request $request, $emp_id=Null, $type=Null){
        $sess_emp_id = $request->session()->get('material_access_id'); 
		if($type == 'app'){
			$sess_emp_id = $emp_id;
		}
        if(!empty($emp_id) && !empty($type) && (($emp_id == $sess_emp_id && $type == 'web') || $type == 'app')){

            $user_details = User::select('users.name','users.role_id','users.mobile','users.register_id','branches.name as branches_name','departments.name as departments_name','userdetails.degination','users.mrl_raise','userbranches.branch_id')
				->leftJoin('userdetails','userdetails.user_id','=','users.id')
				->leftJoin('userbranches','userbranches.user_id','=','users.id')
				->leftJoin('branches','branches.id','=','userbranches.branch_id')
				->leftJoin('departments','departments.id','=','users.department_type')
				->where('users.id', $emp_id)
				->first();
			
			
			$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
			$scategory = Category::where('parent', '!=','0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
         
			//Get All Employee
			$employee = User::where('status',1)->where('is_deleted','0')->whereNotNull('register_id')->get();
			
			//Get All Employee
			$dhemployee = User::where('status',1)->where('is_deleted','0')->where('role_id',21)->whereNotNull('register_id')->orderby('name','ASC')->get();
            return view('material-details', compact('user_details','sess_emp_id', 'type','category','scategory','employee','dhemployee'));
        }
        else{
            return back()->with('error', 'Employee Not Found');
        }
    }

    public function storeMaterialRequisition(Request $request, $emp_id=Null, $type=Null){
        $sess_emp_id = $request->session()->get('material_access_id'); 
		
		$logged_id	=	$emp_id;
		if($type == 'app'){
			$sess_emp_id = $logged_id;
		}
		
        if(!empty($logged_id) && !empty($type) && (($logged_id == $sess_emp_id && $type == 'web') || $type == 'app')){
			$getUser		 =	User::select('users.department_type','users.name','users.register_id','users.role_id','userbranches.branch_id')
								->leftJoin('userbranches','userbranches.user_id','=','users.id')
								->where('users.id', $logged_id)
								->first();
								
			$department_type = 	$getUser->department_type;
			$name       	 = 	$getUser->name;
			$register_id   	 = 	$getUser->register_id ;
			$role       	 = 	$getUser->role_id;
			// $branch_id     	 = 	$getUser->branch_id;
			
			
			/*
			if($role==21 || $role==31){
				$user = User::where('role_id', '=', 25)->first();
				$uID  = $user->id; 
			}else if($logged_id==6828 || $logged_id==6859 || $role==25){
				$user = User::where('role_id', '=', 25)->first();
				$uID  = $user->id;
			}else{
				// $user = User::where('department_type', $department_type)->where('role_id', '=', 21)->where('status', '=', '1')->first();
				
				$user = User::where('id', $logged_id)->where('status', '=', '1')->first();
				if(empty($user)){
				 return back()->with('error', 'You have no department head.');
				}
				$uID  = $user->id; 
			}	
			*/
			
			
			$user = User::where('id', $logged_id)->where('status', '=', '1')->first();
			if(empty($user)){
			 return back()->with('error', 'You have no department head.');
			}
			$uID  = $user->approval_id; 
			
			//Request Add
			// $otp	=	substr(str_shuffle("0123456789"), 0, 6);		
			$otp	=	AssetRequest::select('unique_no')->max('unique_no') + 1;
			
			if(!empty($request->title)){
				$data = array();
				for($i = 0; $i < count($request->title); $i++){				
					$category 		= 	$request->category;
					$scategory		=	$request->scategory;
					$type			=	$request->type;
					$title			=	$request->title;
					$requirement	=	$request->requirement;
					$qty			=	$request->qty;
					$branch_id		=	$request->branch_id;
					$remark			=	$request->remark;
					$request_type	=	$request->request_type;
					$emp_id			=	$request->emp_id;
					$material_category			=	$request->material_category;
					$type_of_business			=	$request->type_of_business;
					$user_id		=	$logged_id;
					$unique_no		=	$otp;
					$request_type 	= 	array_values($request_type);
					$record	=	array(
						// "category"		=>	$category[$i],
						// "scategory"		=>	$scategory[$i],
						// "type"			=>	$type[$i],
						"title"			=>	$title[$i],
						"requirement"	=>	$requirement[$i],
						"branch_id"		=>	$branch_id[$i],
						"qty"			=>	$qty[$i],
						"remark"		=>	$remark[$i],
						"user_id"		=>	$user_id,
						"unique_no"		=>	$unique_no,
						"created_at" 	=>  date('Y-m-d H:i:s'),
						"updated_at"	=> 	date('Y-m-d H:i:s'),
						"request_type"	=>	$request_type[$i],
						"material_category"	=>	$material_category[$i],
						"type_of_business"	=>	$type_of_business[$i],
						"emp_id"		=>	$emp_id[$i],
					);
					
					
					if($files=$request->file('proImg')){		
						if(isset($files[$i])){
							$iname = $files[$i]->getClientOriginalName();
							$iname = uniqid().'-'.$iname;
							$files[$i]->move('laravel/public/quotation',$iname);
							$record['image']= $iname;					
						}
					}
					
					$saveData = AssetRequest::insertGetId($record);
					
					$data[] .= $saveData;
				}
			
				if($data){		
					foreach($data as $x => $asset_id){	
						$this->maintain_history($logged_id, 'asset_request', $asset_id, 'add_request', json_encode($record));
						
						$terms = "1. Taxes: All Taxes Inclusive on the above rate.
2. Description: Goods should be come as per the Specification & Inclusive of All Accessories with Warranty.
3. Delivery Time & Date : In 2 Working days after PO.
4. Validity : Validity of the purchase order is 30 Days.
5. Jurisdiction & Competence : All disputes will be subject to Jodhpur jurisdiction only.
6. Warranty : As per Manufacturer Terms & Condition.
7. Payment : Payment will be settled within 7 Days of Invoice receipt date.";
						

						$nData = array(
							'sender_id' => $logged_id, 
							'request_id' => $asset_id, 
							'receiver_id'=> json_encode(array($uID)), 
							'terms'=> $terms, 
							'message' => $name.'- ('.$register_id.') raised a new requirment');
						AssetRequestNotification::create($nData);
						
						if($role==25 || $role==21){
							//Asset Table
							$newData = array("status" => 1);								
							DB::table('asset_request')->where('id',$asset_id)->update($newData);
							
							//Asset Notification Table
							$newData2 = array("status"	=>	1,"dh_approved"	=>	$logged_id,"updated_at" => date('Y-m-d H:i:s'));								
							DB::table('asset_request_notification')->where('request_id',$asset_id)->update($newData2);
						}	
					}
					
					
					//Requisition Notification 
					$users = DB::table('users')->select('id','gsm_token')->where('id', $uID)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Requisition update!!";
					$load['description'] =	$name.'- ('.$register_id.') raised a new requirment';
					$load['body'] 		 =	$name.'- ('.$register_id.') raised a new requirment';
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	NULL;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
			 
					$token = [];
					if(count($users) > 0){
						foreach ($users as $key => $value) {
							if(!empty($value->gsm_token)){
								$token[] = $value->gsm_token;
							}
						}
					}

					$this->android_notification($token,$load);
					//End
					
					
					return back()->with('success', 'Asset Request Added Successfully');
				} else {
					return  back()->with('error', 'Something Went Wrong !');
				}
			}
			// }else{
				// return  back()->with('error', 'You are not eligible to raise any requistion.');
			// }
        }
        else{
            return back()->with('error', 'Employee Not Found');
        }
    }
	
	/* Old Data
	public function storeMaterialRequisition(Request $request, $emp_id=Null, $type=Null){
        $sess_emp_id = $request->session()->get('material_access_id'); 
		if($type == 'app'){
			$sess_emp_id = $emp_id;
		}
        if(!empty($emp_id) && !empty($type) && (($emp_id == $sess_emp_id && $type == 'web') || $type == 'app')){
            $validatedData = $request->validate([
                'item_description' => 'required',
                'unit' => 'required',
            ]);

            $inputs = $request->only('item_description','unit','remark');
            $inputs['date'] = date('Y-m-d');
            $inputs['user_id'] = $emp_id;


            $material = Material::create($inputs);    

            if ($material->save()) {
                return back()->with('success', 'You have successfully done your form!');
            } else {
                return  back()->with('error', 'Something Went Wrong !');
            }
        }
        else{
            return back()->with('error', 'Employee Not Found');
        }
    }
	*/
	
	
	//New
	public function employee_requisition(Request $request, $emp_id=Null, $type=Null){
        $sess_emp_id = $request->session()->get('material_access_id'); 
		if($type == 'app'){
			$sess_emp_id = $emp_id;
		}
		
		$mrl_number = Input::get('mrl_number');
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
        if(!empty($emp_id) && !empty($type) && (($emp_id == $sess_emp_id && $type == 'web') || $type == 'app')){

            $getRequest = AssetRequest::select('asset_request.id','A.name','B.name as sub_name','asset_request.user_id','asset_request.qty','asset_request.type','asset_request.title','asset_request.requirement','arn.reason','arn.purchase_status','asset_request.status','asset_request.unique_no','asset_request.product_status','arn.it_status','asset_request.created_at','asset_request.image','asset_request.remark','products.name as pname','us.name as emp_name','dh.name as dh_name','material_category.name as material_category','arn.purchase_reason','asset_request.request_type','asset_request.inventory_status','branches.name as branch','branches.branch_location','branches.nickname','branches.short_name','asset_request.emp_grn','asset_request.inventory_grn')
			->leftJoin('asset_request_notification as arn','arn.request_id', '=', 'asset_request.id')
			->leftJoin('category AS A','A.id', '=', 'asset_request.category')
			->leftJoin('category AS B','B.id', '=', 'asset_request.scategory')
			->leftJoin('products','products.id', '=', 'asset_request.product_id')
			->leftJoin('users as us','us.id', '=', 'asset_request.emp_id')
			->leftJoin('users as dh','dh.id', '=', 'asset_request.remark')
			->leftJoin('material_category','material_category.id','asset_request.material_category')
			->leftjoin('branches','branches.id','asset_request.branch_id')
			->where('asset_request.user_id', $emp_id)
			->where('asset_request.is_deleted', '=', '0')
			->orderby('asset_request.id','desc');
			
			if(!empty($mrl_number)){
				$getRequest->where('asset_request.unique_no',$mrl_number);
			}
			
			// $getRequest = $getRequest->get();
			$getRequest = $getRequest->paginate(20);
			$pageNumber = 1;
			if(isset($page)){
				$page = Input::get('page');
				$pageNumber = (20*($page-1));
				
				$pageNumber = $pageNumber +1;
			}
			
            return view('employee-requisition', compact('getRequest','sess_emp_id','type','pageNumber','params'));
        }
        else{
            return back()->with('error', 'Employee Not Found');
        }
    }
	
	
	
	public function getSubCat(Request $request){
		$subCatData = DB::table('category')->where('parent', $request->cat_id)->get();
		
		if (!empty($subCatData))
        {
            echo $res = "<option value=''> Select Sub Category </option>";
            foreach ($subCatData as $key => $value)
            {
                if (!empty($value->id))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Sub Category Not Found </option>";
            die();
        }
	}
	
	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('all_history')->insert($history_data);
	}
	
	
	public function web_view_product_accept(Request $request){	
		$request_id 	=	$request->request_id;
		
		
		if(!empty($request_id)){
			$maxValue 	= DB::select("SELECT GREATEST(MAX(emp_grn), MAX(inventory_grn)) AS max_value FROM asset_request;");					
			$grn_no 	= $maxValue[0]->max_value + 1;
				
			$grnData  = array(
				'product_status' 	=> 1,
				'emp_grn' 			=> $grn_no,
				'emp_grn_date' 		=> date('Y-m-d H:i:s')
			);
			DB::table('asset_request')->where('id', $request_id)->update($grnData);
			
			
			//Product Transfer			
			$assetRequest = AssetRequest::select('asset_request.product_id','asset_request.request_type','asset_request.user_id','asset_request.branch_id','asset_request.qty','arn.it_status')->leftJoin('asset_request_notification as arn','arn.request_id','asset_request.id')->where('asset_request.id', $request_id)->first();
			
			if($assetRequest->request_type=='0'){
				//Branch Location Get
				$getBranchlocation = DB::table('branches')->where('id',$assetRequest->branch_id)->first();
				
				if(!in_array($assetRequest->branch_id,[90,91,94,95])){
					$data = array(
						"transfer_from"	=>	'0',
						"location"	    =>	$getBranchlocation->branch_location,
						"product_id"	=>	$assetRequest->product_id,
						"user_id"		=>	$assetRequest->user_id,
						"transfer_to"	=>	$assetRequest->branch_id,
						"qty"			=>	$assetRequest->qty,
						"request_id"	=>	$request_id,
						"status"		=>	'Accept',
					);
					DB::table('transfer')->insert($data);
				}
				
				//Inventory create_function
				if($assetRequest->it_status==2){
					$data = array(
						"product_id"	=>	$assetRequest->product_id,
						"qty"			=>	$assetRequest->qty,
						"location"	    =>	$getBranchlocation->branch_location,
						"request_id"	=>	$request_id,
						"product_date"	=>	date('Y-m-d'),				
					);
					DB::table('inventory')->insert($data);
				}
			}
			
			return response(['status' => true, 'message' => 'Product Accept By Requisitor'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
	}
	
	
	public function po_approval_list(Request $request, $emp_id=Null, $type=Null){
        $sess_emp_id = $request->session()->get('material_access_id'); 
		if($type == 'app'){
			$sess_emp_id = $emp_id;
		}
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
        if(!empty($emp_id) && !empty($type) && (($emp_id == $sess_emp_id && $type == 'web') || $type == 'app')){
			$po_number = $request->po_number;						
			$getRequest =  AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','asset_request.request_type','departments.name as dname','branches.name as bname','buyer.name')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')							
							->join('users','users.id', '=', 'asset_request_notification.sender_id')
							->join('departments','departments.id', '=', 'users.department_type')
							->join('userbranches','users.id', '=', 'userbranches.user_id')
							->join('branches','branches.id', '=', 'userbranches.branch_id')
							->leftJoin('buyer','buyer.id', '=', 'asset_request_notification.company')
							->where('asset_request_notification.is_deleted', '=', '0')
							->where('asset_request.is_deleted', '=', '0')
							->where('asset_request_notification.status', '=', '1')
							->whereRaw("(asset_request_notification.purchase_status = '3' OR asset_request_notification.purchase_status = '2' )")
							->whereRaw("(asset_request_notification.company != 'na' AND asset_request_notification.company != '-' )")
							->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc')
							->orderby('asset_request_notification.id', 'desc')
							->groupby('asset_request.unique_no');
			
			
			if(!empty($po_number)){
				$getRequest = $getRequest->where('asset_request_notification.po_no',$po_number);
			}
			
			// $getRequest = $getRequest->get();
			$getRequest = $getRequest->paginate(20);
			$pageNumber = 1;
			if(isset($page)){
				$page = Input::get('page');
				$pageNumber = (20*($page-1));
				
				$pageNumber = $pageNumber +1;
			}
			
            return view('po-approval', compact('sess_emp_id','type','getRequest','pageNumber','params'));
        }
        else{
            return back()->with('error', 'Employee Not Found');
        }
    }
	
	public function web_view_po(Request $request){
		$id 	=	$request->id;
		$value 	=	$request->value;
		$rreason 	=	$request->rreason;
		$approve 	=	$request->approve;
		dd($request->all());
		
		if($id!="" && $value!=""){
			$data = array(
				"dm_status"		=>	$value,
				"po_approve"	=>	$approve,
				"reason"		=>	$rreason,
				"dm_updated"	=>	date('Y-m-d H:i:s'),
			);
			
			DB::table('asset_request_notification')->where('request_id', $id)->update($data);
			
			return response(['status' => true, 'message' => 'Status updated successfully.'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!'], 200);
		}
	}
	public function po_wo_status_update(Request $request){
		$id      = $request->id;
		$status   = $request->value;
		$approve = $request->approve;
	
		// Uncomment this if you want to debug incoming data
		// dd($request->all());
	
		if ($id != "" && $status != "") {
			$data = array(
				"vendor_approval"     => $status,
			);
	
			DB::table('asset_request_notification')->where('id', $id)->update($data);
	
			return response(['status' => true, 'message' => 'Status updated successfully.'], 200);
		} else {
			return response(['status' => false, 'message' => 'Required field(s) missing!'], 200);
		}
	}
	
	
	
	public function web_view_poprint(Request $request,$id,$emp_id){
		// print_r($id);
		
		$record = 	AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','asset_request.request_type','asset_request.remark','users.name as dhname','asset_request_notification.request_id')
					->leftJoin('asset_request','asset_request.id','asset_request_notification.request_id')
					->leftJoin('users','users.id','asset_request.remark')
					->leftJoin('material_category','material_category.id','asset_request.material_category')
					->where('asset_request_notification.id', $id)->first();
		
		// print_r($record->company);
		$buyer 	=	Buyer::where('id',$record->company)->first();
		$branch =	Branch::where('id',$record->location)->first();
		$pName 		=	User::where('id',$record->pt_approve)->first();
		$hodName 	=	User::where('id',$record->dh_approved)->first();
		$cfoName 	=	User::where('id',$record->po_approve)->first();
		
		$buyer_account 	=	DB::table('buyer_bank_details')->where('buyer_id',$record->company)->first();
		return view('po', compact('record','buyer','branch','pName','hodName','cfoName','emp_id','buyer_account'));
	}
	
	
	public function web_view_po_status_update(Request $request){
		$id 		=	$request->id;
		$value 		=	$request->value;
		$rreason 	=	$request->rreason;
		$auth_id 	=	$request->auth_id;
		
		if($id!="" && $value!=""){
			$data = array(
				"dm_status"		=>	$value,
				"po_approve"	=>	$auth_id,
				"reason"		=>	$rreason,
				"dm_updated"	=>	date('Y-m-d H:i:s'),
			);
						
			DB::table('asset_request_notification')->where('request_id', $id)->update($data);
			
			return response(['status' => true, 'message' => 'PO status updated successfully.'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!'], 200);
		}
	}
	
	
	public function web_view_product_return(Request $request){	
		$request_id 	=	$request->request_id;
		$product_id 	=	$request->product_id;
				
		if(!empty($request_id) && !empty($product_id)){				
			$grnData  = array(
				'inventory_status' 	=> 	2
			);
			DB::table('asset_request')->where('id', $request_id)->update($grnData);
					
			//Transfer 
			$rdata  = array('status'=> 'Return');			
			DB::table('transfer')->where('request_id', $request_id)->where('product_id', $product_id)->update($rdata);
			
			
			return response(['status' => true, 'message' => 'Product Return Request send successfully'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
	}



	public function po_created_history(Request $request, $buyer_id){
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
        if(!empty($buyer_id)){
			$po_number = $request->po_number;
			$getRequest =  AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','asset_request.request_type')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
							->where('asset_request_notification.company', $buyer_id)
							->orderby('asset_request_notification.pdate','DESC');
			
			
			if(!empty($po_number)){
				$getRequest = $getRequest->where('asset_request_notification.po_no',$po_number);
			}
			
			// $getRequest = $getRequest->get();
			$getRequest = $getRequest->paginate(20);
			$pageNumber = 1;
			if(isset($page)){
				$page = Input::get('page');
				$pageNumber = (20*($page-1));
				
				$pageNumber = $pageNumber +1;
			}
			
            return view('po-created-history', compact('getRequest','pageNumber','params','buyer_id'));
        }
        else{
            return back()->with('error', 'Vendor Not Found');
        }
    }
}
