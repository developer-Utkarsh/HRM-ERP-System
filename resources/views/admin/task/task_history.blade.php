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
						<a href="{{ route('admin.task.index') }}" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<!-- page users view start -->
			<section class="page-users-view">
				<div class="row">
				 
					<!-- information start -->
					<div class="col-md-12 col-12 ">
						<div class="card">
							<div class="table-responsive">
								<table class="table data-list-view">
									<thead>
										<tr>
											<th>S.No.</th>
											<th>Task Name</th>
											<th>Plan Hour</th>
											<th>Spent Hour</th>
											<th>Status</th>
											<th>Description</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										?>
										@foreach($task_history_get as  $key => $value)
										
										<?php 
										$taskDetail = \App\Task::where('id', $value->task_id)->first();
										?>
										<tr>
											<td class="product-category">{{ $i++ }}</td>
											<td class="product-category">{{ $value->name }}</td>
											<td class="product-category">{{ $value->plan_hour }}</td>
											<td class="product-category">{{ $value->spent_hour }}</td>
											<td class="product-category">{{ $value->status }}</td>
											<td class="product-category">{{ $value->description }}</td>
											<td class="product-category"><?php echo date('d-m-Y',strtotime($taskDetail->date)); ?></td>
										</tr>
										@endforeach							
									</tbody>
								</table>
							</div>    
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					 
					
					
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/app-user.css') }}">
@endsection
