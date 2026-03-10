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
						<h2 class="content-header-title float-left mb-0">Vendor Invoice</h2>
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
								<form action="{{ route('admin.request.vendor-invoice') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-3 col-sm-3 col-lg-3">
											<label for="users-list-verified">MRL / PO Number</label>
											<fieldset class="form-group">
												<input type="text" class="form-control rnumber" name="rnumber" value="{{ app('request')->input('rnumber') }}">
											</fieldset>
										</div>
										<div class="col-3 col-sm-3 col-lg-3">
											<label for="users-list-verified">HandOver Accounts</label>
											<fieldset class="form-group">
												<input type="date" class="form-control hadate" name="hadate" value="{{ app('request')->input('hadate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Vendor</label>
											<?php 
											$vendor_location = app('request')->input('vendor_id');
											$buyer = \App\Buyer::where('is_deleted', '0'); 
											if(!empty($vendor_location)){
												$buyer->where('id', $vendor_location);
											}
											$buyer = $buyer->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 vendor_id" name="vendor_id">
													<option value="">Select Any</option>
													@if(count($buyer) > 0)
													@foreach($buyer as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('vendor_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php
											$branch_location = app('request')->input('branch_location');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('branch_location', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id" onchange="locationBatch(this.value);">
													<option value="">Select Any</option>
													@foreach($branches as $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-6  pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.vendor-invoice') }}" class="btn btn-warning">Reset</a>
											
											<a href="javascript:void(0)" id="download_report" class="btn btn-primary">Payment Report</a>
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
								<th>MRL</th>	
								<th>PO</th>	
								<th>Vendor</th>	
								<th>Date Of Invoice</th>	
								<th>Invoice No</th>	
								<th>HandOver Accounts</th>	
								<th>Amount</th>	
								<th>Remark</th>	
								<th>Payment Type</th>	
								<th>CFO</th>	
								<th>UTR No.</th>
								<th>GRN No.</th>
							</tr>
						</thead>
						<tbody>			
							<?php 
								if(count($vendor) > 0){
								$i = 1;
								foreach($vendor as $key => $value){
									if(!empty($value->po_location)){
										$po = 'UTKPO-'.$value->po_location.'-'.$value->po_no.'-'.$value->po_month;
									}else{
										$po = 'UTKPO-'.$value->po_no;
									}
									
									if($po=='UTKPO-0'){
										$poText = "Below 5000";
									}else if($po=='UTKPO-'){
										$poText = 'Manual Invoive';
									}else{
										$poText = $po;
									}
									
									if(!empty($value->unique_no)){
										$mrlNo = 'REQ-'.$value->unique_no;
									}else{
										$mrlNo = '-';
									}
									
									
							?>
							<tr class="">
								<td>{{ $pageNumber++ }}</td>
								<td>
									{{ $mrlNo }} 
									<?php if(!empty($value->company) && $value->company!='-' && $value->company!='na'){ ?>
									<hr style="margin:0.5rem 0;">
									<a href="{{ route('admin.request.poprint', $value->request_id) }}" target='_blank' class='text-primary'>PO</a>
									<?php } ?>
								</td>
								<td>{{ $poText }}</td>
								<td>{{ isset($value->bname) ?  $value->bname : '-' }}</td>
								<td>{{ date('d-m-Y', strtotime($value->date_of_invoice)) }}</td>
								<td>{{ $value->invoice_no }}</td>
								<td>{{ date('d-m-Y', strtotime($value->handover_accounts)) }}</td>
								<td>{{ $value->amount }}</td>
								<td>
									<?php if(!empty($value->attachment)){ ?>
									<a href="{{ asset('laravel/public/quotation/'.$value->attachment) }}" download >Preview</a>
									<?php }else{ echo '-'; } ?>
									<hr style="margin:0.5rem 0;">
									{{ $value->	remark }}
								</td>
								<td>
									<?php  if($value->type==1){ echo 'Credit'; }else if($value->type==2){ echo 'Cash'; } ?>
								</td>
								<td>
									<?php 
										if(Auth::user()->id==8799 || Auth::user()->id==6859){
											if($value->cfo_status==1){ 
									?>
										<i class="fa fa-check text-success" aria-hidden="true"></i>
									<?php }else if($value->cfo_status==2){ ?>
										<i class="fa fa-close text-danger" aria-hidden="true"></i>
										<hr>
										<?=$value->cfo_reason;?>									
									<?php }else{ ?>
										<a href="javascript:void(0)"  data-id="{{ $value->id }}" class="get_edit_cfo text-primary">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
									<?php } }else{ echo '-';} ?>
								</td>
								<td>
									<?php if($value->status==0 && $value->cfo_status==1){ ?>
									<a href="javascript:void(0)"  data-id="{{ $value->id }}" class="get_edit_data text-primary">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php }else{ echo $value->utr_no; } ?>
									
								</td>
								<td>
									<?php 
										if($value->emp_grn!=0){ 
											$name = $value->branch;
											$words = explode(" ", $name);
											$firstLetters = "";

											foreach ($words as $word) {
												$firstLetters .= substr($word, 0, 1);
											}
											
											
											
											$grn_date = $value->mrl_created_at;
											
											$emp_grm = $value->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($grn_date)).'/'.$value->emp_grn; 
										} else{
											$emp_grm = '-';
										}
										echo $emp_grm;
									?>
								</td>
							</tr>
							<?php $i++; } 
								}else{ 
							?>
							<tr>
								<td class="text-center" colspan="12">No Record Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $vendor->appends($params)->links() !!}
					</div>
				</div>
				                  
			</section>
		</div>
	</div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.accounts-invoice-update') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Invoice Details Update</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="invoice_id" value="" class="invoice_id"/> 
					<div>
						<label>Select Payment Status</label>
						<select class="form-control" name="status" onChange="amountStatus(this.value)">
							<option value="">-- Select --</option>
							<option value="1">Pending</option>
							<option value="2">Completed</option>
						</select>
					</div>
					<div class="pt-2 utr_no" style="display:none;">
						<label>UTR Number</label>
						<input type="text" class="form-control utrFiled" name="utr_no" value=""/> 
					</div>
					
					<div class="pt-2"><button type="submit" class="btn btn-success">Submit</button></div>
				</div>
			</div>
		</div>
	</form>
</div>


<div class="modal fade" id="invoicePop" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.invoice-details') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Invoice Details Update</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="asset_noti_id" value="" class="asset_noti_id"/> 
					<div>
						<label>Invoice Date</label>
						<input type="date" class="form-control" name="invoice_date" placeholder="Invoice Date"/> 
					</div>
					<div class="pt-2">
						<label>Invoice Number</label>
						<input type="text" class="form-control" name="invoice_number" value=""/> 
					</div>
					<div class="pt-2">
						<label>HandOver To Accounts</label>
						<input type="date" class="form-control" name="handover_accounts" value=""/> 
					</div>
					<div class="pt-2">
						<label>Invoice Attachment</label>
						<input type="file" class="form-control" name="attachment" value=""/> 
					</div>
					
					<div class="pt-2">
						<label>Remark</label>
						<textarea name="remark" class="form-control"></textarea>
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


<!-- CFO -->
<div class="modal fade" id="cfoModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.request.status') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">CFO Update</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="vendor_id" value="" class="vendor_id"/> 
					<div>
						<label>Select Status</label>
						<select class="form-control" name="status" onChange="cfoStatus(this.value)">
							<option value="">-- Select --</option>
							<option value="1">Approved</option>
							<option value="2">Rejected</option>
						</select>
					</div>
					<div class="pt-2 cfo_reason" style="display:none;">
						<label>Reason</label>
						<textarea name="cfo_reason" class="form-control cfReason"></textarea>
					</div>
					
					<div class="pt-2"><button type="submit" class="btn btn-success">Submit</button></div>
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
	function amountStatus(value){
		if(value==2){
			$('.utr_no').show();
			$('.utrFiled').prop('required',true);
		}else{
			$('.utr_no').hide();
			$('.utrFiled').prop('required',false);
		}
	}
	
	function cfoStatus(value){
		if(value==2){
			$('.cfo_reason').show();
			$('.cfReason').prop('required',true);
		}else{
			$('.cfo_reason').hide();
			$('.cfReason').prop('required',false);
		}
	}
	
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
		
		$('.invoice_id').val(request_id);	
		/*	
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
			*/
	}); 
	
	$(".get_edit_cfo").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#cfoModel').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
		$('.vendor_id').val(request_id);	
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
		data.fdate 		= $('.fdate').val(),
		data.tdate 		= $('.tdate').val(),
		data.po_status  = $('.po_status').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/po-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	
	$("body").on("click", "#download_report", function (e) {
		var data = {};					
		data.rnumber 		= $('.rnumber').val(),
		data.hadate 		= $('.hadate').val(),
		data.vendor_id  	= $('.vendor_id').val(),
		data.branch_id  	= $('.branch_id').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/po-payment-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});	
</script>
@endsection