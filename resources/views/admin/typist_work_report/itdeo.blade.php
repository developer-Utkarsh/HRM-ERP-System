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
						<h2 class="content-header-title float-left mb-0">IT DEO Work Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.add-it-deo-work-report') }}" class="btn btn-primary text-white">Add</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.it-deo-work-report') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Employee</label>
											<?php $employee = \App\User::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id" id="">
													<option value="">Select Any</option>
													@if(count($employee) > 0)
													@foreach($employee as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('emp_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										
										 <div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.it-deo-work-report') }}" class="btn btn-warning">Reset</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>Task Name</th>
								<th>Number Of</th>
								<th>Paper Type</th>
								<th>Time</th>						
							</tr>
						</thead>
						<tbody>
							<?php if(count($it_deo) > 0){  foreach($it_deo as $val){ ?>
							<tr>
								<td>{{ $val->question }}</td>
								<td>{{ $val->number_of }}</td>
								<td>{{ $val->paper }}</td>
								<td>{{ $val->time }}</td>						
							</tr>
							<?php } }else{ ?>
							<tr>
								<td colspan="4" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>
						</body>
					</table>					 
				</div>       

			</section>
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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_typist_work_excel", function (e) {
		var data = {};
			data.emp_id = $('.emp_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/typist-work-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>

@endsection
