@extends('layouts.without_login_admin')
@section('content')

 <style>
	body {
		background-image: url(/laravel/public/Gradient-BG.png);
		background-size: 100% 100%;
		background-repeat: no-repeat;
		background-attachment: fixed;
	}
	
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
						<h2 class="content-header-title float-left mb-0">Test Report</h2>
						 
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
								<form action="{{ route('test-report') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" name="user_id" class="faculty_id_get" name="" value="{{ app('request')->input('user_id') }}">
										
										
										<div class="col-12 col-sm-6 col-lg-3">											
											<label for="users-list-status">Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="{{ $selectFromDate }}" class="form-control StartDateClass fdate">	
											</fieldset>	
										</div>	
										<div class="col-12 col-md-3">
											<label for="users-list-status">Batch</label>
											<?php 
												//$batches = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); 
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batches) > 0)
													@foreach($batches as $key => $value)
													<option value="{{ $value->batch_id }}" @if($value->batch_id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->batch_name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-4 col-lg-4"  >
												<label for="users-list-status">&nbsp;</label>		
										 		<fieldset class="form-group" style="">		
												<button type="submit" class="btn btn-dark" style="color: #fff !important;">Search</button>
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
				if (!empty($user_id)) {
				?>
             <!--  table-responsive-stack -->
					<table class="table table-bordered table-striped table-condensed " id="tableOne">
					 
						 
						<thead>
							<tr style="">
								<th scope="col">Schedule Time</th>
								<th scope="col">Batch Name</th>
								<th scope="col">Subject Name</th>
								<!--th scope="col">Spent Time</th-->
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$whereCond = '1=1';;
							
							$whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'" ';
							
							$whereCond .= ' AND timetables.branch_id = "'.$user_branch_id.'" ';

							if(!empty($batch_id)){
								$whereCond .= ' AND timetables.batch_id = "'.$batch_id.'" ';
							}
							
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
													  // ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
													  ->where('timetables.time_table_parent_id', '0')
													  ->where('timetables.is_deleted', '0')
													  ->where('timetables.is_publish', '1')
													  ->where('timetables.online_class_type', 'Test')
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
								$batch_name = isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '';
								$batch_array = array();
								$batch_array[] = $get_faculty_timetable_value->batch_id;
								$get_batch_data = DB::table('timetables')
													  ->select('timetables.batch_id','batch.name as batch_name')
													  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													  ->where('timetables.is_deleted', '0')
													  ->where('timetables.is_publish', '1')
													  ->where('timetables.online_class_type', 'Test')
													  ->where('timetables.time_table_parent_id', $get_faculty_timetable_value->id)
													  ->get();
								if(count($get_batch_data) > 0){
									foreach($get_batch_data as $bval){
										if(!in_array($bval->batch_id,$batch_array)){
											$batch_array[] = $bval->batch_id;
											$batch_name .=", ".$bval->batch_name;
										}
									}
								}
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
								
								$check_update = DB::table('test_report')->where('tt_id',$get_faculty_timetable_value->id)->count();
								
								
							?>
								<tr style="border-bottom-style:hidden;color:#000 !important;">
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '' ?> to <?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '' ?> (<?=$schedule_duration?>)</td>
									
								 
									<td><?php echo $batch_name; ?></td>
									 
									<td><?php echo isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '' ?>
										
									 
									</td>
									<!--td class=""><?=$duration?></td-->
									<td class=""> 
										<?php if(date('Y-m-d',strtotime($selectFromDate. ' +2 day')) >= date('Y-m-d')){?>
											<a href="{{route('test-report-update')}}?user_id={{$user_id}}&tt_id={{$get_faculty_timetable_value->id}}" class="btn btn-dark" style="color:#fff !important;margin-bottom: 5px;"> Update </a> &nbsp;
										<?php } ?>
										
										<?php if($check_update > 0){ ?>
										<a href="{{route('test-report-view')}}?user_id={{$user_id}}&tt_id={{$get_faculty_timetable_value->id}}" class="btn btn-dark" style="color:#fff !important;margin-bottom: 5px;"> View </a>
										
										<a href="{{route('test-report-download')}}?user_id={{$user_id}}&tt_id={{$get_faculty_timetable_value->id}}" class="btn btn-dark" style="color:#fff !important;margin-bottom: 5px;"> Download </a>
										<?php } ?>
										
									</td>
									
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
							?>
							
							 	
						</tbody>
					</table>
					  
				<?php 
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

	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
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
