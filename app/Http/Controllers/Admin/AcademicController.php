<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Buyer;
use Input;
use Validator;
use DB;
use Auth;
use DateTime;
use Excel;
use App\Exports\DueStudentExport;

use Illuminate\Support\Facades\Cache;

class AcademicController extends Controller
{
    
    public function index(Request $request)
    {   
		$is_cxo  	 		= 	Auth::user()->is_cxo;
		$erp_category  		= 	Auth::user()->course_category;
		$designation  		=	Auth::user()->user_details->degination;
		$location 			= 	Auth::user()->user_branches[0]->branch['branch_location'];
        
        $cache_key="academic_dashboard_";
        if($is_cxo){
		   $cache_key.='cxo';
		}else if($designation=="CITY HEAD"){
		   $cache_key.="city_head_".$location;
		}else if($designation=="CATEGORY HEAD"){
		   $cache_cat_head_id=Auth::user()->id;
		   $cache_key.="category_head_".$cache_cat_head_id;
		}

		//$where = "1=1 AND rg.s_type='Approved' AND rg.refund_status!='YES' ";
		$where = "1=1 AND rg.s_type='Approved' AND rg.std_type='Registration' ";
		$where = "1=1 AND rg.s_type='Approved' AND rg.std_type='Registration' AND rg.refund_status!='YES' ";

		if(!empty($request->f_year)){
			$f_year=$request->f_year;
			$f_year_arr=explode("&",$request->f_year);
			$where.=" AND rg.reg_date>='".$f_year_arr[0]."' AND rg.reg_date<='".$f_year_arr[1]."'";
		}else{
			$f_year='2024-04-01&2025-03-31';
			$f_year_arr=explode("&",$f_year);
			$where.=" AND rg.reg_date>='".$f_year_arr[0]."' AND rg.reg_date<='".$f_year_arr[1]."'";
		}
		
		if($designation=="CATEGORY HEAD"){
			$erp_category = explode(',',$erp_category);
			$course_category = "'".implode("','",$erp_category)."'";
			$where	.= " AND tb.category IN (".$course_category.")";
		}
		
		if($designation=="CITY HEAD"){
			$where	.= " AND tb.branch = '".$location."'";
		}

		if(!empty($request->category)){
			$where	.= " AND tb.category='".$request->category."'";
		}

		$cache_key.=urlencode($where);

		//Cache::forget($cache_key);
		$cache_data=Cache::get($cache_key);



		if(empty($cache_data)){
			$today=date('Y-m-d');

			$data = DB::connection('mysql2')->select("SELECT 
				tb.branch,
				count(reg_id) as enrolled,
				sum(rg.total_fee) as fee_booked,
				(sum(rg.total_fee)-sum(rg.due_amount)) as fee_collected,
				sum(rg.due_amount) as due_amount,
				sum(if(due_days!=0,1,0)) as total_due_student,
				sum(if((due_amount>0 && due_days<0),rg.due_amount,0)) as overdue_due_amount,
				sum(if((due_amount>0 && due_days<0),1,0)) as overdue_due_student
				FROM tbl_batch as tb
				Left Join tbl_registration as rg
				ON rg.batch_id=tb.Bat_id
				where tb.branch IN ('Jodhpur','Jaipur','Prayagraj','Indore') AND $where group by branch");

			$groupby_column="tb.category";
			if($is_cxo==1){
	          $groupby_column="tb.category_maper";
			}else if($designation=="CITY HEAD"){
	          $groupby_column="tb.category";
	          $groupby_column="tb.category_maper";
			}else if($designation=="CATEGORY HEAD"){
	          $groupby_column="tb.branch";
			}

			
					
			
			$data2 = DB::connection('mysql2')->select("SELECT 
				tb.branch,$groupby_column as category,count($groupby_column) as rowcount,
				sum(if((due_days>-10 && due_days<=0),1,0)) as slab_1_count,
				sum(if((due_days>-10 && due_days<=0),due_amount,0)) as slab_1_amount,
				sum(if((due_days>-30 && due_days<=-10),1,0)) as slab_2_count,
				sum(if((due_days>-30 && due_days<=-10),due_amount,0)) as slab_2_amount,
				sum(if((due_days>-60 && due_days<=-30),1,0)) as slab_3_count,
				sum(if((due_days>-60 && due_days<=-30),due_amount,0)) as slab_3_amount,
				sum(if(due_days<=-60,1,0)) as slab_4_count,
				sum(if(due_days<=-60,due_amount,0)) as slab_4_amount
				FROM tbl_batch as tb
				Left Join tbl_registration as rg
				ON rg.batch_id=tb.Bat_id
				where rg.duedate!='' AND tb.branch IN ('Jodhpur','Jaipur','Prayagraj','Indore') AND $where
				group by tb.branch,$groupby_column");
				
		
			$data3 = DB::connection('mysql2')->select("SELECT 
				tb.branch,$groupby_column as category,count($groupby_column) as rowcount,
				sum(if((due_days>0 && due_days<=3),1,0)) as slab_1_count,
				sum(if((due_days>0 && due_days<=3),due_amount,0)) as slab_1_amount,
				sum(if((due_days>3 && due_days<=7),1,0)) as slab_2_count,
				sum(if((due_days>3 && due_days<=7),due_amount,0)) as slab_2_amount,
				sum(if((due_days>7 && due_days<=15),1,0)) as slab_3_count,
				sum(if((due_days>7 && due_days<=15),due_amount,0)) as slab_3_amount,
				sum(if((due_days>15 && due_days<=30),1,0)) as slab_4_count,
				sum(if((due_days>15 && due_days<=30),due_amount,0)) as slab_4_amount
				FROM tbl_batch as tb
				Left Join tbl_registration as rg
				ON rg.batch_id=tb.Bat_id
				where rg.duedate!='' AND tb.branch IN ('Jodhpur','Jaipur','Prayagraj','Indore') AND $where
				group by tb.branch,$groupby_column");
					
					
			$course = DB::connection('mysql2')->table('tbl_course')->orderby('course_name')->get();

			//put to cache
			$cache_data=Cache::remember($cache_key,3600*12,function()use($data,$data2,$data3,$course,$erp_category){
				$cache_data=["data"=>$data,"data2"=>$data2,"data3"=>$data3,'course'=>$course,'erp_category'=>$erp_category];
                return $cache_data;
			});
		}

		if(!empty($cache_data) && count($cache_data)){
			$data =$cache_data['data'];
			$data2=$cache_data['data2'];
			$data3=$cache_data['data3'];
			$course=$cache_data['course'];
			$erp_category=$cache_data['erp_category'];
		}
		
		
        return view('admin.academic.index',compact('f_year','is_cxo','designation','data','data2','data3','erp_category','course'));
    }
	
	
	public function get_coursewise_batch(Request $request){
		$id  = $request->course_id; 
		$respnse = DB::connection('mysql2')->table('tbl_batch')->where('course_id', $id)->get();
		if (!empty($respnse))
        {
            $res = '<option value="">-- Select --</option>';			
			foreach($respnse as $key){ 
				$res .= "<option value='".$key->Bat_id."'>".$key->batch_name."</option>";
            }			
			echo $res;
			exit();
        }else{
            echo $res = "<label>Error</label>";
            die();
        }
	}
	
	public function attendance(Request $request){
		$designation  		=	Auth::user()->user_details->degination;
		$location 			= 	Auth::user()->user_branches[0]->branch['branch_location'];

		//$where = "1=1 AND rg.s_type='Approved' AND rg.refund_status!='YES' ";
		$where = "1=1 ";

		$where.= " AND tb.branch='".$location."' ";
		if(!empty($request->course_id)){
			$where	.= " AND tb.course_id='".$request->course_id."'";
		}
		
		if(!empty($request->batch_id)){
			$where	.= " AND tb.Bat_id='".$request->batch_id."'";
		}
		
		if(!empty($request->fdate) && !empty($request->tdate)){
			$fdate=$request->fdate;
			$tdate=$request->tdate;

			$diff=abs(strtotime($tdate) - strtotime($fdate));
			$diff=$diff/86400;
			if($diff>6){
               return back()->with('error','Select date range withing 6 days only');   
			}

            //return back()->with('error','Try After some time');

			$begin = new DateTime($request->tdate);
			$end   = new DateTime($request->fdate);
		}else{
			$fdate=date("Y-m-d",strtotime(date("Y-m-d")." -6 day"));
			$tdate=date("Y-m-d");


			$begin = new DateTime($tdate);
			$end   = new DateTime($fdate);
		}
        
        $batch_code="";
		if($designation=="CENTER HEAD"){
            $branch_id 			= 	Auth::user()->user_branches[0]->branch_id;
			$timetable=DB::table('timetables')->selectRAW("batch.batch_code")
			->leftJoin('batch','batch.id','timetables.batch_id')
			->where('timetables.branch_id',$branch_id)
			->where('cdate','>=',$fdate)
			->where('cdate','<=',$tdate)
			->groupby('batch_id')->get();
			$batch_code='-1';
			foreach($timetable as $val){
				if(!empty($val->batch_code)){
                  $batch_code.=",".$val->batch_code;
				}
			}
		    
		    //	$batch_code=$timetable->batch_code??'';
		    // $batch_code=$batch_code==NULL?'-1':$batch_code;
			$where	.= " AND tb.Bat_id IN (".$batch_code.")";
			$batch_code= " AND tb.Bat_id IN (".$batch_code.")";
		}
        
        $cache_key="academic_attendance_cityhead_";
		$cache_key.=urlencode($location.$where.$fdate.$tdate);
		//$cache_data=Cache::forget($cache_key);
		$cache_data=Cache::get($cache_key);

		
		if(empty($cache_data)){

			$record=[];
			$batch_ids="0";
			for($i=$begin;$i>=$end; $i->modify('-1 day')){
				$date=$i->format("Y-m-d");
				$batch_data= DB::connection('mysql2')->select("SELECT 
					count(distinct(Bat_id)) as active_batches,
					count(rg.reg_id) as enrolled,
					sum(if((rg.due_amount>1 AND rg.s_type='Approved' AND rg.refund_status!='YES' AND rg.duedate!='' AND DATE(rg.duedate)>='$date'),1,0)) as due_student,
					GROUP_CONCAT(distinct(Bat_id)) as batch_ids
					FROM tbl_batch as tb
					Left Join tbl_registration as rg
					ON rg.batch_id=tb.Bat_id
					where $where AND DATE_FORMAT(tb.end_date,'%Y-%m-%d')>='$date'")[0];

				//print_r($batch_data);die();

				if($batch_data->batch_ids!=NULL){
				   $batch_ids.=$batch_data->batch_ids;

				   $batch_data->batch_ids=rtrim($batch_data->batch_ids,",");
				   $punch = DB::table('student_attendance')->selectraw("count(id) as total_punch,sum(if(due_date=1,1,0)) as due_punch")
					->whereRAW("batch_id IN (".$batch_data->batch_ids.")")
					->whereRAW("date like '".$date."%'")->first();
				  
				  $total_punch=$punch->total_punch;
				  $due_punch=$punch->due_punch;
				}else{
					$total_punch=0;
					$due_punch=0;
				}

				$record[]=[
					    'date'=>$date,
					    'day'=>date("l",strtotime($date)),
			            'active_batches'=>$batch_data->active_batches,
						'enrolled'=>$batch_data->enrolled,
						'due_student'=>$batch_data->due_student,
						'batch_ids'=>$batch_data->batch_ids,
					    "total_punch"=>$total_punch,
					    "due_punch"=>$due_punch,
					    "present_precent"=> round(($total_punch*100/max($batch_data->enrolled,1)),2),
					    "due_precent"    => round(($due_punch*100/max($batch_data->due_student,1)),2),
					];
			}
	         
	        
	        //OverAll Data
			$fdate=date("Y-m-d");
			$tdate=date("Y-m-d");
			$overall_data= DB::connection('mysql2')->select("SELECT 
				count(distinct(Bat_id)) as active_batches,
				count(rg.reg_id) as enrolled,
				sum(if((rg.due_amount>1 AND rg.s_type='Approved' AND rg.refund_status!='YES' AND rg.duedate!='' AND DATE(rg.duedate)>='$fdate'),1,0)) as due_student,
				GROUP_CONCAT(distinct(Bat_id)) as batch_ids
				FROM tbl_batch as tb
				Left Join tbl_registration as rg
				ON rg.batch_id=tb.Bat_id
				where tb.branch='$location' $batch_code AND DATE_FORMAT(tb.end_date,'%Y-%m-%d')>='$fdate'")[0];
	        
	        $total_punch=0;
			$due_punch=0;
			if($overall_data->batch_ids!=null){
			  $punch = DB::table('student_attendance')->selectraw("count(id) as total_punch,sum(if(due_date=1,1,0)) as due_punch")
				->whereRAW("batch_id IN (".$overall_data->batch_ids.")")
				//->where("date",'=',$fdate)
				//->where("date",'=',$tdate)
				->whereRAW("date like '".$fdate."%'")
				->first();

			  $total_punch=$punch->total_punch;
			  $due_punch=$punch->due_punch;
			}

			$overall=['active_batches'=>$overall_data->active_batches,
				'enrolled'=>$overall_data->enrolled,
				'due_student'=>$overall_data->due_student,
				'batch_ids'=>$overall_data->batch_ids,
			    "total_punch"=>$total_punch,
			    "due_punch"=>$due_punch
			];

			$course = DB::connection('mysql2')->table('tbl_course')->orderby('course_name')->get();

			//put to cache
			$cache_data=Cache::remember($cache_key,3600*12,function()use($course,$record,$overall){
				$cache_data=['course'=>$course,'record'=>$record,'overall'=>$overall];
                return $cache_data;
			});
		}

		if(!empty($cache_data) && count($cache_data)){
			$course =$cache_data['course'];
			$record=$cache_data['record'];
			$overall=$cache_data['overall'];
			$chart_mom=[];
		}

		/*$course =[];
		$record=[];
		$overall=[];
		$chart_mom=[];*/
        
        $month=date("Y-m");
        $chart_mom=[];
        return view('admin.academic.attendance',compact('location','course','record','overall','chart_mom'));
	}

	public function attendancechartmom(Request $request){ 
		return response(['status'=>true,'chart_mom'=>[]],200);
	    $location=Auth::user()->user_branches[0]->branch['branch_location'];
        $where   = "1=1 ";
		$where.=" AND tb.branch='".$location."' ";
        $month=date("Y-m");
        $chart_mom=[];
		for($i=0;$i<4; $i++){
            $s_week="";
            $e_week="";
			if($i=0){
              $s_week=$month."-01";
              $e_week=$month."-08";
			}else if($i=1){
              $s_week=$month."-09";
              $e_week=$month."-16";
			}else if($i=2){
              $s_week=$month."-17";
              $e_week=$month."-24";
			}else if($i=3){
              $s_week=$month."-25";
              $e_week=$month."-31";
			}

			$batch_data= DB::connection('mysql2')->select("SELECT 
				count(distinct(Bat_id)) as active_batches,
				count(rg.reg_id) as enrolled,
				sum(if((rg.due_amount>1 AND rg.s_type='Approved' AND rg.refund_status!='YES'),1,0)) as due_student,
				GROUP_CONCAT(distinct(Bat_id)) as batch_ids
				FROM tbl_batch as tb
				Left Join tbl_registration as rg
				ON rg.batch_id=tb.Bat_id
				where $where AND DATE_FORMAT(tb.end_date,'%Y-%m-%d')>='$e_week'")[0];

			//print_r($batch_data);die();

			if($batch_data->batch_ids!=NULL){
			   $batch_data->batch_ids=rtrim($batch_data->batch_ids,",");
			   $punch = DB::table('student_attendance')->selectraw("count(id) as total_punch,sum(if(due_date=1,1,0)) as due_punch")
				->whereRAW("batch_id IN (".$batch_data->batch_ids.")")
				->whereRAW("date>='".$s_week."%' AND date<='".$e_week."%'")->first();
			  
			  $total_punch=$punch->total_punch;
			  $due_punch=$punch->due_punch;
			}else{
				$total_punch=0;
				$due_punch=0;
			}

			$chart_mom[]=[
			    'week'=> ($i+1),
	            'active_batches'=>$batch_data->active_batches,
				'enrolled'=>$batch_data->enrolled,
				'due_student'=>$batch_data->due_student,
			    "total_punch"=>$total_punch,
			    "due_punch"=>$due_punch,
			    "present_precent"=> round(($total_punch*100/max($batch_data->enrolled,1)),2),
			    "due_precent"=> round(($due_punch*100/max($batch_data->due_student,1)),2),
			];
		}

		return response(['status'=>true,'chart_mom'=>$chart_mom],200);

	}
	
	public function get_academic_student(Request $request){
		$is_cxo  	= 	Auth::user()->is_cxo;
		$erp_category  		= 	Auth::user()->course_category;
		$designation  		=	Auth::user()->user_details->degination;
		$location 			= 	Auth::user()->user_branches[0]->branch['branch_location'];

		$branch		=	$request->branch;
		$category	=	$request->category;
		$slab		=	$request->slab;
		$type		=	$request->type;
		
		//$where = "rg.s_type='Approved' AND rg.refund_status!='YES' ";
		$where = "rg.s_type='Approved' ";

		if($is_cxo==0 && $designation=="CATEGORY HEAD"){
			$erp_category = explode(',',$erp_category);
			$course_category = "'".implode("','",$erp_category)."'";
			$where	.= " AND tb.category IN (".$course_category.")";
		}
		
		if($is_cxo==0 &&  $designation=="CITY HEAD"){
			$where	.= " AND tb.branch = '".$location."'";
		}


		//filter
		if(!empty($request->branch)){
			$where.=" AND tb.branch='$request->branch'";
		}

		

		if(($is_cxo==1 || $designation=="CITY HEAD") && !empty($request->category)){
			$where.=" AND tb.category_maper='$request->category'";
		}else if(!empty($request->category)){
			$where.=" AND tb.category='$request->category'";
		}
		
		if($type=='pastdays'){
			if($slab==1){
				$where .= " AND (due_days BETWEEN -9 AND 0)";
			}else if($slab==2){
				$where .= " AND (due_days BETWEEN -29 AND -10)";
			}else if($slab==3){
				$where .= " AND (due_days BETWEEN -59 AND -30)";
			}else if($slab==4){
				$where .= " AND (due_days < -60)";
			}
		}else if($type=='upcomming'){
			if($slab==1){
				$where .= " AND (due_days BETWEEN 0 AND 3)";
			}else if($slab==2){
				$where .= " AND (due_days BETWEEN 4 AND 7)";
			}else if($slab==3){
				$where .= " AND (due_days BETWEEN 8 AND 15)";
			}else if($slab==4){
				$where .= " AND (due_days BETWEEN 16  AND 30)";
			}
		}

		//die();


		$student = DB::connection('mysql2')->select("SELECT rg.*,tb.batch_running_status,boh.status as course_status,tb.category,rc.receipt_date
			FROM tbl_registration as rg
			left join tbl_batch as tb
			ON tb.Bat_id=rg.batch_id
			left join tbl_batch_onlinehistory as boh
			ON boh.reg_no=rg.reg_number AND boh.id=(SELECT max(id) from tbl_batch_onlinehistory as mxbh where mxbh.reg_no=boh.reg_no)
			left join tbl_receipt as rc
			ON rc.s_reg_number=rg.reg_number AND rc.id=(SELECT max(id) from tbl_receipt as mxrc where mxrc.s_reg_number=rc.s_reg_number)
			where duedate!='' AND $where");
			
		return view('admin.academic.student',compact('student'));
	}

	public function fee_chart(Request $request){
		//is cxo only month change & City and chart will be WOW only
		$is_cxo  	 		= 	Auth::user()->is_cxo;

		$erp_category  		= 	Auth::user()->course_category;
		$designation  		=	Auth::user()->user_details->degination;
		$location 			= 	Auth::user()->user_branches[0]->branch['branch_location'];
		
		//$where = "rg.s_type='Approved' AND rg.refund_status!='YES' ";
		$where="1=1 ";
		//$whereMOM="1=1 AND month>=".date('Y-m',strtotime(date("Y-m-d")." -4 month"))." ";
		$whereMOM="1=1 ";
		
		//is CATEGORY HEAD only month change  & City and chart will be WOW & MOM by City Wise
		if($designation=="CATEGORY HEAD"){
			$erp_category = explode(',',$erp_category);
			$course_category = "'".implode("','",$erp_category)."'";
			$where	 .= " AND category IN (".$course_category.")";
			$whereMOM.= " AND category IN (".$course_category.")";
		}

		//is CITY HEAD only month change  & Category and chart will be WOW & MOM by City Wise
		if($designation=="CITY HEAD"){
			$where	.= " AND branch = '".$location."'";
			$whereMOM	.= " AND branch = '".$location."'";
		}

		if(!empty($request->month)){
          $where.=" AND month='".$request->month."' ";
		}else{
          $where.=" AND month='".date("Y-m")."' ";
		}

		if(!empty($request->branch)){
          $where.=" AND branch='".$request->branch."' ";
		}

		if(!empty($request->category)){
          $where.=" AND category='".$request->category."' ";
          
          $whereMOM.=" AND category='".$request->category."' ";
		}

		if(!empty($request->filter_key) && !empty($request->filter_value)){
          $where.=" AND ".$request->filter_key."='".$request->filter_value."' ";
		}

		

		$groupby_column="branch";
		if($is_cxo){
		  $groupby_column="branch";
		}elseif($designation=="CITY HEAD"){
		  $groupby_column="category";
		  $groupby_column="category_maper";
		  $groupby_column="branch";
		}elseif($designation=="CATEGORY HEAD"){
		  $groupby_column="branch";
		  $groupby_column="null";
		}



		$monthList=$this->getmonths();

		$data = DB::connection('mysql2')->select("SELECT $groupby_column as groupby_column,week,sum(paid_fee) as paid_fee,sum(due_fee) as due_fee FROM academic_fee_recovery Where $where group by $groupby_column,week"); 
        $chart_data=[];
		foreach($data as $val){
			$key=str_replace(" ","",$val->groupby_column)."WOW";
			$chart_data[$key][]=["filter"=>"Yes","title"=>$val->groupby_column." (WOW)",
			"filter_key"=>$groupby_column,"filter_value"=>$val->groupby_column,
			"week"=>"Week ".$val->week,"paid_fee"=>$val->paid_fee,"due_fee"=>$val->due_fee];
		}

		if($designation=="CITY HEAD" || $designation=="CATEGORY HEAD"){
			$data = DB::connection('mysql2')->select("SELECT $groupby_column as groupby_column,month as week,sum(paid_fee) as paid_fee,sum(due_fee) as due_fee FROM academic_fee_recovery Where $whereMOM group by $groupby_column,month order by id desc"); 
	        //$chart_data=[];
			foreach($data as $val){
				$key=str_replace(" ","",$val->groupby_column)."MOM";
				$chart_data[$key][]=["filter"=>"No","title"=>$val->groupby_column." (MOM)",
					"filter_key"=>$groupby_column,"filter_value"=>$val->groupby_column,
					"week"=>$val->week,"paid_fee"=>$val->paid_fee,"due_fee"=>$val->due_fee];
			}

			//$grouptype='month limit 12 orderby by id desc';

		}



		//print_r($chart_data);
		//echo json_encode($chart_data);die('');
		return response(['monthList'=>$monthList,'current_month'=>date('Y-m'),'chart_data'=>$chart_data]);
		// return view('admin.academic.index');
	}
	
	public function getmonths(){		
		$months = [];
        for($i =0; $i < 8; $i++) {
		  $months[]=date('Y-m', strtotime("-$i month"));
		}
        return $months;
	}
	
	
	
	public function due_student_report_excel(){   
		$logged_role_id  = Auth::user()->role_id;
		$logged_id       = Auth::user()->id;
		$designation  	=Auth::user()->user_details->degination;

		
		$data_date		= 	Input::get('data_date');
		$data_location	= 	Input::get('data_location');

		$where="1=1 AND rg.s_type='Approved' AND due_amount>1";
		$where.=" AND tb.branch='$data_location'";

		$batch_code="";
		if($designation=="CENTER HEAD"){
            $branch_id= 	Auth::user()->user_branches[0]->branch_id;
			$timetable=DB::table('timetables')->selectRAW("batch.batch_code")
			->leftJoin('batch','batch.id','timetables.batch_id')
			->where('timetables.branch_id',$branch_id)
			->where('cdate','>=',$data_date)
			->where('cdate','<=',$data_date)
			->groupby('batch_id')->get();
			$batch_code='-1';
			foreach($timetable as $val){
				if(!empty($val->batch_code)){
                  $batch_code.=",".$val->batch_code;
				}
			}

			$where.= " AND tb.Bat_id IN (".$batch_code.")";
		}
	
		$data =  DB::connection('mysql2')->select("SELECT rg.*,tb.batch_running_status,boh.status as course_status,tb.category,rc.receipt_date
			FROM tbl_registration as rg
			left join tbl_batch as tb
			ON tb.Bat_id=rg.batch_id
			left join tbl_batch_onlinehistory as boh
			ON boh.reg_no=rg.reg_number AND boh.id=(SELECT max(id) from tbl_batch_onlinehistory as mxbh where mxbh.reg_no=boh.reg_no)
			left join tbl_receipt as rc
			ON rc.s_reg_number=rg.reg_number AND rc.id=(SELECT max(id) from tbl_receipt as mxrc where mxrc.s_reg_number=rc.s_reg_number)
			where $where AND tb.end_date>= '$data_date' AND DATE(rg.duedate)>='$data_date'");
				
        if(count($data) > 0){
            return Excel::download(new DueStudentExport($data), 'DueStudentExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    } 
	
}
