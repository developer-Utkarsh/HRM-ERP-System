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
						<h2 class="content-header-title float-left mb-0">New Task</h2>
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
								<form action="{{ route('admin.newtask.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="{{ app('request')->input('search') }}" id="myInputSearch" onkeyup="myFunctionSearch()">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Assign</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 assign" name="assign">
													<option value="">Select Any</option>	
													<option value="assign_to_self"  @if('assign_to_self' == app('request')->input('assign')) selected="selected" @endif>Assign To Self</option>													<option value="assign_by_other"  @if('assign_by_other' == app('request')->input('assign')) selected="selected" @endif>Assign By Other</option>													<option value="assign_to_other"  @if('assign_to_other' == app('request')->input('assign')) selected="selected" @endif>Assign To Other</option>
												</select>												
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
											<label for="users-list-status">Role</label>
											<?php $roles = \App\Role::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 role_id" name="role_id">
													<option value="">Select Any</option>
													@if(count($roles) > 0)
													@foreach($roles as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('role_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Degination</label>
											<?php 
											$designation = \App\Designation::where('status', 'Active')->where('is_deleted','0')->orderBy('name')->get();
																						
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 designation_name" name="designation_name">
													<option value="">Select Any</option>
													@if(count($designation) > 0)
													@foreach($designation as $key => $value)
													<option value="{{ $value->name }}" @if($value->name == app('request')->input('designation_name')) selected="selected" @endif>{{ $value->name }}</option>
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
									<a href="{{ route('admin.newtask.index') }}" class="btn btn-warning">Reset</a>
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
						    $statusArray = array();
						    // echo "<pre>"; print_r($task); die;
							foreach($task as  $key => $value){
								$date = $value['date'];
								if(!empty($value['employees'])){
									foreach($value['employees'] as $employee){
										$emp_id = $employee['emp_id'];
										$employee_details = DB::table('users')->where('id', $emp_id)->first();
									?>
									<tr >
										<td><?=$i++;?></td>
										<td class="product-category">{{ isset($employee_details->name) ?  $employee_details->name : '' }}</td>
										<td class="product-category">{{ $date }}</td>
										<td class="product-category" colspan="4">
										<table class="table data-list-view" style="background: #f7f7f73d;">
											<?php
											$editView = false;
											foreach($employee['task_array'] as $task_details){
												if($task_details['is_transferred']==0){
													$editView = true;
												}
												?>
												<tr>
													<td style="width:200px;"><?=$task_details['task_title']?></td>
													<td><?=$task_details['plan_hour']?></td>
													<td><?=$task_details['status']?></td>
													<td>
													<a href="{{ route('admin.newtask.task_history', 
													[
														'task_id' => $task_details['task_id']
													]) }}" class="btn btn-sm btn-primary waves-effect waves-light">History</a>
													</td>
												</tr>
												<?php
											}
											?>
											
											
										</table>
										</td>
												
										<td class="product-action">
										<?php
										if($date >= date('Y-m-d') && ($editView==true)){
										?>
											<a href="{{ route('admin.newtask.edit',[ 'user_id' => $emp_id, 'date'=>strtotime($date)]) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
											</a>
										<?php
										}
										?>
											
										</td>
									</tr>
									<?php
									$statusArray = array();
									}
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
		data.search = $('.search').val(),
		data.role_id = $('.role_id').val(),
		data.assign = $('.assign').val(),
		data.designation_name = $('.designation_name').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(), 
	window.location.href = "<?php echo URL::to('/admin/'); ?>/newtask-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>
@endsection
