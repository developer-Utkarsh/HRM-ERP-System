<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiNotification;
use Input;
use App\User;
use App\NewTask;
use Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$logged_id       = Auth::user()->id;
		$logged_role_id  = Auth::user()->role_id;
		$logid = array();
        // $users           = NewTask::getEmployeeByLogID($logged_id); 
		
        $title = Input::get('title');
        //$status = Input::get('status');

        $notifications = ApiNotification::orderBy('id', 'desc');
        if (!empty($title)){
            $notifications->where('title', 'LIKE', '%' . $title . '%');
        }
		
		
		if($logged_role_id == 20){
			$notifications->whereRaw("is_deleted = '0' and ( type !='Birthday' or type IS NULL or receiver_id LIKE '%$logged_id' )");
		}
		else{
			$notifications->whereRaw("is_deleted = '0' and ( type !='Birthday' or type IS NULL or receiver_id LIKE '%$logged_id' )");
			
			/* $logid[] = $logged_id;
			foreach($users as $usersvalue){
				$logid[] = $usersvalue['id'];
			}  */
			// $notifications->whereIn('sender_id', $logid);
		}
		
        $notifications = $notifications->get();

        return view('admin.notification.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$logged_id         = Auth::user()->id;
		$logged_role_id         = Auth::user()->role_id;
		$logged_department_type = Auth::user()->department_type;
		if($logged_role_id == 21){
			$employee = NewTask::getEmployeeForDepartmentHead($logged_id, $logged_role_id, $logged_department_type);
		}
        else{
			$employee = User::orderBy('id','desc')->get();
		} 
		//echo '<pre>'; print_r($users);die;
        return view('admin.notification.add', compact('employee'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
		$logged_id     = Auth::user()->id;
        $role_id       = $request->role_id;
        $branch_id     = $request->branch_id;
        $employee_id[]   = $request->employee_id;
        $degination    = $request->degination;


        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $inputs = $request->only('title','description','image','date');

        date_default_timezone_set('Asia/Kolkata');

        $current_date = date('Y-m-d');
        $current_time = date('H:m:i');
		$inputs['sender_id'] = $logged_id;
        $inputs['date'] = $current_date. ' ' .$current_time; 

        if (Input::hasfile('image')){
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        } 
		if(!empty($employee_id)){
			 $inputs['receiver_id'] = json_encode($employee_id);
		}		
		//echo '<pre>'; print_r($inputs);die; 
        $notification = ApiNotification::create($inputs);
        
        //$user = User::select('gsm_token')->where('role_id', '!=', '1')->get();

        $user = User::with(['user_details','user_branches']);

                if(!empty($role_id)){
                    $user = $user->where('role_id', '=', $role_id);
                }
                if(!empty($employee_id)){
                    $user = $user->where('id', $employee_id);
                }
                if(!empty($degination)){
                    $user->WhereHas('user_details', function ($q) use ($degination) {
                        $q->where('degination', $degination);
                    });
                }
                if(!empty($branch_id)){
                    $user->WhereHas('user_branches', function ($q) use ($branch_id) {
                            $q->where('branch_id', $branch_id);
                    });
                }
                $user = $user->where('role_id', '!=', 1)->get();
            //echo '<pre>'; print_r(count($user));die;
            //echo '<pre>'; print_r($user);die;
        $load = array();
        $load['title'] = $notification->title;
        $load['description'] = $notification->description;
        if(!empty($notification->image)){
            $load['image'] = asset('laravel/public/notification/'.$notification->image);
        }else{
            $load['image'] = asset('laravel/public/images/test-image.png');
        }
        $load['date'] = $notification->date;
		$load['status'] = NULL;
        $load['type'] = 'general';

        $token = [];

        if(count($user) > 0){
            foreach ($user as $key => $value) {
                if(!empty($value->gsm_token)){
                    $token[] = $value->gsm_token;
                }
            }
        }

        $this->android_notification($token, $load);    

        if ($notification->save()) {
            return redirect()->route('admin.notification.index')->with('success', 'Notification Added Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notification = ApiNotification::find($id);
        return view('admin.notification.edit', compact('notification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $notification = ApiNotification::where('id', $id)->first();

        $inputs = $request->only('title','description','image');

        if (Input::hasfile('image')){
            $this->RemoveNotificaton($notification->image);
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        }       

        if ($notification->update($inputs)) {
            return redirect()->route('admin.notification.index')->with('success', 'Notification Updated Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
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
        $notification = ApiNotification::find($id);
		$inputs = array('is_deleted' => '1');
        // if($notification->image){
            // $this->RemoveNotificaton($notification->image);
        // }

        if ($notification->update($inputs)) {
            return redirect()->back()->with('success', 'Notification Deleted Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function uploadImage($image){
        $drive = public_path(DIRECTORY_SEPARATOR . 'notification' . DIRECTORY_SEPARATOR);
        $extension = $image->getClientOriginalExtension();
        $imagename = uniqid() . '.' . $extension;    
        $newImage = $drive . $imagename;
        $imgResource = $image->move($drive, $imagename);
        return $imagename;

    }

    public function RemoveNotificaton($image) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'notification' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $image;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }
}
