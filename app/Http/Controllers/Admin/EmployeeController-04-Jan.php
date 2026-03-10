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

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		session_start();
		
		if(!empty($_SESSION["redirect_url"]) && isset($_GET['update'])){
			return redirect("admin/employees".$_SESSION['redirect_url'])->with($_GET['update'],$_GET[$_GET['update']]);
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
        $role_id = Input::get('role_id');
		$status = Input::get('status');

        // $employees = User::with(['user_details.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');
        $employees = User::with(['user_branches.branch','role'])->where('role_id','!=','1')->orderBy('id','desc');// dk
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
		
        $employees = $employees->get();
        
		// print_r($employees); die;
        return view('admin.employee.index', compact('employees'));
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
            'name' => 'required|max:100|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|max:150|unique:users',
            'contact_number' => 'required|numeric|digits:10|unique:users,mobile',            
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5120',     // 5MB Validation  //1024 = 1MB           
            'resume' => 'mimes:pdf,doc,docx,jpeg,png,jpg|max:5120', // 5MB Validation  //1024 = 1MB           
            'branch_id' => 'required',            
            'alternate_contact_number' => 'nullable|numeric|digits:10',            
            'account_number' => 'required|numeric|digits_between:0,20',            
            'bank_name' => 'required|max:100',            
            'ifsc_code' => 'required|max:50',            
            'bank_branch' => 'required|max:150',            
            'net_salary' => 'required|numeric|digits_between:0,10',            
            'tds' => 'required|numeric|digits_between:0,10',
			'fname' => 'nullable|max:100|regex:/^[\pL\s\-]+$/u', 
			'mname' => 'nullable|max:100|regex:/^[\pL\s\-]+$/u', 
			'gender' => 'required',   
			'c_address' => 'required|max:1000',   
			'p_address' => 'required|max:1000',   
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

        $inputs = $request->only('role_id','register_id','name','email','mobile','image','password','supervisor_id');
		if(!empty($request->supervisor_id)){
			$inputs['supervisor_id'] = json_encode($request->supervisor_id);
		}
		$inputs['mobile'] = $request->contact_number;
        $inputs['password'] = Hash::make('123456');
		// Remove branch_id dk
        $userDetails = $request->only('user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds');
		
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

        $last_id = $user->id;
        $user->register_id = '#EMP000' . $last_id;

        if ($user->save()) {
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
		
        return view('admin.employee.edit', compact('employee','subject_ids'));
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
            'name' => 'required|max:100|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|max:150|unique:users,email,'.$id,
            'contact_number' => 'required|numeric|digits:10|unique:users,mobile,'.$id,           
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5120',      // 5MB Validation  //1024 = 1MB          
            'resume' => 'mimes:pdf,doc,docx,jpeg,png,jpg|max:5120', // 5MB Validation  //1024 = 1MB           
            'branch_id' => 'required',             
            'alternate_contact_number' => 'nullable|numeric|digits:10',            
            'account_number' => 'required|numeric|digits_between:0,20',            
            'bank_name' => 'required|max:100',            
            'ifsc_code' => 'required|max:50',            
            'bank_branch' => 'required|max:150',            
            'net_salary' => 'required|numeric|digits_between:0,10',            
            'tds' => "required|numeric|digits_between:0,10|max:$max_tds", 
            'fname' => 'nullable|max:100|regex:/^[\pL\s\-]+$/u', 
			'mname' => 'nullable|max:100|regex:/^[\pL\s\-]+$/u', 
			'gender' => 'required',   
			'c_address' => 'required|max:1000',   
			'p_address' => 'required|max:1000',   
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

        $inputs = $request->only('role_id','name','email','mobile','image','supervisor_id');

        $inputs['mobile'] = $request->contact_number;
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

        $userDetails = $request->only('user_id','dob','fname','mname','alternate_contact_number','alternate_email','gender','material_status','p_address','c_address','employee_type','degination','blood_group','joining_date','resume','account_number','bank_name','ifsc_code','bank_branch','net_salary','tds');
 
		
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
                    $user->faculty_relations()->create($data);
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
                    $user->user_branches()->create($data);
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
			return redirect("admin/employees?update=success&success=Employee Updated Successfully")->with('success', 'Employee Updated Successfully');
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

        $user = User::with('user_details')->find($id);
        if($user){
            $this->RemoveProfile($user->image);
            $this->RemoveResume($user->user_details->resume);
        }

        FacultyRelation::where('user_id', $user->id)->delete();
        Userdetails::where('user_id', $user->id)->delete();

        if ($user->delete()) {
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
                'status' => !$user->status,
                'updated_at' => new \DateTime(),
            ]);
			if($user->status==0){
				Studio::where('assistant_id', $id)->update(['assistant_id'=>NULL]);
			}
			
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
        $employee = User::with('user_details','role','faculty_relations')->find($id);
		
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
}
