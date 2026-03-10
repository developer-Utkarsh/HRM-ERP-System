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
						<h2 class="content-header-title float-left mb-0">Class Change Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			{{-- <div class="content-header-left col-md-3 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<a href="{{ route('studiomanager.chapters.create') }}" class="btn btn-primary">
							Add Chapter
						</a>
					</div>
				</div>
			</div> --}}
		</div>
         @php 
         if(empty($_GET['typ'])){
	        $dflt = 'active'; 
	     }
	     else{
		     $dflt = ''; 
		 }
         @endphp

		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">				
				<div class="card-content">
					<div class="card-body">						
						<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link {{ !empty($_GET['typ']) && $_GET['typ'] == 1 ? 'active' : $dflt}}" id="home-tab-fill" data-toggle="tab" href="#reschedule" role="tab" aria-controls="home-fill" aria-selected="true">Reschedule Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{ !empty($_GET['typ']) && $_GET['typ'] == 2 ? 'active' : ''}}" id="profile-tab-fill" data-toggle="tab" href="#swap" role="tab" aria-controls="profile-fill" aria-selected="false">Swap Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{ !empty($_GET['typ']) && $_GET['typ'] == 3 ? 'active' : ''}}" id="messages-tab-fill" data-toggle="tab" href="#delete" role="tab" aria-controls="messages-fill" aria-selected="false">Cancel Class Request</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content pt-1">
							<div class="tab-pane {{ !empty($_GET['typ']) && $_GET['typ'] == 1 ? 'active' : $dflt}}" id="reschedule" role="tabpanel" aria-labelledby="home-tab-fill">



								

					<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.classchangerequest.index') }}" method="get" name="filtersubmit">
									<div class="row">
									
										
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $stdiofaulty = \App\user::where('status', '1')->orderBy('id','desc')->get(); ?>  
											<fieldset class="form-group">												
												<select class="form-control select-multiple11" name="reschedule_faulty_id">
													<option value="">Select Any</option>
													@if(count($stdiofaulty) > 0)
													@foreach($stdiofaulty as $key => $faultyValue)
													<option value="{{ $faultyValue->id }}" @if($faultyValue->id == app('request')->input('reschedule_faulty_id')) selected="selected" @endif>{{ $faultyValue->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Status</label>
											
											<fieldset class="form-group">												
												<select class="form-control select-multiple12 reschedule_status" name="reschedule_status" id="">
													<option value="">Select Any</option>
													<option value="pending" @if('pending' == app('request')->input('reschedule_status')) selected="selected" @endif>Pending</option>
													<option value="Approved" @if('Approved' == app('request')->input('reschedule_status')) selected="selected" @endif>Approved</option>
													<option value="Reject" @if('Reject' == app('request')->input('reschedule_status')) selected="selected" @endif>Reject</option>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										 <div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control fdate">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control tdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">
									<input type="hidden" name="typ" value="1">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.classchangerequest.index') }}" class="btn btn-warning">Reset</a>
									<!-- <a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a> -->
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>


								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th>Faculty</th>
												<th>Time</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$fullArray = array();
										//echo '<pre>'; print_r($studios);die;
											if(count($studios1) > 0){
											foreach($studios1 as $key => $studio){
											if(!empty($studio->timetable)){
											foreach($studio->timetable as $count => $time_table){
											if(!empty($time_table->reschedule)){
											foreach($time_table->reschedule as $single_reschedule){
											if(!empty($single_reschedule)){
												$setArray['faculty_name'] = isset($time_table->faculty->name) ? $time_table->faculty->name : '';
												$setArray['from_time'] = isset($single_reschedule->from_time) ? $single_reschedule->from_time : '';
												$setArray['faculty_reason'] = isset($single_reschedule->faculty_reason) ? $single_reschedule->faculty_reason : '';
												$setArray['admin_reason'] = isset($single_reschedule->admin_reason) ? $single_reschedule->admin_reason : '';
												$setArray['status'] = isset($single_reschedule->status) ? $single_reschedule->status : '';
												$created_at = "";
												if(!empty($single_reschedule->created_at)){
													$created_at = $single_reschedule->created_at->format('d-m-Y');
												}
												$setArray['created_at'] = $created_at;
												$setArray['reschedule_id'] = $single_reschedule->id;
												$fullArray[] = $setArray;
											?>
											<!--tr>
												<td>{{ isset($time_table->faculty->name) ? $time_table->faculty->name : '' }}</td>
												<td>{{ isset($single_reschedule->to_time) ? $single_reschedule->to_time : '' }}</td>
												<td>{{ isset($single_reschedule->faculty_reason) ? $single_reschedule->faculty_reason : '' }}</td>
												<td>{{ isset($single_reschedule->admin_reason) ? $single_reschedule->admin_reason : '' }}</td>
												<td>{{ isset($single_reschedule->status) ? $single_reschedule->status : '' }}</td>
												<td>
													@if(!empty($single_reschedule->created_at))
													{{ $single_reschedule->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('admin.reschedule.edit', $single_reschedule->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr-->
											<?php
											}
											}
											}
											}
											}
											}
											}
											
											if(!empty($fullArray)){
												array_multisort(array_map(function($element) {
													return $element['reschedule_id'];
												}, $fullArray), SORT_DESC, $fullArray);
												// echo "<pre>";print_r($fullArray); die;
											
											foreach($fullArray as $val){
											?>
											
											<tr>
												<td><?=$val['faculty_name']?></td>
												<td><?=$val['from_time']?></td>
												<td><?=$val['faculty_reason']?></td>
												<td><?=$val['admin_reason']?></td>
												<td><?=$val['status']?></td>
												<td><?=$val['created_at']?></td>
												<td>
													<a href="{{ route('admin.reschedule.edit', $val['reschedule_id']) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											
											<?php
											}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane {{ !empty($_GET['typ']) && $_GET['typ'] == 2 ? 'active' : ''}}" id="swap" role="tabpanel" aria-labelledby="profile-tab-fill">


								<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.classchangerequest.index') }}" method="get" name="filtersubmit">
									<div class="row">
									
										
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $stdiofaulty = \App\user::where('status', '1')->orderBy('id','desc')->get(); ?>  
											<fieldset class="form-group">												
												<select class="form-control select-multiple21" name="reschedule_faulty_id2">
													<option value="">Select Any</option>
													@if(count($stdiofaulty) > 0)
													@foreach($stdiofaulty as $key => $faultyValue)
													<option value="{{ $faultyValue->id }}" @if($faultyValue->id == app('request')->input('reschedule_faulty_id2')) selected="selected" @endif>{{ $faultyValue->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Status</label>
											
											<fieldset class="form-group">												
												<select class="form-control select-multiple22 reschedule_status" name="reschedule_status2" id="">
													<option value="">Select Any</option>
													<option value="pending" @if('pending' == app('request')->input('reschedule_status2')) selected="selected" @endif>Pending</option>
													<option value="Approved" @if('Approved' == app('request')->input('reschedule_status2')) selected="selected" @endif>Approved</option>
													<option value="Reject" @if('Reject' == app('request')->input('reschedule_status2')) selected="selected" @endif>Reject</option>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										 <div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate2" placeholder="Date" value="{{ app('request')->input('fdate2') }}" class="form-control fdate2">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate2" placeholder="Date" value="{{ app('request')->input('tdate2') }}" class="form-control tdate2">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">
									<input type="hidden" name="typ" value="2">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.classchangerequest.index') }}" class="btn btn-warning">Reset</a>
									<!-- <a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a> -->
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>



								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th>Faculty</th>
												<th>Time Table</th>
												<th>Swap Faculty</th>
												<th>Swap Time Table</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$swapArray = array();
											if(count($studios2) > 0){
											foreach($studios2 as $key => $studio){
											if(!empty($studio->timetable)){
											foreach($studio->timetable as $count => $time_table){
											if(!empty($time_table->swap)){
											foreach($time_table->swap as $single_swap){
											if(!empty($single_swap)){
												$setSwapArray['faculty_name'] = isset($time_table->faculty->name) ? $time_table->faculty->name : '';
												$timetable = "";
												// print_r($single_swap->s_timetable); die;
												if(!empty($single_swap->s_timetable)){
													$timetable = $single_swap->s_timetable->from_time ." - ".$single_swap->s_timetable->to_time;
												}
												$setSwapArray['timetable'] = $timetable;
												$swap_faculty = "";
												if(!empty($single_swap->faculty)){
													$swap_faculty = $single_swap->faculty->name;
												}
												$setSwapArray['swap_faculty'] = $swap_faculty;
												
												$swap_timetable = "";
												if(!empty($single_swap->swap_timetable)){
													$swap_timetable = $single_swap->swap_timetable->from_time ." - ". $single_swap->swap_timetable->to_time;
												}
												$setSwapArray['swap_timetable'] = $swap_timetable;
												$setSwapArray['swap_status'] = isset($single_swap->status) ? $single_swap->status : '';
												
												$created_at = "";
												if(!empty($single_swap->created_at)){
													$created_at = $single_swap->created_at->format('d-m-Y');
												}
												$setSwapArray['created_at'] = $created_at;
												$setSwapArray['swap_id'] = $single_swap->id;
												
												$swapArray[] = $setSwapArray; 
												
											?>
											<!--tr>
												<td>{{ isset($time_table->faculty->name) ? $time_table->faculty->name : '' }}</td>
												<td>
													@if(!empty($single_swap->s_timetable))
													{{ $single_swap->s_timetable->from_time }} - {{ $single_swap->s_timetable->to_time }}
													@endif
												</td>
												<td>
													@if(!empty($single_swap->faculty))
													{{ $single_swap->faculty->name }}
													@endif
												</td>
												<td>
													@if(!empty($single_swap->swap_timetable))
													{{ $single_swap->swap_timetable->from_time }} - {{ $single_swap->swap_timetable->to_time }}
													@endif
												</td>
												<td>{{ isset($single_swap->status) ? $single_swap->status : '' }}</td>
												<td>
													@if(!empty($single_swap->created_at))
													{{ $single_swap->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('admin.swap.edit', $single_swap->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr-->
											<?php
											}
											}
											}
											}
											}
											}
											}
											if(!empty($swapArray)){
												array_multisort(array_map(function($element) {
													return $element['swap_id'];
												}, $swapArray), SORT_DESC, $swapArray);
											
											foreach($swapArray as $val){
											?>
											
											<tr>
												<td><?=$val['faculty_name']?></td>
												<td><?=$val['timetable']?></td>
												<td><?=$val['swap_faculty']?></td>
												<td><?=$val['swap_timetable']?></td>
												<td><?=$val['swap_status']?></td>
												<td><?=$val['created_at']?></td>
												<td>
													<a href="{{ route('admin.swap.edit', $val['swap_id']) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											<?php
											}
											}
											?>
											
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane {{ !empty($_GET['typ']) && $_GET['typ'] == 3 ? 'active' : ''}}" id="delete" role="tabpanel" aria-labelledby="messages-tab-fill">


								<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.classchangerequest.index') }}" method="get" name="filtersubmit">
									<div class="row">
									
										
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $stdiofaulty = \App\user::where('status', '1')->orderBy('id','desc')->get(); ?>  
											<fieldset class="form-group">												
												<select class="form-control select-multiple31" name="reschedule_faulty_id3">
													<option value="">Select Any</option>
													@if(count($stdiofaulty) > 0)
													@foreach($stdiofaulty as $key => $faultyValue)
													<option value="{{ $faultyValue->id }}" @if($faultyValue->id == app('request')->input('reschedule_faulty_id3')) selected="selected" @endif>{{ $faultyValue->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Status</label>
											
											<fieldset class="form-group">												
												<select class="form-control select-multiple32 reschedule_status3" name="reschedule_status3" id="">
													<option value="">Select Any</option>
													<option value="pending" @if('pending' == app('request')->input('reschedule_status3')) selected="selected" @endif>Pending</option>
													<option value="Approved" @if('Approved' == app('request')->input('reschedule_status3')) selected="selected" @endif>Approved</option>
													<option value="Reject" @if('Reject' == app('request')->input('reschedule_status3')) selected="selected" @endif>Reject</option>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										 <div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate3" placeholder="Date" value="{{ app('request')->input('fdate3') }}" class="form-control fdate3">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate3" placeholder="Date" value="{{ app('request')->input('tdate3') }}" class="form-control tdate3">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">
									<input type="hidden" name="typ" value="3">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.classchangerequest.index') }}" class="btn btn-warning">Reset</a>
									<!-- <a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a> -->
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>

								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th>Faculty</th>
												<th>Days</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$cancelArray = array();
											if(count($studios3) > 0){
											foreach($studios3 as $key => $studio){
											if(!empty($studio->timetable)){
											foreach($studio->timetable as $count => $time_table){
											if(!empty($time_table->cancelclass)){
											foreach($time_table->cancelclass as $single_cancelclass){
											if(!empty($single_cancelclass)){
												$setCancelArray['faculty_name'] = isset($time_table->faculty->name) ? $time_table->faculty->name : '';
												$setCancelArray['days'] = isset($single_cancelclass->days) ? $single_cancelclass->days : '';
												$setCancelArray['faculty_reason'] = isset($single_cancelclass->faculty_reason) ? $single_cancelclass->faculty_reason : '';
												$setCancelArray['admin_reason'] = isset($single_cancelclass->admin_reason) ? $single_cancelclass->admin_reason : '';
												$setCancelArray['status'] = isset($single_cancelclass->status) ? $single_cancelclass->status : '';
												$created_at = "";
												if(!empty($single_cancelclass->created_at)){
													$created_at = $single_cancelclass->created_at->format('d-m-Y');
												}
												$setCancelArray['created_at'] = $created_at;
												$setCancelArray['cancel_id'] = $single_cancelclass->id;
												$cancelArray[] = $setCancelArray;
												
												?>
											<!--tr>
												<td>{{ isset($time_table->faculty->name) ? $time_table->faculty->name : '' }}</td>
												<td>{{ isset($single_cancelclass->days) ? $single_cancelclass->days : '' }}</td>
												<td>{{ isset($single_cancelclass->faculty_reason) ? $single_cancelclass->faculty_reason : '' }}</td>
												<td>{{ isset($single_cancelclass->admin_reason) ? $single_cancelclass->admin_reason : '' }}</td>
												<td>{{ isset($single_cancelclass->status) ? $single_cancelclass->status : '' }}</td>
												<td>@if(!empty($single_cancelclass->created_at))
													{{ $single_cancelclass->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('admin.cancelclass.edit', $single_cancelclass->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr-->
											<?php 
											}
											}
											}
											}
											}
											}
											}
											
											if(!empty($cancelArray)){
												array_multisort(array_map(function($element) {
													return $element['cancel_id'];
												}, $cancelArray), SORT_DESC, $cancelArray);
											
											foreach($cancelArray as $val){
											?>
											
											<tr>
												<td><?=$val['faculty_name']?></td>
												<td><?=$val['days']?></td>
												<td><?=$val['faculty_reason']?></td>
												<td><?=$val['admin_reason']?></td>
												<td><?=$val['status']?></td>
												<td><?=$val['created_at']?></td>
												<td>
													<a href="{{ route('admin.cancelclass.edit', $val['cancel_id']) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											
											<?php
											}
											}
											?>
										</tbody>
									</table>
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

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple11').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple12').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple21').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple22').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple31').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple32').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
	}); 
</script>
@endsection
