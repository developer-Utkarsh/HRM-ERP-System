<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Input;
use DB;
use App\Appraisal;
use App\AppraisalQuestions;
use App\NewTask;
use Auth;
use App\ApiNotification;

class FreelancerController extends Controller
{
    
    public function index()
    {  
		$month     =  Input::get('month');
		
		
		$query  = DB::table('freelancer');
		
		$month_year_to_days = array();
		if(!empty($month)){
			$month_year_to_days = explode('-',$month);
		}
		
		if(!empty($month_year_to_days)){
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
			
			$query->whereRaw("(MONTH(created_at) = $mt and YEAR(created_at) = $yr)");
		}		
		
		$record = $query->orderBy('id', 'DESC')->get();

        return view('admin.freelancer.index',compact('record'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		 return view('admin.freelancer.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$tittle = $request->tittle;
		$msg = $request->msg;	
		
		
		if (!empty($tittle) && !empty($msg)) {
			$data = array(
				"tittle"	=>	$tittle,
				"msg"	=>	$msg,
			);
			
			DB::table('freelancer')->insert($data);
            return redirect()->route('admin.freelancer.index')->with('success', 'Added Successfully');
        } else {
            return redirect()->route('admin.freelancer.index')->with('error', 'Something Went Wrong !');
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change_status(Request $request)
    {
		$id = $request->id;
		$status = $request->status;
       
		if(!empty($id) && !empty($status)){	   
			DB::table('freelancer')->where('id',$id)->update(['status'=>$status]);
            return redirect()->route('admin.freelancer.index')->with('success', 'Updated Successfully');
		}else{
			return redirect()->route('admin.freelancer.index')->with('error', 'Something went wrong!!');
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
       
    }	
	
	public function freelancer_invoice(Request $request){
		$name 		 = $request->name;
		$phone 		 = $request->phone;
		$location 	 = $request->location;
		$month 		 = $request->month;
		$description = $request->description;
		$amount 	 = $request->amount;
		$pan 		 = $request->pan;
		$accountName = $request->accountName;
		$accountNo   = $request->accountNo;
		$ifsc 		 = $request->ifsc;
		
		if(!empty($name)){
			$idata  = array(
				"name"			=>	$name,
				"phone"			=>		$phone,
				"location"		=>$location,
				"month"			=>$month,
				"description"	=>$description,
				"amount"		=>$amount,
				"pan"			=>$pan,
				"account_name"	=>$accountName,
				"account_no"	=>$accountNo,
				"ifsc"			=>$ifsc,
			);
			
			$id = DB::table('freelancer_invoice')->insertGetId($idata);
			
			
			
			echo json_encode(['status' => 'success',  'message' => 'success','insert_id' => $id]);
		}else{
			echo json_encode(['status' => 'false', 'message' => 'etorr']);
		}
	}
	
	public function freelancer_invoice_history(Request $request){
		$record = DB::table('freelancer_invoice')->orderBy('id', 'DESC')->get();
		return view('admin.freelancer.invoice_history',compact('record'));
	} 
	
	public function freelancer_invoice_download(Request $request, $id){
		$record = DB::table('freelancer_invoice')->where('id',$id)->first();
		return view('admin.freelancer.download_invoice',compact('record'));
	}
	
	public function faculty_invoice(Request $request){
		$month = $request->month;
		$faculty_id = $request->faculty_id;
		$record = DB::table('faculty_invoice')
					->select('faculty_invoice.*','users.name')
					->leftjoin('users','users.id','faculty_invoice.user_id');
		
		if(!empty($month)){
			$record = $record->where('faculty_invoice.month',$month);
		}else{
			$record = $record->where('faculty_invoice.month',date('Y-m'));
		}
		
		if(!empty($faculty_id)){
			$record = $record->where('faculty_invoice.user_id',$faculty_id);
		}
		
		$record = $record->orderby('faculty_invoice.id','desc')
					->get();
		
		$faculty = DB::table('users')->where('status',1)->where('role_id',2)->get();
		return view('admin.freelancer.faculty_invoice',compact('record','faculty'));
	}
	
	public function invoicestautsupdate(Request $request){
		$status = $request->status;
		$invoice_id = $request->invoice_id;
		$remark = $request->remark;
		
		if(!empty($invoice_id) && !empty($status)){	   
			DB::table('faculty_invoice')->where('id',$invoice_id)->update(['status'=>$status,'reason'=>$remark]);
            return redirect()->route('admin.freelancer.faculty-invoice-history')->with('success', 'Updated Successfully');
		}else{
			return redirect()->route('admin.freelancer.faculty-invoice-history')->with('error', 'Something went wrong!!');
		}
	}
}
