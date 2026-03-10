<?php 
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Input;
use DB;
class ErpContentCtr extends Controller{
	
	public function subject(Request $request){
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required',
            'status' => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        return response(['status'=>true,"message"=>'Updated'],200);

        $erp_subject_id=$request->id;
        
        $msg="";
        $input['name']=$request->name;
        $input['erp_subject_id']=$request->id;
        $input['status']=$request->status??1;
    
        $subject=DB::table("subject")->where("erp_subject_id",$erp_subject_id)->first();
        if(!empty($subject)){
            //update
            DB::table("subject")
            ->where("erp_subject_id",$erp_subject_id)
            ->where("id",$subject->id)
            ->update($input);

            $msg="Updated";
        }else{
            $input['user_id']=9999;
            //insert
            DB::table("subject")->insert($input);

            $msg="Created";
        }

        return response(['status'=>true,"message"=>$msg],200);
    }

    public function chapter_push_erp(Request $request){
        if(empty($request->chapter_id)){
         $chapter=DB::table("chapter")
         ->select('chapter.*','subject.erp_subject_id')
         ->leftjoin('subject','subject.id','chapter.subject_id')
         ->where("erp_chapter_id",0)->first();
        }else{
         $chapter=DB::table("chapter")
         ->select('chapter.*','subject.erp_subject_id')
         ->where("id",$request->chapter_id)
         ->leftjoin('subject','subject.id','chapter.subject_id')->first();
        }

        if(!empty($chapter)){
            $data="subject_id=".$chapter->erp_subject_id."&topic=".$chapter->name;
            $rsp=$this->utkarshAppApi('AddTopic',$data,'POST');
            if(!empty($rsp) && $rsp->status){
                $erp_chapter_id=$rsp->data[0]->id;
                //update
                DB::table("chapter")
                ->where("id",$chapter->id)
                ->update(["erp_chapter_id"=>$erp_chapter_id]);
                echo $msg="Updated";
            }
        }
    }

    public function timetable_topic_check(Request $request){

        if(empty($request->is_staging)){
           return response(['status'=>true,"message"=>''],200);
        }

        $rules = [
            'timetable_id'   => 'required|numeric|min:1',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        //$tt=DB::table("timetables")->where("id",$request->timetable_id);
        $timetable_id=$request->timetable_id;
        $cdate=date("Y-m-d");
        $record=DB::select("Select t.id,t.time_table_parent_id,t.batch_id,b.name as batch_name,b.course_planer_enable from timetables as t
                    Left Join batch as b ON b.id=t.batch_id
                    Where (t.id=$timetable_id OR time_table_parent_id=$timetable_id) AND cdate='$cdate'"
                );

        $error="";
        $batch_wise_status=[];
        if(!empty($record)){
            foreach($record as $val) {
                if($val->course_planer_enable){
                    $timetable_ids=$val->id;
                    if($val->time_table_parent_id){
                        $timetable_ids.=",".$val->time_table_parent_id;
                    }

                    $check=DB::select("Select id from timetable_topic Where timetable_id IN ($timetable_ids) AND batch_id=$val->batch_id limit 1");
                    if(empty($check)){
                       //$error.=" \n Topic Not seleted for ".$timetable_ids.'-'.$val->batch_name;
                       $error .= "\n Topic has not been selected for " . $val->batch_name . " (" . $timetable_ids . ") ";
                       
                       $batch_wise_status[]=[
                            "name"=>$val->batch_name,
                            "status"=>"No",
                       ];

                    }else{
                        $batch_wise_status[]=[
                            "name"=>$val->batch_name,
                            "status"=>"Yes",
                       ];
                    }
                }else{
                    $batch_wise_status[]=[
                        "name"=>$val->batch_name,
                        "status"=>"Without Planner",
                   ];
                }
            }
        }else{
            //$error="Wrong Timetable Id Mapped..Frist Update timetable id in class schedule";
            $error = "Wrong Timetable ID has been mapped by the DEO team. Please connect with Vishal Dourwal.";
        }
        
        if($error){
            return response(['status'=>false,"message"=>$error,'batch_wise_status'=>$batch_wise_status],200);
        }else{
            return response(['status'=>true,"message"=>$error],200);
        }
    }

    public function timetable_course_sync(Request $request){
        $rules = [
            'timetable_id'   => 'required|numeric|min:1',
            'erp_course_id'  => 'required|numeric',
            'erp_subject_id' => 'required|numeric',
            'erp_topic_id'   => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        $erp_json=[
            "erp_subject_name"=>$request->erp_subject_name,
            "erp_topic_name"=>$request->erp_topic_name,
            "erp_course_names"=>$request->erp_course_names,
            "erp_course_id"=>$request->erp_course_id,
            "erp_subject_id"=>$request->erp_subject_id,
            "erp_topic_id"=>$request->erp_topic_id
        ];

        $erp_json=json_encode($erp_json);

        file_put_contents("/var/www/html/laravel/public/ttsync.txt", $erp_json,FILE_APPEND);

        $tt=DB::table("timetables")->where("id",$request->timetable_id)
        ->update(["erp_json" =>$erp_json]);
        $msg="Updated";

        return response(['status'=>true,"message"=>$msg],200);
    }

    public function discountApprovel(Request $request){
        $rules = [
            'category'    => 'required',
            'approver_id'    => 'required|numeric',
            'type'       => 'required',
            'agent_name' => 'required',
            'student_mobile'   => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        $input=[
            "user_id"=>$request->approver_id,
            "type"=>$request->type,
            "agent_name"=>$request->agent_name,
            "student_mobile"=>$request->student_mobile,
            'coupon_id'=>$request->coupon_id??'0',
            "remark"=>$request->remark??'',
            "proof_doc"=>$request->proof_doc??'',
            "doc_url"=>$request->doc_url??'',
            "batch"=>$request->batch??'',
            "details"=>$request->details??'',
            "discount_amount"=>$request->discount_amount??'0',
            "discount_percentage"=>$request->discount_percentage??'0',
            "category"=>$request->category
        ];
		
		if(Input::hasfile('proof_doc')){
            $input['proof_doc'] = $this->uploadFilePdf(Input::file('proof_doc')); 
        }

        $tt=DB::table("discount_approvel")->insert($input);

        return response(['status'=>true,"message"=>'Request for Approvel Sent.'],200); 
    }

    public function approvelList(Request $request){
		if(!empty($_GET['type']) && $_GET['type']=="online"){
			$category = $_GET['category'];
			$list=DB::table("discount_role_category_wise as drcw")->selectraw('users.id,users.name,drcw.online as online_discount,drcw.offline as offline_discount')
						->leftjoin('users','users.role_id','drcw.role_id')
						->where('drcw.category',$category)
						->where('users.status',1)
						->whereRAW("(drcw.online>0)")
						->get();
		}
		else if(!empty($_GET['type']) && $_GET['type']=="offline"){
			$category = $_GET['category'];
			$list=DB::table("discount_role_category_wise as drcw")->selectraw('users.id,users.name,drcw.online as online_discount,drcw.offline as offline_discount')
						->leftjoin('users','users.role_id','drcw.role_id')
						->where('drcw.category',$category)
						->where('users.status',1)
						->whereRAW("(drcw.offline>0)")
						->get();
			//$list=DB::table("users")->selectraw('id,name,offline_discount,online_discount')->where('status',1)->whereRAW("(offline_discount>0 OR online_discount>0)")->get();
		}
		else{
			$list = array();
			$list=DB::table("users")->selectraw('id,name,offline_discount,online_discount')->where('status',1)->whereRAW("(offline_discount>0 OR online_discount>0)")->get();
		}
		
		return response(['status'=>true,"message"=>'List','data'=>$list],200);
    }

    public function approvelCheck(Request $request){
        $rules = [
            'approver_id'    => 'required|numeric',
            'type'           => 'required',
            'student_mobile' => 'required|numeric',
            'batch' => 'required',
        ];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $data['status']=false;
            $data['message']=$validator->getMessageBag()->first();
            return response($data,200);
        }

        $check=DB::table("discount_approvel")
          ->selectraw('id,status,remark,discount_amount,discount_percentage')
          ->where('student_mobile',$request->student_mobile)
          ->where('user_id',$request->approver_id)
          ->where('type',$request->type)
          ->where('batch',$request->batch)
          ->orderby('id','desc')->first();

        if(empty($check)){
           return response(['status'=>false,"message"=>'No Request Found'],200); 
        }else if($check->status==0){
           return response(['status'=>false,"message"=>'Approvel is Pending.'],200);
        }else if($check->status==1){
          return response(['status'=>true,"message"=>'Approved','data'=>$check],200);
        }else if($check->status==2){
          return response(['status'=>false,"message"=>'Approvel is Rejected.'],200);
        }

        return response(['status'=>false,"message"=>'Something Went Wrong'],200);
    }
	
	public function uploadFilePdf($file){
		$drive = public_path(DIRECTORY_SEPARATOR . 'discount_approvel' . DIRECTORY_SEPARATOR);
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;    
		$file->move($drive, $filename);
		return $filename;
	}
}
?>

