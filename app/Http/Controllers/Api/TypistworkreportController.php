<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use App\User;
use App\TypistWorkReport;
use DateTime;

class TypistworkreportController extends Controller
{
    public function add_typist_work_report(Request $request)
    {
    	try {

    		$emp_id = $request->emp_id;
    		$action = $request->action;
			if(isset($emp_id) && !empty($emp_id)){
				$user = User::where('id', $emp_id)->first();
				if(!empty($user)){
					if($user->status=='1'){
						if(isset($action) && !empty($action)){
							if($action == 'add'){
								$inputs = $request->only('emp_id','number_of_questions','ocr_panel','arrange_correction','total_page','remark','cdate');
								
								$inputs['emp_id'] = $emp_id;
								$inputs['cdate'] = date('Y-m-d');
								$save = TypistWorkReport::create($inputs);
								if($save->save()){
									return $this->returnResponse(200, true, "Typist Work Report save successfully");
								}else{
									return $this->returnResponse(200, false, "Something went wrong.");
								}
							}
							else if($action=="update"){
								if(!empty($request->id)){
									$typist_work_report = TypistWorkReport::find($request->id);
									if(!empty($typist_work_report)){
										$update_data['number_of_questions'] = $request->number_of_questions;
										$update_data['ocr_panel'] = $request->ocr_panel;
										$update_data['arrange_correction'] = $request->arrange_correction;
										$update_data['total_page'] = $request->total_page;
										$update_data['remark'] = $request->remark;
										$update = $typist_work_report->update($update_data);
										
										if($update){
											return $this->returnResponse(200, true, "Typist Work Report update successfully");
										}
										else{
											return $this->returnResponse(200, false, "Typist Work Report not update. Please try again.");
										}
									}
									else{
										return $this->returnResponse(200, false, "Id is not valid.");
									}
									
								}
								else{
									return $this->returnResponse(200, false, "Something went wrong 2.");
								}
							}
							else{
								return $this->returnResponse(200, false, "Something went wrong 1.");
							}
						}
						else{
							return $this->returnResponse(200, false, "Action is required.");
						}
					}
					else{
						return $this->returnResponse(200, false, "Employee Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "Employee Id Not Found"); 
				}
			}
			else{
				return $this->returnResponse(200, false, "Employee Id Required Found");
			}

    		

    	} catch (\Illuminate\Database\QueryException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	} catch (ModelNotFoundException $ex) {
    		return $this->returnResponse(500, false, $ex->getMessage());
    	}
    }
	
	public function get_typist_work_report(Request $request)
    {
        try{

            $emp_id = $request->emp_id;

            if(isset($emp_id) && !empty($emp_id)){
				$user_check = User::where('id', $emp_id);
				$user = $user_check->first();
				if(!empty($user)){
					if($user->status=='1'){
						
						$date_wise_attendance = TypistWorkReport::where('emp_id',$emp_id)->groupBy('cdate')->orderBy('cdate', 'desc');
						if(!empty($request->date_from) && !empty($request->date_to)){
							$date_wise_attendance->where('cdate', '>=', $request->date_from);
							$date_wise_attendance->where('cdate', '<=', $request->date_to);
						}
						else{
							$data_show_days = date('Y-m-d', strtotime('-30 days'));
							
							$date_wise_attendance->where('cdate', '>=', $data_show_days);
							$date_wise_attendance->where('cdate', '<=', date('Y-m-d'));
						}
						$date_wise_attendance = $date_wise_attendance->get();
						// echo count($date_wise_attendance); die;
						if(count($date_wise_attendance) > 0){
							$responseArray = array();
							$ii = 0;
							foreach($date_wise_attendance as $key=>$valAtt){
								$report = TypistWorkReport::where('emp_id', $valAtt->emp_id)->where('cdate', $valAtt->cdate)->where('status', 'Active')->orderBy('id', 'asc')->get();
								// echo count($report); die;
								$i = 0; 
								$report_array = array();
								if(count($report) > 0){
									foreach($report as $key2=>$value){
										if(!empty($value)){
											$reportdetails['id'] = $value->id;
											$reportdetails['emp_id'] = $value->emp_id;
											$reportdetails['number_of_questions'] = $value->number_of_questions;
											$reportdetails['ocr_panel'] = $value->ocr_panel;
											$reportdetails['arrange_correction'] = $value->arrange_correction;
											$reportdetails['total_page'] = $value->total_page;
											$reportdetails['remark'] = $value->remark;
											$reportdetails['date'] = $value->cdate;
											
											$report_array[$i] = $reportdetails;
											$i++;
										}
									}
								}
								
								if(!empty($report_array)){
									$responseArray[$ii]['date'] = $valAtt->cdate;
									$responseArray[$ii]['emp_id'] = $valAtt->emp_id;
									$responseArray[$ii]['report'] = $report_array;
									$ii++;
								}
								// print_r($responseArray); die;
							}
							
							if(!empty($responseArray)){
								// print_r($responseArray); die;
								$data['dates'] = $responseArray;
								return $this->returnResponse(200, true, "Report Details", $data);
							}
							else{
								return $this->returnResponse(200, false, "Report Not Found");
							} 
							
						}
						else{
							return $this->returnResponse(200, false, "Report Not Found");
						}
						
						
						
					}
					else{
						return $this->returnResponse(200, false, "User Not Active");
					}
				}
				else{
					return $this->returnResponse(200, false, "User Id Not Found"); 
				}				
            }else{
                return $this->returnResponse(200, false, "User Id Not Found");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
	
	public function delete_typist_work_report(Request $request)
    {
        try{
            $id = $request->id;

            if(isset($id) && !empty($id)){ 
				$find_id = TypistWorkReport::find($id);
				if(!empty($find_id)){
					// TypistWorkReport::where('id', $id)->delete();
					$updateData['status'] = "Deleted";
					TypistWorkReport::where('id', $id)->update($updateData);
					
					return $this->returnResponse(200, true, "Delete Successfully");
				}
				else{
					return $this->returnResponse(200, false, "Report Id invalid");  
				}
						 
            }else{
                return $this->returnResponse(200, false, "Report id is required");  
            }           
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
}
