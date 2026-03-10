<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Topic;
use Input;

class TopicController extends Controller
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
		$page       	 = Input::get('page');
		
        $course_id = Input::get('course_id');
        $subject_id = Input::get('subject_id');
        $chapter_id = Input::get('chapter_id');
        $name = Input::get('name');
        $status = Input::get('status');
        
        $topics = Topic::with('course','subject','chapter')->orderBy('id', 'desc');

        if (!empty($course_id)){
            $topics->where('course_id', $course_id);
        }

        if (!empty($subject_id)){
            $topics->where('subject_id', $subject_id);
        }

        if (!empty($chapter_id)){
            $topics->where('chapter_id', $chapter_id);
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $topics->where('status', '=', '0');
            }else{
                $topics->where('status', '=', '1');
            }
        }
		
		$topics->where('is_deleted', '0');
        $topics = $topics->paginate(100);
		
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (100*($page-1));
			
			$pageNumber = $pageNumber +1;
		}
          // echo '<pre>'; print_r($topics);die;
        return view('admin.topic.index', compact('topics','pageNumber','params'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.topic.add');
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
            'course_id' => 'required',
            'name' => 'required',
        ]);

        $inputs = $request->only('course_id','subject_id','chapter_id','name','duration','status');        

        $topic = Topic::create($inputs);    

        if ($topic->save()) {
            return redirect()->route('admin.topics.index')->with('success', 'Topic Added Successfully');
        } else {
            return redirect()->route('admin.topics.index')->with('error', 'Something Went Wrong !');
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
        $topic = Topic::with('course','subject','chapter')->find($id);
        return view('admin.topic.edit', compact('topic'));
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
            'course_id' => 'required',
            'name' => 'required',
        ]);

        $topic = Topic::where('id', $id)->first();

        $inputs = $request->only('course_id','subject_id','chapter_id','name','duration','status');       

        if ($topic->update($inputs)) {
            return redirect()->route('admin.topics.index')->with('success', 'Topic Updated Successfully');
        } else {
            return redirect()->route('admin.topics.index')->with('error', 'Something Went Wrong !');
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
        $topic = Topic::find($id);
		$inputs = array('is_deleted' => '1','status' => '0');
		
        // if ($topic->delete()) {
        if ($topic->update($inputs)) {
            return redirect()->back()->with('success', 'Topic Deleted Successfully');
        } else {
            return redirect()->route('admin.topics.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	//dk
	public function togglePublish($id) {
        $topic = Topic::find($id);
        if (is_null($topic)) {
            return redirect()->route('admin.topics.index')->with('error', 'Topic not found');
        }
        try {
            $topic->update([
                'status' => !$topic->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.topics.index')->with('success', 'Status Updated Successfully.');
    }
}
