<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ExpenseCategory;
use App\Expense;
use Input;
use Validator;


class ExpenseCategoryController extends Controller
{
    
    public function index()
    {
  		$name   = Input::get('name');
        $category = ExpenseCategory::where('is_deleted', '0')->orderBy('id', 'desc')->where('name', 'LIKE', '%' . $name . '%')->get();
        return view('admin.expense.category', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:expense_category',
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.expense_category.index')->with('error', 'Category Already Exits');
		}
		
		$inputs = $request->only('name'); 
		
		if(!empty($request->id)){
			$categoryId  = ExpenseCategory::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
		}
		else{
			$category = ExpenseCategory::create($inputs); 
			$cat_res  = $category->save();
		}
    

        if($cat_res) {
            return redirect()->route('admin.expense_category.index')->with('success', 'Category Added Successfully');
        } else {
            return redirect()->route('admin.expense_category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $category = ExpenseCategory::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('admin.expense_category.index')->with('error', 'Something Went Wrong !');
        }
    }

	
	public function edit_category(Request $request){
		$cat_id  = $request->cat_id;  
        $respnse = ExpenseCategory::where([['id', '=', $cat_id]])->first();
		
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
        $expense_category = ExpenseCategory::find($id);
        if (is_null($expense_category)) {
            return redirect()->route('admin.expense_category.index')->with('error', 'Category not found');
        }
		
		if($expense_category->status == 'Active'){
			$sts = 'Inactive';
		}
		else{
			$sts = 'Active';
		}
		
		$expense_category->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.expense_category.index')->with('success', 'Status Updated Successfully.');
    }
}
