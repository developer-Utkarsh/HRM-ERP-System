@extends('layouts.admin')
@section('content')

@if (Auth::viaRemember())
    {{666}}
@else
    {{777}}
@endif
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Inventory</a>
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
									<form class="form" action="{{ route('admin.inventory.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Product</label>
														 <select class="form-control select-multiple10 product_id" name="product_id">
															<option value="">Select Poduct</option>
															@if(count($product) > 0)
															@foreach($product as $val)
															<option value="{{ $val->id }}" {{ old('product_id') ? 'selected' : ''}}>{{ $val->cat_name }} - {{ $val->sub_cat_name }} - {{ $val->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('product_id'))
														<span class="text-danger">{{ $errors->first('product_id') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Maintains</label>
														<select class="form-control select-multiple1 maintains" name="maintains">
															<option value="Yes" {{ old('maintains') == 'Yes' ? 'selected' : ''}}>Yes</option>
															<option value="No" {{ old('maintains') == 'No' ? 'selected' : ''}}>No</option>
														</select>
														@if($errors->has('maintains'))
														<span class="text-danger">{{ $errors->first('maintains') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Warranty</label>
														<select class="form-control select-multiple1 warranty" name="warranty">
															<option value="Yes" {{ old('warranty') == 'Yes' ? 'selected' : ''}}>Yes</option>
															<option value="No" {{ old('warranty') == 'No' ? 'selected' : ''}}>No</option>
														</select>
														@if($errors->has('warranty'))
														<span class="text-danger">{{ $errors->first('warranty') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12 wperiod">
													<div class="form-group">
														<label for="first-name-column">Warranty Period</label>
														<input type="text" class="form-control" placeholder="Warranty Period" name="warranty_period" value="{{ old('warranty_period') }}">
														@if($errors->has('warranty_period'))
														<span class="text-danger">{{ $errors->first('warranty_period') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Expiry Date</label>
														<input type="date" class="form-control" name="expiry_date" value="{{ old('expiry_date') }}">
														@if($errors->has('expiry_date'))
														<span class="text-danger">{{ $errors->first('expiry_date') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Consumable</label>
														<select class="form-control select-multiple1 is_consumer" name="is_consumer">
															<option value="Yes" {{ old('is_consumer') == 'Yes' ? 'selected' : ''}}>Yes</option>
															<option value="No" {{ old('is_consumer') == 'No' ? 'selected' : ''}}>No</option>
														</select>
														@if($errors->has('is_consumer'))
														<span class="text-danger">{{ $errors->first('is_consumer') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" name="qty" placeholder="Quantity" value="{{ old('qty') }}" step="0.01">
														@if($errors->has('qty'))
														<span class="text-danger">{{ $errors->first('qty') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Measurement</label>
														<!--<input type="text" class="form-control" name="measurement" placeholder="Measurement" value="{{ old('measurement') }}">-->
														<select class="form-control select-multiple1 measurement" name="measurement">
															<option value="Piece" {{ old('measurement') == 'Piece' ? 'selected' : ''}}>Piece</option>
															<option value="Liter" {{ old('measurement') == 'Liter' ? 'selected' : ''}}>Liter</option>
															<option value="KG" {{ old('measurement') == 'KG' ? 'selected' : ''}}>KG</option>
															<option value="Mtr" {{ old('measurement') == 'Mtr' ? 'selected' : ''}}>Mtr</option>
															<option value="SQ Feet" {{ old('measurement') == 'SQ Feet' ? 'selected' : ''}}>SQ Feet</option>
															<option value="Dozen" {{ old('measurement') == 'Dozen' ? 'selected' : ''}}>Dozen</option>
															<option value="PKT" {{ old('measurement') == 'PKT' ? 'selected' : ''}}>PKT</option>
															<option value="CAN" {{ old('measurement') == 'CAN' ? 'selected' : ''}}>CAN</option>
														</select>
														@if($errors->has('measurement'))
														<span class="text-danger">{{ $errors->first('measurement') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Price</label>
														<input type="number" class="form-control" name="price" placeholder="Price" value="{{ old('price') }}">
														@if($errors->has('price'))
														<span class="text-danger">{{ $errors->first('price') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Buyer</label>
														@php $buyer_data = \App\Buyer::where('is_deleted', '0')->orderBy('id','desc')->get(); @endphp
														 <select class="form-control select-multiple2 buyer_id" name="buyer_id">
															<option value="">Select Buyer</option>
															@if(count($buyer_data) > 0)
															@foreach($buyer_data as $buyer_data_value)
															<option value="{{ $buyer_data_value->id }}" {{ old('buyer_id') ? 'selected' : ''}}>{{ $buyer_data_value->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('buyer_id'))
														<span class="text-danger">{{ $errors->first('buyer_id') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill No</label>
														<input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="{{ !empty($get_bill_detail->bill_no) ? $get_bill_detail->bill_no : old('bill_no') }}">
														
														<!--
														<select class="form-control select-multiple3 bill_no" name="bill_no">
															@if(!empty(old('bill_no')))
																@php
																	$billData = DB::table('bill')->where('buyer_id', old('bill_no'))->get();
																@endphp
																@foreach ($billData as $key => $billDataValue)
																	<option value="{{ $billDataValue->id }}" {{ old('bill_no', !empty(old('bill_no')) && $billDataValue->id == old('bill_no') ? 'selected' : '' ) }}>{{ $billDataValue->bill_no }}</option>
																@endforeach
															@else
																<option value="">Select Bill</option>
															@endif
														</select>
														-->
														
														
														@if($errors->has('bill_no'))
														<span class="text-danger">{{ $errors->first('bill_no') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Mode</label>
														<select class="form-control select-multiple1 mode" name="mode">
															<option value="Credit" {{ old('mode') == 'Credit' ? 'selected' : ''}}>Credit</option>
															<option value="Cash" {{ old('mode') == 'Cash' ? 'selected' : ''}}>Cash</option>
														</select>
														@if($errors->has('mode'))
														<span class="text-danger">{{ $errors->first('mode') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill File</label>
														<input type="file" class="form-control" name="bill_file">
														@if($errors->has('bill_file'))
														<span class="text-danger">{{ $errors->first('bill_file') }} </span>
														@endif
													</div>
												</div>	
												
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Image 1</label>
														<input type="file" class="form-control" name="product_one" id="product_img_one">
														@if($errors->has('product_one'))
														<span class="text-danger">{{ $errors->first('product_one') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Image 2</label>
														<input type="file" class="form-control" name="product_two" id="product_img_two">
														@if($errors->has('product_two'))
														<span class="text-danger">{{ $errors->first('product_two') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Type</label>
														<!--<input type="text" class="form-control" name="type" placeholder="Type" value="{{ old('type') }}">-->
														<select class="form-control select-multiple1 type" name="type">
															<option value="New" {{ old('type') == 'New' ? 'selected' : ''}}>New</option>
															<option value="Expired" {{ old('type') == 'Expired' ? 'selected' : ''}}>Expired</option>
															<option value="Dead" {{ old('type') == 'Dead' ? 'selected' : ''}}>Dead</option>
														</select>
														@if($errors->has('type'))
														<span class="text-danger">{{ $errors->first('type') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control select-multiple1 status" name="status">
															<option value="New" {{ old('status') == 'New' ? 'selected' : ''}}>New</option>
															<option value="Dead" {{ old('status') == 'Dead' ? 'selected' : ''}}>Dead</option>
															<option value="Lost" {{ old('status') == 'Lost' ? 'selected' : ''}}>Lost</option>
															<option value="Out of stock" {{ old('status') == 'Out of stock' ? 'selected' : ''}}>Out of stock</option>
														</select>
														@if($errors->has('status'))
														<span class="text-danger">{{ $errors->first('status') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Location</label>
														<?php if(Auth::user()->role_id == 25){ ?>
															<input type="text" name="location" class="form-control" readonly value="<?=Auth::user()->user_branches[0]->branch['branch_location'];?>"/> 
														<?php }else{ ?>														
															@php $branch_location = \App\Branch::where('is_deleted', '0')->orderBy('id','asc')->groupby('branch_location')->get(); @endphp
															 <select class="form-control select-multiple2 location" name="location">
																<option value="">Select Location</option>
																@if(count($branch_location) > 0)
																@foreach($branch_location as $branch_location_value)
																<option value="{{ $branch_location_value->branch_location }}" {{ old('location') ? 'selected' : ''}}>{{ $branch_location_value->branch_location }}</option>
																@endforeach
																@endif
															</select>
															@if($errors->has('location'))
															<span class="text-danger">{{ $errors->first('location') }} </span>
															@endif
														<?php } ?>														
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Serial Number</label>
														<input type="text" class="form-control" name="model_no">
														@if($errors->has('model_no'))
														<span class="text-danger">{{ $errors->first('model_no') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Model Number</label>
														<input type="text" class="form-control" name="serial_no">
														@if($errors->has('serial_no'))
														<span class="text-danger">{{ $errors->first('serial_no') }} </span>
														@endif
													</div>
												</div>
												
												
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Remark</label>
														<textarea name="remark" class="form-control remark" placeholder="Remark" required></textarea>
														@if($errors->has('remark'))
														<span class="text-danger">{{ $errors->first('remark') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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

			
	$(document).ready(function() {
		$('.select-multiple10').select2({
			width: "100%",
			placeholder: "Select Product",
			allowClear: true
		});
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
			placeholder: "Select",
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
	
	$('#product_img_one').change(function () {
		var fileName=this.value;
		var ext =fileName.substr(fileName.lastIndexOf('.') + 1);// this.value.match(/\.(.+)$/)[1];
		ext=ext.toLowerCase();
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			break;
			default:
			alert('This is not an allowed file type.');
			this.value = '';
		}
	});
	
	$('#product_img_two').change(function () {
		var fileName=this.value;
		var ext =fileName.substr(fileName.lastIndexOf('.') + 1);// this.value.match(/\.(.+)$/)[1];
		ext=ext.toLowerCase();
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			break;
			default:
			alert('This is not an allowed file type.');
			this.value = '';
		}
	});
</script>
@endsection
