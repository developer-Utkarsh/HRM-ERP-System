<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TrainingVideoCategory;
use App\TrainingVideo;
use Input;
use Validator;


class TrainingVideoCategoryController extends Controller
{
    
    public function index()
    {
  		$name   = Input::get('name');
        $category = TrainingVideoCategory::where('is_deleted', '0')->orderBy('id', 'desc')->where('name', 'LIKE', '%' . $name . '%')->get();
        return view('admin.training_video.category', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:training_video_category',
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.training_video_category.index')->with('error', 'Category Already Exits');
		}
		
		$inputs = $request->only('name'); 
		
		if(!empty($request->id)){
			$categoryId  = TrainingVideoCategory::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
		}
		else{
			$category = TrainingVideoCategory::create($inputs); 
			$cat_res  = $category->save();
		}
    

        if($cat_res) {
            return redirect()->route('admin.training_video_category.index')->with('success', 'Category Added Successfully');
        } else {
            return redirect()->route('admin.training_video_category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $category = TrainingVideoCategory::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('admin.training_video_category.index')->with('error', 'Something Went Wrong !');
        }
    }

	
	public function edit_category(Request $request){
		$cat_id  = $request->cat_id;  
        $respnse = TrainingVideoCategory::where([['id', '=', $cat_id]])->first();
		
        if (!empty($respnse))
        {
            $res = "";
			$res .= "<label>Category:</label><input type='text' name='name' class='form-control' value='" . $respnse->name . "'><input type='hidden' name='id' class='form-control' value='" . $respnse->id . "'>";
            
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<label>Category:</label>";
            die();
        }
	}

    public function togglePublish($id) {
        $training_video_category = TrainingVideoCategory::find($id);
        if (is_null($training_video_category)) {
            return redirect()->route('admin.training_video_category.index')->with('error', 'Category not found');
        }
		
		if($training_video_category->status == 'Active'){
			$sts = 'Inactive';
		}
		else{
			$sts = 'Active';
		}
		
		$training_video_category->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.training_video_category.index')->with('success', 'Status Updated Successfully.');
    }
}
