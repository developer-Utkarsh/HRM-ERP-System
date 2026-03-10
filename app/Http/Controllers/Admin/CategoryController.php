<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Input;
use Validator;


class CategoryController extends Controller
{
    
    public function index()
    {
		$name   = Input::get('name');

	
        $category = Category::where('parent', '0')->where('is_deleted', '0')->orderBy('id', 'desc');
		
		if(!empty($name)){
			$category->where('id',$name);
		}
		$category = $category->get();
		
		//echo '<pre>'; print_r($category);die;
        return view('admin.category.index', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:category',
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.category.index')->with('error', 'Category Already Exits');
		}
		
		$inputs = $request->only('name'); 
		
		if(!empty($request->id)){
			$categoryId  = Category::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
		}
		else{
			$category = Category::create($inputs); 
			$cat_res  = $category->save();
		}
    

        if($cat_res) {
            return redirect()->route('admin.category.index')->with('success', 'Category Added Successfully');
        } else {
            return redirect()->route('admin.category.index')->with('error', 'Something Went Wrong !');
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
        $category = Category::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('admin.category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function subCategoryDestroy($id){
		
	}
	
	public function edit_category(Request $request){
		$cat_id  = $request->cat_id;  
        $respnse = Category::where([['id', '=', $cat_id], ['parent', '=', '0']])->first();
		
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
	
	
	public function subcategory($id, Request $request){
		$sub_category = Category::where('parent', $id)->where('is_deleted', '0')->orderBy('id', 'desc')->get();  
		$cat_id       = $id;
    	return view('admin.category.sub-category',compact('sub_category','cat_id'));
	}
	
	public function subCategoryEditStore(Request $request){
		$inputs    = array('name' => $request->name,  'parent' => $request->parent);
		
		if(!empty($request->id)){
			$categoryId   = Category::where('id', $request->id)->first();
			$sub_cat_res  = $categoryId->update($inputs);
			$msg =  "Sub Category Update Successfully";
		}
		else{
			$category = Category::create($inputs); 
			$sub_cat_res  = $category->save();
			$msg =  "Sub Category Added Successfully";
		}
    

        if($sub_cat_res) {
            //return redirect()->route('admin.category.index')->with('success', 'Sub Category Added Successfully');
			return redirect()->back()->with('success', $msg);
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function subCategoryStore(Request $request){   //echo '<pre>'; print_r($request->all());die;
		
		$inputs    = array('name' => $request['name'], 'parent' => $request['parent']);
		if(!empty($request->id)){
			$categoryId  = Category::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
		}
		else{
			$category = Category::create($inputs); 
			$cat_res  = $category->save();
		}
		
		if($cat_res){
			return redirect()->route('admin.category.index')->with('success', 'Sub Category Added Successfully');
		}
		else{
			 return redirect()->route('admin.category.index')->with('error', 'Something Went Wrong !');
		}
	}
	
	public function editSubCategory(Request $request){ 
		$sub_cat_id  = $request->sub_cat_id;
		$parent_id   = $request->parent_id;
		$cat_id      = $request->cat_id;
		$category    = array();
		if(!empty($sub_cat_id) && !empty($parent_id)){
			$respnse     = Category::where([['id', '=', $sub_cat_id], ['parent', '=', $parent_id]])->first();
			$category    = Category::where('parent', '0')->orderBy('id', 'desc')->get(); 
		}
		
		if(!empty($cat_id)){
			$respnse       = Category::where([['id', '=', $cat_id]])->first();
			
		}
        
        if (!empty($respnse))
        {
            $res  = "";
			$sel  = "";
			
			
			$res .= "
					<label>Category:</label>
					<select class='form-control select-multiple1' name='parent' required>";
						if(count($category) > 0){
						foreach($category as $categoryvalue): 
						if( $categoryvalue->id == $respnse->parent):
						$sel = 'selected';
						endif;
						$res .="<option value='" . $categoryvalue->id . "' " . $sel . ">". $categoryvalue->name ."</option>";
						$sel = '';
						endforeach;
						}
						else{
						$res .="<option value='" . $respnse->id . "' >". $respnse->name ."</option>";	
						}
			$res .="</select>
					<br>
					<label>Sub Category:</label>";
			if(!empty($cat_id)){		
				$res .="<input type='text' name='name' class='form-control' value='' required>";
			}
			else{
				$res .="<input type='text' name='name' class='form-control' value='" . $respnse->name . "' required>
						<input type='hidden' name='id' class='form-control' value='" . $respnse->id . "'>";
			}
            
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<label>Category:</label>";
            die();
        }
	}
}
