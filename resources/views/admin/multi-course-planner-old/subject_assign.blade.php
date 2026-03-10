@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-8 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Subject Assign</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
							</ol>
						</div>
					</div>  
				</div> 
			</div>
			<div class="content-header-right text-md-right col-md-4 col-12 d-md-block">
				<?php if(Auth::user()->role_id==21 || Auth::user()->department_type==50 || Auth::user()->role_id==27){ ?>
				<a href="{{ route('admin.multi-course-planner.multi-planner-summary',[$id]) }}"><button class="btn btn-primary" type="button">View Planner</button></a>
				<?php } ?>
				
				
				<a href="{{ route('admin.multi-course-planner.planner-request-view') }}"><button class="btn btn-primary" type="button">Back</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										<form action="{{ route('admin.multi-course-planner.save-subject-assign') }}" method="post" name="filtersubmit">
											@csrf
											<input type="hidden" name="course_id" readonly value="<?=$record->course_id;?>"/>
											<input type="hidden" name="req_id" readonly value="<?=$record->id;?>"/>
											<div id="filter-wrapper">
												<div class="row filter-section">
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">Planner Type Naming</label>
														<fieldset class="form-group">												
															<input type="text" name="" readonly value="<?=$record->planner_name?>" class="form-control"/>
														</fieldset>
													</div>
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">Course Mode</label>
														<fieldset class="form-group">												
															<input type="text" name="" readonly value="<?=$record->mode?>" class="form-control"/>
														</fieldset>
													</div>
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">City Of Batch</label>
														<fieldset class="form-group">												
															<input type="text" name="" readonly value="<?=$record->city?>" class="form-control"/>
														</fieldset>
													</div>
													<div class="col-12 col-sm-6 col-lg-3">
														<label for="users-list-status">Planner Timelines</label>
														<fieldset class="form-group">												
															<input type="text" name="" readonly value="<?=date('d-m-Y',strtotime($record->timelines))?>" class="form-control"/>
														</fieldset>
													</div>
													
													
													<div class="col-12 col-sm-6 col-lg-3">
														<div class="form-group">
															<label for="first-name-column">Content Head Status</label>
															<?php $status = [1 => 'Pending', 2 => 'Approved', 3 => 'Rejected']; ?>
															<select class="form-control select-multiple2 status" name="status"
																@if(old('status', $record->status ?? '') == 3 || Auth::user()->role_id != 21) disabled @endif>
																<option value="">Select Any</option>
																@foreach($status as $key => $label)
																	<option value="{{ $key }}"
																		@if(old('status', $record->status ?? '') == $key) selected @endif>
																		{{ $label }}
																	</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-12 col-sm-6 col-lg-4 reason" style="display:none">
														<label for="users-list-status">Reason</label>
														<fieldset class="form-group">												
															<textarea name="reason" class="form-control reason_text" @if(old('status', $record->status ?? '') == 3) readonly @endif>{{ $record->reason ?? '' }}</textarea>

														</fieldset>
													</div>
												</div>
							
												<div class="assign_subject_sme" style="display:none">
													<div class="table-responsive">
														<table class="table data-list-view">
															<thead>
																<tr>
																	<th style="min-width: 180px;">Subject</th>
																	<th style="min-width: 180px;">SME</th>
																	<th style="min-width: 180px;">Remark For SME</th>
																	<th>SME Timelines</th>
																	<th style="min-width: 120px;">SME Status</th>
																	<th style="min-width: 160px;">
																		Approval Status 
																		<i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="By Content Head"></i>
																	</th>
																	<th style="min-width: 180px;">
																		TT Manager Remark 
																		<i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="For Faculty"></i>
																	</th>
																	<th>Assigned Faculty</th>
																	<th style="min-width: 180px;">Faculty Comments</th>
																</tr>
															</thead>
															<tbody>
																<?php 
																	if(count($sme_relation) > 0){
																		foreach($sme_relation as $sr){ 
																			$topic_status = DB::table('course_planner_topic_relation as cptr')
																				->join('topic_master as tm', 'tm.id', '=', 'cptr.topic_id')
																				->where('cptr.req_id', $sr->req_id)
																				->where('tm.subject_id', $sr->subject_id)
																				->select('cptr.status as subject_status')
																				->first();

																			if(empty($topic_status->subject_status)){
																				$tStatus = 'Pending';
																			}else if($topic_status->subject_status==1){
																				$tStatus = 'Submitted';
																			}else if($topic_status->subject_status==2){
																				$tStatus = 'Save As Draft';
																			}else{
																				$tStatus = 'Pending';
																			}
																?>
																<tr>
																	<input type="hidden" name="sr_id[]" value="<?=$sr->id;?>"/>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">												
																			<select name="subject_id[]" class="form-control select-multiple1 readonly-select" <?php if(Auth::user()->role_id!=21){ echo 'disabled'; } ?>>
																				<option value="">-- Select --</option>
																				<?php foreach($subject as $su){ ?>
																				<option value="{{ $su->id }}" <?php if($sr->subject_id==$su->id){ echo 'selected'; } ?>>{{ $su->name }}</option>
																				<?php } ?>
																			</select>
																			
																			<?php if(Auth::user()->role_id==27){ ?>
																			<input type="hidden" name="subject_id[]" value="<?=$sr->subject_id;?>"/>
																			<?php } ?>
																		</fieldset>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">											
																			<select name="sme_id[]" class="form-control select-multiple1"
																			<?php if(Auth::user()->role_id!=21){ echo 'disabled'; } ?>>
																				<option value="">-- Select --</option>
																				<?php foreach($sme as $sm){ ?>
																				<option value="{{ $sm->id }}" <?php if($sr->sme_id==$sm->id){ echo 'selected'; } ?>>{{ $sm->name }} - {{ $sm->register_id }}</option>
																				<?php } ?>
																			</select>
																			
																			<?php if(Auth::user()->role_id==27){ ?>
																			<input type="hidden" name="sme_id[]" value="<?=$sr->sme_id;?>"/>
																			<?php } ?>
																		</fieldset>
																	</td>	
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">												
																			<input type="text" name="remark[]" class="form-control" value="<?=$sr->sme_remark??''?>" <?php if(Auth::user()->role_id!=21){ echo 'readonly'; } ?>/>
																		</fieldset>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">												
																			<input type="date" name="edate[]" class="form-control" value="<?=$sr->date??''?>" <?php if(Auth::user()->role_id!=21){ echo 'readonly'; } ?>/>
																		</fieldset>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<div class="">
																			<h5 class="mb-2" style="font-size:12px !important;"><strong><span class="text-danger">{{ $tStatus }}</span></strong></h5>
																		</div>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<div class="">
																			<?php $sstatus = [0 => 'Pending', 1 => 'Approved']; ?>
																			<select class="form-control select-multiple2 sstatus" name="sstatus[]"
																			<?php if(Auth::user()->role_id!=21){ echo 'disabled'; } ?>
																			>
																				<option value="">Select Any</option>
																				@foreach($sstatus as $key => $label)
																					<option value="{{ $key }}"
																						@if(old('sstatus', $sr->is_subject ?? '') == $key) selected @endif>
																						{{ $label }}
																					</option>
																				@endforeach
																			</select>
																			
																			<?php if(Auth::user()->role_id==27){ ?>
																			<input type="hidden" name="sstatus[]" value="<?=$sr->is_subject;?>"/>
																			<?php } ?>
																		</div>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">												
																			<input type="text" name="tt_remark[]" class="form-control" value="<?=$sr->tt_remark??''?>" <?php if(Auth::user()->role_id!=27){ echo 'readonly'; } ?>/>
																		</fieldset>
																	</td>
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">											
																			<select name="faculty_id[]" class="form-control select-multiple1" <?php if(Auth::user()->role_id!=27){ echo 'disabled'; } ?>>
																				<option value="">-- Select --</option>
																				<?php foreach($faculty as $fa){ ?>
																				<option value="{{ $fa->id }}" <?php if($sr->faculty_id==$fa->id){ echo 'selected'; } ?>>{{ $fa->name }} - {{ $fa->register_id }}</option>
																				<?php } ?>
																			</select>
																			
																			<?php if(Auth::user()->role_id==21){ ?>
																			<input type="hidden" name="faculty_id[]" value="<?=$sr->faculty_id;?>"/>
																			<?php } ?>
																		</fieldset>
																	</td>
																	
																	<td class="col-12 col-sm-6 col-lg-3">
																		<fieldset class="">												
																			<input type="text" name="faculty_remark[]" class="form-control" value="<?=$sr->faculty_remark??'-'?>" readonly />
																		</fieldset>
																	</td>
																	
																	
																	
																	
																</tr>
																<?php } 
																	}else{ 
																?>
																<tr>
																	<td colspan="10" class="text-center">No Record Found</td>
																</tr>
																<?php } ?>
															</tbody>
														</table>
													</div>
													<hr>
													
													<?php 
														$max_date = date('Y-m-d', strtotime($record->timelines . ' -1 day'));	
														if(Auth::user()->role_id!=27 && Auth::user()->id!=8232 && Auth::user()->id!=8866){  
													?>
													
													<div class="row">
														<div class="col-12 col-sm-6 col-lg-3">
															<label for="users-list-status">Subject</label>
															<fieldset class="form-group">												
																<select name="subject_id[]" class="form-control select-multiple1">
																	<option value="">-- Select --</option>
																	<?php foreach($subject as $su){ ?>
																	<option value="{{ $su->id }}">{{ $su->name }}</option>
																	<?php } ?>
																</select>
															</fieldset>
														</div>
														<div class="col-12 col-sm-6 col-lg-3">
															<label for="users-list-status">SME</label>
															<fieldset class="form-group">											
																<select name="sme_id[]" class="form-control select-multiple1">
																	<option value="">-- Select --</option>
																	<?php foreach($sme as $sm){ ?>
																	<option value="{{ $sm->id }}">{{ $sm->name }} - {{ $sm->register_id }}</option>
																	<?php } ?>
																</select>
															</fieldset>
														</div>															
														<div class="col-12 col-sm-6 col-lg-3">
															<label for="users-list-status">SME Remark</label>
															<fieldset class="form-group">												
																<input type="text" name="remark[]" class="form-control" value=""/>
															</fieldset>
														</div>
														<div class="col-12 col-sm-6 col-lg-2">
															<label for="users-list-status">Timelines</label>
															<fieldset class="form-group">
																<input type="date" name="edate[]" class="form-control" value="" max="<?= $max_date ?>" />
															</fieldset>
														</div>
														<div class="col-12 col-sm-6 col-lg-1">
															<label for="users-list-status">&nbsp;</label>
															<fieldset class="form-group">												
																<button type="button" class="btn btn-sm btn-primary add-more p-1">Add</button>
															</fieldset>
														</div>
													</div>
													<div class="append"></div>
												</div>
												<?php } ?>
												
												<?php if($record->status!=3 && Auth::user()->id!=8232 && Auth::user()->id!=8866){ ?>
												<div class="col-12 col-sm-12 col-lg-12">
													<fieldset class="form-group">
														<button type="submit" class="btn btn-primary">Submit</button>
													</fieldset>
												</div>
												<?php } ?>
											</div>
										</form>
										
										<!-- Copy -->
										<div class="row copy-filed" style="display:none">
											<div class="col-12 col-sm-6 col-lg-3">
												<label for="users-list-status">Subject</label>
												<fieldset class="form-group">												
													<select name="subject_id[]" class="form-control select-multiple3">
														<option value="">-- Select --</option>
														<?php foreach($subject as $su){ ?>
														<option value="{{ $su->id }}">{{ $su->name }}</option>
														<?php } ?>
													</select>
												</fieldset>
											</div>
											<div class="col-12 col-sm-6 col-lg-3">
												<label for="users-list-status">SME</label>
												<fieldset class="form-group">												
													<select name="sme_id[]" class="form-control select-multiple3">
														<option value="">-- Select --</option>
														<?php foreach($sme as $sm){ ?>
														<option value="{{ $sm->id }}">{{ $sm->name }} - {{ $sm->register_id }}</option>
														<?php } ?>
													</select>
												</fieldset>
											</div>																	
											<div class="col-12 col-sm-6 col-lg-3">
												<label for="users-list-status">Remark</label>
												<fieldset class="form-group">												
													<input type="text" name="remark[]" class="form-control"/>
												</fieldset>
											</div>
											<div class="col-12 col-sm-6 col-lg-2">
												<label for="users-list-status">Date</label>
												<fieldset class="form-group">												
													<input type="date" name="edate[]" class="form-control" value="" max="<?= $max_date ?>" />
												</fieldset>
											</div>
											<div class="col-12 col-sm-6 col-lg-1">
												<label for="users-list-status">&nbsp;</label>
												<fieldset class="form-group">												
													<button type="button" class="btn btn-sm p-1 btn-danger remove">Remove</button>
												</fieldset>
											</div>
										</div>
									</div>
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
<style>
  .readonly-select {
     pointer-events: none;     /* Prevent clicks and selection */
    background-color: #e9ecef; /* Optional: gray background to look readonly */
    color: #495057; 
  }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script type="text/javascript">
	
$(document).ready(function() {
    $('.select-multiple1').select2({
        placeholder: "Select Any",
        width: '100%', // ✅ Fix: wrap 100% in quotes
        allowClear: true
    });

    $('.select-multiple2').select2({
        placeholder: "Select Any",
		width: '100%',
        allowClear: true
    });
	
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Add More Button Click
    $('.add-more').click(function () {
        var html = $('.copy-filed').html(); // Clone hidden HTML
        $('.append').append('<div class="row">' + html + '</div>'); // Append it inside a .row wrapper
		
		$('.append .select-multiple3').select2({				
			width:'100%',
			placeholder: "Select",
			allowClear: true
		});
    });

    // Remove Button Click (Event Delegation for dynamic elements)
    $(document).on('click', '.remove', function () {
        $(this).closest('.row').remove(); // Remove the parent row
    });
});
</script>

<script>
	$(document).ready(function() {
		function toggleSections(status) {
			$('.reason_text').removeAttr('required');
			if (status == 3) {
				$('.reason').show();
				$('.assign_subject_sme').hide();
				$('.reason_text').attr('required', true);
			} else if (status == 2) {
				$('.assign_subject_sme').show();
				$('.reason').hide();
			} else {
				$('.reason, .assign_subject_sme').hide();
			}
		}

		toggleSections($('.status').val());

		$('.status').on('change', function() {
			toggleSections($(this).val());
		});
	});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("timeline");
        const today = new Date();
        today.setDate(today.getDate() + 3); // add 3 days
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

</script>
<script>
 $(document).ready(function () {
  $('.readonly-select').each(function () {
    const $select = $(this);
    const originalValue = $select.val();

    $select.on('mousedown', function (e) {
      // Prevent dropdown from opening
      e.preventDefault();
    });

    $select.on('change', function () {
      $(this).val(originalValue); // Revert to original
    });
  });
});

</script>


@endsection
