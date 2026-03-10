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
use Session;

use Illuminate\Support\Facades\Cache;

class CrmdeskController extends Controller
{
	
	public function get_agents($token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://desk.zoho.in/api/v1/agents?limit=200&status=ACTIVE&departmentIds=16227000162608925",
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

	public function get_offline_courses($branch){
		$response=Cache::remember('get_offline_course_All'.$branch,600,function()use($branch){	
			$url="http://utkarshpublications.com/soft/apis/offline-admissionapis/web_running_courses.php?query=branch_wise_course_all&branch=".$branch;
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
			return $response;
		});

		return $result = json_decode($response);
	}
	
	public function get_all_courses(){
		$url = "https://apps-s3-prod.utkarshapp.com/reports/course_list_with_cat_1.json";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);
		curl_close($curl);
		return $result = json_decode($response);
	}

	public function unique_array($my_array, $key) { 
		$result = array(); 
		$i = 0; 
		$key_array = array(); 
		
		foreach($my_array as $val) { 
			if (!in_array($val[$key], $key_array)) { 
				$key_array[$i] = $val[$key]; 
				$result[$i] = $val; 
			} 
			$i++; 
		} 
		return $result; 
	}  
	
	public function search()
    {
    	

		$logged_name       = Auth::user()->name;
		$department_type       = Auth::user()->department_type;
		$department = DB::table('departments')->select('name')->where('id', $department_type)->first();
		$department_name = "";
		if(!empty($department)){
			$department_name       = $department->name;
		}
		
		// $all_courses = $this->get_all_courses();
		// $all_courses = json_decode(json_encode($all_courses),true);
		// echo "<pre>";print_r($all_courses); die;
		//$category_name = $this->unique_array($all_courses, "category name");

		// $category_name = array();
		// if(count($all_courses)>0){
			// foreach($all_courses as $val){
				// $mcategory=explode(",",$val['category name']);
				// for($i=0;$i<count($mcategory);$i++){
				  // $category_name[]=$mcategory[$i];
				// }
			// }
		// }

		// $category_name=array_unique($category_name,SORT_STRING);
		// $category_name=array_values($category_name);
		
		
		$all_courses	=	$this->getMainCategory();
		$all_courses  =$all_courses->data??[];
		$category_name=[]; 
		foreach($all_courses as $val){
			$category_name[] =	$val->main_category;
		}
		
		$category_name=array_unique($category_name,SORT_STRING);
		$category_name=array_values($category_name);
		
		$today_date = date('Y-m-d');
		$file_name = "laravel/public/zoho-agent/zoho-agent-$today_date.txt";
		if(!file_exists($file_name)){
			$token_get = DB::table('token')->where('type', 'zoho')->first();
			$token =  $token_get->token;
			$result = $this->get_agents($token);
			if(!empty($result->errorCode)){
				if($result->errorCode =='INVALID_OAUTH'){
					$token = $this->zoho_gettoken();
					$result = $this->get_agents($token);
				}
			}
			$agents = array();
			if(!empty($result)){
				$agents_get = $result->data;
				foreach($agents_get as $val){
					$agents[$val->id] = $val->name;
				}
			}
			file_put_contents($file_name, json_encode($agents));
		}

		$offlinejaipur = array();
		$offlinejodhpur = array();
		$offlinebihar = array();
		$offlineprayagraj = array();
		$vidyapeethjodhpur = array();
		$mpindore = array();
		$nvs=array();
		$get_crm_courses = DB::table('crm_courses')->where('status', '1')->get();
		if(!empty($get_crm_courses)){
			foreach($get_crm_courses as $crm_val){
				if($crm_val->type=="Jaipur Offline"){
					$offlinejaipur[] = array('course'=>$crm_val->name);
				}
				else if($crm_val->type=="Jodhpur Offline"){
					$offlinejodhpur[] = array('course'=>$crm_val->name);
				}
				else if($crm_val->type=="Bihar Offline"){
					$offlinebihar[] = array('course'=>$crm_val->name);
				}
				else if($crm_val->type=="Prayagraj Offline"){
					$offlineprayagraj[] = array('course'=>$crm_val->name);
				}else if($crm_val->type=="VIDYAPEETH JODHPUR"){
					$vidyapeethjodhpur[] = array('course'=>$crm_val->name);
				}else if($crm_val->type=="MP (Indore) Offline"){
					$mpindore[] = array('course'=>$crm_val->name);
				}else if($crm_val->type=="Nehal Virtual School"){
					$nvs[] = array('course'=>$crm_val->name);
				}
			}
		}
		
		// print_r(json_encode($offlinejaipur)); die;
		// $offlinejodhpur=$this->get_offline_courses("Jodhpur");
		// $offlineprayagraj=$this->get_offline_courses("Prayagraj");
		// $offlinejaipur=$this->get_offline_courses("Jaipur");
		
	$StudentSupport = DB::table('course_type')->where('parent_id',0)->where('status',1)->get();
		
    return view('admin.crmdesk.search', compact('all_courses','logged_name','department_name','category_name','offlinejodhpur','offlinebihar','offlinejaipur','offlineprayagraj','vidyapeethjodhpur','mpindore','nvs','StudentSupport'));
    }
	
	public function zoho_gettoken(){
		// return;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://accounts.zoho.in/oauth/v2/token?refresh_token=1000.3a177c1930222fb58fd713ea5fff511a.fe2f6801741833f1c7c6757f9eb836e7&client_id=1000.15TYRU4BD5D2HLPLY868GFY6QMSDUU&client_secret=f04be1c425ca82ab4c36af12a92977b1987621db0d&scope=Desk.tickets.ALL,Desk.contacts.ALL,Desk.contacts.READ,Desk.contacts.WRITE,Desk.contacts.CREATE,Desk.contacts.UPDATE,Desk.search.READ,Desk.settings.READ,Desk.basic.READ&redirect_uri=https://www.utkarsh.com/&grant_type=refresh_token',
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
	  if(!empty($resp['access_token'])){
		  DB::table('token')->where('type', 'zoho')->update([ 'token' => $resp['access_token'] ]);
      return $resp['access_token'];
    }
    return;
  }
  
	public function zoho_gettoken_old(){
		return;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://accounts.zoho.in/oauth/v2/token?refresh_token=1000.3a177c1930222fb58fd713ea5fff511a.fe2f6801741833f1c7c6757f9eb836e7&client_id=1000.15TYRU4BD5D2HLPLY868GFY6QMSDUU&client_secret=f04be1c425ca82ab4c36af12a92977b1987621db0d&scope=Desk.tickets.ALL,Desk.contacts.ALL,Desk.contacts.READ,Desk.contacts.WRITE,Desk.contacts.CREATE,Desk.contacts.UPDATE,ZohoCRM.modules.ALL,Desk.search.READ,Desk.settings.READ,Desk.basic.READ&redirect_uri=https://www.utkarsh.com/&grant_type=refresh_token',
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
	  if(!empty($resp['access_token'])){
		  DB::table('token')->where('type', 'zoho')->update([ 'token' => $resp['access_token'] ]);
      return $resp['access_token'];
    }
    return;
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
				if($result->errorCode =='INVALID_OAUTH'){
					$token = $this->zoho_gettoken();
					$result = $this->get_tickets($token,$params);
				}
				else{
					$html .="<tr><td style='line-height:22px;'> ".$result->message."</td></tr>";
					return response(['status' => true, 'html' => $html], 200);
				}
			}

			// echo "<pre>"; print_r($result); die;
			/* if($result->errorCode =='UNAUTHORIZED'){
       $token = $this->zoho_gettoken();
       return;
			} */

			if(!empty($result)){
				$data = $result->data;
				
				$today_date = date('Y-m-d');
				$file_name = "laravel/public/zoho-agent/zoho-agent-$today_date.txt";
				if(!file_exists($file_name)){
					$file_name = "laravel/public/zoho-agent/zoho-agent.txt";
				}				
				$agents_name = json_decode(file_get_contents($file_name),TRUE);
				
				foreach($data as $val){
					$html .="<tr><td style='line-height:22px;position: relative;'> <label class='text-primary text-right w-100 ticket_status'>".$val->status."</label>
					<a href='javascript:void(0);' class='call_activity' id='".$val->id."' onClick='call_activity(this)' data-id='".$val->id."' data-email='".$val->email."' title='Click Here'>
						<strong>#".$val->ticketNumber."</strong>&nbsp;&nbsp;</br>
						<span class='text-dark'>".$val->subject."&nbsp;&nbsp;(".$val->phone.")</span> </br>
						<strong>Date : </strong><span class='text-dark'>".date('d-m-Y H:i:s', strtotime($val->createdTime))."</span>";
					$html .=" <strong>Department : </strong><span class='text-dark'>";
					if(!empty($val->department)){
						$html .= $val->department->name;
					}
					$html .="</span></br>";
					$html .="</a>";
						$assigneeId = "";
						$html .=" <strong>Assigned : </strong><span class='text-dark assigned_name'>";
						if(!empty($val->assignee)){
							$assigneeId = $val->assignee->id;
							$html .= $val->assignee->firstName ." ". $val->assignee->lastName;
						}
						$html .="</span>";
						
						if(!empty($agents_name)){
							$html .=" <span style='float: right;'>&nbsp;&nbsp;<strong>Assigned To : </strong>"; 
							$html .=" <select class='agent_assign' data-id='".$val->id."'>";
							$html .=" <option value=''> --Select-- </option>";
							foreach($agents_name as $a_key=>$a_val){
								$selected = "";
								if($assigneeId ==$a_key){
									$selected = 'selected';
								}
								$html .=" <option value='$a_key' $selected > $a_val </option>";
							}
							$html .="</select><br/><strong class='please_wait_agent text-danger'></strong></span>"; 
						}
						
					

					$html .="</td></tr>";
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
				if($result->errorCode =='INVALID_OAUTH'){
					$token = $this->zoho_gettoken();
					$result = $this->get_ticket_activity($token,$id);
				}
				else{
					$html .="<tr><td style='line-height:22px;'> ".$result->message."</td></tr>";
					return response(['status' => true, 'html' => $html], 200);
				}
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
	
	public function activity_reply_curl($token,$request){
		$logged_name = Auth::user()->name."(".Auth::user()->register_id.")";
		$ticket_id = $request->ticket_id;
		$ticket_email = $request->ticket_email;
		$description = $request->description;
		$description .= $description ." - Regards $logged_name";
		$description = str_replace(array("\r", "\n"), '', $description);
		$url = "https://desk.zoho.in/api/v1/tickets/$ticket_id/sendReply";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "orgId: 60002777408",
		   "Authorization: Zoho-oauthtoken $token",
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = "{'ticketStatus' : 'Closed','channel' : 'EMAIL','to' : '$ticket_email','fromEmailAddress' : 'support@utkarsh.com','contentType' : 'plainText','content' : '$description','isForward' : 'true'}";

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		return $result = json_decode($resp);
	}
	
	public function activity_comment_curl($token,$request){
		$logged_name = Auth::user()->name."(".Auth::user()->register_id.")";
		$ticket_id = $request->ticket_id;
		$ticket_email = $request->ticket_email;
		$description = $request->description;
		$description = $description ." - Regards $logged_name";
		$description = str_replace(array("\r", "\n"), '', $description);

		$url = "https://desk.zoho.in/api/v1/tickets/$ticket_id/comments";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "orgId: 60002777408",
		   "Authorization: Zoho-oauthtoken $token",
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		//echo $data = "{'isPublic' : 'false','contentType' : 'plainText','content' : '$description'}";
		$data = '{"isPublic" : false,"contentType" : "plainText","content" :"'.$description.'"}';

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		return $result = json_decode($resp);
	}
	
	public function activity_reply(Request $request){
		
		// return response(['status' => true, 'message' => 'Reply Successfully'], 200); exit;
		
		$token_get = DB::table('token')->where('type', 'zoho')->first();
		$token =  $token_get->token;
		
		if($request->reply_type=='reply'){
			$result = $this->activity_reply_curl($token,$request);
			// echo  "<pre>"; print_r($result);die;
			if(!empty($result->errorCode)){
				if($result->errorCode =='INVALID_OAUTH'){
					$token = $this->zoho_gettoken();
					$result = $this->activity_reply_curl($token,$request);
				}
				else{
					return response(['status' => false, 'message' => $result->message], 200); exit;
				}
			}
		}
		else if($request->reply_type=='comment'){
			$result = $this->activity_comment_curl($token,$request);
			// echo  "<pre>"; print_r($result);die;
			if(!empty($result->errorCode)){
				if($result->errorCode =='INVALID_OAUTH'){
					$token = $this->zoho_gettoken();
					$result = $this->activity_comment_curl($token,$request);
				}
				else{
					return response(['status' => false, 'message' => $result->message], 200); exit;
				}
			}
		}
		else{
			$result = array();
		}
		
		if(!empty($result)){
			return response(['status' => true, 'message' => 'Reply Successfully'], 200); exit;
		}
		else{
			return response(['status' => false, 'message' => 'Something Went Wrong.'], 200); exit;
		}
		
	}
	
	
	public function assign_agent_curl($token,$request){
		$ticket_id = $request->ticket_id;
		$agent_id = $request->agent_id;
		
		$url = "https://desk.zoho.in/api/v1/tickets/$ticket_id";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "orgId: 60002777408",
		   "Authorization: Zoho-oauthtoken $token",
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = "{'assigneeId':'$agent_id','status':'Open'}";
		$data = '{"assigneeId":"'.$agent_id.'","status":"Open"}';


		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		return $result = json_decode($resp);
	}
	
	
	public function assign_agent(Request $request){
		$token_get = DB::table('token')->where('type', 'zoho')->first();
		$token =  $token_get->token;
		// echo $token; die;
		$result = $this->assign_agent_curl($token,$request);
		if(!empty($result->errorCode)){
			if($result->errorCode =='INVALID_OAUTH'){
				$token = $this->zoho_gettoken();
				$result = $this->assign_agent_curl($token,$request);
			}			
		}		
		if(!empty($result)){
			if(!empty($result->errorCode)){
				return response(['status' => false, 'message' => $result->message], 200); exit;
			}
			return response(['status' => true, 'message' => 'Assigned successfully'], 200); exit;
		}
		else{
			return response(['status' => false, 'message' => 'Something Went Wrong.'], 200); exit;
		}
		
	}
	
	public function save_course(Request $request){
		
			// return response(['status' => false, 'message' => $request->crm_course_name], 200); exit;
		if(!empty($request->course_type) && !empty($request->crm_course_name)){
			$course_type = $request->course_type;
			$crm_course_name = $request->crm_course_name;
			$already = DB::table('crm_courses')->where('type', $course_type)->where('name',$crm_course_name)->get();
			if(count($already) > 0){
				return response(['status' => false, 'message' => 'Course name already exits.'], 200); exit;
			}
			
			$save = DB::table('crm_courses')->insert(['type' => $course_type, 'name' => $crm_course_name]);
			
			if($save){
				return response(['status' => true, 'message' => 'Course Save Successfully'], 200); exit;
			}
			else{
				return response(['status' => false, 'message' => 'Something Went Wrong.'], 200); exit;
			}
		}
		else{
			return response(['status' => false, 'message' => 'Something Went Wrong.'], 200); exit;
		}
		
	}
	
	
	//22-11-2023
	public function getMainCategory(){	
	  $today_date = date('Y-m-d');
		$file_name = "/var/www/html/laravel/public/zoho-agent/crm-online-courses-$today_date.txt";
		
		if(!file_exists($file_name)){
			
      $curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getReportCategories',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_HTTPHEADER => array(
				'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
			  ),
			));

			$response = curl_exec($curl);
		  curl_close($curl);

		  $response=file_put_contents($file_name,$response);
		}
		
		$response=file_get_contents($file_name);
		
		return json_decode($response);
	}
	
	
	public function get_main_cat(Request $request){
    $category_id = $request->category_name;
    $key_array = array();
    $i = 0;
    $html = "";
    $html .= "<option value=''>Select Main Category</option>";
    $all_courses = $this->getMainCategory();

    $category = array();
    $all_cat = $this->getMainCategory();
    $seenValues = array(); // Temporary array to track unique values

    foreach ($all_cat->data as $val) {
        if (str_contains($val->main_category, $category_id)) {
            $currentValue = array(
                'sub_cat' => $val->sub_cat,
                'sub_cat_id' => $val->sub_cat_id
            );

            // Check if the combination of 'sub_cat' and 'sub_cat_id' is unique
            $hash = md5(json_encode($currentValue));
            if (!in_array($hash, $seenValues)) {
                $seenValues[] = $hash;
                $category[] = $currentValue;
            }
        }
    }

    // Now $category is an array of unique associative arrays with 'sub_cat' and 'sub_cat_id'

    foreach ($category as $val) {                       
        $html .= "<option value='".$val['sub_cat']."' data-id='".$val['sub_cat_id']."'>".$val['sub_cat']."</option>";            
    }

    return response(['status' => true, 'html' => $html], 200);
}

	public function get_course_name(Request $request){
		$category_name = $request->main_category_name;

		$main_category_id = $request->main_category_id;

		$key_array = array();
		$i = 0;
		$html = "";
		$html .="<option value=''>Select Course</option>";
		
		
		$all_courses = $this->getMainCategory();		
		$course 	= 	array();

		foreach($all_courses->data as $val){
			if(str_contains($val->sub_cat_id,$main_category_id) && str_contains($val->sub_cat,$category_name)){	
				//$course[] =	$val->title;
				$course[] =	$val->id.'$$'.$val->title.'$$'.$val->course_sp;
			}
		}
		
		$course=array_unique($course,SORT_STRING);
		$course=array_values($course);
		
		
		foreach($course as $val){
		  $val=explode("$$",$val);				
			$html.="<option value='".$val['1']."' data-id='".$val['0']."' data-price='".$val['2']."'>".$val['1'].'('.$val['0'].")</option>";			
		}
   

		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function get_main_cat_old(Request $request){
		$category_name = $request->category_name;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .="<option value=''>Select Main Category</option>";
		$all_courses = $this->get_all_courses();
		$all_courses = json_decode(json_encode($all_courses),true);
		/*if(count($all_courses) > 0){
			foreach($all_courses as $val){
				if($val['category name']==$category_name){
				  if(!in_array($val['main category'],$key_array)){
						$key_array[$i] = $val['main category'];
						$html .="<option value='".$val['main category']."'>".$val['main category']."</option>";
					}
				}
				$i++;
			}
		}*/
    
		$main_categories = array();
		if(count($all_courses)>0){
			foreach($all_courses as $val){
				if(str_contains($val['category name'],$category_name)){
					$mcategory=explode(",",$val['main category']);
					for($i=0;$i<count($mcategory);$i++){
					  $main_categories[]=$mcategory[$i];
					}
				}
			}
		}

		$main_categories=array_unique($main_categories,SORT_STRING);
		$main_categories=array_values($main_categories);
		for($i=0;$i<count($main_categories);$i++){
			$dd=$main_categories[$i];
      $html.="<option value='".$dd."'>".$dd."</option>";
		}

		return response(['status' => true, 'html' => $html], 200);
	}
	
	public function get_course_name_old(Request $request){
		$main_category_name = $request->main_category_name;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .="<option value=''>Select Course</option>";
		$all_courses = $this->get_all_courses();
		$all_courses = json_decode(json_encode($all_courses),true);
		/*if(count($all_courses) > 0){
			foreach($all_courses as $val){
				if($val['main category']==$main_category_name){
					if(!in_array($val['name'],$key_array)){
						$key_array[$i] = $val['name']; 
						$html .="<option value='".$val['name']."'>".$val['name']."</option>";
					}
				}
				$i++;
			}
		}*/
    
    $courses = array();
		if(count($all_courses) > 0){
			foreach($all_courses as $val){
				if(str_contains($val['main category'],$main_category_name)){
					$mcourses=explode(",",$val['name']);
					for($i=0;$i<count($mcourses);$i++){
					  $courses[]=$mcourses[$i];
					}
				}
			}
		}

		$courses=array_unique($courses,SORT_STRING);
		$courses=array_values($courses);
		for($i=0;$i<count($courses);$i++){
			$dd=$courses[$i];
      $html.="<option value='".$dd."'>".$dd."</option>";
		}
		
		return response(['status' => true, 'html' => $html], 200);
	}

	public function sendToNpf(Request $request){
		//print_r($request->mobile);die();
		$data=json_decode(json_encode($request->data),true);
		$data['field_user_activity_date']=date("Y-m-d");
		$this->meritoNpfLead($request->mobile,$data);		
		
		$this->meritoNpfLeadNew($request->mobile,$data);				
	}
	
	public function meritoNpfLeadNew($mobile,$data){
		$ndata = [];
		$ndata['activity_config_id'] = "kg2QJ3EMG0wKI7w2fJ67hn0D5aLoxrpX";
		$ndata['search_criteria'] 	= "mobile";
		$ndata['mobile'] 			= $mobile; 
		$ndata['country_dialcode'] = '+91';
		$ndata['activity_date'] = [
			"timezone" => "Asia/Kolkata",
			"date"     => date("Y-m-d\TH:i")
		];
		$ndata['dynamic_fields'] = [
			"cf_name"                       => $data['name'] ?? "",
			"cf_mobile_number"              => $mobile,
			"cf_mobile_number_dial_code"    => "+91",
			"cf_main_category"              => $data['field_main_category'] ?? "",
			"cf_sub_category"               => $data['field_sub_category'] ?? "",
			"cf_program_name"               => $data['field_program'] ?? "",
			"cf_program_code"               => $data['cf_program_code'] ?? "",
			"cf_amount_total"               => $data['cf_amount_total'] ?? "",
			"cf_amount_total_currency"      => "INR",
			"cf_payment_date_and_time"      => date("d-m-Y h:i A")
		];

		$meritto_data = json_encode($ndata, JSON_UNESCAPED_UNICODE);

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://api.nopaperforms.io/lead/v1/postDynamicActivity/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $meritto_data,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Secret-key: f472572219464e46a403628fe5ea04c2',
				'Access-key: bff0d7fffc524327827c585e07a51289'
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			echo "cURL Error: " . $err;
		} else {
			echo $response;
		}

		
		if($mobile=='8442023446'){
		$file_name = "laravel/public/zoho-agent/npf-log-2.txt";
		file_put_contents($file_name,"Request: ".json_encode($ndata)." \n \n Response: ".$response);
		}
		return 'ddd';
	}


	public function meritoNpfLead($mobile,$data){
		$data['secret_key']         = 'f472572219464e46a403628fe5ea04c2';
		$data['college_id']         = '5873';
		$data['country_dial_code'] = '+91';
		$data['source'] 							= "utkarsh classes";
					
		$data['user_id']=$this->meritoUserId($mobile);
		$data['mobile']            = $mobile;
		//$data['user_id']            = '11975';

		$meritto_data = http_build_query($data);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://services.in7.nopaperforms.com/webhooks/v1/leadCapture/5873',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $meritto_data,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		
		echo $response = curl_exec($curl);
		curl_close($curl);
		
		$file_name = "laravel/public/zoho-agent/npf-log.txt";
		file_put_contents($file_name,"Request: ".json_encode($data)." \n \n Response: ".$response);
		return 'ddd';
	}

	public function meritoUserId($mobile){
		//$user_id="11975";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://support.utkarshapp.com/index.php/getNpfData',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array("mobile"=>$mobile),
			CURLOPT_HTTPHEADER => array(
			'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0'
		  ),
		));
		
		$rsp = curl_exec($curl);
		curl_close($curl);
		$rsp=json_decode($rsp);
		$user_id=$rsp->data->meritto_id;
		return $user_id;
	}
	
	
	
	public function get_course_type(Request $request){
		$ctype_name = $request->ctype_name;
		$ctype_id = $request->ctype_id;
		$key_array = array();
		$i = 0;
		$html = "";
		$html .="<option value=''>--Select--</option>";
		
		$course = DB::table('course_type')
			->where('parent_id', $ctype_id)
			->where('status', 1)
			->pluck('title')
			->unique()
			->values();

		foreach ($course as $val) {
			$html .= "<option value='{$val}'>{$val}</option>";
		}

		return response([
			'status' => true,
			'html'   => $html
		], 200);
	}
}
