@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Asset Request List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="{{ route('admin.request.index') }}" method="get" name="filtersubmit">
									<div class="row pt-2">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<input type="text" class="form-control" name="title" placeholder="MRL No." value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Unique</th>
								<th>Employee</th>
								<th>Type Of Business</th>
								<th>Product Name</th>
								<th>Product Description</th>		
								<th>Qty</th>
								<th>Category</th>
								<th>Sub Category</th>										
								<th>Type</th>		
								<th>Remark</th>		
								<th>Status</th>	
								<th>Requested Category</th>	
								<th>Reason</th>	
								<th>Action</th>
								<th>GRN</th>
							</tr>
						</thead>
						<tbody>			
							<?php if(count($getRequest) > 0){ ?>
								@foreach($getRequest as  $key => $value)
								<tr>
									<td>{{ $key + 1 }}</td>
									<td>REQ-{{ $value->unique_no }}</td>
									<td>{{ $value->emp_name }}</td>
									<td>{{ $value->type_of_business }}</td>
									<td>
										{{ $value->title }} - 
										<?php if(!empty($value->image)){ ?><a href="{{ asset('laravel/public/quotation/' . $value->image) }}" download >Preview</a><?php } ?>
									</td>
									<td>{{ $value->requirement }}</td>									
									<td>{{ $value->qty }}</td>
									<td><?php if(!empty($value->name)){ echo $value->name; }else{ echo '-'; } ?></td>
									<td><?php if(!empty($value->sub_name)){ echo $value->sub_name; }else{ echo '-'; } ?></td>																	
									<td><?php if(!empty($value->type)){ echo $value->type; }else{ echo '-'; } ?></td>									
									<td><?php if(is_numeric($value->remark)){ echo $value->dh_name; }else{ echo $value->remark; } ?></td>									
									<td>
										<?php 
											if($value->status==0 || $value->status==2){
												$status = $value->status;
												switch($status){
													case 1	:	$sText = "Approved";	break;
													case 2	:	$sText = "Rejected";	break;
													default	:	$sText = "Pending";		break;
												}											
												echo 'Department Head Status is <b>'.$sText.'</b>';
												
											}else if(($value->it_status==0 || $value->it_status==1 || $value->it_status==3 || $value->it_status==4 || $value->it_status==5) && $value->status==1){
												$status = $value->it_status;
												switch($status){
													case 1	:	$sText = "In Stock";			break;
													case 2	:	$sText = "Proceed to purchase";	break;
													case 3	:	$sText = "Rejected";			break;
													case 4	:	$sText = "Transfer to Networking Team";			break;
													case 5	:	$sText = "Proceed To Instock Approval";			break;
													default	:	$sText = "Pending";				break;
												}											
												echo 'Inventory Team Status is <b>'.$sText.'</b>';											
											}else if($value->it_status==2){
												$status = $value->purchase_status;
												switch($status){
													case 1	:	$sText = "On Hold";					break;
													case 2	:	$sText = "Deliver";					break;
													case 3	:	$sText = "PO Generated";			break;
													case 4 	: 	$sText = "Below 5000 - Deliver";	break;
													case 5 	: 	$sText = "Cancel";					break;
													case 6 	: 	$sText = "Rejected";				break;
													case 7 	: 	$sText = "Proceed To Maintenance";	break;
													default	:	$sText = "In Progress";				break;
												}											
												echo 'Purchase Team Status is <b>'.$sText.'</b>';											
											}
										?>	
									</td>
									<td><?php if(!empty($value->material_category)){ echo $value->material_category; }else{ echo '-'; } ?></td>
									<td>
										<?php 
											if($value->purchase_status==1 || $value->purchase_status==6){
												echo $value->purchase_reason;
											}else if(!empty($value->reason)){ echo $value->reason; }else{ echo '-'; } 
										?>
									</td>
									
									<td class="product-action">
										@if($value->status==0)
											<a href="{{ route('admin.request.delete', $value->id)}}" onclick="return confirm('Are You Sure To Delete Request')">
												<span class="action-delete"><i class="feather icon-trash"></i></span>
											</a>
											<a href="{{ route('admin.request.edit', $value->id) }}">
												<span class="action-edit"><i class="feather icon-edit"></i></span>
											</a>
										@else
											Not Editable
										@endif
										
										
										@if(($value->purchase_status==2 || $value->purchase_status==4 || $value->it_status==1) && $value->product_status==0)
											<hr>
											<a href="javascript:void(0)" title="Accept" data-id="{{ $value->id }}" class="product_accept btn-success" style="padding:5px;">Accept</a>
										@elseif($value->product_status==1)
											<hr>
											<?php if($value->request_type=='0' && $value->inventory_status==0 && $value->type=='Asset'){ ?>
											<a href="javascript:void(0)" title="Accept" data-id="{{ $value->id }}" class="product_return btn-danger" style="padding:5px;">Return</a>
											<?php 
												}else if($value->inventory_status==2){ 
													echo 'Return Request Send'; 
												}else if($value->inventory_status==1){ 
													echo 'Return Request Accept';
												}
											?>
										@endif
										
										<?php
										if($value->emp_id !== null && $value->emp_id == 0){ ?>
											<a href="javascript:void(0)"  data-id="{{ $value->id }}" class="get_edit_data text-primary">Transfer</a>
										<?php  } ?>
									</td>
									<td>										
										<?php 
											if($value->emp_grn!=0){ 
												$name = $value->branch;
												$words = explode(" ", $name);
												$firstLetters = "";

												foreach ($words as $word) {
													$firstLetters .= substr($word, 0, 1);
												}
												
												if($value->request_type==1){
													$ctext = "SRN";
												}else{
													$ctext = "GRN";
												}
												
												echo '<b>'.$ctext.' :</b> '.$value->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($value->created_at)).'/'.$value->emp_grn;  
											}
										?>
										
										<?php 
											if($value->inventory_grn!=0){ 
												$name = $value->branch;
												$words = explode(" ", $name);
												$firstLetters = "";

												foreach ($words as $word) {
													$firstLetters .= substr($word, 0, 1);
												}
												
												echo '<b>Return GRN :</b> '.$value->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($value->created_at)).'/'.$value->inventory_grn; 
											}
										?>
									</td>
								</tr>
								@endforeach									
							<?php }else{ ?>
								
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							<?php } ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>
<div class="modal fade bd-example-modal-xl" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.request.request-transfer') }}" method="post">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Assign Employee</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<input type="hidden" name="request_id" value="" class="request_id"/> 
						<select class="form-control select-multiple" name="assign_employee" required>
							<option value="">Select</option>
							<?php foreach($users as $us) { ?>
							<option value="{{ $us->id }}">{{ $us->name }}</option>
							<?php } ?>
						</select> 
						</br>
						<button type="submit" class="btn btn-success mt-2">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<style type="text/css">
	.table tbody td {
		word-break: break-word;
	}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
	
	$(".product_accept").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
	
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.request.product-accept') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id},
			dataType : 'json',
			success : function (data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){		
					swal("Done!", data.message, "success").then(function(){  		
						location.reload();
					});
				}
			}
		});
	}); 
	
	$(".product_return").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
	
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.request.product-return') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id},
			dataType : 'json',
			success : function (data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){		
					swal("Done!", data.message, "success").then(function(){  		
						location.reload();
					});
				}
			}
		});
	});
	
	$(".get_edit_data").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
						
		$('.request_id').val(request_id);
	}); 
	
</script>
@endsection