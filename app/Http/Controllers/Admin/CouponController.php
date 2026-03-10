<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Input;
use Excel;
use App\Exports\CouponExport;
class CouponController extends Controller
{
    public function index(Request $request)
    {
        $params = [];
        $res = parse_url($_SERVER['REQUEST_URI']);
        if (!empty($res['query'])) {
            parse_str($res['query'], $params);
        }

        $logged_id = Auth::user()->id;

        $query = DB::table("assign_coupon")
            ->leftJoin("users", "assign_coupon.user_id", "=", "users.id")
            ->select("assign_coupon.*", "users.name as assigner_name")
            ->orderBy("assign_coupon.id", "desc");

        if (!in_array($logged_id, [901, 7509, 7322])) {
            $query->where("assign_coupon.user_id", $logged_id);
        }

        if ($request->filled('mobile')) {
            $query->where('assign_coupon.mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->filled('emp_id')) {
            $query->where('assign_coupon.user_id', $request->emp_id);
        }
        
        if ($request->filled('fdate') && $request->filled('tdate')) {
            $query->whereBetween('assign_coupon.created_at', [
                $request->fdate . ' 00:00:00',
                $request->tdate . ' 23:59:59'
            ]);
        } elseif ($request->filled('fdate')) {
            $query->where('assign_coupon.created_at', '>=', $request->fdate . ' 00:00:00');
        } elseif ($request->filled('tdate')) {
            $query->where('assign_coupon.created_at', '<=', $request->tdate . ' 23:59:59');
        }


        $userDetails = $query->paginate(20)->appends($params);

        return view('admin.coupon.index', compact('userDetails', 'params'));
    }


    public function assign()
    {
        return view('admin.coupon.assign');
    }
    public function store(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10'
        ]);
        $logged_id = Auth::user()->id;
        $logged_name = Auth::user()->name;
        $mobile = $request->mobile;
        $coupon_code = 'EXTRA10'; //EXTRA10
        $remark = "ASSIGN BY HRM → " . $logged_name . " AND ITS id → " . $logged_id;

        $response = $this->assignCoupon($mobile, $coupon_code, $remark);

        if (isset($response['status']) && $response['status'] == true) {
            DB::table('assign_coupon')->insert([
                'user_id' => $logged_id,
                'mobile' => $mobile,
                'coupon_code' => $coupon_code,
                'remark' => $remark,
                'created_at' => now()
            ]);
            return redirect()->route('coupon.index')->with('success', 'Coupon assigned successfully.');
        } else {
            // return redirect()->route('coupon.index')->with('error', 'Coupon Not Assigned!!.');
            return redirect()->back()->with('error', 'Coupon Not Assigned!!.');
        }
    }
    public function export(Request $request)
    {
        $logged_id = Auth::user()->id;

        $query = DB::table("assign_coupon")
            ->leftJoin("users", "assign_coupon.user_id", "=", "users.id")
            ->select("assign_coupon.*", "users.name as assigner_name")
            ->orderBy("assign_coupon.id", "desc");

        if (!in_array($logged_id, [901, 7509, 7322])) {
            $query->where("assign_coupon.user_id", $logged_id);
        }

        if ($request->filled('mobile')) {
            $query->where('assign_coupon.mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->filled('emp_id')) {
            $query->where('assign_coupon.user_id', $request->emp_id);
        }

        if ($request->filled('fdate') && $request->filled('tdate')) {
            $query->whereBetween('assign_coupon.created_at', [
                $request->fdate . ' 00:00:00',
                $request->tdate . ' 23:59:59'
            ]);
        } elseif ($request->filled('fdate')) {
            $query->where('assign_coupon.created_at', '>=', $request->fdate . ' 00:00:00');
        } elseif ($request->filled('tdate')) {
            $query->where('assign_coupon.created_at', '<=', $request->tdate . ' 23:59:59');
        }

        $coupons = $query->get();

        $responseArray = [];
        if ($coupons->count() > 0) {
            foreach ($coupons as $key => $value) {
                $responseArray[$key]['mobile'] = $value->mobile;
                $responseArray[$key]['coupon_code'] = $value->coupon_code;
                $responseArray[$key]['created_at'] = \Carbon\Carbon::parse($value->created_at)->format('d M Y');
                if (in_array($logged_id, [901, 7509, 7322])) {
                    $responseArray[$key]['assigner_name'] = $value->assigner_name ?? '-';
                }
            }
        }

        if (count($responseArray) > 0) {
            return Excel::download(new CouponExport($responseArray), 'Coupons_' . date('Y-m-d_H-i-s') . '.xlsx');
        } else {
            return redirect()->back()->with('error', 'No data available to export!');
        }
    }
    private function assignCoupon($mobile, $coupon_code, $remark)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://support.utkarshapp.com/index.php/support_model/courses/assign_coupon',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'mobile' => $mobile,
                'coupon_code' => $coupon_code,
                'remark' => $remark
            ],
            CURLOPT_HTTPHEADER => [
                'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

}
