@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Request Branch Inventory</h2>
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
								<form action="{{ route('admin.request-branch-inventory') }}" method="get" name="filtersubmit">
									<div class="row">
										<!--div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Branch ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="bID" placeholder="Branch ID" value="{{ app('request')->input('bID') }}">
											</fieldset>
										</div-->

										<div class="col-md-3">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>

										<div class="col-md-3">
											<label for="users-list-status">Status</label>
											<?php $status = array('Pending','Accept','Reject','Raise a issue'); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 status" name="status">
													<option value="">Select Any</option>
													@if(count($status) > 0)
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request-branch-inventory') }}" class="btn btn-warning">Reset</a>
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
								<th>Product Name</th>
								<th>Branch Name</th>
								<th>Qty</th>
								<th>Status</th>
								<th>Remark</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->product_name }}</td>
								<td class="product-category">{{ $value->branch_name }}</td>
								<td class="product-category">{{ $value->qty }}</td>
								<td class="product-category">{{ $value->status }}</td>
								<td class="product-category">{{ $value->remark }}</td>
								<td class="product-category">{{ $value->created_at->format('d-m-Y') }}</td>
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
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
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Branch",
			allowClear: true
		});
		$('.select-multiple2').select2({
			width: "100%",
			placeholder: "Select Status",
			allowClear: true
		});
	});
	
	$(".cat_id").on("change", function () {
		var cat_id = $(".cat_id option:selected").attr('value'); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-sub-cat') }}',
				data : {'_token' : '{{ csrf_token() }}', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.sub_cat_id').empty();
					$('.sub_cat_id').append(data);
				}
			});
		}
	});
</script>
@endsection
