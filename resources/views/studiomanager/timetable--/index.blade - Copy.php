@extends('layouts.admin')
<style type="text/css">
	.set_time{
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(-90deg);
		display: inline-block;
		font-size: 9px;
		margin: -4.7px;
		margin-top: 2px;
	}
	.ui-timepicker-container.ui-timepicker-standard.ui-timepicker-no-scrollbar {
		z-index: 9999999 !important;
	}
	.table thead th {
		border-bottom: 0px solid #f8f8f8 !important;
	}
	.table th, .table td {
		padding: 8px 6px !important;
	}
	.btn-sm, .btn-group-sm > .btn {
		padding: 8px 8px !important;
	}
	.faculty-set{
		/*width: 7%;*/
		float: left;
		border: 1px solid #ececec;
		padding: 8px 8px;

	}
	.faculty-set p{
		font-size: 12px;
		font-weight: bold;
		text-align: center;
		margin-bottom: 2px;
	}

	.faculty-set span{
		font-size: 8px;
		font-weight: bold;
		width: 100%;
		text-align: center;
		display: inline-block;
	}

	.from-time{
		border-left: 2px solid #7367f0 !important;
	}
	.to-time{
		border-right: 2px solid #7367f0 !important;
	}
</style>
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Time Table</h2>
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
								<form action="{{ route('admin.timetable.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Branch</label>
											<fieldset class="form-group">
												<select class="form-control get_branch" name="branch">
													<option value=""> - Select Branch - </option>
													<option value="Branch 1" @if(app('request')->input('branch') == 'Branch 1') selected="selected" @endif>Branch 1</option>
													<option value="Branch 2" @if(app('request')->input('branch') == 'Branch 2') selected="selected" @endif>Branch 2</option>
													<option value="Branch 3" @if(app('request')->input('branch') == 'Branch 3') selected="selected" @endif>Branch 3</option>
													<option value="Branch 4" @if(app('request')->input('branch') == 'Branch 4') selected="selected" @endif>Branch 4</option>
													<option value="Branch 5" @if(app('request')->input('branch') == 'Branch 5') selected="selected" @endif>Branch 5</option>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<fieldset class="form-group">
												<?php $faculty = \App\User::where('role_id', '2')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control" name="faculty_id">
													<option value=""> - Select Faculty - </option>
													@if(count($faculty) > 0)
													@foreach($faculty as $value)
													<option value="{{ $value->id }}" @if(old('faculty_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Date</label>
											<fieldset class="form-group">
												<input type="date" name="date" placeholder="Date" class="form-control">
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row" id="table-responsive">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<table class="table table-responsive mb-0">
									<thead>
										<tr>											
											<th scope="col">Studio\Time</th>
											<th scope="col" style="width: 100%;display: inline-flex;">
												@if(count($timeslots))
												@foreach($timeslots as $time)
												<div class="set_time" id="{{ $time->id }}">{{ $time->time_slot }}</div>
												@endforeach
												@endif											
											</th>
										</tr>
									</thead>
									<tbody>
										<?php $get_studios =  \App\Studio::where('branch', app('request')->input('branch'))->orderBy('id', 'desc')->get(); ?>
										@if(count($get_studios) > 0)
										@foreach($get_studios as $value)
										<tr>
											<th scope="row text-center">
												<p>{{ $value->name }}</p>						
												<a href="#myModal" data-toggle="modal" studioname="{{ $value->name  }} - Set Class" data-id="{{ $value->id }}" class="btn btn-sm btn-primary get_studio_data">
													Set Class
												</a>
											</th>
											<td>
												<?php $faculty_timetables = \App\Timetable::with('faculty')->where('studio_id', $value->id)->orderBy('from_time', 'asc')->get();
												?>
												@if(count($faculty_timetables) > 0)
												@foreach($faculty_timetables as $faculty)
												@foreach($timeslots as $time)
												@if($faculty->from_time == $time->time_slot)
												<?php
												$w = 14.7;
												//$mr = 12.9;
												$start=strtotime($faculty->from_time);
												$end=strtotime($faculty->to_time);
												$number = 0;
												for ($i=$start;$i<=$end;$i = $i + 5*60)
												{
													$number++;
												}
												$width = $w*$number;
												//$marginright = $mr*$number;
												//$marginleft = $ml*$number;
												//echo $number;
												//echo $time->time_slot;
												?>
												<div class="faculty-set from-time to-time" style="width: {{$width}}px; margin:0px 7px; ">
													<p>{{ $faculty->faculty->name }}</p>
													<span>{{ $faculty->from_time }} - {{ $faculty->to_time }}</span><br>
													<a href="#" class="btn btn-sm btn-primary mt-1">
														Reschedule
													</a>
													<a href="#" class="btn btn-sm btn-primary mt-1">
														Swap
													</a>
													<a href="#" class="btn btn-sm btn-primary mt-1">
														Delete
													</a>
												</div>				
												@endif									
												@endforeach
												@endforeach
												@endif												
											</td>
										</tr>
										@endforeach
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>              
			</section>
		</div>
	</div>
</div>
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="submit_timetable_form">
				<div class="modal-header">
					<h5 class="modal-title studioname"></h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<?php $faculty = \App\User::where('role_id', '2')->orderBy('id', 'desc')->get(); ?>
									<select class="form-control" name="faculty_id">
										<option value=""> - Select Faculty - </option>
										@if(count($faculty) > 0)
										@foreach($faculty as $value)
										<option value="{{ $value->id }}">{{ $value->name }}</option>
										@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<?php $batchs = \App\Batch::orderBy('id', 'desc')->get(); ?>
									<select class="form-control" name="batch_id">
										<option value=""> - Select Batch - </option>
										@if(count($batchs) > 0)
										@foreach($batchs as $batch)
										<option value="{{ $batch->id }}">{{ $batch->name }}</option>
										@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-6 col-12" id="course_loader">
								<div class="form-label-group">
									<?php $courses = \App\Course::orderBy('id', 'desc')->get(); ?>
									<select class="form-control course_id" name="course_id">
										<option value=""> - Select Course - </option>
										@if(count($courses) > 0)
										@foreach($courses as $course)
										<option value="{{ $course->id }}">{{ $course->name }}</option>
										@endforeach
										@endif
									</select>
									<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
								</div>
							</div>
							<div class="col-md-6 col-12" id="subject_loader">
								<div class="form-label-group">
									<select class="form-control subject_id" name="subject_id">
										<option value=""> - Select Subject - </option>
									</select>
									<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
								</div>
							</div>
							<div class="col-md-6 col-12" id="chapter_loader">
								<div class="form-label-group">
									<select class="form-control chapter_id" name="chapter_id">
										<option value=""> - Select Chapter - </option>
									</select>
									<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<select class="form-control topic_id" name="topic_id">
										<option value=""> - Select Topic - </option>
									</select>
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ old('from_time') }}" autocomplete="off">
									<label for="first-name-column">From Time</label>
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ old('to_time') }}" autocomplete="off">
									<label for="first-name-column">To Time</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="studio_id" class="studio_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="timetable_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>


@endsection

@section('scripts')
<script type="text/javascript">
	$('.get_branch').on('change', function() {
		$("form[name='filtersubmit']").submit();
	});
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('focus', '.timepicker', function(){
			$(this).timepicker({
				interval: 5,
				timeFormat: 'HH:mm',
				minTime: '4:00',
				maxTime: '22:00',
			});
		});
	}); 
</script>
<script type="text/javascript">
	$(function() {
		$(".get_studio_data").on("click", function() {
			var studio_id = $(this).attr("data-id");
			var studioname = $(this).attr("studioname");
			$(".studio_id").val(studio_id);
			$(".studioname").text(studioname);
		})     
	})
</script>
<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-subject') }}',
				data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id},
				dataType : 'html',
				success : function (data){
					$("#course_loader i").hide();
					$('.subject_id').empty();
					$('.subject_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	$(".subject_id").on("change", function () {
		var subject_id = $(".subject_id option:selected").attr('value');
		if (subject_id) {
			$.ajax({
				beforeSend: function(){
					$("#subject_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-chapter') }}',
				data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id},
				dataType : 'html',
				success : function (data){
					$("#subject_loader i").hide();
					$('.chapter_id').empty();
					$('.chapter_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	$(".chapter_id").on("change", function () {
		var chapter_id = $(".chapter_id option:selected").attr('value');
		if (chapter_id) {
			$.ajax({
				beforeSend: function(){
					$("#chapter_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-topic') }}',
				data : {'_token' : '{{ csrf_token() }}', 'chapter_id': chapter_id},
				dataType : 'html',
				success : function (data){
					$("#chapter_loader i").hide();
					$('.topic_id').empty();
					$('.topic_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	var $form = $('#submit_timetable_form');
	validatorprice = $form.validate({
		ignore: [],
		rules: {
			faculty_id : {
				required: true,                
			},
			batch_id : {
				required: true,               
			},
			course_id : {
				required: true,
			},
			subject_id : {
				required: true,
			},
			chapter_id : {
				required: true,
			},
			topic_id : {
				required: true,
			},
			from_time : {
				required: true,
			},
			to_time : {
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

	$("#submit_timetable_form").submit(function(e) {
		var form = document.getElementById('submit_timetable_form');
		var dataForm = new FormData(form);
		e.preventDefault();
		if(validatorprice.valid()){
			$('#timetable_btn').attr('disabled', 'disabled');
			$.ajax({
				beforeSend: function(){
					$("#timetable_btn i").show();
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.timetable.store') }}',
				data : dataForm,
				processData : false, 
				contentType : false,
				dataType : 'json',
				success : function(data){
					//console.log(data);
					if(data.status == false){
						swal("Error!", data.message, "error");
						$('#timetable_btn').removeAttr('disabled');
						$("#timetable_btn i").hide();
					} else if(data.status == true){
						$('#submit_timetable_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
						$('#timetable_btn').removeAttr('disabled');
						$("#timetable_btn i").hide();
					}
				}
			});
		}       
	});
</script>
@endsection
