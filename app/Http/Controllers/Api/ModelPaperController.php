<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\CourseSubjectRelation;
use App\Modelpaper;
use App\ModelPaperRelation;
use App\ModelPaperRelationStatusHistory;
use Hash;
use Input;
use DB;
use Auth;

class ModelPaperController extends Controller
{
    
    public function modelPaperList(Request $request)
    {
        try {
			$data = array();$whereCond1 = ' 1=1';$whereCond = ' 1=1';
			
			if (!empty($request->user_id)){
				$check_user = DB::table('users')
								->select('id','role_id','department_type')
								->where('id', $request->user_id)
								->where('status', 1)
								->first();
				if(!empty($check_user->id)){
					$whereCond1 = '';$selt = '';$mp_details =array();			
					$selt .= "model_paper.name";
					if($check_user->role_id == 21 && $check_user->department_type == 4){
						 $whereCond1 .= (" model_paper_relations.content_writer_id = $request->user_id");
						 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'content_writer' as assigned";
					}
					if($check_user->role_id == 2){
						 $whereCond1 .= (" model_paper_relations.faculty_id = $request->user_id");
						 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
					}
					if($check_user->role_id == 21 && $check_user->department_type == 5){
						 $whereCond1 .= (" model_paper_relations.typist_id = $request->user_id");
						 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
					}	
					if(!empty($whereCond1)){
						$mp_details = DB::table('model_paper')
										->selectRaw($selt)
										->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
										->whereRaw($whereCond1)
										->groupBy('model_paper.name')
										->get();
					}
					
					
					$whereCond2 = '';$selt1 = '';$mp_details1 =array();	
					
					$selt1      .= "model_paper.name";
					if($check_user->role_id == 21 && $check_user->department_type == 4){
						$whereCond2 .= (" model_paper_relations.proof_reader_id = $request->user_id");
						$selt1      .= ",model_paper_relations.id as model_paper_relations_id,'proof_reader' as assigned";
					}
					if($check_user->role_id == 2){
						 $whereCond2 .= (" model_paper_relations.faculty_id = $request->user_id");
						 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
					}
					if($check_user->role_id == 21 && $check_user->department_type == 5){
						 $whereCond2 .= (" model_paper_relations.typist_id = $request->user_id");
						 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
					}
					if(!empty($whereCond2)){
						$mp_details1 = DB::table('model_paper')
										->selectRaw($selt1)
										->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
										->whereRaw($whereCond2)
										->groupBy('model_paper.name')
										->get();
					}		

					if(count($mp_details)>0 && count($mp_details1) > 0){					
						$mp_details_merge = array_merge($mp_details->toArray(), $mp_details1->toArray());

						$ids = array_column($mp_details_merge, 'name');  
						$ids = array_unique($ids);  
						$mp_details_details = array_filter($mp_details_merge, function ($key, $value) use ($ids) {
							return in_array($value, array_keys($ids));
						}, ARRAY_FILTER_USE_BOTH);
						
						if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'content_writer'){
							$whereCond .= (" AND model_paper_relations.content_writer_id = $request->user_id");
						}
						
						if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'proof_reader'){
							$whereCond .= (" AND model_paper_relations.proof_reader_id = $request->user_id");
						}
						
						if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'faculty'){
							$whereCond .= (" AND model_paper_relations.faculty_id = $request->user_id");
						}
						
						if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'typist'){
							$whereCond .= (" AND model_paper_relations.typist_id = $request->user_id");
						}
					}
					else{
						return $this->returnResponse(200, false, "Model paper not found for this user");
					}

					//echo '<pre>'; print_r($mp_details_details);die;
				}				
				else{
					return $this->returnResponse(200, false, "User Not Active Otherwise User Not Found");
				}				
			}
			
			if (!empty($request->course_name)){
				$whereCond .= (" AND course.name Like '%$request->course_name%'");
			}
			
			if ($request->status != ''){
				$whereCond .= (" AND model_paper.status = $request->status");
			}
			else{
				$whereCond .= (" AND model_paper.status = 1");
			}
			$whereCond .= (" AND model_paper.is_deleted = '0'");
			
			$model_paper_list = DB::table('model_paper')
								->select('model_paper.id','model_paper.name as model_paper_name','model_paper.end_date','model_paper.status','model_paper.created_at','course.name as course_name')
								->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
								->join('course', 'course.id', '=', 'model_paper.course_id')
								->whereRaw($whereCond)
								->groupBy('model_paper_relations.model_paper_id')
								->get();
								
			$data['model-paper'] = $model_paper_list;
			return $this->returnResponse(200, true, "Model Paper Details", $data);

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
	
	public function subModelPaperList(Request $request){
		try {  
			$data = array();$whereCond = ' 1=1';$whereCond2 = '';
			if(!empty($request->id)){
				
				if(!empty($request->user_id)){
					$check_user = DB::table('users')
									->select('id','role_id','department_type')
									->where('id', $request->user_id)
									->where('status', 1)
									->first();
					if(!empty($check_user->id)){
						$whereCond1 = '';$selt = '';$mp_details =array();			
						$selt .= "model_paper.name";
						if($check_user->role_id == 21 && $check_user->department_type == 4){
							 $whereCond1 .= (" model_paper_relations.content_writer_id = $request->user_id");
							 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'content_writer' as assigned";
						}
						if($check_user->role_id == 2){
							 $whereCond1 .= (" model_paper_relations.faculty_id = $request->user_id");
							 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
						}
						if($check_user->role_id == 21 && $check_user->department_type == 5){
							 $whereCond1 .= (" model_paper_relations.typist_id = $request->user_id");
							 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
						}	
						if(!empty($whereCond1)){
							$mp_details = DB::table('model_paper')
											->selectRaw($selt)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond1)
											->groupBy('model_paper.name')
											->get();
						}
						
						
						$whereCond2 = '';$selt1 = '';$mp_details1 =array();	
						
						$selt1      .= "model_paper.name";
						if($check_user->role_id == 21 && $check_user->department_type == 4){
							$whereCond2 .= (" model_paper_relations.proof_reader_id = $request->user_id");
							$selt1      .= ",model_paper_relations.id as model_paper_relations_id,'proof_reader' as assigned";
						}
						if($check_user->role_id == 2){
							 $whereCond2 .= (" model_paper_relations.faculty_id = $request->user_id");
							 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
						}
						if($check_user->role_id == 21 && $check_user->department_type == 5){
							 $whereCond2 .= (" model_paper_relations.typist_id = $request->user_id");
							 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
						}
						if(!empty($whereCond2)){
							$mp_details1 = DB::table('model_paper')
											->selectRaw($selt1)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond2)
											->groupBy('model_paper.name')
											->get();
						}		

						if(count($mp_details)>0 && count($mp_details1) > 0){					
							$mp_details_merge = array_merge($mp_details->toArray(), $mp_details1->toArray());

							$ids = array_column($mp_details_merge, 'name');  
							$ids = array_unique($ids);  
							$mp_details_details = array_filter($mp_details_merge, function ($key, $value) use ($ids) {
								return in_array($value, array_keys($ids));
							}, ARRAY_FILTER_USE_BOTH);
							
							if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'content_writer'){
								$whereCond .= (" AND model_paper_relations.content_writer_id = $request->user_id");
							}
							
							if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'proof_reader'){
								$whereCond .= (" AND model_paper_relations.proof_reader_id = $request->user_id");
							}
							
							if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'faculty'){
								$whereCond .= (" AND model_paper_relations.faculty_id = $request->user_id");
							}
							
							if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'typist'){
								$whereCond .= (" AND model_paper_relations.typist_id = $request->user_id");
							}
						}
						else{
							return $this->returnResponse(200, false, "Model paper not found for this user");
						}

						//echo '<pre>'; print_r($mp_details_details);die;
					}				
					else{
						return $this->returnResponse(200, false, "User Not Active Otherwise User Not Found");
					}
				}
				
				
				if (!empty($request->status) && $request->status == 'Completed'){
					$whereCond .= (" AND model_paper_relations.status_by_content_writer = 'Approved' AND model_paper_relations.status_by_proof_reader = 'Approved' AND model_paper_relations.status_by_faculty = 'Approved' AND model_paper_relations.status_by_typist = 'Approved'");
				}
				if (!empty($request->status) && $request->status == 'In Progress'){ 
					$whereCond .= (" AND (((model_paper_relations.status_by_content_writer = 'Pending' OR model_paper_relations.status_by_content_writer = 'Approved') AND model_paper_relations.status_by_content_writer != 'Approved')  OR ((model_paper_relations.status_by_proof_reader = 'Pending' OR model_paper_relations.status_by_proof_reader = 'Approved') AND model_paper_relations.status_by_proof_reader != 'Approved') OR ((model_paper_relations.status_by_faculty = 'Pending' OR model_paper_relations.status_by_faculty = 'Approved') AND model_paper_relations.status_by_faculty != 'Approved') OR ((model_paper_relations.status_by_typist = 'Pending' OR model_paper_relations.status_by_typist = 'Approved') AND model_paper_relations.status_by_typist != 'Approved'))"); 
					
					
				}
				
				$whereCond .= (" AND model_paper_relations.model_paper_id = $request->id");
				
				
				$sub_model_paper_list = DB::table('model_paper')
											->select(DB::raw("model_paper.id as model_paper_id,model_paper.name as model_paper_name,model_paper.course_id as model_paper_course_id,course.name as course_name,model_paper_relations.id as sub_model_paper_id,model_paper_relations.model_paper_name as sub_model_paper_name,model_paper_relations.subject_id,model_paper_relations.created_at,(CASE WHEN model_paper_relations.status_by_content_writer='Approved' AND model_paper_relations.status_by_proof_reader='Approved' AND model_paper_relations.status_by_faculty='Approved' AND model_paper_relations.status_by_typist='Approved' THEN 'Completed' ELSE 'In Progress' END) as status"))
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->join('course', 'course.id', '=', 'model_paper.course_id')
											->whereRaw($whereCond)
											->groupBy('model_paper_name')
											//->orderBy('model_paper_relations.id','desc')
											->get();
				//echo '<pre>'; print_r($sub_model_paper_list);die;							
				$sub_model_array1 = array();										
				if(count($sub_model_paper_list) > 0){
								
					foreach($sub_model_paper_list as $key=>$sub_model_paper_list_value){	
						$whereCond2 = (" model_paper_relations.model_paper_id = $sub_model_paper_list_value->model_paper_id AND model_paper_relations.model_paper_name = '$sub_model_paper_list_value->sub_model_paper_name'");
						$sub_model_paper_list_res = DB::table('model_paper')
														->select('subject.name as subject_name','faculty.name as faculty_name','content_writer.name as content_writer_name','proof_reader.name as proof_reader_name','typist.name as typist_name','model_paper_relations.*')
														->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
														->join('subject', 'subject.id', '=', 'model_paper_relations.subject_id')
														->join('users as faculty', 'faculty.id', '=', 'model_paper_relations.faculty_id')
														->join('users as content_writer', 'content_writer.id', '=', 'model_paper_relations.content_writer_id')
														->join('users as proof_reader', 'proof_reader.id', '=', 'model_paper_relations.proof_reader_id')
														->join('users as typist', 'typist.id', '=', 'model_paper_relations.typist_id')
														->whereRaw($whereCond2)
														->get();
														
						//echo '<pre>'; print_r($sub_model_paper_list_res);die;							
										   			
						if(count($sub_model_paper_list_res) > 0){
							$sub_model_array2 = array();
							foreach($sub_model_paper_list_res as $key1=>$sub_model_paper_list_res_val){
								
								$content_writer_remark = '';$proof_reader_remark = '';$faculty_remark = '';$typist_remark='';
								
								$get_remark = DB::table('model_paper_status_history')->select(DB::raw("id,(CASE WHEN `type`='Content Writer' THEN remark ELSE '' END) as content_writer_remark, (CASE WHEN `type`='Proof Reader' THEN remark ELSE '' END) as proof_reader_remark, (CASE WHEN `type`='Faculty' THEN remark ELSE '' END) as faculty_remark, (CASE WHEN `type`='Typist' THEN remark ELSE '' END) as typist_remark"))->where('model_paper_relations_id',$sub_model_paper_list_res_val->id)->whereNotNull('remark')->get();
								

								if(count($get_remark) > 0){
									$content_writer_s_no = 1;$proof_reader_s_no = 1;$faculty_s_no = 1;$typist_s_no = 1;
									foreach($get_remark as $get_remark_value){
										if(!empty($get_remark_value->content_writer_remark)){
											$content_writer_remark .= ' '.$content_writer_s_no.'.'.$get_remark_value->content_writer_remark.', ';
											$content_writer_s_no++;
										}
										if(!empty($get_remark_value->proof_reader_remark)){
											$proof_reader_remark .= ' '.$proof_reader_s_no.'.'.$get_remark_value->proof_reader_remark.', ';
											$proof_reader_s_no++;
										}
										if(!empty($get_remark_value->faculty_remark)){
											$faculty_remark .= ' '.$faculty_s_no.'.'.$get_remark_value->faculty_remark.', ';
											$faculty_s_no++;
										}
										if(!empty($get_remark_value->typist_remark)){
											$typist_remark .= ' '.$typist_s_no.'.'.$get_remark_value->typist_remark.', ';
											$typist_s_no++;
										}
									}
								}
								
								
								$modelDetails['subject_name']             = $sub_model_paper_list_res_val->subject_name;
								$modelDetails['faculty_name']             = $sub_model_paper_list_res_val->faculty_name;
								$modelDetails['content_writer_name']      = $sub_model_paper_list_res_val->content_writer_name;
								$modelDetails['proof_reader_name']        = $sub_model_paper_list_res_val->proof_reader_name;
								$modelDetails['typist_name']              = $sub_model_paper_list_res_val->typist_name;
								$modelDetails['sub_model_paper_id']       = $sub_model_paper_list_res_val->id;
								$modelDetails['model_paper_id']           = $sub_model_paper_list_res_val->model_paper_id;
								$modelDetails['model_paper_name']         = $sub_model_paper_list_res_val->model_paper_name;
								$modelDetails['subject_id']               = $sub_model_paper_list_res_val->subject_id;
								$modelDetails['faculty_id']               = $sub_model_paper_list_res_val->faculty_id;
								$modelDetails['content_writer_id']        = $sub_model_paper_list_res_val->content_writer_id;
								$modelDetails['proof_reader_id']          = $sub_model_paper_list_res_val->proof_reader_id;
								$modelDetails['typist_id']                = $sub_model_paper_list_res_val->typist_id;
								$modelDetails['no_of_question']           = $sub_model_paper_list_res_val->no_of_question;
								$modelDetails['from_question']            = $sub_model_paper_list_res_val->from_question;
								$modelDetails['to_question']              = $sub_model_paper_list_res_val->to_question;
								$modelDetails['content_writer_document']  = $sub_model_paper_list_res_val->content_writer_document;
								$modelDetails['content_writer_remark']    = rtrim($content_writer_remark, ", ");
								$modelDetails['status_by_content_writer'] = $sub_model_paper_list_res_val->status_by_content_writer;
								$modelDetails['proof_reader_document']    = $sub_model_paper_list_res_val->proof_reader_document;
								$modelDetails['proof_reader_remark']      = rtrim($proof_reader_remark, ", ");
								$modelDetails['status_by_proof_reader']   = $sub_model_paper_list_res_val->status_by_proof_reader;
								$modelDetails['faculty_document']         = $sub_model_paper_list_res_val->faculty_document;
								$modelDetails['faculty_remark']           = rtrim($faculty_remark, ", ");
								$modelDetails['status_by_faculty']        = $sub_model_paper_list_res_val->status_by_faculty;
								$modelDetails['typist_document']          = $sub_model_paper_list_res_val->typist_document;
								$modelDetails['typist_remark']            = rtrim($typist_remark, ", ");
								$modelDetails['status_by_typist']         = $sub_model_paper_list_res_val->status_by_typist;
								$modelDetails['created_at']               = $sub_model_paper_list_res_val->created_at;
								
								$sub_model_array2[$key1] = $modelDetails;
							}
						}
						$sub_model_array1[$key]['sub_model_paper_name'] = $sub_model_paper_list_value->sub_model_paper_name;					
						$sub_model_array1[$key]['sub_model_paper_id']   = $sub_model_paper_list_value->sub_model_paper_id;	
						$sub_model_array1[$key]['status']               = $sub_model_paper_list_value->status;					
						$sub_model_array1[$key]['created_date']         = $sub_model_paper_list_value->created_at;	
						$sub_model_array1[$key]['subject']              = $sub_model_array2; 	
					}
					$data['sub-model-paper'] = $sub_model_array1;
					return $this->returnResponse(200, true, "Sub Model Paper Details", $data); 
				}
				else{
					return $this->returnResponse(200, true, "Sub Model Paper Not Found", $data);
				}		
			
			}
			else{
				return $this->returnResponse(200, false, "Model Paper ID Required");
			}

        } catch (\Illuminate\Database\QueryException $ex){
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } 
	}
	
	public function uploadRemark(Request $request){
		try {  
			if(!empty($request->user_id)){
				if(!empty($request->sub_model_papar_id)){
					if(!empty($request->remark)){
						$check_user = DB::table('users')
										->select('id','role_id','department_type')
										->where('id', $request->user_id)
										->where('status', 1)
										->first();
						
						if(!empty($check_user->id)){				
							$whereCond = ' 1=1';$selt = '';			
							$selt .= "model_paper.*";
							if($check_user->role_id == 21 && $check_user->department_type == 4){
								 $whereCond .= (" AND model_paper_relations.content_writer_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'content_writer' as assigned";
							}
							if($check_user->role_id == 2){
								 $whereCond .= (" AND model_paper_relations.faculty_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
							}
							if($check_user->role_id == 21 && $check_user->department_type == 5){
								 $whereCond .= (" AND model_paper_relations.typist_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
							}	
							$whereCond .= (" AND model_paper_relations.id = $request->sub_model_papar_id");
							$mp_details = DB::table('model_paper')
											->selectRaw($selt)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond)
											->get();

						  
							
							$whereCond1 = ' 1=1';$selt1 = '';
							
							$selt1      .= "model_paper.*";
							if($check_user->role_id == 21 && $check_user->department_type == 4){
								$whereCond1 .= (" AND model_paper_relations.proof_reader_id = $request->user_id");
								$selt1      .= ",model_paper_relations.id as model_paper_relations_id,'proof_reader' as assigned";
							}
							if($check_user->role_id == 2){
								 $whereCond1 .= (" AND model_paper_relations.faculty_id = $request->user_id");
								 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
							}
							if($check_user->role_id == 21 && $check_user->department_type == 5){
								 $whereCond1 .= (" AND model_paper_relations.typist_id = $request->user_id");
								 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
							}
							$whereCond1 .= (" AND model_paper_relations.id = $request->sub_model_papar_id");
							$mp_details1 = DB::table('model_paper')
											->selectRaw($selt1)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond1)
											->get();
							 		
											
							$mp_details_merge = array_merge($mp_details->toArray(), $mp_details1->toArray());
		
							$ids = array_column($mp_details_merge, 'name');  
							$ids = array_unique($ids);  
							$mp_details_details = array_filter($mp_details_merge, function ($key, $value) use ($ids) {
								return in_array($value, array_keys($ids));
							}, ARRAY_FILTER_USE_BOTH);
		
							//echo '<pre>'; print_r($mp_details_details);die;
							
							if(count($mp_details_details) > 0){
								$remark_res = '';
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'content_writer'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('content_writer_id',$request->user_id)->update([ 'content_writer_remark'=> $request->remark]);
									
									$typ = 'Content Writer';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'proof_reader'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('proof_reader_id',$request->user_id)->update([ 'proof_reader_remark'=> $request->remark]);
									
									$typ = 'Proof Reader';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'faculty'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('faculty_id',$request->user_id)->update([ 'faculty_remark'=> $request->remark]);
									
									$typ = 'Faculty';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'typist'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('typist_id',$request->user_id)->update([ 'typist_remark'=> $request->remark]);
									
									$typ = 'Typist';
								}
								
								if($remark_res){
									ModelPaperRelationStatusHistory::create([ 
										'user_id'                  => $request->user_id,
										'model_paper_relations_id' => $request->sub_model_papar_id,
										'remark'                   => $request->remark,
										'type'                     => $typ
									]);
									return $this->returnResponse(200, true, "Remark Upload Successfully");
								}
								else{
									return $this->returnResponse(200, false, "Something went wrong.");
								}
							}
							else{
								return $this->returnResponse(200, false, "No Model Paper Found");
							}
							
						}				
						else{
							return $this->returnResponse(200, false, "User Not Active");
						}
					}
					else{
						return $this->returnResponse(200, false, "Remark ID Required");
					}
				}
				else{
					return $this->returnResponse(200, false, "Sub Model Paper ID Required");
				}
			}
			else{
				return $this->returnResponse(200, false, "User ID Required");
			}

        } catch (\Illuminate\Database\QueryException $ex){
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
	
	public function uploadStatus(Request $request){
		try {//echo '<pre>'; print_r('dd');die;
			if(!empty($request->user_id)){
				if(!empty($request->sub_model_papar_id)){
					if(!empty($request->status)){
						$check_user = DB::table('users')
										->select('id','role_id','department_type')
										->where('id', $request->user_id)
										->where('status', 1)
										->first();
						
						if(!empty($check_user->id)){				
							$whereCond = ' 1=1';$selt = '';			
							$selt .= "model_paper.*";
							if($check_user->role_id == 21 && $check_user->department_type == 4){
								 $whereCond .= (" AND model_paper_relations.content_writer_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'content_writer' as assigned";
							}
							if($check_user->role_id == 2){
								 $whereCond .= (" AND model_paper_relations.faculty_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
							}
							if($check_user->role_id == 21 && $check_user->department_type == 5){
								 $whereCond .= (" AND model_paper_relations.typist_id = $request->user_id");
								 $selt      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
							}	
							$whereCond .= (" AND model_paper_relations.id = $request->sub_model_papar_id");
							$mp_details = DB::table('model_paper')
											->selectRaw($selt)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond)
											->get();

						  
							
							$whereCond1 = ' 1=1';$selt1 = '';
							
							$selt1      .= "model_paper.*";
							if($check_user->role_id == 21 && $check_user->department_type == 4){
								$whereCond1 .= (" AND model_paper_relations.proof_reader_id = $request->user_id");
								$selt1      .= ",model_paper_relations.id as model_paper_relations_id,'proof_reader' as assigned";
							}
							if($check_user->role_id == 2){
								 $whereCond1 .= (" AND model_paper_relations.faculty_id = $request->user_id");
								 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'faculty' as assigned";
							}
							if($check_user->role_id == 21 && $check_user->department_type == 5){
								 $whereCond1 .= (" AND model_paper_relations.typist_id = $request->user_id");
								 $selt1      .= ",model_paper_relations.id as model_paper_relations_id,'typist' as assigned";
							}
							$whereCond1 .= (" AND model_paper_relations.id = $request->sub_model_papar_id");
							$mp_details1 = DB::table('model_paper')
											->selectRaw($selt1)
											->join('model_paper_relations', 'model_paper_relations.model_paper_id', '=', 'model_paper.id')
											->whereRaw($whereCond1)
											->get();
							 		
											
							$mp_details_merge = array_merge($mp_details->toArray(), $mp_details1->toArray());
		
							$ids = array_column($mp_details_merge, 'name');  
							$ids = array_unique($ids);  
							$mp_details_details = array_filter($mp_details_merge, function ($key, $value) use ($ids) {
								return in_array($value, array_keys($ids));
							}, ARRAY_FILTER_USE_BOTH);
		
							//echo '<pre>'; print_r($mp_details_details);die;
							
							if(count($mp_details_details) > 0){
								$remark_res = '';
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'content_writer'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('content_writer_id',$request->user_id)->update([ 'status_by_content_writer'=> $request->status]);
									
									$typ = 'Content Writer';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'proof_reader'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('proof_reader_id',$request->user_id)->update([ 'status_by_proof_reader'=> $request->status]);
									
									$typ = 'Proof Reader';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'faculty'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('faculty_id',$request->user_id)->update([ 'status_by_faculty'=> $request->status]);
									
									$typ = 'Faculty';
								}
								
								if(!empty($mp_details_details[0]->assigned) && $mp_details_details[0]->assigned == 'typist'){
									$remark_res = ModelPaperRelation::where('id', $request->sub_model_papar_id)->where('typist_id',$request->user_id)->update([ 'status_by_typist'=> $request->status]);
									
									$typ = 'Typist';
								}
								
								if($remark_res){
									ModelPaperRelationStatusHistory::create([ 
										'user_id'                  => $request->user_id,
										'model_paper_relations_id' => $request->sub_model_papar_id,
										'status'                   => $request->status,
										'type'                     => $typ
									]);
									return $this->returnResponse(200, true, "Status Upload Successfully");
								}
								else{
									return $this->returnResponse(200, false, "Something went wrong.");
								}
							}
							else{
								return $this->returnResponse(200, false, "No Model Paper Found");
							}
							
						}				
						else{
							return $this->returnResponse(200, false, "User Not Active");
						}
					}
					else{
						return $this->returnResponse(200, false, "Status Required");
					}
				}
				else{
					return $this->returnResponse(200, false, "Sub Model Paper ID Required");
				}
			}
			else{
				return $this->returnResponse(200, false, "User ID Required");
			}

        } catch (\Illuminate\Database\QueryException $ex){
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }
	}
}
