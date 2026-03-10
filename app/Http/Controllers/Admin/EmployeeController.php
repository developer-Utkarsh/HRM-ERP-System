<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Input;
use App\FacultyRelation;
use App\Userdetails;
use App\Subject;
use DB;
use App\Userbranches;
use App\Studio;
use App\Users_pending;
use Auth;
use Excel;
use App\Exports\EmployeeExport;
use App\AttendanceNew;
use App\Attendance;
use App\Exports\LateEmployeeExport;
use App\Exports\EmployeeProbationExport;
use App\Exports\EmployeeBirthdayExport;
use App\Exports\EmployeeWorkAnniversaryExport;
use App\Department;
use App\SubDepartment;
use App\JobRole;
use App\NewTask;
use App\Branch;  
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeAccessAlertMail;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {		
		// echo Hash::make('PW763041');
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		session_start();

        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		if(!empty($_SESSION["redirect_url"]) && isset($_GET['update'])){
			return redirect("admin/employees".$_SESSION['redirect_url'])->with($_GET['update'],$_GET[$_GET['update']]);
		}
		unset($_SESSION["redirect_url"]);
		if(isset($_GET['search'])){
			$paremeters="?";
			$all_request 	= 	is_array($_GET);
				
			if(is_array($all_request) || is_object($all_request)){
				foreach($all_request as $key=>$val){
					$paremeters .= "&$key=$val";
				}
				$_SESSION["redirect_url"] = $paremeters;
			}	
		}
		
		
		$search = Input::get('search');
        $branch_id = Input::get('branch_id');
        $role_id = Input::get('role_id');
		$status = Input::get('status');
		$department_type = Input::get('department_type');
		$year_wise_month     =  Input::get('year_wise_month');
		$branch_location     =  Input::get('branch_location'); 
		$darwin_code     =  Input::get('darwin_code'); 
		$month_year_to_days = array();
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		}

		
        // $employees = User::with(['user_details.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');
        $employees = User::with(['user_details','user_branches.branch','role'])
		->where('role_id','!=','1')->where('is_deleted', '0');
		
		if(Auth::user()->role_id == 29){
			
		}
		elseif(!empty(Auth::user()->sub_department_type)){
			$employees->where('sub_department_type', Auth::user()->sub_department_type);
		}
		
		$employees->orderBy('name');
		//$employees->where('register_id','!=',NUll);
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search )
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($month_year_to_days)){
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
			
			if($status=='Active'){
				$employees->WhereHas('user_details', function ($q) use ($yr,$mt) {	
					$q->whereRaw("(MONTH(joining_date) = $mt and YEAR(joining_date) = $yr)");							
				});
			}
			if($status=='Inactive'){
				$employees->whereRaw("(MONTH(reason_date) = $mt and YEAR(reason_date) = $yr)");
			}
			
		}
        if(!empty($branch_id)) {
            /*$employees->WhereHas('user_details', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });*/
			
			/*
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
			*/
			
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk				
				$q->whereIn('branch_id', $branch_id);							
			});					
        }

        if(!empty($role_id)){
            $employees->where('role_id',$role_id);
        }
		
		if(!empty($darwin_code)){
            $employees->where('darwin_code',$darwin_code);
        }
		
		if(!empty($branch_location)){
			$employees->WhereHas('user_branches.branch', function ($q) use ($branch_location) { // orWhereHas dk				
				$q->where('branch_location', $branch_location);							
			});	
		}
		
		if(!empty($status) && ($logged_role_id == 29 || $logged_role_id == 24)){
            if($status == 'Inactive'){
                $employees->where('status', '=', '0');
            }else{
                $employees->where('status', '=', '1');
            }
        }
		else{
			if($logged_role_id != 29 && $logged_role_id != 24){
				$employees->where('status', '=', '1');
			}
		}
		if(!empty($department_type)){
			$employees->where('department_type','=',$department_type);
		}

		if($logged_role_id == 29){
			$users = NewTask::getEmployeeByLogID($logged_id,'all-employee');
		}
		else if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else if($logged_role_id == 21){
			$users = NewTask::getEmployeeByLogID($logged_id,'location_wise');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		// echo '<pre>'; print_r($users);die;
		$employeeArray = array();
		$usr=implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		
		$employeeArray   = explode(',',$usr);		
		// echo '<pre>'; print_r(count($employeeArray));die;
		$employees->whereIn('id', $employeeArray);


		
		$employees = $employees->paginate(10);
		// $employees = $employees->get();
		// echo '<pre>'; print_r(count($employees));die;
        
		$allDepartmentTypes  = $this->allDepartmentTypes();
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        
		// echo '<pre>'; print_r($employees); die;
        return view('admin.employee.index', compact('employees','pageNumber','params','allDepartmentTypes')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$subjects = Subject::where('status', '=', '1')->orderBy('id', 'desc')->get();
        return view("admin.employee.add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	
	//dk
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'role_id' => 'required',
			//'register_id' => 'required|max:150|unique:users',             		
            'department_type' => 'required', 			
            'name' => 'required|max:100', //|regex:/^[\pL\s\-]+$/u			
            'email' => 'max:150|unique:users,email,NULL,id,status,1',
            'contact_number' => 'required|numeric|digits:10|unique:users,mobile,NULL,id,status,'.'1',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5120',     // 5MB Validation  //1024 = 1MB           
            'resume' => 'mimes:pdf,doc,docx,jpeg,png,jpg|max:5120', // 5MB Validation  //1024 = 1MB           
            'branch_id' => 'required',            
            'alternate_contact_number' => 'required|numeric|digits:10',            
            'account_number' => 'required|numeric|digits_between:0,20',            
            'bank_name' => 'required|max:100',            
            'ifsc_code' => 'required|max:50',            
            'bank_branch' => 'required|max:150',            
            'net_salary' => 'required|numeric|digits_between:0,10',            
            'tds' => 'required|numeric|digits_between:0,10',			
			'gender' => 'required',   
			'c_address' => 'required|max:1000',   
			'p_address' => 'required|max:1000',
			'joining_date' => 'required', 	
			// 'aadhar_card_no' => 'min:12|max:12',	
			'total_time' => 'required|numeric',

			//Chetan
			'supervisor_id'	=> 'required', 	
			'nickname'	=> 'required', 
			'nominee_name'	=> 'required', 	
			'dob'	=> 'required', 
			'fname' => 'required|max:100', //|regex:/^[\pL\s\-]+$/u
			'mname' => 'required|max:100', //|regex:/^[\pL\s\-]+$/u
			'material_status'	=> 'required', 
			'employee_type'	=> 'required', 
			'degination'	=> 'required', 
			'probation_from'	=> 'required', 
        ],
		[
			'image.max' => 'The image may not be greater than 5 MB.',
			'resume.max' => 'The resume may not be greater than 5 MB.',
			'fname.regex' => 'Father name is invalid.',
			'mname.regex' => 'Mother name is invalid.',
			'c_address.required' => 'Current address is required.',
			'p_address.required' => 'Permanent address is required.',
		]
		);
		
        //print_r($request->all()); die;

        $inputs = $request->only('role_id','email','mobile','image','password','supervisor_id','department_type','sub_department_type','total_time','asset_requirement');
		if(!empty($request->supervisor_id)){
			$inputs['supervisor_id'] = json_encode($request->supervisor_id);
		}
		
		$inputs['name'] 	= strtoupper($request->name);
		$inputs['nominee_name'] = strtoupper($request->nominee_name);
		$inputs['nickname'] 	= strtoupper($request->nickname);
		
		$inputs['is_extra_working_salary'] 	= $request->extraPay;
		
		if(!empty($request->darwin_code)){
			$inputs['darwin_code'] 	= $request->darwin_code;
		}
				
		if(!empty($request->agreement)){
			$inputs['agreement'] 	= $request->agreement;
		}
		
		if(!empty($request->committed_hours)){
			$inputs['committed_hours'] 	= $request->committed_hours;
		}
		
		if($request->extraPay == '0'){
			if($request->probation == 'Yes'){
				$inputs['comp_off_start_date']  = $request->probation_from;
			}
			else{
				$inputs['comp_off_start_date']  = $request->joining_date;
			}
		}
		
		$inputs['mobile'] 	= $request->contact_number;
        $inputs['password'] = Hash::make('123456');
		$inputs['course_category'] 	= !empty($request->course_category) ? implode(",",$request->course_category) : '';
		// Remove branch_id dk
        $userDetails = $request->only('user_id','dob','alternate_contact_number','alternate_email','gender','material_status','employee_type','degination','blood_group','joining_date','probation','probation_from','resume','account_number','net_salary','tds','pf_amount','pf_date','is_pf','esi_amount','esi_date','is_esi','aadhar_card_no','aadhar_name','pan_no','pan_name','official_no','previous_experience','esic_no','uan_no','timing_shift_in','timing_shift_out','bank_emp_name','emp_file_no','pl','cl','sl','anniversary_date');
	$userDetails['fname'] 		=	strtoupper($request->fname);
		$userDetails['mname'] 		=	strtoupper($request->mname);
		$userDetails['p_address'] 	=	strtoupper($request->p_address);
		$userDetails['c_address'] 	=	strtoupper($request->c_address);
		$userDetails['bank_name'] 	=	strtoupper($request->bank_name);
		$userDetails['ifsc_code'] 	=	strtoupper($request->ifsc_code);
		$userDetails['bank_branch'] =	strtoupper($request->bank_branch);
		$userDetails['probation_to'] =	$request->joining_date;

	
		if (is_array($request->faculty) && !empty($request->faculty)) {
            $faculty = $request->faculty;
			$error = false;
            foreach ($faculty['from_time'] as $key => $value) {
                if(!empty($value)){
					if(!empty($faculty['to_time'][$key])){
						if(strtotime($value) > strtotime($faculty['to_time'][$key])){
							$error = true;
						}
					}
					else{
						$error = true;
					}
                }
            }
        }
		if($error){
			return redirect()->back()->with('error', 'End time should not be less than start time.');
		}
		
        if (Input::hasfile('image')){
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        }

        if(count(array_unique($request->faculty['from_time'])) < count($request->faculty['to_time'])){
            return redirect()->back()->with('error', 'Faculty Time Hours Duplicates.');
        }        
        
        $user = User::create($inputs);
		$inputs['id'] = $user->id;
		$this->maintain_history(Auth::user()->id, 'users', $user->id, 'add_employee', json_encode($inputs));
		
		$rg_id = 0;
		$max_reg_id = DB::select("SELECT MAX( CAST(register_id AS DECIMAL)) as max_reg_id FROM users");
		if(!empty($max_reg_id[0]->max_reg_id)){
			$rg_id = $max_reg_id[0]->max_reg_id;
		}
		
		User::where('id', $user['id'])->update(['register_id' => $rg_id+1]);
		
        $faculty = $request->faculty;      
        if(isset($faculty) && is_array($faculty)){

            foreach($faculty['from_time'] as $key => $value){
                if(!empty($value)){                          
                    $data = array(                  
                        'from_time'=>$value,
                        'to_time'=>$faculty['to_time'][$key]                   
                    );                    
                    $faculty_relations_id = $user->faculty_relations()->create($data);
					$data['user_id'] = $faculty_relations_id->user_id;
					$this->maintain_history(Auth::user()->id, 'faculty_relations', $faculty_relations_id->id, 'create_faculty_relations', json_encode($data));
                }
            }
        }
		
		$branch_id = $request->branch_id;      
        if(isset($branch_id) && is_array($branch_id)){
            foreach($branch_id as $key => $value){
                if(!empty($value)){                          
                    $data = array(                  
                        'branch_id'=>$value,               
                    );                    
					$user_branches_id = $user->user_branches()->create($data);
					$data['user_id'] = $user_branches_id->user_id;
					$this->maintain_history(Auth::user()->id, 'userbranches', $user_branches_id->id, 'create_userbranches', json_encode($data));
                }
            }
        }
		
		if($request->role_id==2){
			if(isset($request->subject_id) && is_array($request->subject_id)){
				$subject_ids = $request->subject_id;
				foreach($subject_ids as $key => $value){
					if(!empty($value)){                          
						$faculty_subjects = array(                  
							'user_id'=>$user->id,
							'subject_id'=>$value                   
						);                    
						$faculty_subjects_id = DB::table('faculty_subjects')->insertGetId($faculty_subjects);
						$faculty_subjects['user_id'] = $user->id;
						$this->maintain_history(Auth::user()->id, 'faculty_subjects', $faculty_subjects_id, 'create_faculty_subjects', json_encode($faculty_subjects));
					}
				}
			}
		}

        if (Input::hasfile('resume')){
            $userDetails['resume'] = $this->uploadResume(Input::file('resume'));
        }              

		$userdetails_id = $user->user_details()->create($userDetails);
		$userDetails['user_id'] = $userdetails_id->user_id;
		$this->maintain_history(Auth::user()->id, 'userdetails', $userdetails_id->id, 'create_user_details', json_encode($userDetails));
        // $last_id = $user->id;
        // $user->register_id = '#EMP000' . $last_id;

        if ($user->save()) {
			
			$check_user_id = $user->id;
			
			$probation_from = $request->probation_from;
			$check_month = 0;
			$check_year  = 0;
			if(!empty($probation_from)){
				$check_month = date('n',strtotime($probation_from));
				$check_year = date('Y',strtotime($probation_from));
				$check_date = date('d',strtotime($probation_from));
				if($check_date > 15){
					$check_month = $check_month + 1;
				}
			}
				
			/* $_details = DB::table('leave_records')->where('user_id',$check_user_id)->where('session',$check_year)->first();
			if(empty($_details)){
				if($check_month > 0){
					$month_leave = DB::table('leave_month')->where('month',$check_month)->first();
					if(!empty($month_leave)){
						DB::table('leave_records')->insertGetId([ 
							'user_id' => $check_user_id,
							'session' => $check_year,
							'pl' => $month_leave->pl,
							'cl' => $month_leave->cl,
							'sl' => $month_leave->sl
						]);
					}
				}
			} */
			
            return redirect()->route('admin.employees.index')->with('success', 'Employee Added Successfully');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = User::with('user_details','role','faculty_relations','user_branches.branch')->find($id);

        return view('admin.employee.view', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	

    public function edit($id)
    {
        $employee = User::with('user_details','faculty_relations','user_branches')->find($id);
		
		$subject_ids = array();
		$faculty_subjects = DB::table('faculty_subjects')->where(['user_id'=>$id])->get();
		if(!empty($faculty_subjects)){
			foreach($faculty_subjects as $val){
				$subject_ids[] = $val->subject_id;
			}
		}
		
		
		$all_courses	=	$this->getMainCategory();
		$all_courses  =$all_courses->data;
		foreach($all_courses as $val){
			 $category_name[] =	$val->main_category;
		}
		
		$category_name=array_unique($category_name,SORT_STRING);
		$category_name=array_values($category_name);
		
		
        return view('admin.employee.edit', compact('employee','subject_ids','category_name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	//dk
    public function update(Request $request, $id)
    {
		
		
		
		$logged_id    = Auth::user()->id;
		$max_tds = 0;
		if(!empty($request->net_salary) && !empty($request->tds)){
			$max_tds = ($request->net_salary/100)*10;
		}
        $validatedData = $request->validate([
			'role_id' => 'required', 
            'department_type' => 'required',			
            'name' => 'required|max:100',
            'email' => 'max:150|unique:users,email,'.$id,
            'contact_number' => 'required|numeric|digits:10|unique:users,mobile,'.$id,           
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5120',      // 5MB Validation  //1024 = 1MB          
            'resume' => 'mimes:pdf,doc,docx,jpeg,png,jpg|max:5120', // 5MB Validation  //1024 = 1MB           
            'branch_id' => 'required',             
            'alternate_contact_number' => 'nullable|numeric|digits:10',            
            'account_number' => 'nullable|numeric|digits_between:0,20',            
            'bank_name' => 'max:100',            
            'ifsc_code' => 'max:50',            
            'bank_branch' => 'max:150',            
            'net_salary' => 'nullable|numeric|digits_between:0,10',            
            'tds' => "nullable|numeric|digits_between:0,10|max:$max_tds", 
            'fname' => 'nullable|max:100', 
			'mname' => 'nullable|max:100', 
			'gender' => 'required',   
			'c_address' => 'required|max:1000',   
			'p_address' => 'required|max:1000',
			// 'aadhar_card_no' => 'min:12|max:12',
			'total_time' => 'required|numeric',	
			'joining_date' => 'required',
			'probation_from'	=> 'required', 
        ],
		[
			'image.max' => 'The image may not be greater than 5 MB.',
			'resume.max' => 'The resume may not be greater than 5 MB.',
			'fname.regex' => 'Father name is invalid.',
			'mname.regex' => 'Mother name is invalid.',
			'c_address.required' => 'Current address is required.',
			'p_address.required' => 'Permanent address is required.',
			'tds.max' => 'TDS can max 10% of Net Salary',
		]
		);
		
		if (is_array($request->faculty) && !empty($request->faculty)) {
            $faculty = $request->faculty;
			$error = false;
            foreach ($faculty['from_time'] as $key => $value) {
                if(!empty($value)){
					if(!empty($faculty['to_time'][$key])){
						if(strtotime($value) > strtotime($faculty['to_time'][$key])){
							$error = true;
						}
					}
					else{
						$error = true;
					}
                }
            }
        }
		if($error){
			return redirect()->back()->with('error', 'End time should not be less than start time.');
		}
		
		if(count(array_unique($request->faculty['from_time'])) < count($request->faculty['to_time'])){
            return redirect()->back()->with('error', 'Faculty Time Hours Duplicates.');
        }
		
        $user = User::with('user_details')->where('id', $id)->first();

        $inputs = $request->only('role_id','email','mobile','image','supervisor_id','department_type','sub_department_type','total_time','asset_requirement');
	
	$inputs['name'] 	= strtoupper($request->name);
	$inputs['nominee_name'] = strtoupper($request->nominee_name);
	$inputs['nickname'] 	= strtoupper($request->nickname);
	$inputs['reason'] 	= strtoupper($request->reason);
		
	//$inputs['is_extra_working_salary'] 	= $request->extraPay;
	
	if(!empty($request->darwin_code)){
		$inputs['darwin_code'] 	= $request->darwin_code;
	}
	
	if(!empty($request->agreement)){
		$inputs['agreement'] 	= $request->agreement;
	}
	
	
	if(!empty($request->committed_hours)){
		$inputs['committed_hours'] 	= $request->committed_hours;
	}
		
		
	$inputs['mobile'] 	= $request->contact_number;
        $inputs['edit_id'] 	= $logged_id;
		$inputs['reason_date'] 	= $request->reason_date;
		$inputs['course_category'] 	= !empty($request->course_category) ? implode(",",$request->course_category) : '';
		$inputs['course_category'] 	= !empty($request->erp_main_category) ? implode(",",$request->erp_main_category) : '';
		
		
		
		if(!empty($request->password)){
			$inputs['password'] 	= Hash::make($request->password);
		}
		
		
		if(!empty($request->supervisor_id)){
			$inputs['supervisor_id'] = json_encode($request->supervisor_id);
		}
		else{
			$inputs['supervisor_id'] = NULL;
		}

        if (Input::hasfile('image')){
            $this->RemoveProfile($user->image);
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        }

        $userDetails = $request->only('user_id','dob','alternate_contact_number','alternate_email','gender','material_status','employee_type','degination','blood_group','joining_date','probation','probation_from','resume','account_number','net_salary','tds','pf_amount','pf_date','is_pf','esi_amount','esi_date','is_esi','aadhar_card_no','aadhar_name','pan_no','pan_name','official_no','previous_experience','esic_no','uan_no','timing_shift_in','timing_shift_out','bank_emp_name','emp_file_no','pl','cl','sl','anniversary_date');
 	$userDetails['fname'] 		=	strtoupper($request->fname);
		$userDetails['mname'] 		=	strtoupper($request->mname);
		$userDetails['p_address'] 	=	strtoupper($request->p_address);
		$userDetails['c_address'] 	=	strtoupper($request->c_address);
		$userDetails['bank_name'] 	=	strtoupper($request->bank_name);
		$userDetails['ifsc_code'] 	=	strtoupper($request->ifsc_code);
		$userDetails['bank_branch'] =	strtoupper($request->bank_branch);
		$userDetails['probation_to'] =	$request->joining_date;
		
        if (Input::hasfile('resume')){
            $this->RemoveResume($user->user_details->resume);
            $userDetails['resume'] = $this->uploadResume(Input::file('resume'));
        } 
		
        if (is_array($request->faculty) && !empty($request->faculty)) {
            FacultyRelation::where('user_id', $id)->delete();
            $faculty = $request->faculty;
            foreach ($faculty['from_time'] as $key => $value) {
                if(!empty($value)){
                    $data = array(                  
                        'from_time'=>$value,
                        'to_time'=>$faculty['to_time'][$key],
                    );
					$faculty_relations_id = $user->faculty_relations()->create($data);
					$this->maintain_history(Auth::user()->id, 'faculty_relations', $faculty_relations_id->id, 'create_faculty_relations', json_encode($data));
                }
            }
        }
		
		$branch_id = $request->branch_id;      
        if(isset($branch_id) && is_array($branch_id)){
			Userbranches::where('user_id', $id)->delete();
            foreach($branch_id as $key => $value){
                if(!empty($value)){                          
                    $data = array(                  
                        'branch_id'=>$value,               
                    );                    
					$user_branches_id = $user->user_branches()->create($data);

					$this->maintain_history(Auth::user()->id, 'userbranches', $user_branches_id->id, 'create_user_branches', json_encode($data));
                }
            }
        }
		
		if($request->role_id==2){
			if(isset($request->subject_id) && is_array($request->subject_id)){
				DB::table('faculty_subjects')->where('user_id', $user->id)->delete();
				$subject_ids = $request->subject_id;
				foreach($subject_ids as $key => $value){
					if(!empty($value)){                          
						$faculty_subjects = array(                  
							'user_id'=>$user->id,
							'subject_id'=>$value                   
						);                    
						$faculty_subjects_id = DB::table('faculty_subjects')->insertGetId($faculty_subjects);

						$this->maintain_history(Auth::user()->id, 'faculty_subjects', $faculty_subjects_id, 'create_faculty_subjects', json_encode($faculty_subjects));
					}
				}
			}
		}

        if($user->user_details){
			$userdetails_id = Userdetails::where('user_id', $id)->first();
            $user->user_details()->update($userDetails);
			$this->maintain_history(Auth::user()->id, 'userdetails', $userdetails_id->id, 'update_userdetails', json_encode($userDetails));
        }else{
            $userdetails_id = $user->user_details()->create($userDetails); 
			$this->maintain_history(Auth::user()->id, 'userdetails', $userdetails_id->id, 'create_userdetails', json_encode($userDetails));
        }
        
		
		$enter_mobile = $request->contact_number;
		$check_user_mobile = User::find($id); 
		if(!empty($check_user_mobile->mobile) && !empty($enter_mobile) && $check_user_mobile->mobile != $enter_mobile){ 
			$this->free_course_delete($check_user_mobile->mobile);
		}
		
		$inputs['online_discount'] = $request->online_discount;
		$inputs['offline_discount'] = $request->offline_discount;
        if($user->update($inputs)) {
			//return redirect("admin/employees?update=success&success=Employee Updated Successfully")->with('success', 'Employee Updated Successfully');
			$this->maintain_history(Auth::user()->id, 'users', $id, 'update_users', json_encode($inputs));
			
			
			$check = $_SERVER['QUERY_STRING'];
			if(!empty($check)){
				$nCheck	=	"?".$check;
			}else{
				$nCheck	=	"";
			}
			
			$check_user_id = $id;
			
			$probation_from = $request->probation_from;
			$check_month = 0;
			$check_year  = 0;
			if(!empty($probation_from)){
				$check_month = date('n',strtotime($probation_from));
				$check_year = date('Y',strtotime($probation_from));
				$check_date = date('d',strtotime($probation_from));
				if($check_date > 15){
					$check_month = $check_month + 1;
				}
			}
				
			/* $_details = DB::table('leave_records')->where('user_id',$check_user_id)->where('session',$check_year)->first();
			if(empty($_details)){
				if($check_month > 0){
					$month_leave = DB::table('leave_month')->where('month',$check_month)->first();
					if(!empty($month_leave)){
						DB::table('leave_records')->insertGetId([ 
							'user_id' => $check_user_id,
							'session' => $check_year,
							'pl' => $month_leave->pl,
							'cl' => $month_leave->cl,
							'sl' => $month_leave->sl
						]);
					}
				}
			}
			else{
				if($check_year >= date('Y')){
					if($check_month > 0){
						$month_leave = DB::table('leave_month')->where('month',$check_month)->first();
						if(!empty($month_leave)){
							DB::table('leave_records')->where('id', $_details->id)->update([ 
								//'user_id' => $check_user_id,
								//'session' => $check_year,
								'pl' => $month_leave->pl,
								'cl' => $month_leave->cl,
								'sl' => $month_leave->sl,
								'updated_at' => date('Y-m-d H:i:s')
							]);
						}
					}
				}
			} */
			
			return redirect('admin/employees'.$nCheck)->with('success', 'Employee Updated Successfully');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
	$logged_id       = Auth::user()->id;
        $user = User::with('user_details')->find($id);
        // if($user){
            // $this->RemoveProfile($user->image);
            // $this->RemoveResume($user->user_details->resume);
        // }

        // FacultyRelation::where('user_id', $user->id)->delete();
        // Userdetails::where('user_id', $user->id)->delete();
        $inputs = array('is_deleted' => '1', 'delete_date' => date('Y-m-d'), 'delete_id' => $logged_id);
        if ($user->update($inputs)) {
			$this->maintain_history(Auth::user()->id, 'users', $id, 'delete_users', json_encode($inputs));
            return redirect()->back()->with('success', 'Employee Deleted Successfully');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function togglePublish($id) {
        $user = User::find($id);
        if (is_null($user)) {
            return redirect()->route('admin.employees.index')->with('error', 'Employee not found');
        }
        try {
			
			if($user->status==1){
				$studio = Studio::where('assistant_id',$user->id)->get();
				if(count($studio) > 0){
					// return redirect()->route('admin.employees.index')->with('error', 'Please remove this assistant from assigned studio!');
					return redirect("admin/employees?update=error&error=Please remove this assistant from assigned studio!")->with('error', 'Please remove this assistant from assigned studio!');
				}
			}
			
					
            $user->update([
				'inactive_date' => NULL,
				'reason' 		=> NULL,
				'reason_date' 	=> NULL,
                'status'        => !$user->status,
                'updated_at'    => new \DateTime(),
            ]);
			if($user->status==0){
				Studio::where('assistant_id', $id)->update(['assistant_id'=>NULL]);
			}
			
			$input_data = array('date_time'=>date('Y-m-d H:i:s'));
			$history_data = array(                  
				'user_id'=>Auth::user()->id,
				'table_name'=>'users',
				'table_id'=>$id,
				'type'=>'user_active',
				'save_data'=>json_encode($input_data)
			);                    
			DB::table('all_history')->insert($history_data);
			
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        // return redirect()->route('admin.employees.index')->with('success', 'Status Updated Successfully.');
		return redirect("admin/employees?update=success&success=Employee Updated Successfully")->with('success', 'Employee Updated Successfully');
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approval($id)
    {
        $employee = User::with('user_details','role','faculty_relations','user_branches.branch')->find($id);
		
		$employee_pending = Users_pending::with('user_details_pending','faculty_relations_pending')->where('user_id', $id)->first();
		// echo "<pre>"; print_r($employee_pending); die;
        return view('admin.employee.approval', compact('employee','employee_pending'));
    }
	
	public function approval_update(Request $request, $id)
    {
        $validatedData = $request->validate([
			'admin_approval' => 'required'
        ]);
		
		$inputs = $request->only('admin_approval');
		
		
		$users_pending = Users_pending::with('user_details_pending')->where('user_id', $id)->first();
		if($request->admin_approval=="Approved"){
			// print_r($users_pending->name); die;
			$inputs['name'] = $users_pending->name;
			$inputs['email'] = $users_pending->email;
			$inputs['mobile'] = $users_pending->mobile;
			$inputs['image'] = $users_pending->image;
			
			$userDetails['user_id'] = $users_pending->user_details_pending->user_id;
			$userDetails['dob'] = $users_pending->user_details_pending->dob;
			$userDetails['fname'] = $users_pending->user_details_pending->fname;
			$userDetails['mname'] = $users_pending->user_details_pending->mname;
			$userDetails['alternate_contact_number'] = $users_pending->user_details_pending->alternate_contact_number;
			$userDetails['alternate_email'] = $users_pending->user_details_pending->alternate_email;
			$userDetails['gender'] = $users_pending->user_details_pending->gender;
			$userDetails['material_status'] = $users_pending->user_details_pending->material_status;
			$userDetails['p_address'] = $users_pending->user_details_pending->p_address;
			$userDetails['c_address'] = $users_pending->user_details_pending->c_address;
			$userDetails['employee_type'] = $users_pending->user_details_pending->employee_type;
			$userDetails['degination'] = $users_pending->user_details_pending->degination;
			$userDetails['blood_group'] = $users_pending->user_details_pending->blood_group;
			$userDetails['branch_id'] = $users_pending->user_details_pending->branch_id;
			$userDetails['joining_date'] = $users_pending->user_details_pending->joining_date;
			$userDetails['resume'] = $users_pending->user_details_pending->resume;
			$userDetails['account_number'] = $users_pending->user_details_pending->account_number;
			$userDetails['bank_name'] = $users_pending->user_details_pending->bank_name;
			$userDetails['ifsc_code'] = $users_pending->user_details_pending->ifsc_code;
			$userDetails['bank_branch'] = $users_pending->user_details_pending->bank_branch;
			$userDetails['net_salary'] = $users_pending->user_details_pending->net_salary;
			$userDetails['tds'] = $users_pending->user_details_pending->tds;
			
			$user = User::with('user_details')->where('id', $id)->first();
			if($user->user_details){
				$user->user_details()->update($userDetails);
			}else{
				$user->user_details()->create($userDetails); 
			}
			
			if($user->update($inputs)) {
				return redirect()->route('admin.employees.index')->with('success', 'Approval Updated Successfully');
			} else {
				return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
			}
		}
		else{
			$user = User::where('id', $id)->first();
			
			if($user->update($inputs)) {
				return redirect()->route('admin.employees.index')->with('success', 'Rejected Updated Successfully');
			} else {
				return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
			}
		}
		
        
    }
	
	public function download_excel()
    {   
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		$branch_id  = Input::get('branch_id');
		$search     = Input::get('search');
        $role_id    = Input::get('role_id');
		$status     = Input::get('status');
		$department_type = Input::get('department_type');
		$year_wise_month     =  Input::get('year_wise_month');
		$branch_location     =  Input::get('branch_location'); 
		$month_year_to_days = array();
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		}
		
		$employees = User::with(['user_details','user_branches.branch','role'])->where('role_id','!=','1')->where('is_deleted', '0')->orderBy('name');// dk
		// $employees->where('register_id','!=',NUll);
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search )
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($month_year_to_days)){
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
			
			if($status=='Active'){
				$employees->WhereHas('user_details', function ($q) use ($yr,$mt) {	
					$q->whereRaw("(MONTH(joining_date) = $mt and YEAR(joining_date) = $yr)");							
				});
			}
			if($status=='Inactive'){
				$employees->whereRaw("(MONTH(reason_date) = $mt and YEAR(reason_date) = $yr)");
			}
			
		}

        if(!empty($branch_id)) {
			$branch_id = explode(",",$branch_id);
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk				
				$q->whereIn('branch_id', $branch_id);							
			});	
        }
		if(!empty($branch_location)){
			$employees->WhereHas('user_branches.branch', function ($q) use ($branch_location) { // orWhereHas dk				
				$q->where('branch_location', $branch_location);							
			});	
		}

        if(!empty($role_id)){
            $employees->where('role_id',$role_id);
        }
		
		if(!empty($status) && ($logged_role_id == 29 || $logged_role_id == 24)){
            if($status == 'Inactive'){
                $employees->where('status', '=', '0');
            }else{
                $employees->where('status', '=', '1');
            }
        }
		else{
			if($logged_role_id != 29 && $logged_role_id != 24){
				$employees->where('status', '=', '1');
			}
		}
		
		if(!empty($department_type)){
			$employees->where('department_type','=',$department_type);
		}
		
		if($logged_role_id == 29){
			$users = NewTask::getEmployeeByLogID($logged_id,'all-employee');
		}
		else if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else if($logged_role_id == 21){
			$users = NewTask::getEmployeeByLogID($logged_id,'location_wise');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees->whereIn('id', $employeeArray);
		
        $employees = $employees->get();
		
        if(count($employees) > 0){
            return Excel::download(new EmployeeExport($employees), 'EmployeeData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }  
	
	public function late_emp_list(Request $request){
		$logged_id      = Auth::user()->id;
		$logged_role_id = Auth::user()->role_id;

		$search = Input::get('search');
		$fdate = Input::get('fdate');
		$tdate = Input::get('tdate');
		$branch_id = Input::get('branch_id');
		
		$userArray    = array();
		$supervisorId = array();
		$present      = 0;
		$i            = 0;
		
		if($logged_role_id == 29 || $logged_role_id == 24){
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get();
		}
		else{
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ')->orderBy('id','desc')->get(); 
		}
		
		// if(!empty($employees)){
		// 	foreach($employees as $key=>$value){ 
		// 		if(!in_array($value->id,$supervisorId)){
		// 			$supervisorId[]                   = $value->id;
		// 			$userArray[$i]['id']              = $value->id;
		// 			$userArray[$i]['name']            = $value->name;
		// 			$userArray[$i]['register_id']     = $value->register_id;
		// 			$userArray[$i]['role_name']       = $value->role->name;
		// 			//$userArray[$i]['timing_shift_in'] = $value->user_details->timing_shift_in;
		// 			$i++;
		// 		}
				
		// 	}
		// }

		if($logged_role_id == 29){ 
			$userArray = NewTask::getEmployeeByLogID($logged_id,'approved-emp'); 
		}
		else if($logged_role_id == 21){  
			//$userArray = NewTask::getEmployeeByLogID($logged_id,'create-attendance'); 
			$userArray = NewTask::getEmployeeByLogID($logged_id,'department-emp');
		}
		else{ 
			$userArray = NewTask::getEmployeeByLogID($logged_id); 
		}

		//echo '<pre>'; print_r($userArray);die;

		$time_shift_count = 0;

		$time_shift_attendance_new = AttendanceNew::select('attendance_new.id','attendance_new.emp_id','attendance_new.time','attendance_new.date','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance_new.time, '00:10:00') as m_time"),'branches.name as branch_name')->leftJoin('userdetails','userdetails.user_id', '=', 'attendance_new.emp_id')->leftJoin('userbranches','userbranches.user_id', '=', 'userdetails.user_id')->leftJoin('branches','branches.id', '=', 'userbranches.branch_id');
		
		if(!empty($fdate) && !empty($tdate)){
			$time_shift_attendance_new->where('date', '>=', $fdate);
			$time_shift_attendance_new->where('date', '<=', $tdate);
		}
		else{
			$time_shift_attendance_new->where('attendance_new.date', '=',date('Y-m-d'));
		}

		if(!empty($branch_id)){
			$time_shift_attendance_new->where('branches.id', '=',$branch_id);
		}

		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance_new->whereIn('attendance_new.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance_new->where('attendance_new.type', 'In');
		$time_shift_attendance_new->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance_new.time, '00:10:00')");
		
		//$time_shift_attendance_new = $time_shift_attendance_new->get();
		//echo '<pre>'; print_r($time_shift_attendance_new);die;

		$time_shift_attendance = Attendance::select('attendance.id','attendance.emp_id','attendance.time','attendance.date','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance.time, '00:10:00') as m_time"),'branches.name as branch_name')->leftJoin('userdetails','userdetails.user_id', '=', 'attendance.emp_id')->leftJoin('userbranches','userbranches.user_id', '=', 'userdetails.user_id')->leftJoin('branches','branches.id', '=', 'userbranches.branch_id');
		
		if(!empty($fdate) && !empty($tdate)){
			$time_shift_attendance->where('date', '>=', $fdate);
			$time_shift_attendance->where('date', '<=', $tdate);
		}
		else{
			$time_shift_attendance->where('attendance.date', '=',date('Y-m-d'));
		}

		if(!empty($branch_id)){
			$time_shift_attendance->where('branches.id', '=',$branch_id);
		}
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance->whereIn('attendance.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance->where('attendance.type', 'In');
		$time_shift_attendance->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance.time, '00:10:00')");
		//$time_shift_attendance = $time_shift_attendance->get();
		//echo '<pre>'; print_r($time_shift_attendance);die;

		$late_comman = $time_shift_attendance_new->union($time_shift_attendance);
		$late_comman_result = DB::table(DB::raw("({$late_comman->toSql()}) as late_comman"))
						   ->mergeBindings($late_comman->getQuery())
						   ->groupBy('late_comman.emp_id')
						   ->groupBy('late_comman.date')
						   ->get();

		$time_shift_count = $late_comman_result;				   
		
		//echo '<pre>'; print_r($time_shift_count);die;
		$empArray   = array();
		if(count($time_shift_count) > 0){
			
			foreach($time_shift_count as $key2=>$time_shift_count_value){
				
				$emp_details = User::with('department');
					if (!empty($search)) {
						$emp_details->where(function ($query) use ($search) {
							return $query
							->orWhere('name', 'LIKE', '%' . $search . '%')
							->orWhere('email', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('register_id', 'LIKE', '%' . $search);
						});
					}
				$emp_details = $emp_details->where('id', $time_shift_count_value->emp_id)->first();
				//echo '<pre>'; print_r($emp_details->name);die;
				if(!empty($emp_details)){
					$empArray[$key2]['id']         = !empty($emp_details->id) ? $emp_details->id : '';
                    $empArray[$key2]['register_id']       = !empty($emp_details->register_id) ? $emp_details->register_id : '';

					$empArray[$key2]['name']       = !empty($emp_details->name) ? $emp_details->name : '';
					$empArray[$key2]['email']      = !empty($emp_details->email) ? $emp_details->email : '';
					$empArray[$key2]['mobile']     = !empty($emp_details->mobile) ? $emp_details->mobile : '';
					$empArray[$key2]['department'] = !empty($emp_details->department->name) ? $emp_details->department->name : '';
					$empArray[$key2]['date']       = $time_shift_count_value->date;
					$empArray[$key2]['branch_name']= $time_shift_count_value->branch_name;
					$empArray[$key2]['intime']     = $time_shift_count_value->time;
				}	
			}	
		}
		//echo'<pre>'; print_r($empArray);die;
		return view('admin.employee.late_employee_list', compact('empArray'));
		
	}
	
	public function statusByReason(Request $request){
		//echo '<pre>'; print_r($request->post());die;
		if(!empty($request->reason) && !empty($request->p_id)){
			$input_data = array('reason' => $request->reason, 'reason_date' => $request->reason_date);
			/* if($request->sts == 1){
				$input_data['status'] = 0;
			}
			if($request->sts == 0){
				$input_data['status'] = 1;
			} */
			
			$input_data['status'] = 0;
			$input_data['inactive_date'] = date('Y-m-d');
			
			$save =User::where('id', $request->p_id)->update($input_data);
			
			$user = User::find($request->p_id); 
			if(!empty($user) && !empty($user->mobile)){
				$this->free_course_delete($user->mobile);
			}

			$activeSoftwareAccess = DB::table('system_access_request as a')
			->select('u.email as owner_email',
					'u.name as owner_name',
					's.name as software_name')
            ->leftjoin('system_master as s','s.id','a.software_id')
			->leftjoin('users as u','u.id','s.owner_id')
                            ->where('a.user_id', $user->id)
                            ->where('a.status', 'Approved')
                            ->get();

			if(count($activeSoftwareAccess) > 0){
				foreach($activeSoftwareAccess as $val){
					//print_r($val);die;
					$softwareOwnerEmail = $val->owner_email;
        			$softwareOwnerName  = $val->owner_name;
					// $softwareOwnerEmail = 'sumitkhowal1003@gmail.com'; 
					Mail::to($softwareOwnerEmail)->send(new EmployeeAccessAlertMail(
						$user->name,
						$val,
						$softwareOwnerName
					));
				}
			}
			
			
			$history_data = array(                  
				'user_id'=>Auth::user()->id,
				'table_name'=>'users',
				'table_id'=>$request->p_id,
				'type'=>'user_inactive',
				'save_data'=>json_encode($input_data)
			);                    
			DB::table('all_history')->insert($history_data);

            return redirect()->route('admin.employees.index')->with('success', 'Employee Updated Successfully');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Something Went Wrong !');
        }
	}
	
	public function late_employee_download_excel(){
		$logged_id      = Auth::user()->id;
		$logged_role_id = Auth::user()->role_id;

		$search = Input::get('search');
		$fdate = Input::get('fdate');
		$tdate = Input::get('tdate');
		$branch_id = Input::get('branch_id');
		
		$userArray    = array();
		$supervisorId = array();
		$present      = 0;
		$i            = 0;
		
		/*if($logged_role_id == 29 || $logged_role_id == 24){
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get();
		}
		else{
			$employees    = User::with('user_details','role')->where('status', 1)->where('role_id', '!=', 1)->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ')->orderBy('id','desc')->get(); 
		}
		
		if(!empty($employees)){
			foreach($employees as $key=>$value){ 
				if(!in_array($value->id,$supervisorId)){
					$supervisorId[]                   = $value->id;
					$userArray[$i]['id']              = $value->id;
					$userArray[$i]['name']            = $value->name;
					$userArray[$i]['register_id']     = $value->register_id;
					$userArray[$i]['role_name']       = $value->role->name;
					//$userArray[$i]['timing_shift_in'] = $value->user_details->timing_shift_in;
					$i++;
				}
				
			}
		}*/

	

		if($logged_role_id == 29){
			$userArray = NewTask::getEmployeeByLogID($logged_id,'approved-emp');
		}
		else if($logged_role_id == 21){
			//$userArray = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
			$userArray = NewTask::getEmployeeByLogID($logged_id,'department-emp');
		}
		else{
			$userArray = NewTask::getEmployeeByLogID($logged_id);
		}

		
		$time_shift_count = 0;

		$time_shift_attendance_new = AttendanceNew::select('attendance_new.id','attendance_new.emp_id','attendance_new.time','attendance_new.date','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance_new.time, '00:10:00') as m_time"),'branches.name as branch_name')->leftJoin('userdetails','userdetails.user_id', '=', 'attendance_new.emp_id')->leftJoin('userbranches','userbranches.user_id', '=', 'userdetails.user_id')->leftJoin('branches','branches.id', '=', 'userbranches.branch_id');
		
		if(!empty($fdate) && !empty($tdate)){
			$time_shift_attendance_new->where('date', '>=', $fdate);
			$time_shift_attendance_new->where('date', '<=', $tdate);
		}
		else{
			$time_shift_attendance_new->where('attendance_new.date', '=',date('Y-m-d'));
		}

		if(!empty($branch_id)){
			$time_shift_attendance_new->where('branches.id', '=',$branch_id);
		}

		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance_new->whereIn('attendance_new.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance_new->where('attendance_new.type', 'In');
		$time_shift_attendance_new->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance_new.time, '00:10:00')");
		
		//$time_shift_attendance_new = $time_shift_attendance_new->get();
		//echo '<pre>'; print_r($time_shift_attendance_new);die;

		$time_shift_attendance = Attendance::select('attendance.id','attendance.emp_id','attendance.time','attendance.date','userdetails.timing_shift_in', DB::Raw("SUBTIME(attendance.time, '00:10:00') as m_time"),'branches.name as branch_name')->leftJoin('userdetails','userdetails.user_id', '=', 'attendance.emp_id')->leftJoin('userbranches','userbranches.user_id', '=', 'userdetails.user_id')->leftJoin('branches','branches.id', '=', 'userbranches.branch_id');
		
		if(!empty($fdate) && !empty($tdate)){
			$time_shift_attendance->where('date', '>=', $fdate);
			$time_shift_attendance->where('date', '<=', $tdate);
		}
		else{
			$time_shift_attendance->where('attendance.date', '=',date('Y-m-d'));
		}

		if(!empty($branch_id)){
			$time_shift_attendance->where('branches.id', '=',$branch_id);
		}
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance->whereIn('attendance.emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance->where('attendance.type', 'In');
		$time_shift_attendance->whereRaw("userdetails.timing_shift_in < SUBTIME(attendance.time, '00:10:00')");
		//$time_shift_attendance = $time_shift_attendance->get();
		//echo '<pre>'; print_r($time_shift_attendance);die;

		$late_comman = $time_shift_attendance_new->union($time_shift_attendance);
		$late_comman_result = DB::table(DB::raw("({$late_comman->toSql()}) as late_comman"))
						   ->mergeBindings($late_comman->getQuery())
						   ->groupBy('late_comman.emp_id')
						   ->groupBy('late_comman.date')
						   ->get();

		$time_shift_count = $late_comman_result;				   
		
		//echo '<pre>'; print_r($time_shift_count);die;
		$empArray   = array();
		if(count($time_shift_count) > 0){
			
			foreach($time_shift_count as $key2=>$time_shift_count_value){
				
				$emp_details = User::with('department');
					if (!empty($search)) {
						$emp_details->where(function ($query) use ($search) {
							return $query
							->orWhere('name', 'LIKE', '%' . $search . '%')
							->orWhere('email', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('register_id', 'LIKE', '%' . $search);
						});
					}
				$emp_details = $emp_details->where('id', $time_shift_count_value->emp_id)->first();
				//echo '<pre>'; print_r($emp_details->name);die;
				if(!empty($emp_details)){
					$empArray[$key2]['id']         = !empty($emp_details->id) ? $emp_details->id : '';
                    $empArray[$key2]['register_id']       = !empty($emp_details->register_id) ? $emp_details->register_id : '';

					$empArray[$key2]['name']       = !empty($emp_details->name) ? $emp_details->name : '';
					$empArray[$key2]['email']      = !empty($emp_details->email) ? $emp_details->email : '';
					$empArray[$key2]['mobile']     = !empty($emp_details->mobile) ? $emp_details->mobile : '';
					$empArray[$key2]['department'] = !empty($emp_details->department->name) ? $emp_details->department->name : '';
					$empArray[$key2]['date']       = $time_shift_count_value->date;
					$empArray[$key2]['branch_name']= $time_shift_count_value->branch_name;
					$empArray[$key2]['intime']     = $time_shift_count_value->time;
				}	
			}	
		}
		//echo'<pre>'; print_r($empArray);die;
	
		
        if(count($empArray) > 0){
            return Excel::download(new LateEmployeeExport($empArray), 'LateEmployeeData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function get_leave_month(Request $request){
		//echo '<pre>'; print_r($request->probation_date);die;
		if(!empty($request->probation_date)){
			$probation_date = $request->probation_date; 
			$month = date('n',strtotime($probation_date));
			$date = date('d',strtotime($probation_date));
			if($date > 15){
				$month = $month + 1;
			}
			if($month > 12){
				$month = 1;
			}
			
			$parent_data = DB::table('leave_month')->where('month', $month)->first();
			
			if(!empty($parent_data)){
				echo json_encode($parent_data);
			}else{
				echo json_encode($parent_data);
			}
		}
		else{
			echo json_encode(array());
		}
	}
	
	public function emp_supervisorid_update_manual(Request $request){
		
		/* $all_user = DB::table('9321dbdd_1e0e_4750_ba81_d617bf9e4b69')->get();
		
		foreach($all_user as $u_val){
			$user_id = $u_val->COL_1;
			$update_supervisor_id = $u_val->COL_4;
			DB::table('users')->where('id', $user_id)->update([ 'supervisor_id' => $update_supervisor_id]);
		} */
		
		// die('qqq');
		
		$all_emp = DB::table('userbranches')->whereIn('branch_id',[44])->get();
		// echo count($all_emp); die;
		if(!empty($all_emp)){
			foreach($all_emp as $val){
				$user_id = $val->user_id;
				$superVisioerrr = "1004";
				$emp = DB::table('users')->where(['id'=>$user_id,'status'=>'1'])->first();
				if(!empty($emp)){
					if(!empty($emp->supervisor_id)){
						// die('2');
						$supervisor_id = json_decode($emp->supervisor_id);
						if(!empty($supervisor_id)){
							// die('3');
							if(!in_array($superVisioerrr,$supervisor_id)){
								array_push($supervisor_id,$superVisioerrr);
								$update_supervisor_id = json_encode($supervisor_id);
								DB::table('users')->where('id', $user_id)->update([ 'supervisor_id' => $update_supervisor_id]);
							}
						}
						else{
							// die('4');
							$update_supervisor_id = array($superVisioerrr);
							DB::table('users')->where('id', $user_id)->update([ 'supervisor_id' => $update_supervisor_id]);
						}
					}
					else{
						// die('1');
						$update_supervisor_id = array($superVisioerrr);
						DB::table('users')->where('id', $user_id)->update([ 'supervisor_id' => $update_supervisor_id]);
					}
				}
				
				// die;
			}
		}
	}
	
	public function getBranch(Request $request){
		
		$subBranch = DB::table('branches')->where('branch_location', $request->branch_id)->get();
		
		
		if (!empty($subBranch))
        {
            echo $res = "<option value=''> Select Branch </option>";
            foreach ($subBranch as $key => $value)
            {
                if (!empty($value->id))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value=''> Sub Branch Not Found </option>";
            die();
        }
	}
	
	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('all_history')->insert($history_data);
	}
	
	public function esic_no_detail(Request $request){ 
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		session_start();

        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		
		$search = Input::get('search');
		$status = Input::get('status');

        $employees = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', '1')->orderBy('name');
		
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }
		
		
		$employees->WhereHas('user_details', function ($q) use ($status) { 
			if(!empty($status) && $status == '0'){
				$q->whereNull('esic_no');
			}	
			elseif(!empty($status) && $status == '1'){
				$q->whereNotNull('esic_no');
			}
			elseif(!empty($status) && $status == '2'){
				
			}				
			else{
				$q->whereNull('esic_no');		
			}						
		});	
        
		$employees = $employees->paginate(20);
        
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (20*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        return view('admin.employee.esic_no', compact('employees','pageNumber','params'));
	}

	public function uan_no_detail(Request $request){ 
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		session_start();

        $logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		
		$search = Input::get('search');
		$status = Input::get('status');

        $employees = User::with(['user_details'])->where('role_id','!=','1')->where('is_deleted', '0')->where('status', '1')->orderBy('name');
		
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }
		
		
		$employees->WhereHas('user_details', function ($q) use ($status) { 
			if(!empty($status) && $status == '0'){
				$q->whereNull('uan_no');
			}	
			elseif(!empty($status) && $status == '1'){
				$q->whereNotNull('uan_no');
			}
			elseif(!empty($status) && $status == '2'){
				
			}				
			else{
				$q->whereNull('uan_no');		
			}						
		});	
        
		$employees = $employees->paginate(20);
        
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (20*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        return view('admin.employee.uan_no', compact('employees','pageNumber','params'));
	}

	public function get_sub_department(Request $request)
    {
        $department_type_id = $request->department_type_id;
        $sub_department = SubDepartment::where('department_id', $department_type_id)->where('status', 'Active')->where('is_deleted', '0')->get();
        if (!empty($sub_department))
        {
            $res = "";
            foreach ($sub_department as $key => $value)
            {
                if (!empty($value->name) && !empty($value->id))
                {
                    $res .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
			if(empty($res)){
				$res = "<option value=''> Select Sub Department </option>";
			}
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<option value=''> Sub Department Not Found </option>";
            die();
        }
    }
	
	public function addSupervisor(){
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        $employees_list = User::where('status','1')->where('is_deleted','0')->get();
		$branch_list = Branch::where('status','1')->where('is_deleted','0')->get();
        return view('admin.employee.add-supervisor', compact('department_list','employees_list','branch_list'));
    }
	
	public function storeSupervisorByBranch(Request $request)
	{
		
        $validatedData = $request->validate([
			'branch_location' =>'required',
            //'branch_id' => 'required',
            'to_branch_employee' => 'required',
        ]);
		
        //$emp_list = User::select('users.*')->leftJoin('userbranches','userbranches.user_id','=','users.id')->where('users.status', '1')->where('users.is_deleted', '0')->where('userbranches.branch_id', $request->branch_id)->get();
	
		$emp_list = User::select('users.*')->leftJoin('userbranches','userbranches.user_id','=','users.id')
					->leftjoin('branches','branches.id','=','userbranches.branch_id')
					->where('branches.branch_location', $request->branch_location);
					if(!empty($request->branch_id[0])){
						
						 $emp_list->whereIn('userbranches.branch_id', $request->branch_id);
						
					}
					$emp_list = $emp_list->where('users.status', '1')
					->where('users.is_deleted', '0')
					->get();
					
	   // dd(count($emp_list));
		$ii=0;

		//echo '<pre>'; print_r($emp_list); die;
        if(count($emp_list) > 0){
			foreach($emp_list as $emp_list_val){
				// $json_decode_data = json_decode($emp_list_val->supervisor_id);
				if(!empty($emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($emp_list_val->supervisor_id);
					//dd($json_decode_data);
				}
				else{
					$json_decode_data = array();
				}

				$check_emp_res = in_array($request->to_branch_employee, $json_decode_data);
				//dd($check_emp_res);
				  
				
				if(empty($check_emp_res)){
					array_push($json_decode_data, $request->to_branch_employee);
					$json_encode_data = json_encode($json_decode_data); 		
					User::where('id', $emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
					$ii++;
				}
			}
            return redirect()->route('admin.employees.add-supervisor')->with('success', "Supervisor Add Successfully");
        }
        else {
            return redirect()->route('admin.employees.add-supervisor')->with('error', 'User Not Found');
        }
		
		
		
    }

	public function storeSupervisorByDepartment(Request $request){
        $validatedData = $request->validate([
            'from_department' => 'required',
            'to_department_employee' => 'required',
        ]);
		$branch_id = $request->branch_id;
		$sub_department = $request->from_sub_department;
        $emp_list_q = User::with(['user_branches'])->where('status', '1')->where('is_deleted', '0')->where('department_type', $request->from_department);
		if(!empty($sub_department)) {		
			$emp_list_q->where('sub_department_type', $sub_department);				
        }
		if(!empty($branch_id)) {
			if(!empty($branch_id[0])) {
				$emp_list_q->WhereHas('user_branches', function ($q) use ($branch_id) {		
					$q->whereIn('branch_id', $branch_id);							
				});	
			}				
        }
		
		$emp_list = $emp_list_q->get();
        if(count($emp_list) > 0){
			// echo "<pre>"; print_R($emp_list); die;
			foreach($emp_list as $emp_list_val){
				if(!empty($emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($emp_list_val->supervisor_id);
				}
				else{
					$json_decode_data = array();;
				}
				$check_emp_res = in_array($request->to_department_employee, $json_decode_data);
				if(empty($check_emp_res)){
					array_push($json_decode_data, $request->to_department_employee);
					$json_encode_data = json_encode($json_decode_data);
					User::where('id', $emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
				}
			}
            return redirect()->route('admin.employees.add-supervisor')->with('success', 'Supervisor Add Successfully');
        }
        else {
            return redirect()->route('admin.employees.add-supervisor')->with('error', 'User Not Found');
        }
    }

	public function storSupervisorByEmployee(Request $request){
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'supervisor_employee' => 'required',
        ]);
        //echo '<pre>'; print_r($request->post()); die;
        $multi_emp_list = User::where('status', '1')->where('is_deleted', '0')->whereIn('id', $request->employee_name)->get();
        
        if(count($multi_emp_list) > 0){
            foreach($multi_emp_list as $multi_emp_list_val){
				// $json_decode_data = json_decode($multi_emp_list_val->supervisor_id);
				
				if(!empty($multi_emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($multi_emp_list_val->supervisor_id);
				}
				else{
					$json_decode_data = array();;
				}
				
				$check_emp_res = in_array($request->supervisor_employee, $json_decode_data);
				if(empty($check_emp_res)){
					array_push($json_decode_data, $request->supervisor_employee);
					$json_encode_data = json_encode($json_decode_data);
					User::where('id', $multi_emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
				}
			}
            return redirect()->route('admin.employees.add-supervisor')->with('success', 'Supervisor Add Successfully');
        }
        else {
            return redirect()->route('admin.employees.add-supervisor')->with('error', 'User Not Found');
        }
    }

	public function jobRole(){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$emp_id = Input::get('emp_id');

        $employees_list = User::where('status','1')->where('is_deleted','0');

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}

		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees_list->whereIn('id', $employeeArray);
		
		$employees_list = $employees_list->orderBy('name')->get();

		$job_role_result  = JobRole::with('user')->whereHas('user', function ($q) use ($logged_role_id,$logged_id,$employeeArray){
			//if($logged_role_id == 21){
				//$q->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ')->orWhere('id', $logged_id);
				$q->whereIn('id', $employeeArray);
			//}
		})->where('is_deleted','0');
		if (!empty($emp_id)){
            $job_role_result->where('user_id',$emp_id);
        }
		$job_role_result  = $job_role_result->get();
		//echo '<pre'; print_r($job_role_result); die;
        return view('admin.employee.job-role', compact('employees_list','job_role_result'));
    }

	public function viewJobRole(){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$job_role_result  = JobRole::where('user_id', $logged_id)->where('status', 'Lock')->where('is_deleted','0')->first();

        return view('admin.employee.view-job-role', compact('job_role_result'));
    }

	public function addJobRole($id=NULL){ 
		$job_result = array();
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		if(!empty($id)){
			$job_result = JobRole::find($id);
		}
		$employees_list = User::where('status','1')->where('is_deleted','0');
		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}

		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees_list->whereIn('id', $employeeArray);

		$employees_list = $employees_list->orderBy('name')->get();
        return view('admin.employee.add-job-role', compact('employees_list','job_result'));
	}

	public function storeJobRole(Request $request, $id=NULL){
        $validatedData = $request->validate([
            //'user_id' => 'required|unique:job_roles,user_id,'.$id,
            'user_id' => ['required', Rule::unique('job_roles')->where(function ($query) use($id, $request) {
				if($id){
					return $query->where('is_deleted', '0')->where('user_id', $request->user_id)->where('user_id', $id);
				}
				else{
					return $query->where('is_deleted', '0')->where('user_id', $request->user_id);
				}
			})],
            'description' => 'required',
        ]);
        
		$inputs = $request->only('user_id','description');        

		if(!empty($id)){
			$job_role = JobRole::where('id', $id)->update($inputs); 
			$msg = "Job Role Update Successfully";   
		}
		else{
			$job_role = JobRole::create($inputs); 
			$msg = "Job Role Added Successfully";   
		}
        

        if ($job_role) {
            return redirect()->route('admin.employees.job-role')->with('success', $msg);
        } else {
            return redirect()->route('admin.employees.job-role')->with('error', 'Something Went Wrong !');
        }
    }

	public function jobRoleTogglePublish($id){
		$job_role = JobRole::find($id);
        if (is_null($job_role)) {
            return redirect()->route('admin.employees.job-role')->with('error', 'Job Role not found');
        }
		
		if($job_role->status == 'Lock'){
			$sts = 'Unlock';
		}
		else{
			$sts = 'Lock';
		}
		
		$job_role->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.employees.job-role')->with('success', $sts.' Successfully.');
	}

	public function jobRoleDestroy($id)
    { 
		$logged_id = Auth::user()->id;
        $job_role  = JobRole::find($id);
        $inputs = array('is_deleted' => '1');
        if ($job_role->update($inputs)) {
			return redirect()->route('admin.employees.job-role')->with('success', 'Job Role Deleted Successfully');
        } else {
			return redirect()->route('admin.employees.job-role')->with('error', 'Something Went Wrong !');
        }
    }

	public function probationMonth(){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$year_wise_month = Input::get('year_wise_month');
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
		}
		else{
			$yr = date('Y');
			$mt = date('m');
		}
		
        $employees_list = User::where('status','1')->where('is_deleted','0')->orderBy('name')->get();

		$probation_result  = User::with('user_details')->where('is_deleted','0');

		$probation_result  = User::with('user_details')->whereHas('user_details', function ($q) use ($yr, $mt){
				$q->whereRaw("YEAR(probation_from) = $yr AND MONTH(probation_from) = $mt");
		})->where('is_deleted','0');

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}

		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$probation_result->whereIn('id', $employeeArray);
		$probation_result = $probation_result->get();

		//echo '<pre>'; print_r($probation_result); die;
        return view('admin.employee.probation-month', compact('employees_list','probation_result'));
    }
	
	public function removeSupervisor(){
        $department_list = Department::where('status','Active')->where('is_deleted','0')->get();
        $employees_list = User::where('status','1')->where('is_deleted','0')->get();
        $branch_list = Branch::where('status','1')->where('is_deleted','0')->get();
        return view('admin.employee.remove-supervisor', compact('department_list','employees_list','branch_list'));
    }

	public function removeSupervisorByDepartment(Request $request){
        $validatedData = $request->validate([
            'from_department' => 'required',
            'to_department_employee' => 'required',
        ]);		
		
		$branch_id = $request->branch_id;
		
		$emp_list_q = User::with(['user_branches'])->where('status', 1)->where('is_deleted', '0')->where('department_type', $request->from_department);
		
		
		if(count($branch_id) > 0 && $branch_id[0]!="") {
			$emp_list_q->WhereHas('user_branches', function ($q) use ($branch_id) {		
				$q->whereIn('branch_id', $branch_id);							
			});					
        }
		
		$emp_list = $emp_list_q->get();
		
        if(count($emp_list) > 0){
			foreach($emp_list as $emp_list_val){
				// $json_decode_data = json_decode($emp_list_val->supervisor_id);
				
				if(!empty($emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($emp_list_val->supervisor_id);
				}
				else{
					$json_decode_data = array();;
				}

				$check_emp_res = in_array($request->to_department_employee, $json_decode_data);
				if(!empty($check_emp_res)){
					$remove_key = array_search($request->to_department_employee, $json_decode_data);

					if (false !== $remove_key) {
						unset($json_decode_data[$remove_key]);
					}
					$json_decode_data = array_values($json_decode_data);
					$json_encode_data = json_encode($json_decode_data);
					User::where('id', $emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
				}
			}
            return redirect()->route('admin.employees.remove-supervisor')->with('success', 'Supervisor Remove Successfully');
        }
        else {
            return redirect()->route('admin.employees.remove-supervisor')->with('error', 'User Not Found');
        }
    }

	public function removeSupervisorByEmployee(Request $request){
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'supervisor_employee' => 'required',
        ]);
        //echo '<pre>'; print_r($request->post()); die;
        $multi_emp_list = User::where('status', '1')->where('is_deleted', '0')->whereIn('id', $request->employee_name)->get();
        
        if(count($multi_emp_list) > 0){
            foreach($multi_emp_list as $multi_emp_list_val){
				// $json_decode_data = json_decode($multi_emp_list_val->supervisor_id);
				
				if(!empty($multi_emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($multi_emp_list_val->supervisor_id);
				}
				else{
					$json_decode_data = array();;
				}

				$check_emp_res = in_array($request->supervisor_employee, $json_decode_data);
				if(!empty($check_emp_res)){
					$remove_key = array_search($request->supervisor_employee, $json_decode_data);

					if (false !== $remove_key) {
						unset($json_decode_data[$remove_key]);
					}
					$json_decode_data = array_values($json_decode_data);
					$json_encode_data = json_encode($json_decode_data);
					User::where('id', $multi_emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
				}
			}
            return redirect()->route('admin.employees.remove-supervisor')->with('success', 'Supervisor Remove Successfully');
        }
        else {
            return redirect()->route('admin.employees.remove-supervisor')->with('error', 'User Not Found');
        }
    }


	public function removeSupervisorByBranch(Request $request){
        $validatedData = $request->validate([
			'branch_location' => 'required',
            //'branch_id' => 'required',
            'to_branch_employee' => 'required',
        ]);
		
		// $branch_id	=	$request->branch_id;
		
        //$emp_list = User::select('users.*')->leftJoin('userbranches','userbranches.user_id','=','users.id')->where('users.status', '1')->where('users.is_deleted', '0')->where('userbranches.branch_id', $request->from_branch)->get();
		$emp_list = User::select('users.*')->leftJoin('userbranches','userbranches.user_id','=','users.id')
					->leftjoin('branches','branches.id','=','userbranches.branch_id')
					->where('branches.branch_location', $request->branch_location);
					if(!empty($request->branch_id[0])){	
							$emp_list->whereIn('userbranches.branch_id', $request->branch_id);						
						}
					$emp_list = $emp_list->where('users.status', '1')
					->where('users.is_deleted', '0')
					->get();
					
		// echo '<pre>'; print_r($emp_list);die;
		
        if(count($emp_list) > 0){
			foreach($emp_list as $emp_list_val){
				
				if(!empty($emp_list_val->supervisor_id)){
					$json_decode_data = json_decode($emp_list_val->supervisor_id);
				}
				else{
					$json_decode_data = array();;
				}
				
				
				$check_emp_res    = in_array($request->to_branch_employee, $json_decode_data);
				
				
				if(!empty($check_emp_res)){
					$remove_key = array_search($request->to_branch_employee, $json_decode_data);

					if (false !== $remove_key) {
						unset($json_decode_data[$remove_key]);
					}
					$json_decode_data = array_values($json_decode_data);
					$json_encode_data = json_encode($json_decode_data); 
					User::where('id', $emp_list_val->id)->update(['supervisor_id' => $json_encode_data ]);
				}
			
			}
            return redirect()->route('admin.employees.remove-supervisor')->with('success', 'Supervisor Remove Successfully');
        }
        else {
            return redirect()->route('admin.employees.remove-supervisor')->with('error', 'User Not Found');
        }
    }
	
	public function birthday(){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$search = Input::get('search');
		$b_date = Input::get('b_date');
		$selectToDate = Input::get('t_date');
		
		if(empty($b_date) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($b_date)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($b_date) && !empty($selectToDate)){
			if($b_date > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		$employees = User::select('users.name','users.register_id','branches.name as branches_name','users.mobile','userdetails.degination','userdetails.dob')->leftJoin('userdetails','userdetails.user_id','=','users.id')->leftJoin('userbranches','userbranches.user_id','=','users.id')->leftJoin('branches','branches.id','=','userbranches.branch_id')->where('users.status', '1')->where('users.is_deleted', '0');
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                ->orWhere('users.mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('users.register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($b_date) && !empty($selectToDate)){
			// $b_date = explode('-',$b_date);
		
			// $current_month = $b_date[1];
			// $current_day = $b_date[2];
		
			// $employees->whereRaw("(MONTH(userdetails.dob) = $current_month and DAY(userdetails.dob) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.dob,'%m-%d') >= DATE_FORMAT('$b_date','%m-%d') and DATE_FORMAT(userdetails.dob,'%m-%d') <= DATE_FORMAT('$selectToDate','%m-%d'))");
		}
		else{
			// $current_month = date("m");
			// $current_day = date("d");
			$current_date = date("Y-m-d");
		
			// $employees->whereRaw("(MONTH(userdetails.dob) = $current_month and DAY(userdetails.dob) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.dob,'%m-%d') = DATE_FORMAT('$current_date','%m-%d'))");
		}
       

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees->whereIn('users.id', $employeeArray);
		$employees = $employees->groupBy('users.id')->get();

		//echo '<pre>'; print_r($employees); die;
        return view('admin.employee.birthday', compact('employees'));
    }

	public function workAnniversary(){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$search = Input::get('search');
		$b_date = Input::get('b_date');
		$selectToDate = Input::get('t_date');
		
		if(empty($b_date) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($b_date)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($b_date) && !empty($selectToDate)){
			if($b_date > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		$employees = User::select('users.name','users.register_id','branches.name as branches_name','userdetails.degination','userdetails.joining_date')->leftJoin('userdetails','userdetails.user_id','=','users.id')->leftJoin('userbranches','userbranches.user_id','=','users.id')->leftJoin('branches','branches.id','=','userbranches.branch_id')->where('users.status', '1')->where('users.is_deleted', '0');
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                ->orWhere('users.mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('users.register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($b_date)){
			// $b_date = explode('-',$b_date);
		
			// $current_month = $b_date[1];
			// $current_day = $b_date[2];
		
			// $employees->whereRaw("(MONTH(userdetails.joining_date) = $current_month and DAY(userdetails.joining_date) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.joining_date,'%m-%d') >= DATE_FORMAT('$b_date','%m-%d') and DATE_FORMAT(userdetails.joining_date,'%m-%d') <= DATE_FORMAT('$selectToDate','%m-%d'))");
		}
		else{
			// $current_month = date("m");
			// $current_day = date("d");
		
			$current_date = date("Y-m-d");
			// $employees->whereRaw("(MONTH(userdetails.joining_date) = $current_month and DAY(userdetails.joining_date) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.joining_date,'%m-%d') = DATE_FORMAT('$current_date','%m-%d'))");
		}
       

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees->whereIn('users.id', $employeeArray);
		$employees = $employees->groupBy('users.id')->get();

		
        return view('admin.employee.work-anniversary', compact('employees'));
    }
	
	public function employeeId(){ 
		$emp_code = Input::get('emp_code');
		$emp_result = '';
		if(!empty($emp_code)){
			$emp_result = User::select('id','name')->where('register_id', 'LIKE', '%'.$emp_code.'%')->where('status','1')->where('is_deleted','0')->first();
		}
		
		return view('admin.employee.employee-id', compact('emp_result'));
	}
	
	public function isCompOff($id){
		if(!empty($id)){
			$employee = User::with('user_details','role','faculty_relations','user_branches.branch')->find($id);
			return view('admin.employee.comp-off', compact('employee'));
		}
		else{
			return back()->with('error', 'ID Not Found');
		}
	}
	
	public function storeCompOff(Request $request,$id){
		if(!empty($id)){
			$updateData = array();
			$comp_off_date = NULL;
			if($request->extraPay == '0'){
				$validatedData = $request->validate([
									'comp_off_start_date' => 'required'
								]); 
				
				if(!empty($request->comp_off_start_date)){
					$comp_off_date = $request->comp_off_start_date.'-01';
				}
			}
			
			$updateData['is_extra_working_salary'] = $request->extraPay;
			$updateData['comp_off_start_date'] = $comp_off_date;
			User::where('id', $id)->update($updateData);
			$this->maintain_history(Auth::user()->id, 'users', $id, 'update_comp_off', json_encode($updateData));
			return back()->with('success', 'Successfully Added');
		}
		else{
			return back()->with('error', 'ID Not Found');
		}
	}
	
	public function free_course_delete($mobile){
		if(!empty($mobile)){
			$contact_no = $mobile;
			$url = "https://support.utkarshapp.com/index.php/getUserCourseDetails?username=$contact_no";
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
			
			$data = '{"destination_number":"'.$contact_no.'"}';
			
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			$resp = curl_exec($curl);
			curl_close($curl);
			if(!empty($resp)){
				$all_courses = json_decode($resp,true); 
				$free_course = $all_courses['freeCourses']; 
				
				if(count($free_course) > 0){
					foreach($free_course as $free_course_val){ 
						$contact_no = $mobile;
						$course_id = $free_course_val['id']; 
						
						
						$url = "https://support.utkarshapp.com/index.php/deleteMainandPackageCourseforUser?userName=$contact_no&mainCourseId=$course_id";
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
						$result = json_decode($resp);  
						if($result->status == 'deleted'){
							$course_status = 'deleted';
						}
						else{
							$course_status = 'error';
						}
						
					}
				}
			}
		}
	}
	
	public function download_probation_excel()
    {   
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$search     = Input::get('search');
		$year_wise_month     =  Input::get('year_wise_month');
		$month_year_to_days = array();
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
		}
		else{
			$yr = date('Y');
			$mt = date('m');
		}
		
	
		
		$employees_list = User::where('status','1')->where('is_deleted','0')->orderBy('name')->get();

		$probation_result  = User::with('user_details')->where('is_deleted','0');

		$probation_result  = User::with('user_details')->whereHas('user_details', function ($q) use ($yr, $mt){
				$q->whereRaw("YEAR(probation_from) = $yr AND MONTH(probation_from) = $mt");
		})->where('is_deleted','0');
		
		 

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}

		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$probation_result->whereIn('id', $employeeArray);
		$probation_result = $probation_result->get();
		
		//dd($probation_result);
		
        if(count($probation_result) > 0){
            return Excel::download(new EmployeeProbationExport($probation_result), 'EmployeeProbationData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
		
    }  
	
	
	public function download_birthday_excel()
	{
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$search = Input::get('search');
		$b_date = Input::get('b_date');
		$selectToDate = Input::get('t_date');
		
		if(empty($b_date) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($b_date)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($b_date) && !empty($selectToDate)){
			if($b_date > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		$employees = User::select('users.name','users.register_id','branches.name as branches_name','users.mobile','userdetails.degination','userdetails.dob')->leftJoin('userdetails','userdetails.user_id','=','users.id')->leftJoin('userbranches','userbranches.user_id','=','users.id')->leftJoin('branches','branches.id','=','userbranches.branch_id')->where('users.status', '1')->where('users.is_deleted', '0');
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                ->orWhere('users.mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('users.register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($b_date) && !empty($selectToDate)){
			// $b_date = explode('-',$b_date);
		
			// $current_month = $b_date[1];
			// $current_day = $b_date[2];
		
			// $employees->whereRaw("(MONTH(userdetails.dob) = $current_month and DAY(userdetails.dob) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.dob,'%m-%d') >= DATE_FORMAT('$b_date','%m-%d') and DATE_FORMAT(userdetails.dob,'%m-%d') <= DATE_FORMAT('$selectToDate','%m-%d'))");
		}
		else{
			// $current_month = date("m");
			// $current_day = date("d");
			$current_date = date("Y-m-d");
		
			// $employees->whereRaw("(MONTH(userdetails.dob) = $current_month and DAY(userdetails.dob) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.dob,'%m-%d') = DATE_FORMAT('$current_date','%m-%d'))");
		}
       

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees->whereIn('users.id', $employeeArray);
		$employees = $employees->groupBy('users.id')->get();
		
		if(count($employees) > 0){
            return Excel::download(new EmployeeBirthdayExport($employees), 'EmployeeBirthdayData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
		
	}
	
	public function download_work_anniversary_excel(){
		
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;

		$search = Input::get('search');
		$b_date = Input::get('b_date');
		$selectToDate = Input::get('t_date');
		
		if(empty($b_date) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($b_date)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($b_date) && !empty($selectToDate)){
			if($b_date > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		$employees = User::select('users.name','users.register_id','branches.name as branches_name','userdetails.degination','userdetails.joining_date')->leftJoin('userdetails','userdetails.user_id','=','users.id')->leftJoin('userbranches','userbranches.user_id','=','users.id')->leftJoin('branches','branches.id','=','userbranches.branch_id')->where('users.status', '1')->where('users.is_deleted', '0');
		
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                ->orWhere('users.mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('users.register_id', 'LIKE', '%' . $search);
            });
        }
		
		if(!empty($b_date)){
			// $b_date = explode('-',$b_date);
		
			// $current_month = $b_date[1];
			// $current_day = $b_date[2];
		
			// $employees->whereRaw("(MONTH(userdetails.joining_date) = $current_month and DAY(userdetails.joining_date) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.joining_date,'%m-%d') >= DATE_FORMAT('$b_date','%m-%d') and DATE_FORMAT(userdetails.joining_date,'%m-%d') <= DATE_FORMAT('$selectToDate','%m-%d'))");
		}
		else{
			// $current_month = date("m");
			// $current_day = date("d");
		
			$current_date = date("Y-m-d");
			// $employees->whereRaw("(MONTH(userdetails.joining_date) = $current_month and DAY(userdetails.joining_date) = $current_day)");
			$employees->whereRaw("(DATE_FORMAT(userdetails.joining_date,'%m-%d') = DATE_FORMAT('$current_date','%m-%d'))");
		}
       

		if($logged_role_id == 24){
			$users = NewTask::getEmployeeByLogID($logged_id,'attendance');
		}
		else{
			$users = NewTask::getEmployeeByLogID($logged_id,'create-attendance');
		}
		
		$employeeArray = array();
		$usr=$logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $users));
		$employeeArray   = explode(',',$usr);
		$employees->whereIn('users.id', $employeeArray);
		$employees = $employees->groupBy('users.id')->get();
		
		//dd($employees);
		
		if(count($employees) > 0){
            return Excel::download(new EmployeeWorkAnniversaryExport($employees), 'EmployeeAnniversaryData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
		
		
	}
	
	
	public function time_update(Request $request)
    {
		
		$empID  =	$request->emp_code;
		$timing_shift_in  =	$request->timing_shift_in;
		$timing_shift_out  =	$request->timing_shift_out;
		$total_time  =	$request->total_time;
		
		$logged_id    = Auth::user()->id;
		//Leave Calculation	
		if(!empty($empID) && !empty($total_time) && !empty($timing_shift_in) && !empty($timing_shift_out)){
			$empID = explode(',',$empID);
			$userData	=	User::select('id')->whereIn('register_id', $empID)->get();
			// print_r(count($userData)); die;
		
			
				// echo $empID ."/".  $timing_shift_in ."/".  $timing_shift_out ."/".  $total_time ."/"; die;
			if(count($userData) > 0){
				$ii = 0;
				foreach($userData as $user_detail){
					$user_id = $user_detail['id'];
					$user = User::with('user_details')->where('id', $user_id)->first();
					$inputs['total_time'] 	= $total_time;
					if($user->update($inputs)) {
						$userDetails['timing_shift_in'] =	$timing_shift_in;
						$userDetails['timing_shift_out'] =	$timing_shift_out;
						$user->user_details()->update($userDetails);
						
						$ii++;
					}
				}
				
				
				$save_successfully ="Time records ($ii) Update successfully";
				return view('admin.employee.time_update', compact('save_successfully'));
				// return redirect()->back()->with('success', 'Save Successfully.');
			}else{
				return redirect()->route('admin.employee.time_update')->with('error', 'Something went wrong');
			}
		}
		else{
			$save_successfully ="Required all fields.";
			return view('admin.employee.time_update',compact('save_successfully'));
		}
    }
	
}
