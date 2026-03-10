@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Probation Month List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.employees.probation-month') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif">
											</fieldset>
										</div>
										<div class="col-md-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.employees.probation-month') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Employee Code</th>
								<th>Employee Name</th>
								<th>Email</th>
								<th>Contact No</th>
								<th>Probation Last Date</th>
							</tr>
						</thead>
						<tbody>
							@if(count($probation_result) > 0)
							@foreach($probation_result as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->register_id) ? $value->register_id : '' }}</td>
								<td class="product-category">{{ !empty($value->name) ? $value->name : '' }}</td>
								<td class="product-category">{{ !empty($value->email) ? $value->email : '' }}</td>
								<td class="product-category">{{ !empty($value->mobile) ? $value->mobile : '' }}</td>
								<td class="product-category">{{ !empty($value->user_details) ? date('d-m-Y',strtotime($value->user_details->probation_from)) : '' }}</td>
							</tr>
							@endforeach
							@else
							<tr><td class="text-center text-primary" colspan="10">No Record Found</td></tr>	
							@endif
						</tbody>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_excel", function (e) {
		 //alert('efvhgfsh');
	 	var data = {};
			data.search    = $('.search').val(),
	 		data.year_wise_month    = $('.year_wise_month').val(),  
	 		window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-probation-excel?" + Object.keys(data).map(function (k) {
	 		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	 	}).join('&');
	 });
</script>
@endsection