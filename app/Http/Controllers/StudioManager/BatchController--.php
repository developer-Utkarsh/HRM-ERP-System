<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\CourseSubjectRelation;
use App\Batch;
use App\Batchrelation;
use App\Course;
use Auth;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = Batch::with('course')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();
        return view('studiomanager.batch.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faculty = User::where('role_id', '2')->orderBy('id','desc')->get();
		//where('user_id', Auth::user()->id)->
        $courses = Course::where('status','1')->orderBy('id', 'desc')->get();
        return view('studiomanager.batch.add', compact('faculty','courses'));
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

        $inputs = $request->only('user_id','course_id','name','start_date','status');

        $inputs['user_id'] = Auth::user()->id;

        $batch = Batch::create($inputs);

        $course = $request->course;      
        if(isset($course) && is_array($course)){               
            foreach($course['subject_id'] as $key => $value){
                if(!empty($value)){                          
                    $data = array(                  
                        'subject_id'=>$value,
                        'faculty_id'=>$course['faculty_id'][$key]                   
                    );            
                    $batch->batch_relations()->create($data);
                }
            }
        }

        if ($batch->save()) {
            return redirect()->route('studiomanager.batch.index')->with('success', 'Batch Added Successfully');
        } else {
            return redirect()->route('studiomanager.batch.index')->with('error', 'Something Went Wrong !');
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
        $batch = Batch::with('batch_relations.subject')->find($id);
        $faculty = User::where('role_id', '2')->orderBy('id','desc')->get();
		//where('user_id', Auth::user()->id)->
        $courses = Course::where('status','1')->orderBy('id', 'desc')->get();
        return view('studiomanager.batch.edit', compact('batch', 'faculty','courses'));
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

        $batch = Batch::with('batch_relations')->where('id', $id)->first();

        $inputs = $request->only('course_id','name','start_date','status');       

        if (is_array($request->course) && !empty($request->course)) {
            Batchrelation::where('batch_id', $id)->delete();
            $course = $request->course;
            foreach ($course['subject_id'] as $key => $value) {
                if(!empty($value)){
                    $data = array(                  
                        'subject_id'=>$value,
                        'faculty_id'=>$course['faculty_id'][$key],
                    );
                    $batch->batch_relations()->create($data);
                }
            }
        }       

        if($batch->update($inputs)) {
            return redirect()->route('studiomanager.batch.index')->with('success', 'Batch Updated Successfully');
        } else {
            return redirect()->route('studiomanager.batch.index')->with('error', 'Something Went Wrong !');
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
        $batch = Batch::find($id);

        Batchrelation::where('batch_id', $id)->delete();

        if ($batch->delete()) {
            return redirect()->back()->with('success', 'Batch Deleted Successfully');
        } else {
            return redirect()->route('studiomanager.batch.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function get_batch_subject(Request $request){

        $course_id = $request->course_id;

        $subjects = CourseSubjectRelation::with('subject')->where('course_id', $course_id)->get();
        //print_r($subjects->toArray()); die;

        if (!empty($subjects)) {                         
            echo $res = "<option value=''> Select Subject </option>";
            foreach ($subjects as $key => $value) {
                if(!empty($value->subject->name) && !empty($value->subject->name)){
                    echo $res = "<option value='". $value->subject->id ."'>" . $value->subject->name ."</option>";
                }
            }
            exit();
        } else {
            echo $res = "<option value='No state'> Subject Not Found </option>";
            die();
        }
    }
}
