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
						<h2 class="content-header-title float-left mb-0">Add Time Table</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Time Table</a>
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
									<form class="form" action="{{ route('admin.timetable.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<?php $studios = \App\Studio::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<select class="form-control" name="studio_id">
															<option value=""> - Select Studio - </option>
															@if(count($studios) > 0)
															@foreach($studios as $value)
															<option value="{{ $value->id }}" @if(old('studio_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-label-group">
														<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ old('from_time') }}">
														<label for="first-name-column">From Time</label>
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-label-group">
														<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ old('to_time') }}">
														<label for="first-name-column">To Time</label>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<?php $faculty = \App\User::where('role_id', '2')->orderBy('id', 'desc')->get(); ?>
														<select class="form-control" name="faculty_id">
															<option value=""> - Select Faculty - </option>
															@if(count($faculty) > 0)
															@foreach($faculty as $value)
															<option value="{{ $value->id }}" @if(old('faculty_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<select class="form-control" name="topic">
															<option value=""> - Select Topic - </option>
															<option value="Topic" @if(old('topic') == 'Topic') selected="selected" @endif>Topic</option>
														</select>
													</div>
												</div>												
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1">
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
													<button type="button" class="btn btn-primary mr-1 mb-1">Submit</button>
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
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('focus', '.timepicker', function(){
			$(this).timepicker({
				interval: 30,
			});
		});
	}); 
</script>
@endsection
