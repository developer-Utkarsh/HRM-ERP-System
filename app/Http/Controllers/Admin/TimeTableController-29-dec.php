<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use App\TimeSlot;
use App\Subject;
use App\Chapter;
use App\Topic;
use DB;
use App\Studio;
use Input;
use App\FacultyRelation;
use App\Reschedule;
use App\CancelClass;
use App\Batchrelation;
use App\Batch;
use App\User;
use App\Swap;
use App\Userdetails;
use App\ClassRemark;
use Excel;
use App\Exports\TimetableExport;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
        $fdate = Input::get('fdate');

        $timeslots = TimeSlot::get();

        $get_studios = array();

        if ($branch_id)
        {

            $get_studios = Studio::with(['assistant', 'timetable' => function ($q) use ($faculty_id, $fdate)
            {
                if (!empty($faculty_id))
                {
                    $q->where('faculty_id', $faculty_id)->orderBy('from_time', 'asc');
                }
                if (!empty($fdate))
                {
                    $q->Where('cdate', $fdate)->orderBy('from_time', 'asc');
                }
            }
            , 'timetable.topic', ])
            ->orderBy('id', 'desc');

            if (!empty($branch_id))
            {
                $get_studios->where('branch_id', '=', $branch_id);
            }

            $get_studios = $get_studios->get();
        }

        return view('admin.timetable.index', compact('timeslots', 'get_studios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.timetable.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax())
        {
			if(!empty($request->from_time) && !empty($request->to_time)){

				$from_time_id = TimeSlot::where('time_slot', $request->from_time)
				->first();
				$get_from_time_id = $from_time_id->id;
				$to_time_id = TimeSlot::where('time_slot', $request->to_time)
				->first();
				$get_to_time_id = $to_time_id->id;

				$get_studio_timetable = Timetable::where('studio_id', $request->studio_id)
				->where('cdate', $request->cdate)
				->get();

				if (count($get_studio_timetable) > 0)
				{
					$from_time2 = [];
					$to_time2 = [];

					foreach ($get_studio_timetable as $value)
					{
						$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
						->first();
						$from_time2[] = $from_time1->id;
						$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
						->first();
						$to_time2[] = $to_time1->id;
					}

					$chk_condition = 'false';

					for ($i = 0;$i < count($from_time2);$i++)
					{
						if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
						{
							$chk_condition = 'true';
						}
						else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
						{
							$chk_condition = 'true';
						}
					}

					if ($chk_condition == 'true')
					{
						return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
					}
					else
					{

						$get_faculty_studio_timetable = Timetable::where('faculty_id', $request->faculty_id)
						->where('cdate', $request->cdate)
						->get();

						if (count($get_faculty_studio_timetable) > 0)
						{

							$from_time2 = [];
							$to_time2 = [];

							foreach ($get_faculty_studio_timetable as $value)
							{
								$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
								->first();
								$from_time2[] = $from_time1->id;
								$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
								->first();
								$to_time2[] = $to_time1->id;
							}

							$chk_condition = 'false';

							for ($i = 0;$i < count($from_time2);$i++)
							{
								if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
								else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
								{
									$chk_condition = 'true';
								}
							}

							if ($chk_condition == 'true')
							{
								return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
							}
							else
							{

								$inputs = $request->only('studio_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'chapter_id', 'topic_id', 'from_time', 'to_time', 'cdate'); 

								$timetable = Timetable::create($inputs);

								$remark = ClassRemark::where('subject_id', $request->subject_id)->first();

								if($remark){
									$remark->remark = $request->remark;
									$remark->update();

								}else{
									$input_remark = $request->only('subject_id','remark');
									$remark = ClassRemark::create($input_remark);
								}                            

								if ($timetable->save())
								{
									return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
								}
								else
								{
									return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
								}

							}

						}
						else
						{

							$inputs = $request->only('studio_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'chapter_id', 'topic_id', 'from_time', 'to_time', 'cdate');

							$timetable = Timetable::create($inputs);

							$remark = ClassRemark::where('subject_id', $request->subject_id)->first();

							if($remark){
								$remark->remark = $request->remark;
								$remark->update();

							}else{
								$input_remark = $request->only('subject_id','remark');
								$remark = ClassRemark::create($input_remark);
							}

							if ($timetable->save())
							{
								return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
							}
							else
							{
								return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
							}

						}
					}

				}
				else
				{
					$get_faculty_studio_timetable = Timetable::where('faculty_id', $request->faculty_id)
					->where('cdate', $request->cdate)
					->get();

					if (count($get_faculty_studio_timetable) > 0)
					{

						$from_time2 = [];
						$to_time2 = [];

						foreach ($get_faculty_studio_timetable as $value)
						{
							$from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
							->first();
							$from_time2[] = $from_time1->id;
							$to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
							->first();
							$to_time2[] = $to_time1->id;
						}

						$chk_condition = 'false';

						for ($i = 0;$i < count($from_time2);$i++)
						{
							if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
							else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
							{
								$chk_condition = 'true';
							}
						}

						if ($chk_condition == 'true')
						{
							return response(['status' => false, 'message' => 'Slot is not available, please choose another one.'], 200);
						}
						else
						{

							$inputs = $request->only('studio_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'chapter_id', 'topic_id', 'from_time', 'to_time', 'cdate');

							$timetable = Timetable::create($inputs);
							
							$remark = ClassRemark::where('subject_id', $request->subject_id)->first();

							if($remark){
								$remark->remark = $request->remark;
								$remark->update();

							}else{
								$input_remark = $request->only('subject_id','remark');
								$remark = ClassRemark::create($input_remark);
							}

							if ($timetable->save())
							{
								return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
							}
							else
							{
								return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
							}
						}

					}
					else
					{
						$inputs = $request->only('studio_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'chapter_id', 'topic_id', 'from_time', 'to_time', 'cdate');

						$timetable = Timetable::create($inputs);
						
						$remark = ClassRemark::where('subject_id', $request->subject_id)->first();

						if($remark){
							$remark->remark = $request->remark;
							$remark->update();

						}else{
							$input_remark = $request->only('subject_id','remark');
							$remark = ClassRemark::create($input_remark);
						}

						if ($timetable->save())
						{
							return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
						}
						else
						{
							return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
						}
						// $faculty_avaliable_time = FacultyRelation::where('user_id', $request->faculty_id)
						//     ->get();

						// if (count($faculty_avaliable_time) > 0)
						// {

						//     $from_time2 = [];
						//     $to_time2 = [];

						//     foreach ($faculty_avaliable_time as $value)
						//     {
						//         $from_time1 = TimeSlot::select('id')->where('time_slot', $value->from_time)
						//             ->first();
						//         $from_time2[] = $from_time1->id;
						//         $to_time1 = TimeSlot::select('id')->where('time_slot', $value->to_time)
						//             ->first();
						//         $to_time2[] = $to_time1->id;
						//     }

						//     $chk_condition = 'false';

						//     for ($i = 0;$i < count($from_time2);$i++)
						//     {
						//         if ($get_from_time_id >= $from_time2[$i] && $get_from_time_id <= $to_time2[$i])
						//         {
						//             $chk_condition = 'true';
						//         }
						//         else if ($get_to_time_id >= $from_time2[$i] && $get_to_time_id <= $to_time2[$i])
						//         {
						//             $chk_condition = 'true';
						//         }
						//     }                        

						//     if ($chk_condition == 'true')
						//     {
						//         return response(['status' => false, 'message' => 'Faculty Is Not Available This Time Slot!! Try Another4.'], 200);
						//     }
						//     else
						//     {

						//         $inputs = $request->only('studio_id', 'faculty_id', 'batch_id', 'course_id', 'subject_id', 'chapter_id', 'topic_id', 'from_time', 'to_time', 'cdate');

						//         $timetable = Timetable::create($inputs);

						//         if ($timetable->save())
						//         {
						//             return response(['status' => true, 'message' => 'Class Added Successfully.'], 200);
						//         }
						//         else
						//         {
						//             return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
						//         }
						//     }

						// }
						// else
						// {
						//     return response(['status' => false, 'message' => 'Faculty Time Slot Not Found'], 200);
						// }

					}
				}
			}
			else{
				return response(['status' => false, 'message' => 'From time and To time required.'], 200);
			}

        }
        else
        {
            return response('Something Went Wrong', 500);
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

    public function get_batch(Request $request)
    {

        $faculty_id = $request->faculty_id;

        $batchs = Batchrelation::with('batch')->select('batch_id')->where('faculty_id', $faculty_id)->groupBy('batch_id')->get();

        if (!empty($batchs))
        {
            echo $res = "<option value=''> Select Batch </option>";
            foreach ($batchs as $key => $value)
            {
                if (!empty($value->batch->name) && !empty($value->batch->name))
                {
                    echo $res = "<option value='" . $value->batch->id . "'>" . $value->batch->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No state'> Batch Not Found </option>";
            die();
        }
    }

    public function get_course(Request $request)
    {

        $batch_id = $request->batch_id;

        $course = Batch::with('course')->select('course_id')->where('id', $batch_id)->get();

        //print_r($course->toArray()); die;
        echo $res = "<input type='hidden' name='batch_id' value='" . $batch_id . "'>";

        if (!empty($course))
        {
            $res = "";
            foreach ($course as $key => $value)
            {
                if (!empty($value->course->name) && !empty($value->course->name))
                {
                    $res .= "<option value='" . $value->course->id . "'>" . $value->course->name . "</option>";
                }
            }
			if(empty($res)){
				$res = "<option value=''> Select Course </option>";
			}
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Course Not Found </option>";
            die();
        }
    }

    public function get_batch_subject(Request $request){

        $batch_id = $request->batch_id;

        $subjects = Batchrelation::with('subject')->where('batch_id', $batch_id)->get();

        if (!empty($subjects)) {                         
            echo $res = "<option value=''> Select Subject </option>";
            foreach ($subjects as $key => $value) {
                if(!empty($value->subject->name) && !empty($value->subject->name)){
                    echo $res = "<option value='". $value->subject->id ."'>" . $value->subject->name ."</option>";
                }
            }
            exit();
        } else {
            echo $res = "<option value='No data'> Subject Not Found </option>";
            die();
        }
    }

    public function get_remark(Request $request)
    {
        $subject_id = $request->subject_id;

        $classremark = ClassRemark::where('subject_id', $subject_id)->first();

        if(!empty($classremark)){
            echo json_encode(['status' => true, 'data' => $classremark->remark]);
        }else{
            echo json_encode(['status' => false, 'data' => '']);
        }
    }

    public function get_chapter(Request $request)
    {
        $subject_id = $request->subject_id;
        $course_id = $request->course_id;

        $chapters = Chapter::where('subject_id', $subject_id)->where('course_id', $course_id)->get();

        if (!empty($chapters))
        {
            echo $res = "<option value=''> Select Chapter </option>";
            foreach ($chapters as $key => $value)
            {
                if (!empty($value->name) && !empty($value->name))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Chapter Not Found </option>";
            die();
        }
    }

    public function get_topic(Request $request)
    {

        $chapter_id = $request->chapter_id;

        $topics = Topic::where('chapter_id', $chapter_id)->get();

        if (!empty($topics))
        {
            echo $res = "<option value=''> Select Topic </option>";
            foreach ($topics as $key => $value)
            {
                if (!empty($value->name) && !empty($value->name))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No state'> Topic Not Found </option>";
            die();
        }
    }

    public function reschedule_store(Request $request)
    {
        $inputs = $request->only('timetable_id','to_time','faculty_reason');            

        $reschedule = Reschedule::create($inputs);           

        if($reschedule->save()){
            return response(['status' => true, 'message' => 'Reschedule Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function swap_store(Request $request)
    {
        $inputs = $request->only('timetable_id', 'swap_with_faculty_id','swap_timetable_id');

        $inputs['swap_with_faculty_id'] = $request->swap_faculty_id;

        $swap = Swap::create($inputs);           

        if($swap->save()){
            return response(['status' => true, 'message' => 'Swap Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function cancelclass_store(Request $request)
    {
        $inputs = $request->only('timetable_id','days','faculty_reason');

        if(!empty($request->other_reason)){
            $inputs['faculty_reason'] = $request->other_reason;
        }           

        $cancelclass = CancelClass::create($inputs);           

        if($cancelclass->save()){
            return response(['status' => true, 'message' => 'Cancel Class Request Sent Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function get_swap_faculty(Request $request)
    {

        $faculty_id = $request->faculty_id;
        $get_faculty_branch = Timetable::with('studio')->where('faculty_id', $faculty_id)->first();
        //print_r($get_faculty_branch->studio->branch_id);

        $userdeatils = Userdetails::with([
            'user' => function($q){
                $q->select('id','name')->where('role_id','2')->where('status', '1');
            },
        ])->where('user_id','!=',$faculty_id)->where('branch_id',$get_faculty_branch->studio->branch_id)->get();

        //print_r($userdeatils->toArray()); die;

        //$get_faculty = User::select('id','name')->where('role_id','2')->where('id','!=',$faculty_id)->get();

        //print_r($get_faculty->toArray()); die;       

        if (!empty($userdeatils))
        {
            echo $res = "<option value=''> Select Faculty </option>";
            foreach ($userdeatils as $key => $value)
            {
                if (!empty($value->user->name) && !empty($value->user->name))
                {
                    echo $res = "<option value='" . $value->user->id . "'>" . $value->user->name . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Faculty Not Found </option>";
            die();
        }
    }

    public function get_swap_faculty_timetable(Request $request)
    {

        $swap_faculty_id = $request->swap_faculty_id;

        $get_faculty_timetable = Timetable::select('id','from_time')->where('faculty_id',$swap_faculty_id)->get();

        //print_r($get_faculty_timetable->toArray()); die;       

        if (!empty($get_faculty_timetable))
        {
            echo $res = "<option value=''> Select From Time </option>";
            foreach ($get_faculty_timetable as $key => $value)
            {
                if (!empty($value->from_time) && !empty($value->from_time))
                {
                    echo $res = "<option value='" . $value->id . "'>" . $value->from_time . "</option>";
                }
            }
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> From Time Not Found </option>";
            die();
        }
    }

    public function timetable_export(Request $request)
    {
        return view('admin.timetable.exportdata');
    }

    public function export_data(Request $request)
    {
        if(is_array($request->id) && !empty($request->id)){

            $id = [];

            if ($request->has('id') && !empty($request->id)) {

                $id =  $request->id;
                return Excel::download(new TimetableExport($id), 'TimetableData.xlsx');
            }

        } else{
            return redirect()->route('admin.timetables.export')->with('error', 'Please Select Checkbox');
        }
    }
	
	public function get_faculty(Request $request)
    {

        $subject_id = $request->subject_id;
        $batch_id = $request->batch_id;

        $faculty = Batchrelation::with('user')->where('batch_id', $batch_id)->where('subject_id', $subject_id)->get();

        if (!empty($faculty))
        {
            $res = "";
            foreach ($faculty as $key => $value)
            {
                if (!empty($value->user->name) && !empty($value->user->name))
                {
                    $res .= "<option value='" . $value->user->id . "'>" . $value->user->name ." (". $value->user->register_id .")". "</option>";
                }
            }
			if(empty($res)){
				$res = "<option value=''> Select Faculty </option>";
			}
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<option value='No data'> Faculty Not Found </option>";
            die();
        }
    }
}

