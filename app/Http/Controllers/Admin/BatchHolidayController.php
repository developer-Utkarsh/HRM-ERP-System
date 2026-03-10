<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Holiday;
use Input;
use Auth;
use DB;
use Hash;


class BatchHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title   = Input::get('title');
		$status  = Input::get('status');
		$type    = Input::get('type');
		
        $holiday =	DB::table('batch_holidays')
					->select('batch_holidays.*','course.name as cname')
					->leftjoin('course','course.id','batch_holidays.course_id')
					->orderby('id','desc');

        if (!empty($title)){
            $holiday->where('batch_holidays.title', 'LIKE', '%' . $title . '%');
        }
		
		if ($status != ''){
            $holiday->where('batch_holidays.status', $status);
        }
		
		if ($type != ''){
            $holiday->where('batch_holidays.type', $type);
        } 

        $holiday = $holiday->where('batch_holidays.status',"!=",3)->get();

        return view('admin.batch_holiday.index', compact('holiday'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.batch_holiday.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$logged_id       = Auth::user()->id;
		
        $validatedData = $request->validate([
            'branch_location' => 'required',
            'title' => 'required',
			'date'  => 'required|unique:holidays',
			'type'  => 'required',
        ]);

        $request->batch_id=$request->batch_id??[];

		$inputs = array(
			"user_id"	=>	$logged_id,
			"title"		=>	$request->title,
			"date"		=>	$request->date,
			"status"	=>	$request->status,
			"course_id"	=>	$request->course_id??0,
			"location"	=>	$request->branch_location,
			"batch_id"	=>	json_encode($request->batch_id),
		);	
		
		$last_id = DB::table('batch_holidays')->insertGetId($inputs);    

        if (!empty($last_id)) {
            return redirect()->route('admin.batch_holiday.index')->with('success', 'Batch Holiday Added Successfully');
        } else {
            return redirect()->route('admin.batch_holiday.index')->with('error', 'Something Went Wrong !');
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
        $holiday = DB::table('batch_holidays')->where('id',$id)->first();
        return view('admin.batch_holiday.edit', compact('holiday'));
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
		$logged_id       = Auth::user()->id;
        $validatedData = $request->validate([
			'title' => 'required|unique:holidays,title,'.$id,
			'date'  => 'required|unique:holidays,date,'.$id,
        ]);
		
		if(!empty($id)){
			$inputs = array(
				"user_id"	=>	$logged_id,
				"title"		=>	$request->title,
				"date"		=>	$request->date,
				"status"	=>	$request->status,
				"course_id"	=>	$request->course_id,
				"location"	=>	$request->branch_location,
				"batch_id"	=>	json_encode($request->batch_id),
			);	
			
			
			DB::table('batch_holidays')->where('id',$id)->update($inputs);    	
			
            return redirect()->route('admin.batch_holiday.index')->with('success', 'Batch Holiday Updated Successfully');
        } else {
            return redirect()->route('admin.batch_holiday.index')->with('error', 'Something Went Wrong !');
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
        $holiday = Holiday::where('id', $id)->first();
		$inputs['status'] = '3';   
        if ($holiday->update($inputs)) {
            return redirect()->back()->with('success', 'Holiday Deleted Successfully');
        } else {
            return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function get_multi_locationwise_branch(Request $request){

		$b_location = $request->b_location;
		$get_branches = DB::table('branches')
			->where('status', 1)
			->where('is_deleted', '0')
			->whereIn('branch_location', $b_location)
			->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')
			->get();			
		$res = "";
        if (!empty($get_branches)) {  
				$res .= "<option value=''> Select Any </option>";
				foreach ($get_branches as $key => $value) {
					$res .= "<option value='". $value->id ."' >" . $value->name ."</option>";
				}
			} 
			else {
				$res .= "<option value=''> Not Found </option>";
			}
		
		echo $res; exit;
	}
	
}
