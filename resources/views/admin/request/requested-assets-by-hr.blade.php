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
						<h2 class="content-header-title float-left mb-0">Requested Asset By HR Team</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="{{ route('admin.request.requested-asset-by-hr') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">Employee Code</label>
												<input type="text" class="form-control mrl" name="emp_code" placeholder="Employee Code" value="{{ app('request')->input('emp_code') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6 pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.requested-asset-by-hr') }}" class="btn btn-warning">Reset</a>
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
								<th>Department</th>
								<th>Asset Required</th>																
							</tr>
						</thead>
						<tbody>			
							<?php $i =1;
							foreach($users as $us){ ?>
							<tr>
								<td>{{ $i }}</td>
								<td>{{ $us->register_id }}</td>
								<td>{{ $us->name }}</td>								
								<td>{{ $us->dname }}</td>								
								<td>{{ $us->asset_requirement }}</td>								
							</tr>
							<?php $i++; } ?>
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
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});	
</script>
@endsection