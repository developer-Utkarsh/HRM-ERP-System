@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Inventory Dashboard</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
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
								<form action="{{ route('studiomanager.inventory-dashboard') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple" name="batch_id">
													<option value="">Select Any</option>
													@foreach($batch as $value)
													<option value="{{ $value->batch_code }}" @if($value->batch_code == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.inventory-dashboard') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
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
								<th>Quantity</th>
								<th>Stock</th>
								<th>Total Student</th>
								<th>Given Count</th>
								<th>Pending</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($inventory) > 0){
									$i = 1;
									foreach($inventory as $key => $value){
										$query = DB::connection('mysql2')->table("tbl_registration")->select(DB::raw('count(assign_inventory) as given'))->whereRaw("find_in_set($value->id , assign_inventory)")->get();
										foreach($query as $key => $assign){
											$total = DB::connection('mysql2')->table("tbl_registration")->where("batch_id", $batch_id)->count();
											
											$pending = $total - $assign->given;
											
											$stock	= $value->quantity - $assign->given;
							?>
							<tr>
								<td>{{ $i++ }}</td>
								<td>{{ $value->name }}</td>
								<td>{{ $value->quantity }}</td>
								<td>{{ $stock }}</td>
								<td>{{ $total }}</td>
								<td><a href="{{ route('studiomanager.student-inventory-view',[$batch_id, $value->id,1]) }}" title="Click Here">{{ $assign->given }}</a></td>
								<td><a href="{{ route('studiomanager.student-inventory-view',[$batch_id, $value->id,2]) }}" title="Click Here">{{ $pending }}</a></td>
							</tr>
							<?php 		}
									}
								}else{ 
							?>
							<tr>
								<td colspan="7" class="text-center">No Inventory Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>

<style>
.table thead th {
	font-size:16px !important;
}

.table tbody td{
	font-size:16px !important;
}

</style>
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
</script>
@endsection
