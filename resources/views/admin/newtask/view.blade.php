<?php
use App\User;
?>
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
						<h2 class="content-header-title float-left mb-0">View Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">View Details</a>
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
					<!-- account start -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Account</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="users-view-image">
										@if(!empty($newtask->user->image))
										<img src="{{ asset('laravel/public/profile/'. $newtask->user->image) }}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										@else
										<img src="{{ asset('laravel/public/images/test-image.png') }}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										@endif
									</div>
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table>
											<tr>
												<td class="font-weight-bold">Employee Id</td>
												<td>{{ $newtask->user->register_id }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Name</td>
												<td>{{ $newtask->user->name }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Email</td>
												<td>{{ $newtask->user->email }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Contact Number</td>
												<td>{{ $newtask->user->mobile }}</td>
											</tr>
										</table>
									</div>
									 
								</div>
							</div>
						</div>
					</div>
					<!-- account end -->
					<!-- information start -->
					<div class="col-md-12 col-12 ">
						<div class="card">
							<div class="table-responsive">
								<table class="table data-list-view">
									<thead>
										<tr>
											<th>S.No.</th>
											<th>Task Title</th>
											<th>Plan Hour</th>
											<th>Spent Hour</th>
											<th>Status</th>
											<th>Description</th>
											<th>Assigned User</th>
											<th>Task History</th>
										</tr>
									</thead>
									<tbody>
									<?php $i = 1; ?>
										<tr>
											<td class="product-category">{{ $i++ }}</td>
											<td class="product-category">{{ $newtask->task_title }}</td>
											<td class="product-category">{{ $newtask->plan_hour }}</td>
											<td class="product-category">{{ $newtask->spent_hour }}</td>
											<td class="product-category">{{ $newtask->status }}</td>
											<td class="product-category">{{ $newtask->task_description }}</td>
											
											<td class="product-category">
											<?php
											$assigned_username = '-';
											if(!empty($newtask->task_added_to)){
												$task_added_to = $newtask->task_added_to;
												
												$addedname = User::where('id', $newtask->task_added_to)->first();
												if(!empty($addedname)){
													$assigned_username = $addedname->name;
												}
											}
											echo $assigned_username;
											?>
											
											</td>
											<td class="product-category">
											<a href="{{ route('admin.newtask.task_history', 
												[
													'task_id' => $newtask->id
												]) }}" class="btn btn-sm btn-primary waves-effect waves-light">History</a>
											</td>
											
											
										 
										</tr>							
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
