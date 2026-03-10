<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use App\Batch;
use Input;
use Dompdf\Dompdf;
use Options;
use Auth;
use DB;
use App\Exports\BatchReportExport;
use Excel;


class BatchReportsController extends Controller
{
	public function get_locationwise_branch(Request $request){

		$b_location = $request->b_location;
		
		$get_branches = DB::table('branches')
			->where('status', 1)
			->where('is_deleted', '0')
			->where('branch_location', $b_location)
			->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')
			->get();			
		$res = "";
        if (!empty($get_branches)) {  
				$res .= "<option value=''> Select Any </option>";
				foreach ($get_branches as $key => $value) {
					$res .= "<option value='". $value->id ."' >" . $value->name ."</option>";
				}
			} 
			else {
				$res .= "<option value=''> Not Found </option>";
			}
		
		echo $res; exit;
	}
	
	public function assistant_report()
    { 
		
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
        // $tdate        = Input::get('tdate');
		$type        = Input::get('type');
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
				}
			}
		}
		
		$conditions   = array();
		$whereCond    = '1=1 ';
		if (!empty($branch_id)){
			if(!empty($branch_id[0])){
				//$conditions['id'] = $branch_id;
				$brnc_id   = implode(",", $branch_id); 
				$whereCond = " id IN ($brnc_id) ";
			}
        }
		
		$get_studios = Branch::with(['studio'=> function ($q) use ($studio_id, $assistant_id,$type)
            {
                if (!empty($studio_id))
                {
                    $q->where('id', $studio_id);
                }
				
				if (!empty($assistant_id))
                {
                    $q->where('assistant_id', $assistant_id);
                }
				
				if (!empty($type))
                {
                    $q->where('type', $type);
                }
				$q->orderBy('order_no', 'asc');
				
            },'studio.assistant', 
			'studio.timetable' => function ($q) use ($fdate)
            {
                if(!empty($fdate)){
					$q->where('cdate', '=', $fdate);
					// $q->where('cdate', '<=', $tdate);
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
				}
            },'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter','studio.timetable.assistant'])->whereRaw($whereCond);
			
		    if (!empty($fdate)){	
				$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
						$q->where('cdate', '=', $fdate);
						// $q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
				});
		    }
			
		$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		
		if(!empty(Auth::user()->id)){
			return view('studiomanager.batch_reports.without_login_assistant_report', compact('get_studios','fdate'));
		}
		else{
			return view('studiomanager.batch_reports.without_login_assistant_report', compact('get_studios','fdate'));
		}
        
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
		
        $branch_location    = Input::get('branch_location');
        $branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_id    = Input::get('batch_id');
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
					$tdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
					$tdate = date('Y-m-d');
				}
			}
		}

		$get_batches=array();
		$search    = Input::get('search');
		if(!empty($search)){
			$conditions   = array();
			$whereCond    = '1=1 ';
			
			$get_batches = Batch::with(['batch_timetables'=> function ($q) use ($fdate, $tdate)
			{
				if (!empty($fdate) && !empty($tdate))
				{
					$q->where('cdate', '>=', $fdate);
					$q->where('cdate', '<=', $tdate);
					$q->where('is_deleted', '=', '0');
					$q->where('is_publish', '1');
					$q->where('is_cancel', 0);
					$q->orderBy('from_time');
					$q->groupBy('id');
				}
				
			}
			,'batch_timetables.studio' => function ($q) use ($studio_id, $assistant_id,$type)
			{
				if (!empty($studio_id))
				{
					$q->where('id', $studio_id);
				}
				
				if (!empty($assistant_id))
				{
					$q->where('assistant_id', $assistant_id);
				}
				
				if (!empty($type))
				{
					$q->where('type', $type);
				}
				$q->whereNotNull('branch_id');
				$q->orderBy('order_no', 'asc');
				
			},'batch_timetables.studio.branch'=> function ($q) use ($branch_id,$branch_location)
			{
				if (!empty($branch_id))
				{
					$q->whereIn('id', $branch_id);
				}

				if(!empty($branch_location))
				{
					$q->where('branch_location', $branch_location);
				}
				
			},'batch_timetables.studio.assistant','batch_timetables.topic','batch_timetables.faculty','batch_timetables.course','batch_timetables.subject','batch_timetables.chapter','batch_timetables.assistant']);
				
			if (!empty($fdate) && !empty($tdate)){	
				$get_batches->WhereHas('batch_timetables', function ($q) use ($fdate, $tdate) {
						$q->where('cdate', '>=', $fdate);
						$q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
						$q->where('is_publish', '1');
						$q->where('is_cancel', 0);
						$q->orderBy('from_time');
						$q->groupBy('id');
				});
			}
			else{
				$get_batches->WhereHas('batch_timetables', function ($q) {
						
						$q->where('is_deleted', '=', '0');
						$q->where('is_publish', '1');
						$q->where('is_cancel', 0);
						$q->orderBy('from_time');
						$q->groupBy('id');
				});
			}

			if (!empty($branch_id)){	
				$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_id) {
					$q->whereIn('id', $branch_id);
				});
			}
			
			if (!empty($branch_location)){	
				$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_location) {
					$q->where('branch_location', $branch_location);
				});
			}

			if (!empty($studio_id) || !empty($assistant_id) || !empty($type)){	
				$get_batches->WhereHas('batch_timetables.studio', function ($q) use ($studio_id, $assistant_id,$type) {
					if (!empty($studio_id))
					{
						$q->where('id', $studio_id);
					}
					
					if (!empty($assistant_id))
					{
						$q->where('assistant_id', $assistant_id);
					}
					
					if (!empty($type))
					{
						$q->where('type', $type);
					}
					
					$q->orderBy('order_no', 'asc');

				});
			}
			
			if(!empty($batch_id)){
				$get_batches->where('id',$batch_id)->get();
			}
			//$get_batches = $get_batches->skip(0)->take(5)->get();
			$get_batches = $get_batches->get();
			
			//dd($get_batches);
			//echo '<pre>'; print_r($get_batches);die;
		}
		
		return view('studiomanager.batch_reports.index', compact('get_batches','fdate','tdate'));
		
    }
	
	public function download_excel()
    {   
		$branch_location    = Input::get('branch_location');
		$branch_id    = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_id    = Input::get('batch_id');
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
					$tdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
					$tdate = date('Y-m-d');
				}
			}
		}
		
		$conditions   = array();
		$whereCond    = '1=1 ';
		// if (!empty($branch_id)){
		// 	if(!empty($branch_id[0])){
		// 		//$conditions['id'] = $branch_id;
		// 		$brnc_id   = implode(",", $branch_id); 
		// 		$whereCond = " id IN ($brnc_id) ";
		// 	}
        // }
		
		// echo "<pre>"; print_r($whereCond); die;
		// $conditions['id'] = 2;
		// $get_studios = Studio::with(['assistant', 'branch','timetable','timetable.topic','timetable.faculty','timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where($conditions)->orderBy('id', 'desc')->get();
		
		
		
		$get_batches = Batch::with(['batch_timetables'=> function ($q) use ($fdate,$tdate)
		{
			if (!empty($fdate) && !empty($tdate))
			{
				$q->where('cdate', '>=', $fdate);
				$q->where('cdate', '<=', $tdate);
				$q->where('is_deleted', '=', '0');
				$q->orderBy('from_time');
				$q->groupBy('id');
			}
			
		}
		,'batch_timetables.studio' => function ($q) use ($studio_id, $assistant_id,$type)
		{
			if (!empty($studio_id))
			{
				$q->where('id', $studio_id);
			}
			
			if (!empty($assistant_id))
			{
				$q->where('assistant_id', $assistant_id);
			}
			
			if (!empty($type))
			{
				$q->where('type', $type);
			}
			$q->whereNotNull('branch_id');
			$q->orderBy('order_no', 'asc');
			
		},'batch_timetables.studio.branch'=> function ($q) use ($branch_id,$branch_location)
		{
			if (!empty($branch_id))
			{
				$q->whereIn('id', $branch_id);
			}
			
			
			if(!empty($branch_location))
			{
				$q->where('branch_location', $branch_location);
			}
			
		},'batch_timetables.studio.assistant','batch_timetables.topic','batch_timetables.faculty','batch_timetables.course','batch_timetables.subject','batch_timetables.chapter','batch_timetables.assistant']);
			
		if (!empty($fdate) && !empty($tdate)){	
			$get_batches->WhereHas('batch_timetables', function ($q) use ($fdate,$tdate) {
					$q->where('cdate', '>=', $fdate);
					$q->where('cdate', '<=', $tdate);
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
					$q->groupBy('id');
			});
		}
		else{
			$get_batches->WhereHas('batch_timetables', function ($q) {
					
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
					$q->groupBy('id');
			});
		}

		if (!empty($branch_id)){	
			$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_id) {
				$q->whereIn('id', $branch_id);
			});
		}
		
		if (!empty($branch_location)){	
			$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_location) {
				$q->where('branch_location', $branch_location);
			});
		}

		if (!empty($studio_id) || !empty($assistant_id) || !empty($type)){	
			$get_batches->WhereHas('batch_timetables.studio', function ($q) use ($studio_id, $assistant_id,$type) {
				if (!empty($studio_id))
				{
					$q->where('id', $studio_id);
				}
				
				if (!empty($assistant_id))
				{
					$q->where('assistant_id', $assistant_id);
				}
				
				if (!empty($type))
				{
					$q->where('type', $type);
				}
				
				$q->orderBy('order_no', 'asc');

			});
		}
		
		if(!empty($batch_id)){
			$get_batches->where('id',$batch_id)->get();
		}
		//$get_batches = $get_batches->skip(0)->take(5)->get();
		$get_batches = $get_batches->get();
		
        if(count($get_batches) > 0){  
            return Excel::download(new BatchReportExport($get_batches), 'BatchReportData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    } 

   public function download_pdf() {
		$branch_location    = Input::get('branch_location');
		$branch_id    = Input::get('branch_id');
		$assistant_id = Input::get('assistant_id');
		$studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_id    = Input::get('batch_id');
	   
		if(!empty($branch_id)){
			$branch_id = explode(",", $branch_id);
		}
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
					$tdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
					$tdate = date('Y-m-d');
				}
			}
		}
		
		$conditions   = array();
		
		$get_batches = Batch::with(['batch_timetables'=> function ($q) use ($fdate,$tdate)
		{
			if (!empty($fdate) && !empty($tdate))
			{
				$q->where('cdate', '>=', $fdate);
				$q->where('cdate', '<=', $tdate);
				$q->where('is_deleted', '=', '0');
				$q->orderBy('from_time');
				$q->groupBy('id');
			}
			
		}
		,'batch_timetables.studio' => function ($q) use ($studio_id, $assistant_id,$type)
		{
			if (!empty($studio_id))
			{
				$q->where('id', $studio_id);
			}
			
			if (!empty($assistant_id))
			{
				$q->where('assistant_id', $assistant_id);
			}
			
			if (!empty($type))
			{
				$q->where('type', $type);
			}
			$q->whereNotNull('branch_id');
			$q->orderBy('order_no', 'asc');
			
		},'batch_timetables.studio.branch'=> function ($q) use ($branch_id,$branch_location)
		{
			if (!empty($branch_id))
			{
				$q->whereIn('id', $branch_id);
			}
			
			if(!empty($branch_location))
			{
				$q->where('branch_location', $branch_location);
			}
			
		},'batch_timetables.studio.assistant','batch_timetables.topic','batch_timetables.faculty','batch_timetables.course','batch_timetables.subject','batch_timetables.chapter','batch_timetables.assistant']);
			
		if (!empty($fdate) && !empty($tdate)){	
			$get_batches->WhereHas('batch_timetables', function ($q) use ($fdate,$tdate) {
					$q->where('cdate', '>=', $fdate);
					$q->where('cdate', '<=', $tdate);
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
					$q->groupBy('id');
			});
		}
		else{
			$get_batches->WhereHas('batch_timetables', function ($q) {
					
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
					$q->groupBy('id');
			});
		}
	
		if (!empty($branch_id)){	
			$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_id) {
				$q->whereIn('id', $branch_id);
			});
		}
		
		if (!empty($branch_location)){	
			$get_batches->WhereHas('batch_timetables.studio.branch', function ($q) use ($branch_location) {
				$q->where('branch_location', $branch_location);
			});
		}

		if (!empty($studio_id) || !empty($assistant_id) || !empty($type)){	
			$get_batches->WhereHas('batch_timetables.studio', function ($q) use ($studio_id, $assistant_id,$type) {
				if (!empty($studio_id))
				{
					$q->where('id', $studio_id);
				}
				
				if (!empty($assistant_id))
				{
					$q->where('assistant_id', $assistant_id);
				}
				
				if (!empty($type))
				{
					$q->where('type', $type);
				}
				
				$q->orderBy('order_no', 'asc');

			});
		}
		
		if(!empty($batch_id)){
			$get_batches->where('id',$batch_id)->get();
		}
		//$get_batches = $get_batches->skip(0)->take(5)->get();
		$get_batches = $get_batches->get();
		//echo '<pre>'; print_r($get_batches);die;
		return view('studiomanager.batch_reports.pdf_html', compact('get_batches','fdate','tdate'));
	   
		require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	    
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Batch Report');
        $pdf->custom_title = 'Batch Report';
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
		$html = view('studiomanager.batch_reports.pdf_html', compact('get_batches'))->render();
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
        $pdf->Output('Timetable_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
   }
   
   public function batchReportsShiftwise()
    { 
        $branch_id    = Input::get('branch_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_id    = Input::get('batch_id');
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
					$tdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
					$tdate = date('Y-m-d');
				}
			}
		}
		
		$get_batches=array();
		$search    = Input::get('search');
		if(!empty($search)){
		 $get_batches = Batch::select('id','name')->where('status','1')->where('is_deleted','0')->get();
		}
		
		//dd($get_batches);
		//echo '<pre>'; print_r($get_batches);die;
		if(!empty(Auth::user()->id)){
			return view('studiomanager.batch_reports.batch_reports_shift_wise', compact('get_batches','fdate','tdate','branch_id','type','batch_id'));
		}
        
    }

	public function download_pdf_shiftwise(){
		$branch_id    = Input::get('branch_id');
		$fdate        = Input::get('fdate');
		$tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$batch_ids    = Input::get('batch_id');
		
		if(empty($fdate)){
			if(!empty(Auth::user()->id)){
				$fdate = date('Y-m-d');
				$tdate = date('Y-m-d');
			}
			else{
				if(date('H') > 17){ // 17 == 5PM
					$fdate = date('Y-m-d',strtotime('+1 day'));
					$tdate = date('Y-m-d',strtotime('+1 day'));
				}
				else{
					$fdate = date('Y-m-d');
					$tdate = date('Y-m-d');
				}
			}
		}
		
		$get_batches = Batch::select('id','name')->where('status','1')->where('is_deleted','0')->get();
		//echo '<pre>'; print_r($get_batches);die;
		return view('studiomanager.batch_reports.shiftwise_pdf_html', compact('get_batches','fdate','tdate','branch_id','type','batch_ids'));
	   
		require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	    
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Batch Report');
        $pdf->custom_title = 'Batch Report';
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
		$html = view('studiomanager.batch_reports.pdf_html', compact('get_batches'))->render();
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
        $pdf->Output('Timetable_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
	}

	public function studio_availability(){
       $get_studios=array();

       $cdate    = Input::get('cdate');
       
       if(!empty($cdate)){
       $branch_location    = Input::get('branch_location');
       $branch_id    = Input::get('branch_id');

        $whereCond= '1=1 ';
		if(!empty($branch_id)){
			if(!empty($branch_id[0])){
				$brnc_id   = implode(",",$branch_id); 
				$whereCond.= " AND id IN ($brnc_id) ";
			}
        }

        if(!empty($branch_location)){
          $whereCond.=" AND branch_location='$branch_location'";
        }

        if(empty($cdate)){
          $cdate=date("Y-m-d");
        }else{
          $cdate=date("Y-m-d",strtotime($cdate));
        }


       

        $get_studios = Branch::with(['studio'=> function ($q)
            {
                $q->where('status',1);
                $q->where('is_deleted','0');
                $q->where('type','Offline');
				$q->orderBy('branch_id', 'asc');
				
            },
			'studio.timetable' => function ($q) use ($cdate){
                if(!empty($cdate)){
					$q->where('cdate', '=',$cdate);
					$q->where('time_table_parent_id', '=', 0);
					$q->where('is_deleted', '=', '0');
					$q->orderBy('from_time');
				}
            },'studio.timetable.batch'])->whereRaw($whereCond);
			
		    /*if (!empty($fdate)){	
				$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
						$q->where('cdate', '=', $fdate);
						// $q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
						$q->where('is_publish', '=', '1');
				});
		    }*/

		//$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		$get_studios = $get_studios->get();

	    }


        //echo "<pre>"; print_r($get_studios); die;
	    return view('studiomanager.batch_reports.studio_availability',compact('get_studios','cdate'));	
	}


	//Batch Hours Reports
	public function batch_hours_reports()
    { 
		$batch_id     = Input::get('batch_id');
		
		$batch_details = array();
		if(!empty($batch_id)){
			$batch_details = DB::table('batch')->select('*')->where('id',$batch_id)->first();
		}
		
		return view('studiomanager.batch_reports.batch_hours_report', compact('batch_id','batch_details'));
    }
	
	
	public function faculty_batch_reports()
    { 
		$faculty_id     = Input::get('faculty_id');
		$from_date 		= Input::get('from_date')??date("Y-m-d");
		$to_date 		= Input::get('to_date')??date("Y-m-d");
		
		return view('studiomanager.batch_reports.faculty_batch_report', compact('faculty_id','from_date','to_date'));
    }
	
	
	public function batch_hours_reports_new()
    { 
		$batch_id     = Input::get('batch_id');
		$batch_details = array();
		if(!empty($batch_id)){
			$batch_details = DB::table('batch')->select('*')->where('id',$batch_id)->first();
		}
		
		return view('studiomanager.batch_reports.batch_hours_report_new', compact('batch_id','batch_details'));
    }
}
