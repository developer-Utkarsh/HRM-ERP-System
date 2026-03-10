<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Location;
use Input;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = Input::get('name');
        $status = Input::get('status');

        $location = Location::orderBy('id', 'asc');

        if (!empty($name)){
            $location->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $location->where('status', '=', '0');
            }else{
                $location->where('status', '=', '1');
            }
        }

        $location = $location->where('is_deleted', '0')->get();

        return view('admin.location.index', compact('location'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.location.add');
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
            'name' => 'required|unique:locations',
        ]);

        $inputs = $request->only('name','status');        

        $location = Location::create($inputs);    

        if ($location->save()) {
            return redirect()->route('admin.location.index')->with('success', 'Location Added Successfully');
        } else {
            return redirect()->route('admin.location.index')->with('error', 'Something Went Wrong !');
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
        $location = Location::find($id);
        return view('admin.location.edit', compact('location'));
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
            'name' => 'required|unique:locations,name,'.$id,
        ]);

        $location = Location::where('id', $id)->first();

        $inputs = $request->only('name','status');       

        if ($location->update($inputs)) {
            return redirect()->route('admin.location.index')->with('success', 'Location Updated Successfully');
        } else {
            return redirect()->route('admin.location.index')->with('error', 'Something Went Wrong !');
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
        $location = Location::where('id',$id);
		$inputs = array('is_deleted' => '1');

        if ($location->update($inputs)) {
            return redirect()->back()->with('success', 'Location Deleted Successfully');
        } else {
            return redirect()->route('admin.location.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function togglePublish($id) {
        $location = Location::find($id);
        if (is_null($location)) {
            return redirect()->route('admin.location.index')->with('error', 'Location not found');
        }
        try {
            $location->update([
                'status' => !$location->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.location.index')->with('success', 'Status Updated Successfully.');
    }
}
