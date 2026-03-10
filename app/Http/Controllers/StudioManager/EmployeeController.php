<?php

namespace App\Http\Controllers\StudioManager;

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

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
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
			return redirect("studiomanager/employees".$_SESSION['redirect_url'])->with($_GET['update'],$_GET[$_GET['update']]);
		}
		unset($_SESSION["redirect_url"]);
		if(isset($_GET['search'])){
			$paremeters="?";
			$all_request = $_GET;
			foreach($all_request as $key=>$val){
				$paremeters .= "&$key=$val";
			}
			$_SESSION["redirect_url"] = $paremeters;
		}
		
		
		$search = Input::get('search');
        $branch_id = Input::get('branch_id');
        $role_id = 2;
		$status = Input::get('status');
		$department_type = Input::get('department_type');

        // $employees = User::with(['user_details.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');
        $employees = User::with(['user_branches.branch','role'])->where('role_id','!=','1')->where('is_deleted', '0')->orderBy('name');// dk
		$employees->where('register_id','!=',NUll);
		
		
		
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }

        if(!empty($branch_id)) {
            /*$employees->WhereHas('user_details', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });*/
			
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if(!empty($role_id)){
            $employees->where('role_id',$role_id);
        }
		
		if(!empty($status)){
            if($status == 'Inactive'){
                $employees->where('status', '=', '0');
            }else{
                $employees->where('status', '=', '1');
            }
        }
		if(!empty($department_type)){
			$employees->where('department_type','=',$department_type);
		}

        if($logged_role_id == 21){
            $employees = $employees->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
        }
		
       $employees = $employees->paginate(50);
        
		$allDepartmentTypes  = $this->allDepartmentTypes();
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        
		//echo '<pre>'; print_r($employees); die;
        return view('studiomanager.employee.index', compact('employees','pageNumber','params','allDepartmentTypes')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$subjects = Subject::where('status', '=', '1')->orderBy('id', 'desc')->get();
        return view("studiomanager.employee.add");
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
            'email' => 'max:150|unique:users',
            'contact_number' => 'required|numeric|digits:10|unique:users,mobile,NULL,id,status,'.'1',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5120',     // 5MB Validation  //1024 = 1MB           
            'resume' => 'mimes:pdf,doc,docx,jpeg,png,jpg|max:5120', // 5MB Validation  //1024 = 1MB           
            'branch_id' => 'required',            
            'alternate_contact_number' => 'nullable|numeric|digits:10',            
            'account_number' => 'nullable|numeric|digits_between:0,20',            
            'bank_name' => 'max:100',            
            'ifsc_code' => 'max:50',            
            'bank_branch' => 'max:150',            
            'net_salary' => 'nullable|numeric|digits_between:0,10',            
            'tds' => 'nullable|numeric|digits_between:0,10',
			'fname' => 'nullable|max:100', //|regex:/^[\pL\s\-]+$/u
			'mname' => 'nullable|max:100', //|regex:/^[\pL\s\-]+$/u
			'gender' => 'required',   
			'c_address' => 'required|max:1000',   
			'p_address' => 'required|max:1000',
			'joining_date' => 'required', 	
			// 'aadhar_card_no' => 'min:12|max:12',
			'total_time' => 'required|numeric',			
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

        $inputs = $request->only('role_id','email','mobile','image','password','supervisor_id','department_type','total_time');
		if(!empty($request->supervisor_id)){
			$inputs['supervisor_id'] = json_encode($request->supervisor_id);
		}
		
		$inputs['name'] 	= strtoupper($request->name);
		$inputs['nominee_name'] = strtoupper($request->nominee_name);
		$inputs['nickname'] 	= strtoupper($request->nickname);

		$inputs['mobile'] = $request->contact_number;
        	$inputs['password'] = Hash::make('123456');
			
		if(!empty($request->agreement)){
			$inputs['agreement'] 	= $request->agreement;
		}
		
			
		if(!empty($request->committed_hours)){
			$inputs['committed_hours'] 	= $request->committed_hours;
		}
		if(!empty($request->agreement_start_date) && !empty($request->agreement_end_date)){
			$inputs['agreement_start_date'] 	= $request->agreement_start_date;
			$inputs['agreement_end_date'] 	= $request->agreement_end_date;
		}

		// Remove branch_id dk
        $userDetails = $request->only('user_id','dob','alternate_contact_number','alternate_email','gender','material_status','employee_type','degination','blood_group','joining_date','resume','account_number','net_salary','tds','pf_amount','pf_date','is_pf','esi_amount','esi_date','is_esi','aadhar_card_no','aadhar_name','pan_no','pan_name','official_no','previous_experience','esic_no','uan_no','timing_shift_in','timing_shift_out','bank_emp_name','emp_file_no','pl','cl','sl','anniversary_date');
		
		$userDetails['fname'] 		=	strtoupper($request->fname);
		$userDetails['mname'] 		=	strtoupper($request->mname);
		$userDetails['p_address'] 	=	strtoupper($request->p_address);
		$userDetails['c_address'] 	=	strtoupper($request->c_address);
		$userDetails['bank_name'] 	=	strtoupper($request->bank_name);
		$userDetails['ifsc_code'] 	=	strtoupper($request->ifsc_code);
		$userDetails['bank_branch'] 	=	strtoupper($request->bank_branch);		

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
                    $user->faculty_relations()->create($data);
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
                    $user->user_branches()->create($data);
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
						DB::table('faculty_subjects')->insert($faculty_subjects);
					}
				}
			}
		}

        if (Input::hasfile('resume')){
            $userDetails['resume'] = $this->uploadResume(Input::file('resume'));
        }              

        $user->user_details()->create($userDetails);

        // $last_id = $user->id;
        // $user->register_id = '#EMP000' . $last_id;

        if ($user->save()) {
            return redirect()->route('studiomanager.employees.index')->with('success', 'Employee Added Successfully');
        } else {
            return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
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

        return view('studiomanager.employee.view', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	//dk
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
		
        return view('studiomanager.employee.edit', compact('employee','subject_ids'));
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

        $inputs = $request->only('role_id','name','email','mobile','nominee_name','image','supervisor_id','department_type','total_time');
	
	$inputs['nickname'] 	= $request->nickname;
        $inputs['mobile'] = $request->contact_number;
		
		if(!empty($request->agreement)){
			$inputs['agreement'] 	= $request->agreement;
		}
		
		if(!empty($request->committed_hours)){
			$inputs['committed_hours'] 	= $request->committed_hours;
		}
		if(!empty($request->agreement_start_date) && !empty($request->agreement_end_date)){
			$inputs['agreement_start_date'] 	= $request->agreement_start_date;
			$inputs['agreement_end_date'] 	= $request->agreement_end_date;
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

        $userDetails = $request->only('user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds','pf_amount','pf_date','is_pf','esi_amount','esi_date','is_esi','aadhar_card_no','aadhar_name','pan_no','pan_name','official_no','previous_experience','esic_no','uan_no','timing_shift_in','timing_shift_out','bank_emp_name','emp_file_no','pl','cl','sl','anniversary_date');
 
		
        if (Input::hasfile('resume')){
            $this->RemoveResume($user->user_details->resume);
            $userDetails['resume'] = $this->uploadResume(Input::file('resume'));
        } 
		
        if (is_array($request->faculty) && !empty($request->faculty)) {
            FacultyRelation::where('user_id', $id)->delete();
			//DB::table('faculty_relations')->where('user_id', $id)->update(['is_deleted' => '1']);
			
			
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
		
		$branch_id = $request->branch_id;      
        if(isset($branch_id) && is_array($branch_id)){
			Userbranches::where('user_id', $id)->delete();
			//DB::table('userbranches')->where('user_id', $id)->update(['is_deleted' => '1']);
			
            foreach($branch_id as $key => $value){
                if(!empty($value)){                          
                    $data = array(                  
                        'branch_id'=>$value,               
                    );                    
                    $user->user_branches()->create($data);
                }
            }
        }
		
		if($request->role_id==2){
			if(isset($request->subject_id) && is_array($request->subject_id)){
				DB::table('faculty_subjects')->where('user_id', $user->id)->delete();
				//DB::table('faculty_subjects')->where('user_id', $id)->update(['is_deleted' => '1']);
				$subject_ids = $request->subject_id;
				foreach($subject_ids as $key => $value){
					if(!empty($value)){                          
						$faculty_subjects = array(                  
							'user_id'=>$user->id,
							'subject_id'=>$value                   
						);                    
						DB::table('faculty_subjects')->insert($faculty_subjects);
					}
				}
			}
		}

        if($user->user_details){
            $user->user_details()->update($userDetails);
        }else{
            $user->user_details()->create($userDetails); 
        }
        
        if($user->update($inputs)) {
			return redirect("studiomanager/employees?update=success&success=Employee Updated Successfully")->with('success', 'Employee Updated Successfully');
        } else {
            return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
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

        $user = User::with('user_details')->find($id);
        // if($user){
            // $this->RemoveProfile($user->image);
            // $this->RemoveResume($user->user_details->resume);
        // }

        // FacultyRelation::where('user_id', $user->id)->delete();
        // Userdetails::where('user_id', $user->id)->delete();
        $inputs = array('is_deleted' => '1', 'delete_date' => date('Y-m-d'));
        if ($user->update($inputs)) {
            return redirect()->back()->with('success', 'Employee Deleted Successfully');
        } else {
            return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function togglePublish($id) {
        $user = User::find($id);
        if (is_null($user)) {
            return redirect()->route('studiomanager.employees.index')->with('error', 'Employee not found');
        }
        try {
			
			if($user->status==1){
				$studio = Studio::where('assistant_id',$user->id)->get();
				if(count($studio) > 0){
					// return redirect()->route('studiomanager.employees.index')->with('error', 'Please remove this assistant from assigned studio!');
					return redirect("studiomanager/employees?update=error&error=Please remove this assistant from assigned studio!")->with('error', 'Please remove this assistant from assigned studio!');
				}
			}
			
			
					
            $user->update([
				'inactive_date' => NULL,
                'status'        => !$user->status,
                'updated_at'    => new \DateTime(),
            ]);
			if($user->status==0){
				Studio::where('assistant_id', $id)->update(['assistant_id'=>NULL]);
			}
			
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        // return redirect()->route('studiomanager.employees.index')->with('success', 'Status Updated Successfully.');
		return redirect("studiomanager/employees?update=success&success=Employee Updated Successfully")->with('success', 'Employee Updated Successfully');
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
        $employee = User::with('user_details','role','faculty_relations')->find($id);
		
		$employee_pending = Users_pending::with('user_details_pending','faculty_relations_pending')->where('user_id', $id)->first();
		// echo "<pre>"; print_r($employee_pending); die;
        return view('studiomanager.employee.approval', compact('employee','employee_pending'));
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
				return redirect()->route('studiomanager.employees.index')->with('success', 'Approval Updated Successfully');
			} else {
				return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
			}
		}
		else{
			$user = User::where('id', $id)->first();
			
			if($user->update($inputs)) {
				return redirect()->route('studiomanager.employees.index')->with('success', 'Rejected Updated Successfully');
			} else {
				return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
			}
		}
		
        
    }
	
	public function download_excel()
    {   
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		// $branch_id  = Input::get('branch_id');
		// $search     = Input::get('search');
        // $role_id    = Input::get('role_id');
		// $status     = Input::get('status');
		
		//$employees = User::with(['user_details','user_branches.branch','role','department'])->where('role_id','!=','1')->where('is_deleted', '0')->orderBy('id','desc');// dk
		//$employees->where('register_id','!=',NUll);
        $search = Input::get('search');
        $branch_id = Input::get('branch_id');
        $role_id = 2;
		$status = Input::get('status');
		$department_type = Input::get('department_type');

        // $employees = User::with(['user_details.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');
        $employees = User::with(['user_branches.branch','role'])->where('role_id','!=','1')->where('is_deleted', '0')->orderBy('name');// dk
		$employees->where('register_id','!=',NUll);
        if (!empty($search)) {
            $employees->where(function ($query) use ($search) {
                return $query
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                ->orWhere('register_id', 'LIKE', '%' . $search);
            });
        }

        if(!empty($branch_id)) {
            /*$employees->WhereHas('user_details', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });*/
			
			$employees->WhereHas('user_branches', function ($q) use ($branch_id) { // orWhereHas dk
                $q->where('branch_id', '=', $branch_id);
            });
        }

        if(!empty($role_id)){
            $employees->where('role_id',$role_id);
        }
		
		if(!empty($status)){
            if($status == 'Inactive'){
                $employees->where('status', '=', '0');
            }else{
                $employees->where('status', '=', '1');
            }
        }
		if(!empty($department_type)){
			$employees->where('department_type','=',$department_type);
		}

        if($logged_role_id == 21){
            $employees = $employees->whereRaw('supervisor_id LIKE  \'%"'.$logged_id.'"%\' ');
        }
		
        $employees = $employees->get();
		//echo "<pre>"; print_r($employees); die;
	
		
        if(count($employees) > 0){
            return Excel::download(new EmployeeExport($employees), 'EmployeeData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }  
	
	public function late_emp_list(Request $request){
		$logged_id      = Auth::user()->id;
		$logged_role_id = Auth::user()->role_id;
		$search = Input::get('search');
		
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
		}
		
		$time_shift_attendance_new = AttendanceNew::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		$time_shift_attendance_new->where('date', '=',date('Y-m-d'));
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance_new->whereIn('emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance_new->where('type', 'In');
		
		$time_shift_attendance_new = $time_shift_attendance_new->get();
		$time_shift_count = array();
	    
		if(count($time_shift_attendance_new) > 0){
			$time_shift_attendance_new_count = array();
			foreach($time_shift_attendance_new as $key1=>$time_shift_attendance_new_value){
				$emp_id  = $time_shift_attendance_new_value->emp_id;
				$in_time = $time_shift_attendance_new_value->time;
				$type    = $time_shift_attendance_new_value->type;
				
				$newTime = date("H:i",strtotime($in_time." -10 minutes"));
				//echo '<pre>'; print_r($newTime);die;
				$time_shift = User::with('user_details');
								if(!empty($emp_id) && !empty($newTime) && $type == 'In') {
									
									$time_shift->WhereHas('user_details', function ($q) use ($emp_id,$newTime) { 
										$q->where('user_id', '=', $emp_id);
										$q->where('timing_shift_in', '<', $newTime);
									});
								}
				$time_shift = $time_shift->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get()->count();
				
				if($time_shift){
					array_push($time_shift_attendance_new_count, $emp_id);
				}
				//echo '<pre>'; print_r($late_emp);die;
			}
			$time_shift_count = $time_shift_attendance_new_count;
		}
		else{
			$time_shift_attendance = Attendance::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
			$time_shift_attendance->where('date', '=',date('Y-m-d'));
			
			$timeShiftEmployeeArray = array();
			$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
			$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
			$time_shift_attendance->whereIn('emp_id', $timeShiftEmployeeArray);
			$time_shift_attendance->where('type', 'In');
			
			$time_shift_attendance = $time_shift_attendance->get();
			
			if(count($time_shift_attendance) > 0){
				$time_shift_attendance_count = array();
				foreach($time_shift_attendance as $key1=>$time_shift_attendance_value){
					$emp_id  = $time_shift_attendance_value->emp_id;
					$in_time = $time_shift_attendance_value->time;
					$type    = $time_shift_attendance_value->type;
					
					$newTime = date("H:i",strtotime($in_time." -10 minutes"));
					//echo '<pre>'; print_r($time_shift_attendance_value);die;
					$time_shift = User::with('user_details');
									if(!empty($emp_id) && !empty($newTime) && $type == 'In') {
										
										$time_shift->WhereHas('user_details', function ($q) use ($emp_id,$newTime) { 
											$q->where('user_id', '=', $emp_id);
											$q->where('timing_shift_in', '<', $newTime);
										});
									}
					$time_shift = $time_shift->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get()->count();
					
					if($time_shift){
						array_push($time_shift_attendance_count, $emp_id);
					}
					//echo '<pre>'; print_r($late_emp);die;
				}
				$time_shift_count = $time_shift_attendance_count;
			}
		}
		
		if(count($time_shift_count) > 0){
			$empArray   = array();
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
				$emp_details = $emp_details->where('id', $time_shift_count_value)->first();
				//echo '<pre>'; print_r($emp_details->name);die;
				if(!empty($emp_details)){
					$empArray[$key2]['id']         = !empty($emp_details->id) ? $emp_details->id : '';
                    $empArray[$key2]['register_id']       = !empty($emp_details->register_id) ? $emp_details->register_id : '';

					$empArray[$key2]['name']       = !empty($emp_details->name) ? $emp_details->name : '';
					$empArray[$key2]['email']      = !empty($emp_details->email) ? $emp_details->email : '';
					$empArray[$key2]['mobile']     = !empty($emp_details->mobile) ? $emp_details->mobile : '';
					$empArray[$key2]['department'] = !empty($emp_details->department->name) ? $emp_details->department->name : '';
                  
                    // intime
                    $emp_intime_attendance_new = AttendanceNew::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
                    $emp_intime_attendance_new->where('date','=',date('Y-m-d'));
                    $emp_intime_attendance_new->where('emp_id',$emp_details->id);
                    $emp_intime_attendance_new->where('type', 'In');
                    $emp_intime_attendance_new=$emp_intime_attendance_new->get();
                    foreach($emp_intime_attendance_new as $key1=>$emp_intime_attendance_new_value){
                        $emp_id =$emp_intime_attendance_new_value->emp_id;
                        $in_time=$emp_intime_attendance_new_value->time;
                        $empArray[$key2]['intime']= $in_time;
                    }
                    // intime	
				}	
			}	
		}
		//echo'<pre>'; print_r($empArray);die;
		return view('studiomanager.employee.late_employee_list', compact('empArray'));
		
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
			
			$history_data = array(                  
				'user_id'=>Auth::user()->id,
				'table_name'=>'users',
				'table_id'=>$request->p_id,
				'type'=>'user_inactive',
				'save_data'=>json_encode($input_data)
			);                    
			DB::table('all_history')->insert($history_data);
			
			

            return redirect()->route('studiomanager.employees.index')->with('success', 'Employee Updated Successfully');
        } else {
            return redirect()->route('studiomanager.employees.index')->with('error', 'Something Went Wrong !');
        }
	}
	
	public function late_employee_download_excel(){
		
		$logged_id      = Auth::user()->id;
		$logged_role_id = Auth::user()->role_id;
		$search = Input::get('search');
		
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
		}
		
		$time_shift_attendance_new = AttendanceNew::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
		$time_shift_attendance_new->where('date', '=',date('Y-m-d'));
		
		$timeShiftEmployeeArray = array();
		$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
		$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
		$time_shift_attendance_new->whereIn('emp_id', $timeShiftEmployeeArray);
		$time_shift_attendance_new->where('type', 'In');
		
		$time_shift_attendance_new = $time_shift_attendance_new->get();
		$time_shift_count = array();
	    
		if(count($time_shift_attendance_new) > 0){
			$time_shift_attendance_new_count = array();
			foreach($time_shift_attendance_new as $key1=>$time_shift_attendance_new_value){
				$emp_id  = $time_shift_attendance_new_value->emp_id;
				$in_time = $time_shift_attendance_new_value->time;
				$type    = $time_shift_attendance_new_value->type;
				
				$newTime = date("H:i",strtotime($in_time." -10 minutes"));
				//echo '<pre>'; print_r($newTime);die;
				$time_shift = User::with('user_details');
								if(!empty($emp_id) && !empty($newTime) && $type == 'In') {
									
									$time_shift->WhereHas('user_details', function ($q) use ($emp_id,$newTime) { 
										$q->where('user_id', '=', $emp_id);
										$q->where('timing_shift_in', '<', $newTime);
									});
								}
				$time_shift = $time_shift->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get()->count();
				
				if($time_shift){
					array_push($time_shift_attendance_new_count, $emp_id);
				}
				//echo '<pre>'; print_r($late_emp);die;
			}
			$time_shift_count = $time_shift_attendance_new_count;
		}
		else{
			$time_shift_attendance = Attendance::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		
			$time_shift_attendance->where('date', '=',date('Y-m-d'));
			
			$timeShiftEmployeeArray = array();
			$time_shift_usr           = $logged_id.','.implode(', ', array_map(function ($emp_data) { return $emp_data['id']; }, $userArray));
			$timeShiftEmployeeArray   = explode(',',$time_shift_usr);
			$time_shift_attendance->whereIn('emp_id', $timeShiftEmployeeArray);
			$time_shift_attendance->where('type', 'In');
			
			$time_shift_attendance = $time_shift_attendance->get();
			
			if(count($time_shift_attendance) > 0){
				$time_shift_attendance_count = array();
				foreach($time_shift_attendance as $key1=>$time_shift_attendance_value){
					$emp_id  = $time_shift_attendance_value->emp_id;
					$in_time = $time_shift_attendance_value->time;
					$type    = $time_shift_attendance_value->type;
					
					$newTime = date("H:i",strtotime($in_time." -10 minutes"));
					//echo '<pre>'; print_r($time_shift_attendance_value);die;
					$time_shift = User::with('user_details');
									if(!empty($emp_id) && !empty($newTime) && $type == 'In') {
										
										$time_shift->WhereHas('user_details', function ($q) use ($emp_id,$newTime) { 
											$q->where('user_id', '=', $emp_id);
											$q->where('timing_shift_in', '<', $newTime);
										});
									}
					$time_shift = $time_shift->where('status', 1)->where('role_id', '!=', 1)->orderBy('id','desc')->get()->count();
					
					if($time_shift){
						array_push($time_shift_attendance_count, $emp_id);
					}
					//echo '<pre>'; print_r($late_emp);die;
				}
				$time_shift_count = $time_shift_attendance_count;
			}
		}
		
		if(count($time_shift_count) > 0){
			$empArray   = array();
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
				$emp_details = $emp_details->where('id', $time_shift_count_value)->first();
				//echo '<pre>'; print_r($emp_details->name);die;
				if(!empty($emp_details)){
					$empArray[$key2]['id']         = !empty($emp_details->id) ? $emp_details->id : '';
                    $empArray[$key2]['register_id']       = !empty($emp_details->register_id) ? $emp_details->register_id : '';

					$empArray[$key2]['name']       = !empty($emp_details->name) ? $emp_details->name : '';
					$empArray[$key2]['email']      = !empty($emp_details->email) ? $emp_details->email : '';
					$empArray[$key2]['mobile']     = !empty($emp_details->mobile) ? $emp_details->mobile : '';
					$empArray[$key2]['department'] = !empty($emp_details->department->name) ? $emp_details->department->name : '';
                  
                    // intime
                    $emp_intime_attendance_new = AttendanceNew::groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
                    $emp_intime_attendance_new->where('date','=',date('Y-m-d'));
                    $emp_intime_attendance_new->where('emp_id',$emp_details->id);
                    $emp_intime_attendance_new->where('type', 'In');
                    $emp_intime_attendance_new=$emp_intime_attendance_new->get();
                    foreach($emp_intime_attendance_new as $key1=>$emp_intime_attendance_new_value){
                        $emp_id =$emp_intime_attendance_new_value->emp_id;
                        $in_time=$emp_intime_attendance_new_value->time;
                        $empArray[$key2]['intime']= $in_time;
                    }
                    // intime	
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
		//echo '<pre>'; print_r($request->j_date);die;
		if(!empty($request->j_date)){
			$join_date = $request->j_date; 
			$month = date('n',strtotime($join_date));
			$date = date('d',strtotime($join_date));
			if($date >= 15){
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
	
}
