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
						<h2 class="content-header-title float-left mb-0">Add Student Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Student Inventory</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<?php if(Auth::user()->role_id == 28 || Auth::user()->role_id == 27 || Auth::user()->role_id == 29 || Auth::user()->user_details->degination == "BRANCH HEAD" || Auth::user()->user_details->degination == "CENTER HEAD" || Auth::user()->user_details->degination == "SR. EXECUTIVE-NOTES DISTRIBUTION"  || Auth::user()->id == 8006){ ?>
										<form class="form" action="{{ route('admin.batchinventory.save') }}" method="post" enctype="multipart/form-data">
											@csrf
											<div class="form-body">
												<div class="row">
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">Location</label>
														<fieldset class="form-group">												
															<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);">
																@php $branch_location = ['Jodhpur', 'Jaipur','Prayagraj','Indore','Delhi']; @endphp
																<option value="">Select Any</option>
																@foreach($branch_location as $key => $value)
																<option value="{{ $value }}" @if($value == app('request')->input('branch_location')) selected="selected" @endif>{{ $value }}</option>
																@endforeach
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
															<select class="form-control select-multiple branch_id" name="branch_id" onchange="locationBatch(this.value);">
																<option value="">Select Any</option>
																@foreach($branches as $value)
																<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
																@endforeach
															</select>												
														</fieldset>
														<input type="hidden" name="allbatch_ids" class="allbatch_ids"/>
													</div>
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">Batch</label>
														<?php
														$ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
														$batch = DB::table('batch')
															->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
															->leftjoin('timetables as tt','tt.batch_id','batch.id')
															->where('batch.status', '1')
															->where('tt.is_deleted', '0')
															->where('tt.is_publish', '1')
															->where('tt.is_cancel', 0)
															->where('tt.cdate','>',$ttdate)
															->where('batch.batch_code','!=',0);
															if(!empty(app('request')->input('branch_id'))){
																$batch->where('tt.branch_id',app('request')->input('branch_id'));
															}
														$batch= $batch->groupby('batch.batch_code')->get();
														?>
														<fieldset class="form-group">												
															<select class="form-control select-multiple2 batch_id" name="batch_id" onchange="getInventory(this.value)">
																<option value="">Select Any</option>
																@foreach($batch as $value)
																<option value="{{ $value->batch_code }}" @if($value->batch_code == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
																@endforeach
															</select>												
														</fieldset>
													</div>
													<div class="col-md-3 col-4">
														<div class="form-group">
															<label for="first-name-column">Type</label>
															 <select class="form-control select-multiple type" name="type" required>
																<option value="">Select Type</option>
																<option value="all">All</option>
																<option value="batch">Batch</option>
															</select>
															@if($errors->has('type'))
															<span class="text-danger">{{ $errors->first('type') }} </span>
															@endif
														</div>	
													</div>
												</div>
												<div class="row">
													
													<div class="col-md-3 col-4">
														<div class="form-group">
															<label for="first-name-column">Inventory Type</label>
															<select class="form-control inventory_type" name="inventory_type[]" required>
																<option value="">Select Type</option>
																<option value="Notes">Notes</option>
																<option value="DPP">DPP</option>
																<option value="Other">Other</option>
															</select>
															
															
														</div>	
													</div>	
													<div class="col-md-3 col-12">
														<div class="form-group">
															<label for="first-name-column">Inventory Name</label>
															<input type="text" class="form-control" list="browsers" name="name[]" id="browser" required>															
															<datalist id="browsers" class="insData">
																<option value="select">
															</datalist>
														</div>
													</div>
													
													<div class="col-md-3 col-12">
														<div class="form-group">
															<label for="first-name-column">Inventory Quantity</label>
															<input type="number" class="form-control" placeholder="Inventory Quantity" name="quantity[]" value="" required min="0">
															@if($errors->has('quantity'))
															<span class="text-danger">{{ $errors->first('quantity') }} </span>
															@endif
														</div>
													</div>
													
													<div class="col-md-3 col-12 rAddmore">
														<div class="form-group text-left pt-2">
															<label for="">&nbsp;</label>
															<button class="btn btn-primary add-more btn-sm" type="button" style="margin-top:10px;">Add More</button>
														</div>
													</div>	
												</div>
												<span class="append_div w-100">
														
												</span>							
												<div class="mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
												
											</div>
										</form>
										
										<div class="copy-fields w-100" style="display:none;">													
											<div class="remove_row row">
												<div class="col-md-3 col-4">
													<div class="form-group">
														<label for="first-name-column">Inventory Type</label>
														<select class="form-control inventory_type" name="inventory_type[]" required>
															<option value="">Select Type</option>
															<option value="Notes">Notes</option>
															<option value="DPP">DPP</option>
															<option value="Other">Other</option>
														</select>
														@if($errors->has('inventory_type'))
														<span class="text-danger">{{ $errors->first('inventory_type') }} </span>
														@endif
													</div>	
												</div>	
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Inventory Name</label>
														<input type="text" class="form-control" list="browsers" name="name[]" id="browser" required>
														<datalist id="browsers" class="insData">
															
														</datalist>
													</div>
												</div>
												
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Inventory Quantity</label>
														<input type="number" class="form-control" placeholder="Inventory Quantity" name="quantity[]" value="" required min="0">
														@if($errors->has('quantity'))
														<span class="text-danger">{{ $errors->first('quantity') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3 col-12 text-left">
													<div class="form-group mb-0 pt-2">
														<label for="">&nbsp;</label>
														<button class="btn btn-danger btn-sm remove" type="button" style="margin-top:10px;">Remove</button>
													</div>
												</div>
											</div>
										</div>
									<?php }else { ?>
										<div class="form-body text-center ">
											<p class="mb-0">You are not eligible to raise any requistion.</p>
										</div>
									
									
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<div id="overlay_loader">
	<div>
		<span>Please Wait.. Request Is In Processing.</span><br>
		<i class="fa fa-refresh fa-spin fa-5x"></i>
	</div>
</div>
<style>

#overlay_loader {
    position: fixed;
	display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    cursor: pointer;
}
#overlay_loader div {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 40px;
    text-align: center;
    color: white;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    width: 100%;
}

</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script type="text/javascript">
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
		
		$('.select-multiple1').select2({
			placeholder: "Select Batch",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Type",
			allowClear: true
		});
		$(document).on("change",'.type',function(){
			var this_v = $(this).val();
			if(this_v=='batch'){
				$(".batch_div").css('display','block');
				$(".batch_code").attr('required',true);
			}
			else{
				$(".batch_div").css('display','none');
				$(".batch_code").attr('required',false);
			}
		});
		
	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.employee.get-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
				$("#overlay_loader").css('display','none');
			}
		});
	}
	
	function locationBatch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-batch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'json',
			success : function (data){
				if(data.status){
					$('.batch_id').empty();
					$('.batch_id').append(data.batches);
					$('.allbatch_ids').val(data.allbatch_ids);
					$("#overlay_loader").css('display','none');
				}
			}
		});
	}
	
	function getInventory(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.batchinventory.get-batch-inventory') }}',
			data : {'_token' : '{{ csrf_token() }}', 'batch_code': value},
			dataType : 'html',
			success : function (data){
				$('.insData').empty();
				$('.insData').append(data);
				$("#overlay_loader").css('display','none');
				
			}
		});
	}
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
		   var html = $(".copy-fields").html();			  
			$(".append_div").append(html);    
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
	});
</script>
@endsection
