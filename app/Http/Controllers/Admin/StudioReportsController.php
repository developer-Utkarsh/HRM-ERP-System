<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Studio;
use App\Userdetails;
use App\Userbranches;
use App\Branch;
use Input;
use Dompdf\Dompdf;
use Options;
use Auth;
use DB;
use App\Exports\StudioReportExport;
use Excel;


class StudioReportsController extends Controller
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
					$q->where('is_publish', '=', '1');
					$q->orderBy('from_time');
				}
            },'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter','studio.timetable.assistant'])->whereRaw($whereCond);
			
		    if (!empty($fdate)){	
				$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
						$q->where('cdate', '=', $fdate);
						// $q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
						$q->where('is_publish', '=', '1');
				});
		    }
			
		$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		
		if(!empty(Auth::user()->id)){
			return view('admin.studio_reports.without_login_assistant_report', compact('get_studios','fdate'));
		}
		else{
			return view('admin.studio_reports.without_login_assistant_report', compact('get_studios','fdate'));
		}
        
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
		$login_brances = array();
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){	
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
		}
		
        $search    = Input::get('search');
        $branch_id    = Input::get('branch_id');
        $branch_location    = Input::get('branch_location');
        $assistant_id = Input::get('assistant_id');
        $studio_id    = Input::get('studio_id');
		$fdate        = Input::get('fdate');
        // $tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$change_after_publish=Input::get('change_after_publish');
		$online_class_type=Input::get('online_class_type');
		
		$get_studios = array();
		if(!empty($search) || empty(Auth::user()->id)){
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
			else if(!empty($login_brances)){
				$brnc_id   = implode(",", $login_brances);
				$whereCond = " id IN ($brnc_id) ";
			}
			
			if(!empty($branch_location)){
				if(!empty($whereCond)){
					$whereCond .= " and branch_location = '$branch_location' ";
				}
				else{
					$whereCond = " branch_location = '$branch_location' ";
				}
			}
			//echo "<pre>"; print_r($whereCond); die;
			// $conditions['id'] = 2;
			// $get_studios = Studio::with(['assistant', 'branch','timetable','timetable.topic','timetable.faculty','timetable.batch','timetable.course','timetable.subject','timetable.chapter'])->where($conditions)->orderBy('id', 'desc')->get();
			
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
				'studio.timetable' => function ($q) use ($fdate,$change_after_publish,$online_class_type)
				{					
					if(!empty($change_after_publish) && $change_after_publish!=3){
						$q->where('change_after_publish',$change_after_publish);						
					}
					
					if(!empty($online_class_type)){
						$q->where('online_class_type',$online_class_type);
					}

					if(!empty($fdate)){
						$q->where('cdate', '=', $fdate);
						
						if(!empty($change_after_publish) && $change_after_publish==3){
							$q->where('is_deleted', '=', '1');
							$q->where('is_publish', '=', '1');
						}else{
							$q->where('is_deleted', '=', '0');
							$q->where('time_table_parent_id', '0');
						}
						
						
						if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
						}
						else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
						}
						else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
							$q->where('assistant_id', Auth::user()->id);
						}
						
						$q->orderBy('from_time');
					}
				},'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter','studio.timetable.assistant'])->whereRaw($whereCond);
				
				if (!empty($fdate)){	
					$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate,$change_after_publish) {
							
						
							if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $q->whereIn('studio_id',$studio_arr);
							}
							else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
							  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
							  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
							  
							  $q->whereIn('studio_id',$studio_arr);
							}
							else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
								$q->where('assistant_id', Auth::user()->id);
							}
							  
							$q->where('cdate', '=', $fdate);
							// $q->where('cdate', '<=', $tdate);
							
							if(!empty($change_after_publish) && $change_after_publish==3){
								$q->where('is_deleted', '=', '1');
							}else{
								$q->where('is_deleted', '=', '0');
								// $q->where('is_publish', '=', '1');
								$q->where('time_table_parent_id', '0');
							}
							
					});
				} 
			
		//$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,66,41,38,48,49,53,63,60,45,39,36,44,46,52,56,47,50,51,43,55,57,58,59,61,62,64,65,70)')->get();
		}
		
		if(!empty(Auth::user()->id)){
			return view('admin.studio_reports.index', compact('get_studios','fdate','login_brances'));
		}
		else{
			return view('admin.studio_reports.without_login_studio_report', compact('get_studios','fdate'));
		}
        
    }

   public function download_pdf() {
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){	
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
		}
		$branch_location = Input::get('branch_location');
		$branch_id = Input::get('branch_id');
        $studio_id = Input::get('studio_id');
        $assistant_id = Input::get('assistant_id');
		$fdate        = Input::get('fdate');
        // $tdate        = Input::get('tdate');
		$type        = Input::get('type');
		$online_class_type        = Input::get('online_class_type');
		
		if(empty($fdate)){
			$fdate = date('Y-m-d');
		}
		/* $conditions = array();
		
		if (!empty($branch_id)){
			$conditions['id'] = $branch_id;
        } */
		
		
		$whereCond    = '1=1 ';
		if (!empty($branch_id)){
			 //echo "<pre>"; print_r($branch_id); die;
			//$brnc_id   = implode(",", $branch_id);  
			$whereCond = " id IN ($branch_id) ";
        }
		else if(!empty($login_brances)){
			$brnc_id   = implode(",", $login_brances);
			$whereCond = " id IN ($brnc_id) ";
		}
		
		if(!empty($branch_location)){
			if(!empty($whereCond)){
				$whereCond .= " and branch_location = '$branch_location' ";
			}
			else{
				$whereCond = " branch_location = '$branch_location' ";
			}
		}
		
		$get_studios = Branch::with(['studio'=> function ($q) use ($studio_id,$assistant_id,$type)
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
			'studio.timetable' => function ($q) use ($fdate,$online_class_type)
            {
                if(!empty($fdate)){
					$q->where('cdate', '=', $fdate);
					// $q->where('cdate', '<=', $tdate);
					$q->where('is_deleted', '=', '0');
					// $q->where('is_publish', '=', '1');
					$q->where('time_table_parent_id', '=', '0');
					$q->orderBy('from_time');					
					$q->orderBy('batch_id','asc');					
				}
				
				if(!empty($online_class_type)){
					$q->where('online_class_type',$online_class_type);
				}

            },'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->whereRaw($whereCond)	;
			
			if (!empty($fdate)){	
				$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
					
						if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
						  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
						  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
						  
						  $q->whereIn('studio_id',$studio_arr);
						}
						else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
						  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
						  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
						  
						  $q->whereIn('studio_id',$studio_arr);
						}
						else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
							$q->where('timetables.assistant_id', Auth::user()->id);
						 }
						 
						$q->where('cdate', '=', $fdate);
						// $q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
						// $q->where('is_publish', '=', '1');
						$q->where('time_table_parent_id', '=', '0');
				});
		    }
			
		//$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,66,41,38,48,49,53,63,60,45,39,36,44,46,52,56,47,50,51,43,55,57,58,59,61,62,64,65,70)')->get();
	   
		return view('admin.studio_reports.pdf_html', compact('get_studios','fdate'));
	   
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
        $pdf->SetFont('freesans', '', 12);

        // ---------------------------------------------------------
        // $title = 'Studio Report';
		$html = view('admin.studio_reports.pdf_html', compact('get_studios'))->render();
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
   
   public function download_excel()
    {   
		if(Auth::user()->user_details->degination == 'PRODUCTION INCHARGE'){	
			if(!empty(Auth::user()->user_branches)){
				foreach(Auth::user()->user_branches as $allBranches){
					$login_brances[] = $allBranches->branch_id;
				}
			}
		}
		$branch_location = Input::get('branch_location');
		$branch_id = Input::get('branch_id');
        $studio_id = Input::get('studio_id');
        $assistant_id = Input::get('assistant_id');
		$fdate        = Input::get('fdate');
        // $tdate        = Input::get('tdate');
		$type        = Input::get('type');
		
		if(empty($fdate)){
			$fdate = date('Y-m-d');
		}
		/* $conditions = array();
		
		if (!empty($branch_id)){
			$conditions['id'] = $branch_id;
        } */
		
		
		$whereCond    = '1=1 ';
		if (!empty($branch_id)){
			 //echo "<pre>"; print_r($branch_id); die;
			//$brnc_id   = implode(",", $branch_id);  
			$whereCond = " id IN ($branch_id) ";
        }
		else if(!empty($login_brances)){
			$brnc_id   = implode(",", $login_brances);
			$whereCond = " id IN ($brnc_id) ";
		}
		
		if(!empty($branch_location)){
			if(!empty($whereCond)){
				$whereCond .= " and branch_location = '$branch_location' ";
			}
			else{
				$whereCond = " branch_location = '$branch_location' ";
			}
		}
		
		$get_studios = Branch::with(['studio'=> function ($q) use ($studio_id,$assistant_id,$type)
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
					// $q->where('is_publish', '=', '1');
					$q->where('time_table_parent_id', '=', '0');
					$q->orderBy('from_time');					
					$q->orderBy('batch_id','asc');					
				}
            },'studio.timetable.topic','studio.timetable.faculty','studio.timetable.batch','studio.timetable.course','studio.timetable.subject','studio.timetable.chapter'])->whereRaw($whereCond)	;
			
			if (!empty($fdate)){	
				$get_studios->WhereHas('studio.timetable', function ($q) use ($fdate) {
					
						if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
						  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
						  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
						  
						  $q->whereIn('studio_id',$studio_arr);
						}
						else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
						  $user_branch_id = Auth::user()->user_branches[0]->branch_id; 
						  $studio_arr = Studio::where('branch_id', $user_branch_id)->get()->pluck('id'); 
						  
						  $q->whereIn('studio_id',$studio_arr);
						}
						else if(!empty(Auth::user()->id) && Auth::user()->role_id == 3){
							$q->where('timetables.assistant_id', Auth::user()->id);
						 }
						 
						$q->where('cdate', '=', $fdate);
						// $q->where('cdate', '<=', $tdate);
						$q->where('is_deleted', '=', '0');
						// $q->where('is_publish', '=', '1');
						$q->where('time_table_parent_id', '=', '0');
				});
		    }
			
		//$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
		$get_studios = $get_studios->orderByRaw('Field(id,37,42,40,66,41,38,48,49,53,63,60,45,39,36,44,46,52,56,47,50,51,43,55,57,58,59,61,62,64,65,70)')->get();
        if(count($get_studios) > 0){  
            return Excel::download(new StudioReportExport($get_studios), 'StudioReportData.xlsx'); 

        } else{
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    } 

    public function changeMark(Request $request){
    	$timetable_id=$request->timetable_id??0;
    	DB::table('timetables')->where('id',$timetable_id)->where('change_after_publish',1)
    	->update(['change_after_publish'=>2]);
    	return response(['status'=>true,'message'=>'Change Marked Done'],200);
    }

}
