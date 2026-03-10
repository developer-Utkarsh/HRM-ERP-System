<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use Auth;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use App\Timetable;

class ClassChangeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $studios = Studio::with(['timetable.reschedule','timetable.swap.s_timetable','timetable.swap.faculty','timetable.swap.swap_timetable','timetable.cancelclass'])->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

        //print_r($studios->toArray()); die;      
        
        return view('studiomanager.classchangerequest.index', compact('studios'));
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
        return view('studiomanager.classchangerequest.edit-reschedule', compact('reschedule'));
    }

    public function update_reschedule(Request $request, $id)
    {
         $validatedData = $request->validate([
            'status' => 'required',            
        ]);

        $reschedule = Reschedule::where('id', $id)->first();

        $inputs = $request->only('to_time','faculty_reason', 'admin_reason','status');       

        if ($reschedule->update($inputs)) {
            return redirect()->route('studiomanager.classchangerequest.index')->with('success', 'Reschedule Request Updated Successfully');
        } else {
            return redirect()->route('studiomanager.classchangerequest.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function edit_swap($id)
    {
        $swap = Swap::where('id', $id)->first();
        return view('studiomanager.classchangerequest.edit-swap', compact('swap'));
    }

    public function update_swap(Request $request, $id)
    {
         $validatedData = $request->validate([
            'status' => 'required',            
        ]);

        $swap = Swap::where('id', $id)->first();

        if($request->status == 'Approved'){
            $get_timetable = Timetable::where('id',$swap->timetable_id)->first();
            $get_swap_timetable = Timetable::where('id',$swap->swap_timetable_id)->first();

            $get_timetable->update([
                'from_time' => $request->s_from_time,
                'to_time' => $request->s_to_time,
            ]);
            $get_swap_timetable->update([
                'from_time' => $request->c_from_time,
                'to_time' => $request->c_to_time,
            ]);                        
        }        

        $inputs = $request->only('status');       

        if ($swap->update($inputs)) {
            return redirect()->route('studiomanager.classchangerequest.index')->with('success', 'Swap Request Updated Successfully');
        } else {
            return redirect()->route('studiomanager.classchangerequest.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function edit_cancelclass($id)
    {
        $cancelclass = CancelClass::where('id', $id)->first();
        return view('studiomanager.classchangerequest.edit-cancelclass', compact('cancelclass'));
    }

    public function update_cancelclass(Request $request, $id)
    {
         $validatedData = $request->validate([
            'status' => 'required',            
        ]);

        $cancelclass = CancelClass::where('id', $id)->first();

        $inputs = $request->only('days','faculty_reason','admin_reason','status');       

        if ($cancelclass->update($inputs)) {
            return redirect()->route('studiomanager.classchangerequest.index')->with('success', 'Class Cancel Updated Successfully');
        } else {
            return redirect()->route('studiomanager.classchangerequest.index')->with('error', 'Something Went Wrong !');
        }
    }
}
