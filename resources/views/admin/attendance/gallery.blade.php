@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Attendance Gallery</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.attendance.gallery') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name, EMP Code" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<?php
										if(Auth::user()->user_details->degination != "CENTER HEAD"){
										 
										?>
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select Any</option>
													<option value="jodhpur" @if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>Jodhpur</option>
													<option value="jaipur" @if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')) selected="selected" @endif>Jaipur</option>
													<option value="delhi" @if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')) selected="selected" @endif>Delhi</option>
													<option value="prayagraj" @if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>Prayagraj</option>
													<option value="indore" @if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')) selected="selected" @endif>Indore</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Branch</label>
												@if(count($allBranches) > 0)
												<select class="form-control get_role select-multiple1 branch_id"  id="se_branch_id" name="branch_id">
													<option value=""> - Select Any - </option>
													@foreach($allBranches as $value)
													<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('branch_id')) selected="selected" @endif>{{ $value['name'] }}</option>
													@endforeach
												</select>
												@endif
											</div>
										</div>
										
										<?php }
										elseif(Auth::user()->user_details->degination == "CENTER HEAD"){
											?>
											<input type="hidden" name="branch_id" value="<?=Auth::user()->user_branches[0]->branch_id?>"/>
											<?php
										}
										?>
										
										<div class="col-md-3">
											&nbsp;
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
											<a href="{{ route('admin.attendance.gallery') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row"> 
					<?php
					$i = 1;
					// print_r($responseArray);
					foreach($responseArray as  $key => $value){
						foreach($value['time'] as $time_details){
					?>
						<div class="col-md-2" style="margin-bottom:50px;">
						<?php
						if(!empty($time_details->image)){
							$image = $time_details->image;
						}
						else{
							$image = "images/default-image.png";
						}
						?>
						<img src="{{ asset('laravel/public/'.$image)}}" style="width:100%;height:150px;">
						<h3 style="text-align:center;"><?=$value['name']?></h3>
						<p style="text-align:center;">		 
						<?php
						echo "Date : ". date('d-m-Y',strtotime($time_details->date));
						echo "<br>";
						if(!empty($time_details->type)){
							if($time_details->type=="In"){
								echo "In : ". date("h:i A", strtotime($time_details->time));
							}
							else if($time_details->type=="Out"){
								echo "Out : ".date("h:i A", strtotime($time_details->time));
							}
						}
						
						?>
						</p>
						
						<?php
						if(!empty($time_details['out_time'])){
							echo date("h:i A", strtotime($time_details['out_time']));
						}
						?>
						</div>
								<?php
							}
							?> 
						 
					
					<?php
					}
					?>                 
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	function locationBranch(value){
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.employee.get-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
			}
		});
	}
</script>
@endsection
