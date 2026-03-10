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
						<h2 class="content-header-title float-left mb-0">Batch</h2>
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
								<form action="{{ route('admin.batch.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Batch</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Batch" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Course</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="course_id">
													<?php $courses = \App\Course::where('status', '1')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													@foreach($courses as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('course_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="status">
													@php $status = ['Inactive', 'Active']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="type">
													@php $type = ['online', 'offline']; @endphp
													<option value="">Select Any</option>
													@foreach($type as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('type')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Chanakya Assigned</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="is_chanakya">
													@php $chanakya = ['Yes', 'No']; @endphp
													<option value="">Select Any</option>
													@foreach($chanakya as $key => $cvalue)
													<option value="{{ $cvalue }}" @if($cvalue == app('request')->input('is_chanakya')) selected="selected" @endif>{{ $cvalue }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.batch.index') }}" class="btn btn-warning">Reset</a>
									</fieldset>
									 
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<a class="float-right" href="{{asset('laravel/public/CourseBySubjectData.xlsx')}}"><span>Sample Import File</span></a>
					</div>				
				</div>
				
				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Batch</th>
								<th>Nickname</th>
								<th width="50px">Batch Code</th>
								<th>Course</th>
								<th>Start Date</th>
								<!--th>Type</th-->
								<th>No of hours</th>
								<th>Capacity</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($batches) > 0)
								@foreach($batches as  $key => $value)
									@php $no_of_count = 0; @endphp
									@if(count($value->batch_relations) > 0)
										@foreach($value->batch_relations as $batch_relations_val) 
											@php $no_of_count += $batch_relations_val->no_of_hours; @endphp
										@endforeach
									@endif
								<tr>
									<td>{{ $pageNumber++ }}</td>
									<td class="product-category">{{ $value->name }}</td>
									<td class="product-category">
										@if($value->nickname!="")
											{{ $value->nickname }}
										@else
											N/A
										@endif
									</td>
									<td class="product-category" style="word-wrap:break-word; display: inline-block;width:200px;">{{ $value->batch_code }}</td>
									<td class="product-category">{{ isset($value->course->name) ? $value->course->name : '' }}</td>								
									<td class="product-category">{{ date('d-m-Y', strtotime($value->start_date)) }}</td>
									<!--td class="product-category">{{ $value->type }}</td-->
									<td class="product-category">{{ $no_of_count }}</td>
									<td class="product-category">{{ $value->capacity }}</td>
									<td class="product-category">@if($value->status == 1) Active @else Inactive @endif</td>
									<!--td>{{ $value->created_at->format('d-m-Y') }}</td-->
									<td class="product-action">
									<?php if(Auth::user()->user_details->degination != "MANAGER- SALES & MARKETING"){?>
										<a href="{{ route('admin.batch.edit', $value->id) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<a href="{{ route('admin.batch.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Batch')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
									<?php } ?>
										<a href="{{ route('admin.batch.view', $value->id) }}">
											<span class="action-edit"><i class="feather icon-eye"></i></span>
										</a>
									<?php if(Auth::user()->user_details->degination != "MANAGER- SALES & MARKETING"){?>
										<a href="javascript:void(0);" data-toggle="modal" data-course-id="{{ $value->course_id }}" class="btn btn-sm btn-primary import_data"><span class="action-edit"><i class="feather icon-upload"></i></span></a>
									<?php } ?>
									</td>
								</tr>
								@endforeach
							@else
								<tr>
									<td class="text-center" colspan="11">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $batches->appends($params)->links() !!}
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>


<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-l">
		<div class="modal-content">
			<form method="post" id="submit_import_file">
				<div class="modal-header">
					<h5 class="modal-title">Import</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
						
							<div class='col-md-12 col-12'>	
								<div class='form-label-group'>	
									<input type="file" class="form-control" name="import_file">
								</div>
							</div>
							
						</div>
					</div>
				</div>
				<input type="hidden" name="course_id" class="course_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="import_btn" class="btn btn-primary dsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Course",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Status",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});
	
	$(function() {
		$(".import_data").on("click", function() {
			var course_id = $(this).attr("data-course-id");
			if(course_id){
				var studioname = $(this).attr("studioname");
				$(".course_id").val(course_id);
				$('#myModal').modal({
						backdrop: 'static',
						keyboard: true, 
						show: true
				});
			}
			else{
				alert('Course ID Not FOund.');
			}
			
		});     
	});
	
	var $form = $('#submit_import_file');
	validatorprice = $form.validate({
		ignore: [],
		rules: {
			'import_file' : {
				required: true,                
			},       
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});

	$("#submit_import_file").submit(function(e) {
		var form = document.getElementById('submit_import_file');
		var dataForm = new FormData(form); 
		e.preventDefault();
		if(validatorprice.valid()){
			$('#import_btn').attr('disabled', 'disabled');
			$.ajax({
				beforeSend: function(){
					$("#import_btn i").show();
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.import-chapter') }}',
				data : dataForm,
				processData : false, 
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
						$('#import_btn').removeAttr('disabled');
						$("#import_btn i").hide();
					} else if(data.status == true){
						$('#submit_timetable_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
						$('#import_btn').removeAttr('disabled');
						$("#import_btn i").hide();
					}
				}
			});
		}       
	});
					
</script>
@endsection
