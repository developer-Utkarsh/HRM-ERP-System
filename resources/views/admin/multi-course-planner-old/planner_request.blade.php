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
						<h2 class="content-header-title float-left mb-0">Multi Course Planner {{ $plantext }}</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Request</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
				<a href="{{ route('admin.multi-course-planner.planner-request-view') }}"><button class="btn btn-primary" type="button">Planner Request View</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.multi-course-planner.save-planner-request') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-4">
													<label for="users-list-status">Course <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $course = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('name')->get(); 
														?>								
														<select class="form-control select-multiple2 course_id" name="course_id" required>
															<option value="">Select Any</option>
															@foreach($course as $key => $value)
																<option value="{{ $value->id }}" 
																	@if((!empty($getRequest->course_id) && $getRequest->course_id == $value->id) || (empty($getRequest->course_id) && old('course_id') == $value->id)) 
																		selected 
																	@endif>
																	{{ $value->name }}
																</option>
															@endforeach
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Planner Type Naming <sup class="text-danger">*</sup></label>
														<?php $planner_name = $getRequest->planner_name ?? old('planner_name'); ?>
														<input type="text" class="form-control"  name="planner_name" value="{{ $planner_name }}" required>
														@if($errors->has('planner_name'))
														<span class="text-danger">{{ $errors->first('planner_name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4">
													<label for="users-list-status">City Of Batch <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $locations = \App\Branch::select('branch_location as name')->where('status', '1')->where('is_deleted', '0')->orderBy('name')->groupby('branch_location')->get(); 
														?>								
														<select class="form-control select-multiple2 branch_location" name="branch_location" required>
															<option value="">Select Any</option>
															@foreach($locations as $key => $value)
																<option value="{{ $value->name }}"
																	@if((!empty($getRequest->city) && $getRequest->city == $value->name) || (empty($getRequest->city) && old('branch_location') == $value->name)) 
																		selected 
																	@endif>
																	{{ ucwords($value->name) }}
																</option>
															@endforeach
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Expected Batch Duration (In Days) <sup class="text-danger">*</sup></label>
														
														<?php $duration = $getRequest->duration ?? old('duration'); ?>
														<input type="number" class="form-control" name="duration" value="{{ $duration }}" required>
														@if($errors->has('duration'))
														<span class="text-danger">{{ $errors->first('duration') }} </span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Course Mode <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $mode = ['Online','Offline','Hybrid']; ?>								
														<select class="form-control select-multiple2 mode" name="mode" required>
															<option value="">Select Any</option>
															@foreach($mode as $value)
																<option value="{{ $value }}" 
																	@if((!empty($getRequest->mode) && $getRequest->mode == $value) || (empty($getRequest->mode) && old('mode') == $value)) 
																		selected 
																	@endif>
																	{{ $value }}
																</option>
															@endforeach
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Planner Timelines <sup class="text-danger">*</sup></label>
														
														<?php $timelines = $getRequest->timelines ?? old('timeline'); ?>
														<input type="date" class="form-control"  id="timeline" placeholder="Planner Timelines" name="timeline" value="{{ $timelines }}" required>
														@if($errors->has('timeline'))
														<span class="text-danger">{{ $errors->first('timeline') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Special Instructions</label>
														<?php $remark = $getRequest->remark ?? old('remark'); ?>
														<textarea name="remark" class="form-control">{{ $remark }}</textarea>
														@if($errors->has('remark'))
														<span class="text-danger">{{ $errors->first('remark') }} </span>
														@endif
													</div>
												</div>
												<div class="col-12">
													<input type="hidden" name="edit_id" value="{{ $getRequest->id ?? '' }}"/>
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
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
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

function selectAll() {
    $(".select-multiple1 > option").prop("selected", true);
    $(".select-multiple1").trigger("change");
}

function deselectAll() {
    $(".select-multiple1 > option").prop("selected", false);
    $(".select-multiple1").trigger("change");
}


</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("timeline");
        const today = new Date();
        today.setDate(today.getDate() + 7); // add 3 days
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    });
</script>
@endsection
