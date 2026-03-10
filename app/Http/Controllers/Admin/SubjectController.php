<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subject;
use Input;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $name = Input::get('name');
        $status = Input::get('status');
        
		$subjects = Subject::orderBy('id', 'desc')
			->where('is_deleted', '0')
			->select('*')
			->selectSub(function ($query) {
				$query->from('topic_master as tm')
					->selectRaw('COUNT(DISTINCT tm.id)')
					->whereColumn('tm.subject_id', 'subject.id');
			}, 'topic_count');

		if (!empty($name)) {
			$subjects->where('name', 'LIKE', '%' . $name . '%');
		}

		if (!empty($status)) {
			$subjects->where('status', $status == 'Inactive' ? '0' : '1');
		}

		$subjects = $subjects->get();

		return view('admin.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.subject.add');
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
            'name' => 'required|max:100|unique:subject',
        ]);

        $inputs = $request->only('name','status');        

        $subject = Subject::create($inputs);    

        if ($subject->save()) {
            return redirect()->route('admin.subjects.index')->with('success', 'Subject Added Successfully');
        } else {
            return redirect()->route('admin.subjects.index')->with('error', 'Something Went Wrong !');
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
        $subject = Subject::find($id);
        return view('admin.subject.edit', compact('subject'));
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
            'name' => 'sometimes|required|max:100||unique:subject,name,'.$id,
        ]);

        $subject = Subject::where('id', $id)->first();

        $inputs = $request->only('name','status');       

        if ($subject->update($inputs)) {
            return redirect()->route('admin.subjects.index')->with('success', 'Subject Updated Successfully');
        } else {
            return redirect()->route('admin.subjects.index')->with('error', 'Something Went Wrong !');
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
        $subject = Subject::find($id);
		$inputs = array('is_deleted' => '1','status' => '0');

        // if ($subject->delete()) {
			
        if ($subject->update($inputs)) {
            return redirect()->back()->with('success', 'Subject Deleted Successfully');
        } else {
            return redirect()->route('admin.subjects.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	//dk
	public function togglePublish($id) {
        $subject = Subject::find($id);
        if (is_null($subject)) {
            return redirect()->route('admin.subjects.index')->with('error', 'Subject not found');
        }
        try {
            $subject->update([
                'status' => !$subject->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('admin.subjects.index')->with('success', 'Status Updated Successfully.');
    }
}
