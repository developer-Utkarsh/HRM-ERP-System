<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccessRequestMail;
use App\Mail\AccessApprovedMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Input;


class RequestAccessController extends Controller
{
    public function index(Request $request)
    {
        $params = array();
        $res = parse_url($_SERVER['REQUEST_URI']);
        if (!empty($res['query'])) {
            parse_str($res['query'], $params);
        }
        $page = Input::get('page');
        $userId = Auth::id();

        $query = DB::table('system_access_request as sar')
            ->join('system_master as sm', 'sar.software_id', '=', 'sm.id')
            ->join('users as requester', 'sar.requester_id', '=', 'requester.id')
            ->leftJoin('users as employee', 'sar.user_id', '=', 'employee.id')
            ->where('sar.is_deleted', 0)
            ->where(function ($q) use ($userId) {
                $q->where('sm.owner_id', $userId)
                    ->orWhere('sar.requester_id', $userId);
            });

        // Apply filters
        if ($request->filled('emp_name')) {
            $query->where('employee.name', 'like', '%' . $request->emp_name . '%');
        }

        if ($request->filled('status')) {
            $query->where('sar.status', $request->status);
        }

        if ($request->filled('requester_name')) {
            $query->where('requester.name', 'like', '%' . $request->requester_name . '%');
        }

        if ($request->filled('soft_name')) {
            $query->where('sm.name', 'like', '%' . $request->soft_name . '%');
        }

        $requests = $query->select(
            'sar.*',
            'sm.name as name',
            'sm.owner_id',
            'requester.name as requester_name',
            'requester.register_id as requester_register_id',
            'employee.name as employee_name',
            'employee.register_id as employee_register_id'
        )
            ->orderBy('sar.created_at', 'desc')
            ->paginate(20);

        foreach ($requests as $request) {
            $request->history = DB::table('system_access_request as sar')
                ->join('users as requester','sar.requester_id' , '=', 'requester.id')
                ->where('sar.user_id', $request->user_id)
                ->where('sar.software_id', $request->software_id)
                ->orderBy('sar.created_at', 'desc')
                ->select('sar.*','requester.name as requester_name','requester.register_id as requester_register_id')
                ->get();
        }
        return view('admin.request-access.index', compact('requests', 'params'));
    }

    public function create()
    {
        $user = Auth::user();
        $employees = DB::table('users')
            ->where('department_type', $user->department_type)
            ->where('status', '1')
            ->Where('id', '!=', $user->id)
            ->get();

        $software_list = DB::table('system_master')
            ->where('status', 'Active')->get();

        return view('admin.request-access.create', compact('employees', 'software_list'));
    }

    public function store(Request $request)
    {
        $requester_id = Auth::id();
        $requester_email = Auth::user()->email;
        // print_r($requester_email);die;
        $rules = [
            'request_for' => 'required|in:self,team_member',
            'software_ids' => 'required|array|min:1',
            'software_ids.*' => 'required|exists:system_master,id',
            'already_have_access' => 'required|array',
            'already_have_access.*' => 'required|in:yes,no',
            'purpose' => 'required|array',
            'purpose.*' => 'required',
        ];
        if ($request->input('request_for') === 'team_member') {
            $rules['employee_id'] = 'required|exists:users,id';
        }
        if (is_array($request->input('already_have_access'))) {
            foreach ($request->input('already_have_access') as $index => $value) {
                if ($value === 'no') {
                    $rules["request_type.$index"] = 'required|in:New Request,Upgrade Access,Downgrade Access';
                }
            }
        }
        $customMessages = [
            'software_ids.*.required' => 'Please select a software.',
            'purpose.*.required' => 'Please provide the purpose of access.',
            'request_type.*.required' => 'Request type is required if you do not have access.'
        ];

        $request->validate($rules, $customMessages);

        foreach ($request->software_ids as $index => $softwareId) {
            $alreadyHave = $request->already_have_access[$index];
            $status = ($alreadyHave == 'yes') ? 'Access Assigned' : 'InProcess';

            $requestId = DB::table('system_access_request')->insertGetId([
                'requester_id' => $requester_id,
                'request_for' => $request->request_for,
                'user_id' => $request->request_for === 'team_member' ? $request->employee_id : null,
                'software_id' => $softwareId,
                'already_have_access' => $alreadyHave,
                'request_type' => $request->already_have_access[$index] === 'no' ? $request->request_type[$index] : null,
                'access_level' => $request->access_level[$index],
                'purpose' => $request->purpose[$index],
                'remark' => $request->remarks[$index],
                'status' => $status,
                'created_at' => now()
            ]);

            // sending mail to the software Owner
            $software = DB::table('system_master')->where('id', $softwareId)->first();
            $employee = DB::table('users')->where('id', $request->employee_id ?? $requester_id)->first();
            $owner = DB::table('users')->where('id', $software->owner_id)->first();
            $deeplinkUrl = 'https://hrm.utkarshupdates.com/index.php/request-access';
            Mail::to($owner->email)->cc($requester_email)->send(new AccessRequestMail(
                $software->name,
                $owner->name,
                $employee->name,
                $deeplinkUrl
            ));

            // Log history
            $this->maintain_history(
                $requester_id,
                'system_access_request',
                $requestId,
                'Created',
                json_encode([
                    'software_id' => $softwareId,
                    'user_id' => $request->request_for === 'team_member' ? $request->employee_id : null,
                    'request_type' => $request->already_have_access[$index] === 'no' ? $request->request_type[$index] : 'Already Have',
                    'access_level' => $request->access_level[$index],
                    'purpose' => $request->purpose[$index],
                ])
            );
        }


        return redirect()->route('request-access')->with('success', 'Access Request Submitted Successfully');
    }

    public function update(Request $request)
    {
        $approved_by = Auth::id();
        $request->validate([
            'id' => 'required|exists:system_access_request,id',
            'status' => 'required|in:Approved,Rejected,Revoked',
            'assign_remark' => 'required_if:status,Approved|max:500',
            'revoke_reason' => 'required_if:status,Revoked|max:500',
            'rej_reason' => 'required_if:status,Rejected|max:500'
        ]);

        $updateData = [
            'approved_by' => $approved_by,
            'approved_at' => now(),
            'status' => $request->status,
        ];
        if ($request->status === 'Approved') {
            $updateData['assign_remark'] = $request->assign_remark;
            $currentRequest = DB::table('system_access_request')
                ->where('id', $request->id)
                ->first();

            // print_r($currentRequest);die;

            if ($currentRequest) {
                DB::table('system_access_request')
                    ->where('user_id', $currentRequest->user_id)
                    ->where('software_id', $currentRequest->software_id)
                    ->where('status', 'Approved')
                    ->where('id', '<>', $request->id)
                    ->where('is_deleted', 0)
                    ->update([
                        'is_deleted' => 1
                    ]);
                // print_r($print);die();
            }
        }

        if ($request->status == 'Rejected') {
            $updateData['rej_reason'] = $request->rej_reason;
        }
        if ($request->status === 'Revoked') {
            $updateData['revoke_reason'] = $request->revoke_reason;
            $updateData['revoke_at'] = now();
        }

        DB::table('system_access_request')
            ->where('id', $request->id)
            ->update($updateData);

        $this->maintain_history(
            Auth::id(),
            'system_access_request',
            $request->id,
            $request->status,
            json_encode($updateData)
        );

        // === EMAIL LOGIC STARTS HERE ===
        if ($request->status === 'Approved') {
            $accessRequest = DB::table('system_access_request as sar')
                ->join('users as requester', 'sar.requester_id', '=', 'requester.id')
                ->leftJoin('users as employee', 'sar.user_id', '=', 'employee.id')
                ->join('system_master as sm', 'sar.software_id', '=', 'sm.id')
                ->where('sar.id', $request->id)
                ->select(
                    'sm.name as software_name',
                    'requester.name as department_head_name',
                    'requester.email as department_head_email',
                    'employee.name as employee_name',
                    'sar.assign_remark'
                )
                ->first();

            if ($accessRequest) {
                $emailData = [
                    'Software_Name' => $accessRequest->software_name,
                    'Department_Head_Name' => $accessRequest->department_head_name,
                    'Employee_Name' => $accessRequest->employee_name ?? 'the employee',
                    'Remarks' => $accessRequest->assign_remark,
                ];
                Mail::to($accessRequest->department_head_email)->send(new AccessApprovedMail($emailData));
            }
        }


        if ($request->status === 'Rejected') {
            $accessRequest = DB::table('system_access_request as sar')
                ->join('users as requester', 'sar.requester_id', '=', 'requester.id')
                ->leftJoin('users as employee', 'sar.user_id', '=', 'employee.id')
                ->join('system_master as sm', 'sar.software_id', '=', 'sm.id')
                ->where('sar.id', $request->id)
                ->select(
                    'sm.name as software_name',
                    'requester.name as department_head_name',
                    'requester.email as department_head_email',
                    'employee.name as employee_name',
                    'sar.rej_reason'
                )
                ->first();

            if ($accessRequest) {
                $emailData = [
                    'Software_Name' => $accessRequest->software_name,
                    'Department_Head_Name' => $accessRequest->department_head_name,
                    'Employee_Name' => $accessRequest->employee_name ?? 'the employee',
                    'Rejection_Reason' => $accessRequest->rej_reason,
                ];

                Mail::to($accessRequest->department_head_email)->send(new \App\Mail\AccessRejectedMail($emailData));
            }
        }

        if ($request->status === 'Revoked') {
            $accessRequest = DB::table('system_access_request as sar')
                ->join('users as requester', 'sar.requester_id', '=', 'requester.id')
                ->leftJoin('users as employee', 'sar.user_id', '=', 'employee.id')
                ->join('system_master as sm', 'sar.software_id', '=', 'sm.id')
                ->where('sar.id', $request->id)
                ->select(
                    'sm.name as software_name',
                    'requester.name as department_head_name',
                    'requester.email as department_head_email',
                    'employee.name as employee_name',
                    'sar.revoke_reason'
                )
                ->first();

            if ($accessRequest) {
                $emailData = [
                    'Software_Name' => $accessRequest->software_name,
                    'Department_Head_Name' => $accessRequest->department_head_name,
                    'Employee_Name' => $accessRequest->employee_name ?? 'the employee',
                    'Revoke_Remark' => $accessRequest->revoke_reason,
                ];

                Mail::to($accessRequest->department_head_email)->send(new \App\Mail\AccessRevokedMail($emailData));
            }
        }


        return redirect()->route('request-access')->with('success', 'Request ' . $request->status . ' Successfully');
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
