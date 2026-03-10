@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">View Employee</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">View Employee</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('studiomanager.employees.index') }}" class="btn btn-primary mr-1">Back</a>
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
										@if(!empty($employee->image))
										<img src="{{ asset('laravel/public/profile/'. $employee->image) }}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										@else
										<img src="{{ asset('laravel/public/images/test-image.png') }}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										@endif
									</div>
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table>
											<tr>
												<td class="font-weight-bold">Employee Id</td>
												<td>{{ $employee->register_id }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Name</td>
												<td>{{ $employee->name }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Nickname</td>
												<td>{{ $employee->nickname }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Email</td>
												<td>{{ $employee->email }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Contact Number</td>
												<td>{{ $employee->mobile }}</td>
											</tr>
											
										</table>
									</div>
									<div class="col-12 col-md-12 col-lg-5">
										<table class="ml-0 ml-sm-0 ml-lg-0">
											<tr>
												<td class="font-weight-bold">Role</td>
												<td>{{ $employee->role->name }}</td>
											</tr>
											<tr>
											<?php 
											$supervisor_name = "";
											if(!empty($employee->supervisor_id)){
												$supervisor = \App\User::whereIn('id', json_decode($employee->supervisor_id))->get(); 
												if(!empty($supervisor)){
													foreach($supervisor as $supervisorDetail){
														$supervisor_name .=  $supervisorDetail->name.", ";
													}
												}
											}
											?>
												<td class="font-weight-bold">Supervisor Name</td>
												<td>
												<?php
												echo rtrim($supervisor_name, ', ');
												?>
												
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Branch</td>
												<td>
												<?php
												$branch_names = "";
												if(isset($employee->user_branches) && !empty($employee->user_branches)){
													foreach($employee->user_branches as $key => $val) { 
														if(!empty($val->branch->name)) {
															$branch_names .= $val->branch->name .", ";
														}
													}
												}
												echo rtrim($branch_names, ", "); 
												?>
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Degination</td>
												<td>{{ isset($employee->user_details->degination) ?  $employee->user_details->degination : '' }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Employee type</td>
												<td>{{ isset($employee->user_details->employee_type) ?  $employee->user_details->employee_type : '' }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">OTP</td>
												<td>{{ $employee->login_otp }}</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- account end -->
					<!-- information start -->
					<div class="col-md-6 col-12 ">
						<div class="card">
							<div class="card-header">
								<div class="card-title mb-2">Information</div>
							</div>
							<div class="card-body">
								<table>
									<tr>
										<td class="font-weight-bold">Birth Date </td>
										<td>
										<?php 
										if(!empty($employee->user_details->dob)){
											$dob = date("d F Y", strtotime($employee->user_details->dob)); 
											echo $dob;
										}
										?>
										<!--{{ isset($dob) ?  $dob : '' }}-->
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Alternate Number</td>
										<td>{{ isset($employee->user_details->alternate_contact_number) ?  $employee->user_details->alternate_contact_number : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Alternate Email</td>
										<td>{{ isset($employee->user_details->alternate_email) ?  $employee->user_details->alternate_email : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Father's Name</td>
										<td>{{ isset($employee->user_details->fname) ?  $employee->user_details->fname : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Mother's Name</td>
										<td>{{ isset($employee->user_details->mname) ?  $employee->user_details->mname : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Gender</td>
										<td>{{ isset($employee->user_details->gender) ?  $employee->user_details->gender : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Material Status</td>
										<td>{{ isset($employee->user_details->material_status) ?  $employee->user_details->material_status : '' }}</td>
									</tr>

									@if(!empty($employee->user_details->material_status) && $employee->user_details->material_status == "Married")
									<tr>
										<td class="font-weight-bold">Anniversary Date </td>
										<td>
											<?php 
											if(!empty($employee->user_details->anniversary_date)){
												$anniversary_date = date("d F Y", strtotime($employee->user_details->anniversary_date)); 
												echo $anniversary_date;
											}
											?>
										</td>
									</tr>
									@endif

									<tr>
										<td class="font-weight-bold">Current Addres</td>
										<td>{{ isset($employee->user_details->c_address) ?  $employee->user_details->c_address : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Permanent Address</td>
										<td>{{ isset($employee->user_details->p_address) ?  $employee->user_details->p_address : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Blood Group</td>
										<td>{{ isset($employee->user_details->blood_group) ?  $employee->user_details->blood_group : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Joining Date</td>
										<td>{{ isset($employee->user_details->joining_date) ?  date('d-m-Y',strtotime($employee->user_details->joining_date)) : '' }}</td>
									</tr>
									
									<tr>
										<td class="font-weight-bold">Nominee Name</td>
										<td>{{ $employee->nominee_name }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Total Time</td>
										<td>{{ date('H:i', mktime(0,$employee->total_time)) }} Hours</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Agreement</td>
										<td>{{ isset($employee->agreement) ?  $employee->agreement : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Committed hours</td>
										<td>{{ isset($employee->committed_hours) ?  $employee->committed_hours : '' }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					<div class="col-md-6 col-12 ">
						<div class="card">
							<div class="card-header">
								<div class="card-title mb-2">Account Information</div>
							</div>
							<div class="card-body">
								<table>
									<tr>
										<td class="font-weight-bold">Account Number</td>
										<td>{{ isset($employee->user_details->account_number) ?  $employee->user_details->account_number : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Bank Name</td>
										<td>{{ isset($employee->user_details->bank_name) ?  $employee->user_details->bank_name : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">IFSC Code</td>
										<td>{{ isset($employee->user_details->ifsc_code) ?  $employee->user_details->ifsc_code : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Branch</td>
										<td>{{ isset($employee->user_details->bank_branch) ?  $employee->user_details->bank_branch : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Net Salary</td>
										<td>{{ isset($employee->user_details->net_salary) ?  $employee->user_details->net_salary : '' }}</td>
									</tr>
									<tr>
										<td class="font-weight-bold">TDS</td>
										<td>{{ isset($employee->user_details->tds) ?  $employee->user_details->tds : '' }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div> 
					
					
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/app-user.css') }}">
@endsection
