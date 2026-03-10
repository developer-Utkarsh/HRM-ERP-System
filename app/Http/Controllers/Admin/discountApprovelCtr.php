<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Input;
use DB;
use Auth;

class discountApprovelCtr extends Controller
{
    
    public function index()
    {  
		$auth=Auth::user();
		$query  = DB::table('discount_approvel as da')
				->select('da.*','users.name as reference_name')
				->join('users', 'users.id', '=', 'da.user_id');
        
        if($auth->id!=901){
			$query->where('da.user_id',$auth->id);
        }	

		//$query->whereIN('da.student_mobile',['7597765508','8949930828','9166366879','6350158285','8824435152','9636401176','7665397956','7987147806','8766661523','8107342946']);


        $query->orderby('da.id','desc');	
		$record  = $query->get();
        return view('admin.discountApprovel.index',compact('record'));
    }

    public function store(Request $request){
        $auth=Auth::user();
        $status=$request->status??0;
        $record_id=$request->record_id;
        $type=$request->type;

        $updateRecord=[];
        $updateRecord['status']=$status;
        if(Input::hasfile('doc_url')){
            $updateRecord['doc_url'] = $this->uploadFilePdf(Input::file('doc_url'));
        }


		if($status == 1 && $type == 'Online'){
			$query_data  = DB::table('discount_approvel')->where('id',$record_id)->first();
			if(!empty($query_data)){
				$details = json_decode($query_data->details,true);
				
				$post_array = array(
						'coupon_id' => $query_data->coupon_id,
						'user_id' => $details['student_id'],
						'remark' => $query_data->remark,
						'added_by' => $details['added_by']
					);

				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://support.utkarshapp.com/auth_panel/hook/user_token_approval',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => $post_array,
				  CURLOPT_HTTPHEADER => array(
					'Cookie: ci_session=g32lkn503t79079cicnknf7pblu9sltf'
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				if(!empty($response)){
					$response = json_decode($response,true);
					if($response['status']){
						DB::table('discount_approvel')->where('id',$record_id)->update($updateRecord);
						return back()->with('success', 'Record Updated Successfully!');
					}
					else{
						return back()->with('error', $response['message']);
					}
				}
			}
		}else{
			DB::table('discount_approvel')->where('id',$record_id)->update($updateRecord);
			return back()->with('success', 'Record Updated Successfully!');
		}
        
    }

    public function uploadFilePdf($file){
		$drive = public_path(DIRECTORY_SEPARATOR . 'discount_approvel' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$file->move($drive, $filename);
		return $filename;
	}
}
