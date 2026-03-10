@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-11 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Inventory History</h2>
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
			<div class="col-md-1"><a href="{{ route('admin.inventory') }}" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				
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
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							@php
							$productData = DB::table('products')->select(DB::raw("(SELECT name FROM category WHERE id=cat_id) AS category_name, (SELECT name FROM category WHERE id=sub_cat_id) AS sub_category_name"))->where('is_deleted', '0')->where('id', $value->product_id)->orderBy('id', 'desc')->first();
							
							//echo '<pre>'; print_r($productData);die;
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ $productData->category_name }}</td>
								<td class="product-category">{{ $productData->sub_category_name }}</td>
								<td class="product-category">{{ $value->qty }}</td>
								<td class="product-category">{{ $value->status }}</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
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
			placeholder: "Select Category",
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
