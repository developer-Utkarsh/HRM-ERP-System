<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Inventory;
use App\Category;
use App\Transfer;
use Input;
use Validator;
use DB;
use Auth;
use Excel;
use App\Userdetails;
use App\Userbranches;
use App\Exports\WarehouseReportExport;

class InventoryController extends Controller
{
	public function index()
    {
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		$product_id      = Input::get('product_id'); 
		$selectCatId     = Input::get('cat_id');
		$selectSubcatId  = Input::get('sub_cat_id');
		$status			 = Input::get('status');
		$whereCond       = '1=1 ';
		
		if(!empty($product_id)){ 
			$whereCond .= " AND (products.id = '$product_id')";
		}
		
		if(!empty($selectCatId)){ 
			$whereCond .= " AND (cat_id = '$selectCatId')";
		}
		
		if(!empty($selectSubcatId)){ 
			$whereCond .= " AND (sub_cat_id = '$selectSubcatId')";
		}
		
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
		
        $product = Inventory::select(DB::raw("inventory.*,SUM(inventory.qty) as total_qty,SUM(inventory.price) as total_price,products.name as p_name,a.name as cat_name,b.name as sub_cat_name,(SELECT SUM(transfer.qty) FROM transfer WHERE status='Accept' AND product_id=inventory.product_id and transfer.location = '".$location."' ) AS transfer_new_qty,SUM(inventory.qty) - (SELECT coalesce(SUM(transfer.qty),0) FROM transfer WHERE status='Accept' AND product_id=inventory.product_id and transfer.location = '".$location."') as outofstock"))
					->leftJoin('products','products.id','=','inventory.product_id')		
					->leftJoin('category as a','a.id','=','products.cat_id')		
					->leftJoin('category as b','b.id','=','products.sub_cat_id')	
					->where('inventory.location', $location)
					->where('inventory.is_deleted', '0')
					->where('inventory.status','!=','Dead')
					->where('inventory.is_parent', 0)
					->whereRaw($whereCond)
					->groupBy('inventory.product_id')
					->orderBy('inventory.id', 'desc');
		
		if($status=="Out of stock"){
			$product->having(DB::raw('outofstock'), '=', 0);
		}else{
			$product->having(DB::raw('outofstock'), '!=', 0);
		}
		
		if($logged_role_id == 25){
			$product->where('inventory.location','=',$location);
		}
	
		$product = $product->paginate(50);	
		
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
		
		// $product = $product->get(); 
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		$product_list = Product::where('status', 'Active')->orderBy('id', 'desc')->get();
		
        return view('admin.inventory.index', compact('product','pageNumber','params','category','product_list','status'));
    }
	
	public function product_inventory_list($product_id)
    {
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
		$name            = Input::get('name'); 
		$selectCatId     = Input::get('cat_id');
		$selectSubcatId  = Input::get('sub_cat_id');
		$whereCond       = '1=1 ';
		
		$whereCond .= " AND (product_id = '$product_id')";
		if(!empty($name)){ 
			$whereCond .= " AND (name = '$name')";
		}
		
		if(!empty($selectCatId)){ 
			$whereCond .= " AND (cat_id = '$selectCatId')";
		}
		
		if(!empty($selectSubcatId)){ 
			$whereCond .= " AND (sub_cat_id = '$selectSubcatId')";
		}
		
        $product = Inventory::select(DB::raw("inventory.*,products.name as p_name, (SELECT name FROM buyer WHERE id=buyer_id) AS buyer_name"))
					->leftJoin('products','products.id','=','inventory.product_id')		
					->where('is_deleted', '0')->where('is_parent', 0)
					->whereRaw($whereCond)
					->orderBy('id', 'desc')
					->where('inventory.location','=',$location)
					->get();
		
		$category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
		
        return view('admin.inventory.product-inventory-list', compact('product','category'));
    }
	
	public function create(Request $request){
		$product = Product::select('products.*','A.name as cat_name','B.name as sub_cat_name')
			->leftJoin('category AS A','A.id', '=', 'products.cat_id')
			->leftJoin('category AS B','B.id', '=', 'products.sub_cat_id')
			->where('status', 'Active')
			->orderBy('id', 'desc')
			->get();

		return view('admin.inventory.add', compact('product'));
	}
	
	
	public function store(Request $request)
    {
		$validatedData = $request->validate([
			'product_id'       => 'required',
			// 'buyer_id'    => 'required',
			// 'bill_no'    => 'required',
			'qty'        => 'required',
			// 'price'      => 'required',
			// 'bill_file'  => 'mimes:jpeg,png,jpg,gif,svg,pdf,txt|max:5120',
			
        ]);
		
		$inputs = $request->only('product_id','maintains','warranty','warranty_period','expiry_date','is_consumer','qty','measurement','price','buyer_id','bill_no','type','status','location','mode','remark'); 
		
		
		if (Input::hasfile('bill_file')){
			$product_id  = '';
			$inputs['bill_file'] = $this->uploadBill(Input::file('bill_file'), $product_id);
		} 
				
		if($_FILES['product_one']['size'] != 0){
			$inputs['product_one']	=	$this->uploadImage(Input::file('product_one'));
		}
		
		if($_FILES['product_two']['size'] != 0){
			$inputs['product_two']	=	$this->uploadImage2(Input::file('product_two'));
		}
		
		$inputs['product_date'] = date('Y-m-d');
		
		if(!empty($request->model_no)){
			$inputs['model_no'] = $request->model_no;
		}
		
		if(!empty($request->serial_no)){
			$inputs['serial_no'] = $request->serial_no;
		}
		
		$add_product = Inventory::create($inputs); 
		
		if($add_product->save()) {
			return redirect()->route('admin.inventory.index')->with('success', 'Inventory Added Successfully');
		} else {
			return redirect()->route('admin.inventory.index')->with('error', 'Something Went Wrong !');
		}
		
    }
	
	public function edit($id)
    {
        $product_detail = Inventory::find($id); 
		$product       = Product::where('status', 'Active')->orderBy('id', 'desc')->get();
        return view('admin.inventory.edit', compact('product_detail','product'));
    }
	
	public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
			'product_id'       => 'required',
			// 'buyer_id'    => 'required',
			// 'bill_no'    => 'required',
			'qty'        => 'required',
			// 'price'      => 'required',
			// 'bill_file'  => 'mimes:jpeg,png,jpg,gif,svg,pdf,txt|max:5120',
        ]);
		
		$edit_inventory = Inventory::where('id', $id)->first();
		
		$inputs = $request->only('product_id','maintains','warranty','warranty_period','expiry_date','is_consumer','qty','measurement','price','buyer_id','bill_no','type','status','mode','remark','location'); 
		if (Input::hasfile('bill_file')){
			$product_id  = $id;
            $inputs['bill_file'] = $this->uploadBill(Input::file('bill_file'), $product_id);
        }
		
		if($_FILES['product_one']['size'] != 0){
			$inputs['product_one']	=	$this->uploadImage(Input::file('product_one'));
		}
		
		if($_FILES['product_two']['size'] != 0){
			$inputs['product_two']	=	$this->uploadImage2(Input::file('product_two'));
		}

		$inputs['product_date'] = date('Y-m-d');


		if(!empty($request->model_no)){
			$inputs['model_no'] = $request->model_no;
		}
		
		if(!empty($request->serial_no)){
			$inputs['serial_no'] = $request->serial_no;
		}

        if ($edit_inventory->update($inputs)) {
            return redirect()->route('admin.inventory.index')->with('success', 'Inventory Updated Successfully');
        } else {
            return redirect()->route('admin.inventory.index')->with('error', 'Something Went Wrong !');
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
	
	public function inventory_transfer($product_id,$primary_id=null){
		$get_product_transfer_detail = ''; 
		$transfer_txt        = "Add";
		if(!empty($product_id) && !empty($primary_id)){
			$get_product_transfer_detail = DB::table('transfer')->where([['id','=', $primary_id], ['product_id', '=', $product_id]])->first();
			$transfer_txt = "Edit";
		}
		
	    
		//echo '<pre>'; print_r($get_buyer_detail);die;
		return view('admin.inventory.inventory-transfer', compact('product_id','primary_id','get_product_transfer_detail','transfer_txt'));
	}
	
	public function inventory_transfer_store(Request $request){
		
		$rqData = [
				//'transfer_from' => 'required',
				'transfer_to'   => 'required',
				'qty'           => 'required|numeric|min:0|not_in:0',
			  ];
		
		$validatedData = $request->validate($rqData);
		
		$remaining_product       = 0;
		$check_qty_from_transfer = 0;
		$check_qty_from_products = 0;
		$branch_location = Auth::user()->user_branches[0]->branch['branch_location'];
		
		if(!empty($request->transfer_id)){
			$check_qty_from_transfer = DB::table('transfer')
										 ->where('product_id',$request->product_id)
										 ->where('id',$request->primary_id)
										 ->where('location', $branch_location)
										 ->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
										 //->where('is_type', 'Branch')
										 ->get()->sum("qty");
			$fill_qty = $request->qty - $check_qty_from_transfer;
		}
		else{
			$check_qty_from_transfer = DB::table('transfer')->where('transfer_from', '0')
									->where('product_id', $request->product_id)
									->where('is_deleted','=','0')
									->where('location', $branch_location)
									->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
									->get()->sum("qty");
			//$fill_qty = $check_qty_from_transfer;
			$fill_qty = $request->qty;     
		}
	
		
		$check_qty_from_products     =  DB::table('inventory')
										->where('product_id', $request->product_id)
										->where('location', $branch_location)
										->sum('qty');
		$check_qty_from_rem_transfer = DB::table('transfer')
										->where('transfer_from', '0')
										->where('product_id', $request->product_id)
										->where('location', $branch_location)
										->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
										->get()->sum("qty");
		
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
            $inputs['location'] = $branch_location;
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
				return redirect()->route('admin.inventory.inventory-transfer-list',[$request->product_id])->with('success', $msg);
			} else {
				return redirect()->route('admin.inventory.inventory-transfer-list',[$request->product_id])->with('error', $msg1); 
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
	
	public function inventory_transfer_list($product_id){
		$get_product_transfer_detail = ''; 
		
		$get_product_detail = array();
		
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];

		if(!empty($product_id)){
			$get_product_detail = DB::table('transfer')
									->select(DB::raw("*, (SELECT name FROM branches WHERE id=transfer_from) As transfer_from_name, (SELECT name FROM branches WHERE id=transfer_to) As transfer_to_name"),'users.name','transfer.status as tstatus','transfer.created_at as tdate')
									->leftJoin('users','users.id','transfer.user_id')
									->where('product_id', $product_id)
									->where('transfer.is_deleted', '0')
									// ->where('is_type', 'Branch')
									->whereRaw("transfer_from  = '0' AND (is_type = 'Branch' OR is_type = 'Asset')")
									->where('location',$location)
									->get();
		}
		
		//echo '<pre>'; print_r($get_product_detail);die;
		return view('admin.inventory.inventory-transfer-list', compact('product_id','get_product_detail'));
	}
	
	public function destroy($id)
    {   
        $product  = Inventory::find($id); 
		$inputs = array('is_deleted' => '1');
		
        if ($product->update($inputs)) {
            return redirect()->back()->with('success', 'Inventory Deleted Successfully');
        } else {
            return redirect()->route('admin.inventory.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	
	public function uploadImage($image){
       $drive = public_path(DIRECTORY_SEPARATOR . 'bill' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
	}
	
	
	public function uploadImage2($image){
       $drive = public_path(DIRECTORY_SEPARATOR . 'bill' . DIRECTORY_SEPARATOR);
       $extension = $image->getClientOriginalExtension();
       $imagename = uniqid() . '.' . $extension;    
       $newImage = $drive . $imagename;
       $imgResource = $image->move($drive, $imagename);
       return $imagename;
	}
	
	
	public function warehouse_report_excel(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		$product_id  = $request->product_id; 
		$status		 = $request->status;
		
		
		$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
		
							
		$comman_result = Inventory::select(DB::raw("inventory.*,SUM(inventory.qty) as total_qty,SUM(inventory.price) as total_price,products.name as p_name,a.name as cat_name,b.name as sub_cat_name,(SELECT SUM(transfer.qty) FROM transfer WHERE product_id=inventory.product_id and transfer.location = '".$location."') AS transfer_new_qty,SUM(inventory.qty) - (SELECT coalesce(SUM(transfer.qty),0) FROM transfer WHERE product_id=inventory.product_id and transfer.location = '".$location."') as outofstock"))
					->leftJoin('products','products.id','=','inventory.product_id')		
					->leftJoin('category as a','a.id','=','products.cat_id')		
					->leftJoin('category as b','b.id','=','products.sub_cat_id')		
					->where('inventory.is_deleted', '0')
					->where('inventory.is_parent', 0)
					->where('inventory.status','!=','Dead')
					->where('inventory.location','=',$location)
					->groupBy('inventory.product_id')
					->orderBy('inventory.id', 'desc');
					
		if($status=="Out of stock"){
			$comman_result->having(DB::raw('outofstock'), '=', 0);
		}else{
			$comman_result->having(DB::raw('outofstock'), '!=', 0);
		}
		
		
		if($logged_role_id == 25){
			$comman_result->where('inventory.location','=',$location);
		}
		
		
		$comman_result = $comman_result->get();
				
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$value){	
				$rem_pro = 0;
				$check_total_approved =	DB::table('transfer')
										  ->where('product_id', $value->product_id)
										  ->where('transfer_from', '0')
										  ->where('is_deleted','=','0')
										  ->where('status','Accept')
										  ->where('location',$location)
										  ->get()
										  ->sum("qty");
										  
				if(!empty($check_total_approved)){
					$rem_pro =	$value->total_qty - $check_total_approved;
				}else{
					$rem_pro = $value->total_qty;
				}
				
				// if($rem_pro > 0){	
					$responseArray[$key]['category_name'] 		= 	$value->cat_name;
					$responseArray[$key]['sub_category_name'] 	= 	$value->sub_cat_name;
					$responseArray[$key]['name'] 				= 	$value->p_name;
					$responseArray[$key]['total_qty'] 			= 	$value->total_qty;
					$responseArray[$key]['rem_pro'] 			= 	$rem_pro;
					$responseArray[$key]['created_at'] 			=	date('d-m-Y h:i:s', strtotime($value->created_at));
				// }
			}
		} 
		
        if(count($responseArray) > 0){
            return Excel::download(new WarehouseReportExport($responseArray), 'WarehouseReport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function product_dead_list($product_id){
		DB::table('inventory')->where('product_id',$product_id)->where('status','!=','Dead')->update(['status'=>'Dead']);
		
		DB::table('transfer')->where('product_id',$product_id)->where('is_deleted','!=','1')->update(['is_deleted'=>'1','remark'=>'Dead']);
		
		return redirect()->back()->with('success', 'Status updated successfully!!');
	}
}
