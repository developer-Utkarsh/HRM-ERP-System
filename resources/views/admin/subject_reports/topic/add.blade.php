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
						<h2 class="content-header-title float-left mb-0">Add Topic</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Topic</a>
								</li>
							</ol>
						</div>
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
									<form class="form" action="{{ route('admin.topics.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12" id="course_loader">
													<div class="form-group">
														<label>Course</label>
														<?php $courses = \App\Course::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														@if(count($courses) > 0)
														<select class="form-control course_id" name="course_id">
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
												<div class="col-md-6 col-12" id="subject_loader">
													<div class="form-group">
														<label>Subject</label>
														<select class="form-control subject_id" name="subject_id">
															<option value=""> - Select Subject - </option>
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Chapter</label>
														<select class="form-control chapter_id" name="chapter_id">
															<option value=""> - Select Chapter - </option>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Topic Name</label>
														<input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Topic Name">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Duration</label>
														<input type="text" name="duration" class="form-control" value="{{ old('duration') }}" placeholder="Duration">
													</div>
												</div>												
												<div class="col-md-6 col-12 mt-2">
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
												<div class="col-12">
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
<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-batch-subject') }}',
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
@endsection
