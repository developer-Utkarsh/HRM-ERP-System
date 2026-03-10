<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Invoice;
use App\CreditNote;
use Input;
use DB;
use Excel;
use App\Exports\CourseBySubjectExport;
use Dompdf\Dompdf;
use Options;
use DataTables;
use Auth;

class CrmdeskController extends Controller
{
	
	public function search()
    {
		$logged_name       = Auth::user()->name;
		$department_type       = Auth::user()->department_type;
		$department = DB::table('departments')->select('name')->where('id', $department_type)->first();
		$department_name = "";
		if(!empty($department)){
			$department_name       = $department->name;
		}
        return view('admin.crmdesk.search', compact('logged_name','department_name'));
    }
	
	public function zoho_gettoken(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://accounts.zoho.in/oauth/v2/token?refresh_token=1000.d3cdfa6134f5676c89921e6b4412e804.9ed43e7856ceb96ad20d7c99d0b3cc85&client_id=1000.15TYRU4BD5D2HLPLY868GFY6QMSDUU&client_secret=f04be1c425ca82ab4c36af12a92977b1987621db0d&scope=Desk.tickets.ALL,Desk.search.READ&redirect_uri=https://www.utkarsh.com/&grant_type=refresh_token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
		  CURLOPT_SSL_VERIFYHOST =>false,
		  CURLOPT_SSL_VERIFYPEER =>false,
        ));

        $resp = curl_exec($curl);
        curl_close($curl);
		// print_r($resp); die;
        $resp=json_decode($resp,true);
		// echo "<pre>"; print_r($resp); die;
		DB::table('token')->where('type', 'zoho')->update([ 'token' => $resp['access_token'] ]);
        return $resp['access_token'];
    }
	
	public function get_tickets($token,$params){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://desk.zoho.in/api/v1/tickets/search?$params",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_SSL_VERIFYHOST =>false,
		  CURLOPT_SSL_VERIFYPEER =>false,
		  CURLOPT_HTTPHEADER => array(
			"Authorization: Zoho-oauthtoken $token",
			'orgId: 60002777408'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $result = json_decode($response);
	}
	
	public function search_result(Request $request){
		
		$type = $request->type;
		$name = $request->name;
		$html = "";
		if (!empty($type) && !empty($name)){
            
			$params = "";
			if($type=='mobile'){
				$params = "phone=$name";
			}
			elseif($type=='email'){
				$params = "email=$name";
			}
			elseif($type=='ticket'){
				$params = "ticketNumber=$name";
			}
			else{
				die('Something Went Wrong.');
			}
			
			$token_get = DB::table('token')->where('type', 'zoho')->first();
			$token =  $token_get->token;
			
			
			$result = $this->get_tickets($token,$params);
			if(!empty($result->errorCode)){
				$token = $this->zoho_gettoken();
				$result = $this->get_tickets($token,$params);
			}
			
			if(!empty($result)){
				$data = $result->data;
				
				// print_r($data);
				// die();
				
				foreach($data as $val){
					$html .="<tr><td style='line-height:22px;position: relative;'> <label class='text-primary text-right w-100' style='position: absolute; top: 14px;left:-6px;'>".$val->status."</label>
					<a href='javascript:void(0);' class='call_activity' data-id='".$val->id."' title='Click Here'>
						<strong>#".$val->ticketNumber."</strong>&nbsp;&nbsp;</br>
						<span class='text-dark'>".$val->subject."&nbsp;&nbsp;(".$val->phone.")</span> </br>
						<strong>Date : </strong><span class='text-dark'>".date('d-m-Y H:i:s', strtotime($val->createdTime))."</span> 
					</a>

					</td></tr>";
				}
			}
			else{
				$html .="<tr><td style='line-height:22px;'> Not Found</td></tr>";
			}
        }
		
		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function get_ticket_activity($token,$id){
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://desk.zoho.in/api/v1/tickets/$id/conversations",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_SSL_VERIFYHOST =>false,
		  CURLOPT_SSL_VERIFYPEER =>false,
		  CURLOPT_HTTPHEADER => array(
			"Authorization: Zoho-oauthtoken $token",
			'Cookie: 2eed0b67fd=9e46bce09d6082e7677cf15d571af1f3; JSESSIONID=2336A2AC63AF106E6A316A9ECF796E12; _zcsr_tmp=c7771239-0aee-45d2-b600-93f9da5cea60; crmcsr=c7771239-0aee-45d2-b600-93f9da5cea60'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $result = json_decode($response);
	}
	
	public function get_ticket_fullresponse($token,$id,$thread_id){

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://desk.zoho.in/api/v1/tickets/$id/threads/$thread_id",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST =>false,
		  CURLOPT_SSL_VERIFYPEER =>false,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
			"Authorization: Zoho-oauthtoken $token",
			'orgId: 60002777408',
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		return $result = json_decode($response);
			
	}
	
	public function call_activity(Request $request){
		
		$id = $request->id;
		// $id = 16227000277805363;
		$html = "";
		if (!empty($id)){
			
			$token_get = DB::table('token')->where('type', 'zoho')->first();
			$token =  $token_get->token;
			
			
			$result = $this->get_ticket_activity($token,$id);
			if(!empty($result->errorCode)){
				$token = $this->zoho_gettoken();
				$result = $this->get_ticket_activity($token,$id);
			}
			// echo "<pre>"; print_R($result); die;
			if(!empty($result)){
				$data = $result->data;
				foreach($data as $val){
					$thread_id = $val->id;
					if($val->type=="thread"){
						$result_thread = $this->get_ticket_fullresponse($token,$id,$thread_id);
						if($val->direction=="out"){
							$html .="<tr class='thread-$thread_id'><td style='background:#f0f6ff'> <strong class='text-dark'> <span class='text-primary'>Agent - </span> ";
							$html .= $val->author->name . "( ".date('d-m-Y H:i:s', strtotime($val->createdTime))." ) </strong> </br>";
							$html .="&nbsp;&nbsp;&nbsp;&nbsp; ".$result_thread->content;
							// echo "<pre>"; print_r($result_thread); die;
							foreach($result_thread->attachments as $key=>$att_val){
								$html .="<br/> <a target='_blank' href='".$att_val->href."'>View File </a>";
							}
							$html .="</td></tr>";
							
						}
						else{
							$html .="<tr class='thread-$thread_id'><td style='background:#f0f6ff'> <strong class='text-dark'> <span class='text-primary'>Student -</span> ";
							$html .= $val->author->name . "( ".date('d-m-Y H:i:s', strtotime($val->createdTime))." ) </strong></br>";
							$html .="&nbsp;&nbsp;&nbsp;&nbsp; ".$result_thread->content;
							foreach($result_thread->attachments as $key=>$att_val){
								$html .="<br/> <a target='_blank' href='".$att_val->href."'>View File </a>";
							}
							$html .="</td></tr>";
						}
					}
					else{
						$html .="<tr class='thread-$thread_id'><td style='background:#f0f6ff'> <strong class='text-dark'> <span class='text-primary'>Agent Private -</span> ";
						$html .= $val->commenter->name . "( ".date('d-m-Y H:i:s', strtotime($val->modifiedTime))." ) </strong></br>";
						$html .="&nbsp;&nbsp;&nbsp;&nbsp;  ".html_entity_decode($val->encodedContent)." </td></tr>";
					}
					
				}
			}
        }
		
		return response(['status' => true, 'html' => $html], 200);
	}
	
}
