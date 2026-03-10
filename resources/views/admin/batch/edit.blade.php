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

										<!-- @method('PATCH') -->

										@csrf

										<div class="form-body">
											<div class="row">
												<div class="col-md-3 col-12" id="course_loader">
													<div class="form-group">
														<label>Courses</label>
														<?php 
														  //$courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
														  $courses = \App\Course::where('id',$batch->course_id)->get();
														 ?>

														@if(count($courses) > 0)
															<!-- <select class="form-control course_id select-multiple1" name="course_id" required> -->
															<select class="form-control" name="course_id" required readonly>
																<!-- <option value=""> - Select Course - </option> -->
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

												<div class="col-md-3 col-12">
													<div class="form-group">
														<label>Batch Name</label>
														<input type="text" name="name" class="form-control" value="{{ old('name', $batch->name) }}" placeholder="Batch Name" readonly required>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>Batch Code</label>
														<input type="text" name="batch_code" class="form-control" value="{{ old('batch_code', $batch->batch_code) }}" placeholder="Batch Code" required readonly>
													</div>
												</div>

												<div class="col-md-2 col-12 capacity_div">
													<div class="form-group">
														<label>Branch</label>
														<input type="text" name="branch" class="form-control" value="{{ old('branch',$batch->branch) }}" placeholder="branch" readonly>
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>Start Date</label>
														<input type="date" name="start_date" class="form-control" value="{{ old('start_date',$batch->start_date) }}" placeholder="Start Date">
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>End Date</label>
														<input type="date" name="end_date" class="form-control" value="{{ old('end_date',$batch->end_date) }}" placeholder="End Date" required>
													</div>
												</div>

												
												<div class="col-md-2 col-12 capacity_div">
													<div class="form-group">
														<label>Capacity</label>
														<input type="text" name="capacity" class="form-control" value="{{ old('capacity', $batch->capacity) }}" placeholder="Capacity">
													</div>
												</div>
												
												<div class="col-md-2 col-12 capacity_div">
													<div class="form-group">
														<label>Total Test</label>
														<input type="number" name="total_test" class="form-control" value="{{ old('total_test', $batch->total_test) }}" placeholder="Total Test">
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>ERP COURSE ID</label>
														<input type="text" name="erp_course_id" class="form-control" value="{{ old('erp_course_id', $batch->erp_course_id) }}" placeholder="Course Id" required>
														@if($errors->has('erp_course_id'))
														<span class="text-danger">{{ $errors->first('erp_course_id') }} </span>
														@endif
													</div>
												</div>
												
												
												<div class="col-md-4 col-12 d-none">
													<div class="form-group">
														<label>Nickname</label>
														<input type="text" name="nickname" class="form-control" value="{{ old('nickname', $batch->nickname) }}" placeholder="Nickname">
													</div>
												</div>
												<div class="col-md-12 col-12" style="display:none;">
													<div class="form-group">
														<label>Venue</label>
														<input type="text" name="venue" class="form-control" value="{{ old('venue', $batch->venue) }}" placeholder="Venue / Location">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Type :</label>
														<label>
															<input type="radio" name="type" value="Live From Classroom" class="change_class" {{ ($batch->type == 'Live From Classroom') ? "checked" : ""}}>Live From Classroom
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="type" value="online" class="change_class" {{ ($batch->type == 'online') ? "checked" : ""}}>Online
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="type" value="offline" class="change_class" {{ ($batch->type == 'offline') ? "checked" : ""}}>Offline
														</label>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-md-4 col-12" id="">
													<div class="form-group">
														<label>Batch Chanakya</label>
														@if(count($faculty) > 0)
														<select class="form-control select-multiple2" name="mentor_id">
															<option value=""> - Select Chankiya - </option>
															@foreach($faculty as $value)
															<option value="{{ $value->id }}" @if($value->id == $batch->mentor_id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														@endif
														@if($errors->has('mentor_id'))
														<span class="text-danger">{{ $errors->first('mentor_id') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-4 col-12" id="">
													<div class="form-group">
														<label>Batch Chopal Agent</label>
													    <select class="form-control select-multiple3" name="chopal_agent_id">
															<option value=""> - Select Chopal Agent - </option>
															@foreach($faculty as $value)
															 <option value="{{ $value->id }}" @if($value->id == $batch->chopal_agent_id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
														</select>
														@if($errors->has('chopal_agent_id'))
														 <span class="text-danger">{{ $errors->first('chopal_agent_id') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12" id="">
													<div class="form-group">
														<label>Batch Category Head</label>
													    <select class="form-control select-multiple3" name="category_head">
															<option value=""> - Select Category Head - </option>
															@foreach($users as $value)
															 <option value="{{ $value->id }}" @if($value->id == $batch->category_head) selected="selected" @endif>{{ $value->name }}-{{ $value->register_id }}</option>
															@endforeach
														</select>
														@if($errors->has('category_head'))
														 <span class="text-danger">{{ $errors->first('category_head') }} </span>
														@endif
													</div>
												</div>
											</div>

											<div class="course_subjects">
													@if(isset($batch->batch_relations) && !empty($batch->batch_relations))
													<?php 
													$subject_ids = array();
													foreach($batch->batch_relations as $key => $course){ 
														if(!empty($course) && isset($course->subject_id) && !empty($course->subject_id)){
														  $subject_ids [] = $course->subject_id;
														?>
														<div class="row">
															<div class="col-md-3 col-12 mb-2">
																<div class="control-group input-group" style="padding-top: 6px;">
																	<select class="form-control" name="course[subject_id][]">
																		<option value="{{ $course->subject_id }}">
																		{{ isset($course->subject->name)?$course->subject->name:'' }}
																		</option>
																	</select>
																</div>
															</div>
															<div class='col-md-3 mb-2'>
																<div class='input-group'>
																	<select class="form-control" name="course[faculty_id][]">

																		<option value=""> - Select Faculty - </option>

																		@if(count($faculty) > 0)

																		@foreach($faculty as $f)

																		<option value="{{ $f->id }}">{{ $f->name }}</option>

																		@endforeach

																		@endif

																	</select>
																</div>
															</div>
															<div class="col-md-3 col-12 mb-2">
																<div class="control-group input-group" style="padding-top: 6px;">
																	<input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' value="{{$course->no_of_hours}}" step=0.01>
																</div>
															</div>

															<div class='col-md-2 col-12 mb-2 text-danger p-1 removeSubject'>X</div>
														</div>

													<?php } 

													} 
													?>

													

													<?php
                                                    if(!empty($course_id)){
														$subjects = CourseSubjectRelation::with('subject')->where('is_deleted','0')->where('course_id', $course_id->course_id)->whereNotIn('subject_id',$subject_ids)->get();
	                                                    $res = "";
														if (!empty($subjects)) {
															foreach($subjects as $key => $value) {
																$duration=0;
																$topic = DB::table('topic')
																	->select('*')
																	->where('course_id', $course_id->course_id)
																	->where('subject_id', $value->subject->id??0)
																	->where('status', 1)
																	->get();
																if(!empty($topic)){
																	foreach ($topic as $Tdetails){
																		$duration+= $Tdetails->duration;
																	}
																}
																$duration=intdiv($duration, 60);

																if(!empty($value->subject->id) && !empty($value->subject->name)){
																	$res.="<option 
																	value='".$value->subject->id."'
																	data-duration='".$duration."'
																	>".$value->subject->name."</option>";
															    }
															}
														} 
													} ?>

													@endif

													<?php if($res!=""){ ?>
														<div class="copy_subject">
															<div class="row">
																<div class='col-md-3 mb-2'>
																	<div class='input-group'>
																		<select class='form-control subjects' name='course[subject_id][]' required>
																			<option value=''>Select Subject ---</option>
																			<?php echo $res;?>
																		</select>
																	</div>
																</div>
																<div class='col-md-3 mb-2'>
																	<div class='input-group'>
																		<select class="form-control" name="course[faculty_id][]">

																			<option value=""> - Select Faculty - </option>

																			@if(count($faculty) > 0)

																			@foreach($faculty as $f)

																			<option value="{{ $f->id }}">{{ $f->name }}</option>

																			@endforeach

																			@endif

																		</select>
																	</div>
																</div>
																<div class='col-md-3 col-12 mb-2'>
																	<div class='input-group'>
																		<input type='number' class='form-control cal_hour' name='course[no_of_hours][]' value="" placeholder='No of hours' step=0.01 required>
																	</div>
																</div>
																<div class='col-md-2 col-12 mb-2 text-danger p-1 removeSubject'>X</div>
															</div>
														</div>
												    <?php } ?>
											</div>
                                            
                                            <?php if($res!=""){ ?>
											<div class="row text-right float-right"><span class="btn btn-primary subject_addMore" data-row="<?=count($subject_ids)+1;?>">Add More</span></div>
											<div class="clearfix"></div>
										    <?php } ?>



											<div class="row">
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
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="2" {{ ($batch->status == 2) ? "checked" : ""}}>
															Completed
														</label>

													</div>
												</div> 

												<?php if(Auth::user()->id == 8866 || Auth::user()->id == 901){ ?>
												<div class="col-md-4 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Course Planer Enable :</label>
														<label>
															<input type="radio" name="course_planer_enable" value="1" {{ ($batch->course_planer_enable == 1) ? "checked" : ""}}>
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="course_planer_enable" value="0" {{ ($batch->course_planer_enable == 0) ? "checked" : ""}}>
															No
														</label>
													</div>													
												</div>    
												<?php } ?>

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
	$(".subject_addMore").on("click",function(){
       $(".course_subjects").append($(".copy_subject").html());
       var rowNo=$(".subject_addMore").data("row");
	   $(".subject_addMore").data("row",rowNo+1);		

	});

	$(document).on("click",".removeSubject",function(){
		var rowNo=$(".subject_addMore").data("row");
		if(rowNo!=1){
			$(".subject_addMore").data("row",rowNo-1);
            $(this).parent(".row").remove();
        }
	});
    
    var add_subjects="";
	$(document).on("change",".subjects",function(){
        subjects=add_subjects.split('$$'); 
		if(jQuery.inArray($(this).val(),subjects)==-1){
		  add_subjects=add_subjects+"$$"+$(this).val();

		  var duration=$('option:selected',this).attr("data-duration");
		  var ddd=$(this).closest(".row");
		  $(ddd).find(".cal_hour").val(duration);
		  //alert(duration);
	    }else{
	    	alert('Subject Already Selected');
	    	$(this).val('');
	    }
	});
	
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

		$('.select-multiple3').select2({
			placeholder: "- Select Chopal Agent -",
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