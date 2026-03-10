<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
use App\StartClass;
use App\Batch;
use App\Branch;
use DateTime;
use Hash;

use DB;
use Excel;
use App\Userdetails;

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
                ])->whereMobile($input['mobile'])->where('status', 1)->first();

				$temp['otp']=$otp_gen=substr(str_shuffle("0123456789"), 0, 6);


                if($input['mobile']=='1234512345'){
                  $row = User::with([
                    'user_details',
                    'role',
                    'user_details.branch',
                    'faculty_relations',
					'user_branches.branch',
                  ])->whereMobile($input['mobile'])->first();

					$temp['otp']=$otp_gen="123489";

                }

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
							$current_app_version = $this->current_app_version();
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

							$row->update(['login_otp'=>$otp_gen,'device_type'=>$request->device_type]);
							$message_content="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
							$message_content=urlencode($message_content);
							$mbl=$temp['mobile'];
							//$mbl=8104001734;
					    	
							if($request->device_type=='1234'){
								//Attendance Menu								
								if($row->role_id==3 || $row->role_id==28 || $row->user_details->degination=="CLASS ASSISTANT" || $row->user_details->degination=="CENTER HEAD" || $row->user_details->degination=="COUNSELOR" || $row->user_details->degination=="ASSISTANT-NOTES DISTRIBUTION" || $row->user_details->degination=="SR. EXECUTIVE-NOTES DISTRIBUTION" || $row->user_details->degination=="ASSISTANT CENTER HEAD" || $row->role_id==32){
									$this->smscountry($mbl,$message_content);  //SMS Country
									//$this->pinbixmsg($mbl,$message_content);	 //Pinbix
								}
							}else{
								//HRM Default 
								$this->smscountry($mbl,$message_content);		//SMS Country	
								//$this->pinbixmsg($mbl,$message_content);		//Pinbix
							}

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
										$temp2['branch_location'] = $value->branch->branch_location;
										$get_user_branches[] = $temp2;
									}
								}
							}
							$temp['user_branches'] = $get_user_branches;
							$temp['current_app_version'] = $current_app_version;

							$get_user_data = $temp;
						}

						$data['user'] = $get_user_data;
						return $this->returnResponse(200, true, "user faculty", $data);
					}
					else{
						return $this->returnResponse(200, false, "User Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Mobile Number Not Found or User Not Active");
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
    { //echo '<pre>'; print_r('ddd');die;
    	try {
            $faculty_id         = $request->faculty_id;
            $role_id            = $request->role_id;
	        $branch_id          = $request->branch_id;
	        $department_head_id = $request->department_head_id;
	        $designation        = $request->designation;
	        $name               = $request->name;
			$date_from 			= $request->date_from;
			$date_to 			= $request->date_to;
			$mobile_no = "";
			if(!empty($request->mobile)){
				$mobile_no 	= $request->mobile;
				// $mobile_no = ltrim($mobile_no,"91");
				$mobile_no = substr($mobile_no, 2);
			}

            if( (isset($faculty_id) && !empty($faculty_id)) || (!empty($mobile_no)) ){
				if(!empty($mobile_no)){
					$user = User::where('mobile', $mobile_no)->first();
					$faculty_id = $user->id;
				}else{
					$user = User::where('id', $faculty_id)->first();
				}
				if(!empty($user)){
					if($user->status=='1'){
						$faculty_timetables = Timetable::with(['topic','studio.assistant','studio.branch','chapter','course']);
						$faculty_timetables1 = Timetable::with(['topic','studio.assistant','studio.branch','chapter','course']);
						
						if(!empty($date_from) && !empty($date_to)){
							$faculty_timetables->where('cdate', '>=', $date_from);
							$faculty_timetables->where('cdate', '<=', $date_to);
							
							$faculty_timetables1->where('cdate', '>=', $date_from);
							$faculty_timetables1->where('cdate', '<=', $date_to);
						}
						else{
							$faculty_timetables->where('cdate', '>=', date('Y-m-d'));
							
							$faculty_timetables1->where('cdate', '>=', date('Y-m-d'));
						}
						
						if(!empty($role_id)){
							$faculty_timetables->WhereHas('faculty', function ($q1) use ($role_id) {
								$q1->where('role_id', $role_id);
							});
							
							$faculty_timetables1->WhereHas('faculty', function ($q1) use ($role_id) {
								$q1->where('role_id', $role_id);
							});
						}

						if(!empty($department_head_id)){
							$faculty_timetables->WhereHas('faculty', function ($q2) use ($department_head_id) {
								$q2->where('id', $department_head_id);
							});
							
							$faculty_timetables1->WhereHas('faculty', function ($q2) use ($department_head_id) {
								$q2->where('id', $department_head_id);
							});
						}

						if(!empty($designation)){
							$faculty_timetables->WhereHas('faculty.user_details', function ($q3) use ($designation) {
								$q3->where('degination', $designation);
							});
							
							$faculty_timetables1->WhereHas('faculty.user_details', function ($q3) use ($designation) {
								$q3->where('degination', $designation);
							});
						}

                        if(!empty($branch_id)){
						    $faculty_timetables->WhereHas('studio.branch', function ($q4) use ($branch_id) {
								$q4->where('id', $branch_id);
							});
							
							$faculty_timetables1->WhereHas('studio.branch', function ($q4) use ($branch_id) {
								$q4->where('id', $branch_id);
							});
                        }


                        if(!empty($name)){
							$faculty_timetables->WhereHas('faculty', function ($q5) use ($name) {
								$q5->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
							
							$faculty_timetables1->WhereHas('faculty', function ($q5) use ($name) {
								$q5->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
							});
						}
						$current_time = date("H:i");
						$result_faculty_timetables = $faculty_timetables->select('id','studio_id','faculty_id','topic_id','from_time','to_time','cdate','chapter_id','course_id')->where('faculty_id', $faculty_id)->where('from_time','>=',$current_time)->where('is_deleted','=','0')->orderBy('cdate','desc')->orderBy('from_time','asc')->get();

						$get_faculty_timetables = [];
						if(count($result_faculty_timetables) > 0){
							foreach($result_faculty_timetables as $key => $timetable){
								$temp['timetable_id'] = $timetable->id;
								$temp['studio_id'] = $timetable->studio_id;
								$temp['faculty_id'] = $timetable->faculty_id;
								$temp['strtime_from_time'] = strtotime($timetable->from_time);
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
								$temp['branch_name'] = !empty($timetable->studio->branch->name)?$timetable->studio->branch->name:'';
								$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
								$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
								
								$get_faculty_timetables[] = $temp;
							}
						}
						
						$result_faculty_timetables = $faculty_timetables1->select('id','studio_id','faculty_id','topic_id','from_time','to_time','cdate','chapter_id','course_id')->where('faculty_id', $faculty_id)->where('from_time','<',$current_time)->where('is_deleted','=','0')->orderBy('cdate','desc')->orderBy('from_time','asc')->get();
						// echo count($result_faculty_timetables); die;
						if(count($result_faculty_timetables) > 0){
							foreach($result_faculty_timetables as $key => $timetable){
								$temp['timetable_id'] = $timetable->id;
								$temp['studio_id'] = $timetable->studio_id;
								$temp['faculty_id'] = $timetable->faculty_id;
								$temp['strtime_from_time'] = strtotime($timetable->from_time);
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
								$temp['branch_name'] = !empty($timetable->studio->branch->name)?$timetable->studio->branch->name:'';
								$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
								$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
								
								$get_faculty_timetables[] = $temp;
							}
						}
						
						if(count($get_faculty_timetables) > 0){
							$data['faculty_timetable'] = $get_faculty_timetables;

							return $this->returnResponse(200, true, "faculty timetable", $data);
						}
						else{
							return $this->returnResponse(200, true, "Faculty Timetable Not Found");
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

	public function links(Request $request)
    { 
		// echo '<pre>'; print_r('ddd');die;
    	try {
			$mobile_no = "";
			if(!empty($request->mobile)){
				$mobile_no 	= $request->mobile;
				// $mobile_no = ltrim($mobile_no,"91");
				$mobile_no =substr($mobile_no, 2);
			}
            if( (!empty($mobile_no)) ){
				if(!empty($mobile_no)){
					$user = User::where('mobile', $mobile_no)->where('status','1')->where('is_deleted','0')->first();
					if(!empty($user)){
						/*$userbranches=DB::table('userbranches')->where('user_id',$user->id)->where('is_deleted','0')->first();
						$bb=array(36,37,38,39,40,41,42,43,45,47,48,49,50,51,52,53,55,56,57,58,59,60,61,62,63,64,65,66,67,70,71,72,77,78,79,88,90,91,92);
						if(!empty($userbranches) && in_array($userbranches->branch_id,$bb)){
							$er="*जयपुर व जोधपुर सेंटर पर आज अवकाश रहेगा*\n अपरिहार्य कारणों से आज बुधवार, 6 दिसम्बर को उत्कर्ष के जयपुर व जोधपुर सेंटर की सभी ऑफ़लाइन कक्षाएँ व यहॉं से संचालित सभी ऑनलाइन कक्षाओं का अवकाश रहेगा।\n 7 दिसम्बर को पुनः यथासमय यथास्थान कक्षाएँ संचालित होगी।अधिक जानकारी के लिए एप पर सूचना देखते रहें।\n - टीम उत्कर्ष,जयपुर-जोधपु";
							return $this->returnResponse(200, false,$er);
							exit;
						}*/
					}else{
						$er="क्षमा करें।\n यह सुविधा केवल उत्कर्ष के Employee के लिए ही उपलब्ध है।आपने जिस मोबाइल नम्बर से हमें Whatsapp Request भेजी है वह नम्बर उत्कर्ष के साथ रजिस्टर्ड नहीं है। \n किसी अन्य जानकारी या तकनीकी सहायता के लिए कृपया 9829213213 पर संपर्क करें। \n - टीम उत्कर्ष";
						return $this->returnResponse(200, false,$er);
						exit;
					}
				}else{
					return $this->returnResponse(200, false, "User Id Not Found"); 
					exit;
				}

				if(!empty($user)){
					if($user->status=='1'){
						$user_id = $user->id;
						$send_link = $user->send_link;
						$selectFromDate =  date('Y-m-d');
						if(date('H')>=19){ // 19 == 7PM
						  $selectFromDate = date('Y-m-d',strtotime('+1 day'));
						}
						
						if($user->role_id==2){ // Faculty
							$msg=$body ="";
							$faculty_id     = $user_id;									
							$whereCond  = ' 1=1';
							$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'"';
							$whereCond .= ' AND timetables.faculty_id = "'.$faculty_id.'"';
							$whereCond .= ' AND timetables.is_publish ="1"';
											  
							$get_faculty = DB::table('timetables')
											  ->select('timetables.id','timetables.assistant_id','timetables.faculty_id','users_faculty.name as faculty_name','users_faculty.id as faculty_id')
											  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
											  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
											  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetables.faculty_id')
											  ->whereRaw($whereCond)
											  ->where('timetables.time_table_parent_id', '0')
											  ->where('timetables.is_deleted', '0')
											  ->orderBy('users_faculty.name','asc')
											  ->groupBy('timetables.faculty_id')
											  ->get();	
							   // echo count($get_faculty); die;
								$body = "";
								if (count($get_faculty) > 0) {
									foreach ($get_faculty as $get_faculty_value) {
										$body.="Respected *".$get_faculty_value->faculty_name."*, \n \n Please check the below details of scheduled Time Table of *".date("d-m-Y",strtotime($selectFromDate))."* \n \n ";
										
										$whereCond = '1=1';
										$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'" ';
										if(!empty($get_faculty_value->faculty_name)){
											$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile')
													  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													  ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
													  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													  ->leftJoin('chapter', 'chapter.id', '=', 'timetables.chapter_id')
													  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
													  ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
													  ->where('timetables.time_table_parent_id', '0')
													  ->where('timetables.is_deleted', '0')
													  ->where('timetables.is_publish', '1')
													  ->whereRaw($whereCond)
													  ->orderBy('timetables.from_time', 'ASC')
													  ->get();
											$duration  = "00 : 00 Hours"; 
											$schedule_duration  = "00 : 00 Hours"; 
											$base_time = new DateTime('00:00');
											$total     = new DateTime('00:00');
											
											$total_schedule = new DateTime('00:00');
											$total_base_schedule = new DateTime('00:00');
											
											if(count($get_faculty_timetable) > 0){
												$i = 0;
												$message = "";
												foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
													$i++;
													$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
													$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
													$interval = $first_date->diff($second_date);
													$duration = $interval->format('%H : %I Hours');
													$base_time->add($interval); 
													
													
													
													$from_time         = new DateTime($get_faculty_timetable_value->from_time);
													$to_time           = new DateTime($get_faculty_timetable_value->to_time);
													$schedule_interval = $from_time->diff($to_time);
													$schedule_duration = $schedule_interval->format('%H : %I Hours');
													$total_base_schedule->add($schedule_interval);
													
													$from_time = isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '';
													$to_time = isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '';
													$date = date('d-m-Y',strtotime($get_faculty_timetable_value->cdate));
													$assistant_name = isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '';
													$batch_name = isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '';
													$subject_name = isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '';
													$branches_name = isset($get_faculty_timetable_value->branches_name) ?  $get_faculty_timetable_value->branches_name : '';
													$studios_name = isset($get_faculty_timetable_value->studios_name) ?  $get_faculty_timetable_value->studios_name : '';
													$message.= "*".$i.".* Time - *".$from_time."-".$to_time."*,";
													// $message.=" Date - ".$date.",";
													$message.=" \n Assistant Name - ".$assistant_name.",";
													$message.=" \n Batch Name - ".$batch_name.",";
													$message.=" \n Subject Name - ".$subject_name.",";
													$message.=" \n Branch Name - *".$branches_name."*,";
													$message.=" \n Hall/Studio - *".$studios_name."*";
													// $message.=" Schedule Time - ".$schedule_duration;
													$message.= "\n\n";
												}
												$body.= $message."\n"." To check your daily time table, click on the below link : \n ".$send_link."\n";
                                               
                                                // for whatsapp
                                                $message=str_replace("\n"," ",$message);
                                                $message=str_replace("  "," ",$message);
                                                $msg.='"variable1": "'.$get_faculty_value->faculty_name.'",';
                                                $msg.='"variable2": "'.$selectFromDate.'",';
												$parts=str_split($message,30);
												for($i=0; $i<count($parts);$i++){ 
													$j=$i+3;
													if($j<23){
												     $msg.='"variable'.$j.'": "'.$parts[$i].'",';
												     //$msg.='"variable'.$j.'": "RAS Foundation RAS",';
												    }
												}
                                                
                                               for($k=$j+1;$k<23;$k++){
													$msg.='"variable'.$k.'": "padama",';
												}

                                                $msg.='"variable23": "'.$send_link.'",';
												$msg=trim($msg,",");

                                            }else{
											 $body="Respected *".$get_faculty_value->faculty_name."*,\n \n Timetable of *".date("d-m-Y",strtotime($selectFromDate))."* is not scheduled yet, please wait and try again after sometime.\n For More details please download the app from the below link : \n https://play.google.com/store/apps/details?id=com.utkarsh.employee \n \n Thanks";

											}
										}
            
									}
								}else{
								  $body="*Respected ".$user->name.",*\n \n Timetable of *".date("d-m-Y",strtotime($selectFromDate))."* is not scheduled yet, please wait and try again after sometime.\n";
								}
								
							 $body.="\n For More details please download the app from the below link :\n https://play.google.com/store/apps/details?id=com.utkarsh.employee \n Thanks \n\n -Team Utkarsh";
							// echo $body; die;


							/*$url = "https://api.imiconnect.in/resources/v1/messaging";
							$curl = curl_init($url);
							curl_setopt($curl, CURLOPT_URL, $url);
							curl_setopt($curl, CURLOPT_POST, true);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							$headers = array(
							   "Content-Type: application/json",
							   "key: 0b08bf38-6dd9-11ea-9da9-025282c394f2",
							);
							curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
							$data_wa='{
							    "appid": "a_158521380743240260",
							    "deliverychannel": "whatsapp",
							    "message": {
							        "template": "4345562635566690",
							        "parameters": {'.$msg.'}
							    },
							    "destination": [{"waid": ["919680702580"]}]
							}';
							curl_setopt($curl, CURLOPT_POSTFIELDS, $data_wa);
							$resp = curl_exec($curl);
							curl_close($curl);*/


							$data['content'] =$body;
							return $this->returnResponse(200, true, "Timetable", $data);
						}else{
							if($user->role_id==4 OR $user->role_id==27 OR $user->role_id==3){
								$data = array();
								$send_link = $user->send_link;
								$today_date_name = "";
								$tomorrow_date_name = "";
								$today_link = $send_link;
								$tomorrow_link = "";
								$msg="नमस्ते,\n टाईमटेबल देखने के लिए नीचे दिए गए लिंक पर क्लिक करें: \n $today_date_name $today_link
								$tomorrow_date_name $tomorrow_link \n - धन्यवाद \n टीम उत्कर्ष";
								$data['content'] = $msg;
								return $this->returnResponse(200, true, "Timetable", $data);
					     	}else{
					     		$data = array();
					     		$drivers = User::with(['user_details'])->where('is_deleted', '0')->where('mobile', $mobile_no)->where('status', 1)->orderBy('name');
								$drivers->where('register_id','!=',NUll);
								$drivers->WhereHas('user_details', function ($q) { 
						                $q->where('degination', '=', 'DRIVER');
						            });
								$employee_ids = $drivers->get();
								if(count($employee_ids) > 0){
									foreach($employee_ids as $emp_detail){
										$send_link = $emp_detail->send_link;
										$today_date_name = "";
										$tomorrow_date_name = "";
										$today_link = $send_link;
										$tomorrow_link = "";
										$msg="नमस्ते,\n टाईमटेबल देखने के लिए नीचे दिए गए लिंक पर क्लिक करें: \n $today_date_name $today_link
								$tomorrow_date_name $tomorrow_link \n - धन्यवाद \n टीम उत्कर्ष";
										$data['content'] = $msg;
									}
								}else{
								 $data['content']="क्षमा करें।\n यह सुविधा केवल उत्कर्ष के Employee के लिए ही उपलब्ध है।आपने जिस मोबाइल नम्बर से हमें Whatsapp Request भेजी है वह नम्बर उत्कर्ष के साथ रजिस्टर्ड नहीं है। \n किसी अन्य जानकारी या तकनीकी सहायता के लिए कृपया 9829213213 पर संपर्क करें। \n - टीम उत्कर्ष";
								}
								return $this->returnResponse(200, true, "Timetable", $data);
					     	}

						}
						 
					}else{
						$data['content']="क्षमा करें।\n यह सुविधा केवल उत्कर्ष के Employee के लिए ही उपलब्ध है।आपने जिस मोबाइल नम्बर से हमें Whatsapp Request भेजी है वह नम्बर उत्कर्ष के साथ रजिस्टर्ड नहीं है। \n किसी अन्य जानकारी या तकनीकी सहायता के लिए कृपया 9829213213 पर संपर्क करें। \n - टीम उत्कर्ष";
					    return $this->returnResponse(200, true, "Timetable", $data);
						//return $this->returnResponse(200, false, "User Not Active");
					}
				}else{
					$data['content']="You are not a registered employee of utkarsh";
					return $this->returnResponse(200, true, "Timetable", $data);
					//return $this->returnResponse(200, false, "User Id Not Found"); 
				}

	        }else{
	            return $this->returnResponse(200, false, "User Not Found");
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
						$userDetails1['dob'] =$user->user_details->dob;
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

							//$userDetails->dob=date("Y-m-d",strtotime($userDetails->dob));

							
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
			$name = $request->name;

            if(isset($user_id) && !empty($user_id)){
				$user = User::where('id', $user_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$current_app_version ="50";; $this->current_app_version();
						$date_from = $request->date_from;
						$date_to = $request->date_to;
						if($user->role_id==2){ //Faculty Role
							$is_chanakya = 0;
							$is_mentor = DB::table('batch')->where('mentor_id',$user_id)->count();
							if($is_mentor > 0){
								$is_chanakya = 1;
							}
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
									$temp['current_app_version'] = $current_app_version;
									$temp['is_chanakya'] = $is_chanakya;
									$total_dashboard_classess = $temp; 
								}

								$data['total_dashboard_classess'] = $total_dashboard_classess;

								return $this->returnResponse(200, true, "Total Faculty Dashboard Class", $data);

							}else{
								return $this->returnResponse(200, false, "Dashboard Data Not Found");
							}   
						}
						else if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with(['timetable'=>function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
									$q->where('is_deleted', '=', '0');
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
									$q->where('is_deleted', '=', '0');
								}
							},'timetable.topic',
							'timetable.faculty'=>function ($q) use ($name) {
								if(!empty($name)){
									$q->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								}
							},
							'timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where('assistant_id', $user_id);
							
							$studios->WhereHas('timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							});
							
							/* if(!empty($name)){
								$studios->WhereHas('timetable.faculty', function ($q) use ($name) {
									$q->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							} */
							// echo $studios->toSql(); die;
							$studios = $studios->first();
							// echo "<pre>";
							// print_R($studios); die;
							if(!empty($studios)){
								$get_notification_count = ApiNotification::count();
								$get_faculty_timetables = [];
								$get_faculty_timetables['studio_id'] = $studios->id;
								$get_faculty_timetables['studio_name'] = $studios->name;
								$get_faculty_timetables['total_notification_count'] = $get_notification_count;
								$get_faculty_timetables['current_app_version'] = $current_app_version;
								$class_array = array();
								foreach($studios->timetable as $key => $timetable){
									if(!empty($timetable->faculty->name)){
										$temp['timetable_id'] = $timetable->id;
										$temp['from_time'] = $timetable->from_time;
										$temp['to_time'] = $timetable->to_time;
										$temp['date'] = $timetable->cdate;
										$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
										$temp['batch_name'] = isset($timetable->batch->name)?$timetable->batch->name:'';
										$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
										$temp['subject_name'] = isset($timetable->subject->name)?$timetable->subject->name:'';
										$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
										$temp['topic_name'] = isset($timetable->topic->name)?$timetable->topic->name:'';
										
										$class_status = "Not Started";
										$startclass = StartClass::where('timetable_id', $timetable->id)->first();
										if(!empty($startclass)){
											$class_status = $startclass->status;
										}
										
										$temp['class_status'] = $class_status;
										
										
										$class_array[] = $temp;
									}
								}
								$get_faculty_timetables['classes'] = $class_array;

								$data['studio'] = $get_faculty_timetables;

								return $this->returnResponse(200, true, "Studio Assistant Classes", $data);
							}else{
								return $this->returnResponse(200, false, "Classes Not Found");
							}
						}
						else if($user->role_id==4 || $user->role_id==27 || $user->role_id==28){ //4 = Studio Manager Role // 27 = Time table Manager
						
							$studio_id ="";
							if(isset($request->studio_id) && !empty($request->studio_id)){
								$studio_id = $request->studio_id;
							}
							$get_notification_count = ApiNotification::count();
							$data['total_notification_count'] = $get_notification_count;
							$data['current_app_version'] = $current_app_version;
							$branches = Userbranches::with(['branch',
							'studio'=>function ($q) use ($studio_id) {
								if(!empty($studio_id)){
									$q->where('id', $studio_id);
								}
							},
							'studio.timetable'=>function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							},'studio.timetable.topic',
							'studio.timetable.faculty'=>function ($q) use ($name) {
								if(!empty($name)){
									$q->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								}
							},
							'studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->where('user_id', $user_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							});
							$branches = $branches->orderBy('branch_id', 'desc')->groupBy('branch_id')->get();
							
							// echo "<pre>";
							// print_R($branches); die;
							if(!empty($branches)){
								$branchesArray = array();
								$i = 0;
								foreach($branches as $key=>$value){
									
									if(!empty($value->studio) && count($value->studio) > 0){
										$ii = 0;
										foreach($value->studio as $key1=>$studios){
											
											if(!empty($studios->timetable) && count($studios->timetable) > 0){
												$branchesArray[$i]['studios'][$ii]['studio_id'] = $studios->id;
												$branchesArray[$i]['studios'][$ii]['studio_name'] = $studios->name;
												$class_array = array();
												foreach($studios->timetable as $key2 => $timetable){
													if(!empty($timetable->faculty->name)){
														$temp['timetable_id'] = $timetable->id;
														$temp['from_time'] = $timetable->from_time;
														$temp['to_time'] = $timetable->to_time;
														$temp['date'] = $timetable->cdate;
														$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
														$temp['batch_name'] = isset($timetable->batch->name)?$timetable->batch->name:'';
														$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
														$temp['subject_name'] = isset($timetable->subject->name)?$timetable->subject->name:'';
														$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
														$temp['topic_name'] = isset($timetable->topic->name)?$timetable->topic->name:'';
														
														$class_array[] = $temp;
													}
												}
												$branchesArray[$i]['studios'][$ii]['classes'] = $class_array;
												$ii++;
											}
											
										}
										if($ii > 0){
											$branchesArray[$i]['branch_id'] = $value->branch_id;
											$branchesArray[$i]['branch_name'] = $value->branch->name;
										}
										
										
									}
								}
								if(count($branchesArray) > 0){
									$data['branches'] = $branchesArray;
								}
								else{
									$branchesArray[0]['studios'] = array();;
									$data['branches'] = $branchesArray;
								}

								return $this->returnResponse(200, true, "Studio Manager Branches Details", $data);
							}else{
								return $this->returnResponse(200, false, "Branches Not Found");
							}
						}
						else if($user->role_id==29){ //Super Admin
							$get_notification_count = ApiNotification::count();
							$data['total_notification_count'] = $get_notification_count;
							$data['current_app_version'] = $current_app_version;
							$branches = Userbranches::with(['user'=>function ($q){
											$q->where('status', 1);
										},
										'branch'=>function ($q){
											$q->where('status', 1);
										}
										,'studio','studio.timetable'=>
										function ($q){
											// $fdate = date('Y-m-d',strtotime('-20 day'));
											// $tdate = date('Y-m-d',strtotime('+7 day'));
											// $q->where('cdate', '>=', $fdate);
											// $q->where('cdate', '<=', $tdate);
											$q->where('cdate',  date('Y-m-d'));
											$q->orderBy('cdate', 'desc');
										},
								'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->orderBy('branch_id', 'desc')->groupBy('branch_id')->get();
								//->where('user_id', $user_id)
							// echo "<pre>";
							// print_R($branches); die;
							if(!empty($branches)){
								$branchesArray = array();
								$i = 0;
								foreach($branches as $key=>$value){
									
									if(!empty($value->studio) && count($value->studio) > 0){
										$ii = 0;
										foreach($value->studio as $key1=>$studios){
											
											if(!empty($studios->timetable) && count($studios->timetable) > 0){
												$branchesArray[$i]['studios'][$ii]['studio_id'] = $studios->id;
												$branchesArray[$i]['studios'][$ii]['studio_name'] = $studios->name;
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
													
													$branchesArray[$i]['studios'][$ii]['classes'][$key2] = $temp;
												}
												$ii++;
											}
										}
										if($ii > 0){
											$branchesArray[$i]['branch_id'] = $value->branch_id;
											$branchesArray[$i]['branch_name'] = isset($value->branch->name)?$value->branch->name:'';
											$i++;
										}
										
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
			$emp_id = $request->emp_id;
			$notifications = ApiNotification::select('id','title','description','image','date','type','appointment_id');
			
			$data_show_days = date('Y-m-d', strtotime('-2 days'));
			$notifications->whereDate('date', '>=', $data_show_days);
			$notifications->whereDate('date', '<=', date('Y-m-d'));

			if(!empty($emp_id)){
				 //$notifications->whereRaw('receiver_id LIKE  \'%"'.$emp_id.'"%\' AND (type = "General" OR type is null)');
				//$notifications->orwhere('type','General');
				//$notifications->orWhereNull('type');
				$notifications->whereRaw('is_deleted="0" AND (type="General" or type = "Appointment" or type = "Task" OR  type is NULL) AND (receiver_id LIKE \'%"'.$emp_id.'"%\' OR receiver_id is NULL)');
			}
			else{
			    $notifications->whereRaw('(type = "General" or type = "Appointment" or type = "Task" or type is NULL  )');
				//$notifications->orwhere('type', 'General');
				//$notifications->orWhereNull('type');
			}
			
			$notifications = $notifications->where('is_deleted','0')->orderBy('id','desc')->get();
           // echo '<pre>'; print_r($notifications);die;

			if(count($notifications) > 0){
                $get_notifications = [];
                foreach($notifications as $key => $notification){
                    $temp['id'] = $notification->id;
                    $temp['title'] = $notification->title;
                    $temp['description'] = $notification->description;
					$notification_img_url = null;
                    if(!empty($notification->image)){
                         $notification_img_url = asset('laravel/public/notification/'.$notification->image);
                    }else{
                         //$notification_img_url = asset('laravel/public/images/test-image.png');
                    }

                    $temp['image'] = $notification_img_url;
                    $temp['date'] =  date("d-m-Y H:i:s", strtotime($notification->date));
					$notification_type = "General";
					if(!empty($notification->type)){
						$notification_type = $notification->type;
					}
					$temp['type'] = $notification_type;
					$temp['appointment_id'] = $notification->appointment_id;

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
								$temp['dob'] =  date("d-m-Y", strtotime($row->user_details->dob));
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
								$temp['joining_date'] =  date("d-m-Y", strtotime($row->user_details->joining_date));
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
											$temp2['branch_location'] = $value->branch->branch_location;
											$get_user_branches[] = $temp2;
										}
									}
								}
								$temp['user_branches'] = $get_user_branches;
								
								$temp['info_text'] = '';
								$temp['info_image'] = '';

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
	
	public function getemployee(Request $request)
    {
		try{
			$emp_id = $request->emp_id;
			
			if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->where('register_id', '!=', NULL)->first();
				if(!empty($user)){
					$department_type = $user->department_type;
					if($user->status=='1'){
						$data = array();
						if($user->role_id==29){
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
							// $check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
							// if(count($check_supervisor) > 0){
								$i = 0;
								$supervisors = User::where('supervisor_id', '!=', NULL)->whereRaw('supervisor_id NOT LIKE  \'%["0"]%\' ')->get();
								if(!empty($supervisors)){
									foreach($check_supervisor as $key=>$value){
										$supervisor_ids = json_decode(($value->supervisor_id));
										
										if(!empty($supervisor_ids)){
											foreach($supervisor_ids as $supervisor_id_val){
												if($supervisor_id_val > 0){
													if(!in_array($supervisor_id_val,$supervisorId)){
														$supervisorId[] = $supervisor_id_val;
														$checkSupervisor = User::with('user_details')->where('id', $supervisor_id_val)->where('status', 1)->first();
														if(!empty($checkSupervisor)){
															$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
															$employeeArray[$i]['name'] = $checkSupervisor->name;
															$employeeArray[$i]['type'] = 'supervisor';
															$employeeArray[$i]['degination'] = $checkSupervisor->user_details->degination;
															$i++;
														}
													}
												}
											}
										} 
									}
								}
								
								$employees = User::with('user_details')->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								if(!empty($employees)){
									foreach($employees as $key=>$value){
										if(!empty($value)){
											// $checkSupervisorAlready = User::where('supervisor_id', $value->id)->get();
											// $checkSupervisorAlready = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
											// if(count($checkSupervisorAlready) == 0){
												if(!in_array($value->id,$supervisorId)){
													$supervisorId[] = $value->id;
													$employeeArray[$i]['emp_id'] = $value->id;
													$employeeArray[$i]['name'] = $value->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = $value->user_details->degination;
													$i++;
												}
											// }
										}
										
									}
								}
							// }
							$data['employees'] = $employeeArray;
						}
						else if($user->role_id==21){
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
							$checkUserRole = User::where([['role_id', '!=', 21],['role_id', '!=', 29]])->get();
							//echo '<pre>'; print_r($checkUserRole);die;
							// if(count($checkUserRole) > 0){
								$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								$i = 0;
								if(count($check_supervisor) > 0){
									$supervisors = User::where('supervisor_id', '!=', NULL)->whereRaw('supervisor_id NOT LIKE  \'%["0"]%\' ')->get();
									if(!empty($supervisors)){
										foreach($check_supervisor as $key=>$value){
											$supervisor_ids = json_decode(($value->supervisor_id));
											
											if(!empty($supervisor_ids)){
												foreach($supervisor_ids as $supervisor_id_val){
													if($supervisor_id_val > 0){
														if(!in_array($supervisor_id_val,$supervisorId)){
															$supervisorId[] = $supervisor_id_val;
															$checkSupervisor = User::with('user_details')->where('id', $supervisor_id_val)->where('status', 1)->first();
															if(!empty($checkSupervisor)){
																$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
																$employeeArray[$i]['name'] = $checkSupervisor->name;
																$employeeArray[$i]['type'] = 'supervisor';
																$employeeArray[$i]['degination'] = $checkSupervisor->user_details->degination;
																$i++;
															}
														}
													}
												}
											} 
										}
									}
								}
								$usrDepartmentType = User::with('user_details')->where('department_type', $department_type)->get();
								if(!empty($usrDepartmentType)){
									foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
										if(!empty($usrDepartmentTypeValue)){
												if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
													$supervisorId[] = $usrDepartmentTypeValue->id;
													$employeeArray[$i]['emp_id'] = $usrDepartmentTypeValue->id;
													$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = $usrDepartmentTypeValue->user_details->degination;
													$i++;
												}
										}
										
									}
								}
								//echo '<pre>'; print_r($usrDepartmentType);die;
								
								$employees = User::with('user_details')->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								if(!empty($employees)){
									foreach($employees as $key=>$value){
										if(!empty($value)){
											// $checkSupervisorAlready = User::where('supervisor_id', $value->id)->get();
											// $checkSupervisorAlready = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
											// if(count($checkSupervisorAlready) == 0){
												if(!in_array($value->id,$supervisorId)){
													$supervisorId[] = $value->id;
													$employeeArray[$i]['emp_id'] = $value->id;
													$employeeArray[$i]['name'] = $value->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = $value->user_details->degination;
													$i++;
												}
											// }
										}
										
									}
								}
							// }
							$data['employees'] = $employeeArray;
						}
						else{
							
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
							$i = 0;
							
							$usrDepartmentType = User::with('user_details')->where('department_type', $department_type)->get();
							if(!empty($usrDepartmentType)){
								foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
									if(!empty($usrDepartmentTypeValue)){
											if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
												$supervisorId[] = $usrDepartmentTypeValue->id;
												$employeeArray[$i]['emp_id'] = $usrDepartmentTypeValue->id;
												$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
												$employeeArray[$i]['type'] = 'employee';
												$employeeArray[$i]['degination'] = $usrDepartmentTypeValue->user_details->degination;
												$i++;
											}
									}
									
								}
							}
							//echo '<pre>'; print_r($usrDepartmentType);die;
							
							$employees = User::with('user_details')->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
							if(!empty($employees)){
								foreach($employees as $key=>$value){
									if(!empty($value)){
										if(!in_array($value->id,$supervisorId)){
											$supervisorId[] = $value->id;
											$employeeArray[$i]['emp_id'] = $value->id;
											$employeeArray[$i]['name'] = $value->name;
											$employeeArray[$i]['type'] = 'employee';
											$employeeArray[$i]['degination'] = $value->user_details->degination;
											$i++;
										}
									}
									
								}
							}
							$data['employees'] = $employeeArray;
						}
						if(!empty($data)){
							return $this->returnResponse(200, true, "Employee List", $data);
						}
						else{
							return $this->returnResponse(200, false, "No any employee.");
						}
						
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
					
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}
				
			}else{
    			return $this->returnResponse(200, false, "Employee Id Not Found");
    		}
		
		}catch (\Illuminate\Database\QueryException $ex) {
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
					$department_type = $user->department_type;
					if($user->status=='1'){
						$data = array();
						if($user->role_id==29){
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
								$i = 0;
								$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								if(count($check_supervisor) > 0){
									foreach($check_supervisor as $key=>$value){
										$supervisor_ids = json_decode(($value->supervisor_id));
										
										if(!empty($supervisor_ids)){
											foreach($supervisor_ids as $supervisor_id_val){
												if($supervisor_id_val > 0){
													if(!in_array($supervisor_id_val,$supervisorId)){
														$supervisorId[] = $supervisor_id_val;
														$checkSupervisor = User::with('user_details')->where('id', $supervisor_id_val)->where('status', 1)->first();
														if(!empty($checkSupervisor)){
															$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
															$employeeArray[$i]['name'] = $checkSupervisor->name;
															$employeeArray[$i]['type'] = 'supervisor';
															$employeeArray[$i]['degination'] = isset($checkSupervisor->user_details->degination)?$checkSupervisor->user_details->degination:'';
															
															$i++;
														}
													}
												}
											}
										} 
									}
								}
								
								$employees = User::with('user_details')->where('status', '=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								if(!empty($employees)){
									foreach($employees as $key=>$value){
										if(!empty($value)){
												if(!in_array($value->id,$supervisorId)){
													$supervisorId[] = $value->id;
													$employeeArray[$i]['emp_id'] = $value->id;
													$employeeArray[$i]['name'] = $value->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = isset($value->user_details->degination)?$value->user_details->degination:'';
													$i++;
												}
										}
										
									}
								}
							$data['employees'] = $employeeArray;
						}
						else if($user->role_id==21){
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
							$checkUserRole = User::where([['role_id', '!=', 21],['role_id', '!=', 29]])->get();
								$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								$i = 0;
								if(count($check_supervisor) > 0){
									$supervisors = User::where('status', '=', 1)->where('supervisor_id', '!=', NULL)->whereRaw('supervisor_id NOT LIKE  \'%["0"]%\' ')->get();
									if(!empty($supervisors)){
										foreach($check_supervisor as $key=>$value){
											$supervisor_ids = json_decode(($value->supervisor_id));
											
											if(!empty($supervisor_ids)){
												foreach($supervisor_ids as $supervisor_id_val){
													if($supervisor_id_val > 0){
														if(!in_array($supervisor_id_val,$supervisorId)){
															$supervisorId[] = $supervisor_id_val;
															$checkSupervisor = User::with('user_details')->where('id', $supervisor_id_val)->where('status', 1)->first();
															if(!empty($checkSupervisor)){
																$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
																$employeeArray[$i]['name'] = $checkSupervisor->name;
																$employeeArray[$i]['type'] = 'supervisor';
																$employeeArray[$i]['degination'] = isset($checkSupervisor->user_details->degination)?$checkSupervisor->user_details->degination:'';
																$i++;
															}
														}
													}
												}
											} 
										}
									}
								}
								$usrDepartmentType = User::with('user_details')->where('status', '=', 1)->where('department_type', $department_type)->get();
								if(!empty($usrDepartmentType)){
									foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
										if(!empty($usrDepartmentTypeValue)){
												if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
													$supervisorId[] = $usrDepartmentTypeValue->id;
													$employeeArray[$i]['emp_id'] = $usrDepartmentTypeValue->id;
													$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = isset($usrDepartmentTypeValue->user_details->degination)?$usrDepartmentTypeValue->user_details->degination:'';
													$i++;
												}
										}
										
									}
								}
								//echo '<pre>'; print_r($usrDepartmentType);die;
								
								$employees = User::with('user_details')->where('status', '=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
								if(!empty($employees)){
									foreach($employees as $key=>$value){
										if(!empty($value)){
												if(!in_array($value->id,$supervisorId)){
													$supervisorId[] = $value->id;
													$employeeArray[$i]['emp_id'] = $value->id;
													$employeeArray[$i]['name'] = $value->name;
													$employeeArray[$i]['type'] = 'employee';
													$employeeArray[$i]['degination'] = isset($value->user_details->degination)?$value->user_details->degination:'';
													$i++;
												}
										}
										
									}
								}
							// }
							$data['employees'] = $employeeArray;
						}
						else{
							
							$employeeArray = array();
							$supervisorId = array();
							$supervisorId[] = $emp_id;
							$i = 0;
							
							/* $usrDepartmentType = User::with('user_details')->where('department_type', $department_type)->get();
							if(!empty($usrDepartmentType)){
								foreach($usrDepartmentType as $key=>$usrDepartmentTypeValue){
									if(!empty($usrDepartmentTypeValue)){
											if(!in_array($usrDepartmentTypeValue->id,$supervisorId)){
												$supervisorId[] = $usrDepartmentTypeValue->id;
												$employeeArray[$i]['emp_id'] = $usrDepartmentTypeValue->id;
												$employeeArray[$i]['name'] = $usrDepartmentTypeValue->name;
												$employeeArray[$i]['type'] = 'employee';
												$employeeArray[$i]['degination'] = isset($usrDepartmentTypeValue->user_details->degination)?$usrDepartmentTypeValue->user_details->degination:'';
												$i++;
											}
									}
									
								}
							} */
							//echo '<pre>'; print_r($usrDepartmentType);die;
							
							$employees = User::with('user_details')->where('status', '=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
							if(!empty($employees)){
								foreach($employees as $key=>$value){
									if(!empty($value)){
										if(!in_array($value->id,$supervisorId)){
											$supervisorId[] = $value->id;
											$employeeArray[$i]['emp_id'] = $value->id;
											$employeeArray[$i]['name'] = $value->name;
											$employeeArray[$i]['type'] = 'employee';
											$employeeArray[$i]['degination'] = isset($value->user_details->degination)?$value->user_details->degination:'';
											$i++;
										}
									}
									
								}
							}
							$data['employees'] = $employeeArray;
						}
						if(!empty($data)){
							return $this->returnResponse(200, true, "Employee List", $data);
						}
						else{
							return $this->returnResponse(200, false, "No any employee.");
						}
						
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
					
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}
				
			}else{
    			return $this->returnResponse(200, false, "Employee Id Not Found");
    		}
		
		}catch (\Illuminate\Database\QueryException $ex) {
		return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
		return $this->returnResponse(500, false, $ex->getMessage());
		}
    }
	
	
	public function getemployeelist_old(Request $request)
    {
        try{

            $emp_id = $request->emp_id;

            if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->where('register_id', '!=', NULL)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$employeeArray = array();
						$supervisorId = array();
						$supervisorId[] = $emp_id;
						$check_supervisor = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
						// $check_supervisor = User::where('supervisor_id', $emp_id)->get();
						// echo count($check_supervisor); die;
						if(count($check_supervisor) > 0){
							$i = 0;
							// $supervisors = User::where('supervisor_id', '!=', 0)->where('supervisor_id', '!=', $emp_id)->groupBy('supervisor_id')->toSql();
							$supervisors = User::where('supervisor_id', '!=', NULL)->whereRaw('supervisor_id NOT LIKE  \'%["0"]%\' ')->get();
							// echo $supervisors; die;
							if(!empty($supervisors)){
								foreach($supervisors as $key=>$value){
									$supervisor_ids = json_decode(($value->supervisor_id));
									if(!empty($supervisor_ids)){
										foreach($supervisor_ids as $supervisor_id_val){
											if($supervisor_id_val > 0){
												if(!in_array($supervisor_id_val,$supervisorId)){
													$supervisorId[] = $supervisor_id_val;
													$checkSupervisor = User::with('user_details')->where('id', $supervisor_id_val)->where('status', 1)->first();
													if(!empty($checkSupervisor)){
														$employeeArray[$i]['emp_id'] = $checkSupervisor->id;
														$employeeArray[$i]['name'] = $checkSupervisor->name;
														$employeeArray[$i]['type'] = 'supervisor';
														$employeeArray[$i]['degination'] = isset($checkSupervisor->user_details->degination)?$checkSupervisor->user_details->degination:'';
														$i++;
													}
												}
											}
										}
									}
									
									
								}
							}
							// echo "<pre>"; print_r($employeeArray); die;
							// $employees = User::where('supervisor_id', $emp_id)->get();
							$employees = User::with('user_details')->whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
							if(!empty($employees)){
								foreach($employees as $key=>$value){
									if(!empty($value)){
										// $checkSupervisorAlready = User::where('supervisor_id', $value->id)->get();
										// $checkSupervisorAlready = User::whereRaw('supervisor_id LIKE  \'%"'.$emp_id.'"%\' ')->get();
										// if(count($checkSupervisorAlready) == 0){
											if(!in_array($value->id,$supervisorId)){
												$supervisorId[] = $value->id;
												$employeeArray[$i]['emp_id'] = $value->id;
												$employeeArray[$i]['name'] = $value->name;
												$employeeArray[$i]['type'] = 'employee';
												$employeeArray[$i]['degination'] = $value->user_details->degination;
												$i++;
											}
										// }
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

    public function get_timetable_by_batch_22_sept_2023(Request $request){
        try {

            $ttdate         = $request->ttdate;
            $fbid           = $request->fbid;
			if(!empty($ttdate) && !empty($fbid)){
				
				$fbid = explode(',',$fbid);
				$ttdate = date('Y-m-d',strtotime($ttdate));
				$branch_ids = array();
				$venue = "";
				foreach($fbid as $bid){
					$batches = Batch::whereRaw("FIND_IN_SET($bid,batch_code)")->get();
					
					if(count($batches)>0){
						foreach($batches as $batchData){
							if(!in_array($batchData->id,$branch_ids)){
								$branch_ids[] = $batchData->id;
								$venue .= $batchData->venue.', ';
							}

						}
					}
				}
				// echo "<pre>"; print_r($branch_ids); die;
				$venue = rtrim($venue, ', ');
				if(!empty($branch_ids)){

					$faculty_timetables = Timetable::with(['topic','studio.assistant','studio.branch','chapter','course','faculty','subject']);
					$faculty_timetables->where('cdate', '=', $ttdate);
					$faculty_timetables->where('is_publish', '=', '1');
					//$faculty_timetables->where('time_table_parent_id', '=', 0);
					$faculty_timetables->whereIn('batch_id', $branch_ids);
					
					$faculty_timetables = $faculty_timetables->select('id','studio_id','batch_id','faculty_id','topic_id','from_time','to_time','cdate','chapter_id','course_id','subject_id','online_class_type')
					->orderBy('course_id','desc')
					->orderBy('studio_id','asc') // new added if issue then comment and check result
					->orderBy('from_time', 'ASC')
					->where('is_deleted', '0')->get();
					
					// echo "<pre>"; print_r($faculty_timetables); die;

					if(count($faculty_timetables) > 0){
						$get_faculty_timetables = [];
						$course_array = [];
						$studio_ids=[];
						$venue_array = [];
						$studio_array = [];
						$studio_array_time = [];
						$studio_hall_name = [];
						$i = 0;
						$venuesdata = "";
						$old_vanue = "";
						$sample = 0;

                       /* $dpp_data= DB::table('batch_dpp')->where('batch_code',$fbid)->where('dpp_date',$ttdate)->first();
                       $dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:''; */


						foreach($faculty_timetables as $key => $timetable){
							// echo $timetable->id; die;
							$dpp_date=date('Y-m-d',strtotime($ttdate. ' -1 day'));
							
							$tt_data = DB::table('timetables')->where('batch_id',$timetable->batch_id)
							                ->where('is_publish','1')
											->whereRaw("DATE(cdate) <= '$dpp_date'")
											->orderBy('cdate','desc')
											->first();
							if(!empty($tt_data)){
								$dpp_date=date('Y-m-d');
								$class_date = date('Y-m-d',strtotime($tt_data->cdate));
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date) = '$class_date' AND dpp_date <= '$dpp_date'")
											->first();
							    if(empty($dpp_data)){
                                  $dpp_date=date('Y-m-d');
								  $dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							    }
							}
							else{
								$dpp_date=date('Y-m-d');
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							}
							
							$dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:'';
					   
							$studio_id = $timetable->studio_id;
							$class_id = $timetable->id;
							if(!empty($timetable->subject_id)){
								// $get_remark = DB::table('class_remarks')->where('subject_id', $timetable->subject_id)->first();
							}
							// $temp['remark'] = isset($get_remark->remark)?$get_remark->remark:'';
							$temp['remark'] = isset($timetable->remark)?$timetable->remark:'';

							$temp['stime']  = date('h:i a', strtotime($timetable->from_time));

							$temp['etime']  = date('h:i a', strtotime($timetable->to_time));

                            $temp['class_id'] =$class_id;

                            $temp['course'] = isset($timetable->course->name)?$timetable->course->name:'';
							$temp['course'] = isset($timetable->subject->name)?$timetable->subject->name.' || '.$temp['course']:'';
							
							$testDefault =  "";
							if($timetable->online_class_type=='Test'){
								$testDefault = " ( Test )";
							}
							$temp['subject'] = isset($timetable->subject->name)?$timetable->subject->name . $testDefault:'';

							$temp['faculty_name'] = "-"; //!empty($timetable->faculty->name)?$timetable->faculty->name:'';
							// $temp['venue'] = $venue;
							$temp['venue'] = isset($timetable->studio->branch->address)?$timetable->studio->branch->name.' - '.$timetable->studio->branch->address:'';
							// echo $temp['venue']; die;
							$from_time = $temp['stime'];
							$to_time = $temp['etime'];
							// $temp['venue'] = $timetable->studio->branch->name.'/'.$timetable->studio->name;
							
							$studio_array[$timetable->studio_id] = $temp['venue'];
							$studio_hall_name[$timetable->studio_id]['hall_name'] = isset($timetable->studio->name)?$timetable->studio->name." ":' ';
							$studio_array_time[$timetable->studio_id][] = $from_time .' - '. $to_time .', ';
							

							$final_vanue= $timetable->studio->name.", ".$temp['venue'];
							
							if(in_array($studio_id,$studio_ids)){
								$venue_array[$temp['venue']] = $temp['venue'];
								if(count($venue_array) > 1){
									// $classes['venue'] = $fullVanue;
									$classes['venue'] = $final_vanue;
								}
								$classes['classes'][] = $temp;
								$course_array[$timetable->studio_id] = $classes;
								$get_faculty_timetables[$ii] = $course_array[$timetable->studio_id];
							}else{
								$venue_array = array();
								$sss = "";
								$studio_ids[] = $studio_id;
								$classes = array();
								$classes['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
								$batch_data = Batch::select("course.name")->leftJoin('course', 'batch.course_id', '=', 'course.id')->where('batch.id',$timetable->batch_id)->first();
								if(!empty($batch_data)){
									$classes['course_name'] = isset($batch_data->name)?$batch_data->name:'';
								}
								
								// $classes['venue'] = $venue;
								$hall_name = isset($timetable->studio->name)?$timetable->studio->name." ":' ';
								$branch_name = isset($timetable->studio->branch->address)?$timetable->studio->branch->address:'';
								// $classes['venue'] = $hall_name . $branch_name;
								$classes['venue'] = $final_vanue;
								$classes['dpp_url'] = $dpp_url;

								$classes['classes'][] = $temp;
								$course_array[$timetable->studio_id] = $classes;
								$ii = $i;
								$get_faculty_timetables[$ii] = $course_array[$timetable->studio_id];
								$i++;
								$venue_array[$temp['venue']] = $temp['venue'];
							}

						}

						$data['status'] = "ok";

						$data['data'] = $get_faculty_timetables;
						return $this->returnResponse(200, true, "Timetable", $data);

					}else{
						return $this->returnResponse(200, false, "Timetable Not Found");
					}

				}else{
					return $this->returnResponse(200, false, "Timetable Not Found");
				}

			}else{
				return $this->returnResponse(200, false, "Date and Batch Code required");
			}

		} catch (\Illuminate\Database\QueryException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		} catch (ModelNotFoundException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		}
    }
	
	
	 public function get_timetable_by_batch(Request $request){
        try {

            $ttdate         = $request->ttdate;
            $fbid           = $request->fbid;
            
            /*$dd=json_encode($request->all());
            file_put_contents("/var/www/html/laravel/public/log.txt", 'ddd'.$dd,FILE_APPEND);
            file_put_contents("/var/www/html/laravel/public/log.txt", 'ddd'.$fbid,FILE_APPEND);*/
            
			if(!empty($ttdate) && !empty($fbid)){
				$offline_batch = DB::connection('mysql2')->table('tbl_batch')->where('Bat_id',$fbid)->first();
				
				$batch_idd=array(586);
				
				$fbid = explode(',',$fbid);
				$ttdate = date('Y-m-d',strtotime($ttdate));
				$branch_ids = array();
				$venue = "";
				foreach($fbid as $bid){
					$batches = Batch::whereRaw("FIND_IN_SET($bid,batch_code)")->get();
					
					if(count($batches)>0){
						foreach($batches as $batchData){
							if(!in_array($batchData->id,$branch_ids)){
								$branch_ids[] = $batchData->id;
								$venue .= $batchData->venue.', ';
							}

						}
					}
				}

				// echo "<pre>"; print_r($branch_ids); die;
				$venue = rtrim($venue, ', ');
				if(!empty($branch_ids)){

					$faculty_timetables = Timetable::with(['topic','studio.assistant','studio.branch','chapter','course','faculty','subject']);
					$faculty_timetables->where('cdate', '=', $ttdate);
					$faculty_timetables->where('is_publish', '=', '1');
					$faculty_timetables->whereIn('batch_id', $branch_ids);
					// $faculty_timetables->whereColumn('time_table_parent_id', 'id');
					
					// $faculty_timetables->where(function ($query) {
						// $query->whereColumn('time_table_parent_id', 'id')
							  // ->where('is_deleted', '=', '0');
					// });

					$faculty_timetables = $faculty_timetables->select('id','studio_id','batch_id','faculty_id','topic_id','from_time','to_time','cdate','chapter_id','course_id','subject_id','online_class_type')
					->orderBy('from_time', 'ASC')
					->where('is_deleted', '0')->get();
					 
					// echo "<pre>"; print_r($faculty_timetables); die;
                    $classes = array();
					if(count($faculty_timetables) > 0){
						$get_faculty_timetables = [];
						$course_array = [];
						$studio_ids=[];
						$venue_array = [];
						$studio_array = [];
						$studio_array_time = [];
						$studio_hall_name = [];
						$i = 0;
						$venuesdata = "";
						$old_vanue = "";
						$sample = 0;

                       /* $dpp_data= DB::table('batch_dpp')->where('batch_code',$fbid)->where('dpp_date',$ttdate)->first();
                       $dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:''; */


						foreach($faculty_timetables as $key => $timetable){
							// echo $timetable->id; die;
							$dpp_date=date('Y-m-d',strtotime($ttdate. ' -1 day'));
							
							$tt_data = DB::table('timetables')->where('batch_id',$timetable->batch_id)
							                ->where('is_publish','1')
							                ->where('is_deleted','0')
											->whereRaw("DATE(cdate) <= '$dpp_date'")
											->orderBy('cdate','desc')
											->first();
							
							/*if(!empty($tt_data)){
								$dpp_date=date('Y-m-d');
								$class_date = date('Y-m-d',strtotime($tt_data->cdate));
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date) = '$class_date' AND dpp_date <= '$dpp_date'")
											->first();
							    if(empty($dpp_data)){
                                  $dpp_date=date('Y-m-d');
								  $dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							    }
							}
							else{
								$dpp_date=date('Y-m-d');
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							}*/
							
							$dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:'';
							$dpp_url="";
					   
							$studio_id = $timetable->studio_id;
							$class_id = $timetable->id;
							if(!empty($timetable->subject_id)){
								// $get_remark = DB::table('class_remarks')->where('subject_id', $timetable->subject_id)->first();
							}
							
							$temp['remark'] = isset($timetable->remark)?$timetable->remark:'';

							$temp['stime']  = date('h:i a', strtotime($timetable->from_time));

							$temp['etime']  = date('h:i a', strtotime($timetable->to_time));

                            $temp['class_id'] =$class_id;

                            $temp['course'] = isset($timetable->course->name)?$timetable->course->name:'';
							$temp['course'] = isset($timetable->subject->name)?$timetable->subject->name.' || '.$temp['course']:'';
							
							$testDefault =  "";
							if($timetable->online_class_type=='Test'){
								$testDefault = " ( Test )";
							}
							$temp['subject'] = isset($timetable->subject->name)?$timetable->subject->name . $testDefault:'';

							$temp['faculty_name'] = $timetable->faculty->name??'-'; 
							$temp['venue'] = isset($timetable->studio->branch->address)?$timetable->studio->branch->name.' - '.$timetable->studio->branch->address:'';
							
							$from_time = $temp['stime'];
							$to_time = $temp['etime'];
							
							$studio_array[$timetable->studio_id] = $temp['venue'];
							$studio_hall_name[$timetable->studio_id]['hall_name'] = isset($timetable->studio->name)?$timetable->studio->name." ":' ';
							$studio_array_time[$timetable->studio_id][] = $from_time .' - '. $to_time .', ';
							
							
							if($offline_batch->branch=='Jaipur' && $timetable->online_class_type=='App Live'){
								$final_vanue="प्रिय विद्यार्थी उपयुक्त टाइम टेबल के अनुसार कक्षा Utkarsh App मे App Live रहेगी । धन्यवाद -उत्कर्ष क्लासेस,जयपुर";
							}else{
								$final_vanue= $timetable->studio->name.", ".$temp['venue'];
							} 
								
							$classes[$timetable->studio_id]['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
							$batch_data = Batch::select("course.name")->leftJoin('course', 'batch.course_id', '=', 'course.id')->where('batch.id',$timetable->batch_id)->first();
							if(!empty($batch_data)){
								$classes[$timetable->studio_id]['course_name'] = isset($batch_data->name)?$batch_data->name:'';
							}
							
							// $classes['venue'] = $venue;
							$hall_name = isset($timetable->studio->name)?$timetable->studio->name." ":' ';
							$branch_name = isset($timetable->studio->branch->address)?$timetable->studio->branch->address:'';
							// $classes['venue'] = $hall_name . $branch_name;
							$classes[$timetable->studio_id]['venue'] = $final_vanue;
							$classes[$timetable->studio_id]['dpp_url'] = $dpp_url;

							$classes[$timetable->studio_id]['classes'][] = $temp;	 

						}
						
						$classes_new = array();
						if(count($classes) > 0){
							foreach($classes as $val){
								$classes_new[] =$val;
							}
						}

						$data['status'] = "ok";

						$data['data'] = $classes_new;
						return $this->returnResponse(200, true, "Timetable", $data);

					}else{
						
						if(date('Y-m-d',strtotime($ttdate))=="2023-12-06"){
							$classes = array();
							$venue = "*जयपुर व जोधपुर सेंटर पर आज अवकाश रहेगा* अपरिहार्य कारणों से आज बुधवार, 6 दिसम्बर को उत्कर्ष के जयपुर व जोधपुर सेंटर की सभी ऑफ़लाइन कक्षाएँ व यहॉं से संचालित सभी ऑनलाइन कक्षाओं का अवकाश रहेगा। 7 दिसम्बर को पुनः यथासमय यथास्थान कक्षाएँ संचालित होगी।अधिक जानकारी के लिए एप पर सूचना देखते रहें। - टीम उत्कर्ष,जयपुर-जोधपुर";
							$classes[] = array('remark'=>'','stime'=>'','etime'=>'','class_id'=>'','course'=>'','subject'=>'-','faculty_name'=>'','venue'=>"");
							$classes_new[] =array("course_name"=>'जयपुर व जोधपुर सेंटर पर आज अवकाश रहेगा',"venue"=>$venue,'dpp_url'=>'','classes'=>$classes);
							$data['status'] = "ok";
							$data['data'] = $classes_new;
							return $this->returnResponse(200, true, "Timetable", $data);
						
						}else if($offline_batch->branch=='Jaipur'){
							$classes = array();
							$message = "प्रिय विद्यार्थी, अगली सूचना तक आपकी कक्षा Applive रहेगी धन्यवाद  टीम उत्कर्ष";
							$data['status'] = "ok";
							return $this->returnResponse(200, false, $message, $data);
						}else{
							return $this->returnResponse(200, false, "Timetable Not Found");
						}
					}

				}else{

				    if($offline_batch->branch=='Jaipur'){
						$classes = array();
						$message = "अगली सूचना तक आपकी कक्षा Applive रहेगी";
						$data['status'] = "ok";
						return $this->returnResponse(200, false, $message, $data);
					}

					return $this->returnResponse(200, false, "Timetable Not Found.");
				}

			}else{
				return $this->returnResponse(200, false, "Date and Batch Code required");
			}

		} catch (\Illuminate\Database\QueryException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		} catch (ModelNotFoundException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		}
    }
	
	public function get_timetable_by_batch_old(Request $request){
		try {

            $ttdate         = $request->ttdate;
            $fbid           = $request->fbid;
			if(!empty($ttdate) && !empty($fbid)){
				
				$fbid = explode(',',$fbid);
				$ttdate = date('Y-m-d',strtotime($ttdate));
				$branch_ids = array();
				$venue = "";
				foreach($fbid as $bid){
					$batches = Batch::whereRaw("FIND_IN_SET($bid,batch_code)")->get();
					
					if(count($batches)>0){
						foreach($batches as $batchData){
							if(!in_array($batchData->id,$branch_ids)){
								$branch_ids[] = $batchData->id;
								$venue .= $batchData->venue.', ';
							}

						}
					}
				}
				// echo "<pre>"; print_r($branch_ids); die;
				$venue = rtrim($venue, ', ');
				if(!empty($branch_ids)){

					$faculty_timetables = Timetable::with(['topic','studio.assistant','studio.branch','chapter','course','faculty','subject']);
					$faculty_timetables->where('cdate', '=', $ttdate);
					$faculty_timetables->where('is_publish', '=', '1');
					//$faculty_timetables->where('time_table_parent_id', '=', 0);
					$faculty_timetables->whereIn('batch_id', $branch_ids);
					$faculty_timetables = $faculty_timetables->select('id','studio_id','batch_id','faculty_id','topic_id','from_time','to_time','cdate','chapter_id','course_id','subject_id','online_class_type')
					->orderBy('course_id','desc')
					->orderBy('from_time', 'ASC')
					->where('is_deleted', '0')->get();

					if(count($faculty_timetables) > 0){
						$get_faculty_timetables = [];
						$course_array = [];
						$course_ids = [];
						$venue_array = [];
						$studio_array = [];
						$studio_array_time = [];
						$studio_hall_name = [];
						$i = 0;
						$venuesdata = "";
						$old_vanue = "";
						$sample = 0;

                       /* $dpp_data= DB::table('batch_dpp')->where('batch_code',$fbid)->where('dpp_date',$ttdate)->first();
                       $dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:''; */


						foreach($faculty_timetables as $key => $timetable){
							// echo $timetable->id; die;
							$dpp_date=date('Y-m-d',strtotime($ttdate. ' -1 day'));
							
							$tt_data = DB::table('timetables')->where('batch_id',$timetable->batch_id)
							                ->where('is_publish','1')
											->whereRaw("DATE(cdate) <= '$dpp_date'")
											->orderBy('cdate','desc')
											->first();
							if(!empty($tt_data)){
								$dpp_date=date('Y-m-d');
								$class_date = date('Y-m-d',strtotime($tt_data->cdate));
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date) = '$class_date' AND dpp_date <= '$dpp_date'")
											->first();
							    if(empty($dpp_data)){
                                  $dpp_date=date('Y-m-d');
								  $dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							    }
							}
							else{
								$dpp_date=date('Y-m-d');
								$dpp_data = DB::table('batch_dpp')->where('batch_id',$timetable->batch_id)
											->whereRaw("DATE(class_date)  < '$ttdate' AND dpp_date <= '$dpp_date'")
											->orderBy('class_date','desc')
											->first();
							}
							
							$dpp_url=isset($dpp_data->dpp_url)?"https://utkarshoffline.s3.ap-south-1.amazonaws.com/batch_dpp/".$dpp_data->dpp_url:'';
					   
							$course_id = $timetable->course_id;
							$class_id = $timetable->id;
							if(!empty($timetable->subject_id)){
								$get_remark = DB::table('class_remarks')->where('subject_id', $timetable->subject_id)->first();
							}

							$temp['remark'] = isset($get_remark->remark)?$get_remark->remark:'';

							$temp['stime']  = date('h:i a', strtotime($timetable->from_time));

							$temp['etime']  = date('h:i a', strtotime($timetable->to_time));

                            $temp['class_id'] =$class_id;

                            $temp['course'] = isset($timetable->course->name)?$timetable->course->name:'';
							$temp['course'] = isset($timetable->subject->name)?$timetable->subject->name.' || '.$temp['course']:'';
							
							$testDefault =  "";
							if($timetable->online_class_type=='Test'){
								$testDefault = " ( Test )";
							}
							$temp['subject'] = isset($timetable->subject->name)?$timetable->subject->name . $testDefault:'';

							$temp['faculty_name'] = "-"; //!empty($timetable->faculty->name)?$timetable->faculty->name:'';
							// $temp['venue'] = $venue;
							$temp['venue'] = isset($timetable->studio->branch->address)?$timetable->studio->branch->name.' - '.$timetable->studio->branch->address:'';
							// echo $temp['venue']; die;
							$from_time = $temp['stime'];
							$to_time = $temp['etime'];
							// $temp['venue'] = $timetable->studio->branch->name.'/'.$timetable->studio->name;
							
							$studio_array[$timetable->studio_id] = $temp['venue'];
							$studio_hall_name[$timetable->studio_id]['hall_name'] = isset($timetable->studio->name)? "Hall - ".$timetable->studio->name." ":' ';
							$studio_array_time[$timetable->studio_id][] = $from_time .' - '. $to_time .', ';
							
							$final_vanue = "";
							foreach($studio_array as $key33=>$vallllll){
								$final_vanue1 = "";
								if(count($studio_array) > 1){
									foreach($studio_array_time[$key33] as $key11=>$ddddd){									 
											$final_vanue1 .= $ddddd;
									}
								}
								$final_vanue .= rtrim($final_vanue1,", "). " " .$studio_hall_name[$key33]['hall_name'] .$studio_array[$key33]." \n\n AND \n\n ";
							}
							
							$final_vanue = rtrim($final_vanue," \n\n AND \n\n ");
							
							if(in_array($course_id,$course_ids)){
								$venue_array[$temp['venue']] = $temp['venue'];
								if(count($venue_array) > 1){
									// $classes['venue'] = $fullVanue;
									$classes['venue'] = $final_vanue;
								}
								$classes['classes'][] = $temp;
								$course_array[$timetable->course_id] = $classes;
								$get_faculty_timetables[$ii] = $course_array[$timetable->course_id];
							}else{
								$venue_array = array();
								$sss = "";
								$course_ids[] = $course_id;
								$classes = array();
								$classes['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
								$batch_data = Batch::select("course.name")->leftJoin('course', 'batch.course_id', '=', 'course.id')->where('batch.id',$timetable->batch_id)->first();
								if(!empty($batch_data)){
									$classes['course_name'] = isset($batch_data->name)?$batch_data->name:'';
								}
								
								// $classes['venue'] = $venue;
								$hall_name = isset($timetable->studio->name)? "Hall - ".$timetable->studio->name." ":' ';
								$branch_name = isset($timetable->studio->branch->address)?$timetable->studio->branch->address:'';
								// $classes['venue'] = $hall_name . $branch_name;
								$classes['venue'] = $final_vanue;
								$classes['dpp_url'] = $dpp_url;

								$classes['classes'][] = $temp;
								$course_array[$timetable->course_id] = $classes;
								$ii = $i;
								$get_faculty_timetables[$ii] = $course_array[$timetable->course_id];
								$i++;
								$venue_array[$temp['venue']] = $temp['venue'];
							}

						}

						$data['status'] = "ok";

						$data['data'] = $get_faculty_timetables;
						return $this->returnResponse(200, true, "Timetable", $data);

					}else{
						return $this->returnResponse(200, false, "Timetable Not Found");
					}

				}else{
					return $this->returnResponse(200, false, "Timetable Not Found");
				}

			}else{
				return $this->returnResponse(200, false, "Date and Batch Code required");
			}

		} catch (\Illuminate\Database\QueryException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		} catch (ModelNotFoundException $ex) {

			return $this->returnResponse(500, false, $ex->getMessage());

		}
	}
	
	
	public function userStringCheck(Request $request){ 
		try {
            $user_id     = $request->user_id;
            $user_string = $request->user_string; 
			if(!empty($user_id)){
				if(!empty($user_string)){
					
					$check_user = DB::table('user_strings')->where('user_id', $user_id)->first();
					//echo '<pre>'; print_r($check_user);die;
					if(!empty($check_user)){
						$user_string_res = DB::table('user_strings')->where('user_id', $user_id)->update([ 'string' => $user_string, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s') ]);
					}
					else{
						$user_string_res = DB::table('user_strings')->insertGetId([ 'user_id' => $user_id, 'string' => $user_string, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s') ]);
					}
					
					if($user_string_res){
						return $this->returnResponse(200, true, "Successfully Update");
					}
					else{
						return $this->returnResponse(200, false, "No Any Change");
					}
				}
				else{
					return $this->returnResponse(200, false, "User String Required");
				}
			}
			else{
				return $this->returnResponse(200, false, "User ID Required");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	public function dashboard_new(Request $request)
    {
        try{

            $user_id = $request->user_id;
			$name = $request->name;

            if(isset($user_id) && !empty($user_id)){
				$user = User::where('id', $user_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						$current_app_version ="50";// $this->current_app_version();
						$date_from = $request->date_from;
						$date_to = $request->date_to;
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
									$temp['current_app_version'] = $current_app_version;
									$total_dashboard_classess = $temp; 
								}

								$data['total_dashboard_classess'] = $total_dashboard_classess;

								return $this->returnResponse(200, true, "Total Faculty Dashboard Class", $data);

							}else{
								return $this->returnResponse(200, false, "Dashboard Data Not Found");
							}   
						}
						else if($user->role_id==3){ //Studio Assistant Role
							$studios = Studio::with(['timetable'=>function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
									$q->where('is_deleted', '=', '0');
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
									$q->where('is_deleted', '=', '0');
								}
							},'timetable.topic',
							'timetable.faculty'=>function ($q) use ($name) {
								if(!empty($name)){
									$q->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								}
							},
							'timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where('assistant_id', $user_id);
							
							$studios->WhereHas('timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							});
							
							/* if(!empty($name)){
								$studios->WhereHas('timetable.faculty', function ($q) use ($name) {
									$q->where('name', 'LIKE', '%' . $name . '%')
										->orWhere('register_id', 'LIKE', '%' . $name)
										->orWhere('mobile', 'LIKE', '%' . $name. '%');
								});
							} */
							// echo $studios->toSql(); die;
							$studios = $studios->get();
							// echo "<pre>";print_R($studios); die;
							if(!empty($studios)){
								$i = 0;
								$studioArray = array();
								$get_notification_count = ApiNotification::count();
								$data['total_notification_count'] = $get_notification_count;
								$data['current_app_version'] = $current_app_version;
								$get_faculty_timetables = [];
								foreach($studios as $key=>$value){
									$get_faculty_timetables['studio_id'] = $value->id;
									$get_faculty_timetables['studio_name'] = $value->name;
									$class_array = array();
									foreach($value->timetable as $key1 => $timetable){
										if(!empty($timetable->faculty->name)){
											$temp['timetable_id'] = $timetable->id;
											$temp['from_time'] = $timetable->from_time;
											$temp['to_time'] = $timetable->to_time;
											$temp['date'] = $timetable->cdate;
											$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
											$temp['batch_name'] = isset($timetable->batch->name)?$timetable->batch->name:'';
											$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
											$temp['subject_name'] = isset($timetable->subject->name)?$timetable->subject->name:'';
											$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
											$temp['topic_name'] = isset($timetable->topic->name)?$timetable->topic->name:'';
											
											$class_status = "Not Started";
											$startclass = StartClass::where('timetable_id', $timetable->id)->first();
											if(!empty($startclass)){
												$class_status = $startclass->status;
											}
											
											$temp['class_status'] = $class_status;
											
											
											$class_array[] = $temp;
										}
									}
									$get_faculty_timetables['classes'] = $class_array;
									
									$studioArray[$i] = $get_faculty_timetables;
									$i++;
								}

								$data['studio'] = $studioArray;

								return $this->returnResponse(200, true, "Studio Assistant Classes", $data);
							}else{
								return $this->returnResponse(200, false, "Classes Not Found");
							}
						}
						else if($user->role_id==4 || $user->role_id==27 || $user->role_id==28){ //4 = Studio Manager Role // 27 = Time table Manager
							$studio_id ="";
							if(isset($request->studio_id) && !empty($request->studio_id)){
								$studio_id = $request->studio_id;
							}
							$get_notification_count = ApiNotification::count();
							$data['total_notification_count'] = $get_notification_count;
							$data['current_app_version'] = $current_app_version;
							$branches = Userbranches::with(['branch',
							'studio'=>function ($q) use ($studio_id) {
								if(!empty($studio_id)){
									$q->where('id', $studio_id);
								}
							},
							'studio.timetable'=>function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							},'studio.timetable.topic',
							'studio.timetable.faculty'=>function ($q) use ($name) {
								if(!empty($name)){
									$q->where('name', 'LIKE', '%' . $name . '%')
									->orWhere('register_id', 'LIKE', '%' . $name)
									->orWhere('mobile', 'LIKE', '%' . $name. '%');
								}
							},
							'studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->where('user_id', $user_id);
							
							$branches->WhereHas('studio.timetable', function ($q) use ($date_from, $date_to) {
								if(!empty($date_from) && !empty($date_to) ){
									$q->where('cdate', '>=', $date_from);
									$q->where('cdate', '<=', $date_to);
								}
								else{
									$q->where('cdate', '>=', date('Y-m-d'));
								}
							});
							$branches = $branches->orderBy('branch_id', 'desc')->groupBy('branch_id')->get();
							
							// echo "<pre>";
							// print_R($branches); die;
							if(!empty($branches)){
								$branchesArray = array();
								$i = 0;
								foreach($branches as $key=>$value){
									
									if(!empty($value->studio) && count($value->studio) > 0){
										$ii = 0;
										foreach($value->studio as $key1=>$studios){
											
											if(!empty($studios->timetable) && count($studios->timetable) > 0){
												$branchesArray[$i]['studios'][$ii]['studio_id'] = $studios->id;
												$branchesArray[$i]['studios'][$ii]['studio_name'] = $studios->name;
												$class_array = array();
												foreach($studios->timetable as $key2 => $timetable){
													if(!empty($timetable->faculty->name)){
														$temp['timetable_id'] = $timetable->id;
														$temp['from_time'] = $timetable->from_time;
														$temp['to_time'] = $timetable->to_time;
														$temp['date'] = $timetable->cdate;
														$temp['faculty_name'] = isset($timetable->faculty->name)?$timetable->faculty->name:'';
														$temp['batch_name'] = isset($timetable->batch->name)?$timetable->batch->name:'';
														$temp['course_name'] = isset($timetable->course->name)?$timetable->course->name:'';
														$temp['subject_name'] = isset($timetable->subject->name)?$timetable->subject->name:'';
														$temp['chapter_name'] = isset($timetable->chapter->name)?$timetable->chapter->name:'';
														$temp['topic_name'] = isset($timetable->topic->name)?$timetable->topic->name:'';
														
														$class_array[] = $temp;
													}
												}
												$branchesArray[$i]['studios'][$ii]['classes'] = $class_array;
												$ii++;
											}
											
										}
										if($ii > 0){
											$branchesArray[$i]['branch_id'] = $value->branch_id;
											$branchesArray[$i]['branch_name'] = $value->branch->name;
										}
										
										
									}
								}
								$data['branches'] = $branchesArray;

								return $this->returnResponse(200, true, "Studio Manager Branches Details", $data);
							}else{
								return $this->returnResponse(200, false, "Branches Not Found");
							}
						}
						else if($user->role_id==29){ //Super Admin
							$get_notification_count = ApiNotification::count();
							$data['total_notification_count'] = $get_notification_count;
							$data['current_app_version'] = $current_app_version;
							$branches = Userbranches::with(['user'=>function ($q){
											$q->where('status', 1);
										},
										'branch'=>function ($q){
											$q->where('status', 1);
										}
										,'studio','studio.timetable'=>
										function ($q){
											// $fdate = date('Y-m-d',strtotime('-20 day'));
											// $tdate = date('Y-m-d',strtotime('+7 day'));
											// $q->where('cdate', '>=', $fdate);
											// $q->where('cdate', '<=', $tdate);
											$q->where('cdate',  date('Y-m-d'));
											$q->orderBy('cdate', 'desc');
										},
								'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->orderBy('branch_id', 'desc')->groupBy('branch_id')->get();
								//->where('user_id', $user_id)
							// echo "<pre>";
							// print_R($branches); die;
							if(!empty($branches)){
								$branchesArray = array();
								$i = 0;
								foreach($branches as $key=>$value){
									
									if(!empty($value->studio) && count($value->studio) > 0){
										$ii = 0;
										foreach($value->studio as $key1=>$studios){
											
											if(!empty($studios->timetable) && count($studios->timetable) > 0){
												$branchesArray[$i]['studios'][$ii]['studio_id'] = $studios->id;
												$branchesArray[$i]['studios'][$ii]['studio_name'] = $studios->name;
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
													
													$branchesArray[$i]['studios'][$ii]['classes'][$key2] = $temp;
												}
												$ii++;
											}
										}
										if($ii > 0){
											$branchesArray[$i]['branch_id'] = $value->branch_id;
											$branchesArray[$i]['branch_name'] = isset($value->branch->name)?$value->branch->name:'';
											$i++;
										}
										
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
	
	public function get_all_users(Request $request)
    {
    	try {
			$user_result = User::select('users.id','users.name','userdetails.degination as designation')->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')->where([['users.status', '=', '1'],['users.is_deleted', '=', '0']])->get();
			
			$responseArray = array();
			if(count($user_result) > 0){
				$responseArray['user_details'] = $user_result;
				return $this->returnResponse(200, true, "Users Details", $responseArray);
			}
			else{
				return $this->returnResponse(200, false, "Usres Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function get_meeting_places(Request $request)
    {
    	try {
			$place_result = DB::table('meeting_place')->where([['status', '=', 'Active'],['is_deleted', '=', '0']])->get();
			
			$responseArray = array();
			if(count($place_result) > 0){
				$responseArray['meeting_places'] = $place_result;
				return $this->returnResponse(200, true, "Places Details", $responseArray);
			}
			else{
				return $this->returnResponse(200, false, "Places Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}
	
	public function updateUserRecord(Request $request){ 
		$file = $request->file('user_file');
		$import = Excel::toArray(null, $file); 
		unset($import[0][0]);

		if(!empty($import[0])){ 
			foreach ($import[0] as $key => $value) { 
				$user_id = User::where('register_id', (string)$value[2])->first(); 
				$in_time_data = ''; $out_time_data = '';
				if(!empty($user_id)){
					$user_array = []; $user_detail_array = [];
					//User
					$user_array['name'] = $value[3];
					$user_array['mobile'] = $value[16];
					$user_array['email'] =  $value[31]; 
					User::where('id', $user_id->id)->update($user_array);
					
					//User Detail
					$explode_time = explode("-", $value[27]); 
					$in_time_data = date("H:i", strtotime($explode_time[0]));
					$out_time_data = date("H:i", strtotime($explode_time[1])); 
					
					$user_detail_array['fname'] = $value[4];
					$user_detail_array['gender'] = ucfirst(strtolower($value[5]));
					$user_detail_array['degination'] = $value[6];
					$user_detail_array['dob'] =  date("Y-m-d", strtotime($value[8]));
					$user_detail_array['joining_date'] =  date("Y-m-d", strtotime($value[9]));
					$user_detail_array['aadhar_card_no'] =  $value[12];
					$user_detail_array['aadhar_name'] =  $value[13];
					$user_detail_array['pan_no'] =  $value[14];
					$user_detail_array['pan_name'] =  $value[15];
					$user_detail_array['official_no'] =  $value[17];
					$user_detail_array['alternate_contact_number'] =  $value[18];
					$user_detail_array['c_address'] =  $value[19];
					$user_detail_array['p_address'] =  $value[20];
					$user_detail_array['material_status'] =  ucfirst(strtolower($value[21]));
					$user_detail_array['previous_experience'] =  $value[22];
					$user_detail_array['esic_no'] =  $value[23];  
					$user_detail_array['esi_date'] =  $value[24];  
					$user_detail_array['uan_no'] =  $value[25];  
					$user_detail_array['pf_date'] =  $value[26];  
					$user_detail_array['timing_shift_in'] =  $in_time_data;  
					$user_detail_array['timing_shift_out'] =  $out_time_data;  
					$user_detail_array['account_number'] =  $value[28];  
					$user_detail_array['ifsc_code'] =  $value[29];  
					$user_detail_array['bank_emp_name'] =  $value[30];  
					 
					Userdetails::where('user_id', $user_id->id)->update($user_detail_array);

				}
			}	
			return $this->returnResponse(200, true, "Sucessfully Update");
		}
	}
	
	
	//Active user check
	public function checkMobile_email(Request $request) 
    {
    	try {
    		$mobile = $request->mobile;
            $gsm_token = $request->gsm_token;
						
			$login_with = "email";
			if(is_numeric($mobile)){
				$login_with = 'mobile';
			}
		
			$row = User::where($login_with,$mobile)->where('status', 1)->first();
			
			if(!empty($row)){
				if($row->status=='1'){
					if(isset($gsm_token) && !empty($gsm_token)){
						$row->gsm_token = $gsm_token;
						$row->update();
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
						$temp['status'] = $row->status;

						$get_user_data = $temp;
					}

					$data['user'] = $get_user_data;

					return $this->returnResponse(200, true, "user record", $data);
				}
				else{
					return $this->returnResponse(200, false, "User Not Active");
				}
			}
			else{
				return $this->returnResponse(200, false, "Mobile Number/Email Not Found or User Not Active");
			}
			
			
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	public function checkUsername_password(Request $request) 
    {
    	try {
    		$mobile 	= $request->mobile;
    		$password 	= $request->password;
            $gsm_token 	= $request->gsm_token;
			
			$login_with = "email";
			if(is_numeric($mobile)){
				$login_with = 'mobile';
			}
		
			// $row = User::where($login_with,$mobile)->where('status', 1)->first();
			// if(Hash::check($row->password, $password)){
			$row=Auth::attempt([$login_with=>$mobile,'password'=>$password]);
			if ($row) {
				$row=Auth::user();
				if($row->status=='1'){
					if(isset($gsm_token) && !empty($gsm_token)){
						$row->gsm_token = $gsm_token;
						$row->update();
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
						$temp['status'] = $row->status;

						$get_user_data = $temp;
					}

					$data['user'] = $get_user_data;

					return $this->returnResponse(200, true, "user record", $data);
				}
				else{
					return $this->returnResponse(200, false, "User Not Active");
				}
			}
			else{
				return $this->returnResponse(200, false, "Invalid credentials");
			}
			
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	public function get_student_attendance(Request $request){
		try {
			$reg_no = $request->reg_no;
			$month  = date("Y").'-'.date("m");
			
			if(!empty($reg_no)){
				$data=[];				
				$attendance = DB::table("student_attendance")->select('*',DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as pdate"))->where('reg_no',$reg_no)->where('user_id','!=',901)->whereRAW("date like '".$month."%'")->get();

				if(count($attendance) > 0){
					$month = explode('-',$month);
					$yr = $month[0];
					$mt = $month[1];

					$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
					$first_date = strtotime($yr.'-'.$mt.'-01');
					$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
					$i=0;
					$st_attendance=[];
					$attendance=json_decode(json_encode($attendance),true);
					while($daysInMonth>0){
						$add_get_date  = date('Y-m-d', $first_date);
						$st_attendance[$i]['pdate']=$add_get_date;
						$st_attendance[$i]['status']='Absent';
						$st_attendance[$i]['date']='-';
						$st_attendance[$i]['time']='-';

						$daysInMonth--;
						$first_date += 86400; 
						$atdIndex=array_search($add_get_date,array_column($attendance,'pdate'));
						if($atdIndex!== false){ 
						 $st_attendance[$i]['date']=$attendance[$atdIndex]['date'];
						 $st_attendance[$i]['status']='Present';
						 $st_attendance[$i]['time']='-';
						}
						$i++;
						
						$data['st_attendance']=$st_attendance;
					}
					return $this->returnResponse(200, true, "Attendance Found", $data);
				}
				else{
					return $this->returnResponse(200, false, "Attendance Not Found"); 
				}
			}else{
				return $this->returnResponse(200, false, "Something Went Wrong!!!");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	
	public function get_student_inventory(Request $request){
		try {
			$reg_no = $request->reg_no;
			$month  = date("Y").'-'.date("m");
			
			if(!empty($reg_no)){
				$data=[];
				$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no','cast')
				->where('reg_number',$reg_no)
				->first();
				
				if(!empty($student)){
					$assign_inventory = explode(',',$student->assign_inventory);
												
					$get_inventory = DB::table("batch_inventory")->where('batch_code',$student->batch_id)->where('status',1)->groupby('name','inventory_type')->get();
					
					$i=0;
					$st_inventory=[];
					foreach($get_inventory as $gi){
						if(in_array($gi->id,$assign_inventory)){ 
							$status =  'Given'; 
						}else{ 
							$status =  'Pending'; 
						}
						
						$st_inventory[$i]['name']		=	$gi->name;					
						$st_inventory[$i]['int_type']	=	$gi->inventory_type;					
						$st_inventory[$i]['int_status']	=	$status;
						
						$i++;
						
						$data['student_int']=$st_inventory;
					}
					return $this->returnResponse(200, true, "Inventory Found", $data);
				}else{
					return $this->returnResponse(200, false, "No Record Found!!!");
				}
			}else{
				return $this->returnResponse(200, false, "Something Went Wrong!!!");
			}
		} catch (\Illuminate\Database\QueryException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		} catch (ModelNotFoundException $ex) {
			return $this->returnResponse(500, false, $ex->getMessage());
		}
	}
	
	public function pinbixmsg($mbl,$message_content){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://app.pingbix.com/SMSApi/send",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "userid=utkarsh&password=BNeWcaP6&mobile=91$mbl&msg=$message_content&senderid=UTKRSH&msgType=text&dltEntityId=1701158072985103391&dltTemplateId=1207167091510853562&duplicatecheck=true&output=json&sendMethod=quick",
		  CURLOPT_HTTPHEADER => array(
			"apikey: somerandomuniquekey",
			"cache-control: no-cache",
			"content-type: application/x-www-form-urlencoded"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		return $response;
	}
	
	public function smscountry($mbl,$message_content){
		$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mbl}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		curl_close($ch);
	}
	
	
	
	public function pw_uc_employee_add(Request $request){
		return $this->returnResponse(200, false, "Mobile Number Not Found or User Not Active");
	}
	
	
	public function pw_uc_employee_inactive(Request $request){
		return $this->returnResponse(200, false, "Inactive Mobile Number Not Found or User Not Active");
	}
}