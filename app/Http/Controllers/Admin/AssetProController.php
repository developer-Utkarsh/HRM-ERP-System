<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Asset;
use App\AssignAsset;
use App\User;
use App\Product;
use App\AssetRequest;
use App\Transfer;
use App\Inventory;
use Input;
use Validator;
use DataTables;
use DB;
use Auth;
use App\NewTask;


class AssetProController extends Controller
{
	
    
    public function index()
    {   
		$logged_id  = Auth::user()->id;
		//$pending_assets = AssignAsset::select('assign_asset.*','products.name')->leftJoin('transfer', 'transfer.id', '=', 'assign_asset.asset_id')->leftJoin('products','products.id','transfer.product_id')->whereRaw("emp_id = $logged_id and is_accepted ='0'")->get();
		
		$pending_assets = AssignAsset::select('assign_asset.*','products.name','assign_asset.asset_id as pid')->leftJoin('products','products.id','assign_asset.asset_id')->whereRaw("emp_id = $logged_id and is_accepted ='0'")->get();
		
        return view('admin.asset_pro.index',compact('pending_assets'));		
    } 
	
	public function assetDetail(Request $request){ 
		$logged_id  = Auth::user()->id;
		$name          = $request->name;
	    $responseArray = array();
		$whereCond   = "(is_accepted = '0' or is_accepted = '2') and  (emp_id = $logged_id OR assigned_by=$logged_id) ";
		if(!empty($name)){
			$whereCond .= " AND (products.name LIKE '%$name%')";
		}

		//$responseArray = AssignAsset::select('assign_asset.id','assign_asset.asset_id','products.name',DB::Raw("SUM(CASE When assign_asset.emp_id=$logged_id and assign_asset.is_accepted='2' Then assign_asset.qty Else 0 End ) as qty"),DB::Raw("SUM(CASE When assign_asset.assigned_by=$logged_id and (assign_asset.is_accepted='0' or assign_asset.is_accepted='2') Then assign_asset.qty Else 0 End ) as transfer_qty"))->leftJoin('asset', 'asset.id', '=', 'assign_asset.asset_id')->leftJoin('products', 'products.id', '=', 'asset.product_id')->whereRaw($whereCond)->orderBy('assign_asset.id','ASC')->groupBy('assign_asset.asset_id')->get();
		
		if($logged_id==1069){
			$responseArray = DB::table('transfer')
								->select(DB::raw("transfer.*,COALESCE(SUM(transfer.qty),0) as qty,products.name,products.id as pid"),DB::Raw("(SELECT COALESCE(SUM(assign_asset.qty),0) from assign_asset where assign_asset.assigned_by=$logged_id and assign_asset.asset_id=products.id and (assign_asset.is_accepted='0' or assign_asset.is_accepted='2')) as transfer_qty"),DB::Raw("(SELECT COALESCE(SUM(assign_asset.qty),0) from assign_asset where assign_asset.emp_id=$logged_id and assign_asset.asset_id=products.id and assign_asset.is_accepted='1') as accept_qty"))
								->leftjoin('products', 'products.id', '=', 'transfer.product_id')
								//->leftjoin('assign_asset', 'assign_asset.asset_id', '=', 'products.id')
								->where('transfer.user_id',$logged_id)
								->groupby('transfer.product_id')
								->get();
			// echo "<pre>"; print_r($responseArray); die;
		}else{
			$responseArray = AssignAsset::select('assign_asset.id','assign_asset.asset_id as pid','products.name',DB::Raw("SUM(CASE When assign_asset.emp_id=$logged_id and assign_asset.is_accepted='2' Then assign_asset.qty Else 0 End ) as qty"),DB::Raw("SUM(CASE When assign_asset.assigned_by=$logged_id and (assign_asset.is_accepted='0' or assign_asset.is_accepted='2') Then assign_asset.qty Else 0 End ) as transfer_qty"),DB::Raw("0 as accept_qty"))->leftJoin('products', 'products.id', '=', 'assign_asset.asset_id')->whereRaw($whereCond)->orderBy('assign_asset.id','ASC')->groupBy('assign_asset.asset_id')->get();
			// echo "<pre>"; print_r($responseArray); die;
		}
		
		return DataTables::of($responseArray)->make(true);
	}
	
	public function addTransferAsset($id){
		$logged_id  = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		
		$user 	= User::where('status','1')->where('department_type',2)->get();
		return view('admin.asset_pro.add-transfer-asset', compact(['id','user']));
	}
	
	public function storeTransferAsset(Request $request, $id){
		$logged_id  = Auth::user()->id;
		$rqData = [
			'emp_id'   => 'required',
			'qty'      => 'required|numeric|min:0|not_in:0',
		  ];
		
		$validatedData = $request->validate($rqData);
		
		$logged_id  = Auth::user()->id;
		
		$remaining_product = 0;
		// $check_asset_qty = Asset::where('id', $id)->sum('qty');
		$check_asset_qty = DB::table('assign_asset')->where('asset_id', $id)->where('emp_id', $logged_id)->where('is_accepted', '2')->get()->sum("qty");
		
		$check_qty_from_rem_transfer = DB::table('assign_asset')->where('asset_id', $id)->where('assigned_by', $logged_id)->whereRaw("(is_accepted='0' or is_accepted='2')")->get()->sum("qty");

		if(!empty($check_asset_qty) || !empty($check_qty_from_rem_transfer)){
			$remaining_product = $check_asset_qty - $check_qty_from_rem_transfer;
		}
		
		
		if(!empty($check_asset_qty) || $logged_id==1069){
			if($request->qty <= $remaining_product || $logged_id==1069){
				$assign_asset_result = AssignAsset::insertGetId(['is_parent' => 1,'asset_id' => $id, 'assigned_by' => $logged_id, 'emp_id' => $request->emp_id, 'qty' => $request->qty, 'remark' => $request->remark]);

				if(!empty($assign_asset_result)){
					return redirect()->route('admin.asset_pro.index')->with('success', 'Asset Added Successfully');
				}
				else {
					return back()->with('error', "Something is wrong");
				}
			}
			else{
				if(!empty($remaining_product)){
					return back()->with('error', "Only $remaining_product Quantity Available");
				}
				else{
					return back()->with('error', "Asset Out Of Stock");
				}
			}
		}
		else {
			return back()->with('error', "Asset Not Found");
		} 
	}
	
	public function transferAssetHistory($id){
		$logged_id  = Auth::user()->id;
		
		/*
		$asset_history = Asset::select('asset.*','products.name','a.name as assigned_to','a.id as assigned_to_id','b.name as assigned_by_name','assign_asset.qty as assign_asset_qty','assign_asset.created_at as assign_asset_created_at','assign_asset.is_accepted')
			->join('products', 'products.id', '=', 'asset.product_id')
			->leftJoin('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
			->join('users as a', 'a.id', '=', 'assign_asset.emp_id')
			->join('users as b', 'b.id', '=', 'assign_asset.assigned_by')
			->where('asset.id',$id);
		if($logged_id!=1069){
			$asset_history->where('assign_asset.assigned_by',$logged_id);
		}
		$asset_history = $asset_history->where('assign_asset.emp_id','!=',$logged_id)->get();
		*/
		
		
		$asset_history = AssignAsset::select('assign_asset.*','a.name as assigned_to','a.id as assigned_to_id','b.name as assigned_by_name','assign_asset.qty as assign_asset_qty','assign_asset.created_at as assign_asset_created_at','assign_asset.is_accepted','products.name')
				->join('products', 'products.id', '=', 'assign_asset.asset_id')
			->join('users as a', 'a.id', '=', 'assign_asset.emp_id')
			->join('users as b', 'b.id', '=', 'assign_asset.assigned_by')
			->where('assign_asset.asset_id',$id);
			if($logged_id!=1069){
				$asset_history->where('assign_asset.emp_id','!=',$logged_id);
			}
			$asset_history = $asset_history->get();
		
		return view('admin.asset_pro.transfer-asset', compact('id','asset_history'));
	}
	
	public function employeeAsset(){ 
		$eID   		= Input::get('eID');
		$logged_id  = Auth::user()->id;		 
		$logged_role_id  = Auth::user()->role_id;
		if($logged_role_id == 21){
			$users = NewTask::getEmployeeByLogID($logged_id,'location_wise');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$emp_asset_list  = Asset::select('users.name','assign_asset.id','assign_asset.emp_id','assign_asset.assigned_by')
								->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
								->join('users', 'assign_asset.emp_id', '=', 'users.id')
								->where('assign_asset.assigned_by',$logged_id)
								->groupBy('assign_asset.emp_id');
										
		$emp_asset_list = $emp_asset_list->get();
		// echo '<pre>'; print_r($emp_asset_list);die;
        return view('admin.asset_pro.employee_asset', compact(['emp_asset_list','users'])); 
	}
	
	public function edit($emp_id)
    {   
		$logged_id  = Auth::user()->id;
        $assigned_asset = DB::table('asset')
							->select('products.name','assign_asset.id','assign_asset.emp_id')
							->join('products', 'products.id', '=', 'asset.product_id')
							->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
							->where('assign_asset.emp_id', $emp_id)
							->whereRAW("(assign_asset.is_accepted = '0' OR assign_asset.is_accepted = '2')  and is_parent =1")
							->where('assign_asset.assigned_by', $logged_id)
							->get();
		//echo '<pre>'; print_r($assigned_asset);die;					
        return view('admin.asset_pro.assign_asset_edit', compact('assigned_asset'));
    }
	
	public function updateAssetStatus($id,$emp_id,$action){
		
		if(!empty($id)){
			$is_accepted = "0";
			$return_date = NULL;
			if($action=='employee_accept'){
				$is_accepted = "2";
			}
			else if($action=='admin_accept'){
				$is_accepted = "1";
				$return_date = date('Y-m-d H:i:s');
			}
			
			if($is_accepted=="0"){
				return back()->with('error', 'Something Went Wrong !');
			}
			else{
				$asset_res = AssignAsset::where('id', $id)->update(['is_accepted' => $is_accepted,'return_date' => $return_date]);
				if($asset_res) {
					return back()->with('success', 'Status Update Successfully');
				} else {
					return back()->with('error', 'Something Went Wrong !');
				} 
			}
			
		}
		else{
			return back()->with('error', 'Asset ID Not Found !');
		}
	}
	
	public function remaining_asset_employee(Request $request){
		//echo '<pre>'; print_r($request->post());die;
		$logged_id  = Auth::user()->id;
		if(!empty($request->asset_id) && !empty($request->assigned_to_id)){
			$assigned_to_id   = $request->assigned_to_id;
			$asset_id   = $request->asset_id;
			
			$whereCond   = "(is_accepted = '0' or is_accepted = '2') and  (emp_id = $assigned_to_id OR assigned_by=$assigned_to_id) and asset_id = $asset_id";
			 

			$responseArray = AssignAsset::select('assign_asset.id','assign_asset.asset_id',DB::Raw("SUM(CASE When assign_asset.emp_id=$assigned_to_id and assign_asset.is_accepted='2' Then assign_asset.qty Else 0 End ) as qty"),DB::Raw("SUM(CASE When assign_asset.assigned_by=$assigned_to_id and (assign_asset.is_accepted='0' or assign_asset.is_accepted='2') Then assign_asset.qty Else 0 End ) as transfer_qty"))->whereRaw($whereCond)->orderBy('assign_asset.id','ASC')->groupBy('assign_asset.asset_id')->first();
			if(!empty($responseArray)){
				// echo "<pre>"; print_R($responseArray->qty); die;
				$total_remaining = $responseArray->qty - $responseArray->transfer_qty;
				return response(['status' => true, 'total' => $total_remaining], 200);
			}else{          
				return response(['status' => false, 'total' => 0], 200);
			}
		}
		else{
			return response(['status' => false, 'message' => 'Asset ID Not Found'], 200);
		}
		
	}
}
