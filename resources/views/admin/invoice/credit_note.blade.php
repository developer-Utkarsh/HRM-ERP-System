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
						<h2 class="content-header-title float-left mb-0">Credit Note Reports</h2>
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
		
		<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.store-credit-message') }}" method="post">
								@csrf
									<h3>Send Message FROM(ID) to TO(ID)</h3>
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From(ID)</label>
											<input type="text" class="form-control" name="fid" value="{{ !empty($start_message_id->id) ? $start_message_id->id : '' }}" required>
											
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To(ID)</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="tid" value="{{ !empty($start_message_id->id) ? $start_message_id->id : '' }}" required>
											</fieldset>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<button type="submit" class="btn btn-primary mt-2">Send</button>
										</div>
									</div>
								</form>
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
								<form action="{{ route('admin.invoice.credit-note') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control" value="{{ app('request')->input('fdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control" value="{{ app('request')->input('tdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<button type="submit" class="btn btn-primary mt-2">Search</button>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<a href="{{ route('admin.invoice.credit-note') }}" class="btn btn-warning mt-2">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<form method="POST" action="{{ route('admin.credit-multi-download') }}">
						@csrf
						<div class="col-12 col-sm-6 col-lg-3">
							<button type="submit" class="btn btn-primary mt-1">PDF</button>
						</div>
					</div>				
					<div class="table-responsive">
						<table class="table data-list-view">
							<thead>
								<tr>
									<th><input type="checkbox" name="check_all" id="check-all"></th>
									<th>ID</th>
									<th>Name</th>
									<th>Invoice No</th>
									<th>Order No</th>
									<th>Contact</th>
									<th>Date</th>
									<th>Amount</th>
									<th>Is Message</th>
									<th>Is Download</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>							
								@foreach($invoice_report as  $key => $value)
								<tr>
									<td><input type="checkbox" class="checkbox" name="id[]" value="{{ $value->id }}"></td>
									<td><?php if(!empty($value->id)){
											echo $value->id;
										}
										?></td>
									<td>
										<?php if(!empty($value->name)){
											echo $value->name;
										}
										?>
									</td>
									<td>
										<?php if(!empty($value->invoice_no)){
											echo $value->invoice_no;
										}
										?>
									</td>
									
									<td>
										<?php if(!empty($value->order_number)){
											echo $value->order_number;
										}
										?>
									</td>
									
									<td>
										<?php if(!empty($value->contact)){
											echo $value->contact;
										}
										?>
									</td>
									
									<td>
										<?php if(!empty($value->date)){
											echo $value->date;
										}
										?>
									</td>
									
									<td>
										<?php if(!empty($value->amount)){
											echo $value->amount;
										}
										?>
									</td>
									
									<td>
										<?php 
										if($value->is_send_mail == 0){
											$txt = 'Not Send';
											$clr = 'danger';						
										}else{
											$txt = 'Send';
											$clr = 'success';
										}
										?>
										<button type="button" class="btn btn-sm btn-<?= $clr ?> waves-effect waves-light" style="cursor: default;">
										<?= $txt ?>
									    </button>
									</td>
									
									<td>
										<?php  
										if($value->is_download == 0){
											$d_txt = 'Not Download';
											$d_clr = 'danger';						
										}else{
											$d_txt = 'Download';
											$d_clr = 'success';
										}
										?>
										<button type="button" class="btn btn-sm btn-<?= $d_clr ?> waves-effect waves-light" style="cursor: default;">
										<?= $d_txt ?>
									    </button>
									</td>
									
									<td class="product-action">
										<a href="javascript:void(0)" id="download_pdf"><span class="action-edit"><i class="feather icon-file-text"></i></span></a>
										<input type="hidden" name="invoice_id" class="invoice_id" value="<?php if(!empty($value->id)){ echo $value->id;}?>">
									</td>
								</tr>
								@endforeach			
							</tbody>
						</table>
					</div>
				</form>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$('#check-all').on('click',function(){
			if(this.checked){
				$('.checkbox').each(function(){
					this.checked = true;
				});
			}else{
				$('.checkbox').each(function(){
					this.checked = false;
				});
			}
		});
	});
	
	$("body").on("click", "#download_pdf", function (e) {

		var data = {};

			var invoice_id = $(this).siblings('.invoice_id').val(); 
			// data.invoice_id = $('.invoice_id').val(),

		window.location.href = "<?php echo URL::to('/admin/'); ?>/credit-report-pdf/" + invoice_id;
		
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/invoice-report-pdf/" + Object.keys(data).map(function (k) {

			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])

		}).join('&'); */

	});
	
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection
