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
						<h2 class="content-header-title float-left mb-0">Inventory</h2>
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
								<form action="{{ route('admin.inventory') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-status">Status</label>
											<?php $status = array('Accept','Reject','Raise a issue'); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 status" name="status">
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
											<a href="{{ route('admin.inventory') }}" class="btn btn-warning">Reset</a>
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
								<th>Name</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Qty</th>
								<th>Status</th>
								<th>Remark</th>
								@if(Auth::user()->role_id == 29)
								<th>Action</th>
								@endif
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							@php
							$productData = DB::table('products')->select(DB::raw("(SELECT name FROM category WHERE id=cat_id) AS category_name, (SELECT name FROM category WHERE id=sub_cat_id) AS sub_category_name"))->where('is_deleted', '0')->where('id', $value->product_id)->orderBy('id', 'desc')->first();
							
							
						
							$rem_pro = 0;
							$check_total_approved = DB::table('transfer')->where('product_id', $value->product_id)->where('transfer_from', $value->transfer_to)->whereRaw("transfer_from  != '0'")->whereRaw("(status = 'Accept' OR status = 'Partially')")->get()->sum("qty");

							if(!empty($check_total_approved)){
								$rem_pro = $value->qty - $check_total_approved;
							}
							else{
								$rem_pro = $value->qty;
							}
							
							
							//echo '<pre>'; print_r($check_total_approved);die;
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ $productData->category_name }}</td>
								<td class="product-category">{{ $productData->sub_category_name }}</td>
								<td class="product-category">{{ $rem_pro }}</td>
								<td class="product-category">{{ $value->status }}</td>
								<td class="product-category">{{ $value->remark }}</td>
								@if(Auth::user()->role_id == 29)
								<td class="product-action">
									<a title="Transfer Product" href="{{ route('admin.transfer-inventory', [$value->product_id,$value->transfer_to]) }}">
										<span class="action-transfer"><i class="feather icon-user"></i></span>
									</a>
									<a title="Transfer Product History" href="{{ route('admin.transfer-inventory-history', $value->product_id) }}">
										<span class="action-transfer"><i class="feather icon-clock"></i></span>
									</a>
								</td>
								@endif
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
			placeholder: "Select Any",
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
