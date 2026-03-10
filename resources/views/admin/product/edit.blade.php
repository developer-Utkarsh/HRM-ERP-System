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
						<h2 class="content-header-title float-left mb-0">Edit Product</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Product</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.product.update', $product_detail->id) }}" method="post" enctype="multipart/form-data">
										<!-- @method('PATCH') -->
										@csrf
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Name</label>
														<input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name', $product_detail->name) }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Code</label>
														<input type="text" class="form-control" placeholder="Product Code" name="pcode" value="{{ old('name', $product_detail->pcode) }}">
														@if($errors->has('pcode'))
														<span class="text-danger">{{ $errors->first('pcode') }}</span>
														@endif
													</div>
												</div>	



												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Category</label>
														 <select class="form-control select-multiple1 cat_id" name="cat_id">
															<option value="">Select Category</option>
															@if(count($category) > 0)
															@foreach($category as $categoryvalue)
															<option value="{{ $categoryvalue->id }}" @if($categoryvalue->id == $product_detail->cat_id) selected="selected" @endif>{{ $categoryvalue->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('cat_id'))
														<span class="text-danger">{{ $errors->first('cat_id') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Category</label>
														<select class="form-control select-multiple4 sub_cat_id" name="sub_cat_id">
														    @if(!empty($product_detail->sub_cat_id))
																@php
																	$subCatData = DB::table('category')->where('parent', $product_detail->cat_id)->get();
																@endphp
																@foreach ($subCatData as $key => $subCatDataValue)
																	<option value="{{ $subCatDataValue->id }}" {{ old('name', !empty($product_detail->sub_cat_id) && $subCatDataValue->id == $product_detail->sub_cat_id ? 'selected' : '' ) }}>{{ $subCatDataValue->name }}</option>
																@endforeach
															@else
																<option value="">Select Sub Category</option>
															@endif
														</select>
														@if($errors->has('sub_cat_id'))
														<span class="text-danger">{{ $errors->first('sub_cat_id') }} </span>
														@endif
													</div>
												</div>	
												
												 

												                                     
												<div class="col-12">
													<input type="hidden" name="gt_warranty" id="gt_warranty" value="{{ $product_detail->warranty }}">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

	$(document).ready(function(){
		var w_val = $('#gt_warranty').val();
		if(w_val == 'Yes'){
			$('.wperiod').show();
		}	
		else{
			$('.wperiod').hide();
		}	
	});
	
	$(document).ready(function() {
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple2').select2({
			width: "100%",
			placeholder: "Select Buyer",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple3').select2({
			width: "100%",
			placeholder: "Select Bill",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple4').select2({
			width: "100%",
			placeholder: "Select Sub Category",
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
	
	$(".warranty").on("change", function(){ 
		var warranty_val = $('.warranty option:selected').attr('value');
		if(warranty_val == 'Yes'){
			$('.wperiod').show();
		}	
		else{
			$('.wperiod').hide();
		}		
	});
	
	$(".buyer_id").on("change", function () {
		var buyer_id = $(".buyer_id option:selected").attr('value'); 
		if (buyer_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-bill') }}',
				data : {'_token' : '{{ csrf_token() }}', 'buyer_id': buyer_id},
				dataType : 'html',
				success : function (data){
					$('.bill_no').empty();
					$('.bill_no').append(data);
				}
			});
		}
	});	
</script>
@endsection
