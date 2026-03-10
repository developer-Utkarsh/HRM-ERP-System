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
						<h2 class="content-header-title float-left mb-0">View Task History</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">View Task History</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.view-task') }}" class="btn btn-primary mr-1">Back</a>
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
						<div class="float-left w-25 newB">Task Name</div>
						<div class="float-right w-75 newB"><?=$task_history_get->title;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Description</div>
						<div class="float-right w-75 newB">							
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
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Plan Hour</div>
						<div class="float-right w-75 newB"><?=$task_history_get->plan;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Spent Hour</div>
						<div class="float-right w-75 newB"><?=$task_history_get->spent;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Status</div>
						<div class="float-right w-75 newB"><?=$task_history_get->status;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Assign By</div>
						<div class="float-right w-75 newB"><?=$task_history_get->assign_name;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Assign To</div>
						<div class="float-right w-75 newB"><?=$task_history_get->emp_name;?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Date</div>
						<div class="float-right w-75 newB"><?php echo date('d-m-Y',strtotime($task_history_get->date)); ?></div>
					</div>
					<div class="row w-100 mx-0">
						<div class="float-left w-25 newB">Remark</div>
						<div class="float-right w-75 newB"><?=$task_history_get->remark;?></div>
					</div>
				 
					
					<!-- information start -->
					<!-- social links end -->
					 
					
					
			</section>
		</div>
	</div>
</div>
@endsection

<style type="text/css">
	.newB{
		border : solid 1px;
		padding: 5px;
	}
	
	.float-left {
		font-weight : 900;
	}
</style>

@section('scripts')
<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/app-user.css') }}">
@endsection
