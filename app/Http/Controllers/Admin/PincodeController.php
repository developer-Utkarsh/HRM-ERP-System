<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Input;
use DB;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PincodeExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Import this class

class PincodeController extends Controller
{
    public function index()
    {  
		$params = array();
		$res = parse_url($_SERVER['REQUEST_URI']);
		if(!empty($res['query'])){
			parse_str($res['query'], $params);
		}
		$page       	 = Input::get('page');
		

		$pincode  = DB::table('pincode_shiprocket')->orderby('id','desc');
		if(!empty(Input::get('pincode'))){
           $pincode->where("pincode",Input::get('pincode'));
		}
		

		$pincode = $pincode->paginate(50);
		$pageNumber = 1;
		if(isset($page)){
			$page = Input::get('page');
			$pageNumber = (100*($page-1));
			
			$pageNumber = $pageNumber +1;
		} 

        return view('admin.pincode.index', compact('pincode','pageNumber','params'));
    }
	
	
	public function import_pincode(Request $request){
		$logged_id       = Auth::user()->id;
		$department_type = Auth::user()->department_type;
		$name       	 = Auth::user()->name;
		$register_id   	 = Auth::user()->register_id ;
		$role       	 = Auth::user()->role_id;
				
				
		$mrl_no			=	$request->mrl_no;
		
			
		$file = $request->file('product_file');
		$exten = $file->getClientOriginalExtension();
		if (strtolower($exten) != 'csv') {
			$validator = Validator::make($request->all(), [
				'product_file' => 'required|mimes:xlsx,xls,csv',
			]);

			if ($validator->fails()) {
				return response(['status' => false, 'message' => $validator->errors()->first('product_file')], 200);
			}
		}


		$import = Excel::toArray(null, $file);
		
		$header = $import[0][0]; 
		unset($import[0][0]);

		$get_data = [];
		$get_data[] = $header; 

		foreach ($import[0] as $row) {
			$pincode = trim($row[0]);
			$exists = DB::table('pincode_shiprocket')->where('pincode', $pincode)->exists();

			if ($exists) {
				$get_data[] = $row;
			}
		}
		
		if (count($get_data) === 1) {
			return response(['status' => false, 'message' => 'No matching pincodes found in the database.'], 200);
		}

		return Excel::download(new PincodeExport($get_data), 'PincodeExport.xlsx',\Maatwebsite\Excel\Excel::XLSX); 
	}
}
