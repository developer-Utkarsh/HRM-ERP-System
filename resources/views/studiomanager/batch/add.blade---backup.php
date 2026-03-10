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
						<h2 class="content-header-title float-left mb-0">Add Batch</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Batch
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
									<form class="form" action="{{ route('studiomanager.batch.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12" id="course_loader">
													<div class="form-group">
														<label>Courses</label>
														<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get(); ?>
														@if(count($courses) > 0)
														<select class="form-control course_id select-multiple1" name="course_id" required>
															<option value=""> - Select Course - </option>
															@foreach($courses as $value)
															<option value="{{ $value->id }}">{{ $value->name }}</option>
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
														<input type="text" name="batch_code" class="form-control" value="{{ old('batch_code') }}" placeholder="Batch Code" required>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label>Nickname</label>
														<input type="text" name="nickname" class="form-control" value="{{ old('nickname') }}" placeholder="Nickname" required>
													</div>
												</div>
												<div class="col-md-12 col-12" style="display:none;">
													<div class="form-group">
														<label>Venue</label>
														<input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="Venue / Location">
													</div>
												</div>
												
											</div>
											<div class="row course_subjects">
												 
											</div>
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Batch Name</label>
														<input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Batch Name" required>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Start Date</label>
														<input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" placeholder="Start Date" min="<?=date("Y-m-d")?>" required>
													</div>
												</div>
												<div class="col-md-6 col-12" style="display:none;">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Type :</label>
														<label>
															<input type="radio" name="type" value="online" checked>
															Online
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="type" value="offline">
															Offline
														</label>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" checked>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0">
															Inactive
														</label>
													</div>
												</div>                                       
												<div class="col-10">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
												<div class="col-md-2 count_div hide">
													<h3>Total Hours: <span id="count_hour">0</span></h3>
												</div>	
											</div>
										</div>
									</form>
									<!--div class="copy-fields hide">
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
									</div-->
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
