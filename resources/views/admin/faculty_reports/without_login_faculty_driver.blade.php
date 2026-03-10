@extends('layouts.without_login_admin')
@section('content')

 <style>
    .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media screen and (max-width: 770px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
.table tbody + tbody {
    border-top: 2px solid #ccc;
}
    </style>

<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Report</h2>
						 
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('faculty-reports-driver') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" name="driver_id" name="" value="{{ app('request')->input('driver_id') }}">
										
										
										<div class="col-12 col-sm-6 col-lg-4">											
											<label for="users-list-status">Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="{{ $selectFromDate }}" class="form-control StartDateClass fdate">	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-8"  >
												<label for="users-list-status">&nbsp;</label>		
										 		<fieldset class="form-group" style="">		
												<button type="submit" class="btn btn-primary">Search</button>
											</fieldset>					
										</div>										
											
									</div>
									
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $get_faculty_value) {
				?>
					<table class="table table-bordered table-striped" id="">
					 
						<head>
							<tr style="">
								<td colspan="9"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> 
								</td>
								<!--th colspan="3"><b>Assistant Name : 
								<?php //echo isset($get_faculty_value->assistant_name) ?  $get_faculty_value->assistant_name : ''; ?>
								</b></th-->
							</tr>
						</head>
                        <tbody>
					<tr> <td>
					<table class="table table-bordered table-striped table-responsive-stack" id="tableOne">
						<thead>
							<tr style="">
								<th scope="col">From Time</th>
								<th scope="col">To Time</th>
								<th scope="col">Date</th>
								<th scope="col">Assistant Name</th>
								<th scope="col">Batch Name</th>
								<!--th scope="col">Course Name</th-->
								<th scope="col">Subject Name</th>
								<!--th scope="col">Chapter Name</th-->
								<th scope="col">Branch Name</th>
								<!--th scope="col">Topic Name</th-->
								<!--th scope="col">Status</th-->
								<th scope="col">Schedule Time</th>
								<!--th scope="col">Spent Time</th-->
							</tr>
						</thead>
						<tbody>
							<?php
							$whereCond = '1=1';;
							
							$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'" ';
							
							// $whereCond .= ' AND timetables.assistant_id = "'.$get_faculty_value->assistant_id.'"';
							if(!empty($get_faculty_value->faculty_name)){
							$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile')
													  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													  ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
													  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													  ->leftJoin('chapter', 'chapter.id', '=', 'timetables.chapter_id')
													  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
													  ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
													  ->where('timetables.time_table_parent_id', '0')
													  ->where('timetables.is_deleted', '0')
													  ->where('timetables.is_publish', '1')
													  ->whereRaw($whereCond)
													  ->orderBy('timetables.from_time', 'ASC')
													  ->get();
													  
													  //echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							$base_time = new DateTime('00:00');
							$total     = new DateTime('00:00');
							
							$total_schedule = new DateTime('00:00');
							$total_base_schedule = new DateTime('00:00');
							
							if(count($get_faculty_timetable) > 0){ 
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								
								$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
								$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
								$interval = $first_date->diff($second_date);
								$duration = $interval->format('%H : %I Hours');
								$base_time->add($interval); 
								
								
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								$total_base_schedule->add($schedule_interval); 
								
							?>
								<tr>
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_timetable_value->cdate)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '' ?>
									( <?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?> )</td> 
									<td><?php echo isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '' ?></td>
									<!--td>
									<?php //echo isset($get_faculty_timetable_value->course_name) ?  $get_faculty_timetable_value->course_name : '' ?>
									</td-->
									<td><?php echo isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '' ?></td>
									<!--td><?php //echo isset($get_faculty_timetable_value->chapter_name) ?  $get_faculty_timetable_value->chapter_name : '' ?></td-->
									<td><?php echo isset($get_faculty_timetable_value->branches_name) ?  $get_faculty_timetable_value->branches_name : '' ?>
									<?php
									if(!empty($get_faculty_timetable_value->branches_id)){
										$get_data = DB::table('users')
										->leftJoin('userbranches','users.id','=','userbranches.user_id')
										->leftJoin('userdetails','users.id','=','userdetails.user_id')
										->select('users.name as user_name','users.mobile as mobile')
										->where('userbranches.branch_id',$get_faculty_timetable_value->branches_id)
										->where('userdetails.degination','CENTER HEAD')->get();
										$center_heads = "";
										if(count($get_data) > 0){
											foreach($get_data as $center_data){
												$center_heads .= $center_data->user_name."( ".$center_data->mobile." ) ,";
											}
											echo "<b>CH.-</b> ".rtrim($center_heads,',');
										}
									}
									
									?>
									</td>
									<!--td><?php //echo isset($get_faculty_timetable_value->topic_name) ?  $get_faculty_timetable_value->topic_name : '' ?></td-->
									<!--td><?php //echo !empty($get_faculty_timetable_value->start_classes_status) ?  $get_faculty_timetable_value->start_classes_status : 'Not Started' ?></td-->
									<td><?=$schedule_duration?></td>
									<!--td><?=$duration?></td-->
								</tr>
							<?php
							}
							}
							}
							?>
							
							<tr>
								<td colspan="4"><b>Total Schedule Time:</b> 
								<?php
								$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
								$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</td> 
								<td colspan="7"><b>Total Spent Time:</b> 
								<?php
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</td> 
							</tr>
								
						</tbody>
					</table>
					</td>
					</tr>
					</tbody>
					</table>
					<hr/>
				<?php 
					}
				}
				?>
		<style>
		hr{background:#000;}
		</style>
					 
				</div>       
				
			</section>
		</div>
	</div>
</div>


<div id="s_class" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="submit_start_class_form">
				<div class="modal-header">
					<h5 class="modal-title">Timetable</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker start_time" placeholder="Start Time" name="start_time" autocomplete="off">
									<label for="first-name-column">Start Time</label>
								</div>
							</div>
							
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker end_time" placeholder="End Time" name="end_time" autocomplete="off">
									<label for="first-name-column">End Time</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="timetable_id" class="timetable_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="start_class_btn" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div> 


				
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>

<script id="rendered-js">
$(document).ready(function () {


  // inspired by http://jsfiddle.net/arunpjohny/564Lxosz/1/
  $('.table-responsive-stack').each(function (i) {
    var id = $(this).attr('id');
    //alert(id);
    $(this).find("th").each(function (i) {
      $('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead">' + $(this).text() + ':</span> ');
      $('.table-responsive-stack-thead').hide();

    });



  });





  $('.table-responsive-stack').each(function () {
    var thCount = $(this).find("th").length;
    var rowGrow = 100 / thCount + '%';
    //console.log(rowGrow);
    $(this).find("th, td").css('flex-basis', rowGrow);
  });




  function flexTable() {
    if ($(window).width() < 770) {

      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").show();
        $(this).find('thead').hide();
      });


      // window is less than 768px   
    } else {


      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").hide();
        $(this).find('thead').show();
      });



    }
    // flextable   
  }

  flexTable();

  window.onresize = function (event) {
    flexTable();
  };






  // document ready  
});
//# sourceURL=pen.js
    </script>
@endsection
