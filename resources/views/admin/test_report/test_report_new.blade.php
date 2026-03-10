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
						<h2 class="content-header-title float-left mb-0">Batch Test</h2>
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
								<form action="{{ route('admin.batch-test-report-new') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);" required>
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
												<select class="form-control select-multiple1 branch_id" name="branch_id" onchange="locationBatch(this.value);">
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
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batchs) > 0)
													@foreach($batchs as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control fdate" value="{{ app('request')->input('fdate') }}">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control tdate" value="{{ app('request')->input('tdate') }}">
											</fieldset>
										</div>
										<!--
										<div class="col-md-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control month" name="month" value="@if(!empty(Request::get('month'))){{ Request::get('month') }}@endif">
											</fieldset>
										</div>
										-->
										<div class="col-12 col-md-6 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.batch-test-report-new') }}" class="btn btn-warning">Reset</a>
											<button type="button" onClick="ExportToExcel()" class="btn btn-primary">Export</button>
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table" id="my-table-id">	
						<thead>					
							<tr>
								<th>S. No.</th>
								<th>Location</th>
								<th>Branch</th>
								<th>Batch Name</th>
								<th>Total Test</th>
								<!--<th>Pending</th>-->
							</tr>
						</thead>
						<tbody>	
							<?php 
								if(count($getbatch) > 0){
									$i = 1;
									foreach($getbatch as $key => $value){
							?>
							<tr>
								<td><?=$i;?></td>
								<td><?=ucfirst($value->location);?></td>
								<td><?=ucfirst($value->bname);?></td>
								<td><a href="{{ route('admin.batch-test-report') }}?batch_id=<?=$value->id;?>" title="Click Here"><?=$value->name;?></a></td>
								<td><?=$value->total_qty;?></td>
								<!--<td>-</td>-->
							</tr>
							<?php $i++; } }else{ ?>
							<tr>
								<td class="text-center" colspan="6">No Record Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>					 
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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	function ExportToExcel(){
		var htmltable= document.getElementById('my-table-id');
		// var newhtml = htmltable.replace('a', 'span');
		
		var html 	 = htmltable.outerHTML;
		
		var newHtml  = html.replaceAll('<a', '<span');
		var newHtml2  = newHtml.replaceAll('</a', '</span');
	
		// alert(newHtml2);
		
       window.open('data:application/vnd.ms-excel,' + encodeURIComponent(newHtml2));
    }
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-studio') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.studio_id').empty();
					$('.studio_id').append(data);
				}
			});
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $(".assistant_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
	
	$(".branch_location").on("change", function () {
		var b_location = $(this).val();
		if (b_location) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-location-wise-branch') }}',
				data : {'_token' : '{{ csrf_token() }}', 'b_location': b_location},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.branch_id').empty();
					$('.branch_id').append(data);
				}
			});
			
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
</script>
@endsection
