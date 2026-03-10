@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Subject Planner</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-6 col-12 d-md-block">
				<a href="{{ route('admin.multi-course-planner.add-topic',[$id]) }}"><button class="btn btn-primary" type="button">Add Topic</button></a>
				<a href="{{ route('admin.multi-course-planner.add-sub-topic',[$id]) }}"><button class="btn btn-primary" type="button">Add Sub Topic</button></a>
			</div>
		</div>
		<div><b>Subject Name</b> : {{ $record[0]->subject_name ?? '' }}</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>S. No.</th>
													<th>Topic</th>
													<th>Sub Topic</th>
													<th>Status</th>
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
													<td>{{ $re->topic_name }}</td>
													<td>{{ $re->name }}</td>
													<td>{{ $re->status == 1 ? 'Active' : 'Inactive' }}</td>

													<td>
														<a href="{{ route('admin.multi-course-planner.edit-sub-topic', $re->id) }}">
															<span class="action-edit"><i class="feather icon-edit"></i></span>
														</a>
														&nbsp;
														<a href="{{ route('admin.multi-course-planner.delete-sub-topic', $re->id) }}" onclick="return confirm('Are You Sure To Delete This Record')">
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
