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
					<a href="http://15.207.232.85/index.php/employee-enquiry?user_id=<?=$user_id;?>" class="btn-dark p-1" style="border-radius:3px;">Back</a>
				</div> 
				
				<?php 
				if(count($query) > 0){
					foreach($query as $q){ 
						$enquiry_id=$q->id;?>
					<div class="card">
						<div class="card-content">
							<div class="card-body">

								<?php
								   $replyers = DB::table('enquiry_description')
										->select('enquiry_description.*')
										->where('enquiry_description.enquiry_id',$q->id)
										->orderby('enquiry_description.id','ASC')->get(); 
									if(count($replyers) > 0){
										$ii = 0;
										foreach($replyers as $r){
											$ii++;
									?> 
										
										<?php
										if($ii==1){
										?>
											<p style="font-size:13px;">
												<b> 
													<span class="text-primary">Your Enquiry : </span> 
													<?=$r->description;?>
													
												</b>
												<span style="float:right;font-size: 11px;color: #273ca5;"><?=date('d-m-Y h:i A',strtotime($r->created_at));?></span>
											</p>
											<p class="text-success" style="float: left;"><b>Reply : </b> </p>
										<?php }
										else{
											?>
											<p style="font-size:13px;">
												&nbsp;<?=$r->description;?>
												<span style="float:right;font-size: 11px;color: #273ca5;"><?=date('d-m-Y h:i A',strtotime($r->created_at));?></span>
											</p>
											<?php
										}
										?> 
								<?php  } } ?>
							</div>
						</div>
					</div>
				<?php } }else{ ?>
				<div class="card">
					<div class="card-content">
						<div class="card-body text-center">
							No Complaints Raised Yet
						</div>
					</div>
				</div>
				
				<?php } ?>
			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>
</div>

<style type="text/css">
	.input {
		position: relative;
		bottom: -3px;
	}
</style>
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
	function getissue(id){
		if(id==1){
			$('#employee').show();
		}else{
			$('#employee').hide();
		}
	}
	
</script>
@include('layouts.notification')
</body>
</html>