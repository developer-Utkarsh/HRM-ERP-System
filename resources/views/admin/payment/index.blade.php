@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Payment Links</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
						<a href="{{ route('admin.payment.create')}}" class="btn btn-primary">Create Links</a>
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
								<form action="{{ route('admin.payment.index') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">User Mobile</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="mobile" placeholder="Mobile Number" value="{{ app('request')->input('mobile') }}">
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Status</label>
											<fieldset class="form-group">
												<select name="status" class="form-control">
													<option value="">--Select--</option>
													<option value="created">Created</option>
													<option value="paid">Paid</option>
												</select>
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.payment.index') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Agent</th>
								<th>Stu. No.</th>
								<th>Course ID</th>
								<th>Course</th>
								<th>Version</th>
								<th>Amount</th>
								<th>Payment Status</th>
								<th>Remark</th>
								<th>Book Add Status</th>
								<th>Created At</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){ 
									$i =1; foreach($record as $re){ 
							?>
							<tr>
								<td><?=$i++;?></td>
								<td><?=$re->name;?></td>
								<td><?=$re->mobile;?></td>
								<td><?=$re->course_id;?></td>
								<td><?=$re->course_title;?></td>
								<td>
									@if($re->plan_type==1)
                                      Prime
									@else
									 Standard
									@endif
								</td>
								<td><?=$re->course_sp;?></td>
								<td>
									<?=$re->status?> <br>
									@if($re->status=='created')
									 <span class="btn-warning btn-sm btn checkStatus" data-payment_link_id="{{$re->payment_link_id}}">Check</span>
									@endif
								</td>
								<td><?=$re->remark?></td>
								<td><?php if($re->course_status==1){ echo 'Added'; }else{ echo 'Not Added'; }?></td>
								<td><?=$re->created_at?></td>
							</tr>
							<?php } 
								}else{ 
							?>
							<tr>
								<td colspan="10" class="text-center">No Record Found</td>
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
		$('#example').DataTable();
	});	
	
	$(document).on('click', '.checkStatus', function () {
		const payment_link_id = $(this).data('payment_link_id');
		$.ajax({
			type : 'GET',
			url : '{{ route('payment.npf-callback') }}?payment_link_id='+payment_link_id,		
			success: function (response) {
				alert(response.message); 
			}
		});
	});
</script>
@endsection
