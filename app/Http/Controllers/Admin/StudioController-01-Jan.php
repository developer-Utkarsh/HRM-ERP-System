<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use Input;

class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $name = Input::get('name');

        $studios = Studio::with('assistant', 'branch')->orderBy('id', 'desc');

        if (!empty($name)){
            $studios->where('name', 'LIKE', '%' . $name . '%');
        }

        if (!empty($branch_id)){
            $studios->where('branch_id', $branch_id);
        }

        if (!empty($assistant_id)){
            $studios->where('assistant_id', $assistant_id);
        }

        $studios = $studios->get();
        
        return view('admin.studio.index', compact('studios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.studio.add');
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
            'name' => 'required|max:150|unique:studios',
            'branch_id' => 'required',
            'assistant_id' => 'required',
            'floor' => 'required',
        ]);

        $studio = Studio::where('assistant_id', $request->assistant_id)->exists();

        if(!empty($studio)){

            return redirect()->back()->with('error', 'Assistant Already Exits In Another Studio!');

        }else{

            $inputs = $request->only('name','assistant_id','studio_slot','branch_id','floor','status');        

            $studio = Studio::create($inputs);    

            if ($studio->save()) {
                return redirect()->route('admin.studios.index')->with('success', 'Studio Added Successfully');
            } else {
                return redirect()->route('admin.studios.index')->with('error', 'Something Went Wrong !');
            }
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
        $studio = Studio::find($id);
        return view('admin.studio.edit', compact('studio'));
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
            'name' => 'required|max:150|unique:studios,name,'.$id,
			'branch_id' => 'required',
            //'assistant_id' => 'required',
			'floor' => 'required',
        ]);

        $studio = Studio::where('id', $id)->first();

        $inputs = $request->only('name','assistant_id','studio_slot','branch_id','floor','status');       

        if ($studio->update($inputs)) {
            return redirect()->route('admin.studios.index')->with('success', 'Studio Updated Successfully');
        } else {
            return redirect()->route('admin.studios.index')->with('error', 'Something Went Wrong !');
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
        $studio = Studio::find($id);

        if ($studio->delete()) {
            return redirect()->back()->with('success', 'Studio Deleted Successfully');
        } else {
            return redirect()->route('admin.studios.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function get_branchwise_assistant(Request $request){

        $branch_id = $request->branch_id;

        if($request->assistant_id){
            $assistant_id = $request->assistant_id; 
        }else{
            $assistant_id = '';
        }

        $userdeatils = Userbranches::with([
            'user' => function($q){
                $q->where('role_id', '3')->where('status', '1');
            }
        ])->where('branch_id', $branch_id)->get();
        
        if (!empty($userdeatils)) {                         
            echo $res = "<option value=''> Select Studio Assistant </option>";
            foreach ($userdeatils as $key => $value) {
                if(!empty($value->user->name) && !empty($value->user->name)){
                    if($value->user->id == $assistant_id){
                        echo $res = "<option value='". $value->user->id ."' selected='selected'>" . $value->user->name ."</option>";
                    }else{
                       echo $res = "<option value='". $value->user->id ."'>" . $value->user->name ."</option>"; 
                   }
               }
           }
           exit();
       } else {
        echo $res = "<option value=''> Assiatant Not Found </option>";
        die();
    }
}

public function getassistantexits(Request $request)
{
    $assistant_id = $request->assistant_id;

    $studio = Studio::where('assistant_id', $assistant_id)->exists();

    if(!empty($studio)){
        echo json_encode(['status' => true, 'data' => 'Assistant Already Exits In Another Studio']);
    }else{
        echo json_encode(['status' => false, 'data' => '']);
    }
}

	public function togglePublish($id) {
        $studio = Studio::find($id);
        if (is_null($studio)) {
            return redirect()->route('admin.studios.index')->with('error', 'Studio not found');
        }
        try {
			
			if($studio->status==1){
				if(!empty($studio->assistant_id)){
					return redirect()->route('admin.studios.index')->with('error', 'Please remove existing assistant from this studio !');
				}
			}
			
            $studio->update([
                'status' => !$studio->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.studios.index')->with('success', 'Status Updated Successfully.');
    }

}
