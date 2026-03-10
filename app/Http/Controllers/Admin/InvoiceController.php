<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Invoice;
use App\CreditNote;
use Input;
use DB;
use Excel;
use App\Exports\CourseBySubjectExport;
use Dompdf\Dompdf;
use Options;
use DataTables;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /* $fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
        $invoice_report = Invoice::orderBy('id', 'asc');
		
		if (!empty($fdate) && !empty($tdate)) {
            $invoice_report->where('c_date', '>=', $fdate)->where('c_date', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $invoice_report->where('c_date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $invoice_report->where('c_date', '<=', $tdate);
        }

        

		$invoice_report = $invoice_report->get();*/
        $start_message_id = Invoice::where('is_send_mail', 0)->first();
		return view('admin.invoice.index', compact('invoice_report','start_message_id'));		
    }
	
	public function credit_note(){
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
        $invoice_report = CreditNote::orderBy('id', 'asc');
		
		if (!empty($fdate) && !empty($tdate)) {
            $invoice_report->where('c_date', '>=', $fdate)->where('c_date', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $invoice_report->where('c_date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $invoice_report->where('c_date', '<=', $tdate);
        }

        

		$invoice_report = $invoice_report->get();
        $start_message_id = CreditNote::where('is_send_mail', 0)->first();
		return view('admin.invoice.credit_note', compact('invoice_report','start_message_id'));
	}
	
	public function store_credit_message(Request $request){
		$from_id = $request->fid;
		$to_id   = $request->tid;
		
		if(!empty($from_id) && !empty($from_id)){
			//$get_message_date = Invoice::where('is_send_mail', 0)->skip(0)->take($to_id)->get();
			$get_message_date = CreditNote::where('is_send_mail', 0)->whereBetween('id', [$from_id, $to_id])->get();
			if(count($get_message_date) > 0){
				foreach($get_message_date as $get_message_date_value){
					    $url = url('/')."/download-invoice-credit";  
					    $msg = $url.'/'.$get_message_date_value->slug;
						$msg = $this->get_tiny_url($msg);
						$mbl = $get_message_date_value->contact;
						$message_content = urlencode($msg);
					    
					$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=ne3008lah&mobilenumber={$mbl}&message={$msg}&sid=UTKRSH&mtype=N&DR=Y";
					
					$ch=curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_exec($ch);
					curl_close($ch);
							
					DB::table('credit_note')->where('id', $get_message_date_value->id)->update([ 'is_send_mail' => 1]);
					
				}
				return back()->with('success', 'Message successfully send.');
			}
			
		}
	}
	
	public function credit_multi_download_pdf(Request $request){
		$get_invoice = [];
		if(count($request->id) > 0){
			foreach($request->id as $invoice_row){
				$invoice_data     = CreditNote::where('id',$invoice_row)->first();
				$get_invoice[] = $invoice_data;
				
				DB::table('credit_note')->where('id', $invoice_row)->update([ 'is_download' => 1]);
			}
			//echo '<pre>'; print_r($get_invoice);die;
			
			require_once base_path('vendor/dompdf/autoload.inc.php'); 
			$html = view('admin.invoice.credit_pdf_html', compact('get_invoice'))->render();
			
			// echo $html; die; 
			$options = new \Dompdf\Options();
			$options->setIsRemoteEnabled(true);
			$options->set('dpi', 100);
			// $options->SetFont('freesans', '', 12); // freesans // times
			$dompdf = new Dompdf($options);
			// $html = 'Insert full HTML content';
			$dompdf->loadHtml($html);
			// $dompdf->setPaper('A4', 'landscape');
			$dompdf->render();
			// $dompdf->stream("codexworld",array("Attachment"=>0));
			$dompdf->stream(md5(time()).".pdf");

		   die('ddd');
		}
	}
	
	public function credit_download_pdf($invoice_id){
		$conditions = array();
           
		if (!empty($invoice_id)){

			$conditions['id'] = $invoice_id;

        }
		
		$get_invoice = CreditNote::where('id',$conditions)->get();
		DB::table('credit_note')->where('id', $conditions)->update([ 'is_download' => 1]);
		
		require_once base_path('vendor/dompdf/autoload.inc.php'); 
		define("DOMPDF_UNICODE_ENABLED", true);
		define("DOMPDF_ENABLE_REMOTE", false);
		define("DOMPDF_ENABLE_CSS_FLOAT", false);
		define ("DOMPDF_ENABLE_FONTSUBSETTING",false);
		define("DOMPDF_ENABLE_HTML5PARSER",false);

		define("DEBUGCSS",false);
		define("DEBUG_LAYOUT",false);
		define("DEBUG_LAYOUT_LINES",true);
		define("DEBUG_LAYOUT_BLOCKS",true);
		define("DEBUG_LAYOUT_INLINE",true);	
		define("DEBUG_LAYOUT_PADDINGBOX	",true);
		$html = view('admin.invoice.credit_pdf_html', compact('get_invoice'))->render();
		
		// echo $html; die; 
		$options = new \Dompdf\Options();
		$options->setIsRemoteEnabled(true);
		$options->set('dpi', 100);
		$dompdf = new Dompdf($options); // array('enable_remote' => true) // $options
		
		// $options1 = $dompdf->getOptions();
		// $options1->setDefaultFont('DejaVu Sans');
		// $dompdf->setOptions($options1);
		$dompdf->loadHtml($html);
		// $dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->set_option("fontDir", url('/laravel/public/Hind/Hind-Regular.ttf')).
		// $dompdf->set_option('defaultFont', 'freesans');
		// $dompdf->stream("codexworld",array("Attachment"=>0));
		$dompdf->stream(md5(time()).".pdf");
		
		die('ddd');
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.invoice.add');
    }
	
	public function send_message(){ 
        $invoice_report = Invoice::orderBy('id', 'asc')->get();
		
		$start_message_id = Invoice::where('is_send_mail', 0)->first();
		//echo '<pre>'; print_r($start_message_id->id);die;
		return view('admin.invoice.message', compact('invoice_report','start_message_id'));
	}
	
	public function store_message(Request $request){ 
		$from_id = $request->fid;
		$to_id   = $request->tid;
		
		if(!empty($from_id) && !empty($from_id)){
			//$get_message_date = Invoice::where('is_send_mail', 0)->skip(0)->take($to_id)->get();
			$get_message_date = Invoice::where('is_send_mail', 0)->whereBetween('id', [$from_id, $to_id])->get();
			if(count($get_message_date) > 0){
				foreach($get_message_date as $get_message_date_value){
					    $url = url('/')."/download-invoice";  
					    $msg = $url.'/'.$get_message_date_value->slug;
						$msg = $this->get_tiny_url($msg);
						$mbl = $get_message_date_value->contact;
						$message_content = urlencode($msg);
					    
					$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=ne3008lah&mobilenumber={$mbl}&message={$msg}&sid=UTKRSH&mtype=N&DR=Y";
					
					$ch=curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_exec($ch);
					curl_close($ch);
							
					DB::table('invoice')->where('id', $get_message_date_value->id)->update([ 'is_send_mail' => 1]);
					
				}
				return back()->with('success', 'Message successfully send.');
			}
			
		}
		 
		
	}
	
	public function download_pdf($invoice_id) {
		
		$conditions = array();

		if (!empty($invoice_id)){

			$conditions['id'] = $invoice_id;

        }
		
		$get_invoice = Invoice::where('id',$conditions)->get();
		DB::table('invoice')->where('id', $conditions)->update([ 'is_download' => 1]);
		
		require_once base_path('vendor/dompdf/autoload.inc.php'); 
		define("DOMPDF_UNICODE_ENABLED", true);
		define("DOMPDF_ENABLE_REMOTE", false);
		define("DOMPDF_ENABLE_CSS_FLOAT", false);
		define ("DOMPDF_ENABLE_FONTSUBSETTING",false);
		define("DOMPDF_ENABLE_HTML5PARSER",false);

		define("DEBUGCSS",false);
		define("DEBUG_LAYOUT",false);
		define("DEBUG_LAYOUT_LINES",true);
		define("DEBUG_LAYOUT_BLOCKS",true);
		define("DEBUG_LAYOUT_INLINE",true);	
		define("DEBUG_LAYOUT_PADDINGBOX	",true);
		$html = view('admin.invoice.pdf_html', compact('get_invoice'))->render();
		
		// echo $html; die; 
		$options = new \Dompdf\Options();
		$options->setIsRemoteEnabled(true);
		$options->set('dpi', 100);
		$dompdf = new Dompdf($options); // array('enable_remote' => true) // $options
		
		// $options1 = $dompdf->getOptions();
		// $options1->setDefaultFont('DejaVu Sans');
		// $dompdf->setOptions($options1);
		$dompdf->loadHtml($html);
		// $dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->set_option("fontDir", url('/laravel/public/Hind/Hind-Regular.ttf')).
		// $dompdf->set_option('defaultFont', 'freesans');
		// $dompdf->stream("codexworld",array("Attachment"=>0));
		//$dompdf->stream(md5(time()).'sau'.".pdf");
		$dompdf->stream($conditions['id'].".pdf");
		
		die('ddd');

	
	}
	
	
	public function multi_download_pdf(Request $request){
		$get_invoice = [];
		if(count($request->id) > 0){
			foreach($request->id as $invoice_row){
				$invoice_data     = Invoice::where('id',$invoice_row)->first();
				$get_invoice[] = array('invoice_no'=>$invoice_data->invoice_no,
								'c_date'=>$invoice_data->c_date,
								'order_number'=>$invoice_data->order_number,
								'date'=>$invoice_data->date,
								'payment_id'=>$invoice_data->payment_id,
								'email'=>$invoice_data->email,
								'contact'=>$invoice_data->contact,
								'state'=>$invoice_data->state,
								'description'=>$invoice_data->description,
								'taxable_amount'=>$invoice_data->taxable_amount,
								'cgst'=>$invoice_data->cgst,
								'sgst'=>$invoice_data->sgst,
								'igst'=>$invoice_data->igst,
								'amount'=>$invoice_data->amount,
								'name'=>$invoice_data->name,
								'created_date'=>$invoice_data->created_date
								);
				
				DB::table('invoice')->where('id', $invoice_row)->update([ 'is_download' => 1]);
			}
			// echo '<pre>'; print_r($get_invoice);die;
			
			require_once base_path('vendor/dompdf/autoload.inc.php'); 
			$html = view('admin.invoice.pdf_html', compact('get_invoice'))->render();
			
			// echo $html; die; 
			$options = new \Dompdf\Options();
			$options->setIsRemoteEnabled(true);
			$options->set('dpi', 100);
			// $options->SetFont('freesans', '', 12); // freesans // times
			$dompdf = new Dompdf($options);
			// $html = 'Insert full HTML content';
			$dompdf->loadHtml($html);
			// $dompdf->setPaper('A4', 'landscape');
			$dompdf->render();
			// $dompdf->stream("codexworld",array("Attachment"=>0));
			$dompdf->stream(md5(time()).".pdf");

		   die('ddd');
		}
		
	}
	
	public function download_pdf1($invoice_id) { 

		// $invoice_id = Input::get('invoice_id');
		
		$conditions = array();

		if (!empty($invoice_id)){

			$conditions['id'] = $invoice_id;

        }

		$get_invoice = Invoice::where('id',$conditions)->get();
        DB::table('invoice')->where('id', $conditions)->update([ 'is_download' => 1]);
	    //echo '<pre>'; print_r($get_invoice);die;

		require_once base_path('vendor/tcpdf/Pdf.php'); 

	    $pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information

        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetAuthor('pdf');

        $pdf->SetTitle('Tax Invoice');

        $pdf->custom_title = 'Tax Invoice';


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

        $pdf->SetFont('freesans', '', 12); // freesans // times



        // ---------------------------------------------------------

        // $title = 'Studio Report';

		$html = view('admin.invoice.pdf_html_tcpdf', compact('get_invoice'))->render();

        // echo $html; exit();

        //Generate HTML table data from MySQL - end

        // add a page

        $pdf->AddPage();

		// echo $html; die;

		// output the HTML content

        $pdf->writeHTML($html, true, false, true, false, '');



        // reset pointer to the last page

        $pdf->lastPage();



        //Close and output PDF document

        $pdf->Output('faculty_report_' . md5(time()) . '.pdf', 'D'); //I  //D
        
		

	   die('ddd');

	}
   
	public function multi_download_pdf1(Request $request){
		$get_invoice = [];
		if(count($request->id) > 0){
			foreach($request->id as $invoice_row){ 
				$invoice_data     = Invoice::where('id',$invoice_row)->first();
				$get_invoice[] = $invoice_data;
				DB::table('invoice')->where('id', $invoice_row)->update([ 'is_download' => 1]);
			}
			//echo '<pre>'; print_r($get_invoice);die;
			require_once base_path('vendor/tcpdf/Pdf.php'); 

			$pdf = new \Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information

			$pdf->SetCreator(PDF_CREATOR);

			$pdf->SetAuthor('pdf');

			$pdf->SetTitle('Tax Invoice');

			$pdf->custom_title = 'Tax Invoice';


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

			$html = view('admin.invoice.pdf_html_tcpdf', compact('get_invoice'))->render();

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

			$pdf->Output('faculty_report_' . md5(time()) . '.pdf', 'D'); // I // D
          
			//return redirect()->route('admin.invoice.index')->with('success', 'PDF successfully download.');
           
		   die('ddd');
		}
		
	}
	
	
   

    public function import_store(Request $request)
    {   
		$validatedData = $request->validate([
			'import_file' => 'required',
		]);
		
		$file = $request->file('import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}

		

        	
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
        $stArr = $import[0][0];
        // unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) { 
				$invoice = Invoice::create([
					'invoice_no' => $value[1],
					'date' => $value[2], // Invoice Date
					'order_number' => $value[3],
					'created_date' => $value[4], // Order Date
					'payment_id' => $value[5],
					'state' => $value[6],
					'status' => $value[7],
					'captured' => $value[8],
					'description' => $value[9],
					'email' => $value[10],
					'contact' => $value[11],
					'taxable_amount' => $value[12],
					'igst' => $value[13],
					'cgst' => $value[14],
					'sgst' => $value[15],
					'amount' => $value[16],
					'name' => $value[17],
					'c_date' => date('Y-m-d')
				]);	
				$slug = Crypt::encryptString($invoice->id."_".time());
				DB::table('invoice')->where('id', $invoice->id)->update([ 'slug' => $slug]);
				//echo '<pre>'; print_r(Crypt::decryptString($slug));die;
			}	
		}
		else{
			return redirect()->route('admin.invoice.create')->with('error', "Something went wrong !");
		}
        return back()->with('success', 'Tax Invoice Excel Data Imported successfully.');       
    }
	
	public function import_credit_store(Request $request)
    {   
		$validatedData = $request->validate([
			'credit_import_file' => 'required',
		]);
		
		$file = $request->file('credit_import_file');
		$exten = $file->getClientOriginalExtension();
		
		if(strtolower($exten)!='csv'){
			$validatedData = $request->validate([
				'credit_import_file' => 'required|mimes:xlsx,xls,csv',
			]);
		}

		

        	
        $path = $file->path();
		
		$conditions = true;
		$import = Excel::toArray(null, $file); 
        $stArr = $import[0][0];
        // unset($import[0][0]);
        $result = [];
		$errors_row = "";
		
		if(!empty($import[0])){
			foreach ($import[0] as $key => $value) { 			
				$invoice = CreditNote::create([
					'invoice_no' => $value[1],
					'date' => $value[2],
					'order_number' => $value[3],
					'created_at' => $value[4],
					'payment_id' => $value[5],
					'state' => $value[6],
					'status' => $value[7],
					'captured' => $value[8],
					'description' => $value[9],
					'email' => $value[10],
					'contact' => $value[11],
					'taxable_amount' => $value[12],
					'igst' => $value[13],
					'cgst' => $value[14],
					'sgst' => $value[15],
					'amount' => $value[16],
					'name' => $value[17],
					'c_date' => date('Y-m-d')
				]);	
				$slug = Crypt::encryptString($invoice->id."_".time());
				DB::table('credit_note')->where('id', $invoice->id)->update([ 'slug' => $slug]);
				//echo '<pre>'; print_r(Crypt::decryptString($slug));die;
			}	
		}
		else{
			return redirect()->route('admin.invoice.create')->with('error', "Something went wrong !");
		}
        return back()->with('success', 'Credit Note Excel Data Imported successfully.');       
    }
	
	function get_tiny_url($url) {  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='. $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}

	public function invoic_detail(){
		$fdate = Input::get('fdate');
        $tdate = Input::get('tdate');
		
		// $fdate = date('d/m/Y',strtotime($fdate));
		// $tdate = date('d/m/Y',strtotime($tdate));
        $invoice_report = Invoice::orderBy('id', 'asc');
		
		if (!empty($fdate) && !empty($tdate)) {
            // $invoice_report->whereRaw("DATE(date) >= $fdate and DATE(date) >= $tdate" );
            $invoice_report->whereRaw("STR_TO_DATE(date, '%d/%m/%Y') >= '$fdate' AND STR_TO_DATE(date, '%d/%m/%Y') <= '$tdate'" );
        } elseif (!empty($fdate)) {
            // $invoice_report->where('DATE(date)', '>=', $fdate);
			 $invoice_report->whereRaw("STR_TO_DATE(date, '%d/%m/%Y') >= '$fdate'" );
        } elseif (!empty($tdate)) {
            // $invoice_report->where('DATE(date)', '<=', $tdate);
			$invoice_report->whereRaw("STR_TO_DATE(date, '%d/%m/%Y') <= '$tdate'" );
        }

         
		
		return DataTables::of($invoice_report)->make(true);
	}
}
