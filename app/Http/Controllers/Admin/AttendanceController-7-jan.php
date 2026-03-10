<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use Input;
use Excel;
use App\Exports\AttendanceExport;
use Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$attendance = Attendance::with('user')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }
		
		$responseArray = array();
		$attendance = $attendance->get();
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = $valAtt->user->register_id;
				$responseArray[$key]['emp_id'] = $valAtt->user->id;
				$responseArray[$key]['name'] = $valAtt->user->name;
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] = $in_time;
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] = $in_time;
								$time_array[$ii]['out_time'] = "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] = $out_time;
								$ii++;
							}
						}
					}
				}
				$responseArray[$key]['time'] = $time_array;
			}
			// echo "<pre>"; print_r($responseArray); die;
		}
        return view('admin.attendance.index', compact('responseArray'));
    }
	
	public function create()
    {

        return view('admin.attendance.add');
    }
	
	public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emp_id' => 'required',
            'date' => 'required',
        ]);
		// print_r($request->date); die;
		if(!empty($request->emp_id)){
			$emp_id = $request->emp_id;
			$user = User::where('id', $emp_id)->first();
			if(!empty($user)){
				if($user->status=='1'){ 
					$inputs['emp_id'] = $emp_id;
					$inputs['date'] = $request->date;
					$i = 0;
					if (is_array($request->time) && !empty($request->time)) {
						$time = $request->time;
						$type = $request->type;
						foreach ($time as $key => $value) {
							if(!empty($value)){
								$data = array();
								$data['emp_id'] = $emp_id;
								$data['date'] = $request->date;
								$data['time'] = $value;
								$data['type'] = $type[$key];
								Attendance::create($data);
								$i++;
							}
						}
						
					}
					
					if($i > 0){
						return redirect()->route('admin.attendance.index')->with('success', 'Attendance Added Successfully');
					}
					else{
						return redirect()->back()->with('error', 'Attendance Empty');
					}
				}
				else{
					return redirect()->back()->with('error', 'User Not Active');
				}
				
			}
			else{
				return redirect()->back()->with('error', 'User Id Not Found');
			}
		}
		else{
			return redirect()->back()->with('error', 'User Id Not Found');
		}
    }


	public function edit($emp_id, $date)
    {
		$attendance = Attendance::with('user');
		if(!empty($emp_id)){
			$attendance->where('emp_id', $emp_id);
		}
		if(!empty($date)){
			$attendance->where('date', $date);
		}
		
		$attendance = $attendance->first();
		
		$attendance_data = array();
		if(!empty($attendance)){
				$attendance_data['date'] = $attendance->date;
				$attendance_data['emp_id'] = $attendance->user->id;
				$attendance_data['name'] = $attendance->user->name;
				$attendance_data['total_hours'] = "00";
				
				$all_time = array();
				$get_attendance = Attendance::where('emp_id', $attendance->emp_id)->where('date', $attendance->date)->orderBy('id', 'asc')->get();
				if(count($get_attendance) > 0){
					$all_time = $get_attendance;
				}
				$attendance_data['time'] = $all_time;
		}
		
		// echo "<pre>"; print_r($attendance_data); die;
		
        return view('admin.attendance.edit', compact('attendance_data'));
    }
	
	public function update(Request $request, $id)
    {
		// echo "<pre>"; print_r($request->time); die;
		
		if(is_array($request->time) && !empty($request->time))
		{
			$i = 0; 
			if (is_array($request->time) && !empty($request->time)) {
				$att_id = $request->att_id;
				$time = $request->time;
				$type = $request->type;
				foreach ($att_id as $key => $value) {
					// print_r($value); die;
					if(isset($time[$key])){
						$attendance_details = Attendance::find($key);
						$data = array();
						$data['time'] = $time[$key];
						$data['type'] = $type[$key];
						$attendance_details->update($data);
						$i++;
					}
					else{
						Attendance::where('id', $value)->delete();
					}
				}
			}
			
			if($i > 0){				
				return redirect()->route("admin.attendance.index")->with('success', 'Attendance Updated Successfully');
			}
			else{
				return redirect()->route("admin.attendance.index")->with('error', 'Something Went Wrong !');
			}
			
			
			
		}
		else {
            return redirect()->route("admin.task.index")->with('error', 'Something Went Wrong !');
        }
    }

	
	//work gallery for
	public function show()
    {
        $name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$attendance = Attendance::with('user')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }
		
		$responseArray = array();
		$attendance = $attendance->get();
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['emp_id'] = $valAtt->user->id;
				$responseArray[$key]['name'] = $valAtt->user->name;
				$responseArray[$key]['total_hours'] = "00";
				
				$all_time = array();
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				if(count($get_attendance) > 0){
					$all_time = $get_attendance;
				}
				$responseArray[$key]['time'] = $all_time;
			}
			
		}
		// echo "<pre>"; print_r($responseArray); die;
        return view('admin.attendance.gallery', compact('responseArray'));
    }
	
    public function gallery()
    {
        
    }
    
	public function download_excel()
    {
		$name = Input::get('name');
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		$attendance = Attendance::with('user')->groupBy('date')->orderBy('date', 'desc')->groupBy('emp_id');
		if(!empty($fdate) && !empty($tdate)){
			$attendance->where('date', '>=', $fdate);
			$attendance->where('date', '<=', $tdate);
		}
		if (!empty($name)){
			$attendance->WhereHas('user', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
                $q->orWhere('register_id', 'LIKE', '%' . $name);
            });
        }
		
		$responseArray = array();
		$attendance = $attendance->get();
		if(count($attendance) > 0){
			foreach($attendance as $key=>$valAtt){
				$responseArray[$key]['date'] = $valAtt->date;
				$responseArray[$key]['register_id'] = $valAtt->user->register_id;
				$responseArray[$key]['emp_id'] = $valAtt->user->id;
				$responseArray[$key]['name'] = $valAtt->user->name;
				$responseArray[$key]['total_hours'] = "00";
				
				$get_attendance = Attendance::where('emp_id', $valAtt->emp_id)->where('date', $valAtt->date)->orderBy('id', 'asc')->get();
				$time_array = array();
				$ii=0;
				if(count($get_attendance) > 0){
					foreach($get_attendance as $key1 => $AttendanceDetail){
						$in_time = "";
						$out_time = "";
						if(empty($time_array[$ii]['in_time'])){
							$time_array[$ii]['in_time'] = "";
						}
						if(empty($time_array[$ii]['out_time'])){
							$time_array[$ii]['out_time'] = "";
						}
						
						if($AttendanceDetail->type=="In"){
							$in_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['in_time'])){
								$time_array[$ii]['in_time'] = $in_time;
							}
							else{
								$ii++;
								$time_array[$ii]['in_time'] = $in_time;
								$time_array[$ii]['out_time'] = "";
							}
						}
						else if($AttendanceDetail->type=="Out"){
							$out_time = $AttendanceDetail->time;
							if(empty($time_array[$ii]['out_time'])){
								$time_array[$ii]['out_time'] = $out_time;
								$ii++;
							}
						}
					}
				}
				$responseArray[$key]['time'] = $time_array;
			}
			// echo "<pre>"; print_r($responseArray); die;
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new AttendanceExport($responseArray), 'AttendanceData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }	

}
