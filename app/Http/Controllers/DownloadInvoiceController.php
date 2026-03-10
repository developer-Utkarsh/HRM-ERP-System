<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Invoice;
use App\CreditNote;
use Input;
use DB;
use Excel;

class DownloadInvoiceController extends Controller
{
    public function index(Request $request)
    {   $invoice_report = array();
	    if(!empty($request->slug)){
			$slug_details   = Crypt::decryptString($request->slug);
			$slug_explode   = explode("_", $slug_details);
			$invoice_report = Invoice::where([['id', '=', $slug_explode[0]], ['slug', '=', $request->slug]])->first();
		    //echo '<pre>'; print_r($invoice_report);die;
		}
		return view('download-invoice', compact('invoice_report'));		
    }
	
	public function credit(Request $request)
    {   
		$invoice_report = array();
	    if(!empty($request->slug)){
			$slug_details   = Crypt::decryptString($request->slug);
			$slug_explode   = explode("_", $slug_details);
			$invoice_report = CreditNote::where([['id', '=', $slug_explode[0]], ['slug', '=', $request->slug]])->first();
		    //echo '<pre>'; print_r($invoice_report);die;
		}
		return view('download-invoice-credit', compact('invoice_report'));		
    }
	// public function download_pdf($invoice_id){
		//$invoice_id = Input::get('invoice_id');
		// echo '<pre>'; print_r($invoice_id);die;
	// }
}
