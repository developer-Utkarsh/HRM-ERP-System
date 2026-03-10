<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\PoWoStatusMail;
use Illuminate\Http\Request;
use App\Asset;
use App\AssignAsset;
use App\User;
use Excel;
use App\AssetRequest;
use App\ApiNotification;
use App\AssetRequestNotification;
use Input;
use Validator;
use DataTables;
use DB;
use Auth;
use App\Category;
use App\Buyer;
use App\Branch;
use Image;
use App\Inventory;
use App\Exports\PoReportExport;
use App\Exports\PoPaymentExport;
use App\Exports\RequestPendingAcceptExport;

class AssetRequestController extends Controller
{
	public function index(){		
		$logged_id  = Auth::user()->id;	
		$role_id    = Auth::user()->role_id;	

		$title = Input::get('title');		
	
		$getRequest = AssetRequest::select('asset_request.id','A.name','B.name as sub_name','asset_request.user_id','asset_request.qty','asset_request.type','asset_request.title','asset_request.requirement','asset_request.created_at','asset_request_notification.reason','asset_request_notification.purchase_status','asset_request.status','asset_request.unique_no','asset_request.product_status','asset_request_notification.it_status','asset_request.image','asset_request.remark','us.name as emp_name','dh.name as dh_name','material_category.name as material_category','asset_request_notification.purchase_reason','asset_request.request_type','asset_request.inventory_status','branches.name as branch','branches.branch_location','branches.nickname','branches.short_name','asset_request.emp_grn','asset_request.inventory_grn','asset_request.type_of_business')
		->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
		->leftJoin('category AS A','A.id', '=', 'asset_request.category')
		->leftJoin('category AS B','B.id', '=', 'asset_request.scategory')
		->leftJoin('users as us','us.id', '=', 'asset_request.emp_id')
		->leftJoin('users as dh','dh.id', '=', 'asset_request.remark')
		->leftJoin('material_category','material_category.id','asset_request.material_category')
		->leftjoin('branches','branches.id','asset_request.branch_id')
		->where('asset_request.user_id', $logged_id)
		->where('asset_request.is_deleted', '=', '0');
		
		if(!empty($title)){
			$getRequest->where('asset_request.unique_no', '=', $title);
		}
		
		$getRequest = $getRequest->orderby('asset_request.id','desc')
		->get();
		
		//print_r($getRequest);	die();
		
		$users = DB::table('users')->select('id','name')->where('status', '=', 1)->where('is_deleted', '=', '0')->get();
		return view('admin.request.index', compact('getRequest','users')); 
	}
	
    public function addRequestAsset(){		
		$logged_id      = 	Auth::user()->id;
		$category 		= 	Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		$scategory 		= 	Category::where('parent', '!=','0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		
		$user 			= 	User::with('user_details')->where('id', $logged_id)->first();
		$degination		= 	$user->user_details->degination;		
		
		$user_branch	= 	Auth::user()->user_branches[0]->branch_id;
		
		//Get All Employee
		$employee = User::where('status',1)->where('is_deleted','0')->whereNotNull('register_id')->orderby('name','ASC')->get();
		
		//Get All Employee
		$dhemployee = User::where('status',1)->where('is_deleted','0')->where('role_id',21)->whereNotNull('register_id')->orderby('name','ASC')->get();
		return view('admin.request.add_request',compact('category','scategory','degination','user_branch','employee','dhemployee'));		
	}
	
	public function store(Request $request){
		$logged_id       = Auth::user()->id;
		$department_type = Auth::user()->department_type;
		$name       	 = Auth::user()->name;
		$register_id   	 = Auth::user()->register_id ;
		$role       	 = Auth::user()->role_id;
				
		/*
		if($role==21 || $role==31){
			//Department Header and Purchase Team			
			$user = User::where('role_id', '=', 25)->first();
			$uID  = $user->id;  //Get Inventory ID
		}else if($logged_id==8799 || $logged_id==6859 || $role==25){
			//$uID  = 8799;  //P.O Account	//Inventory	
			$user = User::where('role_id', '=', 25)->first();
			$uID  = $user->id;
		}else{
			// $user = User::where('department_type', $department_type)->where('role_id', '=', 21)->where('status', '=', '1')->first();
			$user = User::where('id', $logged_id)->where('status', '=', '1')->first();
			$uID  = $user->approval_id;  //Get Department Head ID
		}	
		*/
		
		$user = User::where('id', $logged_id)->where('status', '=', '1')->first();
		$uID  = $user->approval_id;
		
		//Request Add
		// $otp	=	substr(str_shuffle("0123456789"), 0, 6);
		// $otp	=	AssetRequest::select('unique_no')->max('unique_no');
		$otp	=	AssetRequest::select('unique_no')->max('unique_no') + 1;
			
		if(!empty($request->title)){
			$data = array();
			for($i = 0; $i < count($request->title); $i++){				
				$category 		= 	$request->category;
				$scategory		=	$request->scategory;
				$product		=	$request->product;
				$type			=	$request->type;
				$title			=	$request->title;
				$requirement	=	$request->requirement;
				$qty			=	$request->qty;
				$branch_id		=	$request->branch_id;
				$remark			=	$request->remark;
				$emp_id			=	$request->emp_id;
				$material_category			=	$request->material_category;
				$type_of_business			=	$request->type_of_business;
				$user_id		=	$logged_id;
				$unique_no		=	$otp;
				
				$request_type	= array_values($request->request_type);
				
				
				$record = array();
				if(isset($request->category[$i])){
				 $record['category']=$request->category[$i];
				}
				
				if(isset($request->scategory[$i])){
				 $record['scategory']=$request->scategory[$i];
				}
				
				if(isset($request->product[$i])){
				 $record['product_id']=$request->product[$i];
				}
				
				if(isset($request->branch_id[$i])){
					$record['branch_id'] = $request->branch_id[$i];
				}
				
				if(isset($request->type[$i])){
				 $record['type']=$request->type[$i];
				}
				
				if(isset($request->title[$i])){
				$record['title']=$request->title[$i];
				}
				if(isset($request->requirement[$i])){
				$record['requirement']=$request->requirement[$i];
				}
				if(isset($request->qty[$i])){
				$record['qty']=$request->qty[$i];
				}
				
				if(isset($request->remark[$i])){
				$record['remark']=$request->remark[$i];
				}
				
				if(isset($request_type[$i])){
				$record['request_type']=$request_type[$i];
				}
				 
				if(isset($emp_id[$i])){
					$record['emp_id']=$emp_id[$i];
				}
				
				if(isset($material_category[$i])){
				$record['material_category']=$material_category[$i];
				}
				
				if(isset($type_of_business[$i])){
					$record['type_of_business']=$type_of_business[$i];
				}
				
				
				if($files=$request->file('proImg')){	
					if(isset($files[$i])){
						$iname = $files[$i]->getClientOriginalName();
						$iname = uniqid().'-'.$iname;
						$files[$i]->move('laravel/public/quotation',$iname);
						$record['image']= $iname;					
					}
				}
				
				$record['user_id']=$user_id;
				$record['unique_no']=$unique_no;
				$record['created_at']= date('Y-m-d H:i:s');
				$record['updated_at']= date('Y-m-d H:i:s');
				
				if(count($record) > 0){
					$saveData = AssetRequest::insertGetId($record);
				}
				
				$data[] .= $saveData;
			}	
			
			
				
			//History
			//$this->maintain_history($logged_id, 'asset_request', $data->id, 'add_request', json_encode($record));
			
			//Asset Request Notification
			if($data){	
					foreach($data as $x => $asset_id){	
						$this->maintain_history($logged_id, 'asset_request', $asset_id, 'add_request', json_encode($record));
						
					$terms = "1. Taxes: All Taxes Inclusive on the above rate.
2. Description: Goods should be come as per the Specification & Inclusive of All Accessories with Warranty.
3. Delivery Time & Date : In 2 Working days after PO.
4. Validity : Validity of the purchase order is 30 Days.
5. Jurisdiction & Competence : All disputes will be subject to Jodhpur jurisdiction only.
6. Warranty : As per Manufacturer Terms & Condition.
7. Payment : Payment will be settled within 30 Days of Invoice receipt date.";
						

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
							DB::table('asset_request_notification')->where('request_id', $asset_id)->update($newData2);
						}	
					}

				//Requisition Notification 
				$users = DB::table('users')->select('id','gsm_token','device_type')->where('id', $uID)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
				$load = array();
				$load['title'] 		 =	"Requisition update!!";
				$load['description'] =	$name.'- ('.$register_id.') raised a new requirment';
				$load['body'] 		 =	$name.'- ('.$register_id.') raised a new requirment';
				$load['image'] 		 =	asset('laravel/public/images/test-image.png');
				$load['date'] 		 =	NULL;
				$load['status'] 	 =	NULL;
				$load['type'] 		 =	'general';
		 
				$this->notificationDeviceWise($users, $load);
				
				return redirect()->route('admin.request.index')->with('success', 'Asset Request Added Successfully');
			} else {
				return redirect()->route('admin.request.add-request')->with('error', 'Something Went Wrong !');
			} 
		
		}
	}
	
	public function destroy($id){
		$logged_id  = Auth::user()->id;
		$request 	= AssetRequest::where('status', 0)->where('id',$id)->first();
       
        $inputs = array('is_deleted' => '1', 'delete_id' => $logged_id);
		
        if (!empty($request)) {		
			$request->update($inputs);		
            return redirect()->back()->with('success', 'Asset Request Deleted Successfully');
        } else {
            return redirect()->route('admin.request.index')->with('error', 'You can not delete now ');
        }
	}
	
	public function edit($id){
		$getRecord = AssetRequest::where('is_deleted', '=', '0')->find($id);	
		return view('admin.request.edit_request', compact('getRecord'));
	}
	
	public function update(Request $request, $id){
		$logged_id = Auth::user()->id;
		$data = AssetRequest::find($id);
		$inputs = $request->only('title','requirement','qty');
		$inputs['user_id'] = $logged_id;
		$inputs['updated_at']= date('Y-m-d H:i:s');
		
		$this->maintain_history($logged_id, 'asset_request', $data->id, 'edit_request', json_encode($inputs));
		if($data->update($inputs)) {			
		
            return redirect()->route('admin.request.index')->with('success', 'Asset Request Updated Successfully');
        } else {
            return redirect()->route('admin.request.edit_request')->with('error', 'Something Went Wrong !');
        }
	}
	
	//Requisition Request list	
	public function requisitionList(){
		$logged_id  		= Auth::user()->id;		
		$role_id 			= Auth::user()->role_id;
		$department_type 	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$user = User::with('user_details')->where('id', $logged_id)->first();
		$degination	= $user->user_details->degination;		
		
		
		
		$user_branch	= Auth::user()->user_branches[0]->branch_id;	
		$location 		= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$rnumber 		= Input::get('rnumber');
		$pwnumber 		= Input::get('pwnumber');
		$uname 			= Input::get('uname');
		$fdate 			= Input::get('fdate');
        $tdate 			= Input::get('tdate');
        $branch_id  	= Input::get('branch_id');
        $po_status  	= Input::get('po_status');
        $department_id  = Input::get('department_id');
		$pname 			= Input::get('pname');
	
		
		$notification	=	AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','departments.name as dname','branches.name as bname','asset_request.user_id')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
							->join('users','users.id', '=', 'asset_request_notification.sender_id')
							->join('departments','departments.id', '=', 'users.department_type')
							->join('branches','branches.id', '=', 'asset_request.branch_id');
		
		if($degination=="CENTER HEAD"){		
			$notification = $notification->where('asset_request.branch_id', '=', $user_branch)
							->where('asset_request.unique_no', 'LIKE', '%' . $rnumber . '%')
							->orderby('asset_request_notification.status', 'ASC');
		}else if($role_id==31){			
			$notification = $notification->where('branches.branch_location', '=', $location)
							->where('asset_request_notification.status', '=', 1)
							->where('asset_request_notification.it_status', '=', 2)
							->where('asset_request_notification.purchase_status', '!=', 7)
							->whereRaw("(asset_request_notification.company != 'na' OR asset_request_notification.company != '-')")
							->orderby('asset_request_notification.purchase_status', 'ASC');
							
			// $notification = $notification->where('branches.branch_location', '=', $location)
				// ->where('asset_request_notification.status', '=', 1)
				// ->where('asset_request_notification.it_status', '=', 2)				
				// ->whereRaw("(asset_request_notification.company != 'na' OR asset_request_notification.company != '-')")
				// ->orderBy('asset_request_notification.purchase_status', 'ASC');
				
			// if(empty($uname)){
				// $notification = $notification->whereIn('asset_request_notification.purchase_status', [0, 1, 3]);
			// }

			
		}else if($role_id==25){			
			$notification = $notification->where('branches.branch_location', '=', $location)
							->where('asset_request_notification.status', '=', '1')	
							->orderby('asset_request_notification.it_status', 'ASC');
							
			// if(empty($uname)){
				// $notification = $notification->where('asset_request_notification.it_status', 0);
			// }
		}else if($role_id==33){			
			$notification = $notification->where('branches.branch_location', '=', $location)
							->where('asset_request_notification.status', '=', '1')
							->where('asset_request_notification.it_status', '=', 4)
							->orderby('asset_request_notification.it_status', 'ASC');
		}else if($degination=="MANAGER-PURCHASE & STORE"){	
			$notification =	$notification->where('asset_request_notification.status', '=', '1')
							->whereRaw("
								(
									(asset_request_notification.purchase_status = '3' 
										 OR asset_request_notification.purchase_status = '2')
									AND (asset_request_notification.company != 'na' 
									AND asset_request_notification.company != '-')									
								)
							")
							->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc');
		}else if($logged_id==8799 || $logged_id==6859){
			$notification =	$notification->where('asset_request_notification.status', '=', '1')
							->whereRaw("
								(
									asset_request_notification.it_status = 5 AND asset_request.type = 'Asset'
									OR 
									(
										(asset_request_notification.purchase_status = '3' 
										 OR asset_request_notification.purchase_status = '2')
										AND (asset_request_notification.company != 'na' 
											 AND asset_request_notification.company != '-')
										AND asset_request_notification.pm_status = 1
									)
								)
							")
							->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc');
		}else if($role_id==29){			
			$notification = $notification->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc');
		}else{
			$notification = $notification->where('asset_request_notification.receiver_id', 'LIKE', '%' . $logged_id . '%')
							//->where('users.department_type' , $department_type)
							->orderby('asset_request_notification.status', 'ASC');							
		}
		
		$notification	=	$notification->where('asset_request.is_deleted', '=', '0')
							->orderby('asset_request_notification.id', 'desc')
							->groupby('asset_request.unique_no');
		
		if(!empty($rnumber)){
			$notification->whereRaw("(asset_request.unique_no = '".$rnumber."')");
		}
		
		if (!empty($pwnumber)) {
			$notification->whereRaw("CONCAT(asset_request_notification.po_no, '/', asset_request_notification.po_month) = ?", [$pwnumber]);
		}
		
		if(!empty($pname)){
			$notification->whereRaw("(asset_request.title LIKE '%".$pname."%')");	
		}
		
		if(!empty($uname)){
			$notification->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($department_id)){
			$notification->where('departments.id', '=', $department_id);
		}
		
		
		if(!empty($branch_id)){
			$notification->where('asset_request.branch_id', '=', $branch_id);
		}
		
		if(!empty($po_status)){
			$notification->where('asset_request_notification.dm_status', '=', $po_status);
		}
		
		
		if (!empty($fdate) && !empty($tdate)) {			
            $notification->where('asset_request_notification.created_at', '>=', $fdate)->where('asset_request_notification.created_at', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $notification->where('asset_request_notification.created_at', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $notification->where('asset_request_notification.created_at', 'LIKE', '%' . $tdate . '%');
        }
		
				
		$notification = $notification->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		
		// print_r($notification);die();
		
		return view('admin.request.requisition', compact('notification','degination','pageNumber','params'));
	}
	
	
	public function requestApprovalList(){
		$logged_id  		= Auth::user()->id;		
		$role_id 			= Auth::user()->role_id;
		$department_type 	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$user = User::with('user_details')->where('id', $logged_id)->first();
		$degination	= $user->user_details->degination;		
		
		
		
		$user_branch	= Auth::user()->user_branches[0]->branch_id;	
		$location 		= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$rnumber 		= Input::get('rnumber');
		$pwnumber 		= Input::get('pwnumber');
		$uname 			= Input::get('uname');
		$fdate 			= Input::get('fdate');
        $tdate 			= Input::get('tdate');
        $branch_id  	= Input::get('branch_id');
        $po_status  	= Input::get('po_status');
        $department_id  = Input::get('department_id');
		$pname 			= Input::get('pname');
	
		
		$notification	=	AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','departments.name as dname','branches.name as bname','asset_request.user_id')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
							->join('users','users.id', '=', 'asset_request_notification.sender_id')
							->join('departments','departments.id', '=', 'users.department_type')
							->join('branches','branches.id', '=', 'asset_request.branch_id');
		
				
		$notification = $notification->where('branches.branch_location', '=', $location)
							->where('asset_request_notification.status', '=', '1')	
							->where('asset_request_notification.it_status', 5)	
							->orderby('asset_request_notification.it_status', 'ASC');
							
			
		
		$notification	=	$notification->where('asset_request.is_deleted', '=', '0')
							->orderby('asset_request_notification.id', 'desc')
							->groupby('asset_request.unique_no');
		
		if(!empty($rnumber)){
			$notification->whereRaw("(asset_request.unique_no = '".$rnumber."')");
		}
		
		if (!empty($pwnumber)) {
			$notification->whereRaw("CONCAT(asset_request_notification.po_no, '/', asset_request_notification.po_month) = ?", [$pwnumber]);
		}
		
		if(!empty($pname)){
			$notification->whereRaw("(asset_request.title LIKE '%".$pname."%')");	
		}
		
		if(!empty($uname)){
			$notification->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($department_id)){
			$notification->where('departments.id', '=', $department_id);
		}
		
		
		if(!empty($branch_id)){
			$notification->where('asset_request.branch_id', '=', $branch_id);
		}
		
		if(!empty($po_status)){
			$notification->where('asset_request_notification.dm_status', '=', $po_status);
		}
		
		
		if (!empty($fdate) && !empty($tdate)) {			
            $notification->where('asset_request_notification.created_at', '>=', $fdate)->where('asset_request_notification.created_at', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $notification->where('asset_request_notification.created_at', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $notification->where('asset_request_notification.created_at', 'LIKE', '%' . $tdate . '%');
        }
		
				
		$notification = $notification->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		
		// print_r($notification);die();
		
		return view('admin.request.request_approval', compact('notification','degination','pageNumber','params'));
	}
	
	public function editRequisition($id, $type=NULL){
		
		$approval = DB::table('users')->where('approval_id',Auth::user()->id)->get();
		
		$logged_id  = Auth::user()->id;		
		$role_id 	= Auth::user()->role_id;
		$degination = Auth::user()->user_details->degination;	
		
		$notification = AssetRequestNotification::select('asset_request.*','asset_request.id as arid','asset_request_notification.*')
							->leftJoin('users','users.id', 'asset_request_notification.sender_id')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
							->where('asset_request.unique_no' , $id)
							->where('asset_request.is_deleted' , 0)
							->where('asset_request_notification.is_deleted', '=', '0')
							->orderby('asset_request_notification.id', 'desc');
							
		if($role_id==25){
			$notification->where('asset_request_notification.status','1');
		}else if($role_id==33){
			$notification->where('asset_request_notification.it_status','4');
		}else if($role_id==34){
			$notification->where('asset_request_notification.purchase_status','7');
		}else if($role_id==31){
			$notification->where('asset_request_notification.it_status','2');
		}else if($degination=='MANAGER-PURCHASE & STORE'){
			//$notification->where('asset_request_notification.it_status','2')->where('asset_request_notification.company','!=','na');
			$notification->whereRaw("
				(
					(asset_request_notification.purchase_status = '3' 
					 OR asset_request_notification.purchase_status = '2')
					AND (asset_request_notification.company != 'na' 
						 AND asset_request_notification.company != '-')
				)
			");
		}else if($logged_id==8799 || $logged_id==6859){
			//$notification->where('asset_request_notification.it_status','2')->where('asset_request_notification.company','!=','na');
			
			$notification->whereRaw("
				(
					asset_request_notification.it_status = 5 AND asset_request.type = 'Asset'
					OR 
					(
						(asset_request_notification.purchase_status = '3' 
						 OR asset_request_notification.purchase_status = '2')
						AND (asset_request_notification.company != 'na' 
							 AND asset_request_notification.company != '-')
						AND asset_request_notification.pm_status = 1
					)
				)
			");
		}else if($role_id==21 || !empty($approval) || $degination=='CENTER HEAD'){
			$notification->where('asset_request_notification.status','0');
		}else{
			die('Go Back');
		}
		$notification = $notification->get();
		
		
		
		// $record    = 	AssetRequest::where('unique_no', $id)->get();		
		$buyer	   =	Buyer::where('is_deleted','0')->where('status','Active')->orderby('name')->get();
		$branch	   =	Branch::where('is_deleted','0')->orderby('name')->get();
		
		$category 	= array(); //Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		$scategory 	= array(); //Category::where('parent', '!=','0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		
		
			
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		if($location=='prayagraj'){
			$poAddress = "H.NO. 35/11/5, HASTUNGS ROAD, SADAR PRAYAGRAJ, </br> Allahabad, Uttar Pradesh, 211001";
			$poGst	   = "09AAFCE2658C1ZR";
			$polocation	 = "PRG";
		}else if($location=='delhi'){
			$poAddress = "FIRST FLOOR, 1761, OUTRAM LINES,KINGSWAY CAMP, </br>GT.B. NAGAR, DEHLI, North West Delhi, </br>Delhi, 110009";
			$poGst	   = "07AAFCE2658C1ZV";
			$polocation	 = "DL";
		}else if($location=='jaipur'){
			$poAddress = "Utkarsh Tower</br> Utkarsh Classes & Edutech Pvt Ltd </br>Gopalpura bypass road, Near mahesh nagar Police station </br>Jaipur 302017";
			$poGst	   = "08AAFCE2658 C1ZT";
			$polocation	 = "JPR";
		}else if($location=='indore'){
			$poAddress = "Harshdeep Bhawan, siddharth nagar, Bhawarkua, chauraha, AB Rd, Indore, Madhya Pradesh 452001 </br> Landmark : Near Gurjar Hospital & Endoscopy Centre Pvt Ltd,";
			$poGst	   = "23AAFCE2658C1Z1";
			$polocation	 = "IND";
		}else if($location=='lucknow'){
			$poAddress = "H.NO. 35/11/5, HASTUNGS ROAD, SADAR PRAYAGRAJ, </br> Allahabad, Uttar Pradesh, 211001";
			$poGst	   = "09AAFCE2658C1ZR";
			$polocation	 = "LKW";
		}else{
			$poAddress = "832, UTKARSH BHAWAN, NEAR MANDAP RESTAURANT </br> 9TH CHOPASANI ROAD, JODHPUR RAJASTHAN - 342003 </br> CIN: U72900RJ2018PTC063026";
			$poGst	   = "08AAFCE2658 C1ZT";
			$polocation	 = "JDR";
		}
			
		
		$product = array(); //DB::table('products')->where('status','Active')->get();
		
		//Get All Employee
		// $employee = User::where('status',1)->where('is_deleted','0')->whereNotNull('register_id')->orderby('name','ASC')->get();
		$employee = User::where('is_deleted','0')->whereNotNull('register_id')->orderby('name','ASC')->get();
		
		
		//Get All Employee
		$dhemployee = User::where('status',1)->where('is_deleted','0')->where('role_id',21)->whereNotNull('register_id')->orderby('name','ASC')->get();
		
		
		return view('admin.request.edit_requisition', compact('notification','id','type','buyer','branch','category','scategory','degination','poAddress','poGst','product','polocation','employee','dhemployee'));
	}
	
	public function updateRequisition(Request $request, $id, $tid=NULL){
		$logged_id  = Auth::user()->id;
		$role_id  	= Auth::user()->role_id;
		$name       	 = Auth::user()->name;
		$register_id   	 = Auth::user()->register_id ;
		
		
		$userIdget 	= AssetRequest::select('arn.receiver_id','arn.message','asset_request.user_id','arn.dh_approved','arn.updated_at as dh_timing')->leftJoin('asset_request_notification as arn','arn.request_id','asset_request.id')->where('unique_no', $id)->first();	
		
		
		
		for($i = 0; $i < count($request->request_id); $i++){			
			// if(!empty($request->request_id[$i])){
				$dStatus = isset($request->status[$i]) ? $request->status[$i] : 0;
				//Request Edit
				
				$record = array();
				
				$request_type	= array_values($request->request_type);
				if(isset($request_type[$i])){
					$record['request_type']=$request_type[$i];
				}
								
				if(isset($request->product[$i])){
				 $record['product_id']=$request->product[$i];
			    }
				
				if(isset($request->product[$i])){
					$gproductname 			= 	DB::table('products')->where('id',$request->product[$i])->first();
					
					$record['category']		=	$gproductname->cat_id;
					$record['scategory']	=	$gproductname->sub_cat_id;
			    }
				
				if(isset($request->branch_id[$i])){
				 $record['branch_id']=$request->branch_id[$i];
			    }
				
				
				if(isset($request->type[$i])){
				 $record['type']=$request->type[$i];
		        }
				
				if(isset($request->title[$i])){
				$record['title']=$request->title[$i];
				}

				if(isset($request->emp_id[$i])){
					$record['emp_id']=$request->emp_id[$i];
				}


				if(isset($request->requirement[$i])){
				$record['requirement']=$request->requirement[$i];
				}
				if(isset($request->qty[$i])){
				$record['qty']=$request->qty[$i];
				}
				
				if(isset($request->uom[$i])){
				$record['uom']=$request->uom[$i];
				}
				
				
				if(isset($request->remark[$i])){
				$record['remark']=$request->remark[$i];
				}
				if(isset($request->status[$i])){
				  $record['status']=$request->status[$i];
				}
				
				if(isset($request->material_category[$i])){
				  $record['material_category']=$request->material_category[$i];
				}

				
				if(count($record) > 0){
					if(!empty($request->request_id[$i])){
						DB::table('asset_request')->where('id', $request->request_id[$i])->update($record);
					}else{
						$record['user_id']		=	$userIdget->user_id;
						$record['unique_no']	=	$id;
						$record['status']		=	1;
						
						$lastID = DB::table('asset_request')->insertGetId($record);
					}
				}
				
				//Asset Requisition Notification	
				$record2 = array();
				if(isset($request->status[$i])){
					$record2['status']=$request->status[$i];
				}
				
				if(isset($request->purchase_status[$i])){
					$record2['purchase_status']=$request->purchase_status[$i];
				}
				
				if(isset($request->purchase_reason[$i])){
					$record2['purchase_reason']=$request->purchase_reason[$i];
				}
				
				if(isset($request->reason[$i])){
					$record2['reason']=$request->reason[$i];
				}
				
				if(isset($request->dm_status[$i])){
					$record2['dm_status']=$request->dm_status[$i];					
				}
				
				if(isset($request->it_status[$i])){
					$record2['it_status']=$request->it_status[$i];
				}
				
				if(isset($request->pm_status[$i])){
					$record2['pm_status']=$request->pm_status[$i];
					$record2['pm_date']=date('Y-m-d H:i:s');
				}
				
				
				
				//Notification
				$bodyMsg ='';
				$bodyMsg2 ='';
				if($tid==""){		
					switch($request->status[$i]){
						case 0 : $mText = "Pending"; 	break;
						case 1 : $mText = "Approved"; 	break;
						case 2 : $mText = "Reject"; 	break;
					}
					
					$bodyMsg = "You requisition status updated by department head and now status is ".$mText." .";
					
					if($request->status[$i]=="Approved"){
						$next_user 	= User::where('role_id', '=', 25)->first();
						$uID  		= $next_user->id;  
						$bodyMsg2 	= "Department head has forwrading to new requisition";
					}
					
					$record2['updated_at']= date('Y-m-d H:i:s');
					$record2['dh_approved']= $logged_id;
				}
				
				
				if($tid==1){					
					switch($request->purchase_status[$i]){
						case 0 : $mText = "In Progress"; 				break;
						case 1 : $mText = "On Hold"; 					break;
						case 2 : $mText = "Deliver"; 					break;
						case 3 : $mText = "PO Generated";				break;
						case 4 : $mText = "Below 5000 - Deliver";		break;
						case 5 : $mText = "Cancel";						break;
						case 6 : $mText = "Rejected";					break;
						case 7 : $mText = "Proceed To Maintenance";		break;
					}
					
					$bodyMsg = "You requisition status updated by purchase team and now status is ".$mText." .";
					
					if($request->purchase_status[$i]==3 || $request->purchase_status[$i]==7 || $request->purchase_status[$i]==4){
						$record2['pt_updated']= date('Y-m-d H:i:s');
					}
					$record2['pt_approve']= $logged_id;
				}
				
				
				if($tid==2){					
					switch($request->dm_status[$i]){
						case 0 : $mText = "Pending"; 	break;
						case 1 : $mText = "Approved"; 	break;
						case 2 : $mText = "Reject"; 	break;
					}
					
					$bodyMsg = "You requisition status updated by PO team and now status is ".$mText." .";
								
				
					if($request->dm_status[$i]=='Approved'){
						$next_user 	= User::where('role_id', '=', 25)->first();
						$uID  		= $next_user->id;  
						$bodyMsg2 	= "PO Team has forwrading to new requisition";
					}
					
					$record2['po_approve']= $logged_id;
					$record2['dm_updated']= date('Y-m-d H:i:s');
				}
								
				if($tid==3){					
					switch($request->it_status[$i]){
						case 0 : $mText = "Pending"; 				break;
						case 1 : $mText = "In Stock"; 				break;
						case 2 : $mText = "Proceed To Purchase"; 	break;
						case 3 : $mText = "Rejected"; 				break;
						case 4 : $mText = "Transfer To Networking Team"; 	break;
						case 5 : $mText = "Proceed To Instock Approval"; 	break;
					}
					
					$bodyMsg = "You requisition status updated by inventory team and now status is ".$mText." .";
					
					if($request->it_status[$i]=='Proceed To Purchase'){
						$next_user 	= User::where('id', '=', 8799)->first();
						$uID  		= $next_user->id;  
						$bodyMsg2 	= "Inventory Team has forwrading to new requisition";
					}
					
					$record2['it_updated']= date('Y-m-d H:i:s');
					$record2['it_approve']= $logged_id;
				}
				
				
				if(count($record2) > 0){
					if(!empty($request->request_id[$i])){
						if (Auth::user()->user_details->degination == 'MANAGER-PURCHASE & STORE') {
							DB::table('asset_request_notification')
								->where(function($query) use ($request, $i) {
									$query->where('request_id', $request->request_id[$i])
										  ->orWhere('parent_id', $request->request_id[$i]);
								})
								->update($record2);
						} else {
							DB::table('asset_request_notification')
								->where('request_id', $request->request_id[$i])
								->update($record2);
						}
					}else{
						$record2['status']		=	1;
						$record2['request_id']	=	$lastID;
						$record2['sender_id']	=	$userIdget->user_id;
						$record2['message']		=	$userIdget->message;
						$record2['receiver_id']	=	$userIdget->receiver_id;
						$record2['dh_approved']	=	$userIdget->dh_approved;
						$record2['updated_at']	=	$userIdget->dh_timing;
						$record2['terms']		= "1. Taxes: All Taxes Inclusive on the above rate.
2. Description: Goods should be come as per the Specification & Inclusive of All Accessories with Warranty.
3. Delivery Time & Date : In 2 Working days after PO.
4. Validity : Validity of the purchase order is 30 Days.
5. Jurisdiction & Competence : All disputes will be subject to Jodhpur jurisdiction only.
6. Warranty : As per Manufacturer Terms & Condition.
7. Payment : Payment will be settled within 30 Days of Invoice receipt date.";
						DB::table('asset_request_notification')->insert($record2);
					}
				}
			
				
				//Employee Requisition Update Notification 
				if(!empty($request->request_id[$i])){
					$uRecord	= 	AssetRequest::where('id', $request->request_id[$i])->first();	
					
					$user 		=	DB::table('users')->select('id','gsm_token','device_type')->where('id', $uRecord->user_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();				
					$load = array();
					$load['title'] 		 =	"Requisition update!!";
					$load['description'] =	$bodyMsg;
					$load['body'] 		 =	$bodyMsg; 
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	NULL;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
			 
					$this->notificationDeviceWise($user, $load);
				}
				
				
				//Requisition Next Person Notification 
				if(!empty($uID)){
					$nuser = DB::table('users')->select('id','gsm_token','device_type')->where('id', $uID)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Requisition update!!";
					$load['description'] =	$bodyMsg2;
					$load['body'] 		 =	$bodyMsg2; 
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	NULL;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
					
					
					$this->notificationDeviceWise($nuser, $load);
				}
				
				
			// }else {
				// return redirect()->route('admin.request.requisition-request')->with('error', 'Something Went Wrong !');
			// }
			
			
		}
		
		return redirect()->route('admin.request.requisition-request')->with('success', 'Request Status Updated Successfully');
		
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
	
	public function showRequisition(Request $request){
		$request_id  = $request->request_id; 
		
		
		$respnse = AssetRequest::select('asset_request.title','asset_request.request_type','A.name as cname','B.name as sub_name','asset_request.requirement','asset_request.qty','asset_request.type','departments.name as dname','users.department_type','asset_request.unique_no','asset_request.created_at','arn.status as dhstatus','arn.it_status','arn.dm_status','arn.company','arn.purchase_status','asset_request.created_at','asset_request.product_status','arn.updated_at','arn.request_id','arn.quotation_one','arn.quotation_two','arn.quotation_three','arn.it_updated','arn.pt_updated','arn.reason','asset_request.image','products.name as proName','udh.name as department_head','material_category.name as material_category','arn.purchase_reason','branches.name as branch','branches.branch_location','branches.nickname','branches.short_name','asset_request.emp_grn','asset_request.uom','users.name as request_by','asset_request.type_of_business','asset_request.emp_grn_date','buyer.name as vendor_name','arn.pm_status')  
							->leftJoin('users','users.id', '=', 'asset_request.user_id')
							->leftJoin('asset_request_notification as arn','arn.request_id', '=', 'asset_request.id')
							->leftJoin('departments','departments.id', '=', 'users.department_type')
							->leftJoin('category AS A','A.id', '=', 'asset_request.category')
							->leftJoin('category AS B','B.id', '=', 'asset_request.scategory')
							->leftJoin('products','products.id','asset_request.product_id')
							->leftJoin('users as udh','udh.id', '=', 'arn.dh_approved')
							->leftJoin('material_category','material_category.id','asset_request.material_category')
							->leftjoin('branches','branches.id','asset_request.branch_id')
							->leftjoin('po_invoice','po_invoice.request_id','asset_request.id')
							->leftjoin('buyer','buyer.id','po_invoice.vendor_id')
							->where('asset_request.unique_no', $request_id)	
							->where('asset_request.is_deleted' , 0)
							->groupBy('asset_request.id')
							->get();
				
		
		//Department Head Name		
		/*
		if(!empty($respnse->department_type)){
			$user 	= User::where('department_type', $respnse->department_type)->where('role_id', '=', '21')->first();							
			if($user->name!=""){
				$uName = $user->name;
			}else{
				$uName = '-';
			}
		}else{
			$uName = '-';
		}
		*/
		
        if (!empty($respnse))
        {
            $res = "";
			
			echo $res = '<div class="table-responsive">
						<table class="table data-list-view">
							<thead>
								<tr>									
									<th>Req. No</th>
									<th>Request By</th>
									<th>Request Type</th>
									<th>Type Of Business</th>
									<th>Product Title</th>
									<th>Product Description</th>
									<th>Qty</th>
									<th class="remove-column">Category</th>
									<th class="remove-column">Sub Category</th>
									<th class="remove-column">Product</th>
									<th>UOM</th>
									<th class="remove-column">MRL Remark</th>
									<th>Request Category</th>
									<th>D. H. Status</th>
									<th>I. T. / N. T. Status</th>
									<th>P. T. / M. T. Status</th>
									<th>P. M. Status</th>
									<th>D. M. Status</th>
									<th>GRN</th>
									<th>Acceptance Date</th>
									<th>Vendor</th>
								</tr>
							</thead>
							<tbody>';
				foreach ($respnse as $key => $value)
				{
					//Request Type 
					$request_type = $value->request_type;
					switch($request_type){
						case 0	:	$reqText = "MRL";	$powoText = "PO"; break;
						case 1	:	$reqText = "WRL";	$powoText = "WO"; break;
					}
					
					//D.H. Status
					$status = $value->dhstatus;
					switch($status){
						case 1	:	$sText = "Approved";	break;
						case 2	:	$sText = "Rejected";	break;
						default	:	$sText = "Pending";		break;
					}
					
					$itstatus = $value->it_status;
					if($status==1){						
						switch($itstatus){
							case 1	:	$iText = "In Stock";			break;
							case 2	:	$iText = "Proceed To Purchase";	break;
							case 3	:	$iText = "Rejected";			break;
							case 4	:	$iText = "Transfer to Networking Team";			break;
							case 5	:	$iText = "Proceed To Instock Approval";			break;
							default	:	$iText = "Pending";				break;
						}
					}else{
						$iText = '-';
					}
					
					
					if(($itstatus==2 && $value->company != 'na' && $value->pm_status==1) || $itstatus==5){
						$dstatus = $value->dm_status;					
						switch($dstatus){
							case 1	:	$dText = "Approved";	break;
							case 2	:	$dText = "Rejected";	break;
							case 0	:	$dText = "Pending";		break;
						}
						
						
					}else{
						$dText = '-';
					}
					
					
					if(($itstatus==2 && $value->company != 'na') || $itstatus==5){
						$pmstatus = $value->pm_status;					
						switch($pmstatus){
							case 1	:	$pmText = "Approved";	break;
							case 2	:	$pmText = "Rejected";	break;
							case 0	:	$pmText = "Pending";		break;
						}
						
						
					}else{
						$pmText = '-';
					}
					
					
					if($itstatus==2){
						$purStatus = $value->purchase_status;
						
						switch($purStatus){
							case 1	:	$purText = "On Hold";		break;
							case 2	:	$purText = "Deliver";		break;
							case 3	:	$purText = "PO Generated";	break;
							case 4 	: 	$purText = "Below 5000 - Deliver";	break;
							case 5 	: 	$purText = "Cancel";			break;
							case 6 	: 	$purText = "Rejected";			break;
							case 7 	: 	$purText = "Proceed To Maintenance";	break;							
							case 0	:	$purText = "In Progress";	break;
						}
					}else{
						$purText = '-';
					}
						
					
					$dateNew 	= date('d-m-Y h:i:s', strtotime($value->created_at));
					$lastupdate = date('d-m-Y h:i:s', strtotime($value->updated_at));
					if(!empty($lastupdate)){
						$dUpdate = " </br>( ".$lastupdate." )";
					}else{
						$dUpdate = " ";
					}
					
					
					$it_updated = date('d-m-Y h:i:s', strtotime($value->it_updated));
					if($it_updated != '01-01-1970 05:30:00'){
						$itUpdate = " </br> - ( ".$it_updated." )";
					}else{
						$itUpdate = " ";
					}
					
					$pt_updated = date('d-m-Y h:i:s', strtotime($value->pt_updated));
					if($pt_updated != '01-01-1970 05:30:00'){
						$ptUpdate = " </br> ( ".$pt_updated." )";
					}else{
						$ptUpdate = " ";
					}
					
					if($value->it_status==1  && $value->product_status==1){
						$prod = ' - ( Product Accepted )';
					}else{
						$prod = ' ';
					}
					
					if(($value->purchase_status==2 || $value->purchase_status==4) && $value->product_status==1){
						$prod2 = ' - ( Product Accepted )';
					}else{
						$prod2 = ' ';
					}
					
					
					if($value->purchase_status==1 || $value->purchase_status==6){
						$proReason = ' - '.$value->purchase_reason;
					}else{
						$proReason = ' ';
					}
					
					
					$po = " ";
					$qOne = " ";
					$qTwo = " ";
					$qThree = " ";
					
					if(!empty($value->vendor_name)){
						$vendor = '<b>Invoice Vendor :</b> '.$value->vendor_name;
					}else{
						$vendor = '-';
					}					
					
					$approval = DB::table('users')->where('approval_id',Auth::user()->id)->get();
					
					if(Auth::user()->role_id ==31 || Auth::user()->id ==8799 || Auth::user()->id==6859 || Auth::user()->id==5409 ||  Auth::user()->role_id ==29 || Auth::user()->role_id ==25 || Auth::user()->department_type ==10 || Auth::user()->id==1196 || !empty($approval)){
						if($value->company!='' && $value->company!='na' && $value->company!='-'){ 
							$po = "</br>- <a href='".route('admin.request.poprint', $value->request_id)."' target='_blank' class='text-primary'>".$powoText."</a>";
							
							
							if($value->quotation_one !='' && (Auth::user()->id == 8799 || Auth::user()->id==6859 || Auth::user()->id==5409 ||  Auth::user()->role_id ==31 ||  Auth::user()->role_id ==29 || Auth::user()->role_id ==25 || Auth::user()->department_type ==10 || !empty($approval))){
								$qOne = "</br>- <a href='".asset('laravel/public/po_upload/' . $value->quotation_one)."' download >Quotation 1</a>";
							}else{
								$qOne = " ";
							}
							
							if($value->quotation_two !='' && (Auth::user()->id == 8799 || Auth::user()->id==6859 || Auth::user()->id==5409 || Auth::user()->role_id ==31 ||  Auth::user()->role_id ==29 || Auth::user()->role_id ==25 || Auth::user()->department_type ==10 || !empty($approval))){
								$qTwo = "</br>- <a href='".asset('laravel/public/po_upload/' . $value->quotation_two)."' download >Quotation 2</a>";
							}else{
								$qTwo = " ";
							}
							
							if($value->quotation_three != '' && (Auth::user()->id == 8799 || Auth::user()->id==6859 || Auth::user()->id==5409 || Auth::user()->role_id ==31 ||  Auth::user()->role_id ==29 || Auth::user()->role_id ==25 || Auth::user()->department_type ==10 || !empty($approval))){
								$qThree = "</br>- <a href='".asset('laravel/public/po_upload/' . $value->quotation_three)."' download >Quotation 3</a>";
							}else{
								$qThree = " ";
							}
						}else{
							$po = " ";
						}
					}
					
					if(!empty($value->remark)){
						$remark = $value->remark;
					}else{
						$remark = '-';
					}
					
					if(!empty($value->reason)){
						$reason = '( '.$value->reason.' )';
					}else{
						$reason = '-';
					}
					
					
					if(!empty($value->image)){
						$preview = "</br><a href='".asset('laravel/public/quotation/' . $value->image)."' download >Preview</a>";
					}else{
						$preview = '';
					}
					
					
					if($value->emp_grn!=0){ 
						$name = $value->branch;
						$words = explode(" ", $name);
						$firstLetters = "";

						foreach ($words as $word) {
							$firstLetters .= substr($word, 0, 1);
						}
						
						
						
						$grn_date = $value->created_at;
						
						$emp_grm = $value->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($grn_date)).'/'.$value->emp_grn; 
					} else{
						$emp_grm = '';
					}
					
					if(!empty($value->emp_grn_date)){
						$accept_grn_date = date('d-m-Y',strtotime($value->emp_grn_date));
					}else{
						$accept_grn_date = '-';
					}
					
					echo $res = '<tr>
									<td class="product-category"><span class="text-primary">REQ-'.$value->unique_no.'</span> </br>( '.$dateNew.')</td>
									<td class="product-category">'.$value->request_by.'</td>
									<td class="product-category">'.$reqText.'</td>
									<td class="product-category">'.$value->type_of_business.'</td>
									<td class="product-category">'.$value->title.'</td>
									<td class="product-category">'.$value->requirement.' '.$preview.'</td>
									<td class="product-category">'.$value->qty.'</td>
									<td class="product-category remove-column">'.$value->cname.'</td>
									<td class="product-category remove-column">'.$value->sub_name.'</td>
									<td class="product-category remove-column">'.$value->proName.'</td>
									<td class="product-category">'.$value->uom.'</td>
									<td class="product-category remove-column">'.$remark.'</td>
									<td class="product-category">'.$value->material_category.'</td>
									<td class="product-category"><b>'.$value->department_head.'</b><hr class="my-1">'.$sText.' '.$dUpdate.' </td>
									<td class="product-category">'.$iText.' '.$itUpdate.' '.$prod.'</td>
									<td class="product-category">'.$purText.' '.$ptUpdate.' '.$po.' '.$qOne.' '.$qTwo.' '.$qThree.' '.$prod2.' '.$proReason.' <br>'.$vendor.'</td>
									<td class="product-category">'.$pmText.'</td>
									<td class="product-category">'.$dText.' '.$reason.'</td>
									<td class="product-category">'.$emp_grm.'</td>
									<td class="product-category">'.$accept_grn_date.'</td>
									<td class="product-category">' . ((!empty($value->company) && $value->company != 'na') ? 'V' . $value->company : '-') . '</td>

								</tr>';
				}
				echo $res = '</tbody>
				</table>
			</div>';
            exit();
        }
        else
        {
            echo $res = "<label>Error</label>";
            die();
        }
	}
	
	public function poprint(Request $request,$id){
		
		$record = 	AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','asset_request.request_type','asset_request.remark','users.name as dhname','material_category.name as material_category')
					->leftJoin('asset_request','asset_request.id','asset_request_notification.request_id')
					->leftJoin('users','users.id','asset_request.remark')
					->leftJoin('material_category','material_category.id','asset_request.material_category')
					->where('asset_request_notification.request_id', $id)->first();
		
		
		$buyer 		=	Buyer::where('id',$record->company)->first();
		$branch 	=	Branch::where('id',$record->location)->first();
		$pName 		=	User::where('id',$record->pt_approve)->first();
		$hodName 	=	User::where('id',$record->dh_approved)->first();
		$cfoName 	=	User::where('id',$record->po_approve)->first();
		
		$buyer_account 	=	DB::table('buyer_bank_details')->where('buyer_id',$record->company)->first();		
		return view('admin.request.po', compact('record','buyer','branch','pName','hodName','cfoName','buyer_account','id'));
	}
	
	public function quotation_add(Request $request){		
		$id			= $request->appointment_id;
		$quotation	= $request->quotation;
		
		if($id!="" && $quotation!=""){
			$data	=	array(
				"appoinment_id"	=>	$id,
				"request"		=>	$quotation,
			);
			
			DB::table('quotation')->insert($data);
			
			return redirect()->back()->with('success', 'Quotation Send Successfully');
		}else{
			return redirect()->back()->with('error', 'Required filed missing!!');
		}
	}
	
	public function quotation_view(Request $request){	
		$quotation = DB::table('quotation')->select('quotation.*','asset_request.requirement')->leftJoin('asset_request','asset_request.id','quotation.appoinment_id')->get();
		
		return view('admin.request.quotation', compact('quotation'));
	}
	
	public function quotation_upload(Request $request){	
		$quotation_id = $request->quotation_id;
		
		if($quotation_id!=""){
			$data	=	array(
				"attachment"	=>	$this->uploadImage(Input::file('attachment')),
			);
			
			DB::table('quotation')->where('id',$quotation_id)->update($data);
			
			return redirect()->back()->with('success', 'Quotation Send Successfully');
		}else{
			return redirect()->back()->with('error', 'Required filed missing!!');
		}
	}
	
	public function product_accept(Request $request){	
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
						"product_date"	=>	date('Y-m-d'),	
						"request_id"	=>	$request_id,						
					);
					DB::table('inventory')->insert($data);
				}
			}
			
			return response(['status' => true, 'message' => 'Product Accept By Requisitor'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
	}
	
	public function getcompany_details(Request $request){
		$id  = $request->id; 
		
		
		$respnse = Buyer::where('id', $id)->first();
		
		
		if (!empty($respnse))
        {
            $res = "";
			$res .= "
					<textarea name='address' placeholder='Address' readonly>".$respnse->address."</textarea> </br>
					GSTIN - <input type='text' name='gstin' value='".$respnse->gst_no."' readonly/> </br>
					PHONE - <input type='text' name='phone' value='".$respnse->contact_no."' readonly/>	</br>
					Email - <input type='text' name='email' value='".$respnse->email."'/>					
					";
            
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<label>Error</label>";
            die();
        }
	}
	
	public function po_request(Request $request){
		$request_id 	= $request->request_id;
		$company		= $request->company;
		$address 		= $request->address;
		$gstin 			= $request->gstin;
		$phone 			= $request->phone;
		$pdate 			= $request->pdate;
		$po_location 			= $request->po_location;
		// $po_no 			= $request->po_no;
		$po_month 			= $request->po_month;
		$location 		= $request->location;
		$narration 		= $request->narration;
		$advance 		= $request->advance;
		$terms 			= $request->terms;
		$approved 		= $request->approved;
		$finalAmt 		= $request->finalAmt;
		$advanceAmt 	= $request->advanceAmt;
		$requiId 		= $request->requiId;
		$poImportant	= $request->poImportant;
		$po_address		= $request->po_address;
		$po_gst			= $request->po_gst;
		
		
		$buyer_account 	=	DB::table('buyer_bank_details')->where('buyer_id',$company)->where('bnk_status','Active')->first();
		
		
		if(empty($poImportant)){
			$poImportant = 'No';
		}
		
		//Request Type get
		$requestType = DB::table('asset_request')->select('request_type')->where('id',$request_id)->first();
		
		
		//PO Number Generate 
		$mt 	  = date('m');
		$yr 	  = date('Y');
		$date 	  = "2023-04-04";
		$maxValue = DB::table('asset_request_notification')
					->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
					->where('asset_request.request_type',$requestType->request_type)
					->whereYear('asset_request_notification.pdate', '=', $yr)
					->whereMonth('asset_request_notification.pdate', '=', $mt)
					->where('asset_request_notification.pdate','>=',$date)
					->max('asset_request_notification.po_no');
					
		$po_no 	  = $maxValue + 1;
		
		
		
		if($request_id!="" && $company!=""){	
			$data = array(
				'po_address'		=> $po_address,
				'po_gst'			=> $po_gst,
				'company'			=> $company,
				'address'			=> $address,
				'gstin'				=> $gstin,
				'phone'				=> $phone,
				'pdate'				=> $pdate,
				'po_location'		=> $po_location,
				'po_no'				=> $po_no,
				'po_month'			=> $po_month,
				'location'			=> $location,
				'narration'			=> $narration,
				'advance'			=> $advance,
				'terms'				=> $terms,
				'approved'			=> $approved,				
				'final_amt'			=> $finalAmt,				
				'advance_amt'		=> $advanceAmt,				
				'po_important'		=> $poImportant,				
				'account_id'		=> $buyer_account->id??0,				
			);
			
			
			if(!empty(Input::file('quotation_one'))){
				$data['quotation_one'] = $this->uploadProfileImage2(Input::file('quotation_one'));
			}
			
			if(!empty(Input::file('quotation_two'))){
				$data['quotation_two'] = $this->uploadProfileImage3(Input::file('quotation_two'));
			}
			
			if(!empty(Input::file('quotation_three'))){
				$data['quotation_three'] = $this->uploadProfileImage4(Input::file('quotation_three'));
			}
			
			DB::table('asset_request_notification')->where('request_id', $request_id)->update($data);
		
		
			
			
			for($j=0; $j < count($request->requiId); $j++){	
				$rid = explode("&&",$requiId[$j]);				
				if($rid[0] != $request_id){
					DB::table('asset_request_notification')->where('request_id', $rid[0])->update(['company' => '-','parent_id'=>$request_id]);
				}
			}
		
			for($i = 0; $i < count($request->item); $i++){
				$rid = explode("&&",$requiId[$i]);
				
				if(!empty($request->item[$i])){	

					$record = array();
					if(isset($request_id)){
						$record['asset_id']=$request_id;
					}
					
					if(isset($request->item[$i])){
						$record['item']=$request->item[$i];
					}
					
					if(isset($request->requiId[$i])){
						$record['po_request_id']=$rid[0];
					}
					
					if(isset($rid[1]) && !empty($rid[1])){
						$record['product_id']=$rid[1]??0;
					}
					
					
					if(isset($request->uom[$i])){
						$record['uom']=$request->uom[$i];
					}
					
					if(isset($request->qty[$i])){
						$record['qty']=$request->qty[$i];
					}
					
					if(isset($request->rate[$i])){
						$record['rate']=$request->rate[$i];
					}
					
					if(isset($request->amount[$i])){
						$record['amount']=$request->amount[$i];
					}
					
					if(isset($request->gstrate[$i])){
						$record['gst_rate']=$request->gstrate[$i];
					}
					
					if(isset($request->gstamt[$i])){
						$record['gst_amt']=$request->gstamt[$i];
					}
					
					if(isset($request->totalamt[$i])){
						$record['total']=$request->totalamt[$i];
					}
													
					DB::table('po_history')->insert($record);					
				}				
			}
			
			if($requestType->request_type=='1'){
				$resMsg = "WO";
			}else{
				$resMsg = "PO";
			}
			
			return redirect()->back()->with('success', $resMsg.' Send Successfully');
		}else{
			return redirect()->back()->with('error', 'Required Filed Missing!!');
		}		
	}
	
	public function get_request_data(Request $request){
		$getRequest = AssetRequest::select('asset_request.title','asset_request.requirement','asset_request.qty','asset_request_notification.terms','asset_request.unique_no')
		->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
		->where('asset_request.id', $request->request_id)
		->first();
		
		
		
		$subCatData =  	AssetRequest::select('asset_request.id','asset_request.title','products.id as pid','products.name as pname')
						->leftjoin('products','products.id','asset_request.product_id')
						->leftjoin('asset_request_notification as arn','arn.request_id','asset_request.id')
						->where('asset_request.unique_no', $getRequest->unique_no);
						
		if($request->is_edit!=1){
			$subCatData	 = $subCatData->where('arn.company','=',0);
		}
		$subCatData	 = $subCatData->where('it_status','!=',1)
						->orderBy('id','asc')->get();
	
						
		$res  = '<option value="">-- Select --</option>';
		foreach ($subCatData as $key => $value)
		{
			if (!empty($value->id))
			{
				$res .= "<option value='" . $value->id . "&&".$value->pid."'>" . $value->pname . " - ".$value->title."</option>";
			}
		}
	
		return response(['status' => true, 'title' => $getRequest->title, 'requirement' => $getRequest->requirement,'qty' => $getRequest->qty,'terms' => $getRequest->terms,'options' => $res ], 200);
	}
	
	
	public function po_status_update(Request $request){
		$id 	=	$request->id;
		$value 	=	$request->value;
		$rreason 	=	$request->rreason;
		$logged_id  = Auth::user()->id;
		
		if($id!="" && $value!=""){
			$data = array(
				"dm_status"		=>	$value,
				"po_approve"	=>	$logged_id,
				"reason"		=>	$rreason,
				"dm_updated"	=>	date('Y-m-d H:i:s'),
			);
						
			DB::table('asset_request_notification')->where('request_id', $id)->orWhere('parent_id', $id)->update($data);
			
			
			
			// $mrl = DB::table('asset_request')->where('id',$id)->first();			
			// $emailData = [
				// 'mrl_no' 	=> $mrl->unique_no,
				// 'Remarks' 	=> $rreason??'-',
			// ];
			
			// $tomail = 'chetan.makwana@utkarsh.com';
			// Mail::to($tomail)->send(new PoWoStatusMail($emailData)); 
			
			return response(['status' => true, 'message' => 'PO status updated successfully.'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!'], 200);
		}
	}
	
	
	public function uploadImage($image){
       $drive = public_path(DIRECTORY_SEPARATOR . 'quotation' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
	}
	
		
	function uploadProfileImage2($image){
		$drive = public_path(DIRECTORY_SEPARATOR . 'po_upload' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
    }	
	
	function uploadProfileImage3($image){
        $drive = public_path(DIRECTORY_SEPARATOR . 'po_upload' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
    }
	
	function uploadProfileImage4($image){
        $drive = public_path(DIRECTORY_SEPARATOR . 'po_upload' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;

    }
	
	public function check_product_quantity(Request $request){
		$pro_id  = $request->pro_id; 
		
		
		$respnse = Inventory::select(DB::raw("inventory.*,SUM(qty) as total_qty"))->where('product_id',$pro_id)->where('is_deleted', '0')->where('is_parent', 0)->first();
		
		
		$check_total_approved = DB::table('transfer')
								  ->where('product_id', $pro_id)
								  ->where('transfer_from', '0')
								  ->where('is_deleted','=','0')
								  ->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
								  ->get()
								  ->sum("qty");
		
		if(!empty($check_total_approved)){
			$rem_pro = $respnse['total_qty'] - $check_total_approved;
		}
		else{
			$rem_pro = $respnse['total_qty'];
		}
				
		$res = $rem_pro;            
		echo $res;
		die();
	
	}
	
	public function po_list(Request $request){
		$logged_id  = Auth::user()->id;		
		$role_id 	= Auth::user()->role_id;
		$department_type 	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$user = User::with('user_details')->where('id', $logged_id)->first();
		$degination	= $user->user_details->degination;		
		
		
		
		$user_branch= Auth::user()->user_branches[0]->branch_id;
		
		$rnumber 	= Input::get('rnumber');
		$pwnumber 	= Input::get('pwnumber');
		
		$fdate 		= Input::get('fdate');
        $tdate 		= Input::get('tdate');
        $branch_id  = Input::get('branch_id');
        $po_status  = Input::get('po_status');
        $department_id  = Input::get('department_id');
        $invoice_status  = Input::get('invoice_status');
        $pname  = Input::get('pname');
        $uname  = Input::get('uname');
        $vendor  = Input::get('vendor');
	
		
		$notification = AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','departments.name as dname','branches.name as bname')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')							
							->join('users','users.id', '=', 'asset_request_notification.sender_id')
							->join('departments','departments.id', '=', 'users.department_type')
							->join('userbranches','users.id', '=', 'userbranches.user_id')
							->join('branches','branches.id', '=', 'userbranches.branch_id')
							->leftjoin('buyer','buyer.id', '=', 'asset_request_notification.company')
							->where('asset_request_notification.is_deleted', '=', '0')
							->where('asset_request.is_deleted', '=', '0');						
							
							
							if($department_type==10 && !empty($rnumber)){
								$notification->whereRaw("(asset_request_notification.status = 0 OR asset_request_notification.status = 1 OR asset_request_notification.it_status = '1' OR asset_request_notification.it_status = '0'OR asset_request_notification.purchase_status = '0'  OR asset_request_notification.company = 'na')");
							}else{	
								$notification->where('asset_request_notification.status', '=', '1')
								->whereRaw("(asset_request_notification.purchase_status = '2' OR asset_request_notification.purchase_status = '3' OR asset_request_notification.purchase_status = '4')")
								->whereRaw("(asset_request_notification.company != 'na' OR asset_request_notification.company != '-' )");
							}
							
							$notification->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc')
							->orderby('asset_request_notification.id', 'desc')
							->groupby('asset_request.unique_no');
			
		
		if(!empty($rnumber)){
			$notification->whereRaw("(asset_request.unique_no = '".$rnumber."')");
		}
		
		if (!empty($pwnumber)) {
			$notification->whereRaw("CONCAT(asset_request_notification.po_no, '/', asset_request_notification.po_month) = ?", [$pwnumber]);
		}
		
		if(!empty($pname)){
			$notification->whereRaw("(asset_request.title LIKE '%".$pname."%')");	
		}
		
		if(!empty($uname)){
			$notification->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($department_id)){
			$notification->where('departments.id', '=', $department_id);
		}
			
		if(!empty($branch_id)){
			$notification->where('asset_request_notification.location', '=', $branch_id);
		}
		
		if(!empty($po_status)){
			$notification->where('asset_request_notification.dm_status', '=', $po_status);
		}
		
		if(!empty($vendor)){
			$notification->where('buyer.id', $vendor);
		}
		
		
		if (!empty($fdate) && !empty($tdate)) {			
            $notification->where('asset_request_notification.pdate', '>=', $fdate)->where('asset_request_notification.pdate', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $tdate . '%');
        }


		// $notification = $notification->toSql();
		
		// print_r($notification);
		
		// die();
		
		$notification = $notification->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $notification = $notification->get();
		
		return view('admin.request.po-list', compact('notification','degination','pageNumber','notification','params'));
	}
	
	
	public function po_download_excel(Request $request){
		$logged_id  = Auth::user()->id;		
		$role_id 	= Auth::user()->role_id;
		$department_type 	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$user = User::with('user_details')->where('id', $logged_id)->first();
		$degination	= $user->user_details->degination;		
		
		
		
		$user_branch= Auth::user()->user_branches[0]->branch_id;
		
		$rnumber 	= Input::get('rnumber');
		
		$fdate 		= Input::get('fdate');
        $tdate 		= Input::get('tdate');
        $branch_id  = Input::get('branch_id');
        $po_status  = Input::get('po_status');
        $department_id  = Input::get('department_id');
        $invoice_status  = Input::get('invoice_status');
        $pname  = Input::get('pname');
        $uname  = Input::get('uname');
		$vendor  = Input::get('vendor');
	
		
		$notification = AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','departments.name as dname','branches.name as bname','branches.branch_location','po_history.*','buyer.name as vendor','asset_request.request_type','asset_request.remark as emp_remark','material_category.name as mcategory')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')							
							->leftjoin('users','users.id', '=', 'asset_request_notification.sender_id')
							->leftjoin('departments','departments.id', '=', 'users.department_type')
							->leftjoin('userbranches','users.id', '=', 'userbranches.user_id')
							->leftjoin('branches','branches.id', '=', 'userbranches.branch_id')
							->leftjoin('po_history','po_history.asset_id', 'asset_request.id')
							->leftjoin('buyer','buyer.id','asset_request_notification.company')
							->leftjoin('material_category','material_category.id','asset_request.material_category')
							->where('asset_request_notification.is_deleted', '=', '0')
							->where('asset_request.is_deleted', '=', '0');						
							
							
							if($department_type==10 && !empty($rnumber)){
								$notification->whereRaw("(asset_request_notification.status = 0 OR asset_request_notification.status = 1 OR asset_request_notification.it_status = '1' OR asset_request_notification.it_status = '0'OR asset_request_notification.purchase_status = '0'  OR asset_request_notification.company = 'na')");
							}else{	
								$notification->where('asset_request_notification.status', '=', '1')
								->whereRaw("(asset_request_notification.purchase_status = '2' OR asset_request_notification.purchase_status = '3' OR asset_request_notification.purchase_status = '4')")
								->whereRaw("(asset_request_notification.company != 'na' AND asset_request_notification.company != '-' )");
							}
							
							$notification->orderby('asset_request_notification.pdate', 'asc');
							// ->groupby('asset_request.unique_no');
			
		
		if(!empty($rnumber)){
			$notification->whereRaw("(asset_request_notification.po_no = '".$rnumber."' OR asset_request.unique_no = '".$rnumber."')");
		}
		
		if(!empty($pname)){
			$notification->whereRaw("(asset_request.title LIKE '%".$pname."%')");	
		}
		
		if(!empty($uname)){
			$notification->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($department_id)){
			$notification->where('departments.id', '=', $department_id);
		}
			
		if(!empty($branch_id)){
			$notification->where('asset_request_notification.location', '=', $branch_id);
		}
		
		// if(!empty($po_status)){
			// $notification->where('asset_request_notification.dm_status', '=', $po_status);
		// }
		
		
		if (!empty($fdate) && !empty($tdate)) {			
            $notification->where('asset_request_notification.pdate', '>=', $fdate)->whereDate('asset_request_notification.pdate', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $tdate . '%');
        }
		
		if(!empty($vendor)){
			$notification->where('buyer.id', $vendor);
		}
		
		$comman_result = $notification->get();
			
		$responseArray = array();
		// echo '<pre>'; print_r($comman_result);die;
		
		// return response($comman_result,200);die;
		
		
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 					
				if(!empty($valAtt->po_location)){
					$po = 'UTKPO-'.$valAtt->po_location.'-'.$valAtt->po_no.'-'.$valAtt->po_month;
				}else{
					$po = 'UTKPO-'.$valAtt->po_no;
				}
				
				if($valAtt->request_type=='1'){
					$rType = "WRL";
				}else{
					$rType = "MRL";
				}
				
				$responseArray[$key]['po_no'] = $po;
				$responseArray[$key]['unique_no'] = $valAtt->unique_no;
				$responseArray[$key]['request_type'] = $rType;
				$responseArray[$key]['pdate'] = date("d-m-Y", strtotime($valAtt->pdate));
				$responseArray[$key]['item'] = $valAtt->item;
				$responseArray[$key]['uom'] = $valAtt->uom;
				$responseArray[$key]['qty'] = $valAtt->qty;
				$responseArray[$key]['rate'] = $valAtt->rate;
				$responseArray[$key]['amount'] = $valAtt->amount;
				$responseArray[$key]['gst_rate'] = $valAtt->gst_rate;
				$responseArray[$key]['gst_amt'] = $valAtt->gst_amt;
				$responseArray[$key]['total'] = $valAtt->total;
				$responseArray[$key]['bname'] = $valAtt->bname;
				$responseArray[$key]['branch_location'] 	= $valAtt->branch_location;
				$responseArray[$key]['vendor'] 	= $valAtt->vendor;
				$responseArray[$key]['emp_remark'] 	= $valAtt->emp_remark;
				$responseArray[$key]['narration'] 	= $valAtt->narration;
				$responseArray[$key]['mcategory'] 	= $valAtt->mcategory;
			}
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new PoReportExport($responseArray), 'POReport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function copy_mrl(Request $request){
		$id		=	$request->rNumber;
		$rUser	=	$request->rUser;
	
				
		$users_details	 =	DB::table('users')->select('users.id','users.department_type','users.name','users.register_id','users.role_id','userbranches.branch_id')->leftJoin('userbranches','userbranches.user_id','users.id')->where('users.id','=',$rUser)->first();

		$logged_id       = 	$rUser;
		$department_type = 	$users_details->department_type;
		$name       	 = 	$users_details->name;
		$register_id   	 = 	$users_details->register_id;
		$role       	 = 	$users_details->role_id;
				
		if($role==21 || $role==31){
			$user = User::where('role_id', '=', 25)->first();
			$uID  = $user->id;
		}else if($logged_id==8799 || $logged_id==6859 || $role==25){
			$user = User::where('role_id', '=', 25)->first();
			$uID  = $user->id;
		}else{
			$user = User::where('department_type', $department_type)->where('role_id', '=', 21)->where('status', '=', '1')->first();
			$uID  = $user->id;
		}	
		
		
		$mrl_number	=	AssetRequest::select('unique_no')->max('unique_no') + 1;
			
		if(!empty($id) && !empty($rUser)){
			$record = AssetRequest::select('asset_request.*','arn.*')->leftJoin('asset_request_notification as arn','arn.request_id','asset_request.id')->where('asset_request.id', $id)->first();
			
			
			$asset_id 	= 	DB::table('asset_request')->insertGetId([
								"unique_no"		=>	$mrl_number,
								"user_id"		=>	$rUser,
								"emp_id"		=>	$rUser,
								"category"		=>	$record['category'],
								"scategory"		=>	$record['scategory'],
								"product_id"	=>	$record['product_id'],
								"branch_id"		=>	$users_details->branch_id,
								"title"			=>	$record['title'],
								"qty"			=>	$record['qty'],
								"type"			=>	$record['type'],
								"requirement"	=>	$record['requirement'],
								"image"			=>	$record['image'],
								"remark"		=>	$record['remark'],
								"copy_mrl"		=>	'1'
							]);
			
			
			if($asset_id){	
				$this->maintain_history($logged_id, 'asset_request', $asset_id, 'add_request', json_encode($record));
				
				
				$nData = array(
					'sender_id'	 	=> $logged_id, 
					'request_id' 	=> $asset_id, 
					'receiver_id'	=> json_encode(array($uID)), 
					'terms'			=> $record['terms'], 
					'message' 		=> $name.'- ('.$register_id.') raised a new requirment'
				);
				AssetRequestNotification::create($nData);
				
				
				if($role==25 || $role==21){
					//Asset Table
					$newData = array("status" => 1);								
					DB::table('asset_request')->where('id',$asset_id)->update($newData);
					
					//Asset Notification Table
					$newData2 = array("status"	=>	1,"dh_approved"	=>	$logged_id);								
					DB::table('asset_request_notification')->where('request_id', $asset_id)->update($newData2);
				}
				
				
				//Requisition Notification 
				$users = DB::table('users')->select('id','gsm_token','device_type')->where('id', $uID)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
				$load = array();
				$load['title'] 		 =	"Requisition update!!";
				$load['description'] =	$name.'- ('.$register_id.') raised a new requirment';
				$load['body'] 		 =	$name.'- ('.$register_id.') raised a new requirment';
				$load['image'] 		 =	asset('laravel/public/images/test-image.png');
				$load['date'] 		 =	NULL;
				$load['status'] 	 =	NULL;
				$load['type'] 		 =	'general';
		 
				$this->notificationDeviceWise($users, $load);
				
				return redirect()->route('admin.request.index')->with('success', 'MRL Copy Successfully');
			}else{
				return redirect()->route('admin.request.add-request')->with('error', 'Something Went Wrong !');
			} 
		
		}
	}
	
	
	public function po_payment_excel(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
	
		$rnumber 	= Input::get('rnumber');
		$hadate 	= Input::get('hadate');
		$vendor_id 	= Input::get('vendor_id');
		$branch_id 	= Input::get('branch_id');
		
		$comman_result	=	DB::table('po_invoice')->select('po_invoice.*','asset_request.unique_no','arn.po_no','arn.po_month','departments.name as dname','branches.name as bname','branches.branch_location','buyer.name as vendor','asset_request.created_at as mrl_date','arn.pdate as po_date','arn.po_location','branches.short_name','asset_request.emp_grn')
							->leftJoin('asset_request_notification as arn','arn.request_id','po_invoice.request_id')
							->leftJoin('asset_request','asset_request.id','arn.request_id')								
							->leftJoin('buyer','buyer.id','po_invoice.vendor_id')
							->leftjoin('users','users.id', '=', 'arn.sender_id')
							->leftjoin('departments','departments.id', '=', 'users.department_type')							
							->leftjoin('branches','branches.id', '=', 'asset_request.branch_id')
							->where('arn.is_deleted', '=', '0')
							->where('asset_request.is_deleted', '=', '0')
							->where('arn.status', '=', '1')
							->orderBy('po_invoice.created_at','DESC');
							
		if(!empty($rnumber)){
			$comman_result->whereRaw("(arn.po_no = '".$rnumber."' OR asset_request.unique_no = '".$rnumber."' )");
		}
		
		if(!empty($hadate)){
			$comman_result->where('po_invoice.handover_accounts',$hadate);
		}
		
		if(!empty($vendor_id)){
			$comman_result->whereRaw("(buyer.id = '".$vendor_id."')");
		}
		
		if(!empty($branch_id)){
			$comman_result->where('asset_request.branch_id',$branch_id);
		}
		
		$comman_result = $comman_result->get();
		
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){
				if($valAtt->emp_grn!=0){ 
					$name = $valAtt->bname;
					$words = explode(" ", $name);
					$firstLetters = "";

					foreach ($words as $word) {
						$firstLetters .= substr($word, 0, 1);
					}
					
					
					
					$grn_date = $valAtt->mrl_date;
					
					$emp_grn = $valAtt->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($grn_date)).'/'.$valAtt->emp_grn; 
				} else{
					$emp_grn = '-';
				}
		
				
				$responseArray[$key]['unique_no'] 			= $valAtt->unique_no;
				$responseArray[$key]['po_no'] 				= $valAtt->po_no;
				$responseArray[$key]['po_month'] 			= $valAtt->po_month;
				$responseArray[$key]['po_location'] 		= $valAtt->po_location;
				
				$responseArray[$key]['date_of_invoice'] 	= isset($valAtt->date_of_invoice) ?  date("d-m-Y", strtotime($valAtt->date_of_invoice)) : '';
				$responseArray[$key]['invoice_no'] 			= $valAtt->invoice_no;
				$responseArray[$key]['handover_accounts'] 	= isset($valAtt->handover_accounts) ?  date("d-m-Y", strtotime($valAtt->handover_accounts)) : '';
				$responseArray[$key]['total'] 				= $valAtt->amount;
				$responseArray[$key]['dname'] 				= $valAtt->dname;
				$responseArray[$key]['bname']			 	= $valAtt->bname;
				$responseArray[$key]['branch_location']  	= $valAtt->branch_location;
				$responseArray[$key]['vendor'] 			 	= $valAtt->vendor;
				$responseArray[$key]['utr_no'] 				= $valAtt->utr_no;
				$responseArray[$key]['status'] 				= $valAtt->status;
				$responseArray[$key]['remark'] 				= $valAtt->remark;
				
				
				$responseArray[$key]['mrl_date'] 			= isset($valAtt->mrl_date) ?  date("d-m-Y", strtotime($valAtt->mrl_date)) : '';
				$responseArray[$key]['po_date'] 			= isset($valAtt->po_date) ?  date("d-m-Y", strtotime($valAtt->po_date)) : '';
				$responseArray[$key]['emp_grn'] 			= $emp_grn;
			}
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new PoPaymentExport($responseArray), 'PoPaymentExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	} 
	
	public function invoice_details(Request $request){		
		$invoice_date		= $request->invoice_date;
		$invoice_number		= $request->invoice_number;
		$handover_accounts	= $request->handover_accounts;
		$asset_noti_id		= $request->asset_noti_id;
		$remark		= $request->remark;
		$amount		= $request->amount;
		$vendor		= $request->vendor;
		$payment_type		= $request->payment_type;
		
		if($invoice_date!="" && $invoice_number!="" && $handover_accounts!="" && $remark!="" && $amount!="" && $vendor!=""  && $payment_type!=""){
			$rows = DB::table('po_invoice')->where('invoice_no',$invoice_number)->where('vendor_id',$vendor)->count();
			
			
			if($rows > 0){
				return redirect()->back()->with('error', 'Invoice number already exist');
			}else{
				if(!empty($asset_noti_id)){
					$request_id = $asset_noti_id;
				}else{
					$request_id = 0;
				}
				
				$data = array(
					"date_of_invoice"	=>	$invoice_date,
					"invoice_no"		=>	$invoice_number,
					"handover_accounts"	=>	$handover_accounts,
					"request_id"		=>	$request_id,
					"attachment"		=>	$this->uploadImage(Input::file('attachment')),
					"remark"			=>	$remark,
					"amount"			=>	$amount,
					"vendor_id"			=>	$vendor,
					"user_id"			=>	Auth::user()->id,
					"type"				=>	$payment_type,
				);
				
				DB::table('po_invoice')->insert($data);
				
				return redirect()->back()->with('success', 'Invoice Date Updated');
			}				
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	public function vendor_invoice(Request $request){
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$rnumber 	= Input::get('rnumber');
		$hadate 	= Input::get('hadate');
		$vendor_id 	= Input::get('vendor_id');
		$branch_id 	= Input::get('branch_id');
		
		$vendor = DB::table('po_invoice')->select('po_invoice.*','arn.po_location','arn.po_no','arn.po_month','asset_request.unique_no','buyer.name as bname','arn.company','branches.short_name','branches.name as branch','asset_request.emp_grn','asset_request.created_at as mrl_created_at')
					->leftJoin('asset_request_notification as arn','arn.request_id','po_invoice.request_id')
					->leftJoin('asset_request','asset_request.id','arn.request_id')
					->leftJoin('buyer','buyer.id','po_invoice.vendor_id')
					->leftjoin('branches','branches.id','asset_request.branch_id')
					->orderBy('po_invoice.created_at','DESC');
		
		if(!empty($rnumber)){
			$vendor->whereRaw("(arn.po_no = '".$rnumber."' OR asset_request.unique_no = '".$rnumber."' )");
		}
		
		if(!empty($hadate)){
			$vendor->where('handover_accounts',$hadate);
		}
		
		if(!empty($vendor_id)){
			$vendor->whereRaw("(buyer.id = '".$vendor_id."')");
		}
		
		if(!empty($branch_id)){
			$vendor->where('asset_request.branch_id',$branch_id);
		}
		
		$vendor = $vendor->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $vendor	=		$vendor->get();
		return view('admin.request.vendor-invoice',compact('vendor','pageNumber','params'));
	}
	
	public function accounts_invoice_update(Request $request){
		$invoice_id	=	$request->invoice_id;
		$status	=	$request->status;
		$utr_no	=	$request->utr_no;
		
		$logged_id       = Auth::user()->id;
		
		if(!empty($invoice_id) && !empty($status)){
			$data = array(
				"status"	=>	$status,
				"utr_no"	=>	$utr_no,
				"updated_at"=>	date('Y-m-d h:i:s'),
				"user_id"	=>	$logged_id,
			);
			
			DB::table('po_invoice')->where('id',$invoice_id)->update($data);
			
			return redirect()->back()->with('success', 'Accounts Status Updated');
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	public function togglePublish(Request $request) {	
		$vendor_id	=	$request->vendor_id;
        if(!empty($vendor_id)){
			$logged_id       = Auth::user()->id;
			if($logged_id==8799 || $logged_id==6859){
				$status		=	$request->status;
				$cfo_reason	=	$request->cfo_reason;
				
				$data = array(
					"cfo_status" =>	$status,
					"cfo_reason" =>	$cfo_reason,
				);				
				
				DB::table('po_invoice')->where('id',$vendor_id)->update($data);
				
				return redirect()->back()->with('success', 'CFO Status Updated Successfully.');
			}else{
				return redirect()->back()->with('error', 'You are not eligible.');	
			}
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong.');	
		}
    }
	
	public function manual_invoice(Request $request){
		return view('admin.request.manual-invoice');
	}
	
	public function request_pending_accept(Request $request){
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		
		$page  = Input::get('page');
		
		$mrl	=	$request->mrl;
		$cdate	=	$request->cdate;
		$uname	=	$request->uname;
		$branch_id	=	$request->branch_id;
		
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$getRequest = AssetRequest::select('asset_request.id','A.name','B.name as sub_name','asset_request.user_id','asset_request.qty','asset_request.type','asset_request.status','asset_request.unique_no','asset_request.product_status','products.name as pname','users.name as uname','asset_request.created_at','branches.name as brname')
		->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
		->leftJoin('category AS A','A.id', '=', 'asset_request.category')
		->leftJoin('category AS B','B.id', '=', 'asset_request.scategory')
		->leftJoin('products','products.id', '=', 'asset_request.product_id')
		->leftJoin('users','users.id', '=', 'asset_request.user_id')
		->join('branches','branches.id', '=', 'asset_request.branch_id');
		
		if(Auth::user()->role_id != 29 && Auth::user()->id != 8799){
			$getRequest->where('branches.branch_location', '=', $location);
		}
		
		$getRequest->whereRaw("(asset_request_notification.purchase_status = '2' OR asset_request_notification.purchase_status = '4' OR asset_request_notification.it_status = 1)")
		->where('asset_request.status', 1)
		->where('asset_request.is_deleted', 0)
		->where('asset_request.product_status', 0)
		->where('asset_request.request_type', '0')
		->orderby('asset_request.id','desc');
		
		if(!empty($mrl)){
			$getRequest->where('asset_request.unique_no', '=', $mrl);
		}
		
		if(!empty($cdate)){
			$getRequest->whereDate('asset_request.created_at', '=', $cdate);
		}
		
		if(!empty($uname)){
			$getRequest->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($branch_id)){
			$getRequest->where('asset_request.branch_id', '=', $branch_id);
		}
		
		$getRequest = $getRequest->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		
		return view('admin.request.request_pending_accept',compact('getRequest','pageNumber','params'));
	}
	
	public function request_pending_accept_excel(Request $request){
		$mrl	=	$request->mrl;
		$cdate	=	$request->cdate;
		$uname	=	$request->uname;
		$branch_id	=	$request->branch_id;
		
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$comman_result = AssetRequest::select('asset_request.id','A.name','B.name as sub_name','asset_request.user_id','asset_request.qty','asset_request.type','asset_request.status','asset_request.unique_no','asset_request.product_status','products.name as pname','users.name as uname','asset_request.created_at','branches.name as brname')
		->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
		->leftJoin('category AS A','A.id', '=', 'asset_request.category')
		->leftJoin('category AS B','B.id', '=', 'asset_request.scategory')
		->leftJoin('products','products.id', '=', 'asset_request.product_id')
		->leftJoin('users','users.id', '=', 'asset_request.user_id')
		->join('branches','branches.id', '=', 'asset_request.branch_id');
		
		if(Auth::user()->role_id != 29){
			$comman_result->where('branches.branch_location', '=', $location);
		}
		
		$comman_result->whereRaw("(asset_request_notification.purchase_status = '2' OR asset_request_notification.purchase_status = '4' OR asset_request_notification.it_status = 1)")
		->where('asset_request.status', 1)		
		->where('asset_request.is_deleted', 0)
		->where('asset_request.product_status', 0)
		->where('asset_request.request_type', '0')
		->orderby('asset_request.id','desc');
		
		if(!empty($mrl)){
			$comman_result->where('asset_request.unique_no', '=', $mrl);
		}
		
		if(!empty($cdate)){
			$comman_result->whereDate('asset_request.created_at', '=', $cdate);
		}
		
		if(!empty($uname)){
			$comman_result->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($branch_id)){
			$comman_result->where('asset_request.branch_id', '=', $branch_id);
		}
		
		$comman_result = $comman_result->get();
		
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$valAtt){ 					
				$responseArray[$key]['unique_no'] 		= 	$valAtt->unique_no;
				$responseArray[$key]['user_name'] 		= 	$valAtt->uname;
				$responseArray[$key]['cat_name'] 		=	$valAtt->name;
				$responseArray[$key]['sub_name'] 		=	$valAtt->sub_name;
				$responseArray[$key]['pro_name'] 		=	$valAtt->pname;
				$responseArray[$key]['qty'] 			=	$valAtt->qty;
				$responseArray[$key]['brname'] 			=	$valAtt->brname;
				$responseArray[$key]['created_at'] 		=	date('d-m-Y',strtotime($valAtt->created_at));
			}
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new RequestPendingAcceptExport($responseArray), 'RequestPendingAcceptExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function store_purchase_dashboard(Request $request){
		$totalMrl 	=	AssetRequest::where('request_type','0')->whereDate('created_at', date('Y-m-d'))->count();
		$totalWrl 	=	AssetRequest::where('request_type','1')->whereDate('created_at', date('Y-m-d'))->count();
		$totalPO 	=	AssetRequestNotification::where('pdate', date('Y-m-d'))->count();
	
		$store_pending = AssetRequest::select('*')
						->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
						->where('asset_request_notification.status', 1)
						->where('asset_request_notification.it_status', 0)
						->distinct()->count('asset_request.unique_no');
						
		$purchase_pending = AssetRequest::select('*')
						->leftJoin('asset_request_notification','asset_request_notification.request_id', '=', 'asset_request.id')
						->where('asset_request_notification.it_status', 2)
						->where('asset_request_notification.purchase_status', 0)
						->distinct()->count('asset_request.unique_no');
						
						
		$po_approval_pending =	AssetRequestNotification::where('asset_request_notification.purchase_status', 3)->where('dm_status', 0)->whereRaw("(asset_request_notification.company != 'na')")->count();
		
		
		
		$total_report  = DB::select("SELECT count(case when `asset_request`.`request_type` = '0' then 1 else null end) as pocount,count(case when `asset_request`.`request_type` = '1' then 1 else null end) as wocount,count(case when `asset_request_notification`.`dm_status` = 2 then 1 else null end) as total_rejected, sum(final_amt) as total_amount FROM `asset_request` left join asset_request_notification ON asset_request_notification.request_id = asset_request.id WHERE asset_request_notification.company != 'na' AND asset_request_notification.company != '-';");
		
		
		return view('admin.request.store-purchase-dashboard',compact('totalMrl','totalWrl','totalPO','store_pending','purchase_pending','po_approval_pending','total_report'));
	}
	
	public function maintenance_list(Request $request){
		$logged_id  = Auth::user()->id;		
		$role_id 	= Auth::user()->role_id;
		$department_type 	= Auth::user()->department_type;
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$user = User::with('user_details')->where('id', $logged_id)->first();
		$degination	= $user->user_details->degination;		
		
		
		
		$user_branch	= Auth::user()->user_branches[0]->branch_id;
		$location 		= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$rnumber 		= Input::get('rnumber');		
		$pwnumber 		= Input::get('pwnumber');		
		$fdate 			= Input::get('fdate');
        $tdate 			= Input::get('tdate');
        $branch_id  	= Input::get('branch_id');
        $po_status  	= Input::get('po_status');
        $department_id  = Input::get('department_id');
        $invoice_status = Input::get('invoice_status');
        $pname  		= Input::get('pname');
        $uname  		= Input::get('uname');
        $vendor  		= Input::get('vendor');
	
		
		$notification = AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','departments.name as dname','branches.name as bname')
							->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
							->leftjoin('category','category.id','asset_request.category')							
							->join('users','users.id', '=', 'asset_request_notification.sender_id')
							->join('departments','departments.id', '=', 'users.department_type')
							->join('branches','branches.id', '=', 'asset_request.branch_id')
							->where('asset_request_notification.is_deleted', '=', '0')
							->where('asset_request_notification.purchase_status', 7)
							->where('asset_request.is_deleted', '=', '0');
							
							if(Auth::user()->role_id != 29){
								$notification->where('branches.branch_location', '=', $location);
							}
							
							$notification->orderby('asset_request_notification.po_important', 'desc')
							->orderby('asset_request_notification.dm_status', 'asc')
							->orderby('asset_request_notification.id', 'desc')
							->groupby('asset_request.unique_no');
			
		
		if(!empty($rnumber)){
			$notification->whereRaw("(asset_request.unique_no = '".$rnumber."')");
		}
		
		if (!empty($pwnumber)) {
			$notification->whereRaw("CONCAT(asset_request_notification.po_no, '/', asset_request_notification.po_month) = ?", [$pwnumber]);
		}
		
		if(!empty($pname)){
			$notification->whereRaw("(asset_request.title LIKE '%".$pname."%')");	
		}
		
		if(!empty($uname)){
			$notification->whereRaw("(users.name LIKE '%".$uname."%')");			
		}
		
		if(!empty($department_id)){
			$notification->where('departments.id', '=', $department_id);
		}
			
		if(!empty($branch_id)){
			$notification->where('asset_request_notification.location', '=', $branch_id);
		}
		
		if(!empty($po_status)){
			$notification->where('asset_request_notification.dm_status', '=', $po_status);
		}
		
		
		if (!empty($fdate) && !empty($tdate)) {			
            $notification->where('asset_request_notification.pdate', '>=', $fdate)->where('asset_request_notification.pdate', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $notification->where('asset_request_notification.pdate', 'LIKE', '%' . $tdate . '%');
        }


		$notification = $notification->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $notification = $notification->get();
		
		return view('admin.request.maintenance-list', compact('notification','degination','pageNumber','notification','params'));
	}
	
	public function mrl_rollback(Request $request){		
		$mrl_no 			= $request->mrl_no;
		$title 				= $request->title;
		$type 				= $request->type;
		$it_status 		 	= $request->it_status;
		$purchase_status 	= $request->purchase_status;
		
		if($mrl_no!="" && $title!="" && $type!=""){
			$record = DB::table('asset_request')->where('id',$title)->first();
			 
			$data = [];
			
			if($type=="Inventory"){
				$data['it_status'] = $it_status;
				
				DB::table('asset_request_notification')->where('request_id',$record->id)->update($data);
			}else if($type=="Purchase"){
				$data['purchase_status'] = $purchase_status;
				
				DB::table('asset_request_notification')->where('request_id',$record->id)->update($data);
			}else if($type=="CFO"){				
				$data['po_approve'] = NULL;
				$data['dm_updated'] = NULL;
				$data['reason'] = NULL;
				$data['dm_status'] = 0;
				DB::table('asset_request_notification')->where('request_id',$record->id)->orWhere('parent_id',$record->id)->update($data);
			}			
			return redirect()->back()->with('success', 'Status update successfully!!');
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	
	public function getmrl_details(Request $request){
		$id  = $request->mrl_no; 
		
		
		$respnse = DB::table('asset_request')->select('asset_request.*','arn.status','arn.it_status','arn.purchase_status','arn.dm_status')->leftjoin('asset_request_notification as arn','arn.request_id','asset_request.id')->where('unique_no', $id)->get();
		
		
		if (!empty($respnse))
        {
            $res = '<option value="">-- Select Title --</option>';			
			foreach($respnse as $key){ 
				if($key->status==0){
					$status = 'Department Head Level';
				}else if($key->status==1 && ($key->it_status==0 || $key->it_status==3)){
					$status = 'Inventory Team';
				}else if($key->it_status==2){
					$status = 'Purchase Team';
				}else{
					$status = 'Deleted / Rejected';
				}
				
				$res .= "
					<option value='".$key->id."'>".$key->title." - ".$key->requirement."- ".$status."</option>					
					";
            }
			
			echo $res;
			exit();
        }
        else
        {
            echo $res = "<label>Error</label>";
            die();
        }
		
	}

	public function get_product_item(Request $request){
		
		$rid = explode("&&",$request->product_id);
		if(!empty($rid[0])){
			$request  = DB::table('asset_request')->where('id',$rid[0])->first();
	
			return response(['status' => true, 'qty' => $request->qty, 'requirement' => $request->requirement ], 200);
		}else{
			return response(['status' => false, 'qty' => '', 'requirement' => '' ], 200);
		}
	}
	
	public function request_transfer(Request $request){
		$request_id			=	$request->request_id;
		$assign_employee	=	$request->assign_employee;
		
		if($request_id!="" && $assign_employee!=""){
			$data = array(
				"is_transfer" 	=>	1,
				"emp_id" 		=>	$assign_employee,
			);				
			
			DB::table('asset_request')->where('id',$request_id)->update($data);
			
			return redirect()->back()->with('success', 'Transfer Successfully.');
		}else{
			return redirect()->back()->with('error', 'Required filed missing!!!');	
		}
	}
	
	public function requested_asset_by_hr(Request $request){
		$emp_code = $request->emp_code;
		
		
		$users	=	DB::table('users')
					->select('users.name','users.register_id','users.asset_requirement','departments.name as dname')
					->leftjoin('departments','departments.id','users.department_type')
					->where('asset_requirement','!=','-');
									
		if(!empty($emp_code)){
			$users->where('register_id',$emp_code);
		}
									
					
		$users = $users->get();
		return view('admin.request.requested-assets-by-hr',compact('users'));
	}
	
	public function inventory_valuation(Request $request){
		$fmonth    = Input::get('fmonth');
		$branch_id = Input::get('branch_id');
		$search    = Input::get('search');
		$asset_type 	  = $request->asset_type;
		
		$record	=	array();
		if(!empty($search)){
			$record =	DB::table('asset_request')
						->select('po_history.*','branches.name as bname','branches.branch_location','arn.pdate','asset_request.created_at','asset_request.unique_no','asset_request.request_type','products.name as pname','a.name as cname','b.name as sname','products.pcode')
						->leftjoin('asset_request_notification as arn','arn.request_id','asset_request.id')
						->leftjoin('po_history','po_history.asset_id','asset_request.id')
						->leftjoin('products','products.id','asset_request.product_id')
						->leftjoin('category as a','a.id','products.cat_id')
						->leftjoin('category as b','b.id','products.sub_cat_id')
						->leftjoin('branches','branches.id','asset_request.branch_id')
						->whereRaw("(arn.company != 'na' AND arn.company != '-')");
			
			if(!empty($fmonth)){
				$mt	= date('m',strtotime($fmonth));
				$yr	= date('Y',strtotime($fmonth));
				
				$record->whereYear('asset_request.created_at', '=', $yr)->whereMonth('asset_request.created_at', '=', $mt);
			}
			
			if($asset_type=='Asset'){
				$record->where('asset_request.type',$asset_type);
			}else if($asset_type=='Non Asset'){
				$record->where('asset_request.type',$asset_type);
			}
			
			
			
			if(!empty($branch_id)){
				$record->where('asset_request.branch_id',$branch_id);
			}
			
			$record	=	$record->get();
		}
		
		return view('admin.request.reports.inventory_valuation',compact('record'));
	}
		
	public function location_request_list(Request $request){			
		$fmonth 	  = $request->fmonth;
		$branch_id 	  = $request->branch_id;
		
		
		if(!empty($fmonth)){
			$mt	= date('m',strtotime($fmonth));
			$yr	= date('Y',strtotime($fmonth));
		}else{
			$mt	= date('m');
			$yr = date('Y');
		}
		
		
		$record =	DB::table('asset_request')
					->select('po_history.*','branches.name as bname','branches.branch_location','arn.pdate','asset_request.created_at','asset_request.unique_no','asset_request.request_type','products.name as pname','a.name as cname','b.name as sname','products.pcode','buyer.name as vendro_name','users.name as uname','arn.po_no','arn.po_month','arn.po_location')
					->leftjoin('asset_request_notification as arn','arn.request_id','asset_request.id')
					->leftjoin('po_history','po_history.asset_id','asset_request.id')
					->leftjoin('products','products.id','po_history.product_id')
					->leftjoin('category as a','a.id','products.cat_id')
					->leftjoin('category as b','b.id','products.sub_cat_id')
					->leftjoin('branches','branches.id','asset_request.branch_id')
					->leftjoin('buyer','buyer.id','arn.company')
					->leftjoin('users','users.id','asset_request.emp_id')
					->whereRaw("(arn.company != 'na' OR arn.company != '-')") 
					->whereYear('asset_request.created_at', '=', $yr)
					->whereMonth('asset_request.created_at', '=', $mt);
	
		
		
		if(!empty($branch_id)){
			$record->where('asset_request.branch_id',$branch_id);
		}
		
		$record	=	$record->get();
		return view('admin.request.reports.location_request_list',compact('record'));
	}
	
	
	//Purchase-Inventory Masater Data
	public function monthly_master_data(Request $request){
		$fmonth 	  = $request->fmonth;
		$branch_id 	  = $request->branch_id;
		$location_id 	  = $request->location_id;
		
		if(empty($location_id)){		
			$location 		= Auth::user()->user_branches[0]->branch['branch_location'];
		}else{
			$location = $location_id;
		}
		
		
		if(!empty($fmonth)){
			$mt	= date('m',strtotime($fmonth));
			$yr	= date('Y',strtotime($fmonth));
		}else{
			$mt	= date('m');
			$yr = date('Y');
		}
		
		
		$record = DB::table('asset_request')->select('asset_request.created_at','asset_request.unique_no','users.name','arn.po_no','arn.po_month','arn.po_location','arn.final_amt as po_amt','a.name as po_vendor','branches.name as branch','po_invoice.date_of_invoice','po_invoice.invoice_no','po_invoice.amount as Invoive_Amt','po_invoice.remark','a.name as invoice_vendor','asset_request.request_type','po_invoice.type','products.name as pname','c.name as cname','d.name as sname','branches.branch_location','po_history.*','asset_request.requirement','asset_request.emp_grn','branches.nickname','branches.short_name')
		->leftjoin('asset_request_notification as arn','arn.request_id','asset_request.id')
		->leftjoin('po_history','po_history.asset_id','asset_request.id')
		->leftjoin('buyer as a','a.id','arn.company')
		->leftjoin('users','users.id','asset_request.user_id')
		->leftjoin('branches','branches.id','asset_request.branch_id')
		->leftjoin('po_invoice','po_invoice.request_id','asset_request.id')
		->leftjoin('buyer as b','b.id','po_invoice.vendor_id')
		->leftjoin('products','products.id','asset_request.product_id')
		->leftjoin('category as c','c.id','products.cat_id')
		->leftjoin('category as d','d.id','products.sub_cat_id')
		->where('arn.status',1)
		->where('branches.branch_location', '=', $location)
		->whereYear('asset_request.created_at', '=', $yr)
		->whereMonth('asset_request.created_at', '=', $mt);
		
		
		if(!empty($branch_id)){
			$record->where('asset_request.branch_id',$branch_id);
		}
		
		$record	= $record->get();
		
		return view('admin.request.reports.monthly_master_data',compact('record'));
	}


	public function product_return(Request $request){	
		$request_id 	=	$request->request_id;
		
		
		if(!empty($request_id)){				
			$grnData  = array(
				'inventory_status' 	=> 	2
			);
			DB::table('asset_request')->where('id', $request_id)->update($grnData);
			
			return response(['status' => true, 'message' => 'Product Return Request send successfully'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
	}
	
	public function return_product_list(Request $request){
		$employee = DB::table('users')->where('status',1)->get();
		
		$emp_id =	$request->emp_id;
		$rstatus =	$request->rstatus;
		
		$record = DB::table('asset_request')
					->select('asset_request.*','users.name as uname','products.name as pname','branches.name as bname')
					->leftjoin('users','users.id','asset_request.emp_id')
					->leftjoin('products','products.id','asset_request.product_id')
					->leftjoin('branches','branches.id','asset_request.branch_id');
		
		if(!empty($emp_id)){
			$record->where('asset_request.emp_id',$emp_id);
		}else{
			if(!empty($rstatus)){
				$record->where('inventory_status',$rstatus);
			}else{
				$record->where('inventory_status',2);
			}
		}
		
		
		$record = $record->get();
		return view('admin.request.return-product-list',compact('record','employee','emp_id'));
	}
	
	public function inventory_product_accept(Request $request){	
		$request_id 	=	$request->request_id;
		$product_id 	=	$request->product_id;
		
		if(!empty($request_id) && !empty($product_id)){
			$maxValue 	= DB::select("SELECT GREATEST(MAX(emp_grn), MAX(inventory_grn)) AS max_value FROM asset_request;");					
			$grn_no 	= $maxValue[0]->max_value + 1;
				
			$grnData  = array(
				'inventory_status' 	=> 1,
				'inventory_grn' 	=> $grn_no,
			);
			
			DB::table('asset_request')->where('id', $request_id)->update($grnData);

			//Transfer 
			$rdata  = array(
				'status' 		=> 'Return',
			);
			
			DB::table('transfer')->where('request_id', $request_id)->where('product_id', $product_id)->update($rdata);
			
				
						
			return response(['status' => true, 'message' => 'Product Accept By Requisitor'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
	}
	
	public function mrl_import(Request $request){
		$logged_id       = Auth::user()->id;
		$department_type = Auth::user()->department_type;
		$name       	 = Auth::user()->name;
		$register_id   	 = Auth::user()->register_id ;
		$role       	 = Auth::user()->role_id;
				
				
		$mrl_no			=	$request->mrl_no;
		$file		= $request->file('product_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validator = Validator::make($request->all(), [
			   'product_file' => 'required|mimes:xlsx,xls,csv',
			]);
			if ($validator->fails()){
				$messages = $validator->errors(); 
				return response(['status' => false, 'message' => $messages->first('product_file')], 200);
			}
		}
		
		
		$path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		$ngenerate = AssetRequest::select('asset_request.user_id','asset_request.branch_id','asset_request.remark','asset_request.request_type','asset_request.emp_id','asset_request.material_category','asset_request.user_id','asset_request.status','arn.sender_id','arn.request_id','arn.receiver_id','arn.receiver_id','arn.status as dh_status','arn.dh_approved','arn.message','arn.it_approve')
							->leftJoin('asset_request_notification as arn', 'arn.request_id', '=', 'asset_request.id')
							->where('unique_no', $mrl_no)
							->first();  
		
		$data = array();
		
		foreach ($import[0] as $key => $value) {
			$value[0]=trim($value[0]);
			$value[1]=trim($value[1]);
			$value[2]=trim($value[2]);
			$value[3]=trim($value[3]);

			$product_title = $value[0];
			$product_des   = $value[1];
			$product_id    = $value[2];
			$type_demand   = $value[3];
			$quantity	   = $value[4];
			$uom	       = $value[5];
			
			
			$productD			=	DB::table('products')->where('id',$product_id)->first();				
			$category 			= 	$productD->cat_id;
			$scategory			=	$productD->sub_cat_id;
			$productID			=	$productD->id;
			
			$title				=	$product_title;
			$requirement		=	$product_des;
			$qty				=	$quantity;
			$type				=	$type_demand;
			
			
			$branch_id			=	$ngenerate->branch_id;
			$remark				=	$ngenerate->remark;
			$request_type		=  	$ngenerate->request_type;
			
			$emp_id				=	$ngenerate->emp_id;
			$material_category	=	$ngenerate->material_category;
			$user_id			=	$ngenerate->user_id;
			$status				=	$ngenerate->status;
			$unique_no			=	$mrl_no;
			
			//Insert 
			$record = array();
			$record['category']			 = $category;
			$record['scategory']   		 = $scategory;				
			$record['product_id']		 = $productID;
			$record['branch_id'] 		 = $branch_id;
			$record['type']				 = $type;
			$record['title']			 = $title;
			$record['requirement']		 = $requirement;
			$record['qty'] 				 = $qty;				
			$record['remark']			 = $remark;
			$record['request_type']		 = $request_type;
			$record['emp_id']			 = $emp_id;
			$record['material_category'] = $material_category;
			$record['image']			 = '-';			
			$record['user_id']			 = $user_id;				
			$record['unique_no']		 = $unique_no;
			$record['status']			 = $status;
			$record['uom']			 	= $uom;
			$record['created_at']		 = date('Y-m-d H:i:s');
			$record['updated_at']		 = date('Y-m-d H:i:s');
			
			
			$saveDataID = AssetRequest::insertGetId($record);
			
		
			$terms = "1. Taxes: All Taxes Inclusive on the above rate.
2. Description: Goods should be come as per the Specification & Inclusive of All Accessories with Warranty.
3. Delivery Time & Date : In 2 Working days after PO.
4. Validity : Validity of the purchase order is 30 Days.
5. Jurisdiction & Competence : All disputes will be subject to Jodhpur jurisdiction only.
6. Warranty : As per Manufacturer Terms & Condition.
7. Payment : Payment will be settled within 7 Days of Invoice receipt date.";
				

			$nData = array(
				'sender_id' 	=> $ngenerate->sender_id, 
				'request_id' 	=> $saveDataID, 
				'receiver_id'	=> $ngenerate->receiver_id, 
				'terms'			=> $terms, 
				'message' 		=> $ngenerate->message,
				'status' 		=> $ngenerate->dh_status,
				'dh_approved' 	=> $ngenerate->dh_approved,
				'it_approve' 	=> $ngenerate->it_approve,
				'it_updated'	=> date('Y-m-d H:i:s')
			);
			AssetRequestNotification::create($nData);
		}		
		return response(['status' => true, 'message' => 'Request import successfully!!'], 200);
			
	}
	
	
	public function auto_reject_po(Request $request){
		$udata = [
			"reason"      => 'The Purchase Order has been cancelled as it was pending approval at the DM level for over 30 days without any action',
			"dm_status"   => 2,
			"dm_updated"  => date('Y-m-d H:i:s'),
			"pt_approve"  => 8799,
		];

		DB::table('asset_request_notification')
			->where('purchase_status', 3)
			->where('dm_status', 0)
			->where('company', '!=', '-')
			->where('pt_updated', '<=', now()->subDays(30))
			->update($udata);
			
		return response(['status' => true, 'message' => '30 Days Before Purchase Order Rejected Successfully!!'], 200);
	}
	
	
	public function poedit(Request $request,$id){		
		$record = 	AssetRequestNotification::select('asset_request_notification.*','asset_request.unique_no','asset_request.request_type','asset_request.remark','users.name as dhname','material_category.name as material_category')
					->leftJoin('asset_request','asset_request.id','asset_request_notification.request_id')
					->leftJoin('users','users.id','asset_request.remark')
					->leftJoin('buyer','buyer.id','asset_request_notification.company')
					->leftJoin('material_category','material_category.id','asset_request.material_category')
					->where('asset_request_notification.request_id', $id)->first();
		
		
		$buyer	   =	Buyer::where('is_deleted','0')->where('status','Active')->orderby('name')->get();
		$branch 	=	Branch::where('id',$record->location)->first();
		$pName 		=	User::where('id',$record->pt_approve)->first();
		$hodName 	=	User::where('id',$record->dh_approved)->first();
		$cfoName 	=	User::where('id',$record->po_approve)->first();
		
		$buyer_account 	=	DB::table('buyer_bank_details')->where('buyer_id',$record->company)->first();
		
		return view('admin.request.edit_po', compact('record','buyer','branch','pName','hodName','cfoName','buyer_account','id'));
	}
	
	
	
	public function po_request_edit(Request $request){
		DB::table('po_history')->where('asset_id',$request->request_id)->update(['status' => 'inactive']);
		
		
		$record =  DB::table('asset_request_notification as arn')
					->select('arn.*')
					->leftjoin('asset_request','asset_request.id','arn.request_id')
					->where('arn.request_id',$request->request_id)
					->first();
				
		$request_id 	= $request->request_id;
		$company		= $request->company;
		$address 		= $request->address;
		$gstin 			= $request->gstin;
		$phone 			= $request->phone;
		$email 			= $request->email;
		
		$pdate 			= $record->pdate;
		$po_location 	= $record->po_location;
		$po_no 			= $record->po_no;
		$po_month 		= $record->po_month;
		$location 		= $record->location;
		
		
		$narration 		= $request->narration;
		$advance 		= $request->advance;
		$terms 			= $request->terms;
		$approved 		= $record->approved;
		$finalAmt 		= $request->finalAmt;
		$advanceAmt 	= $request->advanceAmt;
		$requiId 		= $request->requiId;
		
		
		$poImportant	= $record->po_important;
		$po_address		= $record->po_address;
		$po_gst			= $record->po_gst;
		$buyer_account	= $record->account_id;
		
		
		
		
		if($request_id!="" && $company!=""){	
			$data = array(
				'po_address'		=> $po_address,
				'po_gst'			=> $po_gst,
				'company'			=> $company,
				'address'			=> $address,
				'gstin'				=> $gstin,
				'phone'				=> $phone,
				'email'				=> $email,
				'pdate'				=> $pdate,
				'po_location'		=> $po_location,
				'po_no'				=> $po_no,
				'po_month'			=> $po_month,
				'location'			=> $location,
				'narration'			=> $narration,
				'advance'			=> $advance,
				'terms'				=> $terms,
				'approved'			=> $approved,				
				'final_amt'			=> $finalAmt,				
				'advance_amt'		=> $advanceAmt,				
				'po_important'		=> $poImportant,				
				'account_id'		=> $buyer_account??0,				
			);
			
			
			if(!empty(Input::file('quotation_one'))){
				$data['quotation_one'] = $this->uploadProfileImage2(Input::file('quotation_one'));
			}
			
			if(!empty(Input::file('quotation_two'))){
				$data['quotation_two'] = $this->uploadProfileImage3(Input::file('quotation_two'));
			}
			
			if(!empty(Input::file('quotation_three'))){
				$data['quotation_three'] = $this->uploadProfileImage4(Input::file('quotation_three'));
			}
			
			DB::table('asset_request_notification')->where('request_id', $request_id)->update($data);
		
		
		
			for($i = 0; $i < count($request->item); $i++){
				$rid = explode("&&",$requiId[$i]);
				
				if(!empty($request->item[$i])){	

					$record = array();
					if(isset($request_id)){
						$record['asset_id']=$request_id;
					}
					
					if(isset($request->item[$i])){
						$record['item']=$request->item[$i];
					}
					
					if(isset($request->requiId[$i])){
						$record['po_request_id']=$rid[0];
					}
					
					if(isset($rid[1]) && !empty($rid[1])){
						$record['product_id']=$rid[1]??0;
					}
					
					
					if(isset($request->uom[$i])){
						$record['uom']=$request->uom[$i];
					}
					
					if(isset($request->qty[$i])){
						$record['qty']=$request->qty[$i];
					}
					
					if(isset($request->rate[$i])){
						$record['rate']=$request->rate[$i];
					}
					
					if(isset($request->amount[$i])){
						$record['amount']=$request->amount[$i];
					}
					
					if(isset($request->gstrate[$i])){
						$record['gst_rate']=$request->gstrate[$i];
					}
					
					if(isset($request->gstamt[$i])){
						$record['gst_amt']=$request->gstamt[$i];
					}
					
					if(isset($request->totalamt[$i])){
						$record['total']=$request->totalamt[$i];
					}
													
					DB::table('po_history')->insert($record);					
				}				
			}			
			return redirect()->back()->with('success', 'Updated Successfully');
		}else{
			return redirect()->back()->with('error', 'Required Filed Missing!!');
		}		
	}
}
 
 