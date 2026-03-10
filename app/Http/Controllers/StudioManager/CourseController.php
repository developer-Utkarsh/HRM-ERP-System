<?php
namespace App\Http\Controllers\StudioManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Course;
use App\Subject;
use App\Chapter;
use App\Topic;
use App\CourseSubjectRelation;
use Input;
use DB;
use Excel;
use App\Exports\CourseBySubjectExport;
use Auth;
class CourseController extends Controller
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
		
        $name = Input::get('name');
        $status = Input::get('status');
        $type = Input::get('type');
        
		$courses = DB::table('course')
                          ->select('course.*')
						  ->leftJoin('batch', 'batch.course_id', '=', 'course.id')
						  ->where('course.is_deleted', '0')
						  ->groupBy('course.id')
						  ->orderBy('course.id', 'desc');
						  
        // $courses = Course::with('batch')->where('is_deleted', '0')->orderBy('id', 'desc');

        if (!empty($name)){
            $courses->where('course.name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $courses->where('course.status', '=', '0');
            }else{
                $courses->where('course.status', '=', '1');
            }
        }
		if(!empty($type)){
			if($type != 'all'){
				$courses->where('batch.type', '=', $type);
			}
        }
		/* else{
			$type = "offline";
			$courses->where('batch.type', '=', $type);
		} */

        $courses = $courses->paginate(10);
		
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (10*($page-1));
			
			$pageNumber = $pageNumber +1;
		}

        return view('studiomanager.course.index', compact('courses','pageNumber','params'));       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		
        if(!in_array(Auth::user()->id,[5126,8866,5603,5883])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }

		$all_cat_name	=	$this->getMainCategory();
		foreach($all_cat_name->data as $val){
			 $category_name[] =	$val->main_category;
		}
		
		$category_name=array_unique($category_name,SORT_STRING);
		$category_name=array_values($category_name);
		
        return view('studiomanager.course.add',compact('category_name'));
    }
	
	public function getMainCategory(){		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getReportCategories',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $res = json_decode($response);
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
            'name' => 'required',
        ]);
		
		$subject_id = array();
		if(isset($request->subjects) && is_array($request->subjects)){
			$subjects_name = $request->subjects;
			$i = 0;
			foreach($subjects_name as $key => $value){
				if(!empty($value)){                          
					$faculty_subjects = array(                  
						'name'=>$value,
						'status'=>1,                 
						'created_at'=>date('Y-m-d H:i:s'),                 
						'updated_at'=>date('Y-m-d H:i:s')                
					);                    
					$insert_id = DB::table('subject')->insertGetId($faculty_subjects,'id');
					$i++;
					$subject_id[] = $insert_id;
					
				}
			}
		}
		
		if(!empty($request->subject_id)){
			$request_subject_id = array_merge($request->subject_id,$subject_id);
		}
		else{
			$request_subject_id = $subject_id;
		}
        $inputs = $request->only('name','category','status');        

        $course = Course::create($inputs);

        //if(is_array($request->subject_id)){//
        if(is_array($request_subject_id)){//dk
            $course->course_subjects()->attach($request_subject_id);
        }

        if ($course->save()) {
            return redirect()->route('studiomanager.course.index')->with('success', 'Course Added Successfully');
        } else {
            return redirect()->route('studiomanager.course.index')->with('error', 'Something Went Wrong !');
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
		if(!in_array(Auth::user()->id,[5126,8866,7459])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }
        
		$subject_course = array();
        // $course = Course::with('course_subjects')->find($id);
		// if($id==271){
			$course = Course::find($id);
			$subject_course = DB::table('course')
                          ->select('course.*','course_subject_relations.subject_id')
						  ->leftJoin('course_subject_relations', 'course_subject_relations.course_id', '=', 'course.id')
						  ->where('course.id', $id)
						  ->where('course_subject_relations.is_deleted', '0')
						  ->get();
			$subject_course = json_decode($subject_course,true);
			// echo "<pre>"; print_r($subject_course); die;
		// }
        return view('studiomanager.course.edit', compact('course','subject_course'));
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
            'name' => 'required',
        ]);

        $course = Course::where('id', $id)->first();

        $inputs = $request->only('name','status');
		
		// print_r($request->subject_id);
		// die();
		
        if(is_array($request->subject_id)){
            //New update array disable 
			DB::table('course_subject_relations')
            ->where('course_id', $id)
            ->whereNOTIN('subject_id',$request->subject_id)
            ->update(['is_deleted' => '1']);
			
			//New update array enable
			DB::table('course_subject_relations')
            ->where('course_id', $id)
            ->whereIN('subject_id',$request->subject_id)
            ->update(['is_deleted' => '0']);
			
            //$course->course_subjects()->attach($request->subject_id);
            $course->course_subjects()->syncWithoutDetaching($request->subject_id);
        }else{
			DB::table('course_subject_relations')->where('course_id', $id)->update(['is_deleted' => '1']);
        }       

        if ($course->update($inputs)) {
            return redirect()->route('studiomanager.course.index')->with('success', 'Course Updated Successfully');
        } else {
            return redirect()->route('studiomanager.course.index')->with('error', 'Something Went Wrong !');
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
        $course = Course::find($id);
		$inputs = array('is_deleted' => '1');
        //CourseSubjectRelation::where('course_id', $id)->delete();

        if ($course->update($inputs)) {
            return redirect()->back()->with('success', 'Course Deleted Successfully');
        } else {
            return redirect()->route('studiomanager.course.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function import(){   
    	die('Not Working');
        return view('studiomanager.course.import');       
    }

    public function import_store(Request $request)
    {
		die('Not Working');
		$validatedData = $request->validate([
			'course_id' => 'required',
			'import_file' => 'required',
		]);
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}

		$subjects_name = array();
        $course_id = $request->course_id;
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->get();
		if(!empty($relation_subjects)){
			foreach ($relation_subjects as $details) 
			{
				if(!empty($details)){
					$subjects = DB::table('subject')
						->select('*')
						->where('id', $details->subject_id)
						->where('status', 1)
						->first();
					if(!empty($subjects)){
						$subjects_name[] = $subjects->name;
					}
				}
			}
		}			
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
		$errors_row = "";
		// echo "<pre>";print_r($import[0]); die;
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) {
				if (empty($value)) {
					continue;
				}
				
				$newArray = [
					// 'course_name' => $value[1],
					'subject_name' => $value[0],
					'chapter_name' => $value[1],
					// 'topic_name' => $value[2],
					'duration' => $value[2]
				];
				array_push($result, $newArray);
				
				if(!in_array($value[0],$subjects_name)){
					$conditions = false;
					$errors_row .= ($key+1).", ";
				}
				
			}
			$errors_row = rtrim($errors_row,", ");
			// echo $conditions; die;
			
			if(!$conditions){
				return redirect()->route('studiomanager.courses.import')->with('error', "Subject not exists rows $errors_row !");
			}
		}
		else{
			return redirect()->route('studiomanager.courses.import')->with('error', "Something went wrong !");
		}
		
		$not_inserted_row_msg = "";
		foreach ($import[0] as $key => $value) {
			
			$chk_subject = Subject::where('name', $value[0])->first();
			$subject_id = $chk_subject->id;
			
			$chk_chapter = Chapter::where('course_id', $course_id)->where('subject_id', $subject_id)->where('name', $value[1])->first();
			if(!empty($chk_chapter)){
				$chapter_id = $chk_chapter->id;
			}else{
				$chapter = Chapter::create([
					'course_id' => $course_id,
					'subject_id' => $subject_id,
					'name' => $value[1],
					'duration' => $value[2],
					'status' => 1,
				]);
				$chapter_id = $chapter->id;
				
			}
			
			/* $chk_topic = Topic::where('course_id', $course_id)->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)->where('name', $value[2])->first();
			if(!empty($chk_topic)){
				$topic_id = $chk_topic->id;
			}else{
				$topic = Topic::create([
					'course_id' => $course_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'name' => $value[2],
					'duration' => $value[3],
					'status' => 1,
				]);
				
			} */

		}    

        return back()->with('success', 'Excel Data Imported successfully.');       
    }
	
	
	public function import_store_old(Request $request)
    {
		$validatedData = $request->validate([
			'course_id' => 'required',
			'import_file' => 'required',
		]);
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}

        
        $path = $file->path();

        $import = Excel::toArray(null, $file);
        $stArr = $import[0][0];
        unset($import[0][0]);
        $result = [];
        foreach ($import[0] as $key => $value) {
            if (empty($value)) {
                continue;
            }
            
            $newArray = [
                'course_name' => $value[1],
                'subject_name' => $value[2],
                'chapter_name' => $value[3],
                'topic_name' => $value[4],
                'duration' => $value[5]
            ];
            array_push($result, $newArray);
        }

        if(isset($result) && is_array($result)){
            foreach ($result as $key => $course) {

                $chk_course = Course::where('name', $course['course_name'])->first();

                if(!empty($chk_course)){
                    echo 'Course Exists';
                }else{
                    $course_data = Course::create([
                        'name' => $course['course_name'],
                        'status' => 1,
                    ]);
                }

                $chk_subject = Subject::where('name', $course['subject_name'])->first();

                if(!empty($chk_subject)){
                    echo 'Subject Exists';
                }else{
                    $subject = Subject::create([
                        'name' => $course['subject_name'],
                        'status' => 1,
                    ]);
                }

                $chk_coursesubjectrelation = CourseSubjectRelation::where('course_id', $course_data->id)->where('subject_id', $subject->id)->first();

                if(!empty($chk_coursesubjectrelation)){
                    echo 'Course Subject Relations Exists';
                }else{
                    $course_subject_relation = CourseSubjectRelation::create([
                        'course_id' => $course_data->id,
                        'subject_id' => $subject->id,
                    ]);
                }             

                $chk_chapter = Chapter::where('name', $course['chapter_name'])->first();

                if(!empty($chk_chapter)){
                    echo 'Chapter Exists';
                }else{
                    $chapter = Chapter::create([
                        'course_id' => $course_data->id,
                        'subject_id' => $subject->id,
                        'name' => $course['chapter_name'],
                        'status' => 1,
                    ]);
                }

                $topic = Topic::create([
                    'course_id' => $course_data->id,
                    'subject_id' => $subject->id,
                    'chapter_id' => $chapter->id,
                    'name' => $course['topic_name'],
                    'duration' => $course['duration'],
                    'status' => 1,
                ]);

                // $chk_topic = Topic::where('name', $course['topic_name'])->first();

                // if(!empty($chk_topic)){
                //     echo 'Topic Exists';
                // }else{
                
                // }
            }
        }        

        return back()->with('success', 'Excel Data Imported successfully.');       
    }
	
	public function togglePublish($id) {
        $course = Course::find($id);
        if (is_null($course)) {
            return redirect()->route('studiomanager.course.index')->with('error', 'Course not found');
        }
        try {
            $course->update([
                'status' => !$course->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('studiomanager.course.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function view($course_id)
    {
		// $totalSubejctHour = DB::table('course')
							// ->leftJoin('course_subject_relations','course_subject_relations.course_id','course.id')
							// ->leftJoin('subject','subject.id','course_subject_relations.subject_id')
							// ->leftJoin('topic','topic.subject_id','subject.id')
							// ->select('course.id','course.name','subject.name as sname',DB::raw("SUM(topic.duration) AS new_duration"))
							// ->where('course.id', $course_id)
							// ->where('topic.course_id', $course_id)
							// ->groupBy('subject.id')
							// ->where('course_subject_relations.is_deleted', '0')
							// ->get();
		
		$totalSubejctHour = DB::table('topic')							
							->leftJoin('subject','subject.id','topic.subject_id')
							->leftJoin('course','course.id','topic.course_id')
							->select('course.id','course.name','subject.name as sname',DB::raw("SUM(topic.duration) AS new_duration"))
							->where('topic.course_id', $course_id)
							->groupBy('subject.id')
							->where('topic.is_deleted', '0')
							->get();
		
		
		$send_array = array();
		$course = DB::table('course')
						->select('*')
						->where('id', $course_id)
						->first();
						
		$course_id = $course->id;
		$course_name = $course->name;
		
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->where('is_deleted', '0')
                    ->get();
		if(!empty($relation_subjects)){
			$i = 0;
			foreach ($relation_subjects as $details) 
			{
				if(!empty($details)){
					$subjects = DB::table('subject')
						->select('*')
						->where('id', $details->subject_id)
						->where('status', 1)
						->first();
					if(!empty($subjects)){
						$subject_name = $subjects->name;
						$subject_id = $subjects->id;
						$chapter = DB::table('chapter')
							->select('*')
							->where('course_id', $course_id)
							->where('subject_id', $subject_id)
							->where('status', 1)
							->get();
						if(!empty($chapter) && count($chapter) > 0){
							foreach ($chapter as $Cdetails){
								$chapter_name = $Cdetails->name;
								$chapter_id = $Cdetails->id;
								$topic = DB::table('topic')
									->select('*')
									->where('course_id', $course_id)
									->where('subject_id', $subject_id)
									->where('chapter_id', $chapter_id)
									->where('status', 1)
									->get();
								if(!empty($topic) && count($topic) > 0){
									foreach ($topic as $Tdetails){
										$i++;
										$row = array();
										$row['s_no'] = $i;
										$row['course_id'] = $course_id;
										$row['course_name'] = $course_name;
										$row['subject_name'] = $subject_name;
										$row['chapter_name'] = $chapter_name;
										$row['topic_name'] = $Tdetails->name;
										$row['topic_id'] = $Tdetails->id;
										$row['duration'] = $Tdetails->duration;
										
										$send_array[] = $row;
									}
								}
								else{
									$i++;									
									$row['s_no'] = $i;
									$row['course_id'] = $course_id;
									$row['course_name'] = $course_name;
									$row['subject_name'] = $subject_name;
									$row['chapter_name'] = $chapter_name;
									$row['topic_name'] = '';
									$row['topic_id'] = '';
									$row['duration'] = '';
									$send_array[] = $row;
								}
							}
						}
						else{
							$i++;
							$row = array();
							$row['s_no'] = $i;
							$row['course_id'] = $course_id;
							$row['course_name'] = $course_name;
							$row['subject_name'] = $subject_name;
							$row['chapter_name'] = '';
							$row['topic_name'] = '';
							$row['topic_id'] = '';
							$row['duration'] = '';
							$send_array[] = $row;
						}
					}
				}
				
			}
		}
		
		
		$new_array =  DB::table('topic')							
							->leftJoin('chapter','chapter.id','topic.chapter_id')
							->leftJoin('subject','subject.id','topic.subject_id')							
							->leftJoin('course','course.id','topic.course_id')
							->select('course.id as course_id','course.name as course_name','chapter.name as chapter_name','subject.name as subject_name','topic.name as topic_name','topic.id as topic_id','topic.duration AS duration')
							->where('topic.course_id', $course_id)
							->where('topic.is_deleted', '0')
							->get();
							
		$course_planner  = DB::table('planner_request')->where('course_id',$course_id)->get();
		
        return view('studiomanager.course.view', compact('send_array','totalSubejctHour','new_array','course_planner','course_id'));
    }
	
	public function download_sample()
    {
		$course_id =  $_GET['course_id'];
		$course = DB::table('course')
						->select('*')
						->where('id', $course_id)
						->first();
						
		// echo "<pre>";
		$course_name = $course->name;
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->where('is_deleted', '0')
                    ->get();
		if(!empty($relation_subjects)){
			$i = 0;
			$row = array();
			foreach ($relation_subjects as $details) 
			{
				if(!empty($details)){
					$subjects = DB::table('subject')
						->select('*')
						->where('id', $details->subject_id)
						->where('status', 1)
						->first();

					if(!empty($subjects)){
						$subject_name = $subjects->name;
						$subject_id = $subjects->id;
						
						$i++;
						$roww = array();
						// $row[] = $i;
						$roww[] = $subject_name;
						$roww[] = '';
						$roww[] = '';
						$roww[] = '';
						array_push($row,$roww);

						//fputcsv($fp, $row); //Arvind
						/*$chapter = DB::table('chapter')
							->select('*')
							->where('course_id', $course_id)
							->where('subject_id', $subject_id)
							->where('status', 1)
							->get();
						if(!empty($chapter) && count($chapter) > 0){
							foreach ($chapter as $Cdetails){
								$chapter_name = $Cdetails->name;
								$chapter_id = $Cdetails->id;
								$topic = DB::table('topic')
									->select('*')
									->where('course_id', $course_id)
									->where('subject_id', $subject_id)
									->where('chapter_id', $chapter_id)
									->where('status', 1)
									->get();
								if(!empty($topic) && count($topic) > 0){
									foreach ($topic as $Tdetails){
										$i++;
										$row = array();
										$row[] = $i;
										// $row[] = $course_name;
										$row[] = $subject_name;
										$row[] = $chapter_name;
										$row[] = $Tdetails->name;
										$row[] = $Tdetails->duration;
										fputcsv($fp, $row);
									}
								}
								else{
									$i++;
									$row = array();
									$row[] = $i;
									// $row[] = $course_name;
									$row[] = $subject_name;
									$row[] = $chapter_name;
									$row[] = '';
									$row[] = '';
									fputcsv($fp, $row);
								}
							}
						}
						else{
							$i++;
							$row = array();
							$row[] = $i;
							// $row[] = $course_name;
							$row[] = $subject_name;
							$row[] = '';
							$row[] = '';
							$row[] = '';
							fputcsv($fp, $row);
						}*/
					}
					else{
						return redirect()->route('studiomanager.courses.import')->with('error', 'Not Found Subject !');
					}
				}
				
			} 

			return Excel::download(new CourseBySubjectExport($row), 'CourseBySubjectData.xlsx');
			
		}
		if($i==0){
			$row = array();
			$row[] = "English";
			$row[] = "Grammer";
			$row[] = "Article";
			$row[] = "50";
			
			return Excel::download(new CourseBySubjectExport($row), 'CourseBySubjectData.xlsx');
		}
    }
	
	public function export_csv($course_id)
    {
		$course = DB::table('course')
						->select('*')
						->where('id', $course_id)
						->first();
						
		// echo "<pre>";
		$course_name = $course->name;
        $filename = "course-details-".date('d-m-Y-h-i-s').".csv";
		$fp = fopen('php://output', 'w');
		$header = array('SR No.','Subject Name','Topic Name','Sub Topic Name','Duration');
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		fputcsv($fp, $header);
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->where('is_deleted', '0')
                    ->get();
		if(!empty($relation_subjects)){
			$i = 0;
			foreach ($relation_subjects as $details) 
			{
				if(!empty($details)){
					$subjects = DB::table('subject')
						->select('*')
						->where('id', $details->subject_id)
						->where('status', 1)
						->first();
					if(!empty($subjects)){
						$subject_name = $subjects->name;
						$subject_id = $subjects->id;
						$chapter = DB::table('chapter')
							->select('*')
							->where('course_id', $course_id)
							->where('subject_id', $subject_id)
							->where('status', 1)
							->get();
						if(!empty($chapter) && count($chapter) > 0){
							foreach ($chapter as $Cdetails){
								$chapter_name = $Cdetails->name;
								$chapter_id = $Cdetails->id;
								$topic = DB::table('topic')
									->select('*')
									->where('course_id', $course_id)
									->where('subject_id', $subject_id)
									->where('chapter_id', $chapter_id)
									->where('status', 1)
									->get();
								if(!empty($topic) && count($topic) > 0){
									foreach ($topic as $Tdetails){
										$i++;
										$row = array();
										$row[] = $i;
										// $row[] = $course_name;
										$row[] = $subject_name;
										$row[] = $chapter_name;
										$row[] = $Tdetails->name;
										$row[] = $Tdetails->duration;
										fputcsv($fp, $row);
									}
								}
								else{
									$i++;
									$row = array();
									$row[] = $i;
									// $row[] = $course_name;
									$row[] = $subject_name;
									$row[] = $chapter_name;
									$row[] = '';
									$row[] = '';
									fputcsv($fp, $row);
								}
							}
						}
						else{
							$i++;
							$row = array();
							$row[] = $i;
							// $row[] = $course_name;
							$row[] = $subject_name;
							$row[] = '';
							$row[] = '';
							$row[] = '';
							fputcsv($fp, $row);
						}
					}
				}
				
			}
		}
		if($i==0){
			$row = array();
			$row[] = 1;
			// $row[] = "Patwar";
			$row[] = "English";
			$row[] = "Grammer";
			$row[] = "Article";
			$row[] = "50";
			
			fputcsv($fp, $row);
		}
    }
	
	public function chapter_topic_view(Request $request,$tid){
		$topic = DB::table('topic')->where('id',$tid)->first();
		return view('studiomanager.course.ctupdate',compact('topic'));
	}
	
	public function chapter_topic_update(Request $request){
		
		$tname = $request->tname;
		$tduration = $request->tduration;
		$topic_id = $request->topic_id;
		
		if(!empty($tname) && !empty($tname) && !empty($tname)){		
			$data = array(
				"name"	=>	$tname,
				"duration"	=>	$tduration,
				"status"	=>	$request->status??1,
			);
			
			DB::table('topic')->where('id',$topic_id)->update($data);
			return redirect()->back()->with('success', 'Topic Updated Successfully');
		}else{
			return redirect()->back()->with('error', 'Required filed missing!!');
		}
	}
}
