<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ExpenseCategory;
use App\Expense;
use App\User;
use Input;
use DB;

class ExpenseController extends Controller
{
    
    public function index()
    { 
        $title = Input::get('title');
        $status = Input::get('status');

        $expense = Expense::select('expense.*','expense_category.name as cat_name','users.name as user_name')->leftJoin('expense_category','expense.cat_id', '=', 'expense_category.id')->leftJoin('users', 'users.id', '=', 'expense.user_id')->where('expense.is_deleted', '0')->orderBy('expense.id', 'desc');
        
        if (!empty($title)){
            $expense->where('expense.title', 'LIKE', '%' . $title . '%');
        }
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            $expense->where('expense.status', '=', $status);
        }

        $expense = $expense->get();

        return view('admin.expense.index', compact('expense'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
       //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	public function status_update(Request $request){
		$estatus	=	$request->estatus;
		$expense_id	=	$request->expense_id;
		
		if($estatus!="" && $expense_id!=""){
			$data = array(
				"status"	=>	$estatus
			);
			
			Expense::where('id',$expense_id)->update($data);
			
			return redirect()->back()->with('success', 'Expense Status Updated');
		}else{
			return redirect()->back()->with('error', 'Something Went Wrong !');
		}
	}
	

}
