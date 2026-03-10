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
 
</head>
		<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" style="background:#fff886">
		<div class="app-content content" style="margin:0;">
			
			<div class="content-wrapper" style="margin:0;">
				<div class="content-header row">
					<div class="content-header-left col-md-12 col-12 mb-2">
						<div class="row breadcrumbs-top">
							<div class="col-12">
								<h2 class="content-header-title float-left mb-0">View Task History</h2>
							</div>
						</div>
					</div>
					<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
					</div>
				</div>
				<div class="content-body">
					<!-- page users view start -->
					<section class="page-users-view history">
						
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Task Name</div>
								<div class="float-right w-70 newB"><?=$task_history_get->title;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Plan Hour</div>
								<div class="float-right w-70 newB"><?=$task_history_get->plan;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Spent Hour</div>
								<div class="float-right w-70 newB"><?=$task_history_get->spent;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Status</div>
								<div class="float-right w-70 newB"><?=$task_history_get->status;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Assign By</div>
								<div class="float-right w-70 newB"><?=$task_history_get->assign_name;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Assign To</div>
								<div class="float-right w-70 newB"><?=$task_history_get->emp_name;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Date</div>
								<div class="float-right w-70 newB"><?php echo date('d-m-Y',strtotime($task_history_get->date)); ?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Remark</div>
								<div class="float-right w-70 newB"><?=$task_history_get->remark;?></div>
							</div>
							<div class="row w-100 mx-0">
								<div class="float-left w-30 newB">Description</div>
								<div class="float-right w-70 newB">							
									<?php 										
										$tHistory = json_decode($task_history_get->description, true);
										if(is_array($tHistory) && count($tHistory)>0){
											foreach ($tHistory as $item) {
												echo "<li>$item</li>";
											}
										}else{
											echo $task_history_get->description;
										}
									?>
								</div>
							</div>
							
							<!-- information start -->
							<!-- social links end -->
							 
							
							
					</section>
				</div>
			</div>
		</div>
		

		<style type="text/css">
			.newB{
				border : solid 1px;
				padding: 5px;
			}
			
			.float-left {
				font-weight : 900;
			}
			
			.w-30{
				width:30%;
			}
			
			.w-70{
				width:70%;
			}
		</style>

		@section('scripts')
		<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/app-user.css') }}">
		

    
</body>
</html>
