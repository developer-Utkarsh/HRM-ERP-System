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
						<h2 class="content-header-title float-left mb-0">Student Inventory Track</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.student-inventory-track') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Registration Number</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="reg_no" placeholder="Search" value="{{ app('request')->input('reg_no') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.student-inventory-track') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div> 
									</div>
								</form>
							</div>
							<div>
								<?php if(!empty($student->s_name)){ ?><div><b>Student Name : </b> {{ $student->s_name }}</div><?php } ?>
								<?php if(!empty($student->batch_id)){ ?><div><b>Batch Code : </b>{{ $student->batch_id }}</div><?php } ?>
								<?php if(!empty($student->batch)){ ?><div><b>Batch Name : </b>{{ $student->batch }}</div><?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Inventory Name</th>
								<th>Given Date & Time</th>								
							</tr>
						</thead>
						<tbody>
							<?php 					
								if(!empty($student)){
									$get_inventory = DB::table("given_inventory")
										->select('given_inventory.*','batch_inventory.name')
										->leftJoin('batch_inventory','batch_inventory.id','given_inventory.inventory_id')
										->where('registration_no',$reg_no)->get();
									if(count($get_inventory) > 0){												
										$i = 1;
										foreach($get_inventory as $gi){
							?>
							<tr>
								<td><?=$i;?></td>
								<td>{{ $gi->name }}</td>
								<td>{{ date('d-m-y h:i:s', strtotime($gi->created_at)) }}</td>
							</tr>	
							<?php 		$i++; }
									}else{
							?>
							<tr>
								<td colspan="5" class="text-center">No Inventory Found</td>
							</tr>							
							<?php 
									}
								}else{ 
							?>
							<tr>
								<td colspan="5" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>							
						</tbody>
					</table>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$('.select-multiple1').select2({
		placeholder: "Select Batch",
		allowClear: true
	});
</script>
@endsection
