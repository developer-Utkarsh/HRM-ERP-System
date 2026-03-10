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
						<h2 class="content-header-title float-left mb-0">DPP System</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.dppsystem.index') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
												</select>
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch</label>
											<?php $batch = \App\Batch::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batch) > 0)
													@foreach($batch as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Date</label>								
											<fieldset class="form-group">			
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">	
											</fieldset>	
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.dppsystem.index') }}" class="btn btn-warning">Reset</a>
											</fieldset>
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
								<th>Batch</th>
								<th>Batch Code</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($get_dpp_records) > 0)
								@foreach($get_dpp_records as $key=>$get_dpp_records_val)
								<tr>
									<td>{{ $key+1 }}</td>
									<td class="product-category">{{!empty($get_dpp_records_val->name) ? $get_dpp_records_val->name : ''}}</td>
									<td class="product-category">{{!empty($get_dpp_records_val->batch_code) ? $get_dpp_records_val->batch_code : ''}}</td>
									<td class="product-category">{{!empty($get_dpp_records_val->cdate) ? $get_dpp_records_val->cdate : ''}}</td>
									<td class="product-action">
										<a href="javascript:void(0);" data-id="{{ $get_dpp_records_val->dpp_record_id }}" data-batch-id="{{ $get_dpp_records_val->id }}" class="get_batch_data">
											<span class="action-edit" title="upload"><i class="feather icon-upload"></i></span>
										</a>
										@if($get_dpp_records_val->filename)
										<a href="{{asset('laravel/public/dpp_record/'.$get_dpp_records_val->filename)}}" target="__blank">
											<span class="action-edit" title="View"><i class="feather icon-eye"></i></span>
										</a>
										@endif
									</td>
								</tr>
								@endforeach
							@else
							<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
							@endif
							
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="submit_batch_file_form">
				<div class="modal-header">
					<h5 class="modal-title">Upload</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class="col-md-12 col-12">
								<div class="form-label-group">
									<input type="file" class="form-control" name="filename" accept="application/pdf"/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="id" class="dpp_id" value="">
				<input type="hidden" name="b_id" class="b_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="reschedule_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});

	$(function() {
		$(".get_batch_data").on("click", function() {
			$(".b_id").val('');
			var batch_id = $(this).attr("data-batch-id");
			var bpp_sys_id = $(this).attr("data-id");
			$(".b_id").val(batch_id);
			$(".dpp_id").val(bpp_sys_id);
			$('#myModal').modal({ 
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		});     
	});

	var $form = $('#submit_batch_file_form');
		validatereschedule = $form.validate({
			ignore: [],
			rules: {
				filename : {
					required: true,
					extension: "pdf"
				},        
			},
			messages: {  
				filename:{
					required:"field is required",                  
					//extension:"select valid input file format"
				}
			},

			errorElement : "span",
			errorClass : 'text-danger'
		});

		$("#submit_batch_file_form").submit(function(e) {
			var form = document.getElementById('submit_batch_file_form');
			var dataForm = new FormData(form);
			e.preventDefault();
			if(validatereschedule.valid()){
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},      
					type: "POST",
					url : '{{ route('admin.dppsystem.store') }}',
					data : dataForm,
					processData : false, 
					contentType : false,
					dataType : 'json',
					success : function(data){
						if(data.status == false){
							swal("Error!", data.message, "error");
						} else if(data.status == true){
							$('#submit_batch_file_form').trigger("reset");						
							swal("Done!", data.message, "success").then(function(){ 
								location.reload();
							});
						}
					}
				});
			}       
		});

</script>
@endsection
