<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">   

   <link href="{{url('../laravel/public/logo.png')}}" rel="icon" type="image/ico" />
    <title>{{ config('app.name') }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/dashboard-analytics.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<style>
		.m_body {
			width: 50%;
		}

		@media only screen and (max-width: 600px) {
			.m_body {
				width: 100%;
			}
		}
	</style>

</head>

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<div class="app-content content m_body" style="margin: 0 auto;">
	<div class="content-wrapper" style="margin-top: 2px;">
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="text-right pb-2">
					<a href="{{ route('employee-details',[$sess_emp_id,$type]) }}" class="btn-dark p-1">Back</a>
				</div>
				<div class="card mb-1">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter ">
								<h3 class="mb-0">Requisition List</h3>
							</div>
						</div>
					</div>
				</div>
				<div class="card mb-1">
					<div class="card-content collapse show">
						<div class="card-body">
							<form class="" action="{{ route('employee-requisition-list',[$sess_emp_id,$type]) }}" method="get">
								<div class="form-group">
									<input type="text" class="form-control" name="mrl_number" value="{{ app('request')->input('mrl_number') }}" placeholder="MRL Number">
								</div>
								<div class="text-right">
									<button type="submit" class="btn btn-success">Submit</button> &nbsp;
									<button type="button" class="btn btn-primary" onclick="location.href = '{{ route('employee-requisition-list',[$sess_emp_id,$type]) }}';">Reset</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<?php if(count($getRequest) > 0){ ?>
						@foreach($getRequest as  $key => $value)
				<div class="card mb-1" style="font-size:13px;">
					<div class="card-content">
						
						<div class="card-body">
							<div class="row">
								<div class="col-5 font-weight-bold">Unique No.</div>
								<div class="col-7 font-weight-bold text-primary">: REQ-{{ $value->unique_no }}</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Date</div>
								<div class="col-7">: <?php echo date('d-m-Y h:i:s', strtotime($value->created_at)); ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Employee</div>
								<div class="col-7">: {{ $value->emp_name }} </div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Product Title</div>
								<div class="col-7">: {{ $value->title }}  <?php if(!empty($value->image)){ ?>- <a href="{{ asset('laravel/public/quotation/' . $value->image) }}" download >Preview</a><?php } ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Product Description</div>
								<div class="col-7">: {{ $value->requirement }}</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Qty</div>
								<div class="col-7">: {{ $value->qty }}</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Relevant person who approved this requisition</div>
								<div class="col-7">: <?php if(is_numeric($value->remark)){ echo $value->dh_name; }else{ echo $value->remark; } ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Category</div>
								<div class="col-7">: <?php if(!empty($value->name)){ echo $value->name; }else{ echo '-';  } ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Sub Category</div>
								<div class="col-7">: <?php if(!empty($value->sub_name)){ echo $value->sub_name; }else{ echo '-';  } ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Product Name</div>
								<div class="col-7">: {{ $value->pname }}</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Type</div>
								<div class="col-7">: 
									<?php if(!empty($value->type)){ echo $value->type; }else{ echo '-';  } ?>
								</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Request Category</div>
								<div class="col-7">: 
									<?php if(!empty($value->material_category)){ echo $value->material_category; }else{ echo '-';  } ?>
								</div>
							</div>
							<hr>
							@if(!empty($value->reason))
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Rejected</div>
								<div class="col-7">: {{ $value->reason }}</div>
							</div>
							@endif
							<div class="row" style="padding-top:5px;">
								<div class="col-12 text-center">
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
												case 1	:	$sText = "On Hold";						break;
												case 2	:	$sText = "Deliver";						break;
												case 3	:	$sText = "PO Generated";				break;
												case 4 	: 	$sText = "Below 5000 - Deliver";		break;
												case 5 	: 	$sText = "Cancel";						break;
												case 6 	: 	$sText = "Rejected";					break;
												case 7 	: 	$sText = "Proceed To Maintenance";		break;
												default	:	$sText = "In Progress";					break;
											}											
											echo 'Purchase Team Status is <b>'.$sText.'</b>';											
										}
									?>	
								</div>
								<hr class="my-1">
								<div class="text-center w-100">
									@if(($value->purchase_status==2 || $value->purchase_status==4 || $value->it_status==1) && $value->product_status==0)
										<a href="javascript:void(0)" title="Accept" data-id="{{ $value->id }}" class="product_accept btn-success" style="padding:5px 20px;">Accept</a>
									@elseif($value->product_status==1)
										<hr>
										<?php if($value->request_type=='0' && $value->inventory_status==0 && $value->type=='Asset'){ ?>
										<a href="javascript:void(0)" title="Accept" data-id="{{ $value->id }}" data-product-id="{{ $value->product_id }}" class="product_return btn-danger" style="padding:5px;">Return</a>
										<?php 
											}else if($value->inventory_status==2){ 
												echo 'Return Request Send'; 
											}else if($value->inventory_status==1){ 
												echo 'Return Request Accept';
											}
										?>
									@endif
									
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
								</div>
							</div>
							
						</div>
						
					</div>
				</div>
				@endforeach									
				<?php }else{ ?>
					
					<div class="text-center">No Data Found</div>	
				<?php } ?>
				<div class="d-flex justify-content-center">					
				{!! $getRequest->appends($params)->links() !!}
				</div>
			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>

<script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/app-menu.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/app.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/components.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>	
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

	$("#generate_salary_otp").submit(function(e) { 
		var form = document.getElementById('generate_salary_otp');
		var dataForm = new FormData(form);
		e.preventDefault();
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('material-send-otp') }}',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} 
				else if(data.status == true){
					$("#generate_salary_otp").css('display','none');
					$("#check_salary_otp").css('display','block');
				}
			}
		}); 
	});
	
	$("#check_salary_otp").submit(function(e) {
		$('.msg-success').text('');
		var form1 = document.getElementById('generate_salary_otp');
		var form = document.getElementById('check_salary_otp');
		var dataForm1 = new FormData(form1);
		var dataForm = new FormData(form);
		dataForm.append('mobile_no', dataForm1.get('mobile_no'));
		e.preventDefault();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('material-access-otp') }}',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){ alert(data.status);
				if(data.status == false){
					swal("Error!", data.message, "error");
				}else if(data.status == true){	 
					window.location.href = "{{ route('employee-details') }}/"+data.material_id+"/web";
				}
			}
		}); 
	});

	$(".product_accept").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		var product_id = $(this).attr("data-product-id"); 
	
		// alert(request_id);
		
		$.ajax({
			type : 'POST',
			url : '{{ route('web-view-product-accept') }}',
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


	$(".product_return").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
	
		$.ajax({
			type : 'POST',
			url : '{{ route('request.web-view-product-return') }}',
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
</script>
@include('layouts.notification')
</body>
</html>