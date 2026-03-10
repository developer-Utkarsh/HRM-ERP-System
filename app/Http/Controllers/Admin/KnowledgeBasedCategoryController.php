<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\KnowledgeBasedCategory;
use Input;
use Validator;


class KnowledgeBasedCategoryController extends Controller
{
    
    public function index()
    {
  		$name   = Input::get('name');
        $category = KnowledgeBasedCategory::where('is_deleted', '0')->orderBy('id', 'desc')->where('name', 'LIKE', '%' . $name . '%')->get();
        return view('admin.knowledge_based.category', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:knowledge_based_category',
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.knowledge_based_category.index')->with('error', 'Category Already Exits');
		}
		
		$inputs = $request->only('name'); 
		
		if(!empty($request->id)){
			$categoryId  = KnowledgeBasedCategory::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
		}
		else{
			$category = KnowledgeBasedCategory::create($inputs); 
			$cat_res  = $category->save();
		}
    

        if($cat_res) {
            return redirect()->route('admin.knowledge_based_category.index')->with('success', 'Category Added Successfully');
        } else {
            return redirect()->route('admin.knowledge_based_category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $category = KnowledgeBasedCategory::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('admin.knowledge_based_category.index')->with('error', 'Something Went Wrong !');
        }
    }

	
	public function edit_category(Request $request){
		$cat_id  = $request->cat_id;  
        $respnse = KnowledgeBasedCategory::where([['id', '=', $cat_id]])->first();
		
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
        $knowledge_based_category = KnowledgeBasedCategory::find($id);
        if (is_null($knowledge_based_category)) {
            return redirect()->route('admin.knowledge_based_category.index')->with('error', 'Category not found');
        }
		
		if($knowledge_based_category->status == 'Active'){
			$sts = 'Inactive';
		}
		else{
			$sts = 'Active';
		}
		
		$knowledge_based_category->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.knowledge_based_category.index')->with('success', 'Status Updated Successfully.');
    }
}
