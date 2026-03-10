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
						<h2 class="content-header-title float-left mb-0">Location List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="{{ route('admin.request.reports.location-request-list') }}" method="get" name="filtersubmit">
									<div class="row pt-2">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">Month</label>
												<input type="month" class="form-control" name="fmonth" placeholder="Title" value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php 
											$branch_location = app('request')->input('branch_id');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('id', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_id" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3 pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.reports.location-request-list') }}" class="btn btn-warning">Reset</a>
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
								<th>Date</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product</th>
								<th>Date Of Purchase</th>
								<th>UOM</th>
								<th>Qty</th>								
								<th>Rate</th>
								<th>Amount</th>										
								<th>GST</th>		
								<th>GST Amt</th>		
								<th>Total Amt</th>		
								<th>Employee</th>		
								<th>Name Of Vendor</th>		
								<th>PO No</th>		
								<th>MRL No</th>		
								<th>Type</th>		
								<th>GRN</th>		
								<th>Location</th>		
								<th>Assets/Non Asset</th>	
							</tr>
						</thead>
						<tbody>
							<?php 
								$i =1;
								foreach($record as $re){
							?>
							<tr>
								<td>{{ $i }}</td>
								<td>{{ date('d-m-Y', strtotime($re->created_at)) }}</td>
								<td>{{ $re->cname }}</td>
								<td>{{ $re->sname }}</td>
								<td>{{ $re->pname }}</td>
								<td><?php if(!empty($re->pdate)){ echo date('d-m-Y', strtotime($re->pdate)); } ?></td>
								<td>{{ $re->uom }}</td>
								<td>{{ $re->qty }}</td>
								<td>{{ $re->rate }}</td>
								<td>{{ $re->amount }}</td>
								<td>{{ $re->gst_rate }}</td>
								<td>{{ $re->gst_amt }}</td>
								<td>{{ $re->total }}</td>
								<td>{{ $re->uname }}</td>
								<td>{{ $re->vendro_name }}</td>
								<td><?php if($re->po_no!=0){ echo 'UTKPO-'.$re->po_location.'-'.$re->po_no.'/'.$re->po_month; } ?></td>
								<td>REQ-{{ $re->unique_no }}</td>
								<td><?php if($re->request_type=='1'){ echo 'WRL'; }else{ echo 'MRL';} ?></td>								
								<td>-</td>
								<td>{{ $re->bname }}</td>
								<td>{{ ucwords($re->branch_location) }}</td>
							</tr>	
							<?php $i++;} ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>

<style type="text/css">
	.table tbody td {
		word-break: break-word;
	}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
</script>
@endsection