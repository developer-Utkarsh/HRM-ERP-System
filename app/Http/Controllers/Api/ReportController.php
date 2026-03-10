<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use App\Batchrelation;

class ReportController extends Controller
{
    /**
    * @var getFacultyClassReport
    * @param Request $request
    * @return Json Response
    */
	
	public function getFacultyClassReport(Request $request)
    {
		try {
			$input = $request->only(['faculty_id']);		

			if(isset($input['faculty_id']) && !empty($input['faculty_id'])){

				$faculty_id = $input['faculty_id'];
				//$date = '2022-07-06';
				$date = date('Y-m-d');
				$date = date('Y-m-d',strtotime('-7 days'));
				$get_faculty_today_class_report = Timetable::with(['batch','subject','startclass','topic','chapter'])->where('faculty_id',$faculty_id)->whereRaw("DATE(cdate) >= '$date'")->where('is_deleted','0')->get();
				
				$fullArray = $get_faculty_today_class_report->toArray();
				
				$keys = array_column($fullArray, 'batch_id');
				array_multisort($keys, SORT_ASC, $fullArray);
				// echo "<pre>";  print_r($fullArray); die;
				$batch_array = array();
				foreach($fullArray as $key=>$val){
					$batch_array[$val['batch_id']]['batch_name'] =  $val['batch']['name'];
					
					$batch_array[$val['batch_id']]['subjects'][$val['subject_id']]['subject_name'] =  $val['subject']['name'];
					$batch_array[$val['batch_id']]['subjects'][$val['subject_id']]['class'][$key] =  $val['id'];
				}
				// echo "<pre>"; print_R($batch_array); die;

				$get_faculty_classreport = [];
				$temp1 = array();

				if(count($batch_array) > 0){
					foreach($batch_array as $batch_id=>$batch_details){
						 
						$temp['batch_id'] = $batch_id;
						$temp['batch_name'] = $batch_details['batch_name'];    				
						$temp['subject'] = array();
						
						foreach($batch_details['subjects'] as $subject_id=>$subject_details){
							$temp1['subject_id'] = $subject_id;
							$temp1['subject_name'] = $subject_details['subject_name'];
							$temp1['topics'] = array();
							
							foreach($subject_details['class'] as $key=>$timetable_id){
									$timetable_details = $fullArray[$key];
									$startclassData = $timetable_details['startclass'];
									
									
									if(count($startclassData) > 0){
										foreach($startclassData as $startclass){
											$temp2 = array();
											$minutes = 0;
											$hours = 0;
											$temp2['timetable_id'] = $timetable_id;
											$temp2['date'] = $timetable_details['cdate'];
											$temp2['start_time'] = isset($startclass['start_time'])?$startclass['start_time']:'';
											$temp2['end_time'] = isset($startclass['end_time'])?$startclass['end_time']:'';
											
											if(!empty($startclass['start_time']) && !empty($startclass['end_time'])){							
												$minutes =  round(abs(strtotime($startclass['start_time']) - strtotime($startclass['end_time'])) / 60,2);
												$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
											}
											$temp2['working_hours'] = $hours . ' hours';
											$temp2['status'] = ''; //$startclass['status']
											if(!empty($timetable_details['chapter'])){
												$temp2['chapter'] = $timetable_details['chapter']['name'];    
											}
											else{
												$temp2['chapter'] = '--'; 
											}
											if(!empty($timetable_details['topic'])){
												// $temp2['topic'] = $timetable_details['topic['name'];
												$temp2['topic'] = $startclass['topic_name'];
												$temp2['topic_duration'] = 0;
											}
											else{
												$temp2['topic'] = "";
												$temp2['topic_duration'] = 0;
											}
											$temp1['topics'][] = $temp2;
                                           

										}
									}								
							}

							/*$temp2 = array();
							$temp2['timetable_id'] = 1;
							$temp2['start_time'] = "12:00 AM";
							$temp2['end_time'] = "14:00 AM";
							$minutes ="60";
							$hours = "1";
							$temp2['working_hours'] = $hours . ' hours';
							$temp2['date'] = "2022-07-05";
							$temp2['status'] = "End Class";
							$temp2['topic'] = "dfgrgr";
							$temp2['chapter'] ="ss";    
							$temp2['topic_duration'] = 2;
							$temp1['topics'][] =$temp2;*/
							$temp['subject'][] = $temp1;
							// echo "<pre>"; print_R($temp); 
						}
						$get_faculty_classreport[] = $temp;
					}
					// die;
				$data['faculty_classreport'] = $get_faculty_classreport;

				return $this->returnResponse(200, true, "Faculty Class Report", $data);

			}else{
				return $this->returnResponse(200, false, "Faculty Class Report Not Found");
			}
		}else{
		   return $this->returnResponse(200, false, "Faculty Id Not Found");
	   }

	   } catch (\Illuminate\Database\QueryException $ex) {
		  return $this->returnResponse(500, false, $ex->getMessage());
	  } catch (ModelNotFoundException $ex) {
		  return $this->returnResponse(500, false, $ex->getMessage());
	  }
	}
	
	
	public function getFacultyClassReport_rrrr(Request $request)
    {
		try {
			$input = $request->only(['faculty_id']);

			if(isset($input['faculty_id']) && !empty($input['faculty_id'])){

				$faculty_id = $input['faculty_id'];
				$date = '2022-06-23';
				$get_faculty_batchids = Timetable::with(['batch','subject'])->select('batch_id','subject_id')->where('faculty_id',$faculty_id)->whereRaw("DATE(cdate) = '$date'")->get();

				print_r($get_faculty_batchids->toArray()); die;

				$get_faculty_classreport = [];

				if(count($get_faculty_batchids) > 0){

					foreach($get_faculty_batchids as $value){
					   $temp['batch_id'] = $value->batch->id;
					   $temp['batch_name'] = $value->batch->name;    				
					   $temp['subject'] = array();
					   $get_faculty_assign_subject_from_batchrelation = Timetable::with(['subject'])->where('batch_id', $value->batch->id)->get();
						//print_r($get_faculty_assign_subject_from_batchrelation->toArray()); die;
					   foreach($get_faculty_assign_subject_from_batchrelation as $key => $batchsubject){
						  if(!empty($batchsubject->subject)){
							$temp1['subject_id'] = $batchsubject->subject->id;
							$temp1['subject_name'] = $batchsubject->subject->name;
							$temp1['topics'] = array();

							$get_faculty_today_class_report = Timetable::with(['startclass','topic','chapter'])->where('faculty_id',$faculty_id)->where('batch_id', $value->batch->id)->where('subject_id', $batchsubject->subject->id)->get();

							if(count($get_faculty_today_class_report) > 0 ){

								foreach($get_faculty_today_class_report as $timetable_data){

									if(count($timetable_data->startclass) > 0){

										foreach($timetable_data->startclass as $startclass){

											$temp2['timetable_id'] = $startclass->timetable_id;
											$temp2['start_time'] = $startclass->start_time;
											$temp2['end_time'] = $startclass->end_time;
											$minutes =  round(abs(strtotime($startclass->start_time) - strtotime($startclass->end_time)) / 60,2);
											$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
											$temp2['working_hours'] = $hours . ' hours';
											$temp2['date'] = $startclass->sc_date;
											$temp2['status'] = $startclass->status;
											if(!empty($timetable_data->chapter)){
												$temp2['chapter'] = $timetable_data->chapter->name;    
											}
											if(!empty($timetable_data->topic)){
												$temp2['topic'] = $timetable_data->topic->name;
												$temp2['topic_duration'] = $timetable_data->topic->duration;
											}
											else{
												$temp2['topic'] = "";
												$temp2['topic_duration'] = 0;
											}
											$temp1['topics'][] = $temp2;

										}

									}

								}

							}

							$temp['subject'][] = $temp1;
						}                
					}
					$get_faculty_classreport[] = $temp;
				}
			//die;

				$data['faculty_classreport'] = $get_faculty_classreport;

				return $this->returnResponse(200, true, "Faculty Class Report", $data);

			}else{
				return $this->returnResponse(200, false, "Faculty Class Report Not Found");
			}    			

				// if(count($get_faculty_timetable_report) > 0){

				// 	$get_faculty_classreport = [];

				// 	foreach($get_faculty_timetable_report as $key => $startclass){
				// 		if(!empty($startclass->startclass)){
				// 			foreach($startclass->startclass as $class_report){
				// 				$temp['subject_id'] = $startclass->subject_id;
				// 				$temp['timetable_id'] = $class_report->timetable_id;
				// 				$temp['start_time'] = $class_report->start_time;
				// 				$temp['end_time'] = $class_report->end_time;
				// 				$temp['status'] = $class_report->status;
				// 				$get_faculty_classreport[] = $temp;
				// 			}
				// 		}                
				// 	}

				// 	print_r(array_merge($get_faculty_batchids,$get_faculty_classreport));

				// 	print_r($get_faculty_classreport);                   

				// 	$data['faculty_classreport'] = $get_faculty_classreport;

				// 	return $this->returnResponse(200, true, "Faculty Class Report", $data);
				// }else{
				// 	return $this->returnResponse(200, false, "Faculty Class Report Not Found");
				// }                   

		}else{
		   return $this->returnResponse(200, false, "Faculty Id Not Found");
	   }

	   } catch (\Illuminate\Database\QueryException $ex) {
		  return $this->returnResponse(500, false, $ex->getMessage());
	  } catch (ModelNotFoundException $ex) {
		  return $this->returnResponse(500, false, $ex->getMessage());
	  }
	}

    public function getFacultyClassReport_old(Request $request)
    {
    	try {
    		$input = $request->only(['faculty_id']);

    		if(isset($input['faculty_id']) && !empty($input['faculty_id'])){

    			$faculty_id = $input['faculty_id']; 

                $get_faculty_batchids = Batchrelation::with('batch')->select('batch_id')->whereFacultyId($faculty_id)->groupBy('batch_id')->get();

                //print_r($get_faculty_batchids->toArray()); die;

                $get_faculty_classreport = [];

                if(count($get_faculty_batchids) > 0){

                    foreach($get_faculty_batchids as $value){
                       $temp['batch_id'] = $value->batch->id;
                       $temp['batch_name'] = $value->batch->name;    				
                       $temp['subject'] = array();
                       $get_faculty_assign_subject_from_batchrelation = Batchrelation::with(['subject'])->where('batch_id', $value->batch->id)->get();
                        //print_r($get_faculty_assign_subject_from_batchrelation->toArray());
                       foreach($get_faculty_assign_subject_from_batchrelation as $key => $batchsubject){
                          if(!empty($batchsubject->subject)){
                            $temp1['subject_id'] = $batchsubject->subject->id;
                            $temp1['subject_name'] = $batchsubject->subject->name;
                            $temp1['topics'] = array();

                            $get_faculty_today_class_report = Timetable::with(['startclass','topic','chapter'])->where('faculty_id',$faculty_id)->where('batch_id', $value->batch->id)->where('subject_id', $batchsubject->subject->id)->get();

                            if(count($get_faculty_today_class_report) > 0 ){

                                foreach($get_faculty_today_class_report as $timetable_data){

                                    if(count($timetable_data->startclass) > 0){

                                        foreach($timetable_data->startclass as $startclass){

                                            $temp2['timetable_id'] = $startclass->timetable_id;
                                            $temp2['start_time'] = $startclass->start_time;
                                            $temp2['end_time'] = $startclass->end_time;
                                            $minutes =  round(abs(strtotime($startclass->start_time) - strtotime($startclass->end_time)) / 60,2);
                                            $hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
                                            $temp2['working_hours'] = $hours . ' hours';
                                            $temp2['date'] = $startclass->sc_date;
                                            $temp2['status'] = $startclass->status;
                                            if(!empty($timetable_data->chapter)){
                                                $temp2['chapter'] = $timetable_data->chapter->name;    
                                            }
                                            if(!empty($timetable_data->topic)){
                                                $temp2['topic'] = $timetable_data->topic->name;
                                                $temp2['topic_duration'] = $timetable_data->topic->duration;
                                            }
                                            $temp1['topics'][] = $temp2;

                                        }

                                    }

                                }

                            }

                            $temp['subject'][] = $temp1;
                        }                
                    }
                    $get_faculty_classreport[] = $temp;
                }
            //die;

                $data['faculty_classreport'] = $get_faculty_classreport;

                return $this->returnResponse(200, true, "Faculty Class Report", $data);

            }else{
                return $this->returnResponse(200, false, "Faculty Class Report Not Found");
            }    			

    			// if(count($get_faculty_timetable_report) > 0){

    			// 	$get_faculty_classreport = [];

    			// 	foreach($get_faculty_timetable_report as $key => $startclass){
    			// 		if(!empty($startclass->startclass)){
    			// 			foreach($startclass->startclass as $class_report){
    			// 				$temp['subject_id'] = $startclass->subject_id;
    			// 				$temp['timetable_id'] = $class_report->timetable_id;
    			// 				$temp['start_time'] = $class_report->start_time;
    			// 				$temp['end_time'] = $class_report->end_time;
    			// 				$temp['status'] = $class_report->status;
    			// 				$get_faculty_classreport[] = $temp;
    			// 			}
    			// 		}                
    			// 	}

    			// 	print_r(array_merge($get_faculty_batchids,$get_faculty_classreport));

    			// 	print_r($get_faculty_classreport);                   

    			// 	$data['faculty_classreport'] = $get_faculty_classreport;

    			// 	return $this->returnResponse(200, true, "Faculty Class Report", $data);
    			// }else{
    			// 	return $this->returnResponse(200, false, "Faculty Class Report Not Found");
    			// }                   

        }else{
           return $this->returnResponse(200, false, "Faculty Id Not Found");
       }

   } catch (\Illuminate\Database\QueryException $ex) {
      return $this->returnResponse(500, false, $ex->getMessage());
  } catch (ModelNotFoundException $ex) {
      return $this->returnResponse(500, false, $ex->getMessage());
  }
}
}
