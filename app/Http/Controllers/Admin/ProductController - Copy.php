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
use App\Userdetails;
use App\Userbranches;

class ProductController extends Controller
{
	
	public function transferInventoryUpdateStatus(Request $request, $id,$product_id,$qty,$status){
		//echo '<pre>'; print_r($id.$product_id.$qty.$status);die;
		if(!empty($id) && !empty($product_id) && !empty($qty) && !empty($status)){
			
			$remaining_product       = 0;
			$check_qty_from_transfer = 0;
			$check_qty_from_products = 0;
			
			$fill_qty = $qty;
			
			$check_qty_from_products     =  DB::table('products')->where('id', $product_id)->orWhere('is_parent', $product_id)->sum('qty');
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
			$product = Transfer::select('transfer.*','products.id as product_id','products.name','products.cat_id','products.sub_cat_id')
		                   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
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
	
	public function inventory(){ 
		$log_id       = Auth::user()->id;
		$user_details = Userbranches::where('user_id', $log_id)->first();
		$product      = array();
		
		$status		=	Input::get('status');
		
		if(!empty($user_details->branch_id)){
			$product = Transfer::select(DB::raw("transfer.*, products.id as product_id, products.name, products.cat_id, products.sub_cat_id")) //, sum(transfer.qty) AS total_qty
		                   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
						   //->where('transfer.transfer_from', '0')
						   ->where('transfer.transfer_to', $user_details->branch_id)
						   //->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially' OR transfer.status = 'Raise a issue')")
						   //->groupBy('transfer.product_idd')
						   ->where('transfer.status', 'LIKE', '%' . $status . '%')
						   ->orderBy('transfer.id','desc')
						   ->get();
		}
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.inventory', compact('product','category'));
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
    
    public function index()
    {
		$name            = Input::get('name'); 
		$selectCatId     = Input::get('cat_id');
		$selectSubcatId  = Input::get('sub_cat_id');
		$whereCond       = '1=1 ';
		
		if(!empty($name)){ 
			$whereCond .= " AND (name = '$name')";
		}
		
		if(!empty($selectCatId)){ 
			$whereCond .= " AND (cat_id = '$selectCatId')";
		}
		
		if(!empty($selectSubcatId)){ 
			$whereCond .= " AND (sub_cat_id = '$selectSubcatId')";
		}
		
        $product = Product::select(DB::raw("*, (SELECT name FROM category WHERE id=cat_id) AS category_name, (SELECT name FROM category WHERE id=sub_cat_id) AS sub_category_name, (SELECT name FROM buyer WHERE id=buyer_id) AS buyer_name"))->where('is_deleted', '0')->where('is_parent', 0)->whereRaw($whereCond)->orderBy('id', 'desc')->get();
		
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		
        return view('admin.product.index', compact('product','category'));
    }
	
	
	public function create(Request $request){
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		return view('admin.product.add', compact('category'));
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
	
	public function store(Request $request)
    {
		$validatedData = $request->validate([
			'name'       => 'required',
			'cat_id'     => 'required',
			'sub_cat_id' => 'required',
			'buyer_id'    => 'required',
			'bill_no'    => 'required',
			'qty'        => 'required',
			'price'      => 'required',
			'bill_file'  => 'mimes:jpeg,png,jpg,gif,svg,pdf,txt|max:5120',
			
        ]);
		$inputs = $request->only('name','cat_id','sub_cat_id','maintains','warranty','warranty_period','expiry_date','is_consumer','qty','measurement','price','buyer_id','bill_no','type','status'); 
		
		// $check_product = Product::whereRaw("(name LIKE  '%".$request->name."%'  AND cat_id = $request->cat_id AND sub_cat_id = $request->sub_cat_id)")->first();
		// if(!empty($check_product)){
		// 	$inputs['is_parent'] = $check_product->id;
		// }
		//echo '<pre>'; print_r($check_product);die;
		
		if (Input::hasfile('bill_file')){
			$product_id  = '';
            $inputs['bill_file'] = $this->uploadBill(Input::file('bill_file'), $product_id);
        } 
		$inputs['product_date'] = date('Y-m-d');
		
		$add_product = Product::create($inputs); 
		
        if($add_product->save()) {
            return redirect()->route('admin.product.index')->with('success', 'Product Added Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function uploadBill($file, $id){
		$drive = public_path(DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$newImage = $drive . $filename;
		if(!empty($id)){
			$get_bill_file = DB::table('products')->where('id', $id)->first();
			if(!empty($get_bill_file->bill_file)){
				unlink($drive.'/'.$get_bill_file->bill_file);
			}
			
		}
		
		$imgResource = $file->move($drive, $filename);
		return $filename;
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
			'cat_id'     => 'required',
			'sub_cat_id' => 'required',
			'buyer_id'    => 'required',
			'bill_no'    => 'required',
			'qty'        => 'required',
			'price'      => 'required',
			'bill_file'  => 'mimes:jpeg,png,jpg,gif,svg,pdf,txt|max:5120',
        ]);
		
		$edit_product = Product::where('id', $id)->first();
		
		$inputs = $request->only('name','cat_id','sub_cat_id','maintains','warranty','warranty_period','expiry_date','is_consumer','qty','measurement','price','buyer_id','bill_no','type','status'); 
		if (Input::hasfile('bill_file')){
			$product_id  = $id;
            $inputs['bill_file'] = $this->uploadBill(Input::file('bill_file'), $product_id);
        }

		$inputs['product_date'] = date('Y-m-d');		

        if ($edit_product->update($inputs)) {
            return redirect()->route('admin.product.index')->with('success', 'Product Updated Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	 public function destroy($id)
    {   
        $product  = Product::find($id); 
		$inputs = array('is_deleted' => '1');
		
        if ($product->update($inputs)) {
            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } else {
            return redirect()->route('admin.product.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function transferProduct($product_id){
		$get_product_transfer_detail = ''; 
		
		$get_product_detail = array();
		if(!empty($product_id)){
			$get_product_detail = DB::table('transfer')->select(DB::raw("*, (SELECT name FROM branches WHERE id=transfer_from) As transfer_from_name, (SELECT name FROM branches WHERE id=transfer_to) As transfer_to_name"))->where('product_id', $product_id)->where('is_deleted', '0')->where('is_type', 'Branch')->whereRaw("transfer_from  = '0'")->get();
		}
		
		//echo '<pre>'; print_r($get_product_detail);die;
		return view('admin.product.transfer', compact('product_id','get_product_detail'));
	}
	
	
	public function addTransferProduct($product_id, $primary_id=null){
		$get_product_transfer_detail = ''; 
		$transfer_txt        = "Add";
		if(!empty($product_id) && !empty($primary_id)){
			$get_product_transfer_detail = DB::table('transfer')->where([['id','=', $primary_id], ['product_id', '=', $product_id]])->first();
			$transfer_txt = "Edit";
		}
		
	    
		//echo '<pre>'; print_r($get_buyer_detail);die;
		return view('admin.product.add-transfer', compact('product_id','primary_id','get_product_transfer_detail','transfer_txt'));
	}
	
	public function transferProductStore(Request $request){
		
		$rqData = [
				//'transfer_from' => 'required',
				'transfer_to'   => 'required',
				'qty'           => 'required|numeric|min:0|not_in:0',
			  ];
		
		$validatedData = $request->validate($rqData);
		
		$remaining_product       = 0;
		$check_qty_from_transfer = 0;
		$check_qty_from_products = 0;
		
		if(!empty($request->transfer_id)){
			$check_qty_from_transfer = DB::table('transfer')
										 ->where([['product_id', '=', $request->product_id], ['id', '=', $request->primary_id]])//->whereRaw("(status = 'Accept' OR status = 'Partially')")
										 ->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
										 //->where('is_type', 'Branch')
										 ->get()
										 ->sum("qty");
			$fill_qty = $request->qty - $check_qty_from_transfer;
		}
		else{
			$check_qty_from_transfer = DB::table('transfer')->where('transfer_from', '0')->where('product_id', $request->product_id)->where('is_deleted','=','0')->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")->get()->sum("qty");
			//$fill_qty = $check_qty_from_transfer;
			$fill_qty = $request->qty;     
		}
	
		
		$check_qty_from_products     =  DB::table('products')->where('id', $request->product_id)->orWhere('is_parent', $request->product_id)->sum('qty');
		$check_qty_from_rem_transfer = DB::table('transfer')->where('transfer_from', '0')->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")->where('product_id', $request->product_id)->get()->sum("qty");
		
		if(!empty($check_qty_from_products) || !empty($check_qty_from_rem_transfer)){
			$remaining_product = $check_qty_from_products - $check_qty_from_rem_transfer;
		}
		
		/* if(!empty($request->transfer_id)){
			$fill_qty = $request->qty - $check_qty_from_transfer;
		}	
		else{
			$fill_qty = $request->qty;
		}  */
		
		if($fill_qty <= $remaining_product){	
			$inputs  = $request->only('product_id','transfer_to','qty'); 
            $inputs['transfer_from'] = 0;
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
				return redirect()->route('admin.product.transfer-product',$request->product_id)->with('success', $msg);
			} else {
				return redirect()->route('admin.product.transfer-product',$request->product_id)->with('error', $msg1); 
			}
		}
		else{
			if($remaining_product == 0){
				$err_msg = "Product Out Of Stock";
			}
			else{
				//$err_msg = "$remaining_product Products Available";
				$err_msg = "Only $remaining_product Products Quantity Available";
			}
			
			
			//return redirect()->route('admin.product.transfer-product',$request->product_id)->with('error', $err_msg);
			return back()->with('error', $err_msg);
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
		$log_id       = Auth::user()->id;
		$product      = array();
		
		//$bID   	 = Input::get('bID');
		$bID   	 = Input::get('branch_id');
		$product = Transfer::select(DB::raw("transfer.*, products.id as product_id, products.name as product_name, branches.name as branch_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->where('transfer.is_type', 'Branch')
					   ->whereRaw("(transfer.status = 'Accept' OR transfer.status = 'Partially')")
					   ->groupBy('transfer.transfer_to')
					   ->groupBy('transfer.product_id')
					   ->where('transfer_to', 'LIKE', '%' . $bID . '%')
					   ->get();
		
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.branch-inventory', compact('product'));
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
		$product      = array();

		$bID		=	Input::get('branch_id');
		$status		=	Input::get('status');
		
		$product = Transfer::select(DB::raw("transfer.*, products.id as product_id, products.name as product_name, branches.name as branch_name"))
					   ->leftJoin('products', 'products.id', '=', 'transfer.product_id')
					   ->leftJoin('branches', 'branches.id', '=', 'transfer.transfer_to')
					   //->where('transfer.transfer_from', '0')
					   ->where('transfer.is_deleted', '0')
					   ->whereRaw("(transfer.is_type = 'Branch')")
					   ->where('transfer.transfer_to', 'LIKE', '%' . $bID . '%')
					   ->where('transfer.status', 'LIKE', '%' . $status . '%')
					   ->get();
		
		//echo '<pre>'; print_r($product);die;
		return view('admin.product.request-branch-inventory', compact('product'));
	}
	
}
