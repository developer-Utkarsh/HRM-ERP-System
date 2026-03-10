<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Holiday;
use Input;
use Auth;
use DB;
use Hash;


class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		// echo Hash::make('utkpw@2025');
        $title   = Input::get('title');
		$status  = Input::get('status');
		$type    = Input::get('type');
		
        $holiday = Holiday::orderBy('date', 'desc');

        if (!empty($title)){
            $holiday->where('title', 'LIKE', '%' . $title . '%');
        }
		
		if ($status != ''){
            $holiday->where('status', $status);
        }
		
		if ($type != ''){
            $holiday->where('type', $type);
        }

        $holiday = $holiday->where('is_deleted', '0')->get();

        return view('admin.holiday.index', compact('holiday'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.holiday.add');
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
            'title' => 'required',
			'date'  => 'required|unique:holidays',
			'type'  => 'required',
            'branch_id' => 'required',
        ]);

        $inputs = $request->only('title','date','status','type');  
		$inputs['user_id'] = $logged_id;  		
		$inputs['branch_id'] = json_encode($request->branch_id); 		
        
        $holiday = Holiday::create($inputs);    

        if ($holiday->save()) {
            return redirect()->route('admin.holiday.index')->with('success', 'Holiday Added Successfully');
        } else {
            return redirect()->route('admin.holiday.index')->with('error', 'Something Went Wrong !');
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
        $holiday = Holiday::find($id);
        return view('admin.holiday.edit', compact('holiday'));
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
			'type'  => 'required',
            'branch_id' => 'required',
        ]);

        $holiday = Holiday::where('id', $id)->first();

        $inputs = $request->only('title','date','status','type');  
		$inputs['user_id'] = $logged_id;     
        $inputs['branch_id'] = json_encode($request->branch_id); 		
        
        if ($holiday->update($inputs)) {
            return redirect()->route('admin.holiday.index')->with('success', 'Holiday Updated Successfully');
        } else {
            return redirect()->route('admin.holiday.index')->with('error', 'Something Went Wrong !');
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
		$inputs['is_deleted'] = '1';   
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
