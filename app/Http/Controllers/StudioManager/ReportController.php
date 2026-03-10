<?php

namespace App\Http\Controllers\StudioManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Input;
use App\Timetable;
use App\StartClass;
use Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = Input::get('branch_id');
        $faculty_id = Input::get('faculty_id');
        $month = Input::get('month');
        $fdate = Input::get('fdate');
        $tdate = Input::get('tdate');

        // $report_faculty = User::with([
        //     'user_details.branch',
        //     'timetable.startclass',
        //     'batchrelations.batch',
        // ])->orderBy('id', 'desc');

        $report_faculty = StartClass::with(['timetable.faculty','timetable.studio.branch'])->orderBy('id', 'desc');

        //print_r($report_faculty->toArray()); die;

        if (!empty($branch_id)) {
            $report_faculty->orWhereHas('timetable.studio.branch', function ($b) use ($branch_id) {
                return $b->where('branch_id', '=', $branch_id);
            });
        }

        if (!empty($faculty_id)) {
            $report_faculty->orWhereHas('timetable.faculty', function ($f) use ($faculty_id) {
                return $f->where('faculty_id', '=', $faculty_id);
            });
        }        

        if (!empty($month)){
            $report_faculty->whereRaw('MONTH(sc_date) = '.$month);
        }

        if (!empty($fdate) && !empty($tdate)) {
            $report_faculty->where('sc_date', '>=', $fdate)->where('sc_date', '<=', $tdate);
        } elseif (!empty($fdate)) {
            $report_faculty->where('sc_date', '>=', $fdate);
        } elseif (!empty($tdate)) {
            $report_faculty->where('sc_date', '<=', $tdate);
        }

        $report_faculty = $report_faculty->get();              

        return view('studiomanager.report.index', compact('report_faculty'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $faculty = User::with(['batchrelations.batch.course','batchrelations.subject'])->where('role_id', '2')->where('id', $id)->first();
        $faculty_report = StartClass::with('timetable.faculty')->where('id', $id)->first();
        //print_r($faculty_report->toArray()); die;
        return view('studiomanager.report.view', compact('faculty_report'));
    }

    public function subject_report(Request $request){

        $faculty_id = $request->fid;
        $subject_id = $request->sid;
        $get_subject_report = Timetable::with('startclass','subject')->where('faculty_id',$faculty_id)->where('subject_id',$subject_id)->first();        

        if (!empty($get_subject_report) && !empty($get_subject_report)) {

            $res = '';
            $res .= '<div class="table-responsive">';
            $res .= '<table class="table data-list-view">';
            $res .= '<thead>';
            $res .= '<tr>';
            $res .= '<th>Subject Name</th>';
            $res .= '<th>Start Time</th>';
            $res .= '<th>End Time</th>';
            $res .= '<th>Working Hours</th>';
            $res .= '<th>Date</th>';
            $res .= '</tr>';
            $res .= '</thead>';
            $res .= '<tbody>';
            foreach($get_subject_report->startclass as $key => $report){

                $minutes =  round(abs(strtotime($report->start_time) - strtotime($report->end_time)) / 60,2);
                $hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);

                $res .= '<tr>';
                $res .= '<td>' . $get_subject_report->subject->name . '</td>';
                $res .= '<td>' . $report->start_time . '</td>';
                $res .= '<td>' . $report->end_time . '</td>';
                $res .= '<td>' . $hours . ' Hours' . '</td>';
                $res .= '<td>' . $report->sc_date . '</td>';
                $res .= '</tr>';
            }
            $res .= '</tbody>';
            $res .= '</table>';
            $res .= '</div>';

            echo  $res;

            exit();

        } else {

            echo $res = '<h3 class="text-center">Subject Report Not Found</h3>';
            die();
        }
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function report_export(Request $request)
    {
        if(is_array($request->id) && !empty($request->id)){

            $id = [];

            if ($request->has('id') && !empty($request->id)) {

                $id =  $request->id;
                return Excel::download(new ReportExport($id), 'report.xlsx');
            }

        } else{

            return redirect()->route('studiomanager.reports.index')->with('error', 'Please Select The Checkbox');
        }
    }
}
