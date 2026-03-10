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
						<h2 class="content-header-title float-left mb-0">Open Task</h2>
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
								<form action="{{ route('admin.newtask.open-task') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-verified">&nbsp;&nbsp;</label>
											<fieldset class="form-group">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.newtask.open-task') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Create By</th>
								<th>Task Title</th>
								<th>Task Date</th>
								<th>Plan Hour</th>
								<th>Spent Hour</th>
								<th>Status</th>
								<!--<th>Task History</th>
								<th>Action</th>-->
							</tr>
						</thead>
						<tbody >
						<?php
						$i = 1; 
						if(count($open_task) > 0){
							foreach($open_task as  $key => $value){
								$created_name = DB::table('users')->where('id', $value['task_added_by'])->first();
							?>
							<tr>
								<td><?=$i++;?></td>
								<td class="product-category">{{ isset($created_name->name) ? $created_name->name : '' }}</td>
								<td class="product-category">{{ isset($value['task_title']) ?  $value['task_title'] : '' }}</td>
								<td class="product-category">{{ isset($value['task_date']) ?  $value['task_date'] : '' }}</td>
								<td class="product-category">{{ isset($value['plan_hour']) ?  $value['plan_hour'] : '' }}</td>
								<td class="product-category">{{ isset($value['spent_hour']) ?  $value['spent_hour'] : '' }}</td>
								<td class="product-category">{{ isset($value['status']) ?  $value['status'] : '' }}</td>
							</tr>
							<?php
							}
						}
						?>
							
							<style>
							table{border: 2px solid #f8f8f8;}
							</style>
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
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

$("body").on("click", "#download_excel", function (e) {
	/* if ($userTable.data().count() == 0) {
		swal("Warning!", "Not have any data!", "warning");
		return;
	} */
	var data = {};
		data.branch_id = $('.branch_id').val(),
		data.name = $('.name').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/task-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>
@endsection
