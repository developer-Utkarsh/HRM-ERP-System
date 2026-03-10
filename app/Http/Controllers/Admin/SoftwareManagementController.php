<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
class SoftwareManagementController extends Controller
{
    public function index()
    {
        $details = DB::table('system_master')
            ->where('system_master.status', 'Active')
            ->join('users', 'system_master.owner_id', '=', 'users.id')
            ->leftJoin('users as assigner', 'system_master.assigner_id', '=', 'assigner.id')
            ->select(
                'system_master.*',
                'users.name as owner_name',
                'users.register_id as owner_register_id',
                'assigner.name as assigner_name',
                'assigner.register_id as assigner_register_id'
            )->orderBy('system_master.created', 'desc')
            ->get();

        return view('admin.software-management.index', compact('details'));
    }


    public function create()
    {
        $users = DB::table('users')->where('status', '1')->where('is_deleted', '0')->get();
        return view('admin.software-management.create', compact('users'));
    }

    public function store(Request $request)
    {
        $assigner_id = Auth::id();
        $request->validate([
            'soft_name' => 'required',
            'soft_type' => 'required',
            'soft_owner' => 'required',
        ]);

        $softwareId = DB::table('system_master')->insertGetId([
            'name' => $request->soft_name,
            'soft_type' => $request->soft_type,
            'description' => $request->description,
            'owner_id' => $request->soft_owner,
            'assigner_id' => $assigner_id,
            'created' => now(),
        ]);

        $this->maintain_history(
            $assigner_id,
            'system_master',
            $softwareId,
            'software_add',
            json_encode([
                'name' => $request->soft_name,
                'type' => $request->soft_type,
                'owner_id' => $request->soft_owner
            ])
        );

        return redirect()->route('software-management')->with('success', 'Software Added Successfully');
    }
    public function edit($id)
    {
        $software = DB::table('system_master')->where('id', $id)->first();
        $users = DB::table('users')->where('status', '1')->where('is_deleted', '0')->get();
        return view('admin.software-management.create', compact('software', 'users'));
    }
    public function update(Request $request, $id)
    {
        $assigner_id = Auth::id();

        $request->validate([
            'soft_name' => 'required',
            'soft_type' => 'required',
            'soft_owner' => 'required',
        ]);

        DB::table('system_master')->where('id', $id)->update([
            'name' => $request->soft_name,
            'soft_type' => $request->soft_type,
            'description' => $request->description,
            'owner_id' => $request->soft_owner,
            'assigner_id' => $assigner_id,
            'updated_at' => now(),
        ]);

        $this->maintain_history(
            $assigner_id,
            'system_master',
            $id,
            'software_update',
            json_encode([
                'name' => $request->soft_name,
                'type' => $request->soft_type,
                'owner_id' => $request->soft_owner
            ])
        );

        return redirect()->route('software-management')->with('success', 'Software Updated Successfully');
    }

    private function maintain_history($user_id, $table_name, $table_id, $type, $save_data)
    {
        $history_data = array(
            'user_id' => $user_id,
            'table_name' => $table_name,
            'table_id' => $table_id,
            'type' => $type,
            'save_data' => $save_data
        );
        return DB::table('all_history')->insert($history_data);
    }



}
