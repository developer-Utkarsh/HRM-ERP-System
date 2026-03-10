<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\KnowledgeBasedCategory;
use App\KnowledgeBased;
use App\User;
use Input;
use DB;
use Excel;
use App\Exports\KnowledgeBasedExport;

class KnowledgeBasedController extends Controller
{
    
    public function index()
    { 
        $title = Input::get('title');
        $category = Input::get('cat_id');
        $status = Input::get('status');

        $knowledge_based = KnowledgeBased::select('knowledge_based.*','users.name as user_name','knowledge_based_category.name as cat_name')->leftJoin('users','knowledge_based.emp_id', '=', 'users.id')->leftJoin('knowledge_based_category','knowledge_based.cat_id', '=', 'knowledge_based_category.id')->where('knowledge_based.is_deleted', '0')->orderBy('knowledge_based.id', 'desc');
        
        if (!empty($title)){
            $knowledge_based->where('knowledge_based.title', 'LIKE', '%' . $title . '%');
        }
		
		 if (!empty($category)){
            $knowledge_based->where('knowledge_based_category.id', 'LIKE', '%' . $category . '%');
        }
		
		
        //echo '<pre>'; print_r($status);die;
        if(!empty($status)){
            $knowledge_based->where('knowledge_based.status', '=', $status);
        }

        $knowledge_based = $knowledge_based->get();
		
		$kb_category = KnowledgeBasedCategory::where('status','Active')->where('is_deleted','0')->get();

        return view('admin.knowledge_based.index', compact('knowledge_based','kb_category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employee = User::where('status','1')->where('is_deleted','0')->get();
        $kb_category = KnowledgeBasedCategory::where('status','Active')->where('is_deleted','0')->get();
        return view('admin.knowledge_based.add', compact('employee','kb_category'));
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
            'emp_id' => 'required',
            'cat_id' => 'required',
            'description' => 'required',
            // 'reference_link' => 'required',
            'status' => 'required',
            'title' => 'required|unique:knowledge_based',
        ]);

        $inputs = $request->only('emp_id','cat_id','title','description','reference_link','status');        

        $knowledge_based = KnowledgeBased::create($inputs);    

        if ($knowledge_based->save()) {
            return redirect()->route('admin.knowledge_based.index')->with('success', 'Knowledge Based Added Successfully');
        } else {
            return redirect()->route('admin.knowledge_based.index')->with('error', 'Something Went Wrong !');
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
        $knowledge_based = KnowledgeBased::find($id);
        $employee = User::where('status','1')->where('is_deleted','0')->get();
        $kb_category = KnowledgeBasedCategory::where('status','Active')->where('is_deleted','0')->get();
        return view('admin.knowledge_based.edit', compact('knowledge_based','employee','kb_category'));
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
            'emp_id' => 'required',
            'cat_id' => 'required',
            'description' => 'required',
            // 'reference_link' => 'required',
            'status' => 'required',
            'title' => 'required|unique:knowledge_based,title,'.$id,
        ]);

        $knowledge_based = KnowledgeBased::where('id', $id)->first();

        $inputs = $request->only('emp_id','cat_id','title','description','reference_link','status');       

        if ($knowledge_based->update($inputs)) {
            return redirect()->route('admin.knowledge_based.index')->with('success', 'Knowledge Based Updated Successfully');
        } else {
            return redirect()->route('admin.knowledge_based.index')->with('error', 'Something Went Wrong !');
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
        $knowledge_based = KnowledgeBased::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($knowledge_based->update($inputs)) {
            return redirect()->back()->with('success', 'Knowledge Based Deleted Successfully');
        } else {
            return redirect()->route('admin.knowledge_based.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	
	public function download_excel()
    {   
        $title = Input::get('title');
        $status = Input::get('status');

        $knowledge_based = KnowledgeBased::select('knowledge_based.*','users.name as user_name','knowledge_based_category.name as cat_name')->leftJoin('users','knowledge_based.emp_id', '=', 'users.id')->leftJoin('knowledge_based_category','knowledge_based.cat_id', '=', 'knowledge_based_category.id')->where('knowledge_based.is_deleted', '0')->orderBy('knowledge_based.id', 'desc');
        
        if (!empty($title)){
            $knowledge_based->where('knowledge_based.title', 'LIKE', '%' . $title . '%');
        }
      
        if(!empty($status)){
            $knowledge_based->where('knowledge_based.status', '=', $status);
        }

        $get_data = $knowledge_based->get();
	
		//echo '<pre>'; print_r($designation);die;
        if(count($get_data) > 0){
            return Excel::download(new KnowledgeBasedExport($get_data), 'KnowledgeBasedData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    } 
}
