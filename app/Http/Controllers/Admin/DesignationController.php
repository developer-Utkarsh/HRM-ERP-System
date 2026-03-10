<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Designation;
use Input;
use DB;
use Excel;
use App\Exports\DesignationExport;

class DesignationController extends Controller
{
    
    public function index()
    { 
        $name = Input::get('name');
        $status = Input::get('status');

        $designation = Designation::where('is_deleted', '0')->orderBy('id', 'desc');
        
        if (!empty($name)){
            $designation->where('name', 'LIKE', '%' . $name . '%');
        }
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            if($status == 'Inactive'){
                $designation->where('status', '=', '0');
            }else{
                $designation->where('status', '=', 'Active');
            }
        }

        $designation = $designation->get();

        return view('admin.designation.index', compact('designation'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.designation.add');
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
            'name' => 'required|unique:designations',
        ]);

        $inputs = $request->only('name','status');        

        $designation = Designation::create($inputs);    

        if ($designation->save()) {
            return redirect()->route('admin.designation.index')->with('success', 'Designation Added Successfully');
        } else {
            return redirect()->route('admin.designation.index')->with('error', 'Something Went Wrong !');
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
        $designation = Designation::find($id);
        return view('admin.designation.edit', compact('designation'));
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
            'name' => 'required|unique:designations,name,'.$id,
        ]);

        $designation = Designation::where('id', $id)->first();

        $inputs = $request->only('name','status');       

        if ($designation->update($inputs)) {
            return redirect()->route('admin.designation.index')->with('success', 'Designation Updated Successfully');
        } else {
            return redirect()->route('admin.designation.index')->with('error', 'Something Went Wrong !');
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
        $designation = Designation::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($designation->update($inputs)) {
            return redirect()->back()->with('success', 'Designation Deleted Successfully');
        } else {
            return redirect()->route('admin.designation.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function togglePublish($id) {
        $designation = Designation::find($id);
        if (is_null($designation)) {
            return redirect()->route('admin.designation.index')->with('error', 'Designation not found');
        }
		
		if($designation->status == 'Active'){
			$sts = '0';
		}
		else{
			$sts = '1';
		}
		
		$designation->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        // try {
            // $department->update([
                // 'status' => !$department->status,
                // 'updated_at' => new \DateTime(),
            // ]);
        // } catch (\PDOException $e) {
            // Log::error($this->getLogMsg($e));
            // return redirect()->back()->with('error', $this->getMessage($e));
        // }
        return redirect()->route('admin.designation.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function download_excel()
    {   
		$name   = Input::get('name');
        $status = Input::get('status');

        $designation = Designation::where('is_deleted', '0')->orderBy('id', 'desc');
        
        if (!empty($name)){
            $designation->where('name', 'LIKE', '%' . $name . '%');
        }
        
        if(!empty($status)){
            if($status == 'Inactive'){
                $designation->where('status', '=', '0');
            }else{
                $designation->where('status', '=', 'Active');
            }
        }

        $designation = $designation->get();
	
		//echo '<pre>'; print_r($designation);die;
        if(count($designation) > 0){
            return Excel::download(new DesignationExport($designation), 'DesignationData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    } 
}
