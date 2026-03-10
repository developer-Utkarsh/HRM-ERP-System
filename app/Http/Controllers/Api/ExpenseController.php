<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Expense;
use Input;
use DB;

class ExpenseController extends Controller
{   
    public function getExpenseCategory(Request $request)
    {
    	try {
			$allCategory = DB::table('expense_category')->where('status', 'Active')->where('is_deleted', '0')->get();
			
			$responseArray = array();
			if(count($allCategory) > 0){
				foreach($allCategory as $key=>$val){
					$responseArray[$key]['id'] = $val->id;
					$responseArray[$key]['name'] = $val->name;
				}
				
				$data['category'] = $responseArray;
				return $this->returnResponse(200, true, "Category Details", $data);
			}
			else{
				return $this->returnResponse(200, false, "Category Not Found"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function addExpense(Request $request){ 
		try {
			
			if(!empty($request->title) && !empty($request->cat_id) && !empty($request->amount) && !empty($request->remark) && !empty($request->file_name) && !empty($request->user_id)){
				
				$inputs = $request->only('user_id','title','cat_id','amount','remark');
				$inputs['edate'] = $request->edate;

				$encoded_string=$_POST['file_name'];
				$decoded_file = base64_decode($encoded_string); 
				//$target_dir="expense/";
				$target_dir=public_path(DIRECTORY_SEPARATOR . 'expense' . DIRECTORY_SEPARATOR);
				$mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE);
				$extension = $this->mime2ext($mime_type);
				$fileName = uniqid() .'.'. $extension; 
				$file_dir = $target_dir . $fileName;
				file_put_contents($file_dir, $decoded_file);
				$file_result='laravel/public/expense/'.$fileName;				
				$inputs['file_name'] = $file_result;
				
				$expenseResult = Expense::create($inputs);
				if($expenseResult){
					return $this->returnResponse(200, true, "Expense Successfully Added");
				}
				else{
					return $this->returnResponse(200, false, "Something is wrong"); 
				}
				
			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function editExpense(Request $request){ 
		try {
			
			if(!empty($request->id) && !empty($request->title) && !empty($request->cat_id) && !empty($request->amount) && !empty($request->remark)){
				
				$expense_result = Expense::where('id',$request->id)->first();
				
				$inputs = $request->only('user_id','title','cat_id','amount','remark');
				$inputs['edate'] = $request->edate;
				
				
				if ($_POST['file_name']){ 
					$encoded_string=$_POST['file_name'];
					$decoded_file = base64_decode($encoded_string); 
					$target_dir=public_path(DIRECTORY_SEPARATOR . 'expense' . DIRECTORY_SEPARATOR);
					$mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE);
					$extension = $this->mime2ext($mime_type);
					$fileName = uniqid() .'.'. $extension; 
					$file_dir = $target_dir . $fileName;

					if(!empty($request->id)){
						$check_file = DB::table('expense')->where('id', $request->id)->first();
						if(!empty($check_file->file_name)){   
							unlink($target_dir.str_replace('laravel/public/expense','',$check_file->file_name));
						}
					}
					
					file_put_contents($file_dir, $decoded_file);
					$file_result='laravel/public/expense/'.$fileName;
					
					$inputs['file_name'] = $file_result;
				}
				else{ 
					$inputs['file_name'] = $expense_result->file_name;
				} 
				
				$expenseResult = $expense_result->update($inputs);
				if($expenseResult){
					return $this->returnResponse(200, true, "Expense Successfully Update");
				}
				else{
					return $this->returnResponse(200, false, "Something is wrong"); 
				}
				
			}
			else{
				return $this->returnResponse(200, false, "All Fields Are Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function deleteExpense(Request $request){ 
		try {
			
			if(!empty($request->id)){
				
				$expenseResult = Expense::find($request->id);
				$inputs = array('is_deleted' => '1');

				if($expenseResult->update($inputs)){
					return $this->returnResponse(200, true, "Expense Successfully Delete");
				}
				else{
					return $this->returnResponse(200, false, "Something is wrong"); 
				}
				
			}
			else{
				return $this->returnResponse(200, false, "ID Required"); 
			}

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function expenseList(Request $request){ 
		try {
			if(!empty($request->user_id)){
				$profile_path = asset('');
				$whereCond = ' 1=1 ';
				if(!empty($request->cat_id)){
					$whereCond .= ' AND expense.cat_id = "'.$request->cat_id.'"';
				}
				$whereCond .= ' AND expense.user_id = "'.$request->user_id.'"';

				$expenseRes = Expense::select('expense.title','expense.cat_id','expense.amount','expense.edate','expense.remark','expense.file_name',DB::raw("CONCAT('$profile_path', expense.file_name) as file_name, SUBSTRING_INDEX(CONCAT('$profile_path', expense.file_name), '.', -1) as type"),'expense.status','expense_category.name as category_name','users.name as user_name')->leftJoin('expense_category', 'expense.cat_id', '=', 'expense_category.id')->leftJoin('users', 'users.id', '=', 'expense.user_id')->where('expense.is_deleted','0')->whereRaw($whereCond)->get();
				
				if(count($expenseRes) > 0){
					$data['expense'] = $expenseRes;
					return $this->returnResponse(200, true, "Expense Details", $data);
				}
				else{
					return $this->returnResponse(200, false, "Expense Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "User ID Required"); 
			}
			
    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
	}

	public function uploadFile($file, $id){
		$drive = public_path(DIRECTORY_SEPARATOR . 'expense' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$newImage = $drive . $filename;
		
		if(!empty($id)){
			$check_file = DB::table('expense')->where('id', $id)->first();
			if(!empty($check_file->file_name)){  
				unlink($drive.str_replace('laravel/public/expense','',$check_file->file_name));
			}
		}
		
		$imgResource = $file->move($drive, $filename);
		return 'laravel/public/expense/'.$filename;
	}
	
	public function mime2ext($mime){
		$all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
		"image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
		"image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
		"application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
		"image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
		"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
		"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
		"video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
		"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
		"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
		"application\/x-jar"],"zip":["application\/x-zip","application\/zip",
		"application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
		"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
		"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
		"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
		"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
		"pdf":["application\/pdf","application\/octet-stream"],
		"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
		"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
		"application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
		"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
		"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
		"application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
		"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
		"video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
		"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
		"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
		"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
		"image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
		"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
		"application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
		"application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
		"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
		"application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
		"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
		"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
		"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
		"application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
		"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
		"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
		"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
		"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
		"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
		"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
		"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
		$all_mimes = json_decode($all_mimes,true);
		foreach ($all_mimes as $key => $value) {
			if(array_search($mime,$value) !== false) return $key;
		}
		return false;
	}
}
