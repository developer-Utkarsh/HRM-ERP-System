<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use Input;
use DB;

class ProductController extends Controller
{   
	
	public function getProducts(Request $request){
		try {
			$name            = $request->name; 
			$selectCatId     = $request->cat_id;
			$selectSubcatId  = $request->sub_cat_id;	
			$whereCond = ' 1=1 ';
			
			if(!empty($name)){ 
				$whereCond .= " AND (name = '$name')";
			}
			
			if(!empty($selectCatId)){ 
				$whereCond .= " AND (cat_id = '$selectCatId')";
			}
			
			if(!empty($selectSubcatId)){ 
				$whereCond .= " AND (sub_cat_id = '$selectSubcatId')";
			}
			
			$product = Product::select(DB::raw("*, (SELECT name FROM category WHERE id=cat_id) AS category_name, (SELECT name FROM category WHERE id=sub_cat_id) AS sub_category_name"))->where('is_deleted', '0')->whereRaw($whereCond)->orderBy('id', 'desc')->get();
			
			if(count($product) > 0){
				
				
				$data['product'] = $product;
				return $this->returnResponse(200, true, "Product Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Product Not Found"); 
			}
			
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function transfer(Request $request){
		try {
			
			if(!empty($request->product_id)){
				if(!empty($request->transfer_from)){
					if(!empty($request->transfer_to)){
						if(!empty($request->qty)){
							$remaining_product       = 0;
							$check_qty_from_transfer = 0;
							$check_qty_from_products = 0;
							
							if(!empty($request->transfer_id)){
								$check_qty_from_transfer = DB::table('transfer')->where([['product_id', '=', $request->product_id], ['id', '=', $request->primary_id], ['status', '=', 'Approved']])->get()->sum("qty");
								$fill_qty = $request->qty - $check_qty_from_transfer;
							}
							else{
								//$check_qty_from_transfer = DB::table('transfer')->where('product_id', $request->product_id)->get()->sum("qty");
								$fill_qty = $request->qty;
							}
							
							
							
							$check_qty_from_products     = DB::table('products')->select('qty')->where('id', $request->product_id)->first();
							$check_qty_from_rem_transfer = DB::table('transfer')->where('product_id', $request->product_id)->where('status', 'Approved')->get()->sum("qty");
							
							if(!empty($check_qty_from_products->qty) || !empty($check_qty_from_rem_transfer)){
								$remaining_product = $check_qty_from_products->qty - $check_qty_from_rem_transfer;
							}
							
							if($fill_qty <= $remaining_product){	
								$inputs  = $request->only('product_id','transfer_from','transfer_to','qty'); 

								
								$transfer_res = DB::table('transfer')->insertGetId($inputs); 
								
								if($transfer_res) {
									return $this->returnResponse(200, true, "Transfer Added Successfully"); 
								} else {
									return $this->returnResponse(200, false, "Something Went Wrong !"); 
								}
							}
							else{
								if($remaining_product == 0){
									$err_msg = "Product Out Of Stock";
								}
								else{
									$err_msg = "$remaining_product Products Available";
								}
								return $this->returnResponse(200, false, $err_msg); 
							}
							
						}
						else{
							return $this->returnResponse(200, false, "Quantity Required"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "Transfer To Required"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Transfer From Required"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Product ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function productTransferStatus(Request $request){
		try {
			
			
			if(!empty($request->transfer_id)){
				if(!empty($request->status)){
					$get_transfer_data = DB::table('transfer')->where('id', $request->transfer_id)->first();
					if(!empty($get_transfer_data)){
						
						$remaining_product       = 0;
						$check_qty_from_products = 0;
						$check_qty_from_transfer = 0;
						
						$check_qty_from_transfer = $get_transfer_data->qty;
						
			
						
						$check_qty_from_products     = DB::table('products')->select('qty')->where('id', $get_transfer_data->product_id)->first();
						$check_qty_from_rem_transfer = DB::table('transfer')->where('id', $get_transfer_data->id)->orWhere('product_id', $get_transfer_data->product_id)->where('status', 'Approved')->get()->sum("qty");
						
						if(!empty($check_qty_from_products->qty) || !empty($check_qty_from_rem_transfer)){
							$remaining_product = $check_qty_from_products->qty - $check_qty_from_rem_transfer;
						}
						
						//echo '<pre>'; print_r($remaining_product);die;
						if($remaining_product >= 0){	
							
							$transfer_res =DB::table('transfer')->where('id', $request->transfer_id)->update(['status' => $request->status]);
							
							if($transfer_res) {
								return $this->returnResponse(200, true, "Status Update Successfully"); 
							} else {
								return $this->returnResponse(200, false, "Already Updated"); 
							}
								
							
						}
						else{
							if($remaining_product <= 0){
								$err_msg = "Product more than stock";
							}
							else{
								$err_msg = "$remaining_product Products Available";
							}
							return $this->returnResponse(200, false, $err_msg); 
						}
		
						
						
					}
					else{
						return $this->returnResponse(200, false, "No Data Found"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Status Required"); 
				}				
			}
			else{
				return $this->returnResponse(200, false, "Transfer ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
