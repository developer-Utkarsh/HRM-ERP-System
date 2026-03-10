<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TrainingVideoCategory;
use App\TrainingVideo;
use App\User;
use Input;
use DB;
use Excel;
use App\Exports\TrainingVideoExport;

class TrainingVideoController extends Controller
{
    
    public function index()
    { 
        $title = Input::get('title');
        $status = Input::get('status');

        $training_video = TrainingVideo::select('training_video.*','users.name as user_name','training_video_category.name as cat_name')->leftJoin('users','training_video.user_id', '=', 'users.id')->leftJoin('training_video_category','training_video.cat_id', '=', 'training_video_category.id')->where('training_video.is_deleted', '0')->orderBy('training_video.id', 'desc');
        
        if (!empty($title)){
            $training_video->where('training_video.title', 'LIKE', '%' . $title . '%');
        }
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            $training_video->where('training_video.status', '=', $status);
        }

        $training_video = $training_video->where('training_video.type', 'video')->get();

        return view('admin.training_video.index', compact('training_video'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employee = User::where('status','1')->where('is_deleted','0')->get();
        $training_category = TrainingVideoCategory::where('status','Active')->where('is_deleted','0')->get();
		$allDepartmentTypes  = $this->allDepartmentTypes();
        return view('admin.training_video.add', compact('employee','training_category','allDepartmentTypes'));
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
            'user_id' => 'required',
            'cat_id' => 'required',
            'date' => 'required',
            'video_id' => 'required',
            'video_url' => 'required',
            'image_url' => 'required',
            'description' => 'required',
            'video_id' => 'required',
            'status' => 'required',
            'title' => 'required|unique:training_video',
            'department_type' => 'required',
        ]);
		
        $inputs = $request->only('user_id','cat_id','title','date','video_id','video_url','description','status');   
        
        if (Input::hasfile('image_url')){
            $inputs['image_url'] = $this->uploadFile(Input::file('image_url'), '');
        }
		
		$inputs['department_id'] = json_encode($request->department_type); 	

        $inputs['type'] = 'video';
        $training_video = TrainingVideo::create($inputs);    

        if ($training_video->save()) {
            return redirect()->route('admin.training_video.index')->with('success', 'Training Video Added Successfully');
        } else {
            return redirect()->route('admin.training_video.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function uploadFile($file, $id){
		$drive = public_path(DIRECTORY_SEPARATOR . 'training_video_image_url' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$newImage = $drive . $filename;
		
		if(!empty($id)){
			$check_file = DB::table('training_video')->where('id', $id)->first();
			if(!empty($check_file->image_url)){ 
				unlink($drive.'/'.$check_file->image_url);
			}
		}
		
		$imgResource = $file->move($drive, $filename);
		return $filename;
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
        $training_video = TrainingVideo::find($id);
        $employee = User::where('status','1')->where('is_deleted','0')->get();
        $training_category = TrainingVideoCategory::where('status','Active')->where('is_deleted','0')->get();
		$allDepartmentTypes  = $this->allDepartmentTypes();
		// echo "<pre>"; print_r($allDepartmentTypes); die;
        return view('admin.training_video.edit', compact('training_video','employee','training_category','allDepartmentTypes'));
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
            'user_id' => 'required',
            'cat_id' => 'required',
            'date' => 'required',
            'video_id' => 'required',
            'video_url' => 'required',
            'description' => 'required',
            'video_id' => 'required',
            'status' => 'required',
            'title' => 'required',
            'department_type' => 'required',
        ]);

        $inputs = $request->only('user_id','cat_id','title','date','video_id','video_url','description','status');

        $training_video_res = TrainingVideo::where('id', $id)->first();
        if (Input::hasfile('image_url')){
            $inputs['image_url'] = $this->uploadFile(Input::file('image_url'), $id);
        }
        else{
            $inputs['image_url'] = $training_video_res->image_url;
        }
		
		$inputs['department_id'] = json_encode($request->department_type); 	


        $training_video = TrainingVideo::where('id', $id)->first();  

        if ($training_video->update($inputs)) {
            return redirect()->route('admin.training_video.index')->with('success', 'Training Video Updated Successfully');
        } else {
            return redirect()->route('admin.training_video.index')->with('error', 'Something Went Wrong !');
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
        $training_video = TrainingVideo::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($training_video->update($inputs)) {
            return redirect()->back()->with('success', 'Training Video Deleted Successfully');
        } else {
            return redirect()->route('admin.training_video.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function download_excel()
    {   
		$title = Input::get('title');
        $status = Input::get('status');

        $training_video = TrainingVideo::select('training_video.*','users.name as user_name','training_video_category.name as cat_name')->leftJoin('users','training_video.user_id', '=', 'users.id')->leftJoin('training_video_category','training_video.cat_id', '=', 'training_video_category.id')->where('training_video.is_deleted', '0')->orderBy('training_video.id', 'desc');
        
        if (!empty($title)){
            $training_video->where('training_video.title', 'LIKE', '%' . $title . '%');
        }
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            $training_video->where('training_video.status', '=', $status);
        }

        $get_data = $training_video->where('training_video.type', 'video')->get();
        if(count($get_data) > 0){
            return Excel::download(new TrainingVideoExport($get_data), 'TrainingVideoData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    } 
}
