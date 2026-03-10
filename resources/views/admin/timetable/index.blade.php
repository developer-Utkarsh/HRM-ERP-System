@extends('layouts.admin')
<style type="text/css"> 
	
.hide{
	display: none;
}

.show{
	display: block;
}

.select2-selection.select2-selection--multiple	{
	min-width: 200px !important;
}
</style>
@section('content')

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Time Table</h2>
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
			<div class="card">
				<div class="card-content collapse show">
					<div class="card-body">
						<div class="users-list-filter">
						<div class="row">
							<div class="col-md-12">
							<form action="{{ route('admin.timetable.index') }}" method="get">
								<div class="row">
									<div class="col-md-3">
										<label for="users-list-status">Location</label>											
										<fieldset class="form-group">												
											<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
												<option value="">Select Any</option>													
												<!--option value="jodhpur">Jodhpur</option>
												<option value="jaipur">Jaipur</option-->
												<option value="jodhpur" @if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>Jodhpur</option>
												<option value="jaipur" @if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')) selected="selected" @endif>Jaipur</option>
												<option value="delhi" @if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')) selected="selected" @endif>Delhi</option>
												<option value="prayagraj" @if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>Prayagraj</option>
											</select>												
										</fieldset>
									</div>
									<div class="col-md-3">
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
											<select class="form-control select-multiple1 branch_id" name="branch_id[]" multiple>
												<option value="">Select Any</option>
												@if(count($branches) > 0)
												@foreach($branches as $key => $value)
												<option value="{{ $value->id }}" @if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))) selected="selected" @endif>{{ $value->name }}</option>
												@endforeach
												@endif
											</select>												
										</fieldset>
									</div>
									<div class="col-md-3">
										<label for="users-list-role">Date</label>
										<fieldset class="form-group">
											<input type="date" class="form-control" name="tt_date" value="{{ app('request')->input('tt_date') ? app('request')->input('tt_date') : date('Y-m-d')}}">
										</fieldset>
									</div>
									<div class="col-md-3">
										<label for="" style="">&nbsp;</label>
										<fieldset class="form-group">		
										<button type="submit" class="btn btn-outline-primary">Search</button>
										<a href="{{ route('admin.timetable.index') }}" class="btn btn-outline-warning">Reset</a>
										@if(!empty(app('request')->input('tt_date')))
										<!--a href="{{ route('admin.copy-timetable', app('request')->input('tt_date')) }}" class="btn btn-outline-info">Copy</a-->
										@endif
										</fieldset>
									</div>
								</div>
							</form>
							</div>
						</div>
						<?php if(Auth::user()->id !=1172 && Auth::user()->id !=6564){ ?>
						<div class="row">
							<div class="col-md-12">
							@if(!empty(app('request')->input('tt_date')))
							<form action="{{ route('admin.copy-timetable') }}" method="post" id="copy_form_tt">
								@csrf
								<div class="row">
									<div class="col-md-4">
										<label for="users-list-role">Date</label>
										<fieldset class="form-group">
											<input type="date" class="form-control copy_date" name="copy_date" required>
										</fieldset>
										@if($errors->has('copy_date'))
										<span class="text-danger">{{ $errors->first('copy_date') }} </span>
										@endif
									</div>
									<div class="col-md-8">
										<label for="" style="">&nbsp;</label>
										<fieldset class="form-group">		
										<input type="hidden" class="form-control" name="from_copy_date" value="{{ app('request')->input('tt_date') }}">
										<input type="hidden" class="form-control copy_location" name="copy_location">
										<button type="button" class="btn btn-outline-info" onclick="confirmCopy('jaipur')">Jaipur Copy</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmCopy('jodhpur')">Jodhpur Copy</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmCopy('delhi')">Delhi Copy</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmCopy('prayagraj')">Prayagraj Copy</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmPublish('jaipur')">Jaipur Publish</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmPublish('jodhpur')">Jodhpur Publish</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmPublish('delhi')">Delhi Publish</button> &nbsp;&nbsp;
										<button type="button" class="btn btn-outline-info" onclick="confirmPublish('prayagraj')">Prayagraj Publish</button> &nbsp;&nbsp;
										
										<button type="button" class="btn btn-outline-info" onclick="confirmUnPublish('jaipur')" style="display:none;">Jaipur Un-Publish</button> &nbsp;&nbsp;
										
										<button type="button" class="btn btn-outline-info" onclick="confirmUnPublish('jodhpur')" style="display:none;">Jodhpur Un-Publish</button> &nbsp;&nbsp;
										
										</fieldset>
									</div>
								</div>
							</form>
							@endif
							</div>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
			@php $selecteddDate = ''; @endphp
			@if(!empty($tt_date))
			@php $selecteddDate = $tt_date; @endphp
			@else
			@php $selecteddDate = date('Y-m-d'); @endphp
			@endif
			<div class="row">
				<div class="col-12">			
					<h2 class="float-right" style="position: fixed;right:20;z-index:999;top: 100px;background: yellow;">@if(!empty(app('request')->input('tt_date'))){{date('d-m-Y',strtotime(app('request')->input('tt_date')))}}@else{{date('d-m-Y')}}@endif</h2>
				</div>
			</div>
			<section id="data-list-view" class="data-list-view-header">
				<div class="row" id="table-responsive">
					<div class="col-12">
						<?php
						$branch_id_get = app('request')->input('branch_id');
						if(empty($branch_id_get)){
							$branch_id_get = array(0);
						}
						$branch = \App\Branch::where('status', '1');
						$branch->whereIn('id',$branch_id_get);
						$branch = $branch->orderByRaw("Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)")->get();
						?>
						<div id="accordion">
							@if(count($branch) > 0)
								@php $i=1; @endphp
								@foreach($branch as $branchval)
									<div class="card">
										<div class="card-header" id="heading{{$i}}" style="padding: 0.5rem 0.5rem 0;">
											<h5 class="mb-0">
											<button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapse{{$i}}" aria-expanded="true" aria-controls="collapseOne" onclick="getBranchTimetable('{{$branchval->id}}','{{$selecteddDate}}');">
											<b>{{$branchval->name}}</b>
											</button>
										  </h5> 
										 </div>
										<div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#accordion">
											<div class="card-body main_div"> 
												@if((!empty($selecteddDate) && $selecteddDate >= date('Y-m-d')) || Auth::user()->role_id == 29)
												<?php if(Auth::user()->id !=1172){ ?>
												<div class="row">
													<div class="col-md-12">
														<button class="btn btn-outline-primary btn-sm float-right plus-click" data-count = "" onClick="showDiv(this,{{$branchval->id}});"><i class="ficon feather icon-plus"></i></button>
													</div>
												</div> 
												<?php } ?>
												@endif
												<div class="timetable-form hide">
													<form action="" method="get" class="timetablesubmit" id="filtersubmit{{$i}}">
													<div class='table-responsive'>
													<table class='table'>
													<thead>
														<tr class='text-center'>
															<th>Class Type</th>
															<th>Batch</th>
															<th>Studio</th>
															<th>Start Time</th>
															<th>End Time</th>
															<th>Faculty</th>
															<th>Subject</th>
															<th>Remark</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody class="add-fields">
														
													</tbody>
													</table>
													</div>
														<div class="row mt-2">
															<div class='col-md-12'>
																<button type='submit' id='time_table_store_btn{{$i}}' data-id="{{$i}}" class='btn btn-outline-primary float-right click_demo_class'>Save <i class='fa fa-spinner fa-spin set-loader' style='display: none;'></i></button>
															</div>
														</div>
													</form>
												</div>

												<div class='edit-timetable-form branch_head{{$branchval->id}}'></div>

											</div>
										</div>
									</div>
								@php $i++; @endphp	
								@endforeach
							@endif
							
						</div>
					</div>
				</div>              
			</section>
		</div>
	</div>
</div>
 
<!--div class="row copy-submit">
	<div class='col-md-12'>
		<input type='hidden' class='online_class_type$u_id' name='online_class_type[]' value='online'>
		<input type='hidden' class='assistant_id$u_id' name='assistant_id[]' value=''>
		<button type='submit' id='time_table_store_btn$u_id' class='btn btn-outline-primary float-right'  onClick='storeTimetable($u_id)'>Save <i class='fa fa-spinner fa-spin set-loader' style='display: none;'></i></button>
	</div>
</div--> 

@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<!--link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script-->

<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>

function getBranchTimetable(branch_id,selecteddDate){
    if($('.branch_head'+branch_id).is(':empty')){
    	$('.branch_head'+branch_id).html("<div class='text-center' style='color:red;'>Please wait..loading branch timetable <br> <i class='fa fa-spinner fa-spin set-loader'></i></div>");
	    $.ajax({
			type : 'POST',
			url : '{{ route('admin.branch-wise-timetable') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id,'selecteddDate':selecteddDate},
			dataType : 'html',
			success : function (data){
				$('.branch_head'+branch_id).html(data);
			}
		});
	}else{
	  //alert('Classes Alreaded Loaded');
    	//$('.edit-timetable-form').html("<div class='text-center' style='color:red;'>Please wait..loading branch timetable <br> <i class='fa fa-spinner fa-spin set-loader'></i></div>");

    }
}

function locationBranch(value){
	$.ajax({
		type : 'POST',
		url : '{{ route('admin.employee.get-branch') }}',
		data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
		dataType : 'html',
		success : function (data){
			$('.branch_id').empty();
			$('.branch_id').append(data);
		}
	});
}
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2,.select-multiple3').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

$(document).ready(function(){
	selectTimepicker();
});

function selectTimepicker(){
	/* $('.timepicker').timepicker({
		timeFormat: 'hh:mm p',
		interval: 5,
		dropdown: true,
		scrollbar: true
	}); */
	
	$('.timepicker').timepicker({ 'step': 5, 'timeFormat': 'h:i A' });
}

function confirmCopy(location){
	var copy_date_val = $('.copy_date').val();
	if(copy_date_val == ''){
		alert('Please select date');
	}else{
		$('.copy_location').val(location);
		if(confirm('Are you sure to want copy timetable!')){
			$('#copy_form_tt').submit();
		}
	}
}

function confirmPublish(location){
	var copy_date_val = $('.copy_date').val();
	if(copy_date_val == ''){
		alert('Please select date');
	}else{
		if(confirm('Are you sure to want publish '+location+' Timetable !')){
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.publish-timetable') }}',
				data : {'_token' : '{{ csrf_token() }}', 'copy_date': copy_date_val, 'location': location},
				dataType : 'json',
				success : function (data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						swal("Success!", data.message, "success");
					}
				}
			});
		}
	}
}

function confirmUnPublish(location){
	var copy_date_val = $('.copy_date').val();
	if(copy_date_val == ''){
		alert('Please select date');
	}else{
		if(confirm('Are you sure to want un-publish '+location+' Timetable !')){
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.unpublish-timetable') }}',
				data : {'_token' : '{{ csrf_token() }}', 'copy_date': copy_date_val, 'location': location},
				dataType : 'json',
				success : function (data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						swal("Success!", data.message, "success");
					}
				}
			});
		}
	}
}
 
function editTimetable(e){
	$(e).parents('.add_row').find('fieldset').removeClass('hide');
	$(e).parents('.add_row').find('.edit_span').addClass('hide');
	selectRefresh2();
	selectTimepicker();
	
	var class_type_t = $(e).parents('.add_row').find('.online_class_type').val();
	if(class_type_t=='Test'){
		$(e).parents('.add_row').find(".test_faculty_name").css('display','block');
		$(e).parents('.add_row').find('.faculty_id').next(".select2-container").hide();
	}
}

function selectRefresh() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select Any",
		allowClear: true
	});
	
}

function selectRefresh2(){
	$('.edittimetable .select-multiple11').select2({
		width: '100%',
		placeholder: "Select Any",
		allowClear: true
	});
}

$('.edittimetable .select-multiple11').select2({
	width: '100%',
	placeholder: "Select Any",
	allowClear: true
});



(function (original) {
  jQuery.fn.clone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
    for (var i = 0, l = my_selects.length;   i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;
				
    return result;
  };
}) (jQuery.fn.clone); 

function showDiv(e, branch_id){
	var thisVal = $(e); 
	var date_val = '<?php echo $selecteddDate; ?>';
	if($(e).attr('data-count') != ''){
		var index_count = parseInt($(e).attr('data-count'))+parseInt(1);
	}
	else{
		var index_count = 0;
	}
	$(e).attr('data-count',index_count);
	
	if (branch_id) {
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-studio-by-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'index_count': index_count, 'date_val': date_val},
			dataType : 'json',
			success : function (data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){
					
					$(thisVal).parents('.main_div').find('.timetable-form').removeClass("hide");
					$(thisVal).parents('.main_div').children('.timetable-form').find('.add-fields').append(data.data);
					selectRefresh();
					selectTimepicker();
				}
			}
		});
	}
}

function removeDiv(e){
	$(e).parents('.add_row').remove();
}

function getStudioName(e){
	var assistant_id = $('option:selected',e).attr("data-asst-id");  
	if(assistant_id != ''){
		$(e).parents('.add_row').find('.assistant_id').val(assistant_id);
	} 
}

function getCourse(e) { 
	var batch_id = $(e).val(); 
	if (batch_id) {
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-course') }}',
			data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
			dataType : 'html',
			success : function (data){
				$(e).parents('.add_row').find('.course_id').empty();
				$(e).parents('.add_row').find('.course_id').append(data);
				
			}
		});
	}
}
					
function getSubject(e) { 
	var batch_id = $(e).val();
	if (batch_id) {
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-class-batch-subject') }}',
			data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
			dataType : 'html',
			success : function (data){ 
				$(e).parents('.add_row').find('.subject_id').empty();
				$(e).parents('.add_row').find('.subject_id').append(data);
			}
		});
	}
	else{
		$(e).parents('.add_row').find('.subject_id').empty();
	}
}

function getSubjectByFaculty(e){
  var date_val = '<?php echo $selecteddDate; ?>'; 
	var faculty_id = $(e).val();
	var batch_id = $(e).parents('.add_row').find('.batch_id').val();	
	if (faculty_id) {
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-class-batch-subject-by-faculty') }}',
			data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id, 'faculty_id': faculty_id,date_val:date_val},
			dataType : 'json',
			success : function (data){
				$(e).parents('.add_row').find('.subject_id').empty();
				if(data.status){
				  $(e).parents('.add_row').find('.subject_id').append(data.subject);
				  if(data.is_birthday){
				  	swal("Be Careful!","Selected Faculty have Bithday on "+date_val, "warning");
				  }
				}else{
					//no subjects found
				}
				
			}
		});
	}
}

function deleteTimetable(e, id){
	if (id) {
		$.ajax({
			beforeSend:function(){ return confirm("Are you sure To Want Delete This!"); },
			type : 'POST',
			url : '{{ route('admin.delete-timetable') }}',
			data : {'_token' : '{{ csrf_token() }}', 'id': id},
			dataType : 'json',
			success : function (data){  
				
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){	
					swal("Done!", data.message, "success");
					$(e).parents('.add_row').addClass('hide');
				}
			}
		});
	}
}


var $form =	"";
$(document).on('click','.click_demo_class', function(e){
	var thisVal = $(this);
	var u_id = $(this).attr('data-id');
    // e.preventDefault();
    //$('#filtersubmit'+u_id).submit();
	
	var $form = $('#filtersubmit'+u_id);
	$form.validate({
		ignore: [],
		rules: {
			'batch_id[]' : {
				required: true,                
			},
			'studio_id[]' : {
				required: true,               
			},
			'from_time[]' : {
				required: true,
			},
			'to_time[]' : {
				required: true,
			},  
			'faculty_id[]' : {
				required: true,
			},
			'subject_id[]' : {
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
		},
		submitHandler: function(form) {
				// var form = document.getElementById('filtersubmit'+u_id);
				var dataForm = new FormData(form); 
				// e.preventDefault();
					$('#time_table_store_btn'+u_id).attr('disabled', 'disabled');
					$.ajax({
						beforeSend: function(){
							$("#time_table_store_btn"+u_id+" i").show();
						},
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},      
						type: "POST",
						url : '{{ route('admin.timetable.store') }}',
						data : dataForm,
						processData : false, 
						contentType : false,
						dataType : 'json',
						success : function(data){
							if(data.status == false){
								swal("Error!", data.message, "error");
								$('#time_table_store_btn'+u_id).removeAttr('disabled');
								$("#time_table_store_btn"+u_id+" i").hide();
							} else if(data.status == true){ 
								//swal("Done!", data.message, "success");
								$('#time_table_store_btn'+u_id).removeAttr('disabled');
								$("#time_table_store_btn"+u_id+" i").hide();
								$(thisVal).parents('.main_div').find('.timetable-form').addClass('hide');
								$(thisVal).parents('.main_div').find('.add-fields').empty();
								$(thisVal).parents('.main_div').find('.edit-timetable-form').append(data.result);
								$(thisVal).parents('.main_div').find('.plus-click').attr('data-count','');
							}
						}
					});
		}
	});
});


var $forms =	"";
$(document).on('click','.click_edit_class', function(e){ 
	var u_id = $(this).attr('data-id'); 
	
	var $forms = $(this).closest("form");
	$forms.validate({
		ignore: [],
		rules: {
			'batch_id[]' : {
				required: true,                
			},
			'studio_id[]' : {
				required: true,               
			},
			'from_time[]' : {
				required: true,
			},
			'to_time[]' : {
				required: true,
			},  
			'faculty_id[]' : {
				required: true,
			},
			'subject_id[]' : {
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
		},
		submitHandler: function(forms) { 
				var dataForm = new FormData(forms); 
				// e.preventDefault();
					$($forms).attr('disabled', 'disabled');
					$.ajax({
						beforeSend: function(){
							//$("#time_table_edit_btn"+u_id+" i").show();
						},
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},      
						type: "POST",
						url : '{{ route('admin.edit-timetable') }}',
						data : dataForm,
						processData : false, 
						contentType : false,
						dataType : 'json',
						success : function(data){
							if(data.status == false){
								swal("Error!", data.message, "error");
								$($forms).removeAttr('disabled');
								//$("#time_table_edit_btn"+u_id+" i").hide();
							} else if(data.status == true){
								swal("Done!", data.message, "success");
								$($forms).removeAttr('disabled');
								//console.log(data.result.from_time);
								$($forms).find('fieldset').addClass('hide');
								$($forms).find('span').removeClass('hide');
								
								
								$($forms).find('.s_batch_id').text(data.result.batch_name);
								$($forms).find('.s_studio_id').text(data.result.studios_name);
								$($forms).find('.s_from_time').text(data.result.from_time);
								$($forms).find('.s_to_time').text(data.result.to_time);
								$($forms).find('.s_faculty').text(data.result.faculty_name);
								$($forms).find('.s_subject_id').text(data.result.subject_name);
								$($forms).find('.s_online_class_type').text(data.result.online_class_type);
								$($forms).find('.s_new_remark').text(data.result.remark);
							}
						}
					});
		}
	});
})


function fixedFaculty(e){ 
	var checkClassType = $(e).val();
	if (checkClassType=='Test') {
		$(".faculty_id").val(5838).change();
		$(e).parents('.add_row').find(".test_faculty_name").css('display','block');
		$(e).parents('.add_row').find('.faculty_id').next(".select2-container").hide();
		setTimeout(function(){
			$(e).parents('.add_row').find(".batch_id").change();
		}, 500);	
	}
	else{
		$(e).parents('.add_row').find('.faculty_id').val('').change();
		$(e).parents('.add_row').find(".test_faculty_name").css('display','none');
		$(e).parents('.add_row').find('.faculty_id').next(".select2-container").show();
	}
}
</script>
@endsection
