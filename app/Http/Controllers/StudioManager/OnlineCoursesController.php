<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Auth;
use App\User;
use DB;

class OnlineCoursesController extends Controller
{
    protected $courseArr;
    public function __construct(){
     $this->courseArr=array(5453,5441,1089,1207,1237,1069,1556,1868,5785,1246,1096,1215,1926,5525,6245,5603,1926,1078,1027);
    }

    public function index()
    {
		if(in_array(Auth::user()->id, $this->courseArr)){
			$department_id = Auth::user()->department_type;
			$employees = User::where('status','1')->where('is_deleted','0')->where('department_type',$department_id)->get();//->pluck('name', 'id');
			//$employees->prepend('Select Employee','');
			
			$online_course_result = DB::table('online_courses')->select('online_courses.*','u.name as emp_name','addby.name as addby_name')->leftJoin('users as u', 'u.id', '=', 'online_courses.emp_id')->leftJoin('users as addby', 'addby.id', '=', 'online_courses.login_id')->where('online_courses.login_id', Auth::user()->id)->where('online_courses.id_deleted','0')->orderBy("online_courses.id", "desc")->get();

			$api_name="getBatchList";
			$batchcourses=$this->online_app_api($api_name);

			//dd($online_course_result);
			return view('studiomanager.onlinecourses.index', compact('employees','online_course_result','batchcourses'));
		}else{
			return back()->with('error', "Access Denied");
		}
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
		if(in_array(Auth::user()->id, $this->courseArr)){
			
			$login_id=Auth::user()->register_id;
			$login_name=Auth::user()->name;

			$validatedData = $request->validate([
				'emp_id'    => 'required',
				'course_type'    => 'required',
				//'course_id' => 'required|numeric',
			]);
			
			$selectEmpDetails = User::where('id',$request->emp_id)->first();
			
			if(!empty($selectEmpDetails)){

				$contact_no = $selectEmpDetails->mobile;
				$foremp=$selectEmpDetails->register_id;
				$remarks    =$login_name."(".$login_id.") to:".$foremp;
                $remarks=urlencode($remarks);
				$amount     = 0; 

                $course_id=array();
                if($request->course_type=='Online Course'){
                    $course_id=explode(",",$request->course_id);
                }else if($request->course_type=='Batch Course'){
                    $course_id  = $request->batch_id;
                }else{
					return redirect()->route('studiomanager.onlinecourses.index')->with('error', 'Something is wrong');
				}


				$add_book_status="";
				for($i=0;$i<count($course_id);$i++){
				    $c_id =$course_id[$i];
				    if(!empty($c_id)){
					  	if($request->course_type=='Online Course'){
					        $paymentId  = $i.time().'-employee-'.$request->emp_id;
					        $api_name="addBooktoUser?userName={$contact_no}&courseId={$c_id}&siteId=1&paymentId=$paymentId&remarks=$remarks&amount=$amount";
		                }else if($request->course_type=='Batch Course'){
		                    $api_name="addUserToBatch?username={$contact_no}&batchId={$c_id}";
		                }

		                $resp=$this->online_app_api($api_name);
					    $result = json_decode($resp);
					    if(!empty($result) && ($result->status=='Ok' || !empty($result->added))){
					    	$onlineCourseArr=array(
												'emp_id' =>$request->emp_id,
												'mobile_no' => $contact_no,
												'course_id' => $c_id,
												'course_status' =>"Ok",
												'full_response' => json_encode($result),
												'login_id' => Auth::user()->id,
												'created_at' => date("Y-m-d H:i:s")
											);
						    $res = DB::table('online_courses')->insertGetId($onlineCourseArr);
					    }else{
		                 $add_book_status.=$c_id." -Not Added; ";
					    }
					}
				}

				if(empty($add_book_status)){
					return redirect()->route('studiomanager.onlinecourses.index')->with('success', 'Successfully added');
				}else{
					return redirect()->route('studiomanager.onlinecourses.index')->with('error',$add_book_status);
				}   
				
			}else{
				return redirect()->route('studiomanager.onlinecourses.index')->with('error', 'User Not Found');
			}					
		}else{
			return back()->with('error', "Access Denied");
		}
    }
	
	public function get_course_list_by_mobile(Request $request){
	    if(in_array(Auth::user()->id, $this->courseArr)){
			if(!empty($request->mobile_no)){
				$contact_no = $request->mobile_no;
				
				$api_name="getUserCourseDetails?username=$contact_no";
				$resp=$this->online_app_api($api_name); 
					
				if(!empty($resp)){
					$res  = '';
					$res1 = '';
					$s_no=0;
					$u_id = $request->unique_id;
					$all_courses = json_decode($resp,true); 
					if(count($all_courses['freeCourses'])>0){
						foreach($all_courses['freeCourses'] as $key=>$all_courses_val){
							$s_no++;
							if($key==0){
							$res.='<tr class="subtr'.$u_id.'">
								<th></th>
								<th colspan="1">Course ID</th>
								<th colspan="1">Course Name</th>
								<th colspan="2">Info</th>
								<th colspan="2">Action</th>
								<th>
								<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light remove_all" data-id="'.$u_id.'" title="Close" style="padding: 0.5rem 0.5rem;"><i class="feather icon-x"></i></i></a>
								</th>
							</tr>';
							}		
							$res.='<tr class="subtr'.$u_id.' offsubstrrm'.$u_id.$key.'">
								<td>'.$s_no.'</td>
								<td colspan="1">'.$all_courses_val['id'].'</td>
								<td colspan="1">'.$all_courses_val['main_coursename'].'</td>
								<td colspan="2">'.$all_courses_val['remarks'].' - expiry date: '.$all_courses_val['expiry_date'].'</td>
								<td colspan="2">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light destroy_course" data-course-id="'.$all_courses_val['id'].'" data-contact="'.$contact_no.'" data-id="'.$u_id.$key.'" title="Delete" data-course-type="free" style="padding: 0.5rem 0.5rem;"><i class="feather icon-trash"></i></a>
								</td>
								<td></td>
							</tr>';	
						}

						foreach($all_courses['paidCourses'] as $key=>$all_courses_val){	
							$s_no++;
						  	$res .= '<tr class="subtr'.$u_id.' paidsubstrrm'.$u_id.$key.'">
								<td>'.$s_no.'</td>
								<td colspan="1">'.$all_courses_val['id'].'</td>
								<td colspan="1">'.$all_courses_val['main_coursename'].'</td>
								<td colspan="2">'.$all_courses_val['payment_id'].' - '.$all_courses_val['payment_amount'].' <br> -'.$all_courses_val['remarks'].'- expiry date: '.$all_courses_val['expiry_date'].'</td>
								<td colspan="2">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light destroy_course" data-course-id="'.$all_courses_val['id'].'" data-contact="'.$contact_no.'" data-id="'.$u_id.$key.'"  data-course-type="paid" title="Delete" style="padding: 0.5rem 0.5rem;"><i class="feather icon-trash"></i></a>
								</td>
								<td></td>
							</tr>';
						}

						foreach($all_courses['batchcourses'] as $key=>$all_courses_val){
							$s_no++;		
							$res.='<tr class="subtr'.$u_id.' batchsubstrrm'.$u_id.$key.'">
								<td>'.$s_no.'</td>
								<td colspan="1">'.$all_courses_val['batch_id'].'</td>
								<td colspan="1">'.$all_courses_val['batch_name'].'</td>
								<td colspan="2">Batch Course</td>
								<td colspan="2">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light destroy_course" data-course-id="'.$all_courses_val['id'].'" data-contact="'.$contact_no.'" data-id="'.$u_id.$key.'"  data-course-type="batch" title="Delete" style="padding: 0.5rem 0.5rem;"><i class="feather icon-trash"></i></a>
								</td>
								<td></td>
							</tr>';
						}
						echo json_encode(['status' => true, 'data' => $res]);	
					}else{
						$res1 .= '<tr class="subtr'.$u_id.'">
										<th></th>
										<th colspan="2">Course ID</th>
										<th colspan="2">Course Name</th>
										<th colspan="2">Action</th>
									</tr>
									<tr class="subtr'.$u_id.' text-center">
										<td colspan="7">No Record Found</td>
									</tr>';
						echo json_encode(['status' => true, 'data' => $res1]);
					}
					
				}
				else{
					echo json_encode(['status' => false, 'message' => 'Record Not Found']);
				}
			}
			else{
				echo json_encode(['status' => false, 'message' => 'Mobile No Required']);
			}	
		}
		else{
			echo json_encode(['status' => false, 'message' => 'Access Denied']);
		}	
	}
	
	public function delete_course(Request $request){
	    if(in_array(Auth::user()->id,$this->courseArr)){
			
			if(!empty($request->course_type) && $request->course_type == 'Error'){
				$check_online_course = DB::table('online_courses')->where('course_id', $request->course_id)->where('mobile_no', $request->contact)->where('login_id', Auth::user()->id)->first();  
				if(!empty($check_online_course)){
					DB::table('online_courses')->where('id', $check_online_course->id)->update(['id_deleted' => '1']);
				}
				
				echo json_encode(['status' => true, 'message' => 'deleted']); die;	
			}
			if($request->course_id != ''){
				if(!empty($request->contact)){
					$contact_no = $request->contact;
					$course_id = $request->course_id;

					$api_name="deleteMainandPackageCourseforUser?userName=$contact_no&mainCourseId=$course_id";
					$resp=$this->online_app_api($api_name); 
					$result = json_decode($resp);

					if($result->status == 'deleted'){
						$course_status = 'deleted';
					}
					else{
						$course_status = 'error';
					}
					$check_online_course = DB::table('online_courses')->where('course_id', $request->course_id)->where('mobile_no', $request->contact)->where('login_id', Auth::user()->id)->where('id_deleted', '0')->first();
					if(!empty($check_online_course)){
						DB::table('online_courses')->where('id', $check_online_course->id)->update(['id_deleted' => '1']);
					}
					
					echo json_encode(['status' => true, 'message' => $course_status]);							
				}
				else{
					echo json_encode(['status' => false, 'message' => 'Contact No. Required']);
				}
			}
			else{
				echo json_encode(['status' => false, 'message' => 'Course Id Required']);
			}
		}
		else{
			echo json_encode(['status' => false, 'message' => 'Access Denied']);
		}
	}

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

  
    public function destroy($id)
    {
        //
    }


    function online_app_api($api_name){
    	$url = "https://support.utkarshapp.com/index.php/".$api_name;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   'Accept-Charset: UTF-8',
		   'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
		   'Accept: application/json',
		   'Content-Type: application/json'
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$data = '';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		return $resp; 
    }
	
}
