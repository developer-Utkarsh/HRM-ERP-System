@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Meeting Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="row pb-2">
								<div class="col-lg-3"><b>Date </b></div>
								<div class="col-lg-9"><b>: </b> {{ date('d-m-Y',strtotime($appointment_result->appointment_date)) }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Start Time </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->start_time }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>End Time </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->end_time }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Title </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->title }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Meeting Agenda </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->description }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Meeting Creator </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->user_name }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Branch </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->branch_name }}</div>
							</div>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Meeting Place </b></div>
								<div class="col-lg-9"><b>: </b> {{ $appointment_result->meeting_place_name }}</div>
							</div>
							
							<div class="row pb-2">
								<div class="col-lg-3"><b>Type </b></div>
								<div class="col-lg-9"><b>: </b> <?php 
									switch($appointment_result->type){
										case 1 : $type = "Physical";	break;
										case 2 : $type = "virtual";		break;
										case 3 : $type = "Both";		break;
										default : $type = "-";			break;
									}
									
									echo $type;
								?></div>
							</div>
							
							<?php if($appointment_result->url!=''){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>URL </b></div>
								<div class="col-lg-9"><b>: </b> 
									<?php 
										if($appointment_result->appointment_date >= date('Y-m-d')){ 
											echo '<a href="'.$appointment_result->url.' target="_blank" class="btn-success px-2"">Join Now</a>';
										}else{
											echo 'URL Expired';
										}
									?>
								</div>
							</div>
							<?php } ?>
							
							<?php if($appointment_result->other_city!=""){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Other </b></div>
								<div class="col-lg-9"><b>: </b>{{ $appointment_result->other_city }}	</div>
							</div>
							<?php } ?>
							
							<?php if($appointment_result->other_place!=""){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Other Place</b></div>
								<div class="col-lg-9"><b>: </b>{{ $appointment_result->other_place }}	</div>
							</div>
							<?php } ?>
							
							<?php if($appointment_result->is_group==1){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Other Place</b></div>
								<div class="col-lg-9"><b>: </b>
									<?php 
										if($appointment_result->is_group==1){
											echo 'Yes';
										}else{
											echo 'No';
										}
									?>
								</div>
							</div>
							<?php } ?>
							
							
							<?php if($appointment_result->key_points!=""){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Key Points</b></div>
								<div class="col-lg-9"><b>: </b>{{ $appointment_result->key_points }}</div>
							</div>
							<?php } ?>
							
							<?php if($appointment_result->cancel_reason!=""){ ?>
							<div class="row pb-2">
								<div class="col-lg-3"><b>Cancel Reason</b></div>
								<div class="col-lg-9"><b>: </b>{{ $appointment_result->cancel_reason }}	</div>
							</div>
							<?php } ?>
							
							
							
							<div class="row pb-2">
								<div class="col-lg-3"><b>Meeting Status</b></div>
								<div class="col-lg-9"><b>: </b>
									<?php 
										if($appointment_result->status==2){
											echo 'Canceled';
										}else if($appointment_result->status==3){
											echo 'Deleted';
										}else{
											echo 'Active';
										}
									?>
								</div>
							</div>
						
						</div>
					</div>
				</div>
				                
			</section>
		</div>
	</div>
</div>


@endsection
