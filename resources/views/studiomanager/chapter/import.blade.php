@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Import Chapter</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Import Chapter
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('studiomanager.chapters.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('studiomanager.chapter.import.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12" id="course_loader">
													<div class="form-group">
														<label>Course</label>
														<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get(); ?>
														@if(count($courses) > 0)
														<select class="form-control select-multiple-course course_id" name="course_id" required>
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
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Subjects</label>
														<select class="form-control subject_id select-multiple1" name="subject_id" required>
															<option value=""> - Select Subject - </option>
														</select>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Import File</label>
														<input type="file" class="form-control" name="import_file" required>
														@if($errors->has('import_file'))
														<span class="text-danger">{{ $errors->first('import_file') }}</span>
														@endif
														<br>
														<a href="{{ asset('laravel/public/chapters-import-sample.xlsx') }}" class="download_sample">Download Sample</a>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>

$(document).ready(function() {
	$('.select-multiple-course').select2({
		placeholder: " Select Course",
		allowClear: true
	});
	$('.select-multiple1').select2({
		placeholder: " Select Subject",
		allowClear: true
	});
});	

$(".course_id").on("change", function () {
	var course_id = $(".course_id option:selected").attr('value');
	if (course_id) {
		$.ajax({
			beforeSend: function(){
				$("#course_loader i").show();
			},
			type : 'POST',
			url : '{{ route('studiomanager.get-batch-subject') }}',
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
@endsection
