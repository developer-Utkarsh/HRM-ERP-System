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
						<h2 class="content-header-title float-left mb-0">Sub Department</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="{{ route('admin.sub_department.create') }}" class="btn btn-primary float-right">Add Sub Department</a>
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
								<form action="{{ route('admin.sub_department.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<div class="form-group">
												<label for="first-name-column">Department</label>
												<select class="form-control select-multiple department_id" name="department_id">
													
													<option value="">Select Department</option>
													@if(count($department_list) > 0)
														@foreach($department_list as $value)
															<option value="{{$value->id}}" @if($value->id == app('request')->input('department_id')) selected="selected" @endif>{{$value->name}}</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Sub Department</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" placeholder="Name" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple status" name="status">
													@php $status = ['Inactive', 'Active']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-12 col-lg-12">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group float-right">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.sub_department.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Department</th>
								<th>Sub Department</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($sub_department) > 0)
							@foreach($sub_department as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->department) ? $value->department->name : '' }}</td>
								<td class="product-category">{{ !empty($value->name) ? $value->name : '' }}</td>
								<td>
									<a href="{{route('admin.sub_department.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a href="{{ route('admin.sub_department.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.sub_department.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Sub Department')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
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
		var data = {};
			data.name   = $('.name').val(),
			data.status = $('.status').val(), 
			window.location.href = "<?php echo URL::to('/admin/'); ?>/sub-department-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
@endsection
