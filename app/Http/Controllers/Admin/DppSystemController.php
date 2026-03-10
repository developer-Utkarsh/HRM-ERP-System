<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Auth;
use App\Batch;
use DB;

class DppSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
		$branch_location = Input::get('branch_location');
        $batch_id        = Input::get('batch_id');
		$selectFromDate  = Input::get('fdate');
		
        $whereCond  = ' 1=1';
        if(!empty($selectFromDate)){
			$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'"';
		}
		else{
			$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
		}

        if(!empty($branch_location)){
			$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
		}

        if(!empty($batch_id)){
			$whereCond .= ' AND timetables.batch_id = "'.$batch_id.'"';
		}

        $get_dpp_records = DB::table('timetables')
						  ->select('batch.*','timetables.id as t_id','timetables.cdate','timetables.faculty_id','dpp_record.id as dpp_record_id','dpp_record.filename')
						  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
						  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
						  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
                          ->leftJoin('dpp_record', 'dpp_record.batch_id', '=', 'batch.id')
						  ->whereRaw($whereCond)
						  ->where('timetables.time_table_parent_id', '0')
						  ->where('timetables.is_deleted', '0')
						  //->groupBy('timetables.faculty_id')
                          ->groupBy('batch.id')
						  ->get();
        //echo '<pre>'; print_r($get_dpp_records);die;
		return view('admin.dpp.index', compact('get_dpp_records'));
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        $id = !empty($request->id) ? $request->id : '';
        $dp_data =  Batch::where('id', $request->b_id)->first();

        if (Input::hasfile('filename')){
            $inputs['filename'] = $this->uploadFile(Input::file('filename'), $id);
        }
        $inputs['date'] = date('Y-m-d');
        
        $export_batch_code = explode(",", $dp_data->batch_code);
        foreach($export_batch_code as $export_batch_code_val){
            if(!empty($request->id)){
                $dpp_result = DB::table('dpp_record')->where('batch_code', $export_batch_code_val)->update($inputs);
            }
            else{
                $inputs['batch_id']   = !empty($request->b_id) ? $request->b_id : '';
                $inputs['batch_code'] = $export_batch_code_val;
                $dpp_result = DB::table('dpp_record')->insert($inputs);
            }
        }
        
        if($dpp_result){
            return response(['status' => true, 'message' => 'File Upload Successfully.'], 200);
        }else{          
            return response(['status' => false, 'message' => 'Something Went Wrong'], 200);
        }
    }

    public function uploadFile($file, $id){
        $drive = public_path(DIRECTORY_SEPARATOR . 'dpp_record' . DIRECTORY_SEPARATOR);
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;    
        
        if(!empty($id)){
            $get_data = DB::table('dpp_record')->where('id', $id)->first();
            if(!empty($get_data->filename)){
                unlink($drive.$get_data->filename);
            }
        }
        $imgResource = $file->move($drive, $filename);
        return $filename;
 
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
	
	//dk
	public function togglePublish($id) {
        
    }
}
