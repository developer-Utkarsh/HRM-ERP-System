<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SupportUser;
use App\SupportCategory;
use App\User;
use Input;
use Validator;


class SupportUserController extends Controller
{
    
    public function index()
    {
  		$emp_id   = Input::get('user_name');
		
        $support_user = SupportUser::select("support_user.*","users.name")->leftJoin("users","users.id","=","support_user.user_id")->where('support_user.is_deleted', '0');
		if(!empty($emp_id)){
			$support_user->where("user_id", $emp_id);
		}
		$support_user = $support_user->orderBy('support_user.id', 'desc')->get();
		
		$category = SupportCategory::where('status', 'Active')->where('is_deleted', '0')->orderBy('name','ASC')->get();
		$users = User::where('status', '1')->where('is_deleted', '0')->orderBy('name','ASC')->get();
        return view('admin.support_user.index', compact('support_user','category','users'));
    }

    public function store(Request $request)
    {  
        $validation = Validator::make( $request->all(), [
			'user_id'=>'unique:support_user,user_id,'.$request->id,
		]);
		
		if ( $validation->fails() ) {
			return redirect()->route('admin.support_user.index')->with('error', 'Employee Already Exits');
		}
		
		$inputs = $request->only('user_id','role'); 
		$inputs['category_id'] = '';
		if(!empty($request->category) && $request->role == 'replier'){
			$inputs['category_id'] = implode(",", $request->category);
		}

		if(!empty($request->id)){
			$supportUserId  = SupportUser::where('id', $request->id)->first();
			$res  = $supportUserId->update($inputs);
			$msg = "Support User Update Successfully";
		}
		else{
			$support_user_result = SupportUser::create($inputs); 
			$res  = $support_user_result->save();
			$msg = "Support User Added Successfully";
		}
    

        if($res) {
            return redirect()->route('admin.support_user.index')->with('success', $msg);
        } else {
            return redirect()->route('admin.support_user.index')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function destroy($id)
    {   
        $result = SupportUser::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($result->update($inputs)) {
            return redirect()->back()->with('success', 'Support User Deleted Successfully');
        } else {
            return redirect()->route('admin.support_user.index')->with('error', 'Something Went Wrong !');
        }
    }

	
	public function edit_support_user(Request $request){ 
		$support_user_id  = $request->support_user_id;  
        $respnse = SupportUser::where([['id', '=', $support_user_id]])->first(); 
		
		$category = SupportCategory::where('status', 'Active')->where('is_deleted', '0')->orderBy('name','ASC')->get();
		$users = User::where('status', '1')->where('is_deleted', '0')->orderBy('name','ASC')->get();
		
        if (!empty($respnse))
        {
            $res = "";
			
			$display_cat = ($respnse->role=='replier')?'':'none';
			$req_cat = ($respnse->role=='replier')?'required':'';
										
			$res .='<div class="row col-md-12">
			<input type="hidden" name="id" value="'.$respnse->id.'">
				<div class="col-md-12 mb-2">
					<select class="form-control select-multiple" name="user_id" required>
						<option value="">Select User</option>';
						if(count($users) > 0){
							foreach($users as $usersvalue){
								if(!empty($respnse->user_id) && $respnse->user_id == $usersvalue->id){ $user_sel = "selected";}else{ $user_sel = ""; }
								$res .='<option value="' . $usersvalue->id . '" '.$user_sel.'>' . $usersvalue->name . '</option>';
							}
						}
					$res .='</select><br>
				</div>
				<div class="col-md-12 mb-2">
					<select class="form-control role select-multiple1" name="role" required>
						<option value="">Select Role</option>
						<option value="query"';if(!empty($respnse->role) && $respnse->role == 'query'){ $res .='selected'; } $res .='>Query</option>
						<option value="replier"';if(!empty($respnse->role) && $respnse->role == 'replier'){ $res .='selected'; } $res .='>Replier</option>
						<option value="admin"';if(!empty($respnse->role) && $respnse->role == 'admin'){ $res .='selected'; } $res .='>Admin</option>
					</select><br>
				</div>
				
				<div class="col-md-12 mb-2 category_div" style="display:'.$display_cat.';" >
					<select class="form-control category  select-multiple2" name="category[]" '.$req_cat.' multiple>
						<option value="">Select category</option>';
						if(count($category) > 0){
							foreach($category as $categoryvalue){ 
								$explode_cat = explode(",",$respnse->category_id); 
								if(!empty($respnse->category_id) && in_array($categoryvalue->id	, $explode_cat)){ $cat_sel = "selected";}else{ $cat_sel = ""; }
								$res .='<option value="'.$categoryvalue->id.'" '.$cat_sel.'>'.$categoryvalue->name.'</option>';
							}
						}
					$res .='</select><br>
				</div>
				
			  </div>';
            
			echo $res;
            exit();
        }
        else
        {
           return redirect()->route('admin.support_user.index')->with('error', 'Data Not Found');
        }
	}
 
    public function togglePublish($id) { 
        $support_user = SupportUser::find($id);
        if (is_null($support_user)) {
            return redirect()->route('admin.support_user.index')->with('error', 'Support User not found');
        }
		
		if($support_user->status == 'Active'){
			$sts = 'Inactive';
		}
		else{
			$sts = 'Active';
		}
		
		$support_user->update([
                'status' => $sts,
                'updated_at' => new \DateTime(),
            ]);
        return redirect()->route('admin.support_user.index')->with('success', 'Status Updated Successfully.');
    }
}
