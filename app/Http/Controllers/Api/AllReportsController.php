<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Attendance;   
use App\Leave; 
use App\LeaveDetail;
use App\ApiNotification;
use App\Mail\Facultylink;
use Excel;
use App\Holiday;
use App\AttendanceNew; 
use DB; 
use DateTime;

class AllReportsController extends Controller
{   

	public function all_reports_link(){
        try {
			$temp['name'] = "Faculty Report";
			$temp['is_show'] = 1;
			$temp['link'] = "faculty-reports-two?faculty_id=903";
			$all_data[] = $temp;
			
			$temp['name'] = "Faculty Hours Report";
			$temp['is_show'] = 1;
			$temp['link'] = "faculty-hours-reports?faculty_id=903";
			$all_data[] = $temp;
			
			$temp['name'] = " BATCH WISE HOURS PLAN";
			$temp['is_show'] = 0;
			$temp['link'] = "batch-wise-hour-plan?faculty_id=903";
			$all_data[] = $temp;
			
			$data['links'] = $all_data;
			return $this->returnResponse(200, true, "Reports Link", $data);

        } catch (\Illuminate\Database\QueryException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    } catch (ModelNotFoundException $ex) {
	        return $this->returnResponse(500, false, $ex->getMessage());
	    }        
	}
 }
