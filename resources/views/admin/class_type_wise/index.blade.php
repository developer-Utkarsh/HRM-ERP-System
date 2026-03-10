@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Class Type Wise Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
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
								<form action="{{ route('admin.class-type-wise') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate"  placeholder="DD-MM-YYYY" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate" >	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-3" style="display:;" >
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" placeholder="DD-MM-YYYY" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>									
										</div>										
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('admin.class-type-wise') }}" class="btn btn-warning">Reset</a> 
										<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a> 
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<table class="table data-list-view" style=''>
				<head>
				<tr style="">
					<th>Type</th>
					<th>Class Count</th>
					<th>Schedule Time</th>
					<th>Spent Time</th>
				</tr>
				</head>
				<body>
				<?php 
				$final_total_schedule = new DateTime('00:00');
				$final_total_schedule_last = new DateTime('00:00');
				
				$final_total_spent = new DateTime('00:00');
				$final_total_spent_last = new DateTime('00:00');
				
				
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {
						$total_cancel_class = 0;
				?>
					
					  
						
							<?php
							$whereCond = '1=1';;
							 
							if(!empty($selectFromDate) && !empty($selectToDate)){
								$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							}
							else{
								$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
							}	
							
							$base_time = new DateTime('00:00');
							$total     = new DateTime('00:00');
							
							$total_schedule = new DateTime('00:00');
							$total_base_schedule = new DateTime('00:00');
							$total_base_cancel = new DateTime('00:00');
							
							// $whereCond .= ' AND timetables.assistant_id = "'.$get_faculty_value->assistant_id.'"';
							if(!empty($get_faculty_value->online_class_type)){
							$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','start_classes.topic_name')
													  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													  ->where('timetables.online_class_type', $get_faculty_value->online_class_type)
													  ->where('timetables.time_table_parent_id', '0')
													  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
													  
							   
							$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)->get();
													  
													  //echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							
							
							if(count($get_faculty_timetable) > 0){ 
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								$total_base_schedule->add($schedule_interval); 
								$final_total_schedule->add($schedule_interval);
								
								if($get_faculty_timetable_value->is_cancel != '1'){
									$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$interval = $first_date->diff($second_date);
									$duration = $interval->format('%H : %I Hours');
									$base_time->add($interval);
									$final_total_spent->add($interval);
								}
								else{
									$total_cancel_class++;
									$duration = 'Cancelled Classes';
									
									$total_base_cancel->add($schedule_interval); 
								}
								
								
								
								 
								
							?>
							<?php
							}
							}
							}
							?>
							
							<tr> 
								<td><b><?php echo isset($get_faculty_value->online_class_type)?$get_faculty_value->online_class_type:''; ?></b> 
								</td> 
								<td><b>{{ $get_faculty_value->classCount }}</b></td>
								<td><b></b> 
								<?php
								$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
								$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</td> 
								<td><b></b> 
								<?php
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</td>
								 						
							</tr>
								
						
					
				<?php 
					}
				}
				?>
				</body>
					
					</table>
					<p><hr/></p>
				<p> <b>
					<?php
					$totalDays = $final_total_schedule_last->diff($final_total_schedule)->format("%a");
					$totalHours = $final_total_schedule_last->diff($final_total_schedule)->format("%H");
					$totalMinute = $final_total_schedule_last->diff($final_total_schedule)->format("%I");
					?>
					Search According Total Schedule Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours 
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					
					<?php
					$totalDays = $final_total_spent_last->diff($final_total_spent)->format("%a");
					$totalHours = $final_total_spent_last->diff($final_total_spent)->format("%H");
					$totalMinute = $final_total_spent_last->diff($final_total_spent)->format("%I");
					?>
					
					Search According Total Spent Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours </b></p>
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


<script type="text/javascript">
	$("body").on("change", ".start_time,.end_time", function (e) {
		var start_time = $('.start_time').val();
		var end_time = $('.end_time').val();
		var str0="01/01/1970 " + start_time;
		var str1="01/01/1970 " + end_time;

		var diff=(Date.parse(str1)-Date.parse(str0))/1000/60;
		var hours=String(100+Math.floor(diff/60)).substr(1);
		var mins=String(100+diff%60).substr(1);
		$(".total_time").text(hours+':'+mins);
	});
</script>
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
		$('.select-multiple4').select2({
			width: '100%',
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
			data.branch_id = $('.branch_id').val(),
			data.branch_location = $('.branch_location').val(),
			data.faculty_id = $('.faculty_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.batch_id = $('.batch_id').val(), 
			data.status = $('.status').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/class-type-wise-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">

	$(".cancel-cls").on("click", function(){
		var remark = $(".remark").val();
		var start_time = $(".start_time").val();
		var end_time = $(".end_time").val();
		var tt_id = $(".timetable_id").val();
		var spent_id = $(".spent_id").val();
		if(remark != ''){
			 $.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.update-cancel-class') }}',
				data : {'_token' : '{{ csrf_token() }}', 'remark': remark, 'start_time': start_time, 'end_time': end_time, 'timetable_id': tt_id, 'spent_id': spent_id},
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
						
					} else if(data.status == true){
						$('.spd_time'+data.spent_id).text(data.spd_time);
						$('#submit_start_class_form').trigger("reset");	
						$('#s_class').modal('hide');
						swal("Done!", data.message, "success").then(function(){ 
							// location.reload();
						});
					}
				}
			});
		
		}
		else{
			alert("Remark are required")
		}
	});
	
	$(".get_start_class_data").on("click", function() {
		$(".timetable_id").val('');
		$(".start_time").val('');
		$(".end_time").val('');
		$(".topic_name").val('');
		$(".remark").val('');
		var tt_id = $(this).attr("data-id");
		var spnt_id = $(this).attr("data-spent-id");
		$(".timetable_id").val(tt_id);
		$(".spent_id").val(spnt_id);
		
		
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
					$(".total_time").text(data.total_spent_time);
					if(data.start_time != ''){
						$('.start_time').val(data.start_time);
					}
					if(data.end_time != ''){
						$('.end_time').val(data.end_time);
					}
					if(data.topic_name != ''){
						$('.topic_name').val(data.topic_name);
					}
					if(data.remark != ''){
						$('.remark').val(data.remark);
					}
					if(data.html != ''){
						$('.htmlset').html(data.html);
					}
					if(data.subject_name != ''){
						$('.subject_name_set').text(data.subject_name);
					}
					if(data.res != ''){
						$('.assistant_id').html('');
						$('.assistant_id').html(data.res);
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
			topic_name : {
				required: true,
			},
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
						$('.spd_time'+data.spent_id).text(data.spd_time);
						$('#submit_start_class_form').trigger("reset");	
						$('#s_class').modal('hide');
						swal("Done!", data.message, "success").then(function(){ 
							// location.reload();
						});
					}
				}
			});
		}       
	});
					

	
	/* $(".branch_id").on("change", function () {
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
	}); */
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $(".faculty_id_get").val();
		/* if (branch_id) {
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
		} */
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
	
	$(document).on("change",".chapter_data", function () {
		var option = $('option:selected', this).attr('data-chname');
		$(this).parents('.row').find('.topic_name').val(option);
	});
	
</script>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>$('.dateddmmyyy').datepicker({ dateFormat: 'dd-mm-yy' }).val();


</script>


@endsection
