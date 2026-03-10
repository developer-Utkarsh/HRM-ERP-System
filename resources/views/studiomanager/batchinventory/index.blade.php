@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
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
								<form action="{{ route('studiomanager.batchinventory.index') }}" method="get" name="filtersubmit">
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
												<select class="form-control select-multiple" name="status">
													@php $status = ['Inactive', 'Active']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.batchinventory.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Type</th>
								<th>Name</th>
								<th>Batch Code</th>
								<th>Batch Name</th>
								<th>Inventory Type</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($inventory as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->type }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category"><?=($value->batch_code)?$value->batch_code:'-';?></td>
								<td class="product-category"><?=($value->batch_name)?$value->batch_name:'-';?></td>
								<td class="product-category"><?=($value->inventory_type)?$value->inventory_type:'-';?></td>
								<td>@if($value->status == 1) Active @else Inactive @endif</td>
								 
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a href="{{ route('studiomanager.batchinventory.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a> &nbsp;&nbsp;&nbsp;&nbsp;
									<a href="{{ route('studiomanager.batchinventory.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Inventory')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							@endforeach
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
