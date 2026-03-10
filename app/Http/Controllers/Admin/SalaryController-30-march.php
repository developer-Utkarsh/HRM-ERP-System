<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;
use Input;
use Excel;
use App\Exports\SalaryExport;
use DB;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		
		$month       = Input::get('month');
		$page        = Input::get('page');
		$search      = Input::get('search');
		$whereCond   = '1=1 ';
		if(!empty($search)){ 
			$whereCond .= " AND (users.name LIKE '%$search%' OR users.email LIKE '%$search%' OR users.mobile LIKE '%$search%')";
		}
		
		$get_emp = User::with('user_details')->where([['role_id', '!=', 1], ['status', '=', 1]])->whereRaw($whereCond)->paginate(10);
		//echo '<pre>'; print_r($month);die;
		$pageNumber = 1;
		if(isset($page)){
			//$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
        return view('admin.salary.index', compact('get_emp','params','pageNumber'));
    }
	
	 public function download_excel(){  
		$year_wise_month = Input::get('year_wise_month');
		$get_emp         = array();
		if(!empty($year_wise_month)){
			$get_emp = User::with('user_details')->where([['role_id', '!=', 1], ['status', '=', 1]])->get();
		}
		
		
		//echo '<pre>'; print_r($get_emp);die;
        if(count($get_emp) > 0){
            return Excel::download(new SalaryExport($get_emp), 'SalaryData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
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

}
