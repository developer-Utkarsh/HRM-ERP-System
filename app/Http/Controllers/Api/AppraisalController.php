<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appraisal;
use App\AppraisalQuestions;
use App\User;
use Input;
use DB;
use App\NewTask;

class AppraisalController extends Controller
{
	public function questions(Request $request)
    {
        try{
            $user_id = $request->user_id;
            if(1){
				$today_date = date('Y-m-d');
				$appraisal = Appraisal::where('status', 'Active')->whereRaw("(date(from_date) <= '$today_date' and date(to_date) >= '$today_date')")->first();
				// print_r($appraisal); die;
				if(!empty($appraisal)){
					$ap_check = DB::table('appraisal_user')->where('user_id', $user_id)->where('appraisal_id', $appraisal->id)->get();
					if(count($ap_check) > 0){
						return $this->returnResponse(200, false, 'Already submited your appraisal.');exit;
					}
					$questions = AppraisalQuestions::where('status', 'Active')->get();
					if(!empty($questions)){
						$i = 0;
						$que_array = array();
						foreach($questions as $key=>$val){
							$data_array['id'] = $val->id;
							$data_array['question'] = $val->question;
							$data_array['hquestion'] = $val->hquestion;
							$que_array[] = $data_array;
						}	
						$data['appraisal_questions'][] = array('appraisal_id'=>"$appraisal->id","questions"=>$que_array);
						// $data['appraisal_questions'] = $que_array;
						return $this->returnResponse(200, true, "Appraisal Questions List", $data);
					}
					else{
						return $this->returnResponse(200, false, "Questions Not Found"); 
					}		
				}
				else{
					return $this->returnResponse(200, false, "No Appraisal.");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function questions_submit(Request $request)
    {
        try{
            $user_id = $request->user_id;
            $appraisal_id = $request->appraisal_id;
            if(isset($user_id) && !empty($user_id)){
				if(!empty($appraisal_id)){
					$user = User::where('id', $user_id)->first();
					if(!empty($user)){
						if($user->status == '1'){
							
							$questions = DB::table('appraisal_user')->where('user_id', $user_id)->where('appraisal_id', $appraisal_id)->get();
							if(count($questions)==0){
								if (is_array($request->answers) && !empty($request->answers)) {
									$saveData = array();
									$checkMarks = true;
									$overall_remark = $request->overall_remark;
									if(empty($overall_remark)){
										return $this->returnResponse(200, false, 'Overall Remark is required.');exit;	
									}
									foreach($request->answers as $answers){
										if(!empty($answers['remark'])){
											$remark = $answers['remark'];
										}
										else{
											$remark = "";
										}
										$categoryArray[] = $answers['id'];
										$categoryArray[] = $answers['marks'];
										$categoryArray[] = $remark;
										
										/* if(empty($answers['marks'])){
											$checkMarks = false;
										} */
										
										$saveData[] = array('user_id'=>$user_id,'appraisal_que_id'=>$answers['id'],'marks'=>$answers['marks'],'remark'=>$remark,'created_at'=>date('Y-m-d H:i:s'));
									}
									
									if(!empty($saveData)){
										$saveUser = array('user_id'=>$user_id,
															'appraisal_id'=>$appraisal_id,
															'overall_remark'=>$overall_remark,
															'date'=>date('Y-m-d')
															);
										$insert_id = DB::table('appraisal_user')->insertGetId($saveUser);
										$final_save = array();
										foreach($saveData as $val){
											$val['appraisal_user_id']=$insert_id;
											$final_save[] = $val;
										}
										DB::table('appraisal_user_que_ans')->insert($final_save);
										return $this->returnResponse(200, true, "Add Successfully");
									}
									else{
										return $this->returnResponse(200, false, 'Something went wrong1.');exit;
									}
									
								}
								else{
									return $this->returnResponse(200, false, 'Something went wrong.');exit;
								}
								
								
							}
							else{
								return $this->returnResponse(200, false, 'Already submited your appraisal.');exit;
							}
						}
						else{
							return $this->returnResponse(200, false, "User Not Active");
						}
					}
					else{
						return $this->returnResponse(200, false, "User Id Not Found");  
					}			
				}
				else{
					return $this->returnResponse(200, false, "Appraisal Id required");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function user_list(Request $request)
    {
        try{
            $user_id = $request->user_id;
            if(isset($user_id) && !empty($user_id)){
				$user = User::where('id', $user_id)->first();
				if(!empty($user)){
					if($user->status == '1'){
						$role_id = $user->role_id;
						
						$users = NewTask::getEmployeeByLogID($user_id,'with-login-id');
						$employeeArray = array();
						$usr = implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
						$employeeArray   = explode(',',$usr);
						
						$app_array = array();
						$user_ap = DB::table('appraisal_user')
									->select('appraisal_user.*','users.name as u_name','users.register_id','userdetails.degination','branches.name as branch_name')
									->leftJoin('users', 'users.id', '=', 'appraisal_user.user_id')
									->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
									->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
									->leftJoin('branches', 'branches.id', '=', 'userbranches.branch_id')
									->whereIn('appraisal_user.user_id', $employeeArray)
									->get();
						if(!empty($user_ap)){
							$user_array = array();
							foreach($user_ap as $key=>$val){
								$data_array['user_id'] = $user_id;
								$data_array['appraisal_user_id'] = $val->id;
								$data_array['emp_id'] = $val->register_id;
								$data_array['name'] = $val->u_name;
								$data_array['designation'] = $val->degination;
								$data_array['branch'] = $val->branch_name;
								$data_array['date'] = $val->date;
								$data_array['status'] = $val->status;
								$app_array[] = $data_array;
							}	
							$data['appraisal_users'] = $app_array;
							return $this->returnResponse(200, true, "Appraisal Users List", $data);
						}
						else{
							return $this->returnResponse(200, false, "Appraisal Users Not Found"); 
						}
					}
					else{
						return $this->returnResponse(200, false, "User Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "User Id Not Found");  
				} 
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function edit(Request $request)
    {
        try{
            $user_id = $request->user_id;
            $appraisal_user_id = $request->appraisal_user_id;
			$user = User::where('id', $user_id)->first();
            if(!empty($user)){
				$role_id = $user->role_id;
				$is_department_head = 0;
				if($role_id==21){
					$is_department_head  = 1;
				}
				$user_ap = DB::table('appraisal_user')->where('id', $appraisal_user_id)->first();
				if(!empty($user_ap)){
					$user_ap_que = DB::table('appraisal_user_que_ans')
									->select('appraisal_user_que_ans.*','appraisal_questions.question')
									->leftJoin('appraisal_questions', 'appraisal_questions.id', '=', 'appraisal_user_que_ans.appraisal_que_id')
									->where('appraisal_user_que_ans.appraisal_user_id', $user_ap->id)
									->get();
					if(!empty($user_ap_que)){
						$ans_array = array();
						foreach($user_ap_que as $key=>$val){
							$data_array['id'] = $val->id;
							$data_array['question'] = $val->question;
							$data_array['marks'] = $val->marks;
							$data_array['remark'] = $val->remark;
							$data_array['head_marks'] = $val->head_marks;
							$data_array['head_remark'] = $val->head_remark;
							$ans_array[] = $data_array;
						}
						$full_res = array(
										'appraisal_user_id'=>$appraisal_user_id,
										'is_submitted'=>$user_ap->is_submitted,
										'is_department_head'=>$is_department_head,
										'appraisal_id'=>$user_ap->appraisal_id,
										'overall_remark'=>$user_ap->overall_remark,
										'head_overall_remark'=>$user_ap->head_overall_remark,
										'status'=>$user_ap->status,
										'date'=>$user_ap->date,
										"answers"=>$ans_array										
										);						
						$data['appraisal_answers'][] = $full_res;
						return $this->returnResponse(200, true, "Appraisal Submit List", $data);
					}
					else{
						return $this->returnResponse(200, false, "Appraisal List Not Found"); 
					}		
				}
				else{
					return $this->returnResponse(200, false, "No Appraisal.");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	
	public function edit_submit(Request $request)
    {
        try{
            $user_id = $request->user_id;
            $appraisal_user_id = $request->appraisal_user_id;
			$user = User::where('id', $user_id)->first();
            if(!empty($user)){
				$role_id = $user->role_id;
				$is_department_head = 0;
				if($role_id==21){
					
				}
				else{
					return $this->returnResponse(200, false, "You are not Department Head.");  
				}
				$head_overall_remark = $request->head_overall_remark;
				$user_ap = DB::table('appraisal_user')->where('id', $appraisal_user_id)->first();
				if(!empty($user_ap)){
					
					if($user_ap->is_submitted==2){
						$is_submitted = 3;
					}
					else if($user_ap->is_submitted==0){
						$is_submitted = 1;
					}
					else{
						return $this->returnResponse(200, false, 'Something went wrong 1.');exit;
					}
					/* if(!empty($user_ap->head_id)){
						return $this->returnResponse(200, false, 'Already Submitted.');exit;
					} */
					$update_save = DB::table('appraisal_user')->where('id', $appraisal_user_id)->update(
										['head_overall_remark' => $head_overall_remark,'head_date' => date('Y-m-d'),'head_id' => $user_id,'is_submitted'=>$is_submitted]
										);
					if(!empty($update_save)){
						if (is_array($request->answers) && !empty($request->answers)) {
							$saveData = array();
							$checkMarks = true;
							$overall_remark = $request->overall_remark;
							foreach($request->answers as $answers){
								if(!empty($answers['head_remark'])){
									$head_remark = $answers['head_remark'];
								}
								else{
									$head_remark = "";
								}
								$head_marks = $answers['head_marks'];
								DB::table('appraisal_user_que_ans')->where('id', $answers['id'])->update(
										['head_marks' => $head_marks,'head_remark' => $head_remark]
										);
							}
							return $this->returnResponse(200, true, "Update successsfully");
							
						}
						else{
							return $this->returnResponse(200, false, 'Something went wrong.');exit;
						}
						
					}
					else{
						return $this->returnResponse(200, false, "Something went wrong."); 
					}		
				}
				else{
					return $this->returnResponse(200, false, "No Appraisal. Please try again.");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function need_discussed(Request $request)
    {
        try{
            $user_id = $request->user_id;
            $appraisal_user_id = $request->appraisal_user_id;
			$user = User::where('id', $user_id)->first();
            if(!empty($user)){
				$role_id = $user->role_id;
				$is_department_head = 0;
				/* if($role_id==21){
					
				}
				else{
					return $this->returnResponse(200, false, "You are not Department Head.");  
				} */
				$user_ap = DB::table('appraisal_user')->where('id', $appraisal_user_id)->first();
				if(!empty($user_ap)){
					if($user_ap->is_submitted==1){
						$update_save = DB::table('appraisal_user')->where('id', $appraisal_user_id)->update(['is_submitted' => 2]);
						if(!empty($update_save)){
							return $this->returnResponse(200, true, "Send notification successsfully");
						}
						else{
							return $this->returnResponse(200, false, "Something went wrong."); 
						}		
					}
					else{
						return $this->returnResponse(200, false, "Please wait for department head response."); 
					}
				}
				else{
					return $this->returnResponse(200, false, "No Appraisal. Please try again.");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function employee_accept(Request $request)
    {
        try{
            $user_id = $request->user_id;
            $appraisal_user_id = $request->appraisal_user_id;
			$user = User::where('id', $user_id)->first();
            if(!empty($user)){
				$role_id = $user->role_id;
				$user_ap = DB::table('appraisal_user')->where('id', $appraisal_user_id)->first();
				if(!empty($user_ap)){
					if($user_ap->is_submitted==3 || $user_ap->is_submitted==1){
						$update_save = DB::table('appraisal_user')->where('id', $appraisal_user_id)->update(['is_submitted' => 4]);
						if(!empty($update_save)){
							return $this->returnResponse(200, true, "Accept successsfully");
						}
						else{
							return $this->returnResponse(200, false, "Something went wrong."); 
						}	
					}
					else{
						return $this->returnResponse(200, false, "Please wait for department head response."); 
					}
						
				}
				else{
					return $this->returnResponse(200, false, "No Appraisal. Please try again.");  
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	

}
