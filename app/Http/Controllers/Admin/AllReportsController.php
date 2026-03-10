<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Timetable;
use Input;
use Auth;
use App\User;
use DB;
use App\Exports\FacultyHoursExport;
use App\Exports\MSGCeoExport;
use Excel;

class AllReportsController extends Controller
{
    public function index(Request $request)
    {
        $user_id     = Input::get('user_id');
		if(!empty(Auth::user()->id)){
			//return redirect("admin/faculty-hours-reports?faculty_id=$user_id");
		}
		
		$mentor_batch_list = array();
		$app_config = array();
		$app_config['is_faculty_report'] = 0;
		$app_config['is_faculty_hours_reports_new'] = 0;
		$app_config['is_test_report'] = 0;
		$app_config['is_employee_leave'] = 0;
		$login_user   = User::with(['user_details'])->where([['id', '=', $user_id]])->first();
		// print_r($login_user->user_details->degination); die;
		if(!empty($login_user)){
			$role_id = $login_user->role_id;
			
			if($role_id==2){
				$app_config['is_faculty_report'] = 1;
				$app_config['is_faculty_hours_reports_new'] = 1;
				
				$batches = DB::table('users')
					  ->select('batch.id as batch_id','batch.name as batch_name','batch.mentor_id','users.name as user_name')
					  ->leftJoin('batch', 'batch.mentor_id', '=', 'users.id')
					  ->where('users.id', $user_id)
					  ->where('users.status', 1);
				$batches =	$batches->get();
				
				if(count($batches) > 0){
					$mentor_batch_list = json_decode(json_encode($batches),true);
				}
			}
			else if($login_user->user_details->degination == 'CONTENT + FACULTY'){
				$app_config['is_faculty_report'] = 1;
				$app_config['is_faculty_hours_reports_new'] = 1;
			}
			else if($login_user->user_details->degination == 'CENTER HEAD' || $login_user->user_details->degination == 'ASSISTANT CENTER HEAD' ){
				$app_config['is_test_report'] = 1;
			}
			else if($user_id == 1647 || $user_id == 5453 || $user_id == 6328 || $user_id == 1237){ // Pradeep Sir default show
				$app_config['is_test_report'] = 1;
			}

			if($role_id==28 || $role_id==3){
              //$app_config['is_support'] = 1;
              //$auth=Auth::loginUsingId($user_id);
			  //return redirect()->route('admin.support-dashboard');
			}
			
			if($role_id!=2){
				$app_config['is_employee_leave'] = 1;
			}
			
		} 
		
		return view('admin.all_reports.index', compact('user_id','app_config','mentor_batch_list'));
		
    }
	
	public function nps_reports(Request $request){
		$firstquery = DB::select("SELECT * FROM `batchwise_2` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		$secondquery = DB::select("SELECT * FROM `batchwise_3` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.nps-details',compact('firstquery','secondquery'));
	}
	
	public function nps_reports_new(Request $request){
		$firstquery = DB::select("SELECT * FROM `batchwise_4` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.nps-details-new',compact('firstquery'));
	}
	
	public function nps_reports_five(Request $request){
		$firstquery = DB::select("SELECT * FROM `batchwise_5` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.nps-details-five',compact('firstquery'));
	}
	
	public function nps_reports_six(Request $request){
		$firstquery = DB::select("SELECT * FROM `batchwise_6` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.nps-details-six',compact('firstquery'));
	}
	
	public function nps_reports_seven(Request $request){
		$firstquery = DB::select("SELECT * FROM `batchwise_7` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.nps-details-seven',compact('firstquery'));
	}
	
	public function faculty_leave_report(Request $request){
		$user_id = $request->faculty_id;
		$fmonth = $request->fmonth;
		
		
		
		$firstquery = DB::table("faculty_leave")->where('faculty_id',$request->faculty_id)->where('is_deleted','0');
		if(!empty($fmonth)){
			$firstquery->where('date','>=',$fmonth);
		}else{
			$firstquery->where('date','>=',date('Y-m'));
		}
		
		$firstquery = $firstquery->get();
		return view('admin.all_reports.faculty-leave',compact('firstquery','user_id'));
	}
	
	
	public function employee_complaint(Request $request){
		$user_id     = Input::get('user_id');
		
		return view('admin.all_reports.employee-complaint', compact('user_id'));
	}
	
	public function employee_complaint_store(Request $request){
		$message				=	$request->message;
		$user_id				=	$request->user_id;
		
		
		if(!empty($user_id)){
			if($message!=""){ 
				$data = array(
					'message'			=>	$message,
					'user_id'			=>	$user_id,
				);
					
				DB::table('emp_complaint')->insert($data);
				
				return back()->with('success', "आपका मैसेज CEO सर को भेज दिया गया . आपकी समस्या का समाधान जल्दी ही किया जायेगा। ");	
			}else{
				return back()->with('error', "Required Filed Missing!!");	
			}
		}else{
			return back()->with('error', "User ID Missing!!");	
		}
	}
	
	public function ceo_complaint_view(Request $request){
	    $export     = Input::get('export');
	    $isFaculty  = '';
	    if(!empty($request->isFaculty)){
	   	  $isFaculty=$request->isFaculty;
	    }

		$data = DB::table('emp_complaint')
				->select('emp_complaint.*','users.id as user_id','users.name as uname','users.mobile','departments.name as dname','users.department_type')
				->leftjoin('users','users.id','emp_complaint.user_id')
				->leftjoin('departments','departments.id','users.department_type')
				->where('emp_complaint.parent_id','0')
				->where('emp_complaint.is_deleted','0');
		if(!empty($isFaculty)){
			$data->where('users.role_id',2);
		}else{
		  $data->where('users.role_id','!=',2);
		}
		
		$data=$data->orderby('emp_complaint.cread','ASC')
				->orderby('emp_complaint.created_at','DESC')
				->get();				
				//->skip(0)->limit(10)->get();

		$query=array();
		$sno=1;
		foreach ($data as $key => $value){
			$department_head=DB::table('users')->select('users.name as head_name')->where('users.department_type',$value->department_type)->where('users.role_id',21)->where('users.status',1)->first();

			$branch=DB::table('userbranches')->select('branches.name')
			->leftjoin('branches','branches.id','userbranches.branch_id')
			->where('userbranches.user_id',$value->user_id)->orderby('userbranches.id','DESC')->first();

			$exdata=array();
			$exdata['sno']=$sno++;
			$exdata['uname']=$value->uname;
			$exdata['dname']=$value->dname;
			$exdata['branch']=$branch->name;
			$exdata['mobile']=$value->mobile;
			$exdata['message']=$value->message;
			$exdata['reply']=$value->reply;
			
			if(!empty($department_head->head_name)){
			  $exdata['head_name']=$department_head->head_name;
			}else{
				$exdata['head_name']="-";
			}

			$exdata['cread']=$value->cread;
			$exdata['id']=$value->id;
			$exdata['created_at']= date('Y-m-d H:i:s', strtotime($value->created_at) + 5 * 3600 + 30 * 60);
             
            $query[]=(object) $exdata;
		}

		$query=(object) $query;
		
	   if(!empty($export)){
	   	 return Excel::download(new MSGCeoExport($query), 'MSGCeoData.xlsx');
	   }
	   
	    if(!empty($isFaculty)){
		  return view('admin.all_reports.complaint-faculty', compact('query'));
		}

		return view('admin.all_reports.ceo-complaint', compact('query'));
	}
	
	public function complaint_read(Request $request){		
		$request_id  = $request->request_id; 
		
		if(!empty($request_id)){
			DB::table('emp_complaint')->where('id', $request_id)->update(array('cread' => '1'));
			
			$data=DB::table('emp_complaint')->select('*')->whereRAW('id='.$request_id.' OR parent_id='.$request_id)->get();
			
			return response(['status' => true, 'message' => 'Complaint View','data'=>$data], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing!!!'], 200);
		}
			
	}
	
	public function complaint_history(Request $request, $id){
		$query = DB::table('emp_complaint')
		        ->select('emp_complaint.*')
		        ->where('emp_complaint.user_id',$id)
		        ->where('emp_complaint.parent_id',0)
		        ->orderby('emp_complaint.id','DESC')->get();

		return view('admin.all_reports.employee-complaint-history', compact('id','query'));
	}
	
	public function employee_complaint_reply(Request $request){
		$complaint_id=$request->complaint_id;
		$ceo_id 	= $request->ceo_id;
		$ceo_reply 	= $request->ceo_reply;
		
		if($ceo_id!="" && $ceo_reply!=""){
			//DB::table('emp_complaint')->where('id',$ceo_id)->update(['reply' => $ceo_reply]);
			$complaint=DB::table('emp_complaint')->where('id',$ceo_id);
			$data=$complaint->first();
			if(!empty($data) && $data->reply!=null){
			  $ceo_reply=$data->reply.' ; '.$ceo_reply;
			}

			$complaint->update(['reply'=>$ceo_reply,'cread'=>'1']);
			return back()->with('success', "Your reply has been successfully added.");	
		}else if($complaint_id!="" && $ceo_reply!=""){

			$complaint=DB::table('emp_complaint')->where('id',$complaint_id);
			$data=$complaint->first();
			if(!empty($complaint)){
			  DB::table('emp_complaint')->insert(['parent_id'=>$complaint_id,'message'=>$ceo_reply,'user_id'=>$data->user_id]);
			  $complaint->update(['cread'=>'0']);
			  return back()->with('success', "Your reply has been successfully added.");	
			}
		}else{
			return back()->with('error', "User ID Missing!!");	
		}
	}
	
	public function employee_complaint_delete(Request $request, $id){
		$id = $request->id;
		
		if($id!=""){
			DB::table('emp_complaint')->where('id',$id)->update(array( 'is_deleted' => '1' ));
			return response(['status' => true, 'message' => 'Complaint deleted'], 200);
		}else{
			return response(['status' => false, 'message' => 'Required filed missing'], 200);
		}
	}
	
	public function chanakya_reports(Request $request)
    {
        $user_id     = Input::get('user_id');
		
		
		$mentor_batch_list = array();
		$app_config = array();
		$app_config['is_test_report'] = 0;
		$login_user   = User::with(['user_details'])->where([['id', '=', $user_id]])->first();
		// print_r($login_user->user_details->degination); die;
		if(!empty($login_user)){
			$role_id = $login_user->role_id;
			
			if($role_id==2){
				DB::table('users')->where('id',$user_id)->update(['chanakya_read' => 1]);
				
				$batches = DB::table('users')
					  ->select('batch.id as batch_id','batch.name as batch_name','batch.mentor_id','users.name as user_name')
					  ->leftJoin('batch', 'batch.mentor_id', '=', 'users.id')
					  ->where('users.id', $user_id)
					  ->where('users.status', 1);
				$batches =	$batches->get();
				
				if(count($batches) > 0){
					$mentor_batch_list = json_decode(json_encode($batches),true);
				}
			}
			
		}
		
		return view('admin.all_reports.chanakya_reports', compact('user_id','mentor_batch_list'));
		
    }
	
	
	public function online_nps_reports(Request $request){
		$faculty_id = $request->faculty_id;
		$hrm_erp=["7340"=>"1121","7849"=>"999","5879"=>"1174","7471"=>"1007","7876"=>"1008","7766"=>"1000","7950"=>"973","1581"=>"1122","904"=>"1325","1666"=>"1123","7468"=>"1009","6379"=>"1306","7964"=>"1155","8231"=>"1329","7775"=>"974","6216"=>"1175","5051"=>"1001","6464"=>"1156","8329"=>"1393","905"=>"1071","8213"=>"1323","8049"=>"1307","7339"=>"1124","907"=>"1072","7694"=>"1058","7345"=>"1010","5033"=>"975","8030"=>"1317","7346"=>"1011","7186"=>"1157","8004"=>"1035","6863"=>"1176","8209"=>"1316","5053"=>"1073","7352"=>"1012","7041"=>"976","5957"=>"1013","7119"=>"1125","7478"=>"1036","7129"=>"1158","6615"=>"1069","7384"=>"1177","6693"=>"1178","6009"=>"1179","7902"=>"1075","908"=>"1076","910"=>"1077","6561"=>"1180","6074"=>"1181","8214"=>"1298","6629"=>"1063","5608"=>"1078","8080"=>"1324","7510"=>"1014","7512"=>"1182","7269"=>"1183","6591"=>"1159","7425"=>"1079","6721"=>"1120","8075"=>"1270","6842"=>"977","1565"=>"1126","5708"=>"1184","7137"=>"1160","6773"=>"978","7219"=>"1015","8059"=>"1300","1893"=>"1127","6407"=>"1037","6879"=>"979","7772"=>"1161","6728"=>"1185","6084"=>"1080","6405"=>"1038","7895"=>"1186","6845"=>"980","7223"=>"1187","6378"=>"1304","7792"=>"1081","7106"=>"1318","8258"=>"1371","5572"=>"1082","7343"=>"1016","7771"=>"1313","6082"=>"1083","7924"=>"1188","7883"=>"1070","6802"=>"1128","6970"=>"1305","6846"=>"981","6642"=>"1064","6587"=>"1162","5607"=>"1084","5044"=>"1085","6415"=>"1039","7747"=>"1189","5041"=>"1190","6650"=>"1191","8260"=>"1368","6244"=>"1193","6502"=>"1040","6408"=>"1041","7540"=>"1192","7212"=>"1086","6810"=>"1129","914"=>"1031","915"=>"1087","916"=>"1088","6632"=>"1163","7418"=>"1089","1911"=>"1130","7466"=>"1017","7960"=>"1164","6577"=>"1165","7454"=>"1018","6345"=>"1131","6466"=>"1166","1575"=>"1067","6657"=>"1132","917"=>"1090","6465"=>"1167","5601"=>"1091","6801"=>"1133","918"=>"1092","7187"=>"1168","919"=>"1093","5119"=>"1134","5829"=>"1135","5040"=>"1094","7774"=>"1094","7660"=>"1043","920"=>"1194","6346"=>"1136","8186"=>"1299","5037"=>"1032","946"=>"1195","945"=>"1095","5106"=>"1137","6959"=>"1196","6338"=>"1197","6468"=>"1169","5323"=>"1033","5185"=>"1096","7529"=>"1044","7571"=>"1044","5796"=>"1002","6366"=>"1198","6503"=>"1045","903"=>"1097","5802"=>"1199","1819"=>"1138","949"=>"1200","5604"=>"1098","5610"=>"1098","8033"=>"1226","6505"=>"1099","8003"=>"1046","6496"=>"1201","950"=>"1100","8064"=>"1266","6337"=>"1202","6406"=>"1203","8016"=>"1059","7351"=>"1019","8121"=>"1309","7809"=>"1204","1716"=>"1139","6636"=>"1205","1567"=>"1140","1572"=>"1141","6765"=>"1206","8275"=>"1367","8145"=>"1338","7967"=>"1060","1889"=>"1142","6775"=>"983","6774"=>"1207","1564"=>"1143","923"=>"1047","5039"=>"984","5123"=>"984","8322"=>"1390","5042"=>"1102","6271"=>"1208","7465"=>"1048","926"=>"1103","6103"=>"1104","7081"=>"1020","1573"=>"1144","8085"=>"1322","8147"=>"1319","5612"=>"1049","7495"=>"1209","1719"=>"1066","6478"=>"1066","8321"=>"1392","6045"=>"1105","5606"=>"1106","6393"=>"1107","1576"=>"1145","7580"=>"1210","7897"=>"1061","7348"=>"1021","7645"=>"1108","7350"=>"1022","8215"=>"1302","925"=>"1211","5125"=>"1211","1784"=>"1068","7794"=>"1003","6287"=>"1212","6607"=>"1065","1577"=>"1057","927"=>"1050","928"=>"1109","7472"=>"1023","7850"=>"987","7508"=>"1213","8198"=>"1315","1578"=>"1146","8052"=>"1243","8158"=>"1311","8161"=>"1312","7947"=>"988","7271"=>"1215","8146"=>"1301","5605"=>"989","7467"=>"1024","6844"=>"990","7023"=>"1216","6635"=>"1170","7677"=>"1217","8079"=>"1269","7658"=>"1025","6504"=>"1051","7183"=>"1218","6766"=>"991","6025"=>"992","933"=>"1147","5443"=>"1148","7032"=>"993","1791"=>"1149","5422"=>"1150","5321"=>"1034","1569"=>"1151","5609"=>"1110","6713"=>"1052","6622"=>"1219","7344"=>"1026","932"=>"1053","7517"=>"1027","7579"=>"1220","7896"=>"1004","7267"=>"994","935"=>"1111","5948"=>"1112","6195"=>"1303","6612"=>"1303","5689"=>"1113","5036"=>"995","7547"=>"995","5035"=>"1114","5178"=>"1054","1584"=>"1152","7126"=>"1115","936"=>"1116","5322"=>"1055","7635"=>"996","7655"=>"1222","6462"=>"1171","7768"=>"1005","8082"=>"1268","939"=>"1223","6753"=>"1117","8261"=>"1360","8403"=>"1360","7349"=>"1028","7216"=>"1172","5959"=>"1118","7503"=>"1029","7765"=>"997","6659"=>"1153","7920"=>"1030","7887"=>"1173","7680"=>"1062","5799"=>"1006","8001"=>"998","8100"=>"1314","944"=>"1119","6422"=>"1224","7528"=>"1056","6422"=>"1224","7528"=>"1056","5937"=>"1074","8714"=>"1746","8323"=>"1391"];

		$faculty_id=$hrm_erp[$faculty_id]??null;
		if($faculty_id==null){
			die('Thank you, NPS Not available for you.');
		}

		// $firstquery = DB::select("SELECT * FROM `batchwise_2` WHERE `hrms_faculty_id` = '".$request->faculty_id."'");
		return view('admin.all_reports.online-nps',compact('faculty_id'));
	}

	public function cleanlinessReport(){
		return view('admin.all_reports.cleanliness-report');
	}
	public function cleanlinessStore(Request $request){

		$request->validate([
			'shift'=> 'required',
			'remark'=> 'required',
			'image'=> 'mimes:jpeg,png,jpg,gif,svg|max:5120|required', 
		]);
		
		if ($request->hasFile('image')) {
			$image = $request->file('image');
			$imagePath = time() . '-' . $image->getClientOriginalName(); 
			$image->move(public_path('cleanliness'), $imagePath);  
		}
		$userId = $request->input('user_id');
		$branchId = $request->input('branch_id'); 
	
		DB::table('cleanliness_report')->insert([
			'user_id' => $userId,
			'branch_id' => $branchId,
			'shift' => $request->input('shift'),
			'remark' => $request->input('remark'),
			'image_path' => $imagePath,  
			'created_at' => now()
		]);
		if ($request->ajax()) {
			return response()->json(['message' => 'Report submitted successfully.']);
		}
		return redirect()->back()->with('success', 'Report submitted successfully.');
	}

	public function cleanlinessReportView(Request $request) {
		$userId = Auth::id();
		
		$branchIds = DB::table('userbranches')
			->where('user_id', $userId)
			->pluck('branch_id')
			->toArray();

		
		$query = DB::table('cleanliness_report')
			->leftJoin('users', 'cleanliness_report.user_id', '=', 'users.id')
			->leftJoin('branches', 'cleanliness_report.branch_id', '=', 'branches.id')
			->select(
				'cleanliness_report.*',
				'users.register_id as register_id',
				'users.name as user_name',
				'branches.name as branch_name'
			)
			->whereIn('cleanliness_report.branch_id', $branchIds) 
			->orderBy('cleanliness_report.created_at', 'desc');
	
		if ($request->filled('emp_code')) {
			$query->where('users.register_id', $request->emp_code);
		}
	
		$reports = $query->get();
	
		return view('admin.all_reports.view-cleanliness-report', compact('reports'));
	}
	

	public function cleanlinessReportViewApp(Request $request, $user_id){
		
		$details = DB::table('cleanliness_report')
			->leftJoin('users', 'cleanliness_report.user_id', '=', 'users.id')
			->leftJoin('branches', 'cleanliness_report.branch_id', '=', 'branches.id')
			->select(
				'cleanliness_report.*',
				'users.register_id as register_id',
				'users.name as user_name',
				'branches.name as branch_name'
			)
			->where('cleanliness_report.user_id', $user_id)
			->orderBy('cleanliness_report.created_at', 'desc')
			->get(); 
	
		return view('admin.all_reports.app-view-cleanliness-report', compact('details'));
	}

	public function cleanlinessReportViewAppUpdate(Request $request,$id){
		// dd($request->all());
		$status = $request->input('status');

		if($status == '3'){
			$request->validate([
				'rejectReason' => 'required'
			]);
			$rej_reason = $request->input('rejectReason') ;
		}else{
			$rej_reason = null;
		}
		
		DB::table('cleanliness_report')
        ->where('id', $id)
        ->update([
            'status' => $status,
			'rej_reason' => $rej_reason
        ]);

    return redirect()->back()->with('success', 'Status updated successfully.');
	}
	
	public function complaintCleanliness(){
		return view('admin.all_reports.complaint-cleanliness');
	}
	public function complaintCleanlinessStore(Request $request){
		$request->validate([
			'complaint'=> 'required',
			'media' => 'required|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:10240', // Max 10MB
		]);
		$userId = $request->input('user_id'); 
		$branchId = $request->input('branch_id'); 

		if($request->hasFile('media')){
			$file = $request->file('media');
			$media_path = time() . '.' . $file->getClientOriginalName();
			$file->move(public_path('cleanliness/complaint'),$media_path);
		}

		DB::table('complaint_cleanliness_report')->insert([
			'user_id'    => $userId,
			'branch_id'  => $branchId,
			'complaint'  => $request->input('complaint'),
			'media_path' => $media_path,  
			'created_at' => now()
		]);
		
		return redirect()->back()->with('success', 'Complaint Send Successfully.');
	}
	public function complaintCleanlinessViewApp(Request $request,$user_id){

		$details = DB::table('complaint_cleanliness_report')
			->leftJoin('users', 'complaint_cleanliness_report.user_id', '=', 'users.id')
			->leftJoin('branches', 'complaint_cleanliness_report.branch_id', '=', 'branches.id')
			->select(
				'complaint_cleanliness_report.*',
				'users.register_id as register_id',
				'users.name as user_name',
				'branches.name as branch_name'
			)
			->where('complaint_cleanliness_report.user_id', $user_id)
			->orderBy('complaint_cleanliness_report.created_at', 'desc')
			->get(); 
	
		return view('admin.all_reports.app-view-complaint', compact('details'));
	}
	public function complaintCleanlinessReportApp($user_id){
		$details = DB::table('complaint_cleanliness_report')
			->leftJoin('users', 'complaint_cleanliness_report.user_id', '=', 'users.id')
			->leftJoin('branches', 'complaint_cleanliness_report.branch_id', '=', 'branches.id')
			->select(
				'complaint_cleanliness_report.*',
				'users.register_id as register_id',
				'users.name as user_name',
				'branches.name as branch_name'
			)
			->orderBy('complaint_cleanliness_report.created_at', 'desc')
			->get(); 
	
		return view('admin.all_reports.app-complaint-view-report', compact('details'));
	}
	public function complaintReportUpdateStatus(Request $request,$id){
		$request->validate([
			'status' => 'required',
			'admin_comment' => 'required|string'
		]);
		DB::table('complaint_cleanliness_report')
		->where('id',$id)
		->update([
			'status' => $request->status,
			'comment' => $request->admin_comment,
			'status_updated_by' => $request->updated_by_id,
			'status_updated_at' => now()

		]);
		return back()->with('success', 'Status updated successfully!');
	}

	public function complaintWebView(Request $request){
		$userId = Auth::id();
		
		$branchIds = DB::table('userbranches')
			->where('user_id', $userId)
			->pluck('branch_id')
			->toArray();

		
		$query = DB::table('complaint_cleanliness_report')
			->leftJoin('users', 'complaint_cleanliness_report.user_id', '=', 'users.id')
			->leftJoin('branches', 'complaint_cleanliness_report.branch_id', '=', 'branches.id')
			->select(
				'complaint_cleanliness_report.*',
				'users.register_id as register_id',
				'users.name as user_name',
				'branches.name as branch_name'
			)
			->whereIn('complaint_cleanliness_report.branch_id', $branchIds) 
			->orderBy('complaint_cleanliness_report.created_at', 'desc');
	
		if ($request->filled('emp_code')) {
			$query->where('users.register_id', $request->emp_code);
		}
	
		$reports = $query->get();
		return view('admin.all_reports.view-cleanliness-complaint',compact('reports'));
	}
	
	
	public function faculty_planner_verification(Request $request){
		$user_id = $request->user_id;
		return view('admin.all_reports.mutli-course-planner.planner-verification-request',compact('user_id'));
	}
	
	public function faculty_assign_time(Request $request){
		$user_id 	= $request->user_id;
		$req_id 	= $request->req_id;
		$course 	= $request->course;
		$subject 	= $request->subject;
		$subject_id 	= $request->subject_id;
		$cpsr_id 	= $request->cpsr_id;
		$planner_name 	= $request->planner_name;

		
		$topic_relation = DB::table('course_planner_topic_relation as cptr')
			->leftJoin('planner_request as pr', 'pr.id', '=', 'cptr.req_id')
			->leftJoin('course_planner_sme_relation as cpsr', function ($join) use ($user_id, $subject_id) {
				$join->on('cpsr.req_id', '=', 'pr.id')
					 ->where('cpsr.faculty_id', $user_id)
					 ->where('cpsr.subject_id', $subject_id);
			})
			->leftJoin('topic_master as tm', 'tm.id', '=', 'cptr.topic_id')
			->leftJoin('sub_topic_master as stm', 'stm.id', '=', 'cptr.sub_topic_id')
			->where('cptr.req_id', $req_id)
			->where('tm.subject_id', $subject_id)
			->select('cptr.*', 'tm.name as topic_name', 'stm.name as sub_topic_name','cpsr.faculty_remark')
			->get();

				
				
		return view('admin.all_reports.mutli-course-planner.assign-time',compact('user_id','req_id','course','subject','topic_relation','cpsr_id','planner_name'));
	}
	
	public function faculty_planner(Request $request){
		$user_id = $request->user_id;

		$route = route('faculty-assign-time'); 
				
		$name	   = $request->name;
		$status	   = $request->status;	
		
		// $record = DB::table('planner_request as pr') 
				// ->select('pr.*','course.name as cname','subject.name as sname','users.name as uname','cpsr.fassign','cpsr.subject_id','cptr.fstatus','cpsr.tt_remark')
				// ->leftjoin('course','course.id','pr.course_id')
				// ->leftjoin('course_planner_sme_relation as cpsr','cpsr.req_id','pr.id')
				// ->leftjoin('course_planner_topic_relation as cptr', 'cptr.req_id', '=', 'pr.id')
				// ->leftjoin('subject','subject.id','cpsr.subject_id')
				// ->leftjoin('users','users.id','pr.user_id')
				// ->where('cpsr.faculty_id',$user_id);
		 
		// if($status!='All'){
			// if($status=='Completed'){
				// $record->where('cptr.fstatus',1);
			// }else if($status=='Pending'){
				// $record->where('cptr.fstatus',0);
			// }else if($status=='Save_as_draft'){
				// $record->where('cptr.fstatus',2);
			// }
		// }		
		
		// $record = $record->groupBy('pr.id', 'cpsr.subject_id')->get();
		
		$record = DB::table('planner_request as pr') 
		->select(
			'pr.*',
			'course.name as cname',
			'subject.name as sname',
			'users.name as uname',
			'cpsr.id as cpsr_id',
			'cpsr.fassign',
			'cpsr.subject_id',
			DB::raw('MAX(cptr.fstatus) as fstatus'), // Take the highest fstatus for subject (1 > 2 > 0)
			'cpsr.tt_remark'
		)
		->leftJoin('course', 'course.id', 'pr.course_id')
		->leftJoin('course_planner_sme_relation as cpsr', 'cpsr.req_id', 'pr.id')
		->leftJoin('course_planner_topic_relation as cptr', function($join) {
			$join->on('cptr.req_id', '=', 'pr.id')
				 ->on('cptr.subject_id', '=', 'cpsr.subject_id'); // ensure join by subject too
		})
		->leftJoin('subject', 'subject.id', 'cpsr.subject_id')
		->leftJoin('users', 'users.id', 'pr.user_id')
		->where('cpsr.faculty_id', $user_id)
		->orderby('pr.id','desc')
		->groupBy('pr.id', 'cpsr.subject_id');
		
		if($status!='All'){
			if($status=='Completed'){
				$record->where('cptr.fstatus',1);
			}else if($status=='Pending'){
				$record->where('cptr.fstatus',0);
			}else if($status=='Save_as_draft'){
				$record->where('cptr.fstatus',2);
			}
		}	
		$record = $record->get();

		
		$data = '';
		if(count($record)>0){
			foreach($record as $re){
				if($re->fstatus==1){
					$sText = 'Completed';
					$cText = 'completed-card';
					$buttonText = 'View Planner';
				}else if($re->fstatus==2){
					$sText = 'Save As Draft';
					$cText = 'saveasdaft-card';
					$buttonText = 'Assign Time';
				}else{
					$sText = 'Pending';
					$cText = 'pending-card';
					$buttonText = 'Assign Time';
				}
				
					
				$data .= '<div class="request-card '.$cText.'">
					<div class="planner-header">
						<div class="planner-info">
							<span class="label">Planner For</span>
							<span class="value">'.$re->cname.' - '.$re->planner_name.'</span>
						</div>
						<div class="status">'.$sText.'</div>
					</div>
					
					<div class="planner-content">
						<div class="info-row">
							<span class="label">Subject</span>
							<span class="value">'.$re->sname.'</span>
						</div>

						<div class="info-row">
							<div class="col">
								<span class="label">Assigned On</span>
								<span class="value">' . (!empty($re->fassign) ? date('d-m-Y H:i:s', strtotime($re->fassign)) : '-') . '</span>
							</div>
							<div class="col">
								<span class="label">Requested By</span>
								<span class="value">'.$re->uname.'</span>
							</div>
						</div>

						<div class="info-row">
							<span class="label">Remark</span>
							<span class="value">' . ($re->tt_remark ?? '-') . '</span>
						</div>


						<a class="assign-btn" href="' . $route . '?user_id=' . $user_id . '&req_id=' . $re->id . '&course=' . urlencode($re->cname) . '&subject=' . urlencode($re->sname) . '&subject_id=' . $re->subject_id . '&cpsr_id='.$re->cpsr_id.'&planner_name='.urlencode($re->planner_name).'">
							<button class="assign-btn">' . $buttonText . '</button>
						</a>

					</div>
				</div>';
			}
		}else{
			$data .= '<div class="request-card completed-card">
				<div class="planner-header">
					<h5>No Record Found</h5>
				</div>
			</div>';
		}

			
		echo  $data;
	}
	
	public function faculty_add_time(Request $request){
		$duration 		= $request->duration;
		$tr_ids			=	$request->tr_id;
		$count 			= count($duration);
		
	
		$faculty_remark = $request->faculty_remark;
		$req_id 		= $request->req_id;
		$cpsr_id 		= $request->cpsr_id;
		
		DB::table('course_planner_sme_relation')->where('id',$cpsr_id)->where('req_id', $req_id)->update(['faculty_remark'=>$faculty_remark??'-']);
		
		
		
		$hasValidDuration = false;
		for ($i = 0; $i < $count; $i++) {
			if (!empty($duration[$i]) && $duration[$i] > 0) {
				$hasValidDuration = true;
				break;
			}
		}
		
		if (!$hasValidDuration) {
			return response(['status' => false, 'message' => 'Please enter at least one duration value.'], 422);
		}
		
		
		for ($i = 0; $i < $count; $i++) {
			if (empty($duration[$i])) {
				continue;
			}
			
			$data = [
				"duration" => $duration[$i]??0,
				"fstatus"   => $request->submit_type,
			];
			
			if (!empty($tr_ids[$i])) {
				DB::table('course_planner_topic_relation')->where('id', $tr_ids[$i])->update($data);
			}
		}
		
		return response(['status' => true, 'message' => 'Time Added'], 200);
	}
	
	public function faculty_add_remark(Request $request){
		$faculty_remark = $request->faculty_remark;
		$faculty_id = $request->faculty_id;
		$req_id = $request->req_id;
		$cpsr_id = $request->cpsr_id;
		
		if(!empty($faculty_remark) && !empty($faculty_remark) && !empty($faculty_remark)){
			DB::table('course_planner_sme_relation')->where('id',$cpsr_id)->where('req_id', $req_id)->where('faculty_id', $faculty_id)->update(['faculty_remark'=>$faculty_remark]);
			
			return back()->with('success', 'Remark updated successfully!');
		}else{
			return back()->with('error', 'Required filed missing!');
		}		
	}
	
	
	public function facultyinvoiceadd(Request $request){
		$user_id 	= $request->user_id;
		return view('admin.all_reports.faculty_invoice',compact('user_id'));		
	}
	
	public function facultyinvoicelist(Request $request,$user_id){		
		$month = $request->month;
		
		$record = DB::table('faculty_invoice')->where('user_id',$user_id);
		
		if(!empty($month)){
			$record = $record->where('month',$month);
		}
		
		$record = $record->orderby('id','desc')->get();
		return view('admin.all_reports.faculty_invoice_list',compact('user_id','record'));		
	}
	
	public function facultyinvoicesave(Request $request){
		$user_id 	 = $request->user_id;
		$upload_file = $request->upload_file;
		$month 		 = $request->month;
		
		if(!empty($user_id) && !empty($upload_file) && !empty($month)){
			$check = DB::table('faculty_invoice')->where('user_id',$user_id)->where('month',$month)->where('status', '!=', 2)->count();
			
			if($check > 0){
				return back()->with('error', 'Invoice already uploaded');
			}else{
				$data = array(
					"month"   => $month,
					"user_id" => $user_id,
				);
				
				if($files=$request->file('upload_file')){		
					if(isset($files)){
						$iname = $files->getClientOriginalName();
						$iname = uniqid().'-'.$iname;
						$files->move('laravel/public/invoice',$iname);
						$data['invoice']= $iname;					
					}
				}
				
				DB::table('faculty_invoice')->insert($data);				
				return back()->with('success', 'Invoice uploaded successfully!');
			}
		}else{
			return back()->with('error', 'Required filed missing!');
		}		
	}
}
