<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chapter;
use Input;
use DB;
use Excel;
use Auth;

class ChapterController extends Controller
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
        $name = Input::get('name');
        $status = Input::get('status');

        $chapters = Chapter::with('course','subject')->orderBy('id', 'desc');
			$chapters->WhereHas('course', function ($q) {	
				$q->where('is_deleted','0');							
				$q->where('status',1);							
			});
			$chapters->WhereHas('subject', function ($q) {	
				$q->where('is_deleted','0');							
				$q->where('status',1);							
			});

        if (!empty($course_id)){
            $chapters->where('course_id', $course_id);
        }

        if (!empty($subject_id)){
            $chapters->where('subject_id', $subject_id);
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $chapters->where('status', '=', '0');
            }else{
                $chapters->where('status', '=', '1');
            }
        }

        $chapters->where('is_deleted', '0');
        $chapters = $chapters->paginate(50);
		
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		}

        return view('studiomanager.chapter.index', compact('chapters','pageNumber','params'));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!in_array(Auth::user()->id,[5126,8866])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }
        
        return view('studiomanager.chapter.add');
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
			'subject_id' => 'required',
            'name' => 'required',
        ]);
	
		$chk_chapter = Chapter::where('course_id', $request->course_id)->where('subject_id', $request->subject_id)->where('name', $request->name)->first();
		if(!empty($chk_chapter)){
			return redirect()->route('studiomanager.chapters.create')->with('error', 'Chapter name already exits !');
		}
		else{
			$inputs = $request->only('course_id','subject_id','name','duration', 'status');        

			$chapter = Chapter::create($inputs);    

			if ($chapter->save()) {
				return redirect()->route('studiomanager.chapters.index')->with('success', 'Chapter Added Successfully');
			} else {
				return redirect()->route('studiomanager.chapters.create')->with('error', 'Something Went Wrong !');
			}
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
        if(!in_array(Auth::user()->id,[5126,5603,8866])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }
        

        $chapter = Chapter::with('subject')->find($id);
        return view('studiomanager.chapter.edit', compact('chapter'));
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
			'subject_id' => 'required',
            'name' => 'required',
        ]);

        $chapter = Chapter::where('id', $id)->first();

        $inputs = $request->only('course_id','subject_id','name','duration', 'status');       

        if ($chapter->update($inputs)) {
            return redirect()->route('studiomanager.chapters.index')->with('success', 'Chapter Updated Successfully');
        } else {
            return redirect()->route('studiomanager.chapters.index')->with('error', 'Something Went Wrong !');
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
        $chapter = Chapter::find($id);
		$inputs = array('is_deleted' => '1','status' => '0');

        if ($chapter->update($inputs)) {
            return redirect()->back()->with('success', 'Chapter Deleted Successfully');
        } else {
            return redirect()->route('studiomanager.chapters.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function import()
    {
        if(!in_array(Auth::user()->id,[5126,8866])){
           die('You can not add course. Contact to HADMAN DAN on : 8769071387');
        }
        
        return view('studiomanager.chapter.import');       
    }

    public function import_store(Request $request)
    {
		$validatedData = $request->validate([
			'course_id' => 'required',
			'subject_id' => 'required',
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
        $subject_id = $request->subject_id;
		$relation_subjects = DB::table('course_subject_relations')
                    ->select('*')
                    ->where('course_id', $course_id)
                    ->where('subject_id', $subject_id)
                    ->get();
		
		if(!empty($relation_subjects)){
			$path = $file->path();
		
			$conditions = true;
			$import = Excel::toArray(null, $file);
			$stArr = $import[0][0];
			unset($import[0][0]);
			$result = [];
			$errors_row = "";
			// echo "<pre>";print_r($import[0]); die;
			if(!empty($import[0])){
				$i = 0;
				foreach ($import[0] as $key => $value) {
					if (empty($value)) {
						continue;
					}
					
					$chk_chapter = Chapter::where('course_id', $course_id)->where('subject_id', $subject_id)->where('name', $value[0])->first();
					if(!empty($chk_chapter)){
						
					}else{
						$i++;
						$chapter = Chapter::create([
							'course_id' => $course_id,
							'subject_id' => $subject_id,
							'name' => $value[0],
							'duration' => $value[1],
							'status' => 1,
						]);
						
					}
					
				}
				if($i ==0){
					return back()->with('error', "No any chapter imported. All chapter already exists.");  
				}
				else{
					return back()->with('success', "Total $i chapter import successfully.");
				}
				
				
			}
			else{
				return redirect()->route('studiomanager.chapter.import')->with('error', "Something went wrong !");
			}
		}
		else{
			return redirect()->route('studiomanager.chapter.import')->with('error', "Something went wrong !");
		}		
             
    }
	
	//dk
	public function togglePublish($id) {
        $chapter = Chapter::find($id);
        if (is_null($chapter)) {
            return redirect()->route('studiomanager.chapters.index')->with('error', 'Chapter not found');
        }
        try {
            $chapter->update([
                'status' => !$chapter->status,
                'updated_at' => new \DateTime(),
            ]);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('error', $this->getMessage($e));
        }
        return redirect()->route('studiomanager.chapters.index')->with('success', 'Status Updated Successfully.');
    }
}
