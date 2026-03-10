<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Transfer;
use Input;
use Validator;
use DB;
use Auth;
use Excel;
use App\Userdetails;
use App\Userbranches;
use App\Exports\BranchInventoryReport;
use App\Exports\WarehouseToBranchReport;

class ProductController extends Controller
{
	
	public function index()
    {
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		
		$name            = Input::get('name'); 
		$selectCatId     = Input::get('cat_id');
		$selectSubcatId  = Input::get('sub_cat_id');
		$whereCond       = '1=1 ';
		
		if(!empty($name)){ 
			$whereCond .= " AND (products.name LIKE '%$name%')";
		}
		
		if(!empty($selectCatId)){ 
			$whereCond .= " AND (products.cat_id = '$selectCatId')";
		}
		
		if(!empty($selectSubcatId)){ 
			$whereCond .= " AND (products.sub_cat_id = '$selectSubcatId')";
		}
		
        $product = DB::table('products')->select('products.*','a.name as category_name','b.name as sub_category_name')
					->leftjoin('category as a','a.id','products.cat_id')
					->leftjoin('category as b','b.id','products.sub_cat_id')
					->where('status', '!=','Deleted')->whereRaw($whereCond)->orderBy('id', 'desc');
		
		$product = $product->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $product = $product->get();
		
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		
        return view('admin.product.index', compact('product','category','pageNumber','params'));
    }
	
	public function create(Request $request){
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		return view('admin.product.add', compact('category'));
	}
	
	public function store(Request $request)
    {
		$validatedData = $request->validate([
			'name'       => 'required',
			// 'pcode'       => 'required',
			'cat_id'     => 'required',
			'sub_cat_id' => 'required'
			
        ]);
		$inputs = $request->only('name','cat_id','sub_cat_id','pcode'); 
		
		
		$inputs['product_date'] = date('Y-m-d');
		
		$add_product = Product::create($inputs); 
		
        if($add_product->save()) {
            return redirect()->route('admin.product.index')->with('success', 'Product Added Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	public function edit($id)
    {
        $product_detail = Product::find($id); 
		$category       = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
        return view('admin.product.edit', compact('product_detail','category'));
    }
	
	public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
			'name'       => 'required',
			'pcode'       => 'required',
			'cat_id'     => 'required',
			'sub_cat_id' => 'required'
        ]);
		
		$edit_product = Product::where('id', $id)->first();
		
		$inputs = $request->only('name','cat_id','sub_cat_id','pcode'); 
		 
		// $inputs['product_date'] = date('Y-m-d');		

        if ($edit_product->update($inputs)) {
            return redirect()->route('admin.product.index')->with('success', 'Product Updated Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	
	public function transferInventoryUpdateStatus(Request $request, $id,$product_id,$qty,$status){
		//echo '<pre>'; print_r($id.$product_id.$qty.$status);die;
		if(!empty($id) && !empty($product_id) && !empty($qty) && !empty($status)){
			
			$remaining_product       = 0;
			$check_qty_from_transfer = 0;
			$check_qty_from_products = 0;
			
			$fill_qty = $qty;
			
			//$check_qty_from_products     =  DB::table('products')->where('id', $product_id)->orWhere('is_parent', $product_id)->sum('qty');
			$check_qty_from_products     = DB::table('inventory')->where('product_id', $product_id)->sum('qty');
			$check_qty_from_rem_transfer = DB::table('transfer')->where('product_id', $product_id)->where('transfer_from', '0')->whereRaw("(status = 'Accept' OR status = 'Partially')")->get()->sum("qty");
			
			if(!empty($check_qty_from_products) || !empty($check_qty_from_rem_transfer)){
				$remaining_product = $check_qty_from_products - $check_qty_from_rem_transfer;
			}
			
			if($fill_qty <= $remaining_product){
				$raise_remark = '';
				if(!empty($request->remark)){
					$raise_remark = $request->remark;
				}	
				$res = Transfer::where('id', $id)->update(['status' => $status, 'remark' => $raise_remark]);
				if($res){
					return redirect()->route('admin.request-inventory')->with('success', 'Product '.$status.' Successfully');
				}
				else{
					return redirect()->route('admin.request-inventory')->with('error', 'Something Is Wrong');
				}
			}
			else{
				if($remaining_product == 0){
					$err_msg = "Product Out Of Stock";
				}
				else{
					$err_msg = "$remaining_product Products Available";
				}
				return redirect()->route('admin.request-inventory')->with('error', $err_msg);
			}
			
		}
		else{
			return redirect()->route('admin.request-inventory')->with('error', 'Something Is Wrong');
		}
	}
	
	public function requestInventory(){
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$product      = array();
		
		if(!empty($user_details->branch_id)){
			$product = Transfer::select('transfer.*','products.id as product_id','products.name','products.cat_id','products.sub_cat_id','a.name as category_name','b.name as sub_category_name')
		                   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
							->leftjoin('category as a','a.id','products.cat_id')
							->leftjoin('category as b','b.id','products.sub_cat_id')
						   //->where('transfer.transfer_from', '0')
						   ->where('transfer.transfer_to', $user_details->branch_id)
						   ->whereRaw("(transfer.status = 'Pending' AND transfer.is_deleted = '0')")
						   ->orderBy('transfer.id', 'desc')
						   ->get();
		}
		
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.request-inventory', compact('product','category'));
	}
	public function transferInventoryHistory($product_id){
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$product      = array();
		
		
		if(!empty($user_details->branch_id)){
			$product = Transfer::select('transfer.*','products.id as product_id','products.name','products.cat_id','products.sub_cat_id')
		                   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
						   ->where('transfer.transfer_from', '0')
						   ->where('transfer.transfer_to', $user_details->branch_id)
						   ->where('transfer.product_id', $product_id)
						   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
						   ->orderBy('transfer.id', 'desc')
						   ->get();
		}
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.inventory-history', compact('product','category'));
	}
	
	
	public function transferInventory($product_id,$branch_id){
		
		$log_id                      = Auth::user()->id;
		$user_details                = Userbranches::where('user_id', $log_id)->first();
		$get_product_transfer_detail = ''; 
		$get_product_detail          = array();
		if(!empty($user_details->branch_id)){
			
			if(!empty($product_id)){
				$get_product_detail = DB::table('transfer')->select(DB::raw("*, (SELECT name FROM branches WHERE id=transfer_from) As transfer_from_name, (SELECT name FROM branches WHERE id=transfer_to) As transfer_to_name"))->where('transfer_from', $branch_id)->where('product_id', $product_id)->where('is_deleted', '0')->where('is_type', 'Branch')->whereRaw("transfer_from  != '0'")->get();
			}
		}
		
		//echo '<pre>'; print_r($get_product_detail);die;
		return view('admin.product.transfer-inventory', compact('product_id','get_product_detail','branch_id'));
	}
	
	public function addTransferInventory($product_id, $primary_id=null){
		$get_product_transfer_detail = ''; 
		$transfer_txt        = "Add";
		if(!empty($product_id) && !empty($primary_id)){
			$get_product_transfer_detail = DB::table('transfer')->where([['id','=', $primary_id], ['product_id', '=', $product_id]])->first();
			$transfer_txt = "Edit";
		}
		
	    
		//echo '<pre>'; print_r($get_buyer_detail);die;
		return view('admin.product.add-transfer-inventory', compact('product_id','primary_id','get_product_transfer_detail','transfer_txt'));
	}
	
	public function transferInventoryStore(Request $request){
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$rqData = [
				//'transfer_from' => 'required',
				'transfer_to'   => 'required',
				'qty'           => 'required',
			  ];
		
		$validatedData = $request->validate($rqData);
		
		$remaining_product       = 0;
		$check_qty_from_transfer = 0;
		if(!empty($user_details->branch_id)){
			if(!empty($request->transfer_id)){
				$check_qty_from_transfer = DB::table('transfer')->where([['product_id', '=', $request->product_id], ['id', '=', $request->primary_id], ['transfer_from', '=', $user_details->branch_id]])->whereRaw("(status = 'Accept' OR status = 'Partially')")->get()->sum("qty");
				$fill_qty = $request->qty - $check_qty_from_transfer;
			}
			else{
				//$check_qty_from_transfer = DB::table('transfer')->where('product_id', $request->product_id)->get()->sum("qty");
				$fill_qty = $request->qty;
			}
		
			
			$check_qty_from_transfer     = DB::table('transfer')->where('product_id', $request->product_id)->where('transfer_to', $user_details->branch_id)->whereRaw("(status = 'Accept' OR status = 'Partially')")->get()->sum("qty");
			$check_qty_from_rem_transfer = DB::table('transfer')->where('product_id', $request->product_id)->where('transfer_from', $user_details->branch_id)->whereRaw("(status = 'Accept' OR status = 'Partially')")->get()->sum("qty");
			
			if(!empty($check_qty_from_transfer) || !empty($check_qty_from_rem_transfer)){
				$remaining_product = $check_qty_from_transfer - $check_qty_from_rem_transfer;
			}
			
		
			
			if($fill_qty <= $remaining_product){	
				$inputs  = $request->only('product_id','transfer_to','qty'); 
				
					$inputs['transfer_from'] = $user_details->branch_id;
					if(!empty($request->transfer_id)){
						$transfer_res = DB::table('transfer')->where('id', $request->transfer_id)->update($inputs);
						$msg = 'Transfer Update Successfully';
						$msg1 = 'No Any Update Record';
					}
					else{
						$transfer_res = DB::table('transfer')->insertGetId($inputs); 
						$msg = 'Transfer Added Successfully';
						$msg1 = 'Something Went Wrong !';
					}
				
			

				if($transfer_res) {
					return redirect()->route('admin.transfer-inventory',$request->product_id)->with('success', $msg);
				} else {
					return redirect()->route('admin.transfer-inventory',$request->product_id)->with('error', $msg1); 
				}
			}
			else{
				if($remaining_product == 0){
					$err_msg = "Product Out Of Stock";
				}
				else{
					$err_msg = "$remaining_product Products Available";
				}
				return redirect()->route('admin.transfer-inventory',$request->product_id)->with('error', $err_msg);
			}
		
		}
		else{
			return redirect()->route('admin.transfer-inventory',$request->product_id)->with('success', 'Branch ID Not Found');
		}
	}
    
	public function getSubCat(Request $request){
		$subCatData = DB::table('category')->where('parent', $request->cat_id)->where('is_deleted','0')->get();
		
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
	
	
	public function getSubProduct(Request $request){
		echo $request->sub_cat_id;
		$proData = DB::table('products')->where('sub_cat_id', $request->sub_cat_id)->where('status','Active')->get();
		
		if (!empty($proData))
        {
            echo $res = "<option value=''> Select Product </option>";
            foreach ($proData as $key => $value)
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
            echo $res = "<option value=''> Product Not Found </option>";
            die();
        }
	}
	
	
	
	public function destroy($id)
    {   
        $product  = Product::find($id); 
		$inputs = array('status' => 'Deleted');
		
        if ($product->update($inputs)) {
            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	
	
	
	
	public function deleteTransferProduct($id){
		$transfer  = DB::table('transfer')->where('id',$id);
		$inputs = array('is_deleted' => '1');
		
        if ($transfer->update($inputs)) {
            return redirect()->back()->with('success', 'Deleted Successfully');
        } else {
			return redirect()->back()->with('success', 'Something Went Wrong !');
        }
	}
	
	public function getBill(Request $request){
		$billData = DB::table('bill')->where('buyer_id', $request->buyer_id)->get();
		
		if (!empty($billData))
        {
            echo $res = "<option value=''> Select Bill </option>";
            foreach ($billData as $key => $value)
            {
                if (!empty($value->id))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->bill_no . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Bill Not Found </option>";
            die();
        }
	}
	
	public function show(Request $request){ 
		$search = $request->get('term');
        $whereCond = array();
        if(!empty($search)) $whereCond[] = 'name LIKE \''.$search.'%\'';
        $whereCondStr = '';
        if(is_array($whereCond) && count($whereCond)>0) $whereCondStr = ' WHERE '.implode(' AND ', $whereCond);
        $result = DB::select("select name AS label FROM products ".$whereCondStr." group by name");
        return response()->json($result);
	}
	
	public function branchInventory(){
		$log_id      	 = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		
		// $product      = array();
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
				
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
		
		//$bID   	 = Input::get('bID');
		$bID   	 	=	Input::get('branch_id');
		$pID   	 	= 	Input::get('name');
		$status  	= 	Input::get('status');
		$fdate 	 	= 	Input::get('fdate');
        $tdate 		= 	Input::get('tdate');
		
		$product 	= 	Transfer::select(DB::raw("transfer.*, products.id as product_ids, products.name as product_name, branches.name as branch_name,a.name as cat_name,b.name as sub_cat_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   ->leftJoin('inventory', 'inventory.product_id', '=', 'transfer.product_id')
					   ->leftJoin('category as a', 'a.id', 'products.cat_id')
					   ->leftJoin('category as b', 'b.id', 'products.sub_cat_id')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
					   ->groupBy('transfer.transfer_to')
					   ->groupBy('transfer.product_id')
					   ->where('transfer_to', 'LIKE', '%' . $bID . '%');
					   
		if (!empty($fdate) && !empty($tdate)) {			
            $product->whereDate('transfer.created_at', '>=', $fdate)->whereDate('transfer.created_at', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $product->where('transfer.created_at', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $product->where('transfer.created_at', 'LIKE', '%' . $tdate . '%');
        }else{
			$product->where('transfer.created_at', 'LIKE', '%' . date('Y-m-d') . '%');
		}
					   
		if($logged_role_id == 25){
			$product->where('inventory.location','=',$location);
		}
		
		if(!empty($pID)){
			$product->where('products.id',$pID);
		}
		
		if(!empty($status)){ 
			$product->where('inventory.inventory_location',$status);
		}
		
		$product = $product->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $product = $product->get();
		$product_list = Product::where('status', 'Active')->orderBy('id', 'desc')->get();
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.branch-inventory', compact('product','pageNumber','params','product_list'));
	}
	
	public function transferBranchInventory($product_id, $branch_id){
		return view('admin.product.transfer-branch-inventory', compact('product_id','branch_id'));
	}
	
	public function transferBranchInventoryStore(Request $request){
		$log_id       = Auth::user()->id;
		$rqData = [
				'transfer_to'   => 'required',
				'qty'           => 'required|numeric|min:0|not_in:0',
			  ];
		
		$validatedData = $request->validate($rqData);
		
		$remaining_product       = 0;
		$check_qty_from_transfer = 0;
			
			$fill_qty = $request->qty;
				
			$check_qty_from_transfer     = DB::table('transfer')
											->where('product_id', $request->product_id)
											->where('transfer_to', $request->branch_id)
											//->whereRaw("(status = 'Accept' OR status = 'Partially')")
											->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
											->get()
											->sum("qty");
			
			$check_qty_from_rem_transfer = DB::table('transfer')
											->where('product_id', $request->product_id)
											->where('transfer_from', $request->branch_id)
											//->whereRaw("(status = 'Accept' OR status = 'Partially')")
											->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
											->get()
											->sum("qty");
			
			if(!empty($check_qty_from_transfer) || !empty($check_qty_from_rem_transfer)){
				$remaining_product = $check_qty_from_transfer - $check_qty_from_rem_transfer;
			}
			
		    //echo '<pre>'; print_r($check_qty_from_transfer);die;
			
			if($fill_qty <= $remaining_product){	
				$inputs  = $request->only('product_id','transfer_to','qty'); 
				
					$inputs['transfer_from'] = $request->branch_id;
					$transfer_res = DB::table('transfer')->insertGetId($inputs); 
					$msg = 'Transfer Added Successfully';
					$msg1 = 'Something Went Wrong !';
					
				
			

				if($transfer_res) {
					return redirect()->route('admin.transfer-branch-inventory',[$request->product_id, $request->branch_id])->with('success', $msg);
				} else {
					return redirect()->route('admin.transfer-branch-inventory',[$request->product_id, $request->branch_id])->with('error', $msg1); 
				}
			}
			else{
				if($remaining_product == 0){
					$err_msg = "Product Out Of Stock";
				}
				else{
					$err_msg = "$remaining_product Products Available";
				}
				return redirect()->route('admin.transfer-branch-inventory',[$request->product_id, $request->branch_id])->with('error', $err_msg);
			}
	}
	
	public function requestBranchInventory(){
		$log_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		// $product      = array();
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
				
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];

		$bID		=	Input::get('branch_id');
		$status		=	Input::get('status');
		
		$product = Transfer::select(DB::raw("transfer.*, products.id as product_ids, products.name as product_name, branches.name as branch_name,a.name as cat_name,b.name as sub_cat_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   ->leftJoin('inventory', 'inventory.product_id', '=', 'transfer.product_id')
					    ->leftJoin('category as a', 'a.id', '=', 'products.cat_id')
					   ->leftJoin('category as b', 'b.id', '=', 'products.sub_cat_id')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.is_type = 'Branch')");
					   
		if(!empty($bID)){
			$product->where('transfer.transfer_to', 'LIKE', '%' . $bID . '%');
		}
		
		if(!empty($status)){
			$product->where('transfer.status', 'LIKE', '%' . $status . '%');
		}
					   
		if($logged_role_id == 25){
			$product->where('inventory.location','=',$location);
			$product->where('branches.branch_location','=',$location);
		}
		
		$product = $product->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}		
		// $product = $product->get();
		
		return view('admin.product.request-branch-inventory', compact('product','pageNumber','params'));
	}
	
	public function branch_report_excel(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$branch_id		=	Input::get('branch_id');
		$product_id		=	Input::get('product_id');
		
		$user_details 	= Userbranches::where('user_id', $logged_id)->first();
							
		$comman_result = Transfer::select(DB::raw("transfer.*, products.id as product_ids, products.name as product_name, branches.name as branch_name,a.name as cat_name,b.name as sub_cat_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   ->leftJoin('inventory', 'inventory.product_id', '=', 'transfer.product_id')
					   ->leftJoin('category as a', 'a.id', 'products.cat_id')
					   ->leftJoin('category as b', 'b.id', 'products.sub_cat_id')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
					   ->groupBy('transfer.transfer_to')
					   ->groupBy('transfer.product_id');
						   
		if(!empty($product_id)){
			$comman_result->where('transfer.product_id', $product_id);
		}
		
		if(Auth::user()->role_id == 25){
			if(!empty($branch_id)){
				$comman_result->where('transfer.transfer_to', $branch_id);
			}else{
				$comman_result->where('transfer.transfer_to', $user_details->branch_id);
			}
		}
		
		if(Auth::user()->role_id == 28){
			$comman_result->where('transfer.transfer_to', $user_details->branch_id);
		}
		
		$comman_result = $comman_result->get();
				
		$responseArray = array();
		//echo '<pre>'; print_r($comman_result);die;
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$value){ 	
				
				$total_qty = DB::table('transfer')
								->where('product_id', $value->product_ids)
								->where('transfer_to', $value->transfer_to)
								//->whereRaw("(status = 'Accept' OR status = 'Partially')")
								->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
								->groupBy('transfer_to')
								->groupBy('product_id')
								->sum("qty");
				
				
				$responseArray[$key]['category_name'] = $value->cat_name;
				$responseArray[$key]['sub_category_name'] = $value->sub_cat_name;
				$responseArray[$key]['name'] = $value->product_name;
				$responseArray[$key]['rem_pro'] = $total_qty;
				$responseArray[$key]['status'] = $value->status;
				$responseArray[$key]['remark'] = $value->remark;
				$responseArray[$key]['created_at'] = date('d-m-Y h:i:s', strtotime($value->created_at));
			}
		} 
		
        if(count($responseArray) > 0){
            return Excel::download(new BranchInventoryReport($responseArray), 'BranchInventoryReport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	
	public function warehouse_branch_report_excel(Request $request){
		$log_id      	 = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
				
		$location 	=	Auth::user()->user_branches[0]->branch['branch_location'];
		$bID   	 	=	Input::get('branch_id');
		$pID   	 	= 	Input::get('name');
		$status  	= 	Input::get('status');
		$fdate 	 	= 	Input::get('fdate');
        $tdate 		= 	Input::get('tdate');
		
		$comman_result 	= 	Transfer::select(DB::raw("transfer.*, products.id as product_ids, products.name as product_name, branches.name as branch_name,a.name as cat_name,b.name as sub_cat_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   ->leftJoin('inventory', 'inventory.product_id', '=', 'transfer.product_id')
					   ->leftJoin('category as a', 'a.id', 'products.cat_id')
					   ->leftJoin('category as b', 'b.id', 'products.sub_cat_id')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
					   ->groupBy('transfer.transfer_to')
					   ->groupBy('transfer.product_id')
					   ->where('transfer_to', 'LIKE', '%' . $bID . '%');
					   
		if (!empty($fdate) && !empty($tdate)) {			
            $comman_result->whereDate('transfer.created_at', '>=', $fdate)->whereDate('transfer.created_at', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $comman_result->where('transfer.created_at', 'LIKE', '%' . $fdate . '%');
        } elseif (!empty($tdate)) {
            $comman_result->where('transfer.created_at', 'LIKE', '%' . $tdate . '%');
        }else{
			$comman_result->where('transfer.created_at', 'LIKE', '%' . date('Y-m-d') . '%');
		}
					   
		if($logged_role_id == 25){
			$comman_result->where('inventory.location','=',$location);
		}
		
		if(!empty($pID)){
			$comman_result->where('products.id',$pID);
		}
		
		if(!empty($status)){ 
			$comman_result->where('inventory.inventory_location',$status);
		}
		
		$comman_result	=	$comman_result->get();
						
		$responseArray = array();
		// echo '<pre>'; print_r($comman_result);die;
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$value){	
				$total_qty = DB::table('transfer')
								->where('product_id', $value->product_ids)
								->where('transfer_to', $value->transfer_to)
								//->whereRaw("(status = 'Accept' OR status = 'Partially')")
								->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
								->groupBy('transfer_to')
								->groupBy('product_id')
								->sum("qty");
				
				$rem_pro = 0;
				$check_total_approved = DB::table('transfer')
											->where('product_id', $value->product_ids)
											->where('transfer_from', $value->transfer_to )
											->whereRaw("transfer_from  != '0'")
											//->whereRaw("(status = 'Accept' OR status = 'Partially')")
											->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
											->groupBy('transfer_from')
											->groupBy('product_id')
											->sum("qty");
				
				if(!empty($check_total_approved)){
					$rem_pro = $total_qty - $check_total_approved;
				}
				else{
					$rem_pro = $total_qty;
				}
				
				
				$responseArray[$key]['category_name'] 		= 	$value->cat_name;
				$responseArray[$key]['sub_category_name'] 	= 	$value->sub_cat_name;
				$responseArray[$key]['product_name'] 		= 	$value->product_name;
				$responseArray[$key]['branch_name'] 		= 	$value->branch_name;
				$responseArray[$key]['transfer_qty'] 		= 	$value->qty;
				$responseArray[$key]['total_branch_stock'] 	= 	$rem_pro;
				$responseArray[$key]['created_at'] 			=	date('d-m-Y h:i:s', strtotime($value->created_at));
			}
		} 
		
        if(count($responseArray) > 0){
            return Excel::download(new WarehouseToBranchReport($responseArray), 'WarehouseToBranchReport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	//Branch Inventory
	public function branch_inventory(){ 
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$product      = array();
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		$status		=	Input::get('status');
		$branch_id	=	Input::get('branch_id');
		$product_id	=	Input::get('product_id');
		
		if(!empty($user_details->branch_id)){ 
			$product = Transfer::select(DB::raw("transfer.*, products.id as product_ids, products.name as product_name, branches.name as branch_name,a.name as cat_name,b.name as sub_cat_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   ->leftJoin('inventory', 'inventory.product_id', '=', 'transfer.product_id')
					   ->leftJoin('category as a', 'a.id', 'products.cat_id')
					   ->leftJoin('category as b', 'b.id', 'products.sub_cat_id')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
					   ->groupBy('transfer.transfer_to')
					   ->groupBy('transfer.product_id');
						   
			if(!empty($product_id)){
				$product->where('transfer.product_id', $product_id);
			}
			
			if(Auth::user()->role_id == 25){
				if(!empty($branch_id)){
					$product->where('transfer.transfer_to', $branch_id);
				}else{
					$product->where('transfer.transfer_to', $user_details->branch_id);
				}
			}
			
			if(Auth::user()->role_id == 28){
				$product->where('transfer.transfer_to', $user_details->branch_id);
			}
			
			
			$product = $product->paginate(100);
			$pageNumber = 1;
			if(isset($page)){
				$page = Input::get('page');
				$pageNumber = (100*($page-1));
				
				$pageNumber = $pageNumber +1;
			}
		}
		
		
		$product_list = Product::where('status', 'Active')->orderBy('id', 'desc')->get();
		return view('admin.product.branch-asset', compact('product','product_list','pageNumber','params'));
	}
	
	
	public function inventory(){ 
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$product      = array();
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		$status		=	Input::get('status');
		$branch_id	=	Input::get('branch_id');
		$product_id	=	Input::get('product_id');
		
		if(!empty($user_details->branch_id)){ 
			$product = Transfer::select(DB::raw("transfer.*, products.id as product_id, products.name, products.cat_id, products.sub_cat_id, a.name as category_name,b.name as sub_category_name")) //, sum(transfer.qty) AS total_qty
							->leftJoin('products', 'products.id', '=', 'transfer.product_id')
							->leftjoin('category as a','a.id','products.cat_id')
							->leftjoin('category as b','b.id','products.sub_cat_id')
							->where('transfer.status','Accept')
							->orderBy('transfer.id','desc');
						   
			if(!empty($product_id)){
				$product->where('transfer.product_id', $product_id);
			}
			
			if(Auth::user()->role_id == 25){
				if(!empty($branch_id)){
					$product->where('transfer.transfer_to', $branch_id);
				}else{
					$product->where('transfer.transfer_to', $user_details->branch_id);
				}
			}
			
			if(Auth::user()->role_id == 28){
				$product->where('transfer.transfer_to', $user_details->branch_id);
			}
			
			
			$product = $product->paginate(100);
			$pageNumber = 1;
			if(isset($page)){
				$page = Input::get('page');
				$pageNumber = (100*($page-1));
				
				$pageNumber = $pageNumber +1;
			}
		}
		
		
		$product_list = Product::where('status', 'Active')->orderBy('id', 'desc')->get();
		return view('admin.product.inventory', compact('product','product_list','pageNumber','params'));
	}
	
	public function product_history(Request $request,$pid){
		$product_history = DB::table('po_history')
							->select('po_history.*','arn.pdate','arn.company','buyer.name as vname')
							->leftjoin('asset_request_notification as arn','arn.request_id','po_history.asset_id')
							->leftjoin('buyer','buyer.id','arn.company')
							->where('product_id',$pid)
							->get();
		return view('admin.product.product-history',compact('product_history'));
	}
}
