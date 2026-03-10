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
						<h2 class="content-header-title float-left mb-0">Return Product List</h2>
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
								<form action="{{ route('admin.request.return-product-list') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-status">Employee</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id">
													<option value="">Select</option>	
													<?php foreach($employee as $em){ ?>
													<option value="{{ $em->id }}"  @if($em->id == app('request')->input('emp_id')) selected="selected" @endif>{{ $em->name }} - {{ $em->register_id }}</option>	
													<?php } ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 rstatus" name="rstatus">
													<option value="">Select Any</option>													
													<option value="1" @if('1' == app('request')->input('rstatus')) selected="selected" @endif>Approved</option>
													<option value="2" @if('2' == app('request')->input('rstatus')) selected="selected" @endif>Pending</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3 pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.return-product-list') }}" class="btn btn-warning">Reset</a>
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
								<th>Request No.</th>
								<th>Employee</th>
								<th>Product Name</th>	
								<th>Qty</th>
								<th>Branch</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>			
							<?php 
								$i =1; 
								if(count($record) > 0){
								foreach($record as $re){ ?>
							<tr>
								<td><?=$i;?></td>
								<td>REQ-{{ $re->unique_no }}</td>
								<td>{{ $re->uname }}</td>
								<td>{{ $re->pname }}</td>
								<td>{{ $re->qty }}</td>
								<td>{{ $re->bname }}</td>
								<td>
									<?php if(($re->inventory_status==2 || !empty($emp_id)) && empty($re->inventory_grn)){ ?>
									<a href="javascript:void(0)" title="Accept" data-id="{{ $re->id }}" data-product-id="{{ $re->product_id }}" class="product_accept btn-success" style="padding:5px;">Accept</a>
									<?php }else{ echo 'Accepted'; }?>
								</td>
							</tr>
							<?php  $i++;} }else{ ?>
							<tr>
								<td colspan="7" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>	
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
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
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
	
	$(".product_accept").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		var product_id = $(this).attr("data-product-id"); 
	
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.request.inventory-product-accept') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id,'product_id':product_id},
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
</script>
@endsection