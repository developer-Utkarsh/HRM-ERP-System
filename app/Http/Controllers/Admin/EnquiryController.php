<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enquiry;
use App\EnquiryDescription;
use App\SupportCategory;
use App\Exports\EnquiryExport;
use Excel;
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
			$category_id = Input::get('category_id');

			$enquiry_result = Enquiry::select("enquiry.*",DB::raw('group_concat(enquiry_description.description," || ") as description'),"enquiry_description.user_id","users.name","support_category.name as cat_name")
			    ->leftJoin("users","enquiry.query_id","=","users.id")
			    ->leftJoin("enquiry_description","enquiry.id","=","enquiry_description.enquiry_id")
			    ->leftJoin("support_category","support_category.id","=","enquiry.category_id");

			$enquiry_result->where('enquiry.status', '!=', 'deleted');
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
			
			if(!empty($category_id)){
				$enquiry_result->where('enquiry.category_id', '=', $category_id);
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

			$enquiry_result = $enquiry_result->orderBy("enquiry_description.id",'desc')->groupBy("enquiry.id")->get();
			$category = SupportCategory::where('status', 'Active')->where('is_deleted', '0')->orderBy('name','ASC')->get();
			
			$excel_export = Input::get('excel_export');
            if(isset($excel_export)){
               return Excel::download(new EnquiryExport($enquiry_result), 'enquiry.xlsx'); 
            }else{
			 return view('admin.enquiry.index', compact('enquiry_result','category')); 
            }
			
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
			return view('admin.enquiry.add', compact('category_list'));
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
				
				return redirect()->route('admin.enquiry.index')->with('success', 'Enquiry Add Successfully');
			} else {
				return redirect()->route('admin.enquiry.index')->with('error', 'Something Went Wrong !');
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
		$check_add_enquiry = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
		if((!empty($check_add_enquiry) && (!empty($check_add_enquiry) && $check_add_enquiry->role == 'query')) || $check_add_enquiry->role == 'admin'){
			$category_list = SupportCategory::where("is_deleted","0")->where("status","Active")->get();
			$enquiry = DB::table('enquiry')->where("id",$id)->first();
			$enquiry_desc = DB::table('enquiry_description')->where("enquiry_id",$id)->first();
			return view('admin.enquiry.edit', compact('category_list','enquiry','enquiry_desc'));
		}
		else{
			return redirect()->back()->with('error', 'Access Denied');
		}
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
							'course_type' => $request->course_type,
							'mobile_no'   => $request->mobile_no,
							'course_name' => $request->course_name,
							'category_id' => $request->category_id,
							'priority'    => $request->priority
						);       

			$save_enquiry = DB::table("enquiry")->where('id',$id)->update($enquiryArr);    
 
			DB::table("enquiry_description")->where('enquiry_id',$id)->update(["description" => $request->description, "user_id" => $loginId]);
			
			// return redirect()->route('admin.enquiry.index')->with('success', 'Enquiry Update Successfully');
			return redirect()->back()->with('success', 'Enquiry Update Successfully');
			 
		}
		else{
			return redirect()->back()->with('error', 'Access Denied');
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
		$save_enquiry = DB::table("enquiry")->where('id',$id)->update(['status'=>'deleted']);
		return redirect()->back()->with('success', 'Enquiry delete successfully.');
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

			return redirect()->route('admin.enquiry.index')->with('success', 'Query Add Successfully');
		}
		else{
			return redirect()->route('admin.enquiry.create')->with('error', 'Description is required');
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
							<th>Created At</th>
							<th>Rating</th>
							<th>Rating Time</th>
						</tr>
					</thead>
					<tbody>";
						if(!empty($request->enq_id)){
							$old_query = DB::table("enquiry_description")->select("enquiry_description.description","users.name","enquiry_description.user_id","enquiry_description.created_at","enquiry_description.rating","enquiry_description.updated_at")->leftJoin("users","enquiry_description.user_id","=","users.id")->where("enquiry_description.enquiry_id", $request->enq_id)->get();
							
							if(count($old_query) > 0){
								foreach($old_query as $key=>$old_query_value){
									if($old_query_value->user_id==0){
                             $old_query_value->name="By Student";
									}else if($old_query_value->user_id == Auth::user()->id){
										$old_query_value->name.= '(You)';
									}

									$sno = $key+1;
									$res .= "<tr>
												<td>".$sno."</td>
												<td>".$old_query_value->name."</td>
												<td>".$old_query_value->description."</td>
												<td>".date("d-m-Y h:i:s",strtotime($old_query_value->updated_at))."</td>
												<td>".ucwords($old_query_value->rating)."</td>
												<td>".($old_query_value->rating!=''?date("d-m-Y h:i:s",strtotime($old_query_value->updated_at)):'')."</td>
											</tr>";
								}
							}
						}
						
			$res .= "</tbody>
				</table>";

			$category=DB::table('support_category')->where("status","Active")->where("is_deleted","0")->get();

			$enquiry=DB::table("enquiry")->where("id", $request->enq_id)->first();

			$old_category="";
			$res.='<div class="modal-body">
						<div class="form-body">
							<h4>Change Category</h4><hr>
							<select class="form-control" name="category">
								<option value="">Select</option>';

							foreach($category as $val){
                        $selected=" ";
								if($enquiry->category_id==$val->id){
                          $selected=" selected";
                          $old_category=$val->name;
								}

                        $res.='<option value="'.$val->id.'" '.$selected.'>'.$val->name.'</option>';
							}

			$res.=		'</select>
						</div>
					</div>';	
			$res.='<input type="hidden" name="old_category" value="'.$old_category.'">';

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
			return redirect()->route('admin.enquiry.index')->with('success', 'Query Add Successfully');
		}
		else{
			return redirect()->route('admin.enquiry.create')->with('error', 'Status is required');
		}
	}
}
