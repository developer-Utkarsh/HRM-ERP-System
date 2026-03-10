<?php 
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Input;
use DB;
class CrmCtr extends Controller{
	
	public function mcube(Request $request){
        //Authorization=>VXRrYXJzaC1EZXYtSHJtLU1jdWJl
        $rules = [
            //'emp_phone' => 'required|numeric',
            'callid' => 'required',
           // 'clicktocalldid' => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        $msg="";

        //$file_name = "laravel/public/zoho-agent/mcube.txt";
        //file_put_contents($file_name, "\n\n ".json_encode($request->all()),FILE_APPEND);
        $input=[];
        $input['emp_phone']=$request->emp_phone??'';
        $input['callid']=$request->callid??'';
        $input['clicktocalldid']=$request->clicktocalldid??'';
        $input['customer_no']=$request->callto??'';
        $input['direction']=$request->direction??'';
        $input['dialstatus']=$request->dialstatus??'';
        $input['starttime']=$request->starttime??'';
        $input['endtime']=$request->endtime??'';
        $input['disconnectedby']=$request->disconnectedby??'';
        $input['answeredtime']=$request->answeredtime??'';
        $input['groupname']=$request->groupname??'';
        $input['agentname']=$request->agentname??'';
        $input['filename']=$request->filename??'';
        $input['is_transferred']=(int)$request->is_transfered??0;
        $input['all_data']=json_encode($request->all());

        $customer_no=$input['customer_no'];
        $emp_phone=$input['emp_phone'];
    
        //$mcube=DB::table("mcube_bk_26")->where("callid",$request->callid)->first();
        $mcube="";
        if(!empty($mcube)){
            //update
            DB::table("mcube_bk_26")
            ->where("callid",$request->callid)
            ->update($input);
            $msg="Updated";
        }else{
            //insert
            DB::table("mcube_bk_26")->insert($input);
            $msg="Created";
        }
        
        $telephony_data['data']=$input;
        $telephony_data=json_encode($telephony_data);

        /*$curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://3.7.91.190:4500/send-message',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$telephony_data,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);*/

        return response(['status'=>true,"message"=>$msg],200);
    }
}
?>

