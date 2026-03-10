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
								<form action="{{ route('faculty-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" name="faculty_id" class="faculty_id_get" name="" value="{{ app('request')->input('faculty_id') }}">
										
										
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
				<table class="table table-bordered " id="">
					<head>
							<tr style="">
								<td colspan="9"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> 
								</td>
							</tr>
					</head>
					<tbody>
					<tr><td>
             <!--  table-responsive-stack -->
					<table class="table table-bordered table-striped table-condensed " id="tableOne">
					 
						 
						<thead>
							<tr style="">
								<th scope="col">Schedule Time</th>
								<!--th scope="col">Assistant Name</th-->
								<th scope="col">Batch Name</th>
								<th scope="col">Subject Name</th>
								<!-- <th scope="col">Branch Name</th> -->
								<th scope="col">Spent Time</th>
								<th scope="col">Topic</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$whereCond = '1=1';;
							
							$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'" ';
							
							// $whereCond .= ' AND timetables.assistant_id = "'.$get_faculty_value->assistant_id.'"';
							if(!empty($get_faculty_value->faculty_name)){
							$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','start_classes.topic_name','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile')
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
								if($get_faculty_timetable_value->is_cancel != '1'){
									$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$interval = $first_date->diff($second_date);
									$duration = $interval->format('%H : %I Hours');
									$base_time->add($interval); 
								}
								else{
									$duration = 'Cancelled Classes';
								}
								
								
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								$total_base_schedule->add($schedule_interval); 
								
							?>
								<tr style="border-bottom-style:hidden;">
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '' ?> to <?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '' ?> (<?=$schedule_duration?>)</td>
									
									<!-- <td><?php //echo isset($get_faculty_timetable_value->cdate) ?  $get_faculty_timetable_value->cdate : '' ?></td> -->
									
									<!--td><?php echo isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '' ?>
									(<a href="tel:<?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?>"><?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?> )</a></td--> 
									<td><?php echo isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '' ?></td>
									<!--td>
									<?php //echo isset($get_faculty_timetable_value->course_name) ?  $get_faculty_timetable_value->course_name : '' ?>
									</td-->
									<td><?php echo isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '' ?>
										
										<?php
										 $no_of_hours=DB::table('batchrelations')->select('no_of_hours')
					                        ->where('is_deleted','0')
					                        ->where('batch_id',$get_faculty_timetable_value->batch_id)
					                        ->where('subject_id',$get_faculty_timetable_value->subject_id)
					                        ->first();
					            if(!empty($no_of_hours)){
					              $no_of_hours=$no_of_hours->no_of_hours;
					              $stime=DB::table('timetables')->select('start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
											->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
					                        ->where('timetables.is_deleted','0')
					                        ->where('timetables.is_cancel','0')
					                        ->where('timetables.is_publish','1')
					                        ->where('timetables.batch_id',$get_faculty_timetable_value->batch_id)
					                        ->where('timetables.subject_id',$get_faculty_timetable_value->subject_id)
											->where('timetables.faculty_id', $get_faculty_value->faculty_id)
					                        ->get();
					              
					              $s_subject = new DateTime('00:00');
					              $bs_subject= new DateTime('00:00');
					              
					              foreach($stime as $skey =>$svalue){
										$from_time = new DateTime($svalue->start_classes_start_time);
										$to_time           = new DateTime($svalue->start_classes_end_time);
										$schedule_interval = $from_time->diff($to_time);
										$s_subject->add($schedule_interval);
					              }

					            // echo $s_subject;
									$subjectDays = $s_subject->diff($bs_subject)->format("%a");
								    $subjectHours = $s_subject->diff($bs_subject)->format("%H");
								    $subjectMinute = $s_subject->diff($bs_subject)->format("%I");
								    $taken_time=($subjectDays*24)+$subjectHours. ":" . $subjectMinute;

								    echo "<br>Plan Hours:".$no_of_hours." (Spent Hours :".$taken_time.")";


					            }
										?>
									</td>
									<td class=""><?=$duration?></td>
									<td class=""><?=$get_faculty_timetable_value->topic_name?></td>







									<!--td><?php //echo isset($get_faculty_timetable_value->chapter_name) ?  $get_faculty_timetable_value->chapter_name : '' ?></td-->
									<tr style="border-bottom-style:dashed; border-bottom-width:2px;border-bottom-color: black;">
									<td colspan="4">
									<?php echo isset($get_faculty_timetable_value->branches_name) ?  $get_faculty_timetable_value->branches_name : '' ?> - <?php echo isset($get_faculty_timetable_value->studios_name) ?  $get_faculty_timetable_value->studios_name : '' ?>
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
												$center_heads .= $center_data->user_name."( <a href='tel:<?=$center_data->mobile?>'>".$center_data->mobile."</a> ) ,";
											}
											echo "<b>CH.-</b> ".rtrim($center_heads,',');
										}
									}
									
									?>
									, Class Assistant - 
									<?php echo isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '' ?>
									(<a href="tel:<?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?>"><?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?> )</a>
									</td></tr>
								</tr>
							<?php
							}
							}
							}
							?>
							
							<tr>
								<th colspan="3"><b>Total Schedule Time:</b> 
								<?php
								$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
								$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</th> 
								<th colspan="7"><b>Total Spent Time:</b> 
								<?php
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</th> 
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
				}else{
					echo "No timetable found";
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
