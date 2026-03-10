@extends('layouts.admin')
@section('content')
@php
	$user = Auth::user();
@endphp
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">All Requests List</h2>
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
								<form action="{{ route('admin.newcoupon.historyList') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Coupon Id, Coupon Title, Remark" value="{{ app('request')->input('search') }}" id="myInputSearch">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" >		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.newcoupon.historyList') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>		
				<div class="table-responsive">
					<table class="table data-list-view" id="coupon_table">
	                                                  <thead>
							<tr>
								<th>Coupon Id</th>
								<th>Categaory Name</th>
								<th>Sub Category</th>
								<th>Coupon Title</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Coupon value</th>
								<th>Max Discount</th>
								<th>Max Usage</th>
								<th>Coupon Type</th>
								<th style="max-width:100px;">Data Link</th>
								<th>Mode</th>
								<th>Course Type</th>
								<th>Reason</th>
								<th>Requested By</th>
								<th>Approved/Rejected By</th>
								<th>Reject Remark</th>
								<th>Approve/Reject Status</th>
								<th>Created By (ERP)</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($coupons) > 0){
								$i =1; 
									foreach($coupons as $c){ 
									?>
									<tr>
										<td>{{ $c->coupon_id }}</td>
										<td>{{ $c->categaory_name }}</td>
										<td>{{ $c->sub_category }}</td>
										<td>{{ $c->coupon_title }}</td>
										<td>{{ date('d/M/Y h:i A',strtotime($c->start_date)) }}</td>
										<td>{{ date('d/M/Y h:i A',strtotime($c->end_date)) }}</td>
										<td><?=$c->coupon_value .  ($c->discount_type == "2" ? "%" : "/-")?></td>
										<td>{{ $c->max_discount }}</td>
										<td>{{ $c->max_usage }}</td>
										<td><?php
										if($c->coupon_type==1){
											echo "User Dependent";
										}
										else if($c->coupon_type==2){
											echo "User + Course Dependent";
										}
										else if($c->coupon_type==0){
											echo "Course Dependent";
										}
										?></td>
										<td>{{ $c->data_link }}</td>
										<td>{{ $c->coupon_mode==1?'Manual':'Auto' }}</td>
										<td>
										<?php
										if($c->course_type==1){
											echo "Prime";
										}
										else if($c->course_type==2){
											echo "Both";
										}
										else if($c->course_type==0){
											echo "Standard";
										}
										?>
										</td>
										<td><?php
											if($c->coupon_id > 0){
												echo $c->remark;
											}
											else{
												echo $c->reason;
											}
										?></td>
										
										<td>{{ $c->created_by_name ?? ''}}</td>
										<td>{{ $c->status_updated_by_name ?? '' }}</td>
										<td>{{ $c->reject_remark ?? '-' }}</td>

										<td>
												@php
													$statusText = '-';
													if ($c->status == 0) $statusText = 'Pending';
													if ($c->status == 1) $statusText = 'Approved';
													if ($c->status == 2) $statusText = 'Rejected';
													@endphp
													<?php
													if($c->status === 0){
														if(Auth::user()->id == 7087 || Auth::user()->id == 901){
														?>

														<div class="status-action" data-id="{{ $c->id }}">
															<select class="form-select status-dropdown">
																<option value="" disabled selected>Select Status</option>
																<option value= "1">Approved</option>
																<option value= "2">Rejected</option>
															</select>
															<button class="btn btn-primary btn-sm submit_status">Submit</button>
															<span class="update_status_wait text-danger"></span>
														</div>
														<?php }
													}
													else{
														echo $statusText; 
													}
													?>
										</td>
										<td><?php
											if($c->status == 1 && $c->erp_mark_status==0){
												if(Auth::user()->id == 8088 || Auth::user()->id == 1665 || Auth::user()->id == 901){												
													?>
													<button class="btn btn-primary btn-sm mark_on_erp" data-id="{{ $c->id }}">Create on ERP</button>
													<?php
												}
											}
											else if($c->erp_mark_status==1){
												echo $c->erp_mark_updated_by_name??'';
											}
										?>
										</td>
									</tr>
									<?php $i++; 
									} 
								}else{
								?>			
								<tr>
									<td class="text-center" colspan="5">No record found</td>
								</tr>
								<?php 
								} ?>
						</tbody>
					</table>	
				</div>                   
			</section>
		</div>
	</div>
</div> 
				
@endsection
@section('scripts')
<script type="text/javascript">
$(document).on('click', '.submit_status', function () {
    const wrapper = $(this).closest('.status-action');
    const id = wrapper.data('id');
    const newStatus = wrapper.find('.status-dropdown').val();

    if (!newStatus) {
        alert('Please select a status');
        return;
    }
	
    
    let rejectRemark = '';
    if (newStatus == 2) {
		rejectRemark = prompt("Please enter reason for rejection:");
		if (!rejectRemark) {
			alert("Rejection reason is required");
			return;
		}
	}
	
	$(this).css('display','none');
	$(".update_status_wait").text('Please Wait..');
    
    $.ajax({
        type: 'POST',
        url: '{{ route("admin.newcoupon.updateStatus") }}',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            status: newStatus,
            reject_remark: rejectRemark,
			action:'approved_by'
        },
        success: function(response){
			if(response.status=='success'){
				alert('Status updated!');
				window.location.reload();
			}
			else{
				alert('Something went wrong. Please try again.');
				$(".update_status_wait").text('');
			}
        },
        error: function(){
            alert('Error updating status!');
			$(".update_status_wait").text('');
        }
    });
});

$(document).on('click', '.mark_on_erp', function (e) {
	e.preventDefault(); // stop immediate action
	const id = $(this).data('id');
	if (confirm("Are you sure you are created on ERP?")) {

		$.ajax({
			type: 'POST',
			url: '{{ route("admin.newcoupon.updateStatus") }}',
			data: {
				_token: '{{ csrf_token() }}',
				id: id,
				action:'mark_on_erp'
			},
			success: function(response){
				alert('Status updated!');
				window.location.reload();
			},
			error: function(){
				alert('Error updating status!');
			}
		});
	}
});
</script>
@endsection

