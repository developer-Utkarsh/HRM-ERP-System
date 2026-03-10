<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use DB; 
use DateTime;

class CronFacultyCtr extends Controller{

    public function topicSelectionReminder(Request $request){
		
	  	$record=[];
	  	$cdate=date("Y-m-d");
	  	$list = DB::table('timetables as t')
		    ->leftJoin('users as u', 'u.id', '=', 't.faculty_id')
		    ->leftJoin('batch as b', 'b.id', '=', 't.batch_id')
		    ->leftJoin('timetable_topic as tt', 'tt.timetable_id', '=', 't.id')
		    ->selectraw('t.id,b.name as batch_name,t.faculty_id,u.name,u.mobile,u.send_link,t.cdate,t.from_time,t.to_time,tt.topic_id')
		    ->where('t.time_table_parent_id', 0)
		    ->where('t.is_deleted', '0')->where('t.is_publish', '1')->where('t.is_cancel', 0)
		    ->where('b.course_planer_enable', 1)
		    ->where('t.cdate', $cdate)
		    ->whereNull('tt.topic_id')
		    ->groupBy('t.id');

		//$list->whereIN('t.faculty_id',[8158,8121,8198,6629,6693]);
		if(!empty($request->before_class)){			
			$temp_id="944720697572588";
			
			$gupsuptemp_id="5b6398aa-4211-48f1-a065-f4e339003843";
			
			
			$from_time=date("H:i",strtotime("+50 minutes"));
			$to_time=date("H:i",strtotime("+65 minutes"));
		  	
		    $list->whereRaw("t.from_time>='$from_time'");
		    $list->whereRaw("t.from_time<='$to_time'");
		    //$record=$list->limit(20)->get();
		    $record=$list->get();
		}else if(!empty($request->after_class)){
			$temp_id="923253053100563";
			
			$gupsuptemp_id="5478bbef-5f03-4880-b5d3-ac3ef35f4328";
			
	        $from_time=date("H:i",strtotime("-50 minutes")); //14:10
			$to_time=date("H:i",strtotime("-65 minutes"));  //13:55
		  	
		    $list->whereRaw("t.to_time<='$from_time'");
		    $list->whereRaw("t.to_time>='$to_time'");
		    //$record=$list->limit(20)->get();
		    $record=$list->get();
		}
		
	  	// print_r($record);
	  	foreach($record as $val) {
	  	    $class_time=$val->from_time."-".$val->to_time;
			//$mobile="7014155376";$mobile="8952067589";
			$mobile=$val->mobile;
	  		$var='"variable1": "'.$val->cdate.' - '.$class_time.'","variable2": "'.$val->batch_name.'","variable3": "'.$val->send_link.'"';
	  		//$var='"variable1": "'.$val->cdate.'","variable2": "'.$val->batch_name.'","variable3": "'.$val->send_link.'"';
	  		$this->whatsapp_msg($mobile,$temp_id,$var);
	  		//break;
			
			
			//Gupsup Message
			$varible_1 = $val->cdate.' - '.$class_time;
			$varible_2 = $val->batch_name;
			$varible_3 = $val->send_link;
			
			
			
			$params = [];
			$params['input']   		 = [$varible_1,$varible_2,$varible_3];
			$params['template_id']   = $temp_id;
			$params['mobile']   	 = $mobile;
			
			$this->gupsup_msg($params);
	  	}

		$temp_id="923253053100563";
	  	$mobile="7014155376";
	  	$var='"variable1": "2024-01-01","variable2": "Test","variable3": "34567"';
	  	//$this->whatsapp_msg($mobile,$temp_id,$var);

	  	/*
	  	    SELECT t.id,t.faculty_id,u.name,u.mobile,t.from_time,tt.topic_id
			FROM `timetables` as t
			Left Join users as u ON u.id=t.faculty_id
			Left Join timetable_topic as tt ON tt.timetable_id=t.id
			WHERE t.cdate= '2024-12-11' AND t.is_deleted= '0' AND t.is_publish= '1' AND t.is_cancel = 0 
			AND STR_TO_DATE(t.from_time, '%H:%i') >= '11:00'  AND STR_TO_DATE(t.from_time, '%H:%i') <= '11:15'
			AND tt.topic_id is null and t.time_table_parent_id=0  group by t.id
		*/
    }

  function whatsapp_msg($mobile,$temp_id,$var){
		$url = "https://api.imiconnect.in/resources/v1/messaging";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
		   "Content-Type: application/json",
		   "key: 0b08bf38-6dd9-11ea-9da9-025282c394f2",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = '{
		    "appid": "a_158521380743240260",
		    "deliverychannel": "whatsapp",
		    "message": {
		        "template": "'.$temp_id.'",
		        "parameters": { '.$var.'}
		    },
		    "destination": [{
		            "waid": ["91'.$mobile.'"]
		        }
		    ]}';
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		echo $resp = curl_exec($curl);
		curl_close($curl);
		//var_dump($resp);
		return "ok";
	}
}

