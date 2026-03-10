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
						<h2 class="content-header-title float-left mb-0">Edit Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Course
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('studiomanager.course.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('studiomanager.course.chapter-topic-update') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Topic Name</label>
														<textarea name="tname" class="form-control" required>{{ $topic->name}}</textarea>
														@if($errors->has('tname'))
														<span class="text-danger">{{ $errors->first('tname') }} </span>
														@endif 
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Duration</label>
														<input type="number" name="tduration" class="form-control" value="{{ $topic->duration}}" required />
														@if($errors->has('tduration'))
														<span class="text-danger">{{ $errors->first('tduration') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status">
															<option value="1" @if($topic->status==1) selected @endif>Active</option>
															<option value="0" @if($topic->status==0) selected @endif>Inactive</option>
														</select>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<input type="hidden" name="topic_id" value="{{ $topic->id }}"/>
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
		$('.select-multiple').select2({
			placeholder: "Select Subjects",
			allowClear: true
		});
	});
</script>
@endsection
