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
						<h2 class="content-header-title float-left mb-0">Product</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4"> <a href="{{ route('admin.product.create') }}" data-id="" class="btn btn-outline-primary float-right">Add Product</a></div>
				</div>
			</div>
		</div>
		<div class="content-body">
		
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.product.index') }}" method="get" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<label>Product Name</label>
														<input type="text" class="form-control" placeholder="Product Name" name="name" value="{{ app('request')->input('name') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>	

												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Category</label>
														 <select class="form-control select-multiple1 cat_id" name="cat_id">
															<option value="">Select Category</option>
															@if(count($category) > 0)
															@foreach($category as $categoryvalue)
															<option value="{{ $categoryvalue->id }}" @if($categoryvalue->id == app('request')->input('cat_id')) selected="selected" @endif>{{ $categoryvalue->name }}</option>
															@endforeach
															@endif
														</select>
													</div>
												</div>	
												@php $gtSubCat = array(); @endphp
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Category</label>
														<select class="form-control select-multiple1 sub_cat_id" name="sub_cat_id">
															<option value="">Select Sub Category</option>
															@if(!empty(app('request')->input('cat_id')))
																@php
																$gtSubCat  = DB::table('category')->where('parent', app('request')->input('cat_id'))->where('is_deleted', '0')->get();
																 
																@endphp
																@if(count($gtSubCat) > 0)
																@foreach($gtSubCat as $gtSubCatvalue)
																<option value="{{ $gtSubCatvalue->id }}" @if($gtSubCatvalue->id == app('request')->input('sub_cat_id')) selected="selected" @endif>{{ $gtSubCatvalue->name }}</option>
																@endforeach
																@endif
															@endif
														</select>
													</div>
												</div>												
												<div class="col-md-3 mt-2">
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="{{ route('admin.product.index') }}" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product Name</th>								
								<th>Product Code</th>								
								<th>Product ID</th>								
								<!--th>Quantity</th>
								<th>In Stock</th-->
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							
							@php
							$total_qty = 0;
							// $total_qty   = DB::table('products')->where('id', $value->id)->sum('qty');
							$rem_pro = 0;
							
							$check_total_approved = DB::table('transfer')
													  ->where('product_id', $value->id)
													  ->where('transfer_from', '0')
													  ->where('is_deleted','=','0')
													  //->where('is_type', 'Branch')
													  //->whereRaw("(status = 'Accept' OR status = 'Partially')")
													  ->whereRaw("(status != 'Reject' AND status != 'Raise a issue')")
													  ->get()
													  ->sum("qty");
							
							if(!empty($check_total_approved)){
								$rem_pro = $total_qty - $check_total_approved;
							}
							else{
								$rem_pro = $total_qty;
							}
							
							@endphp
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td class="product-category">{{ $value->category_name }}</td>
								<td class="product-category">{{ $value->sub_category_name }}</td>
								<td class="product-category"><a href="{{ route('admin.product.product-history',[$value->id]) }}" title="Click Here" target="_blank">{{ $value->name }}</a></td>								
								<td class="product-category">{{ $value->pcode }}</td>
								<td class="product-category">{{ $value->id }}</td>
								<!--td class="product-category">{{ $rem_pro }}</td-->
								<td>{{ $value->created_at }}</td>
								<td class="product-action">
									<a title="Update Inventory" href="{{ route('admin.product.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<!--
									<a title="Delete Inventory" href="{{ route('admin.product.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Product')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									-->
								</td>
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
