@extends('layouts.without_login_admin')
@section('content')
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
					<table class="table data-list-view" style=''>
					 
						<head>
							<tr style="">
								<th colspan="9"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> 
								</th>
								<!--th colspan="3"><b>Assistant Name : 
								<?php //echo isset($get_faculty_value->assistant_name) ?  $get_faculty_value->assistant_name : ''; ?>
								</b></th-->
							</tr>
						</head>
						<head>
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
						</head>
						<body>
							<?php
							$whereCond = '1=1';
							
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
								
						</body>
					
					</table>
					<p><hr/></p>
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

<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="submit_assign_driver_form">
				<input type="hidden" name="faculty_id" class="faculty_id">
				<input type="hidden" name="assign_date" class="assign_date">
				<div class="modal-header">
					<h5 class="modal-title">Assign Driver</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class="col-md-12 col-12">
								<div class="form-label-group">
									<select class="form-control driver_id" name="driver_id">
										<option value=""> - Select Driver - </option>
										<option value="0"> Remove Driver </option>
										@if(count($drivers) > 0)
											@foreach($drivers as $value)
												<option value="{{ $value->id }}">{{ $value->name }}</option>
											@endforeach
										@endif
									</select>
									<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
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

<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
	
					
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			//data.studio_id = $('.studio_id').val(),
			//data.branch_id = $('.branch_id').val(),
			data.branch_location = $('.branch_location').val(),
			data.faculty_id = $('.faculty_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/faculty-report-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">

		
	$(".get_start_class_data").on("click", function() {
		$(".timetable_id").val('');
		$(".start_time").val('');
		$(".end_time").val('');
		var tt_id = $(this).attr("data-id");
		$(".timetable_id").val(tt_id);
		
		
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.edit-start-class') }}',
			data : {'_token' : '{{ csrf_token() }}', 'tt_id': tt_id},
			//processData : false, 
			//contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){
					if(data.start_time != ''){
						$('.start_time').val(data.start_time);
					}
					if(data.end_time != ''){
						$('.end_time').val(data.end_time);
					}
					$('#s_class').modal({
							backdrop: 'static',
							keyboard: true, 
							show: true
					});
				}
			}
		});
							
		
	});  

	var $form = $('#submit_start_class_form');
	validatereschedule = $form.validate({
		ignore: [],
		rules: {
			start_time : {
				required: true,
			},
			end_time : {
				required: true,
			},         
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});
	
	$("#submit_start_class_form").submit(function(e) {
		var form = document.getElementById('submit_start_class_form');
		var dataForm = new FormData(form);
		e.preventDefault();
		if(validatereschedule.valid()){
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.update-start-class') }}',
				data : dataForm,
				processData : false,  
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						$('#submit_start_class_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
					}
				}
			});
		}       
	});
					

	
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $("input[name=faculty_id]").val();
		if (branch_id) {
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-faculty') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'faculty_id': faculty_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.faculty_id').empty();
					$('.faculty_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $(".faculty_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-faculty') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'faculty_id': faculty_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.faculty_id').empty();
					$('.faculty_id').append(data);
				}
			});
		}
	});
	
	$(".assign_driver").on("click", function() {
		var faculty_id = $(this).attr("data-faculty-id");
		var assign_date = $(this).attr("data-assign-date");
		$(".faculty_id").val(faculty_id);
		$(".assign_date").val(assign_date);
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	});
	
	$("#submit_assign_driver_form").submit(function(e) {
		var form = document.getElementById('submit_assign_driver_form');
		var dataForm = new FormData(form);
		e.preventDefault();
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.driver.update_driver') }}',
				data : dataForm,
				processData : false,  
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						// $('#submit_assign_driver_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
					}
				}
			});
	});
	
	$(".assigned_driver").on("click", function() {
		$('#submit_assign_driver_form').trigger("reset");
		var driver_id = $(this).attr("data-driver-id");
		var faculty_id = $(this).attr("data-faculty-id");
		var assign_date = $(this).attr("data-assign-date");
		$(".faculty_id").val(faculty_id);
		$(".assign_date").val(assign_date);
		$(".driver_id").val(driver_id);
		
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	});
	
</script>
@endsection
