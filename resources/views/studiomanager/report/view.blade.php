@extends('layouts.studiomanager')
<style type="text/css">
	.modal-dialog {
		max-width: 622px !important;
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
						<h2 class="content-header-title float-left mb-0">View Faculty Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">View Faculty Report</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('studiomanager.reports.index') }}" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<!-- page users view start -->
			<section class="page-users-view">
				<div class="row">
					<!-- account start -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title pb-1">{{ $faculty_report->timetable->faculty->name }} - Report</div>
							</div>
							<div class="table-responsive">
								<table class="table data-list-view">
									<thead>
										<tr>
											<th>S. No.</th>
											<th>Batch Name</th>
											<th>Course Name</th>
											<th>Subject Name</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										@if($faculty_report->timetable->faculty_id)
										<?php
										$get_facultywise_batch_course = \App\Batchrelation::with('batch','subject')->whereFacultyId($faculty_report->timetable->faculty_id)->get();
										?>
										@endif
										@if(count($get_facultywise_batch_course) > 0)							
										@foreach($get_facultywise_batch_course as  $key => $value)
										<tr>
											<td>{{ $key + 1 }}</td>
											<td>{{ isset($value->batch->name) ? $value->batch->name : '' }}</td>
											<td>{{ isset($value->batch->course->name) ? $value->batch->course->name : '' }}</td>
											<td>{{ isset($value->subject->name) ? $value->subject->name : '' }}</td>
											<td>
												<a href="#" class="get_subject_report" data-fid="{{ $faculty_report->timetable->faculty_id }}" data-sid="{{ $value->subject->id }}" data-toggle="modal" data-target="#reportview">
													<span class="action-edit"><i class="feather icon-eye"></i></span>
												</a>
											</td>								
										</tr>
										@endforeach
										@endif			
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</section>
			<div id="reportview" class="modal fade">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">Subject Report</h3>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-lg-12" id="report_data">
								</div>
							</div>
							<hr>    
							<div class="form-group">
								<div>                
									<button type="button" class="btn btn-default" id="btnclose" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/studiomanager/css/app-user.css') }}">
<script type="text/javascript">
	$(".get_subject_report").on("click", function () {
		var fid = $(this).attr("data-fid");
		var sid = $(this).attr("data-sid");
		if (fid) {
			$.ajax({
				type : 'POST',
				url : '{{ route('studiomanager.subject.report') }}',
				data : {'_token' : '{{ csrf_token() }}', 'fid': fid, 'sid': sid},
				dataType : 'html',
				success : function (data){
					console.log("data", data);
					$('#report_data').empty();
					$('#report_data').append(data);
				}
			});
		}
	});
</script>
@endsection
