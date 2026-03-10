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
						<h2 class="content-header-title float-left mb-0">Batch Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Batch Details
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
			<!-- page users view start -->
			<section class="page-users-view">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
							
							</div>
							<div class="card-body">
								<div class="row">
									 
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table>
											<tr>
												<td class="font-weight-bold">Batch Name</td>
												<td>{{ $batch->name }}</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Course Name</td>
												<td>{{ $batch->course->name }}</td>
											</tr>
											
											<tr>
												<td class="font-weight-bold">Start Date</td>
												<td>{{ date('d-m-Y',strtotime($batch->start_date)) }}</td>
											</tr>
										</table>
									</div>
									 
								</div>
							</div>
						</div>
					</div>
					<!-- information start -->
					<div class="col-md-12 col-12 ">
						<div class="card">
							<div class="table-responsive">
								<table class="table data-list-view">
									<thead>
										<tr>
											<th>S.No.</th>
											<!--th>Faculty</th-->
											<th>Subject</th>
											<!--th>Chapter</th-->
											<!--th>Topic</th-->
											<!--th>Duration</th-->
											<!--th>Schedule Date</th-->
											<!--th>Spent Hour</th-->
											<!--th>Status</th-->
											<!--th>Remark</th-->
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										/*$i = 1;
										foreach($batch->batch_relations as  $key => $value){
										
										$chapter = \App\Chapter::where('course_id', $batch->course->id)->where('subject_id', $value->subject->id)->first();
										?>
										<tr>
											<td class="product-category">{{ $i++ }}</td>
											<td class="product-category">{{ $value->user->name }}</td>
											<td class="product-category">{{ $value->subject->name }}</td>
											<td class="product-category">{{ $chapter->name }}</td>
											 
										</tr>
										<?php }*/ ?>	
										
										<?php
										$i = 1;
										foreach($send_array as  $key => $value){
										?>
										<tr>
											<td class="product-category"><?=$value['s_no']?></td>
											<!--td class="product-category"><?php //echo $value['faculty_name']?></td-->
											<td class="product-category"><?=$value['subject_name']?></td>
											<!--td class="product-category"><?php //echo $value['chapter_name']?></td-->
											<!--td class="product-category"><?php //echo $value['topic_name']?></td-->
											<!--td class="product-category"><?php //echo $value['duration']?></td-->
											<!--td class="product-category"><?php //echo $value['schedule_date']?></td-->
											<!--td class="product-category"><?php //echo $value['spent_hour']?></td>
											<!--td class="product-category"><?php //echo $value['status']?></td-->
											<!--td class="product-category"><?php //echo $value['remark']?></td-->
											<td class="product-category">
												<strong class="text-{{($value['subject_status']=='Complete')?'primary':'danger'}}">
												<?php echo $value['subject_status'];
												if($value['subject_status']=='Complete'){
													echo " (".date('d-m-Y',strtotime($value['complete_date'])).")";
												}
												?>
												</strong>
											</td>
											<td class="product-category">
											
												<?php
												if(Auth::user()->user_details->degination != "MANAGER- SALES & MARKETING"){
													if($value['subject_status']=='Complete'){
														?>
														<input type="hidden" class="batch_relation_id" value="<?=$value['batch_relation_id']?>" />
														<a href="Javascript:void(0);" class="uncomplete_click btn btn-sm btn-danger">Uncomplete</a>
														<?php
													}
													else{
														?>
															<a href="Javascript:void(0);" class="complete_click btn btn-sm btn-primary" >Complete</a>
															<div method="post" class="complete_div" style="display:none;">
																<input type="hidden" class="batch_relation_id" value="<?=$value['batch_relation_id']?>" />
																<input type="date" class="complete_date" />
																<button class="btn btn-sm btn-primary complete_submit" >Submit</button>
															</div>
														<?php
													}
												}
												?>
												
											</td>
											 
										</tr>
										<?php } ?>										
									</tbody>
								</table>
							</div>    
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					 
					
					
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/app-user.css') }}">
<script>
$(document).on("click",".complete_click", function () {
	$(this).hide();
	$(this).siblings(".complete_div").show();
});

$(".complete_submit").on("click",function(e) {
	e.preventDefault();
	if (!confirm("Do you want complete subject")){
	  return false;
	}
	var batch_relation_id = $(this).siblings('.batch_relation_id').val();
	var complete_date = $(this).siblings('.complete_date').val();
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.batch_subject_status_update') }}',
		data : {'batch_relation_id':batch_relation_id,'complete_date':complete_date,'status':'Complete'},
		success : function(data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){					
				swal("Done!", data.message, "success").then(function(){ 
					location.reload();
				});
			}
		}
	});
});

$(".uncomplete_click").on("click",function(e) {
	e.preventDefault();
	if (!confirm("Do you want uncomplete subject")){
	  return false;
	}
	var batch_relation_id = $(this).siblings('.batch_relation_id').val();
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.batch_subject_status_update') }}',
		data : {'batch_relation_id':batch_relation_id,'status':'Uncomplete'},
		success : function(data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){					
				swal("Done!", data.message, "success").then(function(){ 
					location.reload();
				});
			}
		}
	});
});
</script>
@endsection
