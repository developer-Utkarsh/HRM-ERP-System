<?php 
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Timetable;
use App\Reschedule;
use App\Swap;
use App\CancelClass;
use Input;
use App\FacultyRelation;
use App\ApiNotification;
use App\Users_pending;
use App\Userdetails_pending;
use App\FacultyRelations_pending;
use App\Studio;
use App\Userbranches;

class AppVersion extends Controller
{
	
	public function getVersion()
    {
        try{	
            $data = array();
            $data['version'] = "26";	
            $data['url'] = "https://play.google.com/store/apps/details?id=com.utkarsh.employee";		
			return $this->returnResponse(200, true, "App Version",$data);         
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        } catch (ModelNotFoundException $ex) {
            return $this->returnResponse(500, false, $ex->getMessage());
        }
    }
}
?>

