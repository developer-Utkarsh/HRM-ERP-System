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
						<h2 class="content-header-title float-left mb-0">Online Courses</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Online Courses
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
									<form class="form" action="{{ route('admin.onlinecourses.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<h3>Add Online Course</h3>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">User</label>
														@if(count($employees) > 0)
														<select class="form-control select-multiple1" name="emp_id" required>
															<option value="">Select Employee</option>
															@foreach($employees as $key=>$value)
															<option value="{{  $value->id }}" @if(!empty(old('emp_id')) && old('emp_id') ==  $value->id){{ 'selected' }}@endif>{{ $value->name.'-'.$value->register_id.'-'.$value->mobile }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('emp_id'))
														<span class="text-danger">{{ $errors->first('emp_id') }} </span>
														@endif
													</div>
												</div> 

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Course Type</label>
														<select class="form-control select-multiple1" name="course_type" required>
															<option value="">Select Couese Type</option>
															<option value="Online Course">Online Course</option>
															<option value="Batch Course">Batch Course</option>
															
														</select>
													</div>
													@if($errors->has('course_type'))
														<span class="text-danger">{{ $errors->first('course_type') }} </span>
													@endif
												</div>   

												<div class="col-md-4 col-12 select_online d-none">
													<div class="form-group">
														<label for="first-name-column">Online Course ID</label>
														<input type="text" class="form-control" placeholder="Course ID" name="course_id" value="{{ old('course_id') }}">
														@if($errors->has('course_id'))
														<span class="text-danger">{{ $errors->first('course_id') }} </span>
														@endif
													</div>
												</div> 

												<div class="col-md-4 col-12 select_batch d-none">
													<div class="form-group">
														<label for="first-name-column">Batch Course</label>
														<?php 
														  $batchcourses = json_decode($batchcourses);
														  $batchcourses=$batchcourses->data;
														?>
														@if(count($batchcourses) > 0)
														<select class="form-control select-multiple1" name="batch_id[]" style="width:100%;" multiple>
															<option value="">Select Batch Course</option>
															@foreach($batchcourses as $key=>$value)
															<option value="{{  $value->id }}" @if(!empty(old('batch_id')) && old('batch_id') ==  $value->id){{ 'selected' }}@endif>{{ $value->title }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('batch_id'))
														<span class="text-danger">{{ $errors->first('batch_id') }} </span>
														@endif
													</div>
												</div> 
												
												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>

												<div class="table-responsive">
												<table class="table">
													<tr id="course_list"></tr>
												</table>
											    </div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						@if(count($online_course_result) > 0)
						<div class="table-responsive">
							<h3>Online Course List</h3>
							<table class="table data-list-view">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Employee Name</th>
										<th>Mobile No</th>
										<th>Course ID</th>
										<th>Status</th>
										<th>Added By</th>
										<th>Created Date</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($online_course_result as  $key => $value)
									<tr class="rw{{$key}}">
										<td><strong>{{ $key + 1 }}</strong></td>
										<td class="product-category">{{ !empty($value->emp_name) ? $value->emp_name : '' }}</td>
										<td class="product-category">
										<a href="javascript::void(0);" mobile_no="{{ $value->mobile_no }}" data-id="{{ $key }}" class="btn btn-sm btn-primary mt-1 get_course_details">{{$value->mobile_no}}</a>
										</td>
										<td class="product-category">{{ $value->course_id }}</td>
										<td class="product-category">{{ !empty($value->course_status) ? $value->course_status : '' }}</td>
										<td class="product-category">{{ !empty($value->addby_name) ? $value->addby_name : '' }}</td>
										<td>{{ date('d-m-Y', strtotime($value->created_at)) }}</td>
										<td colspan="2">
											<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light delete_course" data-course-id="{{$value->course_id}}" data-contact="{{$value->mobile_no}}" data-course-type="{{$value->course_status == 'Error' ? $value->course_status : ''}}" data-id="{{$key}}" title="Delete" style="padding: 0.5rem 0.5rem;"><i class="feather icon-trash"></i></a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div> 
						@endif
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
$('.select-multiple1').select2({
	placeholder: "Select",
	allowClear: true
});

$('input[name="course_id"]').on('change, keyup', function() {
    var currentInput = $(this).val();
    var fixedInput = currentInput.replace(/[A-Za-z!@#$%^&*()]/g, '');
    $(this).val(fixedInput);
    console.log(fixedInput);
});

$(".get_course_details").on("click", function() {
	var unique_id = $(this).attr("data-id");
	var mobile_no = $(this).attr("mobile_no");
	$.ajax({type : 'POST',
		url : '{{ route('admin.get-course-list-by-mobile') }}',
		data : {'_token' : '{{ csrf_token() }}', 'mobile_no': mobile_no, 'unique_id': unique_id},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				$(".subtr"+unique_id).remove();
				$(".rw"+unique_id).after(data.data);
			}
		}
	});
})

$("select[name='emp_id']").on("change", function() {
	var data=$("select[name='emp_id'] :selected").text();
	data = data.split('-');
    mobile_no=data['2'];
	var unique_id ="333";
	$.ajax({type : 'POST',
		url : '{{ route('admin.get-course-list-by-mobile') }}',
		data : {'_token' : '{{ csrf_token() }}', 'mobile_no': mobile_no, 'unique_id': unique_id},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				$("#course_list").html(data.data);
			}
		}
	});
})

$("select[name='course_type']").on("change", function() {
	if(this.value=='Online Course'){
		$(".select_online").removeClass('d-none');
		$(".select_batch").addClass('d-none');
	}else if(this.value=='Batch Course'){
     $(".select_online").addClass('d-none');
	 $(".select_batch").removeClass('d-none');
	}
})



$(document).on("click", ".destroy_course", function () {  
	if (!confirm("Do you want to delete")){
      return false;
    }   
	var course_id = $(this).attr('data-course-id');
	var contact = $(this).attr('data-contact');
	var u_id = $(this).attr('data-id');
	var course_type = $(this).attr('data-course-type'); 
	
	$.ajax({type : 'POST',
		url : '{{ route('admin.delete-course') }}',
		data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id, 'contact': contact, 'course_type': course_type},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				if(data.message != 'error'){
					if(course_type == "free"){
						$(".offsubstrrm"+u_id).remove();
					}
					if(course_type == "paid"){
						$(".paidsubstrrm"+u_id).remove();
					}
					if(course_type == "batch"){
						$(".batchsubstrrm"+u_id).remove();
					}
					
					swal("Success!", data.message, "success");
				}
				else{
					swal("Error!", data.message, "error");
				}
				
			}
		}
	});
	
});

$(document).on("click", ".delete_course", function () { 
	if (!confirm("Do you want to delete")){
      return false;
    }  
	var course_id = $(this).attr('data-course-id');
	var contact = $(this).attr('data-contact');
	var u_id = $(this).attr('data-id');
	var course_type = $(this).attr('data-course-type'); 
	
	$.ajax({
		type : 'POST',
		url : '{{ route('admin.delete-course') }}',
		data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id, 'contact': contact, 'course_type': course_type},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				if(data.message != 'error'){
					$(".rw"+u_id).remove();
					swal("Success!", data.message, "success");
				}
				else{
					$(".rw"+u_id).remove();
					swal("Error!", data.message, "error");
				}
				
			}
		}
	});
	
});

$(document).on("click", ".remove_all", function () { 
	var u_id = $(this).attr('data-id');
	$(".subtr"+u_id).remove();
	
});

</script>
@endsection
