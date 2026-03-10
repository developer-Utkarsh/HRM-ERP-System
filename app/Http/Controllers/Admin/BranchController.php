<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Userbranches;
use App\Studio;
use Input;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	
	//dk
    public function index()
    {
        $name = Input::get('name');
        $status = Input::get('status');

        $branches = Branch::orderBy('id', 'desc');

        if (!empty($name)){
            $branches->where('name', 'LIKE', '%' . $name . '%');
        }
        
        if(!empty($status)){
            if($status == 'Inactive'){
                $branches->where('status', '=', '0');
            }else{
                $branches->where('status', '=', '1');
            }
        }
		
		$branches->where('is_deleted', '=', '0');
        $branches = $branches->get();
		
		$branch_list = Branch::orderBy('id', 'asc')->get();

        return view('admin.branch.index', compact('branches','branch_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.branch.add');
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
            'name' => 'required|unique:branches|max:90',
            'address' => 'required|max:500',
        ]);

        $inputs = $request->only('name','status','address','related','nickname','show_in_web');
				
		switch($request->related){
			case 1	:	$bText = 'jodhpur';		break;
			case 2	:	$bText = 'jaipur';		break;
			case 3	:	$bText = 'delhi';		break;
			case 4	:	$bText = 'prayagraj';	break;
			case 5	:	$bText = 'indore';	break;
		}
		
		$inputs['branch_location'] = $bText;
				
		// print_R($inputs); die;
        $branch = Branch::create($inputs);    

        if ($branch->save()) {
            return redirect()->route('admin.branch.index')->with('success', 'Branch Added Successfully');
        } else {
            return redirect()->route('admin.branch.index')->with('error', 'Something Went Wrong !');
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
        $branch = Branch::find($id);
        return view('admin.branch.edit', compact('branch'));
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
            'name' => 'required|max:90|unique:branches,name,'.$id,
			'address' => 'required|max:500',
        ]);

        $branch = Branch::where('id', $id)->first();

        $inputs = $request->only('name','status','address','related','nickname','show_in_web');
		
		$imagespath = array_values(array_filter($request->imagespath));
		if (!empty($imagespath)) {
			$inputs['gallery'] = json_encode($imagespath);
		}

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $extension = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = time() . '.' . $extension;
            $image->move(public_path('branch_image'), $imageName);
            $baseUrl = 'https://hrm.utkarshupdates.com/';
            echo $fullPath = $baseUrl . 'laravel/public/branch_image/' . $imageName;
            $inputs['cover_image']=$fullPath;
        }
        //die();
		
		
        if ($branch->update($inputs)) {
            return redirect()->route('admin.branch.index')->with('success', 'Branch Updated Successfully');
        } else {
            return redirect()->route('admin.branch.index')->with('error', 'Something Went Wrong !');
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
        $branch = Branch::find($id);
		$inputs = array('is_deleted' => '1','status' => '0');
		
        // if ($branch->delete()) {
		if ($branch->update($inputs)) {
            return redirect()->back()->with('success', 'Branch Deleted Successfully');
        } else {
            return redirect()->route('admin.branch.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	//dk
	public function togglePublish($id) {
        $branch = Branch::find($id);
        if (is_null($branch)) {
            return redirect()->route('admin.branch.index')->with('error', 'Branch not found');
        }
        try {
			if($branch->status==1){
				$checkBranch = Userbranches::where('branch_id',$id)->first();
				if(!empty($checkBranch)){
					return redirect()->route('admin.branch.index')->with('error', 'Please change existing branch in employee first !');
				}
				
				$checkBranchStudio = Studio::where('branch_id',$id)->first();
				if(!empty($checkBranchStudio)){
					return redirect()->route('admin.branch.index')->with('error', 'Please change existing branch in studio first !');
				}
			}
			
			$branch->update([
				'status' => !$branch->status,
				'updated_at' => new \DateTime(),
			]);
			
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.branch.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function get_branchwise_studio(Request $request){

        $branch_id = $request->branch_id;

        $studio = Studio::where('branch_id', $branch_id)->where('status', 1)->get();
        
        if (!empty($studio)) {
			$res = "<option value=''> Select Studio </option>";
            foreach ($studio as $key => $value) {
                if(!empty($value->name) && !empty($value->name)){
                    $res .= "<option value='". $value->id ."'>" . $value->name ."</option>"; 
				}
           }
		   echo $res;
           exit();
		} 
		else{
			echo $res = "<option value=''> Studio Not Found </option>";
			die();
		}
	}
	
	public function get_branchwise_faculty(Request $request){

        $branch_id = $request->branch_id;

        if($request->faculty_id){
            $faculty_id = $request->faculty_id; 
        }else{
            $faculty_id = '';
        }

        $userdeatils = Userbranches::with([
            'user' => function($q){
                $q->where('role_id', '2')->where('status', '1');
            }
        ])->where('branch_id', $branch_id)->get();
        
        if (!empty($userdeatils)) {                         
            echo $res = "<option value=''> Select Faculty </option>";
            foreach ($userdeatils as $key => $value) {
                if(!empty($value->user->name) && !empty($value->user->name)){
                    if($value->user->id == $faculty_id){
                        echo $res = "<option value='". $value->user->id ."' selected='selected'>" . $value->user->name ."</option>";
                    }else{
                       echo $res = "<option value='". $value->user->id ."'>" . $value->user->name ."</option>"; 
					}
				}
			}
           exit();
		} 
		else
		{
			echo $res = "<option value=''> Faculty Not Found </option>";
			die();
		}
	}
	
	
	public function upload_image(Request $request){
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('branch_image'), $imageName);
           
			$baseUrl = 'https://hrm.utkarshupdates.com/';
			
			$fullPath = $baseUrl . 'laravel/public/branch_image/' . $imageName;

            return response()->json(['success' => 'Image uploaded successfully!', 'imagePath' => $fullPath]);
        }

        return response()->json(['error' => 'No file uploaded.']);
	}
}
