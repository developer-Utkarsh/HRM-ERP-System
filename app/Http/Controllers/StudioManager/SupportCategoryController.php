<?php
namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SupportCategory;
use App\Enquiry;
use Input;
use Validator;
use DB;
use Auth;


class SupportCategoryController extends Controller
{
    
    public function index()
    {
  		$name   = Input::get('name');
        $category = SupportCategory::where('is_deleted', '0')->orderBy('id', 'desc')->where('name', 'LIKE', '%' . $name . '%')->get();
        return view('studiomanager.support_category.category', compact('category'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make( $request->all(), [
			'name'=>'unique:support_category,name,'.$request->id,
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('studiomanager.support_category.index')->with('error', 'Category Already Exits');
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
            return redirect()->route('studiomanager.support_category.index')->with('success', $msg);
        } else {
            return redirect()->route('studiomanager.support_category.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $category = SupportCategory::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($category->update($inputs)) {
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } else {
            return redirect()->route('studiomanager.support_category.index')->with('error', 'Something Went Wrong !');
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
            return redirect()->route('studiomanager.support_category.index')->with('error', 'Category not found');
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
        return redirect()->route('studiomanager.support_category.index')->with('success', 'Status Updated Successfully.');
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
		return view('studiomanager.support_category.dashboard', compact('data'));
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
		return view('studiomanager.support_category.enquery', compact('enquiry'));
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
			//https://courses.utkarsh.com/utkarshadm/offlineapp/includes/notification.php?reg_no=118568&title=test&description=test&batch_id=1
			$title=urlencode("Your Support Ticket Number ".$enquiry->id." has been resolved");
			$description=urlencode($request->description);
			$batch_id=$enquiry->batch_code;
			file_get_contents("https://courses.utkarsh.com/utkarshadm/offlineapp/includes/notification.php?reg_no=".$enquiry->reg_no."&title=".$title."&description=".$description."&batch_id=".$batch_id."&data_type=support");
            
            //update ticket status after every 48 hours
            $updated_at=date('Y-m-d H:i:s',strtotime(now().' -48 hours'));
			Enquiry::where("status",'replied')->where('updated_at','<',$updated_at)->update(['status'=>'resolved']);

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

			if($role_id==28){
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
            $where.=" AND (enquiry.reg_no='".$reg_no."' OR enquiry.mobile_no='".$reg_no."')";
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

		return view('studiomanager.support_category.ceo-dashboard', compact('data','enquiry'));
	}
}
