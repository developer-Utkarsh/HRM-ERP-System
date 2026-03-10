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
							<div class="users-list-filter pb-2">
								<h3 class="mb-0">PO Approval List</h3>
							</div>
							<form class="" action="{{ route('po-approval-list',[$sess_emp_id,$type]) }}" method="get">
								<div class="form-group">
									<input type="text" class="form-control" name="po_number" value="{{ app('request')->input('po_number') }}" placeholder="PO Number">
								</div>
								<div class="text-right">
								<button type="submit" class="btn btn-success">Submit</button> &nbsp;
								<button type="button" class="btn btn-primary" onclick="location.href = '{{ route('po-approval-list',[$sess_emp_id,$type]) }}';">Reset</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<?php 
					foreach($getRequest as $g){
						
						if(($sess_emp_id == 6828 || $sess_emp_id==6859) && $g->po_important=='Yes'){
							$bgClass = 'background-color:#fff1f1';
						}else{
							$bgClass = '';
						}
						
						
						if(!empty($g->po_month)){
							$po_month = $g->po_location.'-'.$g->po_no."/".$g->po_month;
						}else{
							$po_month = $g->po_no;
						} 
						
						if($g->request_type=='1'){ 
							$powoText = 'WO'; 
							$powoHead = 'Work';
						}else{	
							$powoText = 'PO'; 
							$powoHead = 'Purchase';
						} 
				?>
				<div class="card mb-1" style="font-size:13px;<?=$bgClass;?>">
					
					<div class="card-content">
						<div class="card-body">
							<div class="row">
								<div class="col-5 font-weight-bold"><?=$powoText;?> No.</div>
								<div class="col-7 font-weight-bold text-primary">: UTK<?=$powoText;?>-<?=$po_month;?></div>
							</div>
							
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Total Amount</div>
								<div class="col-7">: Rs. <?=$g->final_amt;?> /-</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Advance Amount</div>
								<div class="col-7">: Rs. <?=$g->advance_amt;?> /-</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Vendor Name</div>
								<div class="col-7 font-weight-bold">: <?=$g->name; ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">PO</div>
								<div class="col-7 font-weight-bold">: <a href="{{ route('web-view-poprint', [$g->id,$sess_emp_id]) }}">Download</a></div>
							</div>
							
							<?php if($g->quotation_one !='' || $g->quotation_two !='' || $g->quotation_three){  ?>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Quotation</div>
								<div class="col-7">: 
									<?php if($g->quotation_one !=''){  ?>
									<a href="{{ asset('laravel/public/po_upload/' . $g->quotation_one)}}" download >Quotation 1</a> </br>
									
									<?php } if($g->quotation_two !=''){  ?>
									&nbsp;&nbsp;<a href="{{ asset('laravel/public/po_upload/' . $g->quotation_two)}}" download>Quotation 2</a></br>
									
									
									<?php } if($g->quotation_three != ''){  ?>
									&nbsp;&nbsp;<a href="{{ asset('laravel/public/po_upload/' . $g->quotation_three)}}" download>Quotation 3</a></br>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
							
							<hr>
							<?php								
								if($g->dm_status==0){ ?>
							<div class="row text-right" style="padding-top:10px;">
								<div class="col-12">
									<button type="button" class="btn-success" style="padding:8px;" onclick="poAction('<?=$g->request_id;?>','1','<?=$sess_emp_id?>')">Approved</button> &nbsp;&nbsp;
									<button type="button" class="btn-danger" style="padding:8px;" onclick="poAction('<?=$g->request_id;?>','2','<?=$sess_emp_id?>')">Reject</button>
								</div>
							</div>
							<?php  }else if($g->dm_status==1){ echo '<div class="text-center">PO Approved</div>'; }elseif($g->dm_status==2){ echo '<div class="text-center">PO Rejected</div>'; } ?>
						</div>
					</div>
					
				</div>
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
	function poAction(id, value, approve){  
		
		$.ajax({
			type : 'POST',
			url : '{{ route('web-view-po') }}',
			data : {'_token' : '{{ csrf_token() }}', 'id': id, 'value':value, 'approve':approve},
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
	}; 	
</script>

@include('layouts.notification')
</body>
</html>