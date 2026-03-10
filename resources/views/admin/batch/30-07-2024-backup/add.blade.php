@extends('layouts.admin')
<style type="text/css">
	.hide {
		display: none!important;
	}
</style>
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Add Batch</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Batch
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.batch.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.batch.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12" id="course_loader">
													<div class="form-group">
														<label>Courses</label>
														<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get(); ?>
														@if(count($courses) > 0)
														<select class="form-control course_id select-multiple1" name="course_id" required>
															<option value=""> - Select Course - </option>
															@foreach($courses as $value)
															<option value="{{ $value->id }}">{{ $value->name }}</option>
															@endforeach
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														@endif
														@if($errors->has('course_id'))
														<span class="text-danger">{{ $errors->first('course_id') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label>Batch Nick Name</label>
														<input type="text" name="nickname" class="form-control" value="{{ old('nickname') }}" placeholder="Nickname">
													</div>
												</div>

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label>Start Date</label>
														<input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" placeholder="Start Date" min="<?=date("Y-m-d")?>" required>
													</div>
												</div>

												<div class="col-md-12 col-12" style="display:none;">
													<div class="form-group">
														<label>Venue</label>
														<input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="Venue / Location">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<label>Batch Name</label>
														<select class="form-group select-batche" name="name" required style="width:100%;">
															<option>Select Batch</option>
															<?php
																$url="https://utkarshpublications.com/soft/apis/offlineapp-liveapis/registered-student.php";
																$curl = curl_init($url);
																curl_setopt($curl, CURLOPT_URL, $url);
																curl_setopt($curl, CURLOPT_POST, true);
																curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
																$headers = array(
																   "Content-Type: application/x-www-form-urlencoded",
																);
																curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
																$data="query=running-batches";
																curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
																$resp = curl_exec($curl);
																curl_close($curl);

																$batches=json_decode($resp,true);
																if(!empty($batches)){
																	foreach($batches['data'] as $key => $value) {
																		echo "<option value='".$value['batch_name']."' data='".$value['Bat_id']."'>".$value['batch_name']."</option>";
																	}
																}
																?>
														</select>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>Batch Code</label>
														<input type="text" name="batch_code" class="form-control" value="{{ old('batch_code') }}" placeholder="Batch Code" required>
													</div>
												</div>
												
												<div class="col-md-2 col-12 capacity_div">
													<div class="form-group">
														<label>Capacity</label>
														<input type="text" name="capacity" class="form-control" value="{{ old('capacity') }}" placeholder="Capacity" required>
													</div>
												</div>
												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>Total Test</label>
														<input type="number" name="total_test" min="0" class="form-control" value="{{ old('total_test') }}" placeholder="Total Test">
													</div>
												</div>

												<div class="col-md-2 col-12">
													<div class="form-group">
														<label>ERP COURSE ID</label>
														<input type="text" name="erp_course_id" class="form-control" value="{{ old('erp_course_id') }}" placeholder="Course Id" required>
														@if($errors->has('erp_course_id'))
														<span class="text-danger">{{ $errors->first('erp_course_id') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-6 col-12" style="display:none;">

													<div class="form-group d-flex align-items-center">

														<label class="mr-2">Type :</label>

														<label>

															<input type="radio" name="type" class="change_class" value="online" >

															Online

														</label>

														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

														<label>

															<input type="radio" name="type" class="change_class" value="offline" checked>

															Offline

														</label>

													</div>
												</div>	
											</div>
											<div class="row">
												<div class="col-md-4 col-12" id="">
													<div class="form-group">
														<label>Batch Mentor</label>
														@if(count($faculty) > 0)
														<select class="form-control select-multiple2" name="mentor_id">
															<option value=""> - Select Mentor - </option>
															@foreach($faculty as $value)
															<option value="{{ $value->id }}">{{ $value->name }}</option>
															@endforeach
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														@endif
														@if($errors->has('mentor_id'))
														<span class="text-danger">{{ $errors->first('mentor_id') }} </span>
														@endif
													</div>
												</div>
											</div>
											

											<div class="copy_subject">
												<div class="row">
													<div class='col-md-4 mb-2'>
														<div class='input-group'>
															<select class='form-control subjects' name='course[subject_id][]' required>
																<option value=''>Select Subject</option>
															</select>
														</div>
													</div>
													<div class='col-md-4 col-12 mb-2'>
														<div class='input-group'>
															<input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' step=0.01 required>
														</div>
													</div>
													<div class='col-md-2 col-12 mb-2 text-danger p-1 removeSubject'>X</div>
												</div>
											</div>

											<div class="course_subjects">
												 
											</div>

											<div class="row text-right float-right"><span class="btn btn-primary subject_addMore" data-row="1">Add More</span></div>
											<div class="clearfix"></div>

											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" checked>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0">
															Inactive
														</label>
													</div>													
												</div>
												<div class="col-10">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
												<div class="col-md-2 count_div hide">
													<h3>Total Hours: <span id="count_hour">0</span></h3>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).on('keyup', '.cal_hour', function(){
		var cnt = 0; 
		$('.count_div').removeClass('hide');
		$(".cal_hour").each(function() {  
			if($(this).val() != ''){
				cnt += parseFloat($(this).val());
			}
		});
		$('#count_hour').text(cnt);
	});
	
	
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Course",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "- Select Faculty -",
			allowClear: true
		});
		$('.select-batche').select2({
			placeholder: "Select Course",
			allowClear: true
		});

		$(".select-batche").on("change",function(){
			var batch_id=$(".select-batche :selected").attr("data");
			$("input[name='batch_code']").val(batch_id);
		});
		
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".after-add-more").after(html); 
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".control-group").remove();
		});
	}); 
</script>
<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				//url : '{{ route('admin.get-batch-subject-data') }}',
				url : '{{ route('admin.get-batch-subject') }}',
				data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id},
				dataType : 'html',
				success : function (data){
					$("#course_loader i").hide();
					/*$('.course_subjects').empty();
					$('.course_subjects').append(data);
					$('.select-multiple2').select2({
						placeholder: "- Select Faculty -",
						allowClear: true
					});*/
                    
                    $('.subjects').empty();
                    $(".course_subjects").empty();		

					$(".subjects").append(data);
					$('.select-multiple2').select2({
						placeholder: "- Select Faculty -",
						allowClear: true
					});
				}
			});
		}
	});


	$(".subject_addMore").on("click",function(){
       $(".course_subjects").append($(".copy_subject").html());
       var rowNo=$(".subject_addMore").data("row");
	   $(".subject_addMore").data("row",rowNo+1);		

	});

	$(document).on("click",".removeSubject",function(){
		var rowNo=$(".subject_addMore").data("row");
		if(rowNo!=1){
			$(".subject_addMore").data("row",rowNo-1);
            $(this).parent(".row").remove();
        }
	});
    
    var add_subjects="";
	$(document).on("change",".subjects",function(){
        subjects=add_subjects.split('$$'); 
		if(jQuery.inArray($(this).val(),subjects)==-1){
		  add_subjects=add_subjects+"$$"+$(this).val();
	    }else{
	    	alert('Subject Already Selected');
	    	$(this).val('');
	    }
	});

	$(".change_class").on("change", function () {
		if($(this).val()=='offline'){
			$(".capacity_div").css('display','block');
		}
		else{			
			$(".capacity_div").css('display','none');
		}
	});
</script>
@endsection
