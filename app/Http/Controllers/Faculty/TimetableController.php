<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use App\TimeSlot;
use App\Subject;
use App\Chapter;
use App\Topic;
use DB;
use App\Studio;
use Input;
use Auth;
use App\User;
use App\Reschedule;
use App\CancelClass;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

     $user = User::with('user_details')->where('id', Auth::user()->id)->first();     

     $fdate = Input::get('fdate');

     if(!empty($fdate)){
        $get_date = $fdate;
    }else{
        $get_date = date('Y-m-d');
    } 

    $timeslots = TimeSlot::get();

    $get_studios = array();

    if($user->user_details){

        $get_studios = Studio::with([
            'assistant',
            'timetable' => function($q) use ($get_date){
                if(!empty($get_date)){
                    $q->Where('cdate', $get_date)->where('faculty_id', Auth::user()->id)->orderBy('from_time', 'asc');
                }else{
                    $q->where('faculty_id', Auth::user()->id)->orderBy('from_time', 'asc');
                }                    
            },
            'timetable.topic',
        ])->where('branch_id', $user->user_details->branch_id)->orderBy('id', 'desc');            

        $get_studios = $get_studios->get();

    }else{
        echo 'Branch Id Not Found'; die;
    }

    //print_r($get_studios->toArray()); die;        

    return view('faculty.timetable.index', compact('timeslots' , 'get_studios'));
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

    public function reschedule_store(Request $request)
    {
        $inputs = $request->only('timetable_id','from_time','to_time','faculty_reason');            

        $reschedule = Reschedule::create($inputs);           

        if($reschedule->save()){
            return response(['status' => true, 'message' => 'Reschedule Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function cancelclass_store(Request $request)
    {
        $inputs = $request->only('timetable_id','days','faculty_reason');            

        $cancelclass = CancelClass::create($inputs);           

        if($cancelclass->save()){
            return response(['status' => true, 'message' => 'Cancel Class Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }
}
