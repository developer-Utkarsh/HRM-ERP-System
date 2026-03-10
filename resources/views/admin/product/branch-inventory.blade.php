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
						<h2 class="content-header-title float-left mb-0">Branch Transfer Inventory</h2>
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
								<form action="{{ route('admin.transfer-branch-inventory') }}" method="get" name="filtersubmit">
									<div class="row">
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
										<div class="col-md-3 col-12">
											<div class="form-group">
												<label for="first-name-column">Select Product</label>
												 <select class="form-control select-multiple10 product_id" name="name">
													<option value="">Select Poduct</option>
													@if(count($product_list) > 0)
													@foreach($product_list as $val)
													<option value="{{ $val->id }}" @if($val->id == app('request')->input('name')) selected="selected" @endif>{{ $val->name }}</option>
													@endforeach
													@endif
												</select>
												@if($errors->has('name'))
												<span class="text-danger">{{ $errors->first('name') }} </span>
												@endif
											</div>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-12 col-lg-12 text-right">
											<fieldset class="form-group mb-0">		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.transfer-branch-inventory') }}" class="btn btn-warning">Reset</a>
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
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product Name</th>
								<th>Branch Name</th>
								<th>Transfer Quantity</th>
								<th>Total Branch Stock</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							@php
							$total_qty = DB::table('transfer')
											->where('product_id', $value->product_ids)
											->where('transfer_to', $value->transfer_to)
											//->whereRaw("(status = 'Accept' OR status = 'Partially')")
											->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
											->groupBy('transfer_to')
											->groupBy('product_id')
											->sum("qty");
							
							$rem_pro = 0;
							$check_total_approved = DB::table('transfer')
														->where('product_id', $value->product_ids)
														->where('transfer_from', $value->transfer_to )
														->whereRaw("transfer_from  != '0'")
														//->whereRaw("(status = 'Accept' OR status = 'Partially')")
														->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
														->groupBy('transfer_from')
														->groupBy('product_id')
														->sum("qty");
							
							if(!empty($check_total_approved)){
								$rem_pro = $total_qty - $check_total_approved;
							}
							else{
								$rem_pro = $total_qty;
							}
							
							
							//echo '<pre>'; print_r($product);die;
							@endphp
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td class="product-category">{{ $value->cat_name }}</td>
								<td class="product-category">{{ $value->sub_cat_name }}</td>
								<td class="product-category">{{ $value->product_name }}</td>
								<td class="product-category">{{ $value->branch_name }}</td>
								<td class="product-category">{{ $value->qty }}</td>
								<td class="product-category">{{ $rem_pro }}</td>
								<td class="product-category">{{ date('d-m-Y h:i:s',strtotime($value->created_at)) }}</td>
								<td class="product-action">
									<a title="Transfer Product" href="{{ route('admin.transfer-branch-inventory', [ $value->product_ids, $value->transfer_to]) }}">
										<span class="action-transfer"><i class="feather icon-repeat"></i></span>
									</a>
									<a title="Transfer List" href="{{ route('admin.transfer-inventory', [$value->product_ids, $value->transfer_to]) }}">
										<span class="action-transfer"><i class="feather icon-clock"></i></span>
									</a>
								</td>
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="9">No Record Found</td></tr>
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
			placeholder: "Select Branch",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple10').select2({
			width: "100%",
			placeholder: "Select Product",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple3').select2({
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
	
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.branch_id 	 	= $('.branch_id').val(),
		data.product_id 	= $('.product_id').val(),
		data.fdate 	 		= $('.fdate').val(),
		data.tdate 	 		= $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/warehouse-to-branch-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	}); 
</script>
@endsection
