@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-7">
						<h2 class="content-header-title float-left mb-0">Store / Purchase Dashboard</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-5 text-right">
						 <button type="button" class="btn btn-outline-info waves-effect waves-light" data-toggle="modal" data-target="#mrlstatuscheck">MRL Status</button>
						<?php 					
							if( Auth::user()->id ==6193){
						?>
						 <button type="button" class="btn btn-outline-info waves-effect waves-light" data-toggle="modal" data-target="#rollback">Roll Back</button>
						<?php } ?>
					</div>
				</div>
			</div>
			
		</div>	
		<div class="row">
			<div class="col-md-6 col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between pb-0 mb-2">
						<h4 class="card-title">Today's Report</h4>
					</div>
					<div class="card-content">
						<div class="card-body pt-0">
							<div class="chart-info d-flex justify-content-between">
								<div class="text-center">
									<p class="mb-50">MRL Generated</p>
									<a href="#"><span class="font-large-1">{{ $totalMrl }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">WRL Generated</p>
									<a href="#"><span class="font-large-1">{{ $totalWrl }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">PO Generated</p>
									<a href="#"><span class="font-large-1">{{ $totalPO }}</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between pb-0 mb-2">
						<h4 class="card-title">Total Pending MRL For Approval</h4>
					</div>
					<div class="card-content">
						<div class="card-body pt-0">
							<div class="chart-info d-flex justify-content-between">
								<div class="text-center">
									<p class="mb-50">Store</p>
									<a href="#"><span class="font-large-1">{{ $store_pending }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">Purchase</p>
									<a href="#"><span class="font-large-1">{{ $purchase_pending }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">PO Approval</p>
									<a href="#"><span class="font-large-1">{{ $po_approval_pending }}</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between pb-0 mb-2">
						<h4 class="card-title">Total Purchase / Work Order</h4>
					</div>
					<div class="card-content">
						<div class="card-body pt-0">
							<div class="chart-info d-flex justify-content-between">
								<div class="text-center">
									<p class="mb-50">Purchase Order</p>
									<a href="#"><span class="font-large-1">{{ $total_report[0]->pocount }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">Work Order</p>
									<a href="#"><span class="font-large-1">{{ $total_report[0]->wocount }}</span></a>
								</div>
								<div class="text-center">
									<p class="mb-50">Rejected</p>
									<a href="#"><span class="font-large-1">{{ $total_report[0]->total_rejected }}</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--
			<div class="col-md-6 col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between pb-0 mb-2">
						<h4 class="card-title"> Purchase Amount</h4>
					</div>
					<div class="card-content">
						<div class="card-body pt-0">
							<div class="chart-info d-flex justify-content-between">
								<div class="text-center">
									<p class="mb-50">Total Purchase Amount</p>
									<a href="#"><span class="font-large-1">{{ $total_report[0]->total_amount }}</span></a>
								</div>								
							</div>
						</div>
					</div>
				</div>
			</div>
			-->
		</div>
	</div>
</div>




<div class="modal fade bd-example-modal-lg" id="rollback" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="{{ route('admin.mrl-rollback') }}" method="post"/>
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">MRL RollBack</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<input type="text" name="mrl_no" class="form-control mrl_no" placeholder="MRL Number"/>
					</div>
					<div class="pt-1">
						<select class="form-control fill-name" name="title" required>
							<option value="">-- Select Title --</option>
							
						</select>
					</div>
					<div class="pt-1">
						<select class="form-control" name="type" required onchange="gettype(this.value)">
							<option value="">-- Select Type --</option>
							<option value="Inventory">Inventory</option>
							<option value="Purchase">Purchase</option>
							<option value="CFO">CFO</option>
						</select>
					</div>
					<div class="pt-1">
						<select class="form-control it_status" name="it_status" style="display:none;">
							<option value="">-- Select Status --</option>
							<option value="0">Pending</option>
							<option value="1">In Stock</option>
							<option value="2">Proceed To Purchase</option>		
							<option value="3">Rejected</option>		
						</select>
						<select class="form-control purchase_status" name="purchase_status" style="display:none;">
							<option value="">-- Select Status --</option>
							<option value="0">In Progress</option>
							<option value="1">On Hold</option>
							<option value="2">Deliver</option>
							<option value="3">PO Generated</option>
							<option value="4">Below 5000 - Deliver</option>
							<option value="5">Cancel</option>
							<option value="6">Rejected</option>
							<option value="7">Proceed To Maintenance</option>
						</select>
						<select class="form-control cfo_status" name="cfo_status" style="display:none;">
							<option value="">-- Select Status --</option>
							<option value="0">Pending</option>
						</select>
					</div>
				</div>
				<div class="modal-body">
					<button type="submit" class="form-control btn btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div> 
</div>



<div class="modal fade bd-example-modal-lg" id="mrlstatuscheck" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post"/>
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">MRL Status Check</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<input type="text" name="" class="form-control mrl_no2" placeholder="MRL Number"/>
					</div>
					<div class="pt-1">
						<select class="form-control fill-name" name="" required>
							<option value="">-- Select Title --</option>
							
						</select>
					</div>
				</div>
			</div>
		</form>
	</div> 
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	function gettype(val){
		$('.it_status').hide();
		$('.purchase_status').hide();
		$('.cfo_status').hide();
		
		if(val=="Inventory"){
			$('.it_status').show();
		}else if(val=="Purchase"){
			$('.purchase_status').show();
		}else if(val=="CFO"){
			$('.cfo_status').show();
		}
	}
	
	$(".mrl_no").blur(function(){
		mrl_no = $('.mrl_no').val();
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-mrl-details') }}',
			data : {'_token' : '{{ csrf_token() }}', 'mrl_no': mrl_no},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});	
	});
	
	$(".mrl_no2").blur(function(){
		mrl_no = $('.mrl_no2').val();
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-mrl-details') }}',
			data : {'_token' : '{{ csrf_token() }}', 'mrl_no': mrl_no},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});	
	});
	
</script>
@endsection