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
								<form action="{{ route('admin.product-inventory') }}" method="get" name="filtersubmit">
									<div class="row">
										<?php if(Auth::user()->role_id == 25){ ?>
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
										<?php } ?>
										<div class="col-md-3 col-12">
											<div class="form-group">
												<label for="first-name-column">Select Product</label>
												 <select class="form-control select-multiple1 product_id" name="product_id">
													<option value="">Select Poduct</option>
													@if(count($product_list) > 0)
													@foreach($product_list as $val)
													<option value="{{ $val->id }}" @if($val->id == app('request')->input('product_id')) selected="selected" @endif>{{ $val->name }}</option>
													@endforeach
													@endif
												</select>
												@if($errors->has('name'))
												<span class="text-danger">{{ $errors->first('name') }} </span>
												@endif
											</div> 
										</div>
										<!--
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
										-->
										
										<div class="col-12 col-sm-6 col-lg-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.product-inventory') }}" class="btn btn-warning">Reset</a>
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
					<table class="table data-list-view" id="my-table-id">
						<thead>
							<tr>
								<th>S. No.</th>								
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product</th>
								<th>Qty</th>
								<th>Status</th>
								<th>Remark</th>
								<th>Date</th>
								@if(Auth::user()->role_id == 29)
								<th>Action</th>
								@endif
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							@php
						
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
								<td>{{ $pageNumber++ }}</td>
								<td class="product-category">{{ $value->category_name }}</td>
								<td class="product-category">{{ $value->sub_category_name }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ $rem_pro }}</td>
								<td class="product-category">{{ $value->status }}</td>
								<td class="product-category">{{ isset($value->remark) ? $value->remark : '-' }}</td>
								<td class="product-category">{{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}</td>
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
					<div class="d-flex justify-content-center">					
					{!! $product->appends($params)->links() !!}
					</div>
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
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.product_id 	 = $('.product_id').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/branch-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
@endsection
