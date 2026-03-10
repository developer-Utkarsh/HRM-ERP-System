<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enquiry;
use App\EnquiryDescription;
use App\SupportCategory;
use Input;
use Auth;
use DB;

class EnquiryController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$check_add_enquiry = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
		
		if(!empty($check_add_enquiry)){
			$loginId  = Auth::user()->id;
			$mobile_no = Input::get('mobile_no');
			$course_type = Input::get('course_type');
			$priority = Input::get('priority');
			$status = Input::get('status');

			$enquiry_result = Enquiry::select("enquiry.*","enquiry_description.description","enquiry_description.user_id","users.name")->leftJoin("users","enquiry.query_id","=","users.id")->leftJoin("enquiry_description","enquiry.id","=","enquiry_description.enquiry_id");

			if (!empty($mobile_no)){
				$enquiry_result->where('enquiry.mobile_no', '=', $mobile_no);
			}

			if(!empty($course_type)){
				$enquiry_result->where('enquiry.course_type', '=', $course_type);
			}
			
			if(!empty($priority)){
				$enquiry_result->where('enquiry.priority', '=', $priority);
			}
			
			if(!empty($status)){
				$enquiry_result->where('enquiry.status', '=', $status);
			}
			
			
			$check_support_admin = DB::table("support_user")->where("user_id", Auth::user()->id)->where('role', 'admin')->first();
			if(empty($check_support_admin)){
				if(!empty($check_add_enquiry->category_id)){ 
					$explode_cat = explode(",", $check_add_enquiry->category_id);
					$enquiry_result->whereIn('enquiry.category_id', $explode_cat);
					$enquiry_result->orWhere("enquiry_description.user_id", $loginId);
				}
				else{
					$enquiry_result->where("enquiry_description.user_id", $loginId);
				}
			}

			$enquiry_result = $enquiry_result->orderBy("enquiry_description.id")->groupBy("enquiry.id")->get();

			return view('studiomanager.enquiry.index', compact('enquiry_result'));
		}
		else{
			return redirect()->back()->with('error', 'Access Denied');
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$check_add_enquiry = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
		if((!empty($check_add_enquiry) && (!empty($check_add_enquiry) && $check_add_enquiry->role == 'query')) || $check_add_enquiry->role == 'admin'){
			$category_list = SupportCategory::where("is_deleted","0")->where("status","Active")->get();
			return view('studiomanager.enquiry.add', compact('category_list'));
		}
		else{
			return redirect()->back()->with('error', 'Access Denied');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$check_add_enquiry = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
		if((!empty($check_add_enquiry) && (!empty($check_add_enquiry) && $check_add_enquiry->role == 'query')) || $check_add_enquiry->role == 'admin'){
			$loginId  = Auth::user()->id;
			$validatedData = $request->validate([
				'course_type'     => 'required',
				'mobile_no'       => 'required|numeric|digits:10',
				'course_name'     => 'required',
				'category_id'     => 'required',
				'priority'     => 'required',
				'description'     => 'required',
			]);

			$enquiryArr = array(
							'query_id'    => $loginId,
							'course_type' => $request->course_type,
							'mobile_no'   => $request->mobile_no,
							'course_name' => $request->course_name,
							'category_id' => $request->category_id,
							'priority'    => $request->priority,
							'status'      => 'pending',
							'date'        => date("Y-m-d"),
							"created_at"  => date("Y-m-d H:i:s")
						);       

			$save_enquiry = DB::table("enquiry")->insertGetId($enquiryArr);    

			if(!empty($save_enquiry)){
				DB::table("enquiry_description")->insertGetId(["enquiry_id" => $save_enquiry, "description" => $request->description, "user_id" => $loginId, "created_at" => date("Y-m-d H:i:s")]);
				
				return redirect()->route('studiomanager.enquiry.index')->with('success', 'Query Add Successfully');
			} else {
				return redirect()->route('studiomanager.enquiry.index')->with('error', 'Something Went Wrong !');
			}
		}
		else{
			return redirect()->back()->with('error', 'Access Denied');
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
	
	public function store_enquiry_description(Request $request){
		if(!empty($request->description)){
			$loginId  = Auth::user()->id;
			$check_status = Enquiry::where("id", $request->enquiry_id)->first(); 
			if(!empty($check_status) && $check_status->status == 'pending'){
				$sts = 'resolved'; 
			}
			if(!empty($check_status) && $check_status->status == 'resolved'){
				$sts = 'in_progress';
			}
			if(!empty($check_status) && $check_status->status == 'in_progress'){
				$sts = 'resolved';
			}
			
			Enquiry::where("id",$request->enquiry_id)->update([ 'status' => $sts]);
			DB::table("enquiry_description")->insertGetId(["enquiry_id" => $request->enquiry_id, "description" => $request->description, "user_id" => $loginId, "created_at" => date("Y-m-d H:i:s")]);

			return redirect()->route('studiomanager.enquiry.index')->with('success', 'Query Add Successfully');
		}
		else{
			return redirect()->route('studiomanager.enquiry.create')->with('error', 'Description is required');
		}
	}
	
	public function get_old_query(Request $request){
		$res = "";
		$res .= "<table class='table table-striped'>
					<thead>
						<tr>
							<th>S. No.</th>
							<th>User Name</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>";
						if(!empty($request->enq_id)){
							$old_query = DB::table("enquiry_description")->select("enquiry_description.description","users.name","users.id as user_id")->leftJoin("users","enquiry_description.user_id","=","users.id")->where("enquiry_description.enquiry_id", $request->enq_id)->get();
							
							if(count($old_query) > 0){
								foreach($old_query as $key=>$old_query_value){
									$query_name = '';
									if($old_query_value->user_id == Auth::user()->id){
										$query_name = '(You)';
									}
									$sno = $key+1;
									$res .= "<tr>
												<td>".$sno."</td>
												<td>".$old_query_value->name." ".$query_name."</td>
												<td>".$old_query_value->description."</td>
											</tr>";
								}
							}
						}
						
			$res .= "</tbody>
				</table>";	

		echo $res;
        exit();
	}
	
	public function store_enquiry_status(Request $request){ 
		if(!empty($request->status)){
			$loginId      = Auth::user()->id;
			$check_status = Enquiry::where("id", $request->enquiry_id)->first(); 
			$sts          = $request->status;
			
			
			Enquiry::where("id",$request->enquiry_id)->update([ 'status' => $sts]);
			
			if($request->status == 'resolved'){
				DB::table("enquiry_description")->insertGetId(["enquiry_id" => $request->enquiry_id, "description" => $request->status, "user_id" => $loginId, "created_at" => date("Y-m-d H:i:s")]);
			}
			return redirect()->route('studiomanager.enquiry.index')->with('success', 'Query Add Successfully');
		}
		else{
			return redirect()->route('studiomanager.enquiry.create')->with('error', 'Status is required');
		}
	}
}
