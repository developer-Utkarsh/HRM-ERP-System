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
						<h2 class="content-header-title float-left mb-0">Student List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
							
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.inventory-dashboard') }}" class="btn btn-primary mr-1">Back</a>
						<button onClick="ExportToExcel()" class="btn btn-primary">Report</button>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<div class="card">
				<div class="card-content collapse show">
					<div class="card-body">
						<div class="users-list-filter">
							<form action="{{ route('admin.student-inventory-view',[$batch_id,$id,$type]) }}" method="get" name="filtersubmit">
								<div class="row">
									<div class="col-12 col-sm-6 col-lg-3">
										<label for="users-list-status">Reg. No.</label>
										<input type="text" name="reg_no" value="" class="form-control"/>
									</div>
									<div class="col-12 col-sm-6 col-lg-3">
										<label for="" style="">&nbsp;</label>
										<fieldset class="form-group">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('admin.student-inventory-view',[$batch_id,$id,$type]) }}" class="btn btn-warning">Reset</a>
										</fieldset>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<section id="data-list-view" class="data-list-view-header">
				<div class="table-responsive">
					<table class="table data-list-view" id="my-table-id">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Reg. No.</th>								
							</tr>
						</thead>
						<tbody>
							<?php
								$i = 1;
								foreach($inventory as $key => $in){
							?>
							<tr>
								<td><?=$i;?></td>
								<td>{{ $in->s_name }}</td>
								<td>{{ $in->reg_number }}</td>							
							</tr>
								<?php $i++; } ?>
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
	
	function ExportToExcel(){
	   var htmltable= document.getElementById('my-table-id');
	   var html = htmltable.outerHTML;
	   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	}
</script>
@endsection
