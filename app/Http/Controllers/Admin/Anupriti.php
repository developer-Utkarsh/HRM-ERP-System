<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Asset;
use App\AssignAsset;
use App\User;
use Excel;
use Input;
use Validator;
use DataTables;
use DB;
use Auth;
use DateTime;
use App\Exports\AnupritiExport;
use Carbon\Carbon;

class Anupriti extends Controller
{	
	public function anupritiAttendence(Request $request){
		return view('admin.batchinventory.anupriti.attendence');
	}

	public function anupritiAttendenceDetail(Request $request){		
		$application_id				= $request->application_id;
		$course           = $request->course;
        $month    = $request->month;
        $location    = $request->location;
        $category=$request->category;
        
		if(!empty($course)){
			// $responseArray = $this->calculate_attendance($reg_no,$batch_id);
			
			$responseArray = $this->calculate_attendance_session_24_25($month,$course,$location,$application_id,$category);
			
			// if(!empty($category)){
			  // $responseArray = $this->calculate_attendance_session_23_24_cat($course,$location,$category);
			// }else{
				 // $responseArray = $this->calculate_attendance_session_24_25($month,$course,$location,$application_id);
			// }
		}else{
			$responseArray = array();
		}

		//echo "<pre>";print_R($responseArray); die;
		return DataTables::of($responseArray)->make(true);

		
	}
	
	public function calculate_attendance($reg_no,$batch_id){
		$student = DB::table("student_anupriti");
		
		if(!empty($batch_id)){
		  $student->where('batch_id',$batch_id);
		}
		
		if(!empty($reg_no)){
			$student->where('reg_number',$reg_no);
		}

		$student->whereIN("cast",['Anupriti Yojna-2023-24','Anupriti Yojna-2022-23']);
		
		$student=$student->get();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			foreach($student as $key => $val){

				$sdate=date("Y-m-d",strtotime($val->sdate));
				$edate=date("Y-m-d",strtotime($val->edate));

				$ts1 = strtotime($sdate);
				$ts2 = strtotime($edate);

				$year1 = date('Y', $ts1);
				$year2 = date('Y', $ts2);

				$month1 = date('m', $ts1);
				$month2 = date('m', $ts2);

				$monthDifference =(($year2 - $year1) * 12) + ($month2 - $month1);

                for($sm=0;$sm<=$monthDifference;$sm++){
                	$month=date("Y-m",strtotime($sdate."+".$sm." month"));

					$attendance = DB::table("student_anupriti_attedance")
					->select('*',DB::raw("DATE_FORMAT(absent_date,'%Y-%m-%d') as pdate"))
					->where('reg_no',$val->reg_number);
					$attendance->whereRAW("absent_date like '".$month."%'");
					//$attendance=$attendance->whereDate("absent_date",'>=',$sdate);
					//$attendance=$attendance->whereDate("absent_date",'<=',$edate);
					$attendance = $attendance->get();

					//print_r($attendance);die();
					
					
					$cmonth = explode('-',$month);
					$yr = $cmonth[0];
					$mt = $cmonth[1];

					$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
					$first_date = strtotime($yr.'-'.$mt.'-01');
					$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
					$i=1;
					$totalDays=$daysInMonth;
					$total_present = 0;
					$total_absent  = 0;
					$attendance=json_decode(json_encode($attendance),true);
					while($daysInMonth>0){
						$add_get_date  = date('Y-m-d', $first_date);

						if($add_get_date<$sdate || $add_get_date>$edate){
							$data[$i]='-';
						}else{

							//$sunday=["2022-05-01","2022-05-08","2022-05-15","2022-05-22","2022-05-29","2022-06-05","2022-06-12","2022-06-19","2022-06-26","2022-07-03","2022-07-10","2022-07-17","2022-07-24","2022-07-31","2022-08-07","2022-08-14","2022-08-21","2022-08-28","2022-09-04","2022-09-11","2022-09-18","2022-09-25","2022-10-02","2022-10-09","2022-10-16","2022-10-23","2022-10-30","2022-11-06","2022-11-13","2022-11-20","2022-11-27","2022-12-04","2022-12-11","2022-12-18","2022-12-25","2023-01-01","2023-01-08","2023-01-15","2023-01-22","2023-01-29","2023-02-05","2023-02-12","2023-02-19","2023-02-26","2023-03-05","2023-03-12","2023-03-19","2023-03-26","2023-04-02","2023-04-09","2023-04-16","2023-04-23","2023-04-30","2023-05-07","2023-05-14","2023-05-21","2023-05-28","2023-06-04","2023-06-11","2023-06-18","2023-06-25","2023-07-02","2023-07-09","2023-07-16","2023-07-23","2023-07-30","2023-08-06","2023-08-13","2023-08-20","2023-08-27","2023-09-03","2023-09-10","2023-09-17","2023-09-24","2023-10-01","2023-10-08","2023-10-15","2023-10-22","2023-10-29","2023-11-05","2023-11-12","2023-11-19","2023-11-26","2023-12-03","2023-12-10","2023-12-17","2023-12-24","2023-12-31","2024-01-07","2024-01-14","2024-01-21","2024-01-28"];
							
							$sunday=["2025-04-20","2025-04-27","2025-05-04","2025-05-11","2025-05-18","2025-05-25","2025-06-01","2025-06-08","2025-06-15","2025-06-22","2025-06-29","2025-07-06","2025-07-13","2025-07-20","2025-07-27","2025-08-03","2025-08-10","2025-08-17","2025-08-24","2025-08-31","2025-09-07","2025-09-14","2025-09-21","2025-09-28","2025-10-05","2025-10-12","2025-10-19","2025-10-26","2025-11-02","2025-11-09","2025-11-16","2025-11-23","2025-11-30","2025-12-07","2025-12-14","2025-12-21","2025-12-28","2026-01-04","2026-01-11","2026-01-18","2026-01-25","2026-02-01","2026-02-08","2026-02-15","2026-02-22","2026-03-01","2026-03-08","2026-03-15","2026-03-22","2026-03-29","2026-04-05","2026-04-12","2026-04-19","2026-04-26","2026-05-03","2026-05-10","2026-05-17","2026-05-24"];

						   //$holiday=["2022-01-14","2022-01-15","2022-01-26","2022-03-16","2022-03-17","2022-03-18","2022-03-19","2022-05-03","2022-06-30","2022-07-23","2022-07-25","2022-07-26","2022-07-27","2022-08-11","2022-08-15","2022-08-26","2022-08-29","2022-09-30","2022-10-02","2022-10-24","2022-10-25","2022-10-26","2022-10-27","2023-01-14","2023-01-26","2023-03-06","2023-03-07","2023-03-08","2023-04-22","2023-06-29","2023-08-15","2023-08-30","2023-08-31","2023-09-17","2023-09-25","2023-09-28","2023-10-02","2023-10-24","2023-11-10","2023-11-11","2023-11-12","2023-11-13","2023-11-14","2023-11-15","2023-11-16","2023-12-06","2023-12-07","2024-01-15","2024-01-24","2024-01-26"];
						   
						   $holiday = ["2025-05-29","2025-06-07","2025-07-06","2025-08-09","2025-08-15","2025-08-16","2025-09-02","2025-09-05","2025-09-22","2025-09-30","2025-10-02","2025-10-20","2025-10-22","2025-10-23","2025-11-05","2025-12-25","2025-12-27"];
						   
						    if(in_array($add_get_date,$sunday)){
		                       $data[$i]='WO';
						    }else if(in_array($add_get_date,$holiday)){
		                       $data[$i]='H';
						    }else{
								$atdIndex=array_search($add_get_date,array_column($attendance,'pdate'));
								if($atdIndex!== false || $add_get_date>date("Y-m-d")){ 
								 $data[$i]='A';
								 $total_absent++;
								}else{
									$data[$i]='P';
								    $total_present++;
								}
							}
						}

		                $daysInMonth--;
					    $first_date += 86400; 
						$i++;
					}

					if($totalDays<31){
						while($totalDays<=31){
							$data[$i]='-';
							$i++;
							$totalDays++;
						}
					}

					$data['month']  =date("M-Y",strtotime($month));
					$data['s_name'] =$val->s_name;
					$data['s_regnumber']=$val->reg_number;
					$data['total_present']=$total_present;
					$data['total_absent']=$total_absent;
					$data['percentage']=round(($total_present*100)/($total_present+$total_absent),2);
					
					$data_f[]=$data;
				}
				
				
				
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}
	
	public function anupritiAttendenceExport(Request $request){
		$reg_no				= $request->name;
		$batch_id           = $request->batch_id;
        $year_wise_month    = $request->year_wise_month;
        
		if(!empty($batch_id) || !empty($reg_no)){
			$responseArray = $this->calculate_attendance($reg_no,$batch_id,$year_wise_month);
		}else{
			$responseArray = array();
		}
		
        if(count($responseArray) > 0){
            return Excel::download(new AnupritiExport($responseArray), 'AnupritiExport.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong !');
        }
	}

	public function calculate_attendanc($reg_no,$batch_id,$month){
		$student = DB::connection('mysql2')->table("tbl_registration")
				->select('reg_number','s_name','f_name','duedate','batch','batch_id','assign_inventory','rfid_no','cast');
		
		if(!empty($batch_id)){
		  $student->where('batch_id',$batch_id);
		}
		
		if(!empty($reg_no)){
			$student->where('reg_number',$reg_no);
		}
		
		$student->whereRaw(DB::raw("cast like '%Anupriti Yojna-2023-24%'"));
		
		$student=$student->get();
		// echo $batch_id;
		// print_r($student);
		// die();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			foreach($student as $key => $val){
				$attendance = DB::table("student_anupriti_attedance")->select('*',DB::raw("DATE_FORMAT(absent_date,'%Y-%m-%d') as pdate"))->where('reg_no',$val->reg_number)->whereRAW("absent_date like '".$month."%'");
				
				$attendance = $attendance->get();
				
				
				$cmonth = explode('-',$month);
				$yr = $cmonth[0];
				$mt = $cmonth[1];

				
				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
				$i=1;
				$totalDays=$daysInMonth;
				$total_present = 0;
				$attendance=json_decode(json_encode($attendance),true);
				while($daysInMonth>0){
					$add_get_date  = date('Y-m-d', $first_date);

					//$sunday=["2022-07-10","2022-07-17","2022-07-24","2022-07-31","2022-08-07","2022-08-14","2022-08-21","2022-08-28","2022-09-04","2022-09-11","2022-09-18","2022-09-25","2022-10-02","2022-10-09","2022-10-16","2022-10-23","2022-10-30","2022-11-06","2022-11-13","2022-11-20","2022-11-27","2022-12-04","2022-12-11","2022-12-18","2022-12-25","2023-01-01","2023-01-08","2023-01-15","2023-01-22","2023-01-29","2023-02-05","2023-02-12","2023-02-19","2023-02-26","2023-03-05","2023-03-12","2023-03-19","2023-03-26","2023-04-02","2023-04-09","2023-04-16","2023-04-23","2023-04-30","2023-05-07","2023-05-14","2023-05-21","2023-05-28","2023-06-04","2023-06-11","2023-06-18","2023-06-25","2023-07-02","2023-07-09","2023-07-16","2023-07-23","2023-07-30","2023-08-06","2023-08-13","2023-08-20","2023-08-27","2023-09-03","2023-09-10","2023-09-17","2023-09-24","2023-10-01","2023-10-08","2023-10-15","2023-10-22","2023-10-29","2023-11-05","2023-11-12","2023-11-19","2023-11-26","2023-12-03","2023-12-10","2023-12-17","2023-12-24","2023-12-31","2024-01-07","2024-01-14"];
					
					$sunday=["2025-04-20","2025-04-27","2025-05-04","2025-05-11","2025-05-18","2025-05-25","2025-06-01","2025-06-08","2025-06-15","2025-06-22","2025-06-29","2025-07-06","2025-07-13","2025-07-20","2025-07-27","2025-08-03","2025-08-10","2025-08-17","2025-08-24","2025-08-31","2025-09-07","2025-09-14","2025-09-21","2025-09-28","2025-10-05","2025-10-12","2025-10-19","2025-10-26","2025-11-02","2025-11-09","2025-11-16","2025-11-23","2025-11-30","2025-12-07","2025-12-14","2025-12-21","2025-12-28","2026-01-04","2026-01-11","2026-01-18","2026-01-25","2026-02-01","2026-02-08","2026-02-15","2026-02-22","2026-03-01","2026-03-08","2026-03-15","2026-03-22","2026-03-29","2026-04-05","2026-04-12","2026-04-19","2026-04-26","2026-05-03","2026-05-10","2026-05-17","2026-05-24"];
					
				   //$holiday=["14-01-2023","26-01-2023","06-03-2023","07-03-2023","08-03-2023","22-04-2023","29-06-2023","15-08-2023","30-08-2023","31-08-2023","17-09-2023","25-09-2023","28-09-2023","02-10-2023","24-10-2023","10-11-2023","11-11-2023","12-11-2023","13-11-2023","14-11-2023","15-11-2023","16-11-2023","06-12-2023","07-12-2023","15-01-2024"];
				   
				   $holiday = ["2025-05-29","2025-06-07","2025-07-06","2025-08-09","2025-08-15","2025-08-16","2025-09-02","2025-09-05","2025-09-22","2025-09-30","2025-10-02","2025-10-20","2025-10-22","2025-10-23","2025-11-05","2025-12-25","2025-12-27"];
				    if(in_array($add_get_date,$sunday)){
                       $data[$i]='WO';
				    }else if(in_array($add_get_date,$holiday)){
                       $data[$i]='H';
				    }else{
						$data[$i]='P';
						$total_present++;
                        $atdIndex=array_search($add_get_date,array_column($attendance,'pdate'));
						if($atdIndex!== false || $add_get_date>date("Y-m-d")){ 
						 $data[$i]='A';
						 $total_present--;
						}
					}

                    $daysInMonth--;
					$first_date += 86400; 
					$i++;
				}
				
				
				$data['s_name']=$val->s_name;
				$data['s_regnumber']=$val->reg_number;
				$data['total_present']=$total_present;
				$data['total_absent']=$totalDays-$total_present;
				
				$data_f[]=$data;
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}


	public function calculate_attendance_session_23_24_old($reg_no,$batch_id){
		$student = DB::table("student_session_23_24");
				
		if(!empty($reg_no)){
			$student->where('application_id',$reg_no);
		}
		
		$student=$student->get();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			foreach($student as $key => $val){
				
				$dateObject = DateTime::createFromFormat('d/m/Y', $val->admission_date);
				$sdate = $dateObject->format('Y-m-d');

				if(!empty($val->exit_date)){
					$exit = $val->exit_date;
				}else{
					$exit = '31/07/2024';
				}
				$dateObject2 = DateTime::createFromFormat('d/m/Y', $exit);
				$edate = $dateObject2->format('Y-m-d');

				$ts1 = strtotime($sdate);
				$ts2 = strtotime($edate);

				$year1 = date('Y', $ts1);
				$year2 = date('Y', $ts2);

				$month1 = date('m', $ts1);
				$month2 = date('m', $ts2);

				$monthDifference =(($year2 - $year1) * 12) + ($month2 - $month1);

				
				
                for($sm=0;$sm<=$monthDifference;$sm++){
                	// $month=date("Y-m",strtotime($sdate."+".$sm." month"));
					$month = date('Y-m', strtotime('last day of +' . $sm . ' month', strtotime($sdate)));
	
					$attendance = DB::table("student_attendance_session_23_24")->where('application_id',$val->application_id);
					$attendance->whereRAW("date like '".$month."%'");
					$attendance = $attendance->get();

					//print_r($attendance);die();
					
					
					$cmonth = explode('-',$month);
					$yr = $cmonth[0];
					$mt = $cmonth[1];

					$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
					$first_date = strtotime($yr.'-'.$mt.'-01');
					$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
					$i=1;
					$totalDays=$daysInMonth;
					$total_present = 0;
					$total_absent  = 0;
					$attendance=json_decode(json_encode($attendance),true);
					while($daysInMonth>0){
						$add_get_date  = date('Y-m-d', $first_date);

						if($add_get_date<$sdate || $add_get_date>$edate){
							$data[$i]='-';
						}else{
							//$data[$i]='H';
							$atdIndex=array_search($add_get_date,array_column($attendance,'date'));
							$data[$i]=$attendance[$atdIndex]['type']??'-';
							
							
							if($data[$i]=='A'){
								$total_absent++;
							}else if($data[$i]=='P'){
								$total_present++;
							}
						
						}

		                $daysInMonth--;
					    $first_date += 86400; 
						$i++;
					}

					if($totalDays<31){
						while($totalDays<=31){
							$data[$i]='-';
							$i++;
							$totalDays++;
						}
					}

					$data['month']  =date("M-Y",strtotime($month));
					$data['s_name'] =$val->s_name;
					$data['s_regnumber']=$val->application_id;
					$data['total_present']=$total_present;
					$data['total_absent']=$total_absent;
					$data['percentage']=round(($total_present*100)/($total_present+$total_absent),2);
					
					$data_f[]=$data;
				}
				
				
				
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}

	public function calculate_attendance_session_23_24($month,$course,$location,$application_id){
		$student = DB::table("student_session_23_24");
				
		if(!empty($application_id)){
			$student->where('application_id',$application_id);
		}

		if(!empty($course)){
			$student->where('course',$course);
		}

		if(!empty($location)){
			$student->where('location',$location);
		}
		

		if(!empty($month)){
			//$student->whereRAW("MONTH(admission_date)>='".$month."%'");
			$student->whereRAW("DATE_FORMAT(admission_date,'%Y-%m')<='".$month."'");
			$student->whereRAW("DATE_FORMAT(exit_date,'%Y-%m')>='".$month."'");
			//$student->whereRAW("MONTH(exit_date)<='".$month."%'");
		}
        
        if(!empty($category)){
		   $student->where('category',$category);
		}

		$student->orderby('admission_date','asc');
		
		$student=$student->get();
		//echo $student=$student->toSql();die();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			$iteration=0;
			foreach($student as $key => $val){
				$sdate = $val->admission_date;
				$edate = $val->exit_date;

				// if(!empty($category)){
				//   $month=date("Y-m",strtotime($sdate));
				// }
	
				$attendance = DB::table("student_attendance_session_23_24")
				->where('application_id',$val->application_id);
				$attendance->whereRAW("date like '".$month."%'");
				$attendance = $attendance->get();

				//print_r($attendance);die();
				
				
				$cmonth = explode('-',$month);
				$yr = $cmonth[0];
				$mt = $cmonth[1];

				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
				$i=1;
				$totalDays=$daysInMonth;
				$total_present = 0;
				$total_absent  = 0;
				$attendance=json_decode(json_encode($attendance),true);
				while($daysInMonth>0){
					$add_get_date  = date('Y-m-d', $first_date);

					if($add_get_date<$sdate || $add_get_date>$edate){
						$data[$i]='-';
					}else{
						//$data[$i]='H';
						$atdIndex=array_search($add_get_date,array_column($attendance,'date'));
						$data[$i]=$attendance[$atdIndex]['type']??'-';
						
						
						if($data[$i]=='A'){
							$total_absent++;
						}else if($data[$i]=='P'){
							$total_present++;
						}
					
					}

	                $daysInMonth--;
				    $first_date += 86400; 
					$i++;
				}

				if($totalDays<31){
					while($totalDays<=31){
						$data[$i]='-';
						$i++;
						$totalDays++;
					}
				}

				$iteration++;

				$data['month']         =date("M-Y",strtotime($month));
				$data['sno']           =$iteration;
				$data['s_name']        =$val->s_name;
				$data['application_id']=$val->application_id;
				$data['admission_date']=$val->admission_date;
				$data['exit_date']     =$val->exit_date;
				$data['district']      =$val->district;
				$data['total_present'] =$total_present;
				$data['total_absent']  =$total_absent;
				$data['percentage']    =round(($total_present*100)/($total_present+$total_absent),2);
				
				$data_f[]=$data;
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}

	public function calculate_attendance_session_23_24_cat($course,$location,$category){
		$student = DB::table("student_session_23_24");

		$student->where('id','>','6755');
				
		if(!empty($application_id)){
			$student->where('application_id',$application_id);
		}

		if(!empty($course)){
			$student->where('course',$course);
		}

		if(!empty($location)){
			$student->where('location',$location);
		}
        
        if(!empty($category)){
		   $student->where('category',$category);
		}

		$student->orderby('admission_date','asc');
		
		$student=$student->get();
		//print_r($student);
		//echo $student=$student->toSql();die();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			$iteration=0;
			foreach($student as $key => $val){
				$sdate = $val->admission_date;
				$edate = $val->exit_date;

				$ts1 = strtotime($sdate);
				$ts2 = strtotime($edate);

				$year1 = date('Y', $ts1);
				$year2 = date('Y', $ts2);

				$month1 = date('m', $ts1);
				$month2 = date('m', $ts2);

				$monthDifference =(($year2 - $year1) * 12) + ($month2 - $month1);

				
				
                for($sm=0;$sm<=$monthDifference;$sm++){
                	// $month=date("Y-m",strtotime($sdate."+".$sm." month"));
					$month = date('Y-m', strtotime('last day of +' . $sm . ' month', strtotime($sdate)));
	
					$attendance = DB::table("student_attendance_session_23_24")
					->where('id','>',1437329)
					->where('application_id',$val->application_id);
					$attendance->whereRAW("date like '".$month."%'");
					$attendance = $attendance->get();

					//print_r($attendance);die();
					
					
					$cmonth = explode('-',$month);
					$yr = $cmonth[0];
					$mt = $cmonth[1];

					$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
					$first_date = strtotime($yr.'-'.$mt.'-01');
					$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
					$i=1;
					$totalDays=$daysInMonth;
					$total_present = 0;
					$total_absent  = 0;
					$attendance=json_decode(json_encode($attendance),true);
					while($daysInMonth>0){
						$add_get_date  = date('Y-m-d', $first_date);

						if($add_get_date<$sdate || $add_get_date>$edate){
							$data[$i]='-';
						}else{
							//$data[$i]='H';
							$atdIndex=array_search($add_get_date,array_column($attendance,'date'));
							$data[$i]=$attendance[$atdIndex]['type']??'-';
							
							
							if($data[$i]=='A'){
								$total_absent++;
							}else if($data[$i]=='P'){
								$total_present++;
							}
						
						}

		                $daysInMonth--;
					    $first_date += 86400; 
						$i++;
					}

					if($totalDays<31){
						while($totalDays<=31){
							$data[$i]='-';
							$i++;
							$totalDays++;
						}
					}

					$iteration++;

					$data['month']         =date("M-Y",strtotime($month));
					$data['sno']           =$iteration;
					$data['s_name']        =$val->s_name;
					$data['application_id']=$val->application_id;
					$data['admission_date']=$val->admission_date;
					$data['exit_date']     =$val->exit_date;
					$data['district']      =$val->district;
					$data['total_present'] =$total_present;
					$data['total_absent']  =$total_absent;
					//$data['percentage']    =round(($total_present*100)/($total_present+$total_absent),2);
					if (($total_present + $total_absent) > 0) {
					    $data['percentage'] = round(($total_present * 100) / ($total_present + $total_absent), 2);
					} else {
					    $data['percentage'] = 0; // Default value when no attendance data is available
					}
					
					$data_f[]=$data;
				}
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}



	public function calculate_attendance_session_24_25($month,$course,$location,$application_id,$category){
		$student = DB::table("student_session_24_25");
				
		if(!empty($application_id)){
			$student->where('application_id',$application_id);
		}

		if(!empty($course)){
			$student->where('course',$course);
		}

		if(!empty($location)){
			$student->where('location',$location);
		}
		

		if(!empty($month)){
			//$student->whereRAW("MONTH(admission_date)>='".$month."%'");
			$student->whereRAW("DATE_FORMAT(admission_date,'%Y-%m')<='".$month."'");
			$student->whereRAW("DATE_FORMAT(exit_date,'%Y-%m')>='".$month."'");
			//$student->whereRAW("MONTH(exit_date)<='".$month."%'");
		}
        
        if(!empty($category)){
		   $student->where('category',$category);
		}

		$student->orderby('admission_date','asc');
		
		$student=$student->get();
		// echo $student=$student->toSql();die();
		
		$comman_result = array();
		$cMonth = date('m');
		$data_f=[];
		if(count($student) > 0){
			$iteration=0;
			foreach($student as $key => $val){
				$sdate = $val->admission_date;
				$edate = $val->exit_date;

				// if(!empty($category)){
				//   $month=date("Y-m",strtotime($sdate));
				// }
	
				$attendance = DB::table("student_attendance_session_24_25")
				->where('application_id',$val->application_id);
				$attendance->whereRAW("date like '".$month."%'");
				$attendance = $attendance->get();

				//print_r($attendance);die();
				
				
				$cmonth = explode('-',$month);
				$yr = $cmonth[0];
				$mt = $cmonth[1];

				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
				$first_date = strtotime($yr.'-'.$mt.'-01');
				$last_date = strtotime($yr.'-'.$mt.'-'.$daysInMonth);
				$i=1;
				$totalDays=$daysInMonth;
				$total_present = 0;
				$total_absent  = 0;
				$attendance=json_decode(json_encode($attendance),true);
				while($daysInMonth>0){
					$add_get_date  = date('Y-m-d', $first_date);

					if($add_get_date<$sdate || $add_get_date>$edate){
						$data[$i]='-';
					}else{
						//$data[$i]='H';
						$atdIndex=array_search($add_get_date,array_column($attendance,'date'));
						$data[$i]=$attendance[$atdIndex]['type']??'-';
						
						
						if($data[$i]=='A'){
							$total_absent++;
						}else if($data[$i]=='P'){
							$total_present++;
						}
					
					}

	                $daysInMonth--;
				    $first_date += 86400; 
					$i++;
				}

				if($totalDays<31){
					while($totalDays<=31){
						$data[$i]='-';
						$i++;
						$totalDays++;
					}
				}

				$iteration++;

				$data['month']           =date("M-Y",strtotime($month));
				$data['sno']             =$iteration;
				$data['s_name']          =$val->s_name;
				$data['f_name']          =$val->f_name;
				$data['category']          =$val->category;
				$data['dob']        	 =$val->dob;
				$data['contact']         =$val->contact;
				$data['registration_no'] =$val->reg_no;
				$data['application_id']  =$val->application_id;
				$data['admission_date']  =$val->admission_date;
				$data['exit_date']       ='-';
				$data['district']        =$val->district;
				$data['total_present']   =$total_present;
				$data['total_absent']    =$total_absent;
				
				$total = $total_present + $total_absent;

				if ($total > 0) {
					$data['percentage'] = round(($total_present * 100) / $total, 2);
				} else {
					$data['percentage'] = 0;
				}
				$data_f[]=$data;
			}
			
			return $data_f;
		}else{
			return $data_f;
		}				
	}
}
