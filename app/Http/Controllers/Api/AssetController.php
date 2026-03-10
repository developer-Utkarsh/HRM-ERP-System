<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Asset;
use App\AssignAsset;
use App\User;

class AssetController extends Controller
{   

	public function employeeAsset(Request $request){ 
        try {
		    if(!empty($request->emp_id)){
				
			$selectstatus  = $request->status;	
			$whereCond       = ' 1=1 ';
			
			if($selectstatus != ''){ 
				$whereCond .= " AND (assign_asset.is_accepted = '$selectstatus')";
			}
			
			$asset_data = Asset::select('products.name','assign_asset.*')
							->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
							->join('products', 'products.id', '=', 'asset.product_id')
							->where('assign_asset.emp_id', $request->emp_id)
							->whereRaw($whereCond)
							->get();
					
				if(count($asset_data) > 0){
					$get_asset = [];
					foreach($asset_data as $key => $asset){
						$temp['id']          = $asset->id;
						$temp['name']        = $asset->name;
						$temp['asset_id']    = $asset->asset_id;
						$temp['emp_id']      = $asset->emp_id;
						$temp['is_accepted'] = $asset->is_accepted;
						$temp['date']        = date('Y-m-d', strtotime($asset->created_at));

						$get_asset[] = $temp;
					}

					$data['asset_details'] = $get_asset;

					return $this->returnResponse(200, true, "Asset Details", $data);
					}else{
					return $this->returnResponse(200, false, "Asset Not Found");
				}				
				//echo '<pre>'; print_r($data);die;
			}
			else{
				 return $this->returnResponse(200, false, "Employee ID Required");
			}

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
	
 }
