<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Buyer;
use Input;
use Validator;
use DB;
use Auth;
use Excel;
use App\Exports\BuyerReportExport;


class BuyerController extends Controller
{
    
    public function index()
    {   
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		
		$search  = Input::get('search'); 
		$msme    = Input::get('msme'); 
		$vcode    = Input::get('vcode'); 
		$pan    = Input::get('pan'); 
		$whereCond = '1=1 ';
		
		if(!empty($search)){ 
			$whereCond .= " AND (name LIKE '%$search%' OR gst_no LIKE '%$search%' OR contact_no LIKE '%$search%')";
		}

		if(!empty(Input::get('status'))){ 
			$status=Input::get('status');
			$whereCond .= " AND status='$status'";
		}
		
		if(!empty($vcode)){ 
			$whereCond .= " AND buyer.id = $vcode";
		}
		
		if(!empty($pan)){ 
			$whereCond .= " AND buyer.pan_no = '$pan'";
		}
		
		if(!empty($msme)){ 
			if($msme==1){
				$whereCond .= " AND (msme_uam_no != '-')";
			}else if($msme==2){
				$whereCond .= " AND (msme_uam_no = '-')";
			}
		}
		
        // $buyer  = Buyer::where('is_deleted', '0')->whereRaw($whereCond)->orderBy('id', 'desc');

		$buyer = Buyer::where('buyer.is_deleted', '0')
				->leftJoin('buyer_bank_details', 'buyer.id', '=', 'buyer_bank_details.buyer_id')
				->where('buyer_bank_details.bnk_status','Active')
    			->whereRaw($whereCond)
    			->select('buyer.*', 'buyer_bank_details.bank_name', 'buyer_bank_details.account','buyer_bank_details.beneficiary','buyer_bank_details.ifsc','buyer_bank_details.bank_address') 
    			->orderBy('buyer.id', 'desc');
    	//echo $buyer->toSql();die;

		
		$buyer = $buyer->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		} 
		
		//echo '<pre>'; print_r($buyer);die;
        return view('admin.buyer.index', compact('buyer','pageNumber','params'));
    }
	
	public function create(Request $request){
		return view('admin.buyer.add');
	}
	
	
	
	public function edit($id)
    {
        $buyer = Buyer::find($id); 
		$bank_details =DB::table('buyer_bank_details')->where('buyer_id',$id)->get();
        return view('admin.buyer.edit', compact('buyer','bank_details'));
    }
	
	
	public function addBill($id, $pid=null){ 
	    $get_bill_detail = ''; 
		$bill_txt        = "Add";
		if(!empty($id) && !empty($pid)){
			$get_bill_detail = DB::table('bill')->where([['id','=', $pid], ['buyer_id', '=', $id]])->first();
			$bill_txt = "Edit";
		}
		$buyer_id         = $id;
		//echo '<pre>'; print_r($get_buyer_detail);die;
		return view('admin.buyer.add-bill',compact('buyer_id','get_bill_detail','bill_txt'));
	}
	
	public function bill($id){
		$buyer_id         = $id;
		$get_buyer_detail = array();
		
		$bill_no         = Input::get('bill_no'); 
		$selectFromDate  = Input::get('from_date');
		$selectToDate    = Input::get('to_date');
		$whereCond       = '1=1 ';
		
		if(!empty($bill_no)){ 
			$whereCond .= " AND (bill_no = '$bill_no')";
		}
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
        if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
			else{
				$whereCond .= ' AND DATE(created_at) >= "'.$selectFromDate.'" AND DATE(created_at) <= "'.$selectToDate.'"';
			}
        } 
		
		
		if(!empty($buyer_id)){
			$get_buyer_detail = DB::table('bill')->where('buyer_id', $buyer_id)->where('is_deleted', '0')->whereRaw($whereCond)->get();
		}
		
		return view('admin.buyer.bill',compact('buyer_id','get_buyer_detail'));
		
	}
	
	public function billStore(Request $request){ 
		if(!empty($request->bill_id)){	
			$rqData = [
						'bill_no'   => 'required',
						'bill_file' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
					  ];
		}
		else{
			$rqData = [
					'bill_no'   => 'required|unique:bill',
					'bill_file' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
				  ];
		}
		$validatedData = $request->validate($rqData);
		
		$inputs  = $request->only('buyer_id','bill_no'); 
		$bill_id = '';
		if (Input::hasfile('bill_file')){
			$bill_id  = $request->bill_id;
            $inputs['bill_file'] = $this->uploadBill(Input::file('bill_file'), $bill_id);
        } 
        
		if(!empty($request->bill_id)){
			$bill_res = DB::table('bill')->where('id', $request->bill_id)->update($inputs);
			$msg = 'Bill Update Successfully';
			$msg1 = 'No Any Update Records';
		}
		else{
			$bill_res = DB::table('bill')->insertGetId($inputs); 
			$msg = 'Bill Added Successfully';
			$msg1 = 'Something Went Wrong !';
		}
    

        if($bill_res) {
			return redirect()->route('admin.buyer.bill',$request->buyer_id)->with('success', $msg);
        } else {
			return redirect()->route('admin.buyer.bill',$request->buyer_id)->with('error', $msg1);
        }		
	}
	
	public function uploadBill($file, $bill_id){
		$drive = public_path(DIRECTORY_SEPARATOR . 'bill' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$newImage = $drive . $filename;
		if(!empty($bill_id)){
			$get_bill_file = DB::table('bill')->where('id', $bill_id)->first();
			if(!empty($get_bill_file->bill_file)){
				unlink($drive.'/'.$get_bill_file->bill_file);
			}
			
		}
		
		$imgResource = $file->move($drive, $filename);
		return $filename;
	}
	
	public function deleteBill($id){
		$bill  = DB::table('bill')->where('id',$id);
		$inputs = array('is_deleted' => '1');
		
        if ($bill->update($inputs)) {
            return redirect()->back()->with('success', 'Bill Deleted Successfully');
        } else {
			return redirect()->back()->with('success', 'Something Went Wrong !');
        }
	}

    public function store(Request $request)
    {
		$validatedData = $request->validate([
			'name'       => 'required',
			'contact_no' => 'required',
			'gst_no' 	 => 'required',
			'address'    => 'required'
        ]);
		
		// dd($request->all());
		
		
		$inputs = $request->only('name','contact_no','gst_no','email','address','pincode','credit_day'); 
		
		if($gst_img=$request->file('gst_img')){	
			$gstname = $gst_img->getClientOriginalName();
			$gstname = uniqid().'-'.$gstname;
			$gst_img->move('laravel/public/buyer',$gstname);
			$inputs['gst_img']= $gstname;					
		}
		
		if($pan_img=$request->file('pan_img')){	
			$panname = $pan_img->getClientOriginalName();
			$panname = uniqid().'-'.$panname;
			$pan_img->move('laravel/public/buyer',$panname);
			$inputs['pan_img']= $panname;					
		}
		
		
		if($msme_uam_file=$request->file('msme_uam_file')){	
			$iname = $msme_uam_file->getClientOriginalName();
			$iname = uniqid().'-'.$iname;
			$msme_uam_file->move('laravel/public/buyer',$iname);
			$inputs['msme_uam_file']= $iname;					
		}
		
		if($aggrement=$request->file('aggrement')){	
			$aname = $aggrement->getClientOriginalName();
			$aname = uniqid().'-'.$aname;
			$aggrement->move('laravel/public/buyer',$aname);
			$inputs['aggrement']= $aname;					
		}
		
		
		$inputs['pan_no'] 		= $request->pan_no;
		$inputs['msme_uam_no']  = $request->msme_uam_no;
		$inputs['type']  = $request->type;
		$inputs['msme_category']  = $request->msme_category;
				
		if($declaration_form=$request->file('declaration_form')){	
			$dname = $declaration_form->getClientOriginalName();
			$dname = uniqid().'-'.$dname;
			$declaration_form->move('laravel/public/buyer',$dname);
			$inputs['declaration_form']= $dname;					
		}
		
		if($bank_proof=$request->file('bank_proof')){	
			$bname = $bank_proof->getClientOriginalName();
			$bname = uniqid().'-'.$bname;
			$bank_proof->move('laravel/public/buyer',$bname);
			$inputs['bank_proof']= $bname;					
		}
		
		if($bank_proof_2=$request->file('bank_proof_2')){	
			$bname2 = $bank_proof_2->getClientOriginalName();
			$bname2 = uniqid().'-'.$bname2;
			$bank_proof_2->move('laravel/public/buyer',$bname2);
			$inputs['bank_proof_2']= $bname2;					
		}
		
		if(!empty($request->id)){
			$buyerId   = Buyer::where('id', $request->id)->first();
			$buyer_res = $buyerId->update($inputs);
		}
		else{
			$buyer = Buyer::create($inputs); 
			$buyer_res  = $buyer->save();
		}
		
		$bankCount = count($request->bank_name);
		foreach ($request->bank_name as $key => $val) {
			$bankId = DB::table('buyer_bank_details')->insertGetId([
				'buyer_id'     => $buyer->id,
				'beneficiary'  => $request->beneficiary[$key],
				'account'      => $request->account[$key],
				'bank_name'    => $request->bank_name[$key],
				'ifsc'         => $request->ifsc[$key],
				'bank_address' => $request->bank_address[$key],
				'bnk_status'   => ($key == $bankCount - 1) ? 'Active' : 'Inactive',
				'created_at'   => now()
			]);

			$this->maintain_history(Auth::user()->id,'buyer_bank_details',$buyer->id,'bank_details_insert',json_encode([
					'beneficiary'  => $request->beneficiary[$key],
					'account'      => $request->account[$key],
					'bank_name'    => $request->bank_name[$key],
					'ifsc'         => $request->ifsc[$key],
					'bank_address' => $request->bank_address[$key],
					'bnk_status'   => ($key == $bankCount - 1) ? 'Active' : 'Inactive'
				])
			);
		}


        if($buyer_res) {
			$this->maintain_history(Auth::user()->id, 'buyer', $buyer->id, 'insert', json_encode($inputs));
            return redirect()->route('admin.buyer.index')->with('success', 'Buyer Added Successfully');
        } else {
            return redirect()->route('admin.buyer.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function update(Request $request, $id)
    {
		// if(Auth::user()->id != 5362){
			// die('Please Contact To 	PRIYANSH DAGA (8107564860)');
		// }
        $validatedData = $request->validate([
			'name'       => 'required',
			'contact_no' => 'required',
			'gst_no'     => 'required',
			'address'    => 'required'
        ]);
		
        $buyer = Buyer::where('id', $id)->first();

        $inputs = $request->only('name','contact_no','gst_no','email','address','status','pincode','credit_day'); 

		if($msme_uam_file=$request->file('msme_uam_file')){	
			$iname = $msme_uam_file->getClientOriginalName();
			$iname = uniqid().'-'.$iname;
			$msme_uam_file->move('laravel/public/buyer',$iname);
			$inputs['msme_uam_file']= $iname;					
		}
		
		
		$inputs['pan_no'] 		= $request->pan_no;
		$inputs['msme_uam_no']  = $request->msme_uam_no;
		$inputs['type']  = $request->type;
		$inputs['msme_category']  = $request->msme_category;
		
		if($declaration_form=$request->file('declaration_form')){	
			$dname = $declaration_form->getClientOriginalName();
			$dname = uniqid().'-'.$dname;
			$declaration_form->move('laravel/public/buyer',$dname);
			$inputs['declaration_form']= $dname;					
		}
		
		if($bank_proof=$request->file('bank_proof')){	
			$bname = $bank_proof->getClientOriginalName();
			$bname = uniqid().'-'.$bname;
			$bank_proof->move('laravel/public/buyer',$bname);
			$inputs['bank_proof']= $bname;					
		}
		
		if($bank_proof_2=$request->file('bank_proof_2')){	
			$bname2 = $bank_proof_2->getClientOriginalName();
			$bname2 = uniqid().'-'.$bname2;
			$bank_proof_2->move('laravel/public/buyer',$bname2);
			$inputs['bank_proof_2']= $bname2;					
		}
		
		if($gst_img=$request->file('gst_img')){	
			$gstname = $gst_img->getClientOriginalName();
			$gstname = uniqid().'-'.$gstname;
			$gst_img->move('laravel/public/buyer',$gstname);
			$inputs['gst_img']= $gstname;					
		}
		
		if($pan_img=$request->file('pan_img')){	
			$panname = $pan_img->getClientOriginalName();
			$panname = uniqid().'-'.$panname;
			$pan_img->move('laravel/public/buyer',$panname);
			$inputs['pan_img']= $panname;					
		}
				
		if($aggrement=$request->file('aggrement')){	
			$aname = $aggrement->getClientOriginalName();
			$aname = uniqid().'-'.$aname;
			$aggrement->move('laravel/public/buyer',$aname);
			$inputs['aggrement']= $aname;					
		}

        if ($buyer->update($inputs)) {
			$this->updateBuyerBankDetails($request, $buyer->id);			
			$this->maintain_history(Auth::user()->id, 'buyer', $id, 'update', json_encode($inputs));
						
            return redirect()->route('admin.buyer.index')->with('success', 'Buyer Updated Successfully');
        } else {
            return redirect()->route('admin.buyer.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function updateBuyerBankDetails(Request $request, $buyerId)
{
    $bankDetails = $request->input('bank_details');
	// DB::table('buyer_bank_details')
	// ->where('buyer_id', $buyerId)
	// ->update(['bnk_status' => 'Inactive']);
	// dd($bankDetails);
	foreach ($bankDetails as $detail) {
		if (!empty($detail['id'])) {
			DB::table('buyer_bank_details')
				->where('id', $detail['id'])
				->where('buyer_id', $buyerId)
				->update([
					'beneficiary'  => $detail['beneficiary'],
					'account'      => $detail['account'],
					'bank_name'    => $detail['bank_name'],
					'ifsc'         => $detail['ifsc'],
					'bank_address' => $detail['bank_address'],
					'bnk_status'   => $detail['bnk_status'],
					'updated_at'   => now(),
				]);

			$this->maintain_history(Auth::user()->id,'buyer_bank_details',$buyerId,'bank_details_update',json_encode($detail));

		} else {
			if (!empty($detail['account'])) {
				$lastId = DB::table('buyer_bank_details')->insertGetId([
					'buyer_id'     => $buyerId,
					'beneficiary'  => $detail['beneficiary'],
					'account'      => $detail['account'],
					'bank_name'    => $detail['bank_name'],
					'ifsc'         => $detail['ifsc'],
					'bank_address' => $detail['bank_address'],
					'created_at'   => now()
				]);

				$this->maintain_history(Auth::user()->id,'buyer_bank_details',$buyerId,'bank_details_insert',json_encode($detail));
			}
		}
	}

}

	
    public function destroy($id)
    {   
        $buyer  = Buyer::find($id);
		$inputs = array('is_deleted' => '1');
		
        if ($buyer->update($inputs)) {
            return redirect()->back()->with('success', 'Byuer Deleted Successfully');
        } else {
            return redirect()->route('admin.buyer.index')->with('error', 'Something Went Wrong !');
        }
    }
	
	public function buyer_report_excel(Request $request){
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		
		$product_id  = $request->product_id; 
		$status		 = $request->status;
		
		
		$search = $request->search;
		$msme   = $request->msme;

		$comman_result = Buyer::where('buyer.is_deleted', '0')
						->leftJoin('buyer_bank_details', 'buyer.id', '=', 'buyer_bank_details.buyer_id')
						->where('buyer_bank_details.bnk_status','Active')
						->where('status', $status??'Active')
						->when(!empty($search), function ($query) use ($search) {
							$query->where(function ($q) use ($search) {
								$q->where('name', 'like', "%$search%")
								  ->orWhere('gst_no', 'like', "%$search%")
								  ->orWhere('contact_no', 'like', "%$search%");
							});
						})
						->when(!empty($msme), function ($query) use ($msme) {
							if ($msme == 1) {
								$query->where('msme_uam_no', '!=', '-');
							} elseif ($msme == 2) {
								$query->where('msme_uam_no', '=', '-');
							}
						})
						->select('buyer.*', 'buyer_bank_details.bank_name', 'buyer_bank_details.account','buyer_bank_details.beneficiary','buyer_bank_details.ifsc','buyer_bank_details.bank_address')
						->get();

				
		$responseArray = array();
		if(count($comman_result) > 0){
			foreach($comman_result as $key=>$value){	
				$msmeuam  = 'https://hrm.utkarshupdates.com/laravel/public/buyer/';
			
				$responseArray[$key]['id'] 			= 	$value->id;
				$responseArray[$key]['name'] 		= 	$value->name;
				$responseArray[$key]['contact_no'] 	= 	$value->contact_no;
				$responseArray[$key]['address'] 	= 	$value->address;
				$responseArray[$key]['gst_no'] 		= 	$value->gst_no;
				$responseArray[$key]['email'] 		= 	$value->email;
				$responseArray[$key]['beneficiary'] = 	$value->beneficiary;
				$responseArray[$key]['account'] 	= 	$value->account;
				$responseArray[$key]['bank_name'] 	= 	$value->bank_name;
				$responseArray[$key]['ifsc'] 		= 	$value->ifsc;
				$responseArray[$key]['bank_address']= 	$value->bank_address;
				$responseArray[$key]['pincode']= 	$value->pincode;
				$responseArray[$key]['credit_day']= 	$value->credit_day;
				$responseArray[$key]['type']= 	$value->type;
				
				if($value->msme_uam_file!='-'){
					$responseArray[$key]['msme_uam_file'] 		= 	$msmeuam.$value->msme_uam_file;
				}
				
				$responseArray[$key]['msme_uam_no'] = 	$value->msme_uam_no;
				
				if($value->declaration_form!='-'){
					$responseArray[$key]['declaration_form'] 		= 	$msmeuam.$value->declaration_form;
				}

				if($value->gst_img){
					$responseArray[$key]['gst_img'] 		= 	$msmeuam.$value->gst_img;
				}

				if($value->pan_img){
					$responseArray[$key]['pan_img'] 		= 	$msmeuam.$value->pan_img;
				}

				if($value->bank_proof){
					$responseArray[$key]['bank_proof'] 		= 	$msmeuam.$value->bank_proof;
				}

				if($value->bank_proof_2){
					$responseArray[$key]['bank_proof_2']    = 	$msmeuam.$value->bank_proof_2;
				}

				if($value->aggrement){
					$responseArray[$key]['aggrement'] 		= 	$msmeuam.$value->aggrement;
				}
				
				$responseArray[$key]['pan_no'] 		= 	$value->pan_no;
				$responseArray[$key]['status']    = $value->status;
				$responseArray[$key]['created_at']    = $value->created_at;
				$responseArray[$key]['msme_category']    = $value->msme_category;
			}
		} 
		
        if(count($responseArray) > 0){
            return Excel::download(new BuyerReportExport($responseArray), 'BuyerReport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}
	
	public function vendor_view(Request $request,$id){
		$buyer_history = DB::table('asset_request_notification as arn')->select('arn.*','asset_request.request_type')->leftjoin('asset_request','asset_request.id','arn.request_id')->where('arn.company',$id)->get();
		
		return view('admin.buyer.vendor-view',compact('buyer_history'));
	}
	
	
	public function vendor_new_list()
    {   
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		
		
		
		$search  = Input::get('search'); 
		
		$buyer = DB::table('vendor_new')
    			->orderBy('id', 'desc');

		
		$buyer = $buyer->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (50*($page-1));
			
			$pageNumber = $pageNumber +1;
		} 
		
        return view('admin.buyer.vendor_new_list', compact('buyer','pageNumber','params'));
    }


	private function maintain_history($user_id, $table_name, $table_id, $type, $save_data){
		$history_data = array(                  
			'user_id'    => $user_id,
			'table_name' => $table_name,
			'table_id'   => $table_id,
			'type'       => $type,
			'save_data'  => $save_data
		);                    
		return DB::table('buyer_history')->insert($history_data);
	}
	
	public function vendor_changes_history(Request $request,$id){
		$history = DB::table('buyer_history')
					->select('buyer_history.*','users.name as uname')
					->leftjoin('users','users.id','buyer_history.user_id')
					->where('table_id',$id)->get();
		return view('admin.buyer.buyer_history', compact('history'));
	}
}
