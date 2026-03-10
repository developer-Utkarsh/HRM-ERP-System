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
						<h2 class="content-header-title float-left mb-0">Employee Document</h2>
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
								<form action="{{ route('admin.document.index') }}" method="get" name="employeesubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-status">Employee</label>
											<fieldset class="form-group">
												<?php //$employee = \App\User::where([['role_id', '!=', 1],['status', '=', 1]])->orderBy('id', 'desc')->get(); ?>
												<select class="form-control get_employee select-multiple" name="emp_id">
													<option value=""> - Select Employee - </option>
													@if(count($employee) > 0)
													@foreach($employee as $value)
													<option value="{{ $value->id }}" @if(app('request')->input('emp_id') == $value->id) selected="selected" @endif>{{ $value->name ." (".$value->register_id.")"}}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
									</div>
								</form>
							</div>
							@if(!empty(Request::get('emp_id')))
							<hr>
							<div class="row mt-2">
								<div class="col-12 col-sm-4 col-lg-2">
									<span>Offer Letter : </span>
									<a href="javascript:void(0)" id="download_letter" data-letter="offer"><i class="feather icon-download"></i></a>
								</div>
								<div class="col-12 col-sm-4 col-lg-2">
									<span>Relieving Letter : </span>
									<a href="javascript:void(0)" id="download_letter" data-letter="relieving"><i class="feather icon-download"></i></a>
								</div>
								<input type="hidden" name="employee_id" class="employee_id" value="@if(!empty($get_employee->id)){{ $get_employee->id }}@endif">
							</div>
							
							<hr>
							<form method="post" id="document_submit">
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>10th</b></label>
									</div>
									@if(!empty($get_emp_doc->tenth_marksheet))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->tenth_marksheet) }}" class="btn btn-outline-success ml-1 download-btn1" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn1">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile1" @if(!empty($get_emp_doc->tenth_marksheet)) {{'style=display:none'}} @endif>
										<input type="file" name="tenth_marksheet" class="form-control tenth_marksheet">
									</div>
									
									<div class="col-12 col-md-5 text-success doc-text1"></div>
									
								</div>	
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>12th</b></label>
									</div>
									@if(!empty($get_emp_doc->twelth_marksheet))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->twelth_marksheet) }}" class="btn btn-outline-success ml-1 download-btn2" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn2">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile2" @if(!empty($get_emp_doc->twelth_marksheet)) {{'style=display:none'}} @endif>
										<input type="file" name="twelth_marksheet" class="form-control twelth_marksheet">
									</div>
									<div class="col-12 col-md-5 text-success doc-text2"></div>
									
								</div>	
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>Graduate</b></label>
									</div>
									@if(!empty($get_emp_doc->graduate))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->graduate) }}" class="btn btn-outline-success ml-1 download-btn3" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn3">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile3" @if(!empty($get_emp_doc->graduate)) {{'style=display:none'}} @endif>
										<input type="file" name="graduate" class="form-control graduate">
									</div>
									<div class="col-12 col-md-5 text-success doc-text3"></div>
								</div>							
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>Post Graduate</b></label>
									</div>
									@if(!empty($get_emp_doc->postgraduate))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->postgraduate) }}" class="btn btn-outline-success ml-1 download-btn4" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn4">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile4" @if(!empty($get_emp_doc->postgraduate)) {{'style=display:none'}} @endif>
										<input type="file" name="postgraduate" class="form-control postgraduate">
									</div>
									<div class="col-12 col-md-5 text-success doc-text4"></div>
								</div>
							
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>Aadhar Card</b></label>
									</div>
									@if(!empty($get_emp_doc->aadhar_card))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->aadhar_card) }}" class="btn btn-outline-success ml-1 download-btn5" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn5">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile5" @if(!empty($get_emp_doc->aadhar_card)) {{'style=display:none'}} @endif>
										<input type="file" name="aadhar_card" class="form-control aadhar_card">
									</div>
									<div class="col-12 col-md-5 text-success doc-text5"></div>
								</div>	
								
								<div class="row mt-2">
									<div class="col-12 col-md-2">
										<label for="users-list-status"><b>Experience Letter</b></label>
									</div>
									@if(!empty($get_emp_doc->experience_letter))
										<a href="{{ asset('laravel/public/'.'/document/'.$get_emp_doc->experience_letter) }}" class="btn btn-outline-success ml-1 download-btn6" download>Download</a>
										<a href="javascript:void(0)" class="btn btn-outline-primary ml-1 edit-btn6">Edit</a>
									@endif
									<div class="col-12 col-md-5 showfile6" @if(!empty($get_emp_doc->experience_letter)) {{'style=display:none'}} @endif>
										<input type="file" name="experience_letter" class="form-control experience_letter">
									</div>
									<div class="col-12 col-md-5 text-success doc-text6"></div>
								</div>	
								
								<input type="hidden" name="doc_id" class="doc_id" value="@if(!empty($get_emp_doc->id)){{ $get_emp_doc->id }}@endif">
								<input type="hidden" name="employee_id" class="employee_id" value="@if(!empty($get_employee->id)){{ $get_employee->id }}@endif">
							</form>
							@endif
						</div>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$('.get_employee').on('change', function() {
		$("form[name='employeesubmit']").submit();
	});
	
	
	$("body").on("click", "#download_letter", function (e) { 
		var employee_id = $('.employee_id').val(); 
		var type        = $(this).attr("data-letter"); 
		window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-document-pdf/" + employee_id + "/" + type;
	});
	
	$("form").on("change", ".tenth_marksheet", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'tenth')
	});
	
	$("form").on("change", ".twelth_marksheet", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'twelth')
	});
	
	$("form").on("change", ".graduate", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'graduate')
	});
	
	$("form").on("change", ".postgraduate", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'postgraduate')
	});
	
	$("form").on("change", ".aadhar_card", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'aadhar')
	});
	
	$("form").on("change", ".experience_letter", function () {
		var form = document.getElementById('document_submit');
		storeDocument(form, 'experience_letter')
	})
	
	function storeDocument(form, types){
		var dataForm = new FormData(form); 
		
		$.ajax({
			beforeSend: function(){
				if(types == 'tenth'){
					$(".doc-text1").text('Please Wait..');
				}
				else if(types == 'twelth'){
					$(".doc-text2").text('Please Wait..');
				}
				else if(types == 'graduate'){
					$(".doc-text3").text('Please Wait..');
				}
				else if(types == 'postgraduate'){
					$(".doc-text4").text('Please Wait..');
				}
				else if(types == 'aadhar'){
					$(".doc-text5").text('Please Wait..');
				}
				else if(types == 'experience_letter'){
					$(".doc-text6").text('Please Wait..');
				}
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.document.store') }}',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status){
					if(types == 'tenth'){
						$(".doc-text1").text('Upload Successfully..');
					}
					else if(types == 'twelth'){
						$(".doc-text2").text('Upload Successfully..');
					}
					else if(types == 'graduate'){
						$(".doc-text3").text('Upload Successfully..');
					}
					else if(types == 'postgraduate'){
						$(".doc-text4").text('Upload Successfully..');
					}
					else if(types == 'aadhar'){
						$(".doc-text5").text('Upload Successfully..');
					}
					else if(types == 'experience_letter'){
						$(".doc-text6").text('Upload Successfully..');
					}
				}
				else{
					if(types == 'tenth'){
						$(".doc-text1").text('Something is wrong..');
					}
					else if(types == 'twelth'){
						$(".doc-text2").text('Something is wrong..');
					}
					else if(types == 'graduate'){
						$(".doc-text3").text('Something is wrong..');
					}
					else if(types == 'postgraduate'){
						$(".doc-text4").text('Something is wrong..');
					}
					else if(types == 'aadhar'){
						$(".doc-text5").text('Something is wrong..');
					}
					else if(types == 'experience_letter'){
						$(".doc-text6").text('Upload Successfully..');
					}
				}
			}
		});
	}
	
	
	$(".edit-btn1").click(function(){ 
		// var thisVal = $(this);
		$(".showfile1").show();
		$(".download-btn1").hide();
		$(".edit-btn1").hide();
		/* thisVal.parents('#document_submit').children('div').children('.showfile').show();
		thisVal.parents('#document_submit').children('div').children('.download-btn').hide();
		thisVal.parents('#document_submit').children('div').children('.edit-btn').hide(); */
	});
	
	$(".edit-btn2").click(function(){ 
		$(".showfile2").show();
		$(".download-btn2").hide();
		$(".edit-btn2").hide();
	});
	
	$(".edit-btn3").click(function(){ 
		$(".showfile3").show();
		$(".download-btn3").hide();
		$(".edit-btn3").hide();
	});
	
	$(".edit-btn4").click(function(){ 
		$(".showfile4").show();
		$(".download-btn4").hide();
		$(".edit-btn4").hide();
	});
	
	$(".edit-btn5").click(function(){ 
		$(".showfile5").show();
		$(".download-btn5").hide();
		$(".edit-btn5").hide();
	});
	
	$(".edit-btn6").click(function(){ 
		$(".showfile6").show();
		$(".download-btn6").hide();
		$(".edit-btn6").hide();
	});
</script>
 
@endsection
