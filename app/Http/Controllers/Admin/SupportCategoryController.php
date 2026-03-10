<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SupportCategory;
use App\Enquiry;
use App\ForumQuestion;
use Input;
use Validator;
use DB;
use Auth;


class SupportCategoryController extends Controller
{
    
    public function index()
    {
  	   /*$nt_data['content']="Your query is resolved! ✅ Ticket #34 is now RESOLVED. Rate your experience here!🚀";
       $nt_data['title']='Message to Chairman';
       $nt_data['url']="https://form.utkarsh.com/uploads/offline-support/dashboard.php?reg_no=123&bid=123&mobile=70141555376";
       $nt_data['mobile']="7014155376";
	   $this->app_notification($nt_data);die;*/

	    $name   = Input::get('name');
        $category = SupportCategory::where('is_deleted', '0')->orderBy('id', 'desc')->where('name', 'LIKE', '%' . $name . '%')->get();
        return view('admin.support_category.category', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:support_category,name,'.$request->id,
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.support_category.index')->with('error', 'Category Already Exits');
		}
		
		$inputs = $request->only('name'); 
		
		if(!empty($request->id)){
			$categoryId  = SupportCategory::where('id', $request->id)->first();
			$cat_res  = $categoryId->update($inputs);
			$msg = "Category Update Successfully";
		}
		else{
			$category = SupportCategory::create($inputs); 
			$cat_res  = $category->save();
			$msg = "Category Added Successfully";
		}
    

        if($cat_res) {
            return redirect()->route('admin.support_category.index')->with('success', $msg);
        } else {
            return redirect()->route('admin.support_category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $category = SupportCategory::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('admin.support_category.index')->with('error', 'Something Went Wrong !');
        }
    }

	
	public function edit_category(Request $request){
		$cat_id  = $request->cat_id;  
        $respnse = SupportCategory::where([['id', '=', $cat_id]])->first();
		
        if (!empty($respnse))
        {
            $res = "";
			$res .= "<label>Category:</label><input type='text' name='name' class='form-control' value='" . $respnse->name . "'><input type='hidden' name='id' class='form-control' value='" . $respnse->id . "'>";
            
			echo $res;
            exit();
        }
        else
        {
            echo $res = "<label>Category:</label>";
            die();
        }
	}

    public function togglePublish($id) {
        $support_category = SupportCategory::find($id);
        if (is_null($support_category)) {
            return redirect()->route('admin.support_category.index')->with('error', 'Category not found');
        }
		
		if($support_category->status == 'Active'){
			$sts = 'Inactive';
		}
		else{
			$sts = 'Active';
		}
		
		$support_category->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.support_category.index')->with('success', 'Status Updated Successfully.');
    }
	
	public function support_dashboard(Request $request){
		
		$data=[];

		$where=$this->filter($request);

		//$active_query = Enquiry::where("status", "PENDING")->whereRAW($where)->count();

		$support_category = SupportCategory::where("status","Active")->where("is_deleted","0")->get();
		foreach ($support_category as $key=>$val){
			$ticket_count = Enquiry::select(DB::RAW('status,count(status) as total'))->whereRAW($where)->where('category_id',$val->id)->groupby('status')->get();
			$val->ticket_count=$ticket_count;
			$data[]=$val;
		}
		return view('admin.support_category.dashboard', compact('data'));
	}

	public function enquiry(Request $request){
		$enquiry=[];
		$where=$this->filter($request);
	    $enquiry=Enquiry::select("enquiry.*",
			"support_category.name as cat",
			"branches.name as branch_name",
			"batch.name as batch_name",
			"enquiry_description.description"
		    )
		    ->leftJoin("support_category","support_category.id","=","enquiry.category_id")
		    ->leftJoin("branches","branches.id","enquiry.branch_id")
		    ->leftJoin("batch","batch.id","enquiry.batch_id")
		    ->leftJoin('enquiry_description', function($query) 
			{
			   $query->on('enquiry.id','enquiry_description.enquiry_id')
			   ->whereRaw('enquiry_description.id IN (select MAX(a2.id) from enquiry_description as a2 join enquiry as u2 on u2.id = a2.enquiry_id group by u2.id)');
			})
		    ->whereRAW($where)
		    ->orderBy('enquiry.status','ASC')
		    ->paginate(10);
		return view('admin.support_category.enquery', compact('enquiry'));
	}

	public function enquiryReply(Request $request){
		if(!empty($request->description)){
			$loginId  = Auth::user()->id;
			
			$enquiry=Enquiry::where("id",$request->enquiry_id)->first();
			if($request->category!=$enquiry->category_id){
				$request->description=$request->description.", ## Category Changed from -".$request->old_category;
			    $enquiry->update([ 'category_id' =>$request->category]);
			}else{
			   $enquiry->update([ 'status' =>'replied']);
			}

			DB::table("enquiry_description")->insertGetId(["enquiry_id" => $request->enquiry_id, "description" => $request->description, "user_id" => $loginId, "created_at" => date("Y-m-d H:i:s")]);

			//notification to user app
			/*$title=urlencode("Your Support Ticket Number ".$enquiry->id." has been resolved");
			$description=urlencode($request->description);
			$batch_id=$enquiry->batch_code;
			file_get_contents("https://courses.utkarsh.com/utkarshadm/offlineapp/includes/notification.php?reg_no=$enquiry->reg_no&mobile_no=$enquiry->mobile_no&title=$title&description=$description&batch_id=$batch_id&data_type=support");*/
            
            //update ticket status after every 48 hours
            $updated_at=date('Y-m-d H:i:s',strtotime(now().' -48 hours'));
			$list=Enquiry::where("status",'replied')->where('updated_at','<',$updated_at)->get();
			foreach($list as $val){
               $nt_data['content']="Your query is resolved! ✅ Ticket #$val->id is now RESOLVED. Rate your experience here!🚀";
               $nt_data['title']='Message to Chairman';
               $nt_data['url']="https://form.utkarsh.com/uploads/offline-support/dashboard.php?history=Yes&reg_no=$enquiry->reg_no&bid=$enquiry->batch_code&mobile=$enquiry->mobile_no";
               $nt_data['mobile']=$enquiry->mobile_no;
               //$nt_data['mobile']="7014155376";
			   $this->app_notification($nt_data);
			   Enquiry::where("id",$val->id)->update(['status'=>'resolved']);
			}

			//return redirect()->back()->with('success', 'Query Replied Successfully');
			return response(['status'=>true,'msg'=>'Query Replied Successfully']);
		}else{
			//return redirect()->back()->with('error', 'Description is required');
			return response(['status'=>false,'msg'=>'Description is required']);

		}
	}



	public function filter(Request $request){
		$where='1=1';

		$user=Auth::user();
		if(!empty($user)){
			$role_id=$user->role_id;

			if($role_id==2800){
				$user_branches=$user->user_branches;
				$branch_id=0;
				foreach($user_branches as $val){
		          $branch_id.=",".$val->branch_id;
				}

				$where.=" AND enquiry.branch_id IN (".$branch_id.")";
			}else if($role_id==3){
	          $where.=" AND enquiry.assistant_id=".$user->id;
			}else{

				$check_add_enquiry=DB::table("support_user")
				->where("user_id", $user->id)->first();
				if(!empty($check_add_enquiry)){
					$explode_cat = $check_add_enquiry->category_id;
					$where.=" AND enquiry.category_id IN (".$explode_cat.")";

					if(!empty($check_add_enquiry->city)){
                      $where.=" AND enquiry.location IN (".$check_add_enquiry->city.")";
					}

				}else{
					$where.=" AND enquiry.category_id IN (999999)";
				}
			}
		}



		if(!empty($request->reg_no)){
			$reg_no=$request->reg_no;
            $where.=" AND (enquiry.reg_no='".$reg_no."' OR enquiry.mobile_no='".$reg_no."' OR enquiry.id='".$reg_no."')";
		}

		if(!empty($request->category_id)){
			$where.=" AND enquiry.category_id=".$request->category_id;
		}

		if(!empty($request->branch_location)){
			$where.=" AND enquiry.location='".$request->branch_location."'";
		}

		if(!empty($request->branch_id) && count($request->branch_id)){
			$branch_id=implode(',',$request->branch_id);
			$where.=" AND enquiry.branch_id IN (".$branch_id.")";
		}

		if(!empty($request->studio_id)){
		  $where.=" AND enquiry.studio_id=".$request->studio_id;
		}

		if(!empty($request->status)){
		  $where.=" AND enquiry.status='".$request->status."'";
		}

		if(!empty($request->assistant_id)){
		  $where.=" AND enquiry.assistant_id=".$request->assistant_id;
		}

		if(!empty($request->fdate) && !empty($request->tdate)){
			$fdate=date('Y-m-d',strtotime($request->fdate));
			$tdate=date('Y-m-d',strtotime($request->tdate));
		    $where.=" AND enquiry.date>='".$fdate."' AND enquiry.date<='".$tdate."'";
		}

		if(!empty($request->rating)){
		  $where.=" AND enquiry.rating='".$request->rating."'";
		}

		if(!empty($request->course_id) && count($request->course_id)){
			$course_id=implode(',',$request->course_id);
			$where.=" AND enquiry.course_id IN (".$course_id.")";
		}

		

		return $where;
	}

	public function ceoSSS(Request $request){
		$data=[];
		$where=$this->filter($request);
		$support_category = SupportCategory::where("status","Active")->where("is_deleted","0")->get();
		foreach($support_category as $key=>$val){
			$ticket_count = Enquiry::select(DB::RAW('status,count(status) as total'))
			->whereRAW($where)
			->where('category_id',$val->id)->groupby('status')->get();
			$val->ticket_count=$ticket_count;
			$data[]=$val;
		}

		$enquiry=[];
		$enquiry=Enquiry::select("enquiry.*",
			"support_category.name as cat",
			"branches.name as branch_name",
			"batch.name as batch_name",
			"enquiry_description.description"
		    )
		    ->leftJoin("support_category","support_category.id","=","enquiry.category_id")
		    ->leftJoin("branches","branches.id","enquiry.branch_id")
		    ->leftJoin("batch","batch.id","enquiry.batch_id")
		    ->leftJoin('enquiry_description', function($query) 
			{
			   $query->on('enquiry.id','enquiry_description.enquiry_id')
			   ->whereRaw('enquiry_description.id IN (select MAX(a2.id) from enquiry_description as a2 join enquiry as u2 on u2.id = a2.enquiry_id group by u2.id)');
			})
		    ->whereRAW($where)
		    //->whereIN('enquiry.status',['pending','reopen'])
		    ->orderBy('enquiry.id','DESC')->paginate(10);

		return view('admin.support_category.ceo-dashboard', compact('data','enquiry'));
	}

	public function discussion(Request $request){
		$discussion=[];
		$where=$this->discussionFilter($request);
	    $discussion=ForumQuestion::select("forum_question.*")->withCount(['readPending'])->whereRAW($where)->orderBy('id','desc')->paginate(10);
        
        $blockList=[];
        if(!empty($request->blockUsers) && $request->blockUsers){
	      $blockList=DB::table("forum_blocked")->where('status',5)->get();
        }
		
		$total_students = 0;
		$total_batch = 0;
		if($request->totals==1){
			$total_students=DB::table('forum_question')->distinct('mobile_no')->count('id');	
		}
		
		if($request->totals==2){
			$total_batch=DB::table('forum_question')->distinct('batch_id')->count('id');
		}

	    //print_r($discussion);die('ddd');
		return view('admin.support_category.discussion', compact('discussion','blockList','total_students','total_batch'));
	}
	
	
	public function support_discussion_comment(Request $request){
		DB::table("forum_comment")->where("question_id",$request->enq_id)->update(['is_read'=>1]);

		$discussion=ForumQuestion::select("forum_question.*")->where('id',$request->enq_id)->orderBy('id','desc')->first();

		$userStatus=DB::table("forum_blocked")->where('reg_no',$discussion->reg_no)->where('status',5)->first();

		$res = "";

		if(!empty($userStatus)){
		  $res.="<a href='#' class='m-2 btn btn-sm btn-danger deleteComment' data-id='".$discussion->reg_no."' data-type='block'>User Blocked</a>";
		}else{
		  $res.="<a href='#' class='m-2 btn btn-sm btn-primary deleteComment' data-id='".$discussion->reg_no."' data-type='block'>Block</a>";
		}

		$res.="<a href='#' class='m-2 btn btn-sm btn-success deleteComment' data-id='".$discussion->reg_no."' data-type='unblock'>UnBlock</a>";



		$res .= "<table class='table table-striped'>
					<thead>
						<tr>
							<th>User Action</th>
							<th>S. No.</th>
							<th>Reg. No.</th>
							<th>By Name</th>
							<th>Comment</th>
							<th>Created Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>";
						if(!empty($request->enq_id)){
							$old_query = DB::table("forum_comment")->where("question_id",$request->enq_id)->get();
							if(count($old_query) > 0){
								foreach($old_query as $key=>$old_query_value){
									$sno = $key+1;
									$status='<span class="text-success"> Active</span>';
									if($old_query_value->status!=1){
										$status='<span  class="text-danger">Deleted</span>';
									}

									$userStatus=DB::table("forum_blocked")->where('reg_no',$old_query_value->by_reg_no)->where('status',5)->first();
									$uAction="<a href='#' class='text-info deleteComment' data-id='".$old_query_value->by_reg_no."' data-type='block'>Block</a><br><br>
									    <a href='#' class='text-success deleteComment' data-id='".$old_query_value->by_reg_no."' data-type='unblock'>UnBlock</a>";
									if(!empty($userStatus)){
                                      $uAction="<a href='#' class='text-danger deleteComment' data-id='".$old_query_value->by_reg_no."' data-type='block'>User Blocked</a><br><br>
									    <a href='#' class='text-success deleteComment' data-id='".$old_query_value->by_reg_no."' data-type='unblock'>UnBlock</a>";
									}

									$res.="<tr class='tr-".$old_query_value->id."'>
									        <td>".$uAction."</td>
											<td>".$sno."</td>												
											<td>".$old_query_value->by_reg_no."</td>
											<td>".$old_query_value->by_name."</td>
											<td>".$old_query_value->comment."</td>
											<td>".$old_query_value->created_at."</td>
											<td >".$status."</td>
											<td><a href='#' class='btn btn-sm btn-danger deleteComment' data-id='".$old_query_value->id."' data-type='comment'>Delete</a></td>
										</tr>";
								}
							}
						}
						
			$res .= "</tbody>
				</table>";

		echo $res;
        exit();
	}
	
	public function support_discussion_reply(Request $request){
		if(!empty($request->description) && !empty($request->question_id)){
			$loginId  = Auth::user()->id;
			$forum_com_detail=DB::table("forum_comment")->where("question_id", $request->question_id)->first();
			$reply_to_name = "Student";
			if(!empty($forum_com_detail)){
				$reply_to_name = $forum_com_detail->reply_to_name;
			}
			$id = DB::table("forum_comment")->insertGetId(["question_id" => $request->question_id, "parent_id" => 0, "by_reg_no" => $loginId, "by_name" => 'Utkarsh Classes', "reply_to_name" => $reply_to_name, "comment" => $request->description, "image" => '']);

			if($id){
				return response(['status'=>true,'msg'=>'Replied Successfully']);
			}
			else{
				return response(['status'=>false,'msg'=>'Something went wrong.']);
			}
			
		}else{
			//return redirect()->back()->with('error', 'Description is required');
			return response(['status'=>false,'msg'=>'Reply is required']);

		}
	}

	public function support_discussion_delete(Request $request){
		$user=Auth::user();
		$by_user_id=0;
		if(!empty($user)){
			$by_user_id=$user->id;
		}

        $id=$request->id;
        $type=$request->type;
        $msg="";
		if($type=='comment'){
          DB::table("forum_comment")->where("id", $id)->update(['status'=>2]);
          $msg="Comment Deleted";
		}else if($type=='post'){
          ForumQuestion::where('id',$id)->update(['status'=>2]);
          $msg="Post Deleted";
		}else if($type=='block'){
			DB::table("forum_blocked")->insert(['reg_no'=>$id,'status'=>5,'by_user_id'=>$by_user_id]);
           $msg="User Blocked";
		}else if($type=='unblock'){
		  DB::table("forum_blocked")->where('reg_no',$id)->update(['status'=>1]);
          $msg="User UnBlocked";
		}

		return ['status'=>true,'msg'=>$msg];
	}

	public function discussionFilter(Request $request){
		$where='1=1';

		$user=Auth::user();
		if(!empty($user)){
			$role_id=$user->role_id;

			if($role_id==29){
              //all access
			}else if($role_id==28){
				$user_branches=$user->user_branches;
				$branch_id=0;
				foreach($user_branches as $val){
		          $branch_id.=",".$val->branch_id;
				}
				$where.=" AND branch_id IN (".$branch_id.")";
			}else{
				$check_batch=DB::table("batch")->where("chopal_agent_id", $user->id)->get();
				$batch_ids=0;
				foreach($check_batch as $bvl){
                   $batch_ids.=",".$bvl->id;
				}

				if(count($check_batch)){
					$where.=" AND batch_id IN (".$batch_ids.")";
				}else{
					//no access
				  //$where.=" AND batch_id=0000001";
				  $where.=" AND 1=1";
				}
			}
		}

		if(!empty($request->reg_no)){
			$reg_no=$request->reg_no;
            $where.=" AND (reg_no='".$reg_no."' OR mobile_no='".$reg_no."' OR id='".$reg_no."')";
		}

		if(!empty($request->branch_location)){
			$where.=" AND location='".$request->branch_location."'";
		}

		if(!empty($request->branch_id) && count($request->branch_id)){
			$branch_id=implode(',',$request->branch_id);
			$where.=" AND branch_id IN (".$branch_id.")";
		}

		if(!empty($request->studio_id)){
		  $where.=" AND studio_id=".$request->studio_id;
		}

		if(!empty($request->status)){
		  $where.=" AND status='".$request->status."'";
		}

		if(!empty($request->fdate) && !empty($request->tdate)){
			$fdate=date('Y-m-d',strtotime($request->fdate));
			$tdate=date('Y-m-d',strtotime($request->tdate));
		    $where.=" AND created_at>='".$fdate."' AND created_at<='".$tdate."'";
		}

		if(!empty($request->batch_id) && count($request->batch_id)){
			$batch_id=implode(',',$request->batch_id);
			$where.=" AND batch_id IN (".$batch_id.")";
		}

		return $where;
	}
	
	
	public function support_discussion_batch()
    {
		$forum_question = DB::table('forum_question')->select('batch.name','forum_question.batch_code')->leftJoin('batch','batch.id','=','forum_question.batch_id')->groupBy('batch_id')->get();
        return view('admin.support_category.all_batches', compact('forum_question'));
    }

    public function app_notification($nt_data){
	 	$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/send_notification',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $nt_data,
		  CURLOPT_HTTPHEADER => array(
		    'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
	}
}
