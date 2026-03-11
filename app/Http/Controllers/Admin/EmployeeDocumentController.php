<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;
use Input;
use Image;
use Dompdf\Dompdf;
use DB;
use App\EmployeeDocument;
use App\NewTask;

class EmployeeDocumentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$logged_role_id = Auth::user()->role_id;
		$logged_id = Auth::user()->id;

		$emp_id = Input::get('emp_id');
		$get_employee = array();
		$get_emp_doc = array();
		if (!empty($emp_id)) {
			$get_employee = User::where('id', $emp_id)->first();
			$get_emp_doc = EmployeeDocument::where('emp_id', $emp_id)->first();
		}

		$employee = User::where('status', '1')->where('is_deleted', '0');

		if ($logged_role_id == 24) {
			$users = NewTask::getEmployeeByLogID($logged_id, 'attendance');
		}
		else {
			$users = NewTask::getEmployeeByLogID($logged_id, 'create-attendance');
		}

		$employeeArray = array();
		$usr = $logged_id . ',' . implode(', ', array_map(function ($emp_data) {
			return $emp_data['id']; }, $users));
		$employeeArray = explode(',', $usr);
		$employee->whereIn('id', $employeeArray);

		$employee = $employee->orderBy('name')->get();

		//echo '<pre>'; print_r($get_emp_doc);die;
		return view('admin.employee_document.index', compact('get_employee', 'get_emp_doc', 'employee'));
	}

	public function download_document($employee_id, $type)
	{
		$conditions = array();

		if (!empty($employee_id)) {

			$conditions['id'] = $employee_id;

		}

		$get_emp_details = User::with('user_details')->where('id', $conditions)->first();

		require_once base_path('vendor/dompdf/autoload.inc.php');
		define("DOMPDF_UNICODE_ENABLED", true);
		define("DOMPDF_ENABLE_REMOTE", false);
		define("DOMPDF_ENABLE_CSS_FLOAT", false);
		define("DOMPDF_ENABLE_FONTSUBSETTING", false);
		define("DOMPDF_ENABLE_HTML5PARSER", false);

		define("DEBUGCSS", false);
		define("DEBUG_LAYOUT", false);
		define("DEBUG_LAYOUT_LINES", true);
		define("DEBUG_LAYOUT_BLOCKS", true);
		define("DEBUG_LAYOUT_INLINE", true);
		define("DEBUG_LAYOUT_PADDINGBOX	", true);
		$html = view('admin.employee_document.offer_letter_pdf_html', compact('get_emp_details', 'type'))->render();

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
		$dompdf->set_option("fontDir", url('/laravel/public/Hind/Hind-Regular.ttf')) .
			// $dompdf->set_option('defaultFont', 'freesans');
			// $dompdf->stream("codexworld",array("Attachment"=>0));
			$dompdf->stream(md5(time()) . ".pdf");

		die('ddd');
	}

	public function uploadImage($image)
	{
		$drive = public_path(DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR);
		$extension = $image->getClientOriginalExtension();
		$imagename = uniqid() . '.' . $extension;
		$newImage = $drive . $imagename;
		$imgResource = $image->move($drive, $imagename);
		return $imagename;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
	//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//echo '<pre>'; print_r($request->post());
		if (!empty($request->file('tenth_marksheet'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->tenth_marksheet;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}

				$filedata = $this->uploadImage($request->file('tenth_marksheet'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'tenth_marksheet' => $filedata
				]);

			}
			else {
				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('tenth_marksheet'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'tenth_marksheet' => $filedata,
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('tenth_marksheet'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'tenth_marksheet' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}

			}
		}

		if (!empty($request->file('twelth_marksheet'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->twelth_marksheet;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}
				$filedata = $this->uploadImage($request->file('twelth_marksheet'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'twelth_marksheet' => $filedata
				]);

			}
			else {

				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('twelth_marksheet'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'twelth_marksheet' => $filedata,
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('twelth_marksheet'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'twelth_marksheet' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}


			}

		}

		if (!empty($request->file('graduate'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->graduate;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}
				$filedata = $this->uploadImage($request->file('graduate'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'graduate' => $filedata
				]);

			}
			else {

				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('graduate'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'graduate' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('graduate'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'graduate' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}

			}

		}

		if (!empty($request->file('postgraduate'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->postgraduate;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}
				$filedata = $this->uploadImage($request->file('postgraduate'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'postgraduate' => $filedata
				]);

			}
			else {


				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('postgraduate'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'postgraduate' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('postgraduate'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'postgraduate' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}


			}

		}

		if (!empty($request->file('aadhar_card'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->aadhar_card;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}
				$filedata = $this->uploadImage($request->file('aadhar_card'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'aadhar_card' => $filedata
				]);

			}
			else {


				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('aadhar_card'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'aadhar_card' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('aadhar_card'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'aadhar_card' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}


			}

		}


		if (!empty($request->file('experience_letter'))) {
			if (!empty($request->doc_id)) {
				$get_prev_data = DB::table('employee_document')->where('id', $request->doc_id)->first();
				$get_data = $get_prev_data->experience_letter;
				if (!empty($get_data)) {
					unlink(public_path() . '/document/' . $get_data);
				}
				$filedata = $this->uploadImage($request->file('experience_letter'));
				$save = DB::table('employee_document')->where('id', $request->doc_id)->update([
					'emp_id' => $request->employee_id,
					'experience_letter' => $filedata
				]);

			}
			else {


				$check_tbl = DB::table('employee_document')->where('emp_id', $request->employee_id)->first();

				if (!empty($check_tbl)) {
					$filedata = $this->uploadImage($request->file('experience_letter'));
					$save = DB::table('employee_document')->where('emp_id', $request->employee_id)->update([
						'emp_id' => $request->employee_id,
						'experience_letter' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$filedata = $this->uploadImage($request->file('experience_letter'));
					$save = DB::table('employee_document')->insertGetId([
						'emp_id' => $request->employee_id,
						'experience_letter' => $filedata,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}


			}

		}

		if ($save) {
			echo json_encode(['status' => true]);
		}
		else {
			echo json_encode(['status' => false]);
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
	//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
	//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
	//
	}

}
