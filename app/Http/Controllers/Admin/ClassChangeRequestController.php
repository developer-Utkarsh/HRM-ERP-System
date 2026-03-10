<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use App\Studio;
use App\Timetable;
use App\TimeSlot;
use Input;

class ClassChangeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $typ                  = Input::get('typ');

        $conditions = array();

        // if (!empty($reschedule_faulty_id)){
        //     $conditions['id'] = $reschedule_faulty_id;
        // }
        if($typ == 1){
			$reschedule_faulty_id = Input::get('reschedule_faulty_id');
			$reschedule_status    = Input::get('reschedule_status');
			$fdate                = Input::get('fdate');
			$tdate                = Input::get('tdate');
				
			$studios1 = Studio::with([
            'timetable.reschedule'=>function ($q) use ($fdate,$tdate,$reschedule_status) {
                if(!empty($reschedule_status)){
                    $q->where('status', $reschedule_status);
                }
                if(!empty($fdate) && !empty($tdate)){
                    $q->where('created_at', '>=', $fdate);
                    $q->where('created_at', '<=', $tdate);
                }
            },
            'timetable'=>function ($q) use ($reschedule_faulty_id) {
                if(!empty($reschedule_faulty_id)){
                    $q->where('faculty_id', $reschedule_faulty_id);
                }
            },
            'timetable.faculty'
            ]);

            $studios1->WhereHas('timetable', function ($q) use ($reschedule_faulty_id) { 
                if(!empty($reschedule_faulty_id)){
                    $q->where('faculty_id', $reschedule_faulty_id);
                    //$q->whereNotNull('id');
                }
            });


            $studios1 = $studios1->orderBy('id','desc')->get();
            
        }else{
            $studios1 = Studio::with(['timetable.reschedule','timetable.faculty','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass'])->orderBy('id','desc')->get();
        } 
            
        if($typ == 2){
            $reschedule_faulty_id2 = Input::get('reschedule_faulty_id2');
            $reschedule_status2    = Input::get('reschedule_status2');
            $fdate2                = Input::get('fdate2');
            $tdate2                = Input::get('tdate2'); 
               
            $studios2 = Studio::with([
            'timetable.swap'=>function ($q) use ($fdate2,$tdate2,$reschedule_status2) {
                if(!empty($reschedule_status2)){
                    $q->where('status', $reschedule_status2);
                }
                if(!empty($fdate2) && !empty($tdate2)){
                    $q->where('created_at', '>=', $fdate2);
                    $q->where('created_at', '<=', $tdate2);
                }
            },
            'timetable'=>function ($q) use ($reschedule_faulty_id2) {
                if(!empty($reschedule_faulty_id2)){
                    $q->where('faculty_id', $reschedule_faulty_id2);
                }
            },
            'timetable.faculty','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass']);

            $studios2->WhereHas('timetable.faculty', function ($q) use ($reschedule_faulty_id2) { 
                if(!empty($reschedule_faulty_id2)){
                    $q->where('faculty_id', $reschedule_faulty_id2);
                    //$q->whereNotNull('id');
                }
            });


            $studios2 = $studios2->orderBy('id','desc')->get();
		}else{
            $studios2 = Studio::with(['timetable.reschedule','timetable.faculty','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass'])->orderBy('id','desc')->get();
        }

        if($typ == 3){
            $reschedule_faulty_id3 = Input::get('reschedule_faulty_id3');
            $reschedule_status3    = Input::get('reschedule_status3');
            $fdate3               = Input::get('fdate3');
            $tdate3                = Input::get('tdate3');
            
            $studios3 = Studio::with([
            'timetable.cancelclass'=>function ($q) use ($fdate3,$tdate3,$reschedule_status3) {
                if(!empty($reschedule_status3)){
                    $q->where('status', $reschedule_status3);
                }
                if(!empty($fdate3) && !empty($tdate3)){
                    $q->where('created_at', '>=', $fdate3);
                    $q->where('created_at', '<=', $tdate3);
                }
            },
            'timetable'=>function ($q) use ($reschedule_faulty_id3) {
                if(!empty($reschedule_faulty_id3)){
                    $q->where('faculty_id', $reschedule_faulty_id3);
                }
            },
            'timetable.faculty'
            ]);

            $studios3->WhereHas('timetable.faculty', function ($q) use ($reschedule_faulty_id3) { 
                if(!empty($reschedule_faulty_id3)){
                    $q->where('faculty_id', $reschedule_faulty_id3);
                    //$q->whereNotNull('id');
                }
            });


            $studios3 = $studios3->orderBy('id','desc')->get();
        }else{
            $studios3 = Studio::with(['timetable.reschedule','timetable.faculty','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass'])->orderBy('id','desc')->get();
        }

            
            
            //echo '<pre>'; print_r($studios);die;
        //$studios = Studio::with(['timetable.reschedule','timetable.faculty','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass'])->orderBy('id','desc')->get();

        //print_r($studios->toArray()); die;

        return view('admin.classchangerequest.index', compact('studios1','studios2','studios3'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function edit_reschedule($id)
    {
        $reschedule = Reschedule::where('id', $id)->first();
        return view('admin.classchangerequest.edit-reschedule', compact('reschedule'));
    }

    public function update_reschedule(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required',            
        ]);

        $reschedule = Reschedule::where('id', $id)->first();

        if($request->status == 'Approved'){

            $get_timetable_data = Timetable::where('id', $reschedule->timetable_id)->first();

            $minutes =  round(abs(strtotime($get_timetable_data->from_time) - strtotime($get_timetable_data->to_time)) / 60,2);

            $reschedule_from_time = strtotime($reschedule->from_time) + $minutes*60;

            $get_reschedule_to_time = date('H:i', $reschedule_from_time);
			
			$get_from_time_id = 0;
            $from_time_id = TimeSlot::where('time_slot',$reschedule->from_time)->first();
			if(!empty($from_time_id)){
				$get_from_time_id = $from_time_id->id;
			}
            
			$get_to_time_id = 0;
            $to_time_id = TimeSlot::where('time_slot',$get_reschedule_to_time)->first();
			if(!empty($to_time_id)){
				$get_to_time_id = $to_time_id->id;
			}
            

            $get_studio_timetable = Timetable::where('studio_id', $get_timetable_data->studio_id)->where('faculty_id', $get_timetable_data->faculty_id)->where('cdate', $get_timetable_data->cdate)->get();

            if(count($get_studio_timetable) > 0){

                $from_time2 = [];
                $to_time2 = [];

                foreach($get_studio_timetable as $value){
                    $from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)->first();
                    $from_time2[] = $from_time1->id;
                    $to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)->first();
                    $to_time2[] = $to_time1->id;
                }

                $chk_condition = 'false';          

                for($i=0;$i<count($from_time2);$i++)
                {
                    if($get_from_time_id > 0 && $get_from_time_id>=$from_time2[$i] && $get_from_time_id<=$to_time2[$i])
                    {
                        $chk_condition = 'true';
                    }else if($get_to_time_id > 0 && $get_to_time_id>=$from_time2[$i] && $get_to_time_id<=$to_time2[$i]){
                        $chk_condition = 'true';
                    }
                }

                if($chk_condition == 'true'){

                    $inputs = $request->only('from_time','faculty_reason','status');

                    $inputs['status'] = 'Reject';

                    $get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

                    if($get_faculty){
                        if($get_faculty->faculty->gsm_token){

                            $load = array();
                            $load['title'] = 'Error';
                            $load['admin_reason'] = 'Class Already Exists';
                            $load['status'] = 'Reject';
                            $load['type'] = 'faculty_reschedule';

                            $token = $get_faculty->faculty->gsm_token;

                            $this->android_notification($token, $load);
                        }   
                    }                    

                    if ($reschedule->update($inputs)) {
                        return redirect()->route('admin.classchangerequest.index')->with('error', 'Class Already Exists');
                    }
                }else{

                    $inputs = $request->only('from_time','faculty_reason', 'admin_reason','status');

                    $inputs['status'] = 'Approved';
                    $get_timetable_data->studio_id = $reschedule->studio_id;
                    $get_timetable_data->from_time = $reschedule->from_time;
                    $get_timetable_data->to_time   = $get_reschedule_to_time;
                    $get_timetable_data->update();                   

                    $get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

                    if($get_faculty){
                        if($get_faculty->faculty->gsm_token){

                            $load = array();
                            $load['title'] = 'Success';
                            $load['admin_reason'] = $request->admin_reason;
                            $load['status'] = 'Approved';
							$load['type'] = 'faculty_reschedule';

                            $token = $get_faculty->faculty->gsm_token;

                            $this->android_notification($token, $load);
                        }   
                    }                    

                    if ($reschedule->update($inputs)) {
                        return redirect()->route('admin.classchangerequest.index')->with('success', 'Reschedule Request Updated Successfully');
                    } else {
                        return redirect()->route('admin.classchangerequest.index')->with('error', 'Something Went Wrong !');
                    }
                }

            }else{
                return redirect()->back()->with('error', 'Studio Time Table Not Found');
            }
        }else{

            $inputs = $request->only('from_time','faculty_reason', 'admin_reason','status');

            $inputs['status'] = 'Reject';            

            $get_faculty = Timetable::with('faculty')->where('id', $reschedule->timetable_id)->first();

            if($get_faculty){
                if($get_faculty->faculty->gsm_token){

                    $load = array();
                    $load['title'] = 'Error';
                    $load['admin_reason'] = $request->admin_reason;
                    $load['status'] = 'Reject';
					$load['type'] = 'faculty_reschedule';

                    $token = $get_faculty->faculty->gsm_token;

                    $this->android_notification($token, $load);
                }   
            }           

            if ($reschedule->update($inputs)) {
                return redirect()->route('admin.classchangerequest.index')->with('success', 'Reschedule Request Updated Successfully');
            }

        }                
    }

    public function edit_swap($id)
    {
        $swap = Swap::where('id', $id)->first();
        return view('admin.classchangerequest.edit-swap', compact('swap'));
    }

    public function update_swap(Request $request, $id)
    {
		$validatedData = $request->validate([
			'status' => 'required',            
		]);

       $swap = Swap::where('id', $id)->first();
	   
		if(!empty($swap)){
			$inputs = $request->only('status');
			if($swap->status == $request->status){
				return redirect()->route('admin.classchangerequest.index')->with('error', 'No anything changed!');
			}
			else{
				if($request->status == 'Approved'){
					$get_timetable = Timetable::with('faculty')->where('id',$swap->timetable_id)->first();
					$get_swap_timetable = Timetable::with('faculty')->where('id',$swap->swap_timetable_id)->first();

					$get_timetable->update([
						'from_time' => $request->s_from_time,
						'to_time' => $request->s_to_time,
						'studio_id' => $request->s_studio_id,
					]);

					$get_swap_timetable->update([
						'from_time' => $request->c_from_time,
						'to_time' => $request->c_to_time,
						'studio_id' => $request->c_studio_id,
					]);        

					if($get_timetable){
						if($get_timetable->faculty->gsm_token){

							$load = array();
							$load['title'] = 'Success';
							$load['description'] = 'Class Swap Successfully';
							$load['status'] = 'Approved';
							$load['type'] = 'faculty_swap';

							$token = $get_timetable->faculty->gsm_token;

							$this->android_notification($token, $load);
						}   
					}

					if($get_swap_timetable){
						if($get_swap_timetable->faculty->gsm_token){

							$load = array();
							$load['title'] = 'Success';
							$load['description'] = 'Class Swap Successfully';
							$load['status'] = 'Approved';
							$load['type'] = 'faculty_swap';

							$token = $get_swap_timetable->faculty->gsm_token;
							
							$this->android_notification($token, $load);
						}   
					}        
				}
				else{

					$get_faculty = Timetable::with('faculty')->where('id', $swap->timetable_id)->first();

					if($get_faculty){
						if($get_faculty->faculty->gsm_token){

							$load = array();
							$load['title'] = 'Error';
							$load['description'] = 'Class Not Swap. Status has been changed '.$swap->status. " to ".$request->status;
							$load['status'] = $request->status;
							$load['type'] = 'faculty_swap';

							$token = $get_faculty->faculty->gsm_token;

							$this->android_notification($token, $load);
						}   
					}       
				}

				if ($swap->update($inputs)) {
					return redirect()->route('admin.classchangerequest.index')->with('success', 'Swap Request Updated Successfully');
				} else {
					return redirect()->route('admin.classchangerequest.index')->with('error', 'Something Went Wrong !');
				}
			}
		}
		else{
			return redirect()->route('admin.classchangerequest.index')->with('error', 'Something Went Wrong !');
		}
	}

public function edit_cancelclass($id)
{
    $cancelclass = CancelClass::where('id', $id)->first();
    return view('admin.classchangerequest.edit-cancelclass', compact('cancelclass'));
}

public function update_cancelclass(Request $request, $id)
{
   $validatedData = $request->validate([
    'status' => 'required',            
]);

   $inputs = $request->only('days','faculty_reason','admin_reason','status'); 

   $cancelclass = CancelClass::where('id', $id)->first();

   if($request->status == 'Approved'){

    $days = '+'.$cancelclass->days . 'day';

    $nxtday = date('Y-m-d', strtotime($days));

    $get_timetable_data = Timetable::with('faculty')->where('id', $cancelclass->timetable_id)->first();

    $from_time_id = TimeSlot::where('time_slot',$get_timetable_data->from_time)->first();
    $get_from_time_id = $from_time_id->id;
    $to_time_id = TimeSlot::where('time_slot',$get_timetable_data->to_time)->first();
    $get_to_time_id = $to_time_id->id;

    $get_studio_timetable = Timetable::where('studio_id', $get_timetable_data->studio_id)->where('faculty_id', $get_timetable_data->faculty_id)->where('cdate', $nxtday)->get();

    if(count($get_studio_timetable) > 0){

        $from_time2 = [];
        $to_time2 = [];

        foreach($get_studio_timetable as $value){
            $from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)->first();
            $from_time2[] = $from_time1->id;
            $to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)->first();
            $to_time2[] = $to_time1->id;
        }

        $chk_condition = 'false';          

        for($i=0;$i<count($from_time2);$i++)
        {
            if($get_from_time_id>=$from_time2[$i] && $get_from_time_id<=$to_time2[$i])
            {
                $chk_condition = 'true';
            }else if($get_to_time_id>=$from_time2[$i] && $get_to_time_id<=$to_time2[$i]){
                $chk_condition = 'true';
            }
        }

        if($chk_condition == 'true'){

            $inputs['status'] = 'Reject';

            if($get_timetable_data){
                if($get_timetable_data->faculty->gsm_token){

                    $load = array();
                    $load['title'] = 'Error';
                    $load['admin_reason'] = 'Class Already Exists';
                    $load['status'] = 'Reject';
					$load['type'] = 'faculty_cancel';

                    $token = $get_timetable_data->faculty->gsm_token;

                    $this->android_notification($token, $load);
                }   
            }

            $cancelclass->update($inputs);

            return redirect()->route('admin.classchangerequest.index')->with('error', 'Class Already Exists');
        }else{

         $get_timetable_data->cdate = $nxtday;
         $get_timetable_data->update();

         if($get_timetable_data){
            if($get_timetable_data->faculty->gsm_token){

                $load = array();
                $load['title'] = 'Success';
                $load['admin_reason'] = $request->admin_reason;
                $load['status'] = 'Approved';
				$load['type'] = 'faculty_cancel';

                $token = $get_timetable_data->faculty->gsm_token;

                $this->android_notification($token, $load);
            }   
        }

        if ($cancelclass->update($inputs)) {
            return redirect()->route('admin.classchangerequest.index')->with('success', 'Class Cancel Approved Successfully');
        } else {
            return redirect()->route('admin.classchangerequest.index')->with('error', 'Something Went Wrong !');
        }
    }
}else{

    $get_timetable_data->cdate = $nxtday;
    $get_timetable_data->update();

    if($get_timetable_data){
        if($get_timetable_data->faculty->gsm_token){

            $load = array();
            $load['title'] = 'Success';
            $load['admin_reason'] = $request->admin_reason;
            $load['status'] = 'Approved';
			$load['type'] = 'faculty_cancel';

            $token = $get_timetable_data->faculty->gsm_token;

            $this->android_notification($token, $load);
        }   
    }

    if($cancelclass->update($inputs)){
        return redirect()->route('admin.classchangerequest.index')->with('success', 'Class Cancel Approved Successfully');
    }else{
        return redirect()->route('admin.classchangerequest.index')->with('error', 'Class Cancel Not Approved');
    }    
}
}else{

    $inputs['status'] = 'Reject';

    $get_faculty = Timetable::with('faculty')->where('id', $cancelclass->timetable_id)->first();

    if($get_faculty){
        if($get_faculty->faculty->gsm_token){

            $load = array();
            $load['title'] = 'Error';
            $load['admin_reason'] = $request->admin_reason;
            $load['status'] = 'Reject';
			$load['type'] = 'faculty_cancel';

            $token = $get_faculty->faculty->gsm_token;

            $this->android_notification($token, $load);
        }   
    }

    if ($cancelclass->update($inputs)) {
        return redirect()->route('admin.classchangerequest.index')->with('success', 'Class Cancel Request Rejected');
    } else {
        return redirect()->route('admin.classchangerequest.index')->with('error', 'Something Went Wrong !');
    }
}

}

public function phpinfo()
    {
		echo phpinfo(); die;
		
	}

}
