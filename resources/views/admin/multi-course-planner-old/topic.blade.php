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
						<h2 class="content-header-title float-left mb-0">Topic Listing</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
				<a href="{{ route('admin.multi-course-planner.add-topic') }}"><button class="btn btn-primary" type="button">Add</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										<form action="{{ route('admin.multi-course-planner.topic') }}" method="get" name="filtersubmit">
											<div class="row">
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="users-list-role">Name</label>
													<fieldset class="form-group">
														<input type="text" class="form-control" name="name" placeholder="Name" value="{{ app('request')->input('name') }}">
													</fieldset>
												</div>
												
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="users-list-status">Status</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple status" name="status">
															@php $status = array('1' => 'Active','2' => 'Inactive'); @endphp
															<option value="">Select Any</option>
															@foreach($status as $key => $value)
															<option value="{{ $key }}" @if( app('request')->input('status') != '' && $key == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
															@endforeach
														</select>												
													</fieldset>
												</div>												
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="" style="">&nbsp;</label>
													<fieldset class="form-group">		
													<button type="submit" class="btn btn-primary">Search</button>
													<a href="{{ route('admin.multi-course-planner.topic') }}" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>S. No.</th>
													<th>Subject</th>
													<th>Name</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$i =1;
													foreach($record as $re){
												?>
												<tr>
													<td>{{ $i }}</td>
													<td>{{ $re->subject_name }}</td>
													<td>{{ $re->name }}</td>
													<td>
														<a href="{{ route('admin.multi-course-planner.edit-topic', $re->id) }}">
															<span class="action-edit"><i class="feather icon-edit"></i></span>
														</a>
														&nbsp;
														<a href="{{ route('admin.multi-course-planner.delete-topic', $re->id) }}" onclick="return confirm('Are You Sure To Delete This Record')">
															<span class="action-delete"><i class="feather icon-trash"></i></span>
														</a>
													</td>
												</tr>
												<?php $i++; } ?>
											</tbody>
										</table>
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
        today.setDate(today.getDate() + 3); // add 3 days
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    });
</script>
@endsection
