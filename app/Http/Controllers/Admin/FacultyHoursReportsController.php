<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Timetable;
use Input;
use Auth;
use App\User;
use DB;
use App\Exports\FacultyHoursExport;
use App\Exports\FacultyAgreementHoursReportData;
use Excel;

class FacultyHoursReportsController extends Controller
{
    public function list_reports(Request $request)
    {
        $faculty_id     = Input::get('faculty_id');
		if(!empty(Auth::user()->id)){
			return redirect("admin/faculty-hours-reports?faculty_id=$faculty_id");
		}
		return view('admin.faculty_hours_reports.list_reports', compact('faculty_id'));
		
    }
	
    public function index(Request $request)
    {
        
        $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$online_class_type   = Input::get('online_class_type');
		
		$whereCond  = "  users.status = '1' AND users.is_deleted ='0' AND users.role_id= '2'";
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		else if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		else if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		else{
			if(empty(Auth::user()->id)){
				$selectFromDate = date('Y-m-01');
				$selectToDate = date('Y-m-d');
			}
		}
		
		
		if(!empty(Auth::user()->id)){
			if(!empty($faculty_id) && is_array($faculty_id) > 0){ 
				$whereCond .= " AND users.id IN('".implode("','",$faculty_id)."')";
			}
			else{
				$whereCond .=" AND 1=2";
			}
		}
		else{
			if(!empty($faculty_id)){
				$whereCond .= ' AND users.id = "'.$faculty_id.'"';
			}
		}
		
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($selectFromDate) || !empty($selectToDate)){
			$get_faculty = DB::table('users')
							->select('users.id','users.name','users.mobile','users.committed_hours','users.agreement','departments.name as department_name','branches.name as bname')
							->leftJoin('departments', 'departments.id', '=', 'users.department_type')
							->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
							->leftJoin('branches', 'branches.id', '=', 'userdetails.branch_id')
							->whereRaw($whereCond)
							->orderBy('name')
							->get();				
								
								
			
		}
        
		if(!empty(Auth::user()->id)){
			$reportNotArr =  array(901,1056,5409); 
			if(in_array(Auth::user()->id, $reportNotArr)){ 
			   return view('admin.faculty_hours_reports.index', compact('get_faculty','selectFromDate','selectToDate','branch_location','online_class_type'));
			}else{
				die('No Access');
			}
		}
		else{
			//without_login_faculty_report
			return view('admin.faculty_hours_reports.without_login_index', compact('get_faculty','selectFromDate','selectToDate','branch_location','online_class_type'));
		}
		
    }

   public function download_pdf() { 
         $faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$online_class_type   = Input::get('online_class_type');
		
		$whereCond  = "  status = '1' AND is_deleted ='0' AND role_id= '2'";
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		
		
		if(!empty($faculty_id)){ 
			$whereCond .= " AND users.id IN(".$faculty_id.")";
		}
			
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($selectFromDate) || !empty($selectToDate)){
			
								
			$get_faculty = DB::table('users')
							->select('users.id','users.name','users.mobile','users.committed_hours')
							->whereRaw($whereCond)
							->orderBy('name')
							->get();		
		}	
	    
		require_once base_path('vendor/tcpdf/Pdf.php'); 
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Faculty Hours Report');
        $pdf->custom_title = 'Faculty Hours Report';
        //$pdf->SetSubject('Report generated using Codeigniter and TCPDF');
        //$pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('freesans', '', 12);

        // ---------------------------------------------------------
        // $title = 'Studio Report';
		$html = view('admin.faculty_hours_reports.pdf_html', compact('get_faculty','selectFromDate','selectToDate','branch_location','online_class_type'))->render();
        //echo $html; //exit();
        //Generate HTML table data from MySQL - end
        // add a page
        $pdf->AddPage();
		// echo $html; die;
		// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        $pdf->Output('faculty_hours_report_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
   }
   
   public function download_excel()
    {   
		$faculty_id     = Input::get('faculty_id');		
		$selectFromDate = Input::get('fdate');
        $selectToDate   = Input::get('tdate');
		$branch_location   = Input::get('branch_location');
		$online_class_type   = Input::get('online_class_type');
		
		$whereCond  = "  status = '1' AND is_deleted ='0' AND role_id= '2'";
		
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		
		
		
		if(!empty($faculty_id)){ 
			$whereCond .= " AND users.id IN(".$faculty_id.")";
		}
			
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($selectFromDate) || !empty($selectToDate)){					
			$get_faculty = DB::table('users')
								->select('users.id','users.name','users.mobile','users.committed_hours')
								->whereRaw($whereCond)
								->orderBy('name')
								->get();	
			
			if(count($get_faculty) > 0){  
				return Excel::download(new FacultyHoursExport($get_faculty,$selectFromDate,$selectToDate,$branch_location,$online_class_type), 'FacultyHoursReportData.xlsx'); 

			} else{
				return redirect()->back()->with('error', 'Something Went Wrong!');
			}
		} 
	}
	
	public function agreement_hours(Request $request)
    {
        $faculty_id     	= 	Input::get('faculty_id');		
		$year_wise_month    =   Input::get('fmonth');
		$branch_location    = 	Input::get('branch_location');
		$month_year_to_days = array();
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		}
		
		$whereCond  = "  status = '1' AND is_deleted ='0' AND role_id= '2'";
		$selectFromDate = "";
		$selectToDate = "";
		$online_class_type = "";
		/*
		if(empty($selectFromDate) && !empty($selectToDate)){
			return back()->with('error', 'Please Select From Time');
		}
		if(empty($selectToDate) && !empty($selectFromDate)){
			return back()->with('error', 'Please Select To Time');
		}
		
		if(!empty($selectFromDate) && !empty($selectToDate)){
			if($selectFromDate > $selectToDate){
				return back()->with('error', 'From Time Always Greater Than To Time');
			}
        }
		*/
		
		if(!empty($month_year_to_days)){
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
		}else{
			$mt = date('m');
			$yr = date("Y");
		}
		
		
		
		if(!empty($faculty_id)){ 
			$whereCond .= " AND users.id IN('".implode("','",$faculty_id)."')";
		}
				
		
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($branch_location)){
			
								
			$get_faculty = DB::table('users')
							->select('users.id','users.name','users.mobile','users.committed_hours')
							->whereRaw($whereCond)
							->orderBy('name')
							->get();				
								
			//echo "<pre>"; print_r($get_faculty); die;					
			
		}
        
        return view('admin.faculty_hours_reports.agreement_hours', compact('get_faculty','selectFromDate','selectToDate','branch_location','mt','yr'));
    }

	
	public function agreement_download_excel()
    {   
		$faculty_id     	= Input::get('faculty_id');		
		$branch_location    	= Input::get('branch_location');		
		$year_wise_month    = Input::get('year_wise_month');
		$month_year_to_days = array();
		if(!empty($year_wise_month)){
			$month_year_to_days = explode('-',$year_wise_month);
		}
		
		$whereCond  = "  status = '1' AND is_deleted ='0' AND role_id= '2'";
		
		if(!empty($faculty_id)){ 
			$whereCond .= " AND users.id IN(".$faculty_id.")";
		}
		
		if(!empty($month_year_to_days)){
			$yr = $month_year_to_days[0];
			$mt = $month_year_to_days[1];
		}else{
			$mt = date('m');
			$yr = date("Y");
		}
			
		$get_faculty = array();
		if(!empty($faculty_id) || !empty($branch_location)){					
			$get_faculty = DB::table('users')
								->select('users.id','users.name','users.mobile','users.committed_hours')
								->whereRaw($whereCond)
								->orderBy('name')
								->get();	
			
			if(count($get_faculty) > 0){  
				return Excel::download(new FacultyAgreementHoursReportData($get_faculty,$mt,$yr,$branch_location), 'FacultyAgreementHoursReportData.xlsx'); 

			} else{
				return redirect()->back()->with('error', 'Something Went Wrong!');
			}
		} 
	}

}
