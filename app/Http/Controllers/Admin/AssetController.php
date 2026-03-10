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


class AssetController extends Controller
{
	
    
    public function index()
    {   
        return view('admin.asset.index'); //, compact('asset_list')
    }
	
	public function create(Request $request){
		
		$asset = Inventory::select(DB::raw("products.id,products.name,sum(inventory.qty) as qty, (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) as transfer_qty"))
					->leftJoin('products','products.id','=','inventory.product_id')		
					->where('inventory.is_deleted', '0')
					->whereRaw("(qty > (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) OR (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) IS NULL )")
					->where('products.type', 2)
					->groupBy('inventory.product_id')
					->get();
					
		$employee = User::where('status',1)->where('role_id','!=',29)->where('role_id','!=',2)->get();
		
		return view('admin.asset.add', compact('asset','employee'));
	}
	
	
	
	public function edit($emp_id)
    {   
        $assigned_asset = DB::table('asset')
							->select('products.name','assign_asset.id','assign_asset.emp_id')
							->join('products', 'products.id', '=', 'asset.product_id')
							->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
							->where('assign_asset.emp_id', $emp_id)
							->whereRAW("(assign_asset.is_accepted = '0' OR assign_asset.is_accepted = '2') and is_parent =0")
							->get();
		//echo '<pre>'; print_r($assigned_asset);die;					
        return view('admin.asset.assign_asset_edit', compact('assigned_asset'));
    }

    public function store(Request $request)
    {
		$validatedData = $request->validate([
			'name'       => 'required',
			'qty'       => 'required|numeric|min:0|not_in:0',
        ]);
		$product_id 	= $request->name;
		$product_qty 	= $request->qty;
		$serial_no 		= $request->serial_no;
		$invoice_date 	= $request->invoice_date;
		$invoice_img 	= $request->invoice_img;
		$product_img 	= $request->product_img;
		$remark		 	= $request->remark;
		
		$check_inventory_stock = Inventory::select(DB::raw("products.id,products.name,sum(inventory.qty) as qty, (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) as transfer_qty"))
					->leftJoin('products','products.id','=','inventory.product_id')		
					->where('inventory.is_deleted', '0')
					->where('inventory.product_id', $product_id)
					->whereRaw("(qty > (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) OR (SELECT SUM(qty) FROM transfer WHERE transfer_from = '0' AND status != 'Reject' AND status != 'Raise a issue' AND product_id = products.id) IS NULL )")
					->groupBy('inventory.product_id')
					->first();
		
		$in_stock = 0;
		if(!empty($check_inventory_stock)){
			$in_stock = $check_inventory_stock->qty - $check_inventory_stock->transfer_qty;
		}
	
		if($in_stock >= $product_qty){
			$check_asset_exist = Asset::where('product_id', $product_id)->first();
			if(!empty($check_asset_exist)){
				$total_qty = $check_asset_exist->qty + $product_qty;
				$asset_res = $check_asset_exist->update(['product_id' => $product_id, 'qty' => $total_qty]);
			
				$this->maintain_history(Auth::user()->id, 'asset', $check_asset_exist->id, 'update_asset', json_encode(['product_id' => $product_id, 'qty' => $total_qty]));
			}
			else{
				$inputs = array('product_id' => $product_id, 'qty' => $product_qty); 
				$asset = Asset::create($inputs); 
				$asset_res  = $asset->save();
				
				$this->maintain_history(Auth::user()->id, 'asset', $asset->id, 'add_asset', json_encode($inputs));
			}
			if($asset_res) {
				
				Transfer::insertGetId(['product_id' => $product_id, 'transfer_from' => '0', 'transfer_to' => '0', 'qty' => $product_qty, 'status' => 'Accept', 'is_type' => 'Asset']);

				return redirect()->route('admin.asset.index')->with('success', 'Asset Added Successfully');
			} else {
				return redirect()->route('admin.asset.index')->with('error', 'Something Went Wrong !');
			} 
		}
		else {
			return back()->with('error', "Only $in_stock Inventory Quantity Available");
		} 
    }
	
	public function update(Request $request, $id)
    {
        /* $validatedData = $request->validate([
			'name'       => 'required',
			'contact_no' => 'required',
			'gst_no'     => 'required',
			'address'    => 'required',
        ]);
		
        $buyer = Buyer::where('id', $id)->first();

        $inputs = $request->only('name','contact_no','gst_no','address');       

        if ($buyer->update($inputs)) {
            return redirect()->route('admin.buyer.index')->with('success', 'Buyer Updated Successfully');
        } else {
            return redirect()->route('admin.buyer.index')->with('error', 'Something Went Wrong !');
        } */
    }
	
    public function destroy($id)
    {   
      /*   $buyer  = Buyer::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($buyer->update($inputs)) {
            return redirect()->back()->with('success', 'Byuer Deleted Successfully');
        } else {
            return redirect()->route('admin.buyer.index')->with('error', 'Something Went Wrong !');
        } */
    }
	
	public function show(Request $request){ 
		$search = $request->get('term');
        $whereCond = array();
        if(!empty($search)) $whereCond[] = 'name LIKE \''.$search.'%\'';
        $whereCondStr = '';
        if(is_array($whereCond) && count($whereCond)>0) $whereCondStr = ' WHERE '.implode(' AND ', $whereCond);
        $result = DB::select("select name AS label FROM asset ".$whereCondStr." group by name");
        return response()->json($result);
	}
	
	public function assetDetail(Request $request){ 
		$name          = $request->name;
	    $responseArray = array();
		/*$employeeArray = array();
		
		$employee_list = User::select('id','name')->where([['status', '=', 1]], ['role_id', '!=', 29])->get();
				
		if(count($employee_list) > 0){
			foreach($employee_list as $key1=>$employee_list_value){
				$employeeArray[$key1]['emp_id']  = isset($employee_list_value->id)?$employee_list_value->id:'';
				$employeeArray[$key1]['emp_name'] = isset($employee_list_value->name)?$employee_list_value->name:'';
			}
		}
				
		$asset_list    = Asset::where('is_deleted', '0')->orderBy('id', 'desc');
		if(!empty($name)){
			$asset_list->whereRaw("name LIKE '%$name%'");
		}
		$asset_list = $asset_list->get();
		if(count($asset_list) > 0){
			foreach($asset_list as $key=>$valAtt){
				$responseArray[$key]['id']  = isset($valAtt->id)?$valAtt->id:'';
				$responseArray[$key]['name'] = isset($valAtt->name)?$valAtt->name:'';
				$responseArray[$key]['employees'] = $employeeArray;
			}
		}*/
		$whereCond   = '1=1 ';
		if(!empty($name)){
			$whereCond .= " AND (products.name LIKE '%$name%')";
		}

		$responseArray = Asset::select('asset.*','products.name', DB::Raw("(SELECT SUM(qty) FROM assign_asset WHERE asset_id = asset.id AND (is_accepted = '0' OR is_accepted = '2') and is_parent = 0) as transfer_qty"))->leftJoin('products', 'products.id', '=', 'asset.product_id')->whereRaw($whereCond)->get();
		
		
		// $responseArray = Asset::select('asset.*','products.name',DB::raw('SUM(qty) AS opening_qty'), DB::Raw("(SELECT SUM(qty) FROM assign_asset WHERE asset_id = asset.id AND (is_accepted = '0' OR is_accepted = '2') and is_parent = 0) as transfer_qty"))
		// ->leftJoin('products', 'products.id', '=', 'asset.product_id')
		// ->whereRaw($whereCond)
		// ->groupby('asset.product_id')
		// ->get();
		
		return DataTables::of($responseArray)->make(true);
	}

	public function transferAssetHistory($id){
		$asset_history = Asset::select('asset.*','products.name','users.name as user_name','assign_asset.qty as assign_asset_qty','assign_asset.created_at as assign_asset_created_at')->join('products', 'products.id', '=', 'asset.product_id')->leftJoin('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')->join('users', 'users.id', '=', 'assign_asset.emp_id')->where('asset.id',$id)->get();
		
		return view('admin.asset.transfer-asset', compact('id','asset_history'));
	}

	public function addTransferAsset($id){
		return view('admin.asset.add-transfer-asset', compact('id'));
	}

	public function storeTransferAsset(Request $request, $id){
		$rqData = [
			'emp_id'   => 'required',
			'qty'      => 'required|numeric|min:0|not_in:0',
		  ];
		
		$validatedData = $request->validate($rqData);
		
		$logged_id  = Auth::user()->id;
		$remaining_product = 0;
		$check_asset_qty = Asset::where('id', $id)->sum('qty');
		$check_qty_from_rem_transfer = DB::table('assign_asset')->where('asset_id', $id)->whereRAW("(is_accepted = '0' OR is_accepted = '2') and is_parent = 0")->get()->sum("qty");

		if(!empty($check_asset_qty) || !empty($check_qty_from_rem_transfer)){
			$remaining_product = $check_asset_qty - $check_qty_from_rem_transfer;
		}

		if(!empty($check_asset_qty)){
			if($request->qty <= $remaining_product){
				$assign_asset_result = AssignAsset::insertGetId(['asset_id' => $id, 'assigned_by' => $logged_id, 'emp_id' => $request->emp_id, 'qty' => $request->qty, 'remark' => $request->remark, 'child_id' => $request->serial_no]);
				
				DB::table('asset_child')->where('id',$request->serial_no)->update(['assign' => $request->emp_id]);
				
				if(!empty($assign_asset_result)){
					return redirect()->route('admin.asset.index')->with('success', 'Asset Added Successfully');
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
	
	public function assignedAssetToEmployee(Request $request){
		//echo '<pre>'; print_r($request->post());die;
		$logged_id  = Auth::user()->id;
		if(!empty($request->asset_id)){
			$exp_data = explode("|", $request->asset_id);
			$emp_id   = $exp_data[0];
			$asset_id = $exp_data[1];
			$inputs = array('asset_id' => $asset_id,'assigned_by' => $logged_id, 'emp_id' => $emp_id);
			$assign_asset = AssignAsset::create($inputs);           

			if($assign_asset->save()){
				return response(['status' => true, 'message' => 'Asset Assign Successfully.'], 200);
			}else{          
				return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
			}
		}
		else{
			return response(['status' => false, 'message' => 'Asset ID Not Found'], 200);
		}
		
	}

	
	
	public function employeeAsset(){ 
		$eID   		= Input::get('eID');
		$logged_id  = Auth::user()->id;
		$role_id  	= Auth::user()->role_id;
		
		$emp_asset_list  = Asset::select('users.name','assign_asset.id','assign_asset.emp_id')
								->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
								->join('users', 'assign_asset.emp_id', '=', 'users.id')
								->groupBy('assign_asset.emp_id');
								
								
		if($role_id==30 || $role_id==29 || $role_id==25){
			$emp_asset_list = $emp_asset_list->orWhere('emp_id', 'LIKE', '%' . $eID . '%');
		}else if($role_id==21){
			$emp_asset_list = $emp_asset_list->where('supervisor_id', 'LIKE', '%' . $logged_id . '%')->orWhere('emp_id', 'LIKE', '%' . $logged_id . '%');
		}else{
			$emp_asset_list = $emp_asset_list->where('emp_id', 'LIKE', '%' . $logged_id . '%');
		}
		
		$emp_asset_list = $emp_asset_list->get();
		//echo '<pre>'; print_r($emp_asset_list);die;
        return view('admin.asset.employee_asset', compact('emp_asset_list')); 
	}
	
	public function updateAssetStatus($id,$emp_id){
		
		if(!empty($id)){
			$asset_res = AssignAsset::where('id', $id)->update(['is_accepted' => '1','return_date' => date('Y-m-d H:i:s')]);
			if($asset_res) {
				return back()->with('success', 'Status Update Successfully');
			} else {
				return back()->with('error', 'Something Went Wrong !');
			} 
		}
		else{
			return back()->with('error', 'Asset ID Not Found !');
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
	
	public function asset_product_child($id){
		return view('admin.asset.child-product',compact('id')); 
	}
	
	public function child_store(Request $request){
		//Child Table
		$record = array();
		$record['asset_id']		= $request->asset_id;					
		$record['serial_no']	= $request->serial_no;					
		$record['invoice_date']	= $request->invoice_date;					
		$record['remark']		= $request->remark;					
		
		if($files=$request->file('invoice_img')){					
			$iname = $files->getClientOriginalName();
			$iname = uniqid().'-'.$iname;
			$files->move('laravel/public/employee_asset',$iname);
			$record['invoice_img']= $iname;					
		}
		
		if($pfiles=$request->file('product_img')){					
			$pname = $pfiles->getClientOriginalName();
			$pname = uniqid().'-'.$pname;
			$pfiles->move('laravel/public/employee_asset',$pname);
			$record['product_img']= $pname;					
		}
		DB::table('asset_child')->insert($record);
		
		return back()->with('success', 'Added Successfully');
	}
}
