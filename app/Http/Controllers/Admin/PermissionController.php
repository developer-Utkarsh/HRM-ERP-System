<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SupportUser;
use App\SupportCategory;
use App\User;
use Input;
use DB;
use Validator;

//hrm
class PermissionController extends Controller
{
    
    public function permission_add()
    {
		$url = url('/admin/permission-store');
		$heading = 'Add';
		$title	 = '';
		$permission_ids	= '';
        return view('admin.roles_permission.permission', compact('url','heading','title','permission_ids'));
    }
	
	public function store(Request $request){
		$name = $request->name;
		$permissions = $request->permissions;
		if($name!="" && $permissions!=""){
			$permissions=implode(",",$permissions);
			
			DB::table('roles')->insert(array('status' => 1, 'name' => $name,'permission_ids' =>$permissions));
			
			return redirect()->route('admin.permission-list')->with('success', 'Permission set');
		}else{
			return redirect()->route('admin.permission-add')->with('error', 'Something Went Wrong !');
		}
	}
	
	public function permission_list()
    {
		$roles = DB::table('roles')->where('status',1)->get();
        return view('admin.roles_permission.index', compact('roles'));
    }
	
	public function edit($id,$title){
		$url = url('/admin/permission-update').'/'.$id;
		$heading = 'Edit';
		
		$permissionRole=DB::table('roles')
            ->where('id', '=', $id)
            ->select('permission_ids')
            ->first();
        $permission_ids=$permissionRole->permission_ids;

						
		return view('admin.roles_permission.permission', compact('permission_ids','title','id','url','heading'));		
	}
	
	public function update(Request $request,$id){
		$permissions = $request->permissions;
		$permissions=implode(",",$permissions);
		DB::table('roles')->where('id',$id)->update(['permission_ids' =>$permissions]);
		
		return redirect()->route('admin.permission-list')->with('success', 'Permission Updated');
	}
	
	public function destroy($id)
    {
        $role = DB::table('roles')->find($id);
		
		if(!empty($role)){
			
			DB::table('roles')->where('id', $id)->update(array('is_deleted' => '1','status' =>2));
			
            return redirect()->back()->with('success', 'Role Deleted Successfully');
        } else {
            return redirect()->route('admin.roles.index')->with('error', 'Something Went Wrong !');
        }
    }
}
