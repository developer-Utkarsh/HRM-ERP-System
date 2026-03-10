<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Appointment;
use App\AppointmentStatus;
use Input;
use DB;
use Excel;
use Auth;
use App\Exports\AppointmentExport;
use App\MeetingPlace;

class MeetingController extends Controller
{
    
    public function index()
    { 
        $title	= 	Input::get('title');
		$fdate	=	Input::get('fdate');
        $tdate	=  	Input::get('tdate');
		
		
		$whereCond = ' 1=1 ';
		
		if(Auth::user()->role_id != 29){
			$whereCond .= " AND (appointment.user_id =".Auth::user()->id." OR appointment_status.emp_id =".Auth::user()->id.")";
		}

        if(!empty($title)){
            $whereCond .= " AND appointment.title LIKE '%".$title."%'";
        }
		
		
		if (!empty($fdate) && !empty($tdate)) {  
			$whereCond .= " AND appointment.appointment_date >= '". $fdate."' AND appointment.appointment_date <= '". $tdate."'";
        }else{
			$whereCond .= " AND appointment.appointment_date = '".date('Y-m-d')."'";
		}
		
		
		
		// echo $whereCond;		

        $appointment_result = Appointment::select('appointment.*','u_name.name as user_name','meeting_place.name as meeting_place_name','appointment_status.emp_id','appointment_status.status as astatus')
		->leftJoin('meeting_place', 'appointment.meeting_place_id', '=', 'meeting_place.id')
		->leftJoin('users as u_name', 'appointment.user_id', '=', 'u_name.id')
		->leftJoin('appointment_status','appointment_status.appointment_id', 'appointment.id')
		->whereRaw($whereCond)
		->orderBy('appointment.id','desc')
		->groupby('appointment.id');

				
		$appointment_result = $appointment_result->get();
		
		
        return view('admin.meeting.index', compact('appointment_result'));
    }

    public function meeting_places(){
        $name 	= Input::get('name');
        $name 	= Input::get('name');
        $status = Input::get('status');

        $meeting_place = DB::table('meeting_place')->select('meeting_place.*','branches.branch_location')->leftJoin('branches','branches.id','meeting_place.branch')->where('meeting_place.is_deleted', '0')->orderBy('meeting_place.created_at', 'DESC');
        
        if (!empty($name)){
            $meeting_place->where('meeting_place.name', 'LIKE', '%' . $name . '%');
        }
        if(!empty($status)){
            $meeting_place->where('meeting_place.status', '=', $status);
        }
	

        $meeting_place = $meeting_place->get();
		
        return view('admin.meeting.meeting-places', compact('meeting_place'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$branch = DB::table('branches')->select('id','branch_location')->where('is_deleted','0')->groupby('branch_location')->get();
		
		return view('admin.meeting.meeting-places-add', compact('branch'));
       //
    }
	

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$validatedData = $request->validate([
            'branch' => 'required',
            'name' => 'required',
            'status' => 'required'
        ]);

        $inputs = $request->only('branch','name','status');        

        $meeting_place = MeetingPlace::create($inputs);    

        if ($meeting_place->save()) {
            return redirect()->route('admin.meeting-places')->with('success', 'Meeting Place Added Successfully');
        } else {
            return redirect()->route('admin.meeting-places')->with('error', 'Something Went Wrong !');
        }
        //
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
        $meeting_place = MeetingPlace::find($id);
        return view('admin.meeting.meeting-places-edit', compact('meeting_place'));
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
            'branch' => 'required',
            'name' => 'required',
            'status' => 'required'
        ]);

        $meeting_place = MeetingPlace::where('id', $id)->first();

        $inputs = $request->only('branch','name','status');       

        if ($meeting_place->update($inputs)) {
			return redirect()->back()->with('success', 'Meeting Place Updated Successfully');
        } else {
			return redirect()->back()->with('error', 'Something Went Wrong !');
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
        $meeting_place = MeetingPlace::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($meeting_place->update($inputs)) {
            return redirect()->back()->with('success', 'Meeting Place Deleted Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }

    public function get_appointment_status(Request $request){ 
        $appointment_id = $request->app_id;

        $appointmen_status_result = Appointment::select('u_name.name as user_name','u_name.register_id','appointment_status.status','appointment_status.remark')->leftJoin('appointment_status', 'appointment.id', '=', 'appointment_status.appointment_id')->leftJoin('users as u_name', 'appointment_status.emp_id', '=', 'u_name.id')->where('appointment_status.appointment_id', $appointment_id)->orderBy('u_name.name')->get();
       
        if (count($appointmen_status_result) > 0)
        {
            echo $res = '<div class="table-responsive">
                                <table class="table data-list-view">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Employee Code</th>
                                            <th>Status</th>
											<th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        foreach ($appointmen_status_result as $key => $value)
                        {
                            $sts = 'Pending';
                            if($value->status == '0'){ $sts = 'Pending'; }
                            if($value->status == '1'){ $sts = 'Approved'; }
                            if($value->status == '2'){ $sts = 'Reject'; }

                            echo $res = '<tr>
                                            <td class="product-category">'.$value->user_name.'</td>
                                            <td class="product-category">'.$value->register_id.'</td>
                                            <td class="product-category">'.$sts.'</td>
											<td class="product-category">'.$value->remark.'</td>
                                        </tr>';
                        }
                        echo $res = '</tbody>
                        </table>
                    </div>';
            exit();
        }
        else
        {
            echo $res = "<p> No Data Found </p>";
            exit();
        }
    }

    public function download_excel()
    {   
        $title = Input::get('title');
		$fdate	=	Input::get('fdate');
        $tdate	=  	Input::get('tdate');
		
        $whereCond = ' 1=1 ';

        if(!empty($title)){
            $whereCond .= " AND appointment.title LIKE '% . $title . %'";
        }
		
		if (!empty($fdate) && !empty($tdate)) {  
			$whereCond .= " AND appointment.appointment_date >= '". $fdate."' AND appointment.appointment_date <= '". $tdate."'";
        }else{
			$whereCond .= " AND appointment.appointment_date = '".date('Y-m-d')."'";
		}
		
		
		if(Auth::user()->role_id != 29){
			$whereCond .= " AND appointment.user_id = '".Auth::user()->id."'";
		}
		

        $get_data = Appointment::select('appointment.*','u_name.name as user_name','meeting_place.name as meeting_place_name','appointment_status.emp_id','appointment_status.status as astatus')->leftJoin('meeting_place', 'appointment.meeting_place_id', '=', 'meeting_place.id')->leftJoin('users as u_name', 'appointment.user_id', '=', 'u_name.id')->leftJoin('appointment_status','appointment_status.appointment_id', 'appointment.id')->whereRaw($whereCond)->orderBy('id','desc')->get();
        
        if(count($get_data) > 0){
            return Excel::download(new AppointmentExport($get_data), 'AppointmentData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    } 
	
	
	
	//Chetan
	public function meeting_store(){
		//Attendees
		$user_details = array();
		$app_arr = array();
		$array_key=0;
		
		$appointment_group = DB::table('appointment')
					->select('appointment.id','appointment.title as name','appointment.description as designation')
					->where([['is_group', '=', 1],['is_deleted', '=', '0']])
					->where('user_id', Auth::user()->id)
					->get();
		
		if(count($appointment_group)>0){
			foreach($appointment_group as $key => $value){
				$app_arr[$key]['id'] 			= $value->id;
				$app_arr[$key]['name']  		= $value->name;
				$app_arr[$key]['designation'] 	= '-';
				$app_arr[$key]['user_type'] 	= 'appointment_group';
				
				$array_key=$key;
			}
		}
			
			
			
		
		$user_result = DB::table('users')
					->select('users.id','users.name','userdetails.degination as designation')
					->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
					->where([['users.status', '=', '1'],['users.is_deleted', '=', '0']])
					->get();
		
		if(count($user_result) > 0){
			foreach($user_result as $key => $value){
				$array_key++;
				
				$app_arr[$array_key]['id'] 			= $value->id;
				$app_arr[$array_key]['name']  		= $value->name;
				$app_arr[$array_key]['designation'] 	= $value->designation;
				$app_arr[$array_key]['user_type'] 	= 'attendees';
				
				
			}
		}		
		
		
		// $user_details['user_details'] = $app_arr;
		
		// print_r($user_details); die();
		return view('admin.meeting.meeting-add', compact('app_arr'));
    }
	
	
	public function get_place(Request $request){
		$mplace = DB::table('meeting_place')->where('branch', $request->branch_id)->where('is_deleted', '0')->get();
		
		if (!empty($mplace))
        {
            echo $res = "<option value=''> Select place </option>";
            foreach ($mplace as $key => $value)
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
            echo $res = "<option value=''> Place Not Found </option>";
            die();
        }
	}
	
	public function add_meeting(Request $request){
		$validatedData = $request->validate([
            'title' => 'required',		
            'branch' => 'required',		
            'meeting_place_id' => 'required',		
            'emp_id' => 'required',		
            'meeting_type' => 'required',		
            'appointment_date' => 'required',		
            'start_time' => 'required',		
            'end_time' => 'required',		
        ]);
		
		
		if(!empty($request->title) && !empty($request->meeting_place_id) && !empty($request->emp_id) && !empty($request->appointment_date) && !empty($request->start_time) && !empty($request->end_time) && !empty($request->meeting_type)){
			
			$emp_id=implode(",",$request->emp_id);
		    /*
			$check_timetable = DB::table('appointment')
								->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
								->where('appointment_date', $request->appointment_date)
								->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment.meeting_place_id = '".$request->meeting_place_id."' OR (appointment_status.emp_id IN(".$emp_id."))))")
								->where('appointment.is_deleted','0')
								->get();
			*/
			
			if($request->is_forcefully_schedule!=1){
				$check_timetable = DB::table('appointment')
								->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
								->where('appointment_date', $request->appointment_date)
								->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment.meeting_place_id = '".$request->meeting_place_id."' OR (appointment_status.emp_id IN(".$emp_id."))))")
								->where('appointment.is_deleted','0')
								->where('appointment.meeting_place_id','!=', '0')
								->get();
			}else{
				$check_timetable = DB::table('appointment')
								->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
								->where('appointment_date', $request->appointment_date)
								->whereRaw("appointment.meeting_place_id = '".$request->meeting_place_id."'")
								->where('appointment.is_deleted','0')
								->where('appointment.meeting_place_id','!=', '0')
								->get();
			}
								
			if(count($check_timetable) > 0){
				$checkUser = DB::table('appointment')							
							->select('users.name')
							->leftjoin('appointment_status', 'appointment_status.appointment_id', 'appointment.id')
							->leftjoin('users', 'users.id', 'appointment_status.emp_id')
							->where('appointment_date', $request->appointment_date)
							->whereRaw("(((appointment.start_time>= '".$request->start_time."' && appointment.end_time<= '".$request->end_time."') OR (appointment.end_time>= '".$request->start_time."' && appointment.start_time <= '".$request->end_time."')) AND (appointment_status.emp_id IN (".$emp_id.")))")
							->get(); 
								
				if(count($checkUser) > 0){
					$newData	=	'';		
					foreach($checkUser as $cu){
						$newData .=	$cu->name.', ';
					}
				}else{
					$newData	=	'Meeting place already booked';
				}
				
				return redirect()->back()->with('error', 'Meeting Already Schedule ( '.$newData.' )');
			}else{			
				$appointment_input_arr = array(
					'user_id' 			=> Auth::user()->id,
					'title' 			=> $request->title,
					'is_group' 			=> isset($request->group) ? $request->group : '0',
					'description' 		=> $request->description,
					'meeting_place_id' 	=> $request->meeting_place_id,
					'appointment_date' 	=> $request->appointment_date,
					'start_time' 		=> $request->start_time,
					'end_time' 			=> $request->end_time,
					'type' 				=> $request->meeting_type,
					'url' 				=> $request->meeting_url,
					'branch_id'			=> $request->branch
				);

				$appointment_result = Appointment::insertGetId($appointment_input_arr);
				if($appointment_result){
					$emp_arr = $request->emp_id;					
					foreach($emp_arr as $value){
						$appointment_status_input_arr = array('appointment_id' => $appointment_result,'emp_id' => $value);
						AppointmentStatus::insert($appointment_status_input_arr);

					}
					
					
					//Notification Insert
					//Username 
					$get_name = User::where('id', Auth::user()->id)->first();
					
				
					$employee_id   = $request->emp_id;
					
					$current_date = date('Y-m-d');
					$current_time = date('H:i:s');
	
					$intData	=	array(
						"title"			=>	"Meeting update!!",
						"sender_id"		=>	Auth::user()->id,
						"date"			=>	$current_date. ' ' .$current_time,
						"description"	=>	"You are a participent in meeting schedued by ".$get_name->name." . Click to view details",
						"receiver_id"	=>	json_encode($employee_id),
						"type"			=>	'General',
					);
					
					 DB::table('api_notifications')->insert($intData);
					//End
					
					
					//Meeting Add Notification 
					$user = DB::table('users')->select('id','gsm_token','device_type')->whereIn('id', $request->emp_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
					$load = array();
					$load['title'] 		 =	"Meeting update!!";
					$load['description'] =	"You are a participent in meeting schedued by ".$get_name->name." . CLick to view details";
					$load['body'] 		 =	"You are a participent in meeting schedued by ".$get_name->name." . CLick to view details"; 
					$load['image'] 		 =	asset('laravel/public/images/test-image.png');
					$load['date'] 		 =	$request->appointment_date;
					$load['status'] 	 =	NULL;
					$load['type'] 		 =	'general';
			 
					$this->notificationDeviceWise($user, $load);
					//End
					
					
					return redirect()->back()->with('success', 'Meeting added Successfully');
				}
				else{
					return redirect()->back()->with('error', 'Something Went Wrong !');
				}
			}
		}
		else{
			 return redirect()->back()->with('error', 'Required field missing');
		}
	}
	
	public function update_appointment_status(Request $request){
		$appointment_id = $request->value;
		$status 		= $request->status;
		$user_id 		= Auth::user()->id;
	
		
		if(!empty($appointment_id) && !empty($status)){
			$check_appointment_date =  Appointment::where('id', $appointment_id)->first();
			
			if(!empty($check_appointment_date)){
				if($check_appointment_date->appointment_date >= date('Y-m-d')){
					$get_name = User::where('id',$user_id)->first();  //meeting creator name
					
					$check_appointment_status = AppointmentStatus::where('appointment_id', $appointment_id)->where('emp_id', $user_id)->first();
					
					if(!empty($check_appointment_status)){
						if($status == '0'){ $sts = 'Pending'; }
						if($status == '1'){ $sts = 'Accepted'; }
						if($status == '2'){ $sts = 'Reject'; }


						$appointment_res = $check_appointment_status->update(['status' => $status]);
						if($appointment_res){
							
							$employee_id   =  $check_appointment_date->user_id;   
							
							$current_date = date('Y-m-d');
							$current_time = date('H:i:s');
			
							$intData	=	array(
								"title"			=>	"Meeting update!!",
								"sender_id"		=>	$user_id,
								"date"			=>	$current_date. ' ' .$current_time,
								"description"	=>	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts,
								"receiver_id"	=>	json_encode($employee_id),
								"type"			=>	'General',
							);
							
							 DB::table('api_notifications')->insert($intData);
							
							
							$user = DB::table('users')->select('id','gsm_token','device_type')->where('id', $employee_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
							$load = array();
							$load['title'] 		 =	"Meeting status update!!";
							$load['description'] =	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts; 
							$load['body'] 		 =	"Your meeting status updated by ".$get_name->name." . and meeting status is ".$sts;
							$load['image'] 		 =	asset('laravel/public/images/test-image.png');
							$load['date'] 		 =	$current_date;
							$load['status'] 	 =	NULL;
							$load['type'] 		 =	'general';
					 
							$this->notificationDeviceWise($user, $load);
							//End
							return response(['status' => true, 'message' => 'Appointment Status Successfully '.$sts], 200);
						}
						else{
							return response(['status' => false, 'message' => 'Appointment Not Found.'], 200);
						}
						
					}
					else{
						return response(['status' => false, 'message' => 'Record Not Found.'], 200);
					}
				}
				else{
					return response(['status' => false, 'message' => 'Appointment Date Expire.'], 200);
				}
			}
		}
	}
	
	
	//Cancel Meeting
	public function cancel_appointment_status(Request $request){
		$appointment_id = $request->appointment_id;
		$status 		= $request->status;
		$cancel_reason 	= $request->cancel_reason;
		$user_id 		= Auth::user()->id;
	
		
		if(!empty($appointment_id) && !empty($cancel_reason)){
			$check_appointment =  Appointment::where('id', $appointment_id)->first();
			
			if(!empty($check_appointment)){				
				$get_name = User::where('id',$user_id)->first();  //meeting creator name
				
				$check_appointment_status = Appointment::where('id', $appointment_id)->update(['status' => 2,'cancel_reason' => $cancel_reason]);
			
			
				//Notification
				$employee_id   =  $check_appointment->user_id;   
				
				$current_date = date('Y-m-d');
				$current_time = date('H:i:s');

				$intData	=	array(
					"title"			=>	"Meeting update!!",
					"sender_id"		=>	$user_id,
					"date"			=>	$current_date. ' ' .$current_time,
					"description"	=>	"Your meeting (".$check_appointment->appointment_date.") cancelled by ".$get_name->name,
					"receiver_id"	=>	json_encode($employee_id),
					"type"			=>	'General',
				);
				
				 DB::table('api_notifications')->insert($intData);
				
				
				$user = DB::table('users')->select('id','gsm_token','device_type')->where('id', $employee_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
				$load = array();
				$load['title'] 		 =	"Meeting status update!!";
				$load['description'] =	"Your meeting (".$check_appointment->appointment_date.") cancelled by ".$get_name->name; 
				$load['body'] 		 =	"Your meeting (".$check_appointment->appointment_date.") cancelled by ".$get_name->name;
				$load['image'] 		 =	asset('laravel/public/images/test-image.png');
				$load['date'] 		 =	$current_date;
				$load['status'] 	 =	NULL;
				$load['type'] 		 =	'general';
		 
				$this->notificationDeviceWise($user, $load);
				
				return redirect()->back()->with('success', 'Appointment Successfully Cancelled');
				
			}else{
				return redirect()->back()->with('error', 'All Fields Are Required');
			}
		}
	}
	
	
	public function add_key_points(Request $request){
		
			$user_id 		= Auth::user()->id;
			if(!empty($request->appointment_id) && !empty($request->key_points) && !empty($user_id)){
				
				$key_points		=	$request->key_points;
				
				$task = array(
					'assign_id'	 => $user_id,
					'appointment_id'	 => $request->appointment_id,
					'date'		 => date('Y-m-d'),
					'emp_id'	 => $user_id,
					'title' 	 => 'Meeting Key Points',
					'plan' 	 	=> '00',
				);
				
				$insertID = DB::table('task_new')->insertGetId($task);	

				if(!empty($key_points)){
					for($j=0; $j<count($key_points); $j++) {
						$data = array(
							'task_id'	  => $insertID,						
							'description' => $key_points[$j],
						);
											
						DB::table('task_key_points')->insert($data);
					}
				}

					
				//Notification Insert
				$employee_id   = $user_id;
				
				$current_date = date('Y-m-d');
				$current_time = date('H:i:s');

				$intData	=	array(
					"title"			=>	"Meeting Task update!!",
					"sender_id"		=>	$user_id,
					"date"			=>	$current_date. ' ' .$current_time,
					"description"	=>	"Your meeting key point added in your task",
					"receiver_id"	=>	json_encode($employee_id),
					"type"			=>	'Appointment',
				);
				
				DB::table('api_notifications')->insert($intData);
				
				
				$user = DB::table('users')->select('id','gsm_token','device_type')->where('id', $user_id)->where('status', '=', 1)->where('is_deleted', '=', '0')->get();						
				$load = array();
				$load['title'] 		 =	"Meeting Task Update!!";
				$load['description'] =	"Your meeting key point added in your task"; //$notification->description;
				$load['body'] 		 =	"Your meeting key point added in your task"; //$notification->description;
				$load['image'] 		 =	asset('laravel/public/images/test-image.png');
				$load['date'] 		 =	$current_date;
				$load['status'] 	 =	NULL;
				$load['type'] 		 =	'Appointment';
		 
				$this->notificationDeviceWise($user, $load);																								
				//End
			
				
				return redirect()->back()->with('success', 'Appointment Key Points Successfully Added');
			}
			else{				
				return redirect()->back()->with('error', 'All Fields Are Required');
			}

	}
	
	public function meeting_history(Request $request, $id){
		
		$appointment_result = Appointment::select('appointment.*','u_name.name as user_name','meeting_place.name as meeting_place_name','appointment_status.emp_id','appointment_status.status as astatus','branches.name as branch_name')
		->leftJoin('meeting_place', 'appointment.meeting_place_id', '=', 'meeting_place.id')
		->leftJoin('users as u_name', 'appointment.user_id', '=', 'u_name.id')
		->leftJoin('appointment_status','appointment_status.appointment_id', 'appointment.id')
		->leftJoin('branches','branches.id', 'appointment.branch_id')
		->where('appointment.id', $id)
		->groupby('appointment.id');

				
		$appointment_result = $appointment_result->first();
		
		return view('admin.meeting.meeting-history', compact('appointment_result'));
	}
}
