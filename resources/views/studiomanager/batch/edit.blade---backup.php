<?php

use App\CourseSubjectRelation;

?>

@extends('layouts.studiomanager')

<style type="text/css">

	.hide {

		display: none!important;

	}

</style>

@section('content')



<div class="app-content content">

	<div class="content-overlay"></div>

	<div class="header-navbar-shadow"></div>

	<div class="content-wrapper">

		<div class="content-header row">

			<div class="content-header-left col-md-12 col-12 mb-2">

				<div class="row breadcrumbs-top">

					<div class="col-8">

						<h2 class="content-header-title float-left mb-0">Edit Batch</h2>

						<div class="breadcrumb-wrapper col-12">

							<ol class="breadcrumb">

								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>

								</li>

								<li class="breadcrumb-item active">Edit Batch

								</li>

							</ol>

						</div>

					</div>

					<div class="col-4 text-right">

						<a href="{{ route('studiomanager.batch.index') }}" class="btn btn-primary mr-1">Back</a>

					</div>

				</div>

			</div>

			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">

			</div>

		</div>

		<div class="content-body">

			<section id="multiple-column-form">

				<div class="row match-height">

					<div class="col-12">

						<div class="card">

							<div class="card-content">

								<div class="card-body">

									<form class="form" action="{{ route('studiomanager.batch.update', $batch->id) }}" method="post" enctype="multipart/form-data">

										@method('PATCH')

										@csrf

										<div class="form-body">

											<div class="row">

												<div class="col-md-4 col-12" id="course_loader">

													<div class="form-group">

														<label>Courses</label>

														<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get(); ?>

														@if(count($courses) > 0)

														<select class="form-control course_id select-multiple1" name="course_id">

															<option value=""> - Select Course - </option>

															@foreach($courses as $value)

															<option value="{{ $value->id }}" @if($value->id == $batch->course_id) selected="selected" @endif>{{ $value->name }}</option>

															@endforeach

														</select>

														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>

														@endif

														@if($errors->has('course_id'))

														<span class="text-danger">{{ $errors->first('course_id') }} </span>

														@endif

													</div>

												</div>

												

												<div class="col-md-4 col-12">

													<div class="form-group">

														<label>Batch Code</label>

														<input type="text" name="batch_code" class="form-control" value="{{ old('batch_code', $batch->batch_code) }}" placeholder="Batch Code" required>

													</div>

												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label>Nickname</label>
														<input type="text" name="nickname" class="form-control" value="{{ old('nickname', $batch->nickname) }}" placeholder="Nickname" required>
													</div>
												</div>
												
												<div class="col-md-12 col-12" style="display:none;">
													<div class="form-group">
														<label>Venue</label>
														<input type="text" name="venue" class="form-control" value="{{ old('venue', $batch->venue) }}" placeholder="Venue / Location">
													</div>
												</div>

												

											</div>

											<div class="row course_subjects">

													@if(isset($batch->batch_relations) && !empty($batch->batch_relations))

													<?php 

													$subject_ids = array();

													foreach($batch->batch_relations as $key => $course) 

													{ 

														if(!empty($course)) 

														{ ?>

														<div class="col-md-6 col-12 mb-2 ">

															<div class="control-group input-group" style="padding-top: 6px;">

																<?php if(isset($course->subject_id) && !empty($course->subject_id)){

																	$subject_ids [] = $course->subject_id;

																	?>

																<select class="form-control" name="course[subject_id][]">

																	<option value="{{ $course->subject_id }}">

																	{{ isset($course->subject->name)?$course->subject->name:'' }}

																	</option>

																</select>

																<?php } 

																else{

																	?>

																	<select class="form-control subject_id" name="course[subject_id][]">

																		<option value=""> - Select Subject - </option>

																	</select>

																	<?php

																	} 

																?>
															</div>

														</div>
														<div class="col-md-6 col-12 mb-2 ">
														
														
															<div class="control-group input-group" style="padding-top: 6px;">

																<input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' value="<?=(isset($course->no_of_hours))?$course->no_of_hours:'50'?>" step=0.01>

															</div>

														</div>

													<?php 

														} 

													} ?>

													

													<?php
													
													/* $faculty_options = "<option value=''> - Select Faculty - </option>";

													if(!empty($faculty)){

														foreach($faculty as $fval){

															$faculty_options .= "<option value='".$fval->id."'>".$fval->name."</option>";

														}

													}
													*/

													/*if(!empty($course_id)){
													$subjects = CourseSubjectRelation::with('subject')->where('course_id', $course_id->course_id)->where('is_deleted', '0')->get();

													

													$res = "";
   
													if (!empty($subjects)) {

														foreach ($subjects as $key => $value) {
															if(!empty($value->subject->id)){
																if(!in_array($value->subject->id,$subject_ids)){

																	if(!empty($value->subject->name) && !empty($value->subject->name)){
																	
																		$res .= "<div class='col-md-6 mb-2'><div class='input-group'><select class='form-control' name='course[subject_id][]'><option value='".$value->subject->id."'>".$value->subject->name."</option></select></div></div><div class='col-md-6 col-12 mb-2'><div class='input-group'><input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' step=0.01></div></div>";

																	}

																}
															}
 
														}

														echo $res;

													} 
													} */

													?>

													@endif

													

													

											</div>

											</div>

											<div class="row">

												<div class="col-md-6 col-12">

													<div class="form-group">

														<label>Batch Name</label>

														<input type="text" name="name" class="form-control" value="{{ old('name', $batch->name) }}" placeholder="Batch Name">

														@if($errors->has('name'))

														<span class="text-danger">{{ $errors->first('name') }} </span>

														@endif

													</div>

												</div>

												<div class="col-md-6 col-12">

													<div class="form-group">

														<label>Start Date</label>

														@php

														$startdate = '';

														if($batch->start_date){

															$startdate = date('Y-m-d', strtotime($batch->start_date));

														}

														@endphp

														<input type="date" name="start_date" class="form-control" value="{{ old('start_date', $startdate) }}" placeholder="Start Date">

													</div>

												</div>

												<div class="col-md-6 col-12"  style="display:none;">

													<div class="form-group d-flex align-items-center">

														<label class="mr-2">Type :</label>

														<label>

															<input type="radio" name="type" value="online" {{ ($batch->type == 'online') ? "checked" : ""}}>

															Online

														</label>

														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

														<label>

															<input type="radio" name="type" value="offline" {{ ($batch->type == 'offline') ? "checked" : ""}}>

															Offline

														</label>

													</div>

												</div>

												<div class="col-md-6 col-12">

													<div class="form-group d-flex align-items-center">

														<label class="mr-2">Status :</label>

														<label>

															<input type="radio" name="status" value="1" {{ ($batch->status == 1) ? "checked" : ""}}>

															Active

														</label>

														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

														<label>

															<input type="radio" name="status" value="0" {{ ($batch->status == 0) ? "checked" : ""}}>

															Inactive

														</label>

													</div>

												</div>                                       

												<div class="col-md-10">

													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>

												</div>
												<div class="col-md-2 count_div hide">
													<h3>Total Hours: <span id="count_hour">0</span></h3>
												</div>
											</div>

										</div>

									</form>

									<div class="copy-fields hide">

										<div class="control-group input-group" style="padding-top: 6px;">

											<select class="form-control subject_id" name="course[subject_id][]">

												<option value=""> - Select Subject - </option>

											</select>

											<select class="form-control" name="course[faculty_id][]">

												<option value=""> - Select Faculty - </option>

												@if(count($faculty) > 0)

												@foreach($faculty as $f)

												<option value="{{ $f->id }}">{{ $f->name }}</option>

												@endforeach

												@endif

											</select>

											<div class="input-group-append" id="button-addon2">

												<button class="btn btn-danger remove" type="button">Remove</button>

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

			</section>

			<!-- // Basic Floating Label Form section end -->

		</div>

	</div>

</div>

@endsection



@section('scripts')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script type="text/javascript">
	 $(document).on('keyup', '.cal_hour', function(){
		var cnt = 0; 
		$('.count_div').removeClass('hide');
		$(".cal_hour").each(function() {  
			if($(this).val() != ''){
				cnt += parseFloat($(this).val());
			}
		});
		$('#count_hour').text(cnt);
	});
	
	$(document).ready(function() {

		$('.select-multiple1').select2({

			placeholder: "Select Course",

			allowClear: true

		});
		
		$('.select-multiple2').select2({
			placeholder: "- Select Faculty -",
			allowClear: true
		});

	});

</script>

<script type="text/javascript">

	$(document).ready(function() {

		$(".add-more").click(function(){ 

			var html = $(".copy-fields").html();

			$(".after-add-more").after(html);    

		});

		$("body").on("click",".remove",function(){

			$(this).parents(".control-group").remove();

		});

	}); 

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

				url : '{{ route('studiomanager.get-batch-subject-data') }}',

				data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id},

				dataType : 'html',

				success : function (data){

					$("#course_loader i").hide();

					$('.course_subjects').empty();

					$('.course_subjects').append(data);
					$('.select-multiple2').select2({
						placeholder: "- Select Faculty -",
						allowClear: true
					});

				}

			});

		}

	});

</script>

@endsection

