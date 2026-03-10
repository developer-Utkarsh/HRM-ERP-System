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
						<h2 class="content-header-title float-left mb-0">Faculty/SME Request Reuse</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.faculty-sme.index') }}" class="btn btn-primary">Back</a>
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
								<form action="{{ route('admin.faculty-sme.faculty-sme-reuse') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-lg-3 form-group">
											<label><b>Category</b></label>
											<select name="category" class="form-control select_category_name">
												<option value="">Select</option>												
												<?php
												if(!empty($all_courses)){
													foreach($all_courses as $val){
												?>
												<option value="{{ $val->title }}" data-id="{{ $val->id }}" {{ request('category') == $val->title ? 'selected' : '' }}>
													{{ $val->title }}
												</option>

												<?php 
													}
												}
												?>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Exam</b></label>
											<select name="exam" class="select-multiple form-control select-multiple select_exam">
												<option value="">Select</option>
												<?php 
												  if(!empty($_GET['exam'])){
													 echo '<option value="'.$_GET['exam'].'" selected>'.explode("$#",$_GET['exam'])[0].'</option>';
												  }
												?>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Subject</b></label>
											<select name="subject" class="select-multiple form-control select_subject">
												<option value="">Select</option>
												<?php 
												  if(!empty($_GET['subject'])){
													 echo '<option value="'.$_GET['subject'].'" selected>'.explode("$#",$_GET['subject'])[0].'</option>';
												  }
												?>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Chapter</b></label>
											<select name="chapter" class="select-multiple form-control select_chapter">
												<option value="">Select</option>
												<?php 
												  if(!empty($_GET['chapter'])){
													 echo '<option value="'.$_GET['chapter'].'" selected>'.explode("$#",$_GET['chapter'])[0].'</option>';
												  }
												?>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Mode</b></label>
											<select name="mode" class="form-control">
												<option value="">Select</option>
												<option value="Manual" @if('Manual' == app('request')->input('mode')) selected="selected" @endif>Manual</option>
												<option value="PrashnKosh" @if('PrashnKosh' == app('request')->input('mode')) selected="selected" @endif>PrashnKosh</option>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Level</b></label>
											<select name="level[]" class="select-multiple form-control" multiple>
												<option value="">Select</option>
												<option value="Easy" @if(is_array(app('request')->input('level')) && in_array('Easy', app('request')->input('level'))) selected @endif>Easy</option>
												<option value="Medium" @if(is_array(app('request')->input('level')) && in_array('Medium', app('request')->input('level'))) selected @endif>Medium</option>
												<option value="Hard" @if(is_array(app('request')->input('level')) && in_array('Hard', app('request')->input('level'))) selected @endif>Hard</option>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Requirement For</b></label>
											<select name="requirement_for" class="form-control">
												<option value="">Select</option>
												<option value="YouTube" @if('YouTube' == app('request')->input('requirement_for')) selected @endif>YouTube</option>
												<option value="Offline Batch" @if('Offline Batch' == app('request')->input('requirement_for')) selected @endif>Offline Batch</option>
												<option value="Online Batch" @if('Online Batch' == app('request')->input('requirement_for')) selected @endif>Online Batch</option>
											</select>
										</div>
										<div class="col-lg-3 form-group">
											<label><b>Language</b></label>
											<select name="language" class="form-control">
												<option value="">Select</option>
												<option value="English" @if('English' == app('request')->input('language')) selected @endif>English</option>
												<option value="Hindi" @if('Hindi' == app('request')->input('language')) selected @endif>Hindi</option>
												<option value="Bilingual" @if('Bilingual' == app('request')->input('language')) selected @endif>Bilingual</option>
											</select>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group" style="float:right;">		
											<input type="hidden" name="search" value="search"/>
											<input type="hidden" name="request_id" value="{{ $request_id }}"/>
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.faculty-sme.faculty-sme-reuse') }}?request_id={{ $request_id }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Request ID</th>
								<th>Category</th>
								<th>Exam</th>
								<th>Subject</th>
								<th>Chapter</th>
								<th>Language</th>
								<th>Mode</th>
								<th>Level</th>
								<th>Request For</th>
								<th>No. of Question</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){
								$i =1;
								foreach($record as $re){
									$exam = $re->exam;
							?>
							<tr>
								<td>{{ $i++ }}</td>
								<td>{{ $re->request_id }}</td>
								<td>{{ $re->category }}</td>
								<td>
									<?php $exam = json_decode($re->exam);										
										foreach($exam as $ex){ echo $ex->name.',';	}
									?>
								</td>
								<td>
									<?php $subject = json_decode($re->subject);										
										foreach($subject as $su){ echo $su->name.',';	}
									?>
								</td>
								<td>
									<?php $topic = json_decode($re->topic);										
										foreach($topic as $to){ echo $to->name.',';	}
									?>
								</td>
								<td>{{ $re->language }}</td>
								<td>{{ $re->mode }}</td>
								<td>{{ $re->level }}</td>
								<td>{{ $re->requirement_for }}</td>
								<td>{{ $re->no_question }}</td>
								<td>
									<?php if(!empty($re->file)){ ?>
										<a href="{{ asset('laravel/public/faculty_sme/'.$re->file) }}"  target="_blank" class="btn btn-success btn-sm">View</a>
									<?php } ?>
									
									<a href="javascript:void(0)"  data-id="{{ $re->id }}" class="d-none get_edit_data text-dark btn btn-sm btn-info">Pick</a>
									
									
									<a href="{{ route('admin.faculty-sme-uploadfile') }}?request_id={{ $re->request_id }}&reuse=1&resue_by={{ $request_id }}"  target="_blank" class="text-dark btn btn-sm btn-info">Pick</a>
								</td>
							</tr>
							<?php } }else{ ?>
							<tr>
								<td colspan="12" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>					
				</div>                   
			</section>
		</div>
	</div>
</div>
 
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Upload File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-4 form-group">
						<label><b>Category</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Exam</b></label>
						<select name="" class="form-control select-multiple">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Subject</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Chapter</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>No Of Question</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Mode</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">Manual</option>
							<option value="">PrashnKosh</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Level</b></label>
						<select name="" class="form-control">
							<option value="">Select </option>
							<option value="">Easy</option>
							<option value="">Medium</option>
							<option value="">Hard</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Requirement For</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">YouTube</option>
							<option value="">Offline Batch</option>
							<option value="">Online Batch</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Language</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">English</option>
							<option value="">Hindi</option>
							<option value="">Bilingual</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Browse File</b></label>
						<input type="file" name="" value="" class="form-control"/>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
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

.select2.select2-container{
	width:100% !important;
}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple').select2({
		placeholder: "Select",
		allowClear: true
	});
});
	
	
	$(document).on("change",".select_category_name",function(){
		var category_name = $(this).find(":selected").attr("data-id");
		var _this = $(this);
		if(category_name){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.faculty-sme.get_cat_exam') }}',
				data : {'category_name': category_name},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_exam").html(data.html);
					}
				}
			});   
		}
	})
	
	
	$(document).on("change",".select_exam",function(){
		var exam_id = $(this).find(":selected").attr("data-id");
		var _this = $(this);
		if(exam_id){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.faculty-sme.get_exam_subject') }}',
				data : {'exam_id': exam_id},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_subject").html(data.html);
					}
				}
			});   
		}
	})
	
	$(document).on("change",".select_subject",function(){
		var subject_id = $(this).find(":selected").attr("data-id");
		var _this = $(this);
		if(subject_id){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.faculty-sme.get_subject_chapter') }}',
				data : {'subject_id': subject_id},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_chapter").html(data.html);
					}
				}
			});    
		}
	})
	
	
	$(".get_edit_data").on("click", function() {  
		var upload_id = $(this).attr("data-id"); 
		var request_id = <?=$request_id??0;?>;
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.faculty-sme.reuse-request') }}',
			data : {'_token' : '{{ csrf_token() }}', 'upload_id': upload_id,'request_id':request_id},
			dataType : 'json',
			success : function (data){
				if(data.status == true){
					alert(data.message);
					
					setTimeout(function() { window.location=window.location;},3000);
				}else{
					alert(data.message);
				}
				
				
			}
		});		
	}); 
	
</script>
@endsection
