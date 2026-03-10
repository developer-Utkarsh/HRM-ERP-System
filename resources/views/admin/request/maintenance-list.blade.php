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
						<h2 class="content-header-title float-left mb-0">Maintenance List</h2>
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
								<form action="" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">Req. No.</label>
											<fieldset class="form-group">
												<input type="text" class="form-control rnumber" name="rnumber" value="{{ app('request')->input('rnumber') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">Po / Wo No. </label>
											<fieldset class="form-group">
												<input type="text" class="form-control pwnumber" name="pwnumber" value="{{ app('request')->input('pwnumber') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Product Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control pname" name="pname" value="{{ app('request')->input('pname') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">User Name </label>
											<fieldset class="form-group">
												<input type="text" class="form-control uname" name="uname" value="{{ app('request')->input('uname') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Department</label>
											<?php 
											$department_id = app('request')->input('department_id');
											$department = \App\Department::where('is_deleted', '0'); 
											if(!empty($department_id)){
												$department->where('id', $department_id);
											}
											$department = $department->orderBy('id','asc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 department_id" name="department_id">
													<option value="">Select Any</option>
													@if(count($department) > 0)
													@foreach($department as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('department_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
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
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<?php 										
											if( Auth::user()->role_id ==29 || Auth::user()->role_id ==31 || Auth::user()->id ==6828 || Auth::user()->id == 6859){
										?>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">PO Status</label>
											<fieldset class="form-group">												
												<select class="form-control po_status" name="po_status">
													<option value="">Select</option>													
													<option value="0">Pending</option>
													<option value="1">Approved</option>
													<option value="2">Reject</option>
												</select>												
											</fieldset>
										</div>
										<?php } ?>
										
										<div class="col-12 text-right">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.po-list') }}" class="btn btn-warning">Reset</a>
											<!--<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>-->
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-12">
											<strong for="users-list-role">D.H. : Department Head, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">I.T. : Inventory Team, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">P.T. : Purchase Team, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">D.M. : Decision Maker / PO Approval Team, &nbsp;&nbsp;</strong>
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
								<th>Created Date</th>	
								<th>Location</th>	
								<th>Requirements</th>	
								<th>Action</th>	
							</tr>
						</thead>
						<tbody>			
							@if(count($notification) > 0)
								@foreach($notification as  $key => $value)
									
								<tr style="">
									<td>{{ $pageNumber++ }}</td>
									<td>{{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}</td>
									<td>{{ $value->bname }}</td>
									<td>
										<b>(REQ- {{ $value->unique_no }} - {{ $value->dname }} )</b> - {{ $value->message }} 
										<a href="javascript:void(0)"  data-id="{{ $value->unique_no }}" class="get_edit_data text-primary">View</a>										
									</td>
									<td>
										<?php if(Auth::user()->role_id == 34){ ?>
										<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 1 ]) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<?php }else{ echo '-'; } ?>
									</td>
								</tr>
								@endforeach			
							@else
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							@endif
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $notification->appends($params)->links() !!}
					</div>
				</div>
				                  
			</section>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-xl" id="myModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Requisition</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body fill-name">
				
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="invoicePop" tabindex="-1" role="dialog">
	<form action="{{ route('admin.invoice-details') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Invoice Details Update</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="asset_noti_id" value="" class="asset_noti_id"/> 
					<div>
						<label>Invoice Date</label>
						<input type="date" class="form-control" name="invoice_date" placeholder="Invoice Date" required /> 
					</div>
					<div class="pt-2">
						<label>Invoice Number</label>
						<input type="text" class="form-control" name="invoice_number" value="" required /> 
					</div>
					<div class="pt-2">
						<label>HandOver To Accounts</label>
						<input type="date" class="form-control" name="handover_accounts" value="" required /> 
					</div>
					<div class="pt-2">
						<label>Invoice Attachment</label>
						<input type="file" class="form-control" name="attachment" value="" required /> 
					</div>
					<div class="pt-2">
						<label>Amount</label>
						<input type="text" class="form-control" name="amount" value="" required /> 
					</div>
					<div class="pt-2">
						<label>Vendor</label>
						<select name="vendor" class="form-control" required>
							<option value="">-- Select --</option>
							<?php 
								$buyer = \App\Buyer::where('is_deleted', '0')->get();
								foreach($buyer as $b){
							?>
							<option value="<?=$b->id;?>"><?=$b->name;?></option>
							<?php } ?>
						</select>
					</div>
					<div class="pt-2">
						<label>Remark</label>
						<textarea name="remark" class="form-control" required></textarea>
					</div>
					<div class="pt-2"><button type="submit" class="btn btn-success">Submit</button></div>
				</div>
			</div>
		</div>
	</form>
</div>


<div class="modal fade" id="reqQuotation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.quotation-add') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Request Quotation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="appointment_id" value="" class="appointment_id"/> 
					<textarea class="form-control" name="quotation" rows="4" placeholder="Quotation"></textarea> 
					</br>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>




@if(isset($_GET['po']))
    <div class="alert alert-success">        
		<script>window.open('po/'+<?=$_GET['id'];?>, '_blank');</script>
    </div>
@endif


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
	});
	
	$(".get_invoice_data").on("click", function() {  		
		var request_id = $(this).attr("data-id"); 
		$('#invoicePop').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
		$('.asset_noti_id').val(request_id);		
	}); 
	
	$(".get_edit_data").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
				
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.show-requisition') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});		
	}); 
	
	
	$(".send_quotation").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#reqQuotation').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
				
		$('.appointment_id').val(request_id);		
	}); 
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.rnumber 		= $('.rnumber').val(),
		data.fdate 		= $('.fdate').val(),
		data.tdate 		= $('.tdate').val(),
		data.po_status  = $('.po_status').val(),
		data.department_id  = $('.department_id').val(),
		data.branch_id  = $('.branch_id').val(),
		data.pname  = $('.pname').val(),
		data.uname  = $('.uname').val(),
		data.vendor  = $('.vendor').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/po-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	
	$("body").on("click", "#download_report", function (e) {
		var data = {};					
		data.fdate 		= $('.fdate').val(),
		data.tdate 		= $('.tdate').val(),
		data.po_status  = $('.po_status').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/po-payment-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});

</script>
@endsection