<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ApiNotification;
use Input;
use DB;

class KnowledgeBasedController extends Controller
{   
    public function getKbCategory(Request $request)
    {
    	try {
			$allCategory = DB::table('knowledge_based_category')->where('status', 'Active')->where('is_deleted', '0')->get();
			
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
	
	public function getKnowledgeBased(Request $request){
		try {
			if(!empty($request->emp_id)){
				
				$whereCond = ' 1=1 ';
				
				if(!empty($request->cat_id)){
					$whereCond .= ' AND knowledge_based.cat_id = "'.$request->cat_id.'"';
				}
				
			    $whereCond .= ' AND ( knowledge_based.emp_id = "'.$request->emp_id.'" OR ( knowledge_based.emp_id != "'.$request->emp_id.'" AND knowledge_based.status = "Approved")) AND knowledge_based.is_deleted="0"';
				//AND knowledge_based.status="Approved"
				$knowledgeBasedRes = DB::table('knowledge_based')->select('knowledge_based.*','knowledge_based_category.name as category_name')->leftJoin('knowledge_based_category', 'knowledge_based.cat_id', '=', 'knowledge_based_category.id')->whereRaw($whereCond)->get();
				
				if(count($knowledgeBasedRes) > 0){
					
					
					$data['knowledge_based'] = $knowledgeBasedRes;
					return $this->returnResponse(200, true, "Knowledge Based Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Knowledge Based Not Found"); 
				}
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
	
	public function addKnowledgeBased(Request $request){
		try {
			
			if(!empty($request->emp_id)){
				if(!empty($request->cat_id)){
					if(!empty($request->title)){
						$knowledgeResult = DB::table('knowledge_based')->insertGetId([
												'emp_id'         => $request->emp_id,
												'cat_id'         => $request->cat_id,
												'title'          => $request->title,
												'description'    => $request->description,
												'reference_link' => $request->reference_link,
												'status'         => $request->status,
												'created_at'     => date('Y-m-d H:i:s'),
											]);
						
						
						if($knowledgeResult){
							return $this->returnResponse(200, true, "Knowledge Based Successfully Added");
						}
						else{
							return $this->returnResponse(200, false, "Something is wrong"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "Title Required"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Category ID Required"); 
				}
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
	
	public function getTrainingCategory(Request $request)
    {
    	try {
			$allCategory = DB::table('training_video_category')->where('status', 'Active')->where('is_deleted', '0')->get();
			
			$responseArray = array();
			if(count($allCategory) > 0){
				foreach($allCategory as $key=>$val){
					$responseArray[$key]['id'] = $val->id;
					$responseArray[$key]['name'] = $val->name;
				}
				
				$data['category'] = $responseArray;
				return $this->returnResponse(200, true, "Training Video Category Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Training Video Category Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function getTrainingVideo(Request $request){
		try {
				
			$whereCond = ' 1=1 ';
			$profile_path = asset('laravel/public/training_video_image_url/').'/';
			$training_pdf_path = asset('laravel/public/training_pdf/').'/';
			if(!empty($request->cat_id)){
				$whereCond .= ' AND training_video.cat_id = "'.$request->cat_id.'"';
			}
			// if(!empty($request->user_id)){
				// $whereCond .= ' AND training_video.user_id = "'.$request->user_id.'"';
			// }

			
			$whereCond .= ' AND training_video.status="Active" AND training_video.is_deleted="0"';
			
			$trainingVideoRes = DB::table('training_video')->select('training_video.id','training_video.title',DB::raw('DATE_FORMAT(training_video.date, "%d-%m-%Y") as date'),DB::raw("CONCAT('$profile_path', training_video.image_url) as image_url"),'training_video.description','training_video.created_at',DB::Raw("CONCAT(training_video.video_url,training_video.video_id) AS video_url"),DB::raw("CONCAT('$training_pdf_path', training_video.pdf_url) as pdf_url"),'training_video_category.name as category_name','training_video.type')
			->leftJoin('training_video_category', 'training_video.cat_id', '=', 'training_video_category.id')
			->whereRaw($whereCond)->get();
			
			if(count($trainingVideoRes) > 0){
				$data['training_video'] = $trainingVideoRes;
				return $this->returnResponse(200, true, "Training Video Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Training Video Not Found"); 
			}
			
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function editKnowledgeBased(Request $request){
		try {
			if(!empty($request->id)){
				if(!empty($request->emp_id)){
					if(!empty($request->cat_id)){
						if(!empty($request->title)){
							$get_result = DB::table('knowledge_based')->where('id', $request->id)->first();
							if(!empty($get_result) && ($get_result->status == 'Pending' || $get_result->status == 'Reject')){
								$knowledgeResult = DB::table('knowledge_based')->where('id', $request->id)->update([
														'emp_id'         => $request->emp_id,
														'cat_id'         => $request->cat_id,
														'title'          => $request->title,
														'description'    => $request->description,
														'reference_link' => $request->reference_link,
														'status'         => $request->status,
														'created_at'     => date('Y-m-d H:i:s'),
													]);
								
								
								if($knowledgeResult){
									return $this->returnResponse(200, true, "Knowledge Based Successfully Update");
								}
								else{
									return $this->returnResponse(200, false, "Something is wrong"); 
								}
							}
							else{
								return $this->returnResponse(200, false, "Knowledge Based Are Approved"); 
							}
						}
						else{
							return $this->returnResponse(200, false, "Title Required"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "Category ID Required"); 
					}
				}
				else{
					return $this->returnResponse(200, false, "Employee ID Required"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

}
