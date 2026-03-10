@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Add Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Course
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.course.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.course.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Course Name</label>
														<input type="text" class="form-control" placeholder="Course Name" name="name" value="{{ old('name') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Subjects</label>
														<?php $subjects = \App\Subject::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														@if(count($subjects) > 0)
														<select class="form-control select-multiple" multiple="multiple" name="subject_id[]">
															<option value=""> - Select Subjects - </option>
															@foreach($subjects as $value)
															<option value="{{ $value->id }}">{{ $value->name }}</option>
															@endforeach
														</select>
														@endif
														<a href="javascript:void(0);" class="add-more" style="float: right;">New Subject Add</a>
													</div>
												</div>
											</div>
											<div class="row show_fields after-add-more">
												
											</div>
											<div class="row">
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
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
									<div class="copy-fields hide" style="display:none;">
										<div class="col-md-6 form-group remove_rows">
											<div class="control-group input-group" style="padding-top: 6px;">
												<input type="text" name="subjects[]" class="form-control" placeholder="Subject Name" autocomplete="off">
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
		
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".after-add-more").append(html);    
		});
		
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_rows").remove();
		});
	});
</script>
@endsection
