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
						<h2 class="content-header-title float-left mb-0">Batch Holiday</h2>
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
								<form action="{{ route('admin.batch_holiday.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="title" placeholder="Title" value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple status" name="status">
													@php $status = array('1' => 'Active','0' => 'Inactive'); @endphp
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
											<a href="{{ route('admin.holiday.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Title</th>
								<th>Date</th>
								<th>Location</th>
								<th>Course</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($holiday) > 0)
								@foreach($holiday as  $key => $value)
								@php
								$sts = '';$clr = "";
								if($value->status == '1'){ $sts = "Active"; $clr = "success"; }else if($value->status == '0'){ $sts = "Inactive"; $clr = "danger"; }
								@endphp
								<tr>
									<td>{{ $key + 1 }}</td>
									<td class="product-category">{{ $value->title }}</td>
									<td class="product-category">{{ date('d-m-Y',strtotime($value->date)) }}</td>
									<td class="product-category">{{ $value->location }}</td>
									<td class="product-category">{{ $value->cname }}</td>

									<td class="product-category">{{ $sts }}</td>

									<td class="product-action">
										<a href="{{ route('admin.batch_holiday.edit', $value->id) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<a href="{{ route('admin.batch_holiday.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Holday')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
									</td>
								</tr>
								@endforeach
							@else
								<tr>
									<td class="text-center" colspan="5">No Data Found</td>
								</tr>	
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
</script>
@endsection
