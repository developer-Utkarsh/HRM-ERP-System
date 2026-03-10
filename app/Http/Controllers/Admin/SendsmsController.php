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

class SendsmsController extends Controller
{
	
	public function sendsms_templates_add()
    {
        return view('admin.sendsms.add_template');
    }
	
	public function sendsms_templates_save(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:sms_templates',
            'description' => 'required',
        ]);

        $saveArray = array();
		$saveArray['user_id'] = 	Auth::user()->id;
		$saveArray['name'] = 	$request->name;
		$saveArray['tempid'] = 	$request->tempid;
		$saveArray['description'] = $request->description;

        $save = DB::table('sms_templates')->insert($saveArray);   

        if ($save) {
            return redirect()->route('admin.sendsms_templates')->with('success', 'Template Added Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
    }
	
	public function sendsms_templates()
    {
        $name = Input::get('name');
        $status = Input::get('status');

        $templates = DB::table('sms_templates')->where('status', '!=', 'Deleted')->orderBy('id','desc');

        if (!empty($name)){
            $templates->where('name', 'LIKE', '%' . $name . '%');
        }

        if(!empty($status)){
            if($status == 'Inactive'){
                $templates->where('status', '=', '0');
            }else{
                $templates->where('status', '=', '1');
            }
        }

        $templates = $templates->get();

        return view('admin.sendsms.templates', compact('templates'));
    }
	
	public function sendsms_template_delete($id)
    {
        $role = DB::table('sms_templates')->find($id);
		if(!empty($role)){
			DB::table('sms_templates')->where('id',$id)->update(['status' => 'Deleted']);
			
            return redirect()->back()->with('success', 'Template Deleted Successfully');
        } else {
            return redirect()->route('admin.sendsms_templates')->with('error', 'Something Went Wrong !');
        }
    }
	
    public function sendsms_textlocal()
    {
		$templates = DB::table('sms_templates')->where('status','Active')->get();
		return view('admin.sendsms.index',compact('templates'));		
    }
	
	public function sendsms_textlocal_save(Request $request)
    {   
		$validatedData = $request->validate([
			'template' => 'required',
			'import_file' => 'required',
		]);
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}
		
		$template = DB::table('sms_templates')->where('id',$request->template)->first();
        $description = $template->description;
        $tempid = $template->tempid;
        $txttype = $template->txttype;
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
        $stArr = $import[0][0];
        // unset($import[0][0]);
        $result = [];
		$errors_row = "";
		// echo "<pre>"; print_R($import[0]); die;
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) { 
				if($key===0){
					continue;
				}
				$mobile = $value[0];
				$numbers ="91".$mobile;
				
				$VAR1 = "";
				$VAR2 = "";
				$VAR3 = "";
				$VAR4 = "";
				$VAR5 = "";
				$VAR6 = "";
				$VAR7 = "";
				$VAR8 = "";
				$VAR9 = "";
				$VAR10 = "";
				if(!empty($value[1])){
					$VAR1 = $value[1];
				}
				if(!empty($value[2])){
					$VAR2 = $value[2];
				}
				if(!empty($value[3])){
					$VAR3 = $value[3];
				}
				if(!empty($value[4])){
					$VAR4 = $value[4];
				}
				if(!empty($value[5])){
					$VAR5 = $value[5];
				}
				if(!empty($value[6])){
					$VAR6 = $value[6];
				}
				if(!empty($value[7])){
					$VAR7 = $value[7];
				}
				if(!empty($value[8])){
					$VAR8 = $value[8];
				}
				if(!empty($value[9])){
					$VAR9 = $value[9];
				}
				if(!empty($value[10])){
					$VAR10 = $value[10];
				}
				
				$find = ['##VAR1##','##VAR2##','##VAR3##','##VAR4##','##VAR5##','##VAR6##','##VAR7##','##VAR8##','##VAR9##','##VAR10##'];
				$replace = compact('VAR1', 'VAR2', 'VAR3', 'VAR4', 'VAR5', 'VAR6', 'VAR7', 'VAR8', 'VAR9', 'VAR10');

				$message = str_replace($find, $replace, $description);
				
				$message = rawurlencode($message);
				
				// Prepare data for POST request
				
				
				$curl = curl_init();
				  curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://app.pingbix.com/SMSApi/send",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => "userid=utkarsh&password=BNeWcaP6&mobile=$numbers&msg=$message&senderid=UTKRSH&msgType=$txttype&dltEntityId=1701158072985103391&dltTemplateId=$tempid&duplicatecheck=true&output=json&sendMethod=quick",
				  CURLOPT_HTTPHEADER => array(
					"apikey: somerandomuniquekey",
					"cache-control: no-cache",
					"content-type: application/x-www-form-urlencoded"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
				
				// Send the POST request with cURL
				/* 
				$apiKey = urlencode('NGI1OTY5NDI2YzRjNmU1OTRhNDQ1NTVhMzYzMDU0NmI=');		
				$sender = urlencode('UTKRSH');
				$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
				
				$ch = curl_init('https://api.textlocal.in/send/');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($ch);
				curl_close($ch); */
				
				// Process your response here
				// echo $response; die;
				
				
			}
			
			return back()->with('success', 'Message Sent successfully.');  
		}
		else{
			return redirect()->route('admin.invoice.create')->with('error', "Something went wrong !");
		}
		
		return redirect()->route('admin.invoice.create')->with('error', "Something went wrong !");
            
    }
}
