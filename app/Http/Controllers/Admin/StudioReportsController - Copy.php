<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use Input;

class StudioReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id = Input::get('studio_id');
		
		$conditions = array();
		if (!empty($studio_id)){
			$conditions['id'] = $studio_id;
        }

        if (!empty($branch_id)){
			$conditions['branch_id'] = $branch_id;
        }

        if (!empty($assistant_id)){
			$conditions['assistant_id'] = $assistant_id;
        }
		
		$conditions['id'] = 2;
		// $get_studios = Studio::with(['assistant', 'branch','timetable','timetable.topic','timetable.faculty','timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where($conditions)->orderBy('id', 'desc')->get();
		
		$get_studios = Branch::with(['studio','studio.assistant', 'studio.timetable','studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->where($conditions)->orderBy('id', 'desc')->get();
		
        echo "<pre>"; print_r($get_studios); die;
        return view('admin.studio_reports.index', compact('get_studios'));
    }

   public function download_pdf() {
		$branch_id = Input::get('branch_id');
        $assistant_id = Input::get('assistant_id');
        $studio_id = Input::get('studio_id');
		
		$conditions = array();
		if (!empty($studio_id)){
			$conditions['id'] = $studio_id;
        }

        if (!empty($branch_id)){
			$conditions['branch_id'] = $branch_id;
        }

        if (!empty($assistant_id)){
			$conditions['assistant_id'] = $assistant_id;
        }
		$get_studios = Studio::with(['assistant', 'branch','timetable','timetable.topic','timetable.faculty','timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where($conditions)->orderBy('id', 'desc')->get();
	   
	   require_once base_path('vendor/tcpdf/Pdf.php');
	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('pdf');
        $pdf->SetTitle('Studio Report');
        $pdf->custom_title = 'Studio Report';
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
        $pdf->SetFont('times', '', 12);

        // ---------------------------------------------------------
        // $title = 'Studio Report';
		$html = view('admin.studio_reports.pdf_html', compact('get_studios'))->render();
        //echo $html; //exit();
        //Generate HTML table data from MySQL - end
        // add a page
        $pdf->AddPage();

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        $pdf->Output('studio_report_' . md5(time()) . '.pdf', 'D');
		
	   die('ddd');
   }

}
