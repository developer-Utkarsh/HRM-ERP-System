<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Input;
use DB;

class CategoryController extends Controller
{   
	
	public function getCategory(Request $request){
		try {
			$allCategory = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
			
			$responseArray = array();
			if(count($allCategory) > 0){
				foreach($allCategory as $key=>$val){
					$responseArray[$key]['id'] = $val->id;
					$responseArray[$key]['name'] = $val->name;
				}
				
				$data['category'] = $responseArray;
				return $this->returnResponse(200, true, "Category Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Category Not Found"); 
			}
			
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function getsubCategory(Request $request){
		try {
			if(!empty($request->cat_id)){
				$allSubCategory = Category::where('parent', $request->cat_id)->where('is_deleted', '0')->orderBy('id', 'desc')->get();
			
				$responseArray = array();
				if(count($allSubCategory) > 0){
					foreach($allSubCategory as $key=>$val){
						$responseArray[$key]['id'] = $val->id;
						$responseArray[$key]['name'] = $val->name;
					}
					
					$data['sub_category'] = $responseArray;
					return $this->returnResponse(200, true, "Sub Category Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Sub Category Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Category ID Required"); 
			}
			
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
