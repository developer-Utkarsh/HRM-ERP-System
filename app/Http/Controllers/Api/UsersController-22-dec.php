<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Timetable;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use Input;
use App\FacultyRelation;
use App\ApiNotification;
use App\Users_pending;
use App\Userdetails_pending;
use App\FacultyRelations_pending;
use App\Studio;
use App\Userbranches;

class UsersController extends Controller
{
    /**
    * @var Check User Mobile is exists
    * @param Request $request
    * @return Json Response
    */
    public function checkMobile(Request $request)
    {
    	try {
    		$input = $request->only(['mobile']);
            $gsm_token = $request->gsm_token;

            if($request->has('mobile') && is_numeric($request->mobile)){
               $row = User::whereMobile($input['mobile']);

               if($row->exists()){

                $row = User::with([
                    'user_details',
                    'role',
                    'user_details.branch',
                    'faculty_relations',
					'user_branches.branch',
                ])->whereMobile($input['mobile'])->first();
				if(!empty($row)){
					if($row->status=='1'){
						if(isset($gsm_token) && !empty($gsm_token)){
							$row->gsm_token = $gsm_token;
							$row->update();
						}                    

						if(!empty($row->image)){
							$profile_img_url = asset('laravel/public/profile/'.$row->image);
						}else{
							$profile_img_url = asset('laravel/public/images/test-image.png');
						}

						$get_user_data = [];

						if(!empty($row)){
							$temp['id'] = $row->id;
							$temp['role_id'] = $row->role_id;
							$temp['role'] = $row->role->name;
							$temp['register_id'] = $row->register_id;
							$temp['name'] = $row->name;
							$temp['email'] = $row->email;
							$temp['mobile'] = $row->mobile;
							$temp['image'] = $profile_img_url;
							$temp['dob'] = $row->user_details->dob;
							$temp['father_name'] = $row->user_details->fname;
							$temp['mother_name'] = $row->user_details->mname;
							$temp['alternate_contact_number'] = $row->user_details->alternate_contact_number;
							$temp['alternate_email'] = $row->user_details->alternate_email;
							$temp['gender'] = $row->user_details->gender;
							$temp['material_status'] = $row->user_details->material_status;
							$temp['p_address'] = $row->user_details->p_address;
							$temp['c_address'] = $row->user_details->c_address;
							$temp['employee_type'] = $row->user_details->employee_type;
							$temp['degination'] = $row->user_details->degination;
							$temp['blood_group'] = $row->user_details->blood_group;
							// $temp['branch_name'] = $row->user_details->branch->name;
							$temp['joining_date'] = $row->user_details->joining_date;
							$temp['account_number'] = $row->user_details->account_number;
							$temp['bank_name'] = $row->user_details->bank_name;
							$temp['ifsc_code'] = $row->user_details->ifsc_code;
							$temp['bank_branch'] = $row->user_details->bank_branch;
							$temp['net_salary'] = $row->user_details->net_salary;
							$temp['tds'] = $row->user_details->tds;

							$get_faculty_relations = [];
							if(!empty($row->faculty_relations)){
								foreach($row->faculty_relations as $value){
									$temp1['from_time'] = $value->from_time;
									$temp1['to_time'] = $value->to_time;
									$get_faculty_relations[] = $temp1;
								}
							}
							$temp['faculty_relations'] = $get_faculty_relations;
							
							$get_user_branches = [];
							if(!empty($row->user_branches)){
								foreach($row->user_branches as $value){
									if(!empty($value->branch->name)){
										$temp2['name'] = $value->branch->name;
										$get_user_branches[] = $temp2;
									}
								}
							}
							$temp['user_branches'] = $get_user_branches;

							$get_user_data = $temp;
						}

						$data['user'] = $get_user_data;

							// $load = array();
							// $load['title'] = 'Suceess';
							// $load['msg'] = 'Faculty Login Successfully';
							// $token = $row->gsm_token;

							// $this->android_notification($token, $load);

						return $this->returnResponse(200, true, "user faculty", $data);
					}
					else{
						return $this->returnResponse(200, false, "User Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Mobile Number Not Found");
				}

            }else{
                return $this->returnResponse(200, false, "Mobile Number Not Found");
            } 
        }else{
            return $this->returnResponse(200, false, "Please Enter Mobile Number");
        }
    } catch (\Illuminate\Database\QueryException $ex) {
        return $this->returnResponse(500, false, $ex->getMessage());
    } catch (ModelNotFoundException $ex) {
        return $this->returnResponse(500, false, $ex->getMessage());
    }
}


    /**
    * @var getFacultySchedule
    * @param Request $request
    * @return Json Response
    */
    public function getFacultySchedule(Request $request)
    {
    	try {
            $faculty_id = $request->faculty_id;

            if(isset($faculty_id) && !empty($faculty_id)){
				$user = User::where('id', $faculty_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$faculty_timetables = Timetable::with(['topic','studio.assistant'])->select('id','studio_id','faculty_id','topic_id','from_time','to_time','cdate')->where('faculty_id', $faculty_id)->get();

						if(count($faculty_timetables) > 0){
							$get_faculty_timetables = [];
							foreach($faculty_timetables as $key => $timetable){
								$temp['timetable_id'] = $timetable->id;
								$temp['studio_id'] = $timetable->studio_id;
								$temp['faculty_id'] = $timetable->faculty_id;
								$temp['from_time'] = $timetable->from_time;
								$temp['to_time'] = $timetable->to_time;
								$temp['date'] = $timetable->cdate;
								$temp['topic_name'] = !empty($timetable->topic->name)?$timetable->topic->name:'';
								$temp['studio_name'] = !empty($timetable->studio->name)?$timetable->studio->name:'';
								if(!empty($timetable->studio->assistant)){
									$temp['assistant_name'] = $timetable->studio->assistant->name;
									$temp['assistant_mobile'] = $timetable->studio->assistant->mobile;    
								}
								else{
									$temp['assistant_name'] = '';
									$temp['assistant_mobile'] = '';   
								}
								$get_faculty_timetables[] = $temp;
							}                      

							$data['faculty_timetable'] = $get_faculty_timetables;

							return $this->returnResponse(200, true, "faculty timetable", $data);
						}else{
							return $this->returnResponse(200, false, "Faculty Timetable Not Found");
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
            return $this->returnResponse(200, false, "Faculty Id Not Found");
        }             

    } catch (\Illuminate\Database\QueryException $ex) {
        return $this->returnResponse(500, false, $ex->getMessage());
    } catch (ModelNotFoundException $ex) {
        return $this->returnResponse(500, false, $ex->getMessage());
    }
}


    /**
    * @var User Profile update
    * @param Request $request
    * @return Json Response
    */
	
	public function ProfileUpdate(Request $request)
    {
        try{

            $user_id = $request->user_id;

            if(isset($user_id) && !empty($user_id)){
				$user = User::with('user_details')->where('id', $user_id)->first();
				// print_r($user->user_details); die;
				if(!empty($user)){
					if($user->status=='1'){
						Users_pending::where('user_id', $user_id)->delete();
						Userdetails_pending::where('user_id', $user_id)->delete();
						
						$inputs1['user_id'] = $user->id;
						$inputs1['role_id'] = $user->role_id;
						$inputs1['register_id'] = $user->register_id;
						$inputs1['name'] = $user->name;
						$inputs1['email'] = $user->email;
						$inputs1['mobile'] = $user->mobile;
						$inputs1['image'] = $user->image;
						$inputs1['status'] = $user->status;
						$inputs1['gsm_token'] = $user->gsm_token;
						$inputs1['email_verified_at'] = $user->email_verified_at;
						$inputs1['password'] = $user->password;
						$inputs1['remember_token'] = $user->remember_token;
						$users_pending1 = Users_pending::create($inputs1);
						
						$userDetails1['user_id'] = $user->user_details->user_id;
						$userDetails1['dob'] = $user->user_details->dob;
						$userDetails1['fname'] = $user->user_details->fname;
						$userDetails1['mname'] = $user->user_details->mname;
						$userDetails1['alternate_contact_number'] = $user->user_details->alternate_contact_number;
						$userDetails1['alternate_email'] = $user->user_details->alternate_email;
						$userDetails1['gender'] = $user->user_details->gender;
						$userDetails1['material_status'] = $user->user_details->material_status;
						$userDetails1['p_address'] = $user->user_details->p_address;
						$userDetails1['c_address'] = $user->user_details->c_address;
						$userDetails1['employee_type'] = $user->user_details->employee_type;
						$userDetails1['degination'] = $user->user_details->degination;
						$userDetails1['blood_group'] = $user->user_details->blood_group;
						$userDetails1['branch_id'] = $user->user_details->branch_id;
						$userDetails1['joining_date'] = $user->user_details->joining_date;
						$userDetails1['resume'] = $user->user_details->resume;
						$userDetails1['account_number'] = $user->user_details->account_number;
						$userDetails1['bank_name'] = $user->user_details->bank_name;
						$userDetails1['ifsc_code'] = $user->user_details->ifsc_code;
						$userDetails1['bank_branch'] = $user->user_details->bank_branch;
						$userDetails1['net_salary'] = $user->user_details->net_salary;
						$userDetails1['tds'] = $user->user_details->tds;
						$users_pending1->user_details_pending()->create($userDetails1);
						$users_pending1->save();
						
						$users_pending = Users_pending::with('user_details_pending')->where('user_id', $user_id)->first();
						if(!empty($users_pending)){
							// print_r($users_pending); die;
							$inputs = $request->only('name','email','mobile','image');  
							
							if (Input::hasfile('image')){
								$this->RemoveProfile($users_pending->image);
								$inputs['image'] = $this->uploadImage(Input::file('image'));
							}
							
							$userDetails = $request->only('user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds');
							
							if (Input::hasfile('resume')){
								$this->RemoveResume($users_pending->user_details_pending->resume);
								$userDetails['resume'] = $this->uploadResume(Input::file('resume'));
							}
							
							if (is_array($request->faculty) && !empty($request->faculty)) {
								FacultyRelations_pending::where('user_id', $user_id)->delete();
								$faculty = $request->faculty;
								foreach ($faculty['from_time'] as $key => $value) {
									if(!empty($value)){
										$data = array(
											'from_time'=>$value,
											'to_time'=>$faculty['to_time'][$key],
										);
										$users_pending->faculty_relations_pending()->create($data);
									}
								}
							}
							
							$users_pending->user_details_pending()->update($userDetails);
							
							if($users_pending->update($inputs)){
								$update_data['admin_approval'] = 'Pending';
								$user->update($update_data);
								return $this->returnResponse(200, true, "Profile Updated Successfully");
							}else{
								return $this->returnResponse(200, false, "Profile Not Updated!!");
							}
							
						}
						else{
							
							return $this->returnResponse(200, false, "Something Went Wrong !");
							
							/*die('dddd');
							$inputs['user_id'] = $user_id;
							$inputs['register_id'] = '#EMP000' . $user_id;
							$users_pending = Users_pending::create($inputs);
							$users_pending->save();
							$userDetails['user_id'] = $user_id;
							$user_details_pending = Userdetails_pending::create($userDetails);
							$user_details_pending->save();
							
							if (is_array($faculty) && !empty($faculty)) {
								FacultyRelations_pending::where('user_id', $user_id)->delete();
								foreach ($faculty['from_time'] as $key => $value) {
									if(!empty($value)){
										$data = array(                  
											'user_id'=>$user_id,
											'from_time'=>$value,
											'to_time'=>$faculty['to_time'][$key],
										);
										$faculty_relations_pending = FacultyRelations_pending::create($data);
										$faculty_relations_pending->save();
									}
								}
							}*/
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
	
	/*public function ProfileUpdate(Request $request)
    {
        try{

            $user_id = $request->user_id;

            if(isset($user_id) && !empty($user_id)){

                $user = User::with('user_details')->where('id', $user_id)->first();

                $inputs = $request->only('name','email','mobile','image');          

                if (Input::hasfile('image')){
                    $this->RemoveProfile($user->image);
                    $inputs['image'] = $this->uploadImage(Input::file('image'));
                }

                $userDetails = $request->only('user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds');

                if (Input::hasfile('resume')){
                    $this->RemoveResume($user->user_details->resume);
                    $userDetails['resume'] = $this->uploadResume(Input::file('resume'));
                }

                if (is_array($request->faculty) && !empty($request->faculty)) {
                    FacultyRelation::where('user_id', $user_id)->delete();
                    $faculty = $request->faculty;
                    foreach ($faculty['from_time'] as $key => $value) {
                        if(!empty($value)){
                            $data = array(                  
                                'from_time'=>$value,
                                'to_time'=>$faculty['to_time'][$key],
                            );
                            $user->faculty_relations()->create($data);
                        }
                    }
                }            

                $user->user_details()->update($userDetails);
                
                if($user->update($inputs)){
                    return $this->returnResponse(200, true, "Profile Updated Successfully");
                }else{
                    return $this->returnResponse(200, false, "Profile Not Updated!!");
                }
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }*/

    public function Dashboard(Request $request)
    {
        try{

            $user_id = $request->user_id;

            if(isset($user_id) && !empty($user_id)){
				$user = User::where('id', $user_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						if($user->role_id==2){ //Faculty Role
							$get_faculty_dashboard = Timetable::with(['reschedule','swap','cancelclass'])->where('faculty_id', $user_id)->get();
							$get_faculty_class_count = Timetable::where('faculty_id', $user_id)->count();
							$get_notification_count = ApiNotification::count();

							if(count($get_faculty_dashboard) > 0){
								$total_dashboard_classess = [];
								foreach ($get_faculty_dashboard as $class) {
									$temp['total_faculty_class'] = $get_faculty_class_count;    
									$temp['total_reschedule_request'] = $class->reschedule->count();
									$temp['total_swap_request'] = $class->swap->count();
									$temp['total_cancelclass_request'] = $class->cancelclass->count();
									$temp['total_notification_count'] = $get_notification_count;
									$total_dashboard_classess = $temp; 
								}

								$data['total_dashboard_classess'] = $total_dashboard_classess;

								return $this->returnResponse(200, true, "Total Faculty Dashboard Class", $data);

							}else{
								return $this->returnResponse(200, false, "Dashboard Data Not Found");
							}   
						}
						else if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with(['timetable','timetable.topic','timetable.faculty','timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where('assistant_id', $user_id)->first();
							// echo "<pre>";
							// print_R($studios); die;
							if(!empty($studios)){
								$get_notification_count = ApiNotification::count();
								$get_faculty_timetables = [];
								$get_faculty_timetables['studio_id'] = $studios->id;
								$get_faculty_timetables['studio_name'] = $studios->name;
								$get_faculty_timetables['total_notification_count'] = $get_notification_count;
								foreach($studios->timetable as $key => $timetable){
									$temp['timetable_id'] = $timetable->id;
									$temp['from_time'] = $timetable->from_time;
									$temp['to_time'] = $timetable->to_time;
									$temp['date'] = $timetable->cdate;
									$temp['faculty_name'] = $timetable->faculty->name;
									$temp['batch_name'] = $timetable->batch->name;
									$temp['course_name'] = $timetable->course->name;
									$temp['subject_name'] = $timetable->subject->name;
									$temp['chapter_name'] = $timetable->chapter->name;
									$temp['topic_name'] = $timetable->topic->name;
									
									$get_faculty_timetables['classes'][] = $temp;
								}                      

								$data['studio'] = $get_faculty_timetables;

								return $this->returnResponse(200, true, "Studio Assistant Classes", $data);
							}else{
								return $this->returnResponse(200, false, "Classes Not Found");
							}
						}
						else if($user->role_id==4){ //Studio Manager Role
							$get_notification_count = ApiNotification::count();
							$data['total_notification_count'] = $get_notification_count;
							$branches = Userbranches::with(['branch','studio','studio.timetable','studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->where('user_id', $user_id)->get();
							// echo "<pre>";
							// print_R($branches); die;
							if(!empty($branches)){
								$branchesArray = array();
								foreach($branches as $key=>$value){
									$branchesArray[$key]['branch_id'] = $value->branch_id;
									$branchesArray[$key]['branch_name'] = $value->branch->name;
									if(!empty($value->studio) && count($value->studio) > 0){
										foreach($value->studio as $key1=>$studios){
											$branchesArray[$key]['studios'][$key1]['studio_id'] = $studios->id;
											$branchesArray[$key]['studios'][$key1]['studio_name'] = $studios->name;
											if(!empty($studios->timetable) && count($studios->timetable) > 0){
												foreach($studios->timetable as $key2 => $timetable){
													$temp['timetable_id'] = $timetable->id;
													$temp['from_time'] = $timetable->from_time;
													$temp['to_time'] = $timetable->to_time;
													$temp['date'] = $timetable->cdate;
													if(!empty($timetable->faculty->name)){
														$temp['faculty_name'] = $timetable->faculty->name;
													}
													if(!empty($timetable->batch->name)){
														$temp['batch_name'] = $timetable->batch->name;
													}
													if(!empty($timetable->course->name)){
														$temp['course_name'] = $timetable->course->name;
													}
													if(!empty($timetable->subject->name)){
														$temp['subject_name'] = $timetable->subject->name;
													}
													if(!empty($timetable->chapter->name)){
														$temp['chapter_name'] = $timetable->chapter->name;
													}
													if(!empty($timetable->topic->name)){
														$temp['topic_name'] = $timetable->topic->name;
													}
													
													$branchesArray[$key]['studios'][$key1]['classes'][$key2] = $temp;
												}        
											}
											else{
												$branchesArray[$key]['studios'][$key1]['classes'] = array();
											}
										}
									}
									else{
										$branchesArray[$key]['studios'] = array();
									}
								}
								$data['branches'] = $branchesArray;

								return $this->returnResponse(200, true, "Studio Manager Branches Details", $data);
							}else{
								return $this->returnResponse(200, false, "Branches Not Found");
							}
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

    /**
    * @var get All Notifications
    * @param Request $request
    * @return Json Response
    */
    public function getAllNotification(Request $request)
    {
        try {

            $notifications = ApiNotification::select('id','title','description','image','date')->orderBy('id','desc')->get();

            if(count($notifications) > 0){
                $get_notifications = [];
                foreach($notifications as $key => $notification){
                    $temp['id'] = $notification->id;
                    $temp['title'] = $notification->title;
                    $temp['description'] = $notification->description;
					$notification_img_url = null;
                    if(!empty($notification->image)){
                        // $notification_img_url = asset('laravel/public/notification/'.$notification->image);
                    }else{
                        // $notification_img_url = asset('laravel/public/images/test-image.png');
                    }

                    $temp['image'] = $notification_img_url;
                    $temp['date'] = $notification->date;

                    $get_notifications[] = $temp;
                }

                $data['notifications'] = $get_notifications;

                return $this->returnResponse(200, true, "Get All Notifications", $data);
            }else{
                return $this->returnResponse(200, false, "Notification Not Found");
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }

    public function uploadResume($file){
        $drive = public_path(DIRECTORY_SEPARATOR . 'resume' . DIRECTORY_SEPARATOR);
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;    
        $newImage = $drive . $filename;
        $imgResource = $file->move($drive, $filename);
        return $filename;
    }

    public function uploadImage($image){
        $drive = public_path(DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR);
        $extension = $image->getClientOriginalExtension();
        $imagename = uniqid() . '.' . $extension;    
        $newImage = $drive . $imagename;
        $imgResource = $image->move($drive, $imagename);
        return $imagename;
    }


    public function RemoveResume($file) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'resume' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $file;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }

    public function RemoveProfile($image) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $image;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }
	
	
	/**
    * @var Get User Profile Details
    * @param Request $request
    * @return Json Response
    */
    public function getProfile(Request $request)
    {
		try {
			$input = $request->only(['user_id']);
			$gsm_token = $request->gsm_token;

			if($request->has('user_id') && is_numeric($request->user_id)){
				$row = User::where('id',$input['user_id']);  

				if($row->exists()){
					$row = User::with([
						'user_details',
						'role',
						'user_details.branch',
						'faculty_relations',
						'user_branches.branch',
					])->where('id', $input['user_id'])->first();
					if($row){
						if($row->status=='1'){
							if(isset($gsm_token) && !empty($gsm_token)){
								$row->gsm_token = $gsm_token;
								$row->update();
							}                    

							if(!empty($row->image)){
								$profile_img_url = asset('laravel/public/profile/'.$row->image);
							}else{
								$profile_img_url = asset('laravel/public/images/test-image.png');
							}

							$get_user_data = [];

							if(!empty($row)){
								// print_R($row); die;
								$temp['id'] = $row->id;
								$temp['role_id'] = $row->role_id;
								$temp['role'] = $row->role->name;
								$temp['register_id'] = $row->register_id;
								$temp['name'] = $row->name;
								$temp['email'] = $row->email;
								$temp['mobile'] = $row->mobile;
								$temp['image'] = $profile_img_url;
								$temp['dob'] = $row->user_details->dob;
								$temp['father_name'] = $row->user_details->fname;
								$temp['mother_name'] = $row->user_details->mname;
								$temp['alternate_contact_number'] = $row->user_details->alternate_contact_number;
								$temp['alternate_email'] = $row->user_details->alternate_email;
								$temp['gender'] = $row->user_details->gender;
								$temp['material_status'] = $row->user_details->material_status;
								$temp['p_address'] = $row->user_details->p_address;
								$temp['c_address'] = $row->user_details->c_address;
								$temp['employee_type'] = $row->user_details->employee_type;
								$temp['degination'] = $row->user_details->degination;
								$temp['blood_group'] = $row->user_details->blood_group;
								// $temp['branch_name'] = $row->user_details->branch->name;
								$temp['joining_date'] = $row->user_details->joining_date;
								$temp['account_number'] = $row->user_details->account_number;
								$temp['bank_name'] = $row->user_details->bank_name;
								$temp['ifsc_code'] = $row->user_details->ifsc_code;
								$temp['bank_branch'] = $row->user_details->bank_branch;
								$temp['net_salary'] = $row->user_details->net_salary;
								$temp['tds'] = $row->user_details->tds;
								
								$supervisor_id = NULL;
								$supervisor_name = NULL;
								$supervisor = User::where('id', $row->supervisor_id)->first();
								if(!empty($supervisor)){
									$supervisor_id = $supervisor->id;
									$supervisor_name = $supervisor->name;
								}
								$temp['supervisor_id'] = $supervisor_id;
								$temp['supervisor_name'] = $supervisor_name;
								
								$get_faculty_relations = [];
								if(!empty($row->faculty_relations)){
									foreach($row->faculty_relations as $value){
										$temp1['from_time'] = $value->from_time;
										$temp1['to_time'] = $value->to_time;
										$get_faculty_relations[] = $temp1;
									}
								}
								$temp['faculty_relations'] = $get_faculty_relations;
								
								$get_user_branches = [];
								if(!empty($row->user_branches)){
									foreach($row->user_branches as $value){
										if(!empty($value->branch->name)){
											$temp2['name'] = $value->branch->name;
											$get_user_branches[] = $temp2;
										}
									}
								}
								$temp['user_branches'] = $get_user_branches;

								$get_user_data = $temp;
							}

							$data['user'] = $get_user_data;
								// $load = array();
								// $load['title'] = 'Suceess';
								// $load['msg'] = 'Faculty Login Successfully';
								// $token = $row->gsm_token;

								// $this->android_notification($token, $load);

							return $this->returnResponse(200, true, "user faculty", $data);
						}
						else{
							return $this->returnResponse(200, false, "User Not Active");
						}
					}
					else{
						return $this->returnResponse(200, false, "User ID Not Found");
					}

				}else{
					return $this->returnResponse(200, false, "User ID Not Found");
				} 
			}else{
				return $this->returnResponse(200, false, "Please Enter User ID");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	
	//SUpervisor List
	public function getemployeelist(Request $request)
    {
        try{

            $emp_id = $request->emp_id;

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->where('register_id', '!=', NULL)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$employeeArray = array();
						$check_supervisor = User::where('supervisor_id', $emp_id)->get();
						if(count($check_supervisor) > 0){
							$i = 0;
							$supervisors = User::where('supervisor_id', '!=', 0)->where('supervisor_id', '!=', $emp_id)->groupBy('supervisor_id')->get();
							// echo "<pre>";print_r($supervisors); die;
							if(!empty($supervisors)){
								foreach($supervisors as $key=>$value){
									$checkSupervisor = User::where('id', $value->supervisor_id)->where('status', 1)->first();
									if(!empty($checkSupervisor)){
										$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
										$employeeArray[$i]['name'] = $checkSupervisor->name;
										$employeeArray[$i]['type'] = 'supervisor';
										$i++;
									}
									
								}
							}
							
							$employees = User::where('supervisor_id', $emp_id)->get();
							// echo "<pre>";print_r($supervisors); die;
							if(!empty($employees)){
								foreach($employees as $key=>$value){
									if(!empty($value)){
										$employeeArray[$i]['emp_id'] = $value->id;
										$employeeArray[$i]['name'] = $value->name;
										$employeeArray[$i]['type'] = 'employee';
										$i++;
									}
									
								}
							}
						}
						
						$data['employees'] = $employeeArray;
						return $this->returnResponse(200, true, "Employee List", $data);
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
}
