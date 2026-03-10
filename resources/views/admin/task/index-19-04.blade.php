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
						<h2 class="content-header-title float-left mb-0">Task</h2>
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
								<form action="{{ route('admin.task.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" placeholder="Name, EMP Code" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.task.index') }}" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
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
								<th>Employee Name</th>
								<th>Date</th>
								<th>Task</th>
								<th>Plan Hour</th>
								<th>Status</th>
								<th>Task History</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody >
						<?php
						$i = 1;
							foreach($task as  $key => $value){
							if(!empty($value->task_details) && count($value->task_details) > 0){
							?>
							<tr >
								<td><?=$i++;?></td>
								<td class="product-category">{{ isset($value->user->name) ?  $value->user->name : '' }}</td>
								<td class="product-category">{{ isset($value->date) ?  $value->date : '' }}</td>
								<td class="product-category" colspan="4">
								<table class="table data-list-view" style="background: #f7f7f73d;">
									<?php
									foreach($value->task_details as $task_details){
										?>
										<tr>
											<td style="width:200px;"><?=$task_details->name?></td>
											<td><?=$task_details->plan_hour?></td>
											<td><?=$task_details->status?></td>
											<td>
											<a href="{{ route('admin.task.task_history', 
											[
												'task_id' => $value->id, 
												'task_detail_id' => $task_details->id
											]) }}" class="btn btn-sm btn-primary waves-effect waves-light">History</a>
											</td>
										</tr>
										<?php
									}
									?>
									
									
								</table>
								</td>
								<td class="product-action">
									
									@if(Auth::user()->role_id != 21)
									<a href="{{ route('admin.task.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.task.task_delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Task')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									@endif
									<a href="{{ route('admin.task.view', $value->id) }}">
										<span class="action-edit"><i class="feather icon-eye"></i></span>
									</a>
								</td>
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
