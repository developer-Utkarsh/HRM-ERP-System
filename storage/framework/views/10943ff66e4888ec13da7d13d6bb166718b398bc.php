
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
<?php $__env->startSection('content'); ?>

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
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
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
							<div class="col-md-7">
							<form action="<?php echo e(route('studiomanager.timetable.index')); ?>" method="get">
								<div class="row">
									<div class="col-md-6">
										<label for="users-list-role">Date</label>
										<fieldset class="form-group">
											<input type="date" class="form-control" name="tt_date" value="<?php echo e(app('request')->input('tt_date') ? app('request')->input('tt_date') : date('Y-m-d')); ?>">
										</fieldset>
									</div>
									<div class="col-md-6">
										<label for="" style="">&nbsp;</label>
										<fieldset class="form-group">		
										<button type="submit" class="btn btn-outline-primary">Search</button>
										<a href="<?php echo e(route('studiomanager.timetable.index')); ?>" class="btn btn-outline-warning">Reset</a>
										<?php if(!empty(app('request')->input('tt_date'))): ?>
										<!--a href="<?php echo e(route('admin.copy-timetable', app('request')->input('tt_date'))); ?>" class="btn btn-outline-info">Copy</a-->
										<?php endif; ?>
										</fieldset>
									</div>
								</div>
							</form>
							</div>
							</div>
							<div class="row">
							<div class="col-md-12">
							<?php if(!empty(app('request')->input('tt_date'))): ?>
							<form action="<?php echo e(route('studiomanager.copy-timetable')); ?>" method="post" id="copy_form_tt">
								<?php echo csrf_field(); ?>
								<div class="row">
									<div class="col-md-4">
										<label for="users-list-role">Date</label>
										<fieldset class="form-group">
											<input type="date" class="form-control copy_date" name="copy_date" required>
										</fieldset>
										<?php if($errors->has('copy_date')): ?>
										<span class="text-danger"><?php echo e($errors->first('copy_date')); ?> </span>
										<?php endif; ?>
									</div>
									<div class="col-md-8">
										<label for="" style="">&nbsp;</label>
										<fieldset class="form-group">		
										<input type="hidden" class="form-control" name="from_copy_date" value="<?php echo e(app('request')->input('tt_date')); ?>">
										<input type="hidden" class="form-control copy_location" name="copy_location">
							
										<?php if(count($branch_locations) > 0 && in_array('jaipur', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('jaipur')">Jaipur Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('jodhpur', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('jodhpur')">Jodhpur Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('delhi', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('delhi')">Delhi Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('prayagraj', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('prayagraj')">Prayagraj Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('indore', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('indore')">Indore Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('jaipur', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('jaipur')">Jaipur Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('jodhpur', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('jodhpur')">Jodhpur Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('delhi', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('delhi')">Delhi Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('prayagraj', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('prayagraj')">Prayagraj Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('indore', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('indore')">Indore Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										
										
										<?php if(count($branch_locations) > 0 && in_array('barmer', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmCopy('barmer')">Barmer Copy</button> &nbsp;&nbsp;
										<?php endif; ?>
										<?php if(count($branch_locations) > 0 && in_array('barmer', $branch_locations)): ?>
											<button type="button" class="btn btn-outline-info" onclick="confirmPublish('barmer')">Barmer Publish</button> &nbsp;&nbsp;
										<?php endif; ?>
										</fieldset>
									</div>
								</div>
							</form>
							<?php endif; ?>
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php $selecteddDate = ''; ?>
			<?php if(!empty($tt_date)): ?>
			<?php $selecteddDate = $tt_date; ?>
			<?php else: ?>
			<?php $selecteddDate = date('Y-m-d'); ?>
			<?php endif; ?>
			<div class="row">
				<div class="col-12">			
					
					<h2 class="float-right" style="position: fixed;right:20;z-index:999;top: 100px;background: yellow;"><?php if(!empty(app('request')->input('tt_date'))): ?><?php echo e(date('d-m-Y',strtotime(app('request')->input('tt_date')))); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?></h2>
				</div>
			</div>
			<section id="data-list-view" class="data-list-view-header">
				<div class="row" id="table-responsive">
					<div class="col-12">
						<?php 
						$course_cat = explode(",",Auth::user()->course_category);
						$branch = \App\Branch::where('status', '1')->whereIn('branch_location', $branch_locations)->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
						?>
						<div id="accordion">
							<?php if(count($branch) > 0): ?>
								<?php $i=1; ?>
								<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="">
										<!-- Branch Show -->
										<?php 
											$logged_id 	=	Auth::user()->id;
											$logged_role_id 	=	Auth::user()->role_id;
											$bid 		=	$branchval->id;
											$location		=	$branchval->branch_location;
											$bName		=	$branchval->name;															
											$userdetail 	=	 \App\Userdetails::where('user_id', $logged_id)->first();
											$bCode 		=	$userdetail->branch_id;
											

										?>	
										<div class="card-header mt-3" id="heading<?php echo e($i); ?>" style="padding: 0.5rem 0.5rem 0;">
											<h5 class="mb-0">
											<button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapse<?php echo e($i); ?>" aria-expanded="true" aria-controls="collapseOne" onclick="getBranchTimetable('<?php echo e($branchval->id); ?>','<?php echo e($selecteddDate); ?>');">
												 <b><?=$bName;?></b> 
											</button>
										  </h5> 
										</div>
										
											
										
										<!-- Branch End -->


										<div id="collapse<?php echo e($i); ?>" class="collapse card" aria-labelledby="heading<?php echo e($i); ?>" data-parent="#accordion">
											<div class="card-body main_div"> 
												 <?php if((!empty($selecteddDate) && $selecteddDate >= date('Y-m-d')) || Auth::user()->role_id == 27): ?>
												<div class="row">
													<div class="col-md-12">
														<button class="btn btn-outline-primary btn-sm float-right plus-click" data-count = "" onClick="showDiv(this,<?php echo e($branchval->id); ?>);"><i class="ficon feather icon-plus"></i></button>
													</div>
												</div> 
												<?php endif; ?>
												<div class="timetable-form hide">
													<form action="" method="get" class="timetablesubmit" id="filtersubmit<?php echo e($i); ?>">
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
															<th>Chapter</th>
															<th>Topic</th>
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
																<button type='submit' id='time_table_store_btn<?php echo e($i); ?>' data-id="<?php echo e($i); ?>" class='btn btn-outline-primary float-right click_demo_class'>Save <i class='fa fa-spinner fa-spin set-loader' style='display: none;'></i></button>
															</div>
														</div>
													</form>
												</div>

												<div class='edit-timetable-form branch_head<?php echo e($branchval->id); ?>'></div>
												
												<!--div class='edit-timetable-form2'>
												<?php if(1==2): ?>
												<?php												
												$check_timetable = DB::table('timetables')
																	->select('timetables.*','batch.name as batch_name','batch.capacity as batch_capactiy','batch.batch_code as batch_code','studios.name as studios_name','studios.capacity as studios_capacity','users.name as faculty_name','subject.name as subject_name','cu.course_category as course_category')
																	->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
																	->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
																	->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
																	->leftJoin('users as cu', 'cu.id', '=', 'timetables.user_id')
																	->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
																	->where('studios.branch_id', $branchval->id)
																	->where('timetables.cdate', $selecteddDate)
																	->where('timetables.is_deleted','0')
																	->where('timetables.time_table_parent_id','0');
																	if(count($course_cat) > 0){
																		foreach($course_cat as $course_cat_val){
																			$check_timetable->whereRaw("FIND_IN_SET('$course_cat_val',cu.course_category)");
																		}
																	}
												$check_timetable = $check_timetable->orderBy('timetables.batch_id')->orderBy('timetables.from_time')->get();
												
												
												?>
												<?php if(count($check_timetable) > 0): ?>
													    <div class='table-responsive'>
														<table class='table'>
														
														<?php $j = 1; $multiple_batch_array = array(); $multiple_batch_str = ''; ?>
														
														<?php $__currentLoopData = $check_timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check_timetable_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php
														array_push($multiple_batch_array, $check_timetable_value->batch_id, $check_timetable_value->batch_capactiy);
														$multiple_batch_str .= $check_timetable_value->batch_name. ' - '.$check_timetable_value->batch_capactiy.', ';
														
														$get_multiple_batch = DB::table('timetables')
																				->select('timetables.*','batch.name as batch_name','batch.capacity as batch_capactiy','batch.batch_code as batch_code')
																				->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
																				->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
																				->where('studios.branch_id', $branchval->id)
																				->where('timetables.is_deleted','0')
																				->where('timetables.cdate', $selecteddDate)
																				->where('time_table_parent_id', $check_timetable_value->id)
																				->get();
														//echo '<pre>'; print_r($get_multiple_batch);die;	
														?>
														
														<?php if(count($get_multiple_batch) > 0): ?>
														<?php $__currentLoopData = $get_multiple_batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $get_multiple_batch_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php 
														array_push($multiple_batch_array, $get_multiple_batch_val->batch_id, $get_multiple_batch_val->batch_capactiy); 
														$multiple_batch_str .= $get_multiple_batch_val->batch_name.' - '.$get_multiple_batch_val->batch_capactiy.', ';
														?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														<?php endif; ?>
														
														<tr>
														<td>
														<form action='javascript:void(0)' method='get' class='edittimetable' id='editSubmitForm<?php echo e($j); ?>'>
														<table style='width: 100%;'>
														<?php if($j == 1): ?>
														<tr class='text-center'>
															<th>Class Type</th>
															<th>Batch</th>
															<th>Studio</th>
															<th>Start Time</th>
															<th>End Time</th>
															<th>Faculty</th>
															<th>Subject</th>
															<th>Remark</th>
															 <?php if((!empty($selecteddDate) && $selecteddDate >= date('Y-m-d')) || Auth::user()->role_id == 27): ?>
															<th>Action</th>
															<?php endif; ?>
														</tr> 
														<?php endif; ?>

														<tr class='text-center add_row'>
															<td style='width:15%'>
																<span class='edit_span s_online_class_type'><?php echo e($check_timetable_value->online_class_type); ?></span>
																<fieldset class='form-group hide' style='min-width: 100px;'>
																<select class='form-control select-multiple11 online_class_type' name='online_class_type[]' onChange='fixedFaculty(this);'>
																<option value=''>Select Class Type</option>
																
																<option value='Online Course Recording' <?php if($check_timetable_value->online_class_type == 'Online Course Recording'): ?><?php echo e("selected"); ?> <?php endif; ?>>Online Course Recording</option>
																
																<option value='YouTube Live' <?php if($check_timetable_value->online_class_type == 'YouTube Live'): ?><?php echo e("selected"); ?> <?php endif; ?>>YouTube Live</option>
																
																<option value='YouTube & App Live' <?php if($check_timetable_value->online_class_type == 'YouTube & App Live'): ?><?php echo e("selected"); ?> <?php endif; ?>>YouTube & App Live</option>
																
																<option value='Model Paper Recording' <?php if($check_timetable_value->online_class_type == 'Model Paper Recording'): ?><?php echo e("selected"); ?> <?php endif; ?>>Model Paper Recording</option>
																
																<option value='Offline' <?php if($check_timetable_value->online_class_type == 'Offline'): ?><?php echo e("selected"); ?> <?php endif; ?>>Offline</option>
																
																<option value='Offline & App live' <?php if($check_timetable_value->online_class_type == 'Offline & App live'): ?><?php echo e("selected"); ?> <?php endif; ?>>Offline & App live</option>
																
																<option value='App Live' <?php if($check_timetable_value->online_class_type == 'App Live'): ?><?php echo e("selected"); ?> <?php endif; ?>>App Live</option>
																
																<option value='Test' <?php if($check_timetable_value->online_class_type == 'Test'): ?><?php echo e("selected"); ?> <?php endif; ?>>Test</option>
																</select>																
																</fieldset>
															 </td>
															<td style='width:15%'>
																<span class='edit_span s_batch_id'><?php echo e(rtrim($multiple_batch_str, ", ")); ?></span>
																<fieldset class='form-group hide'>
																<?php $batch = \App\Batch::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
																	<select class='form-control select-multiple11 batch_id' name='batch_id[0][]' onChange='getSubject(this);getCourse(this)'  multiple='multiple'>
																	<option value=''> Select Batch </option>
																	<?php if(count($batch) > 0): ?>
																	<?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value='<?php echo e($value->id); ?>' <?php if(count($multiple_batch_array) > 0 && in_array($value->id, $multiple_batch_array)): ?><?php echo e('selected'); ?> <?php endif; ?>><?php echo e($value->name); ?> ( <?php echo e($value->batch_code); ?> ) - <?php echo e($value->capacity); ?></option>  
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?>
																	</select>												
																</fieldset>
															</td>
															
															<td class='course_online_faculty' style='display: none;'>
																<span class='edit_span s_course_id'><?php echo e($check_timetable_value->course_id); ?></span>
																<fieldset class='form-group hide'>
																<select class='form-control course_id' name='course_id[]'>
																	<option value='<?php echo e($check_timetable_value->course_id); ?>'> - Select Course - </option>
																</select>
																</fieldset>
															</td>
					
															<td style='width:15%'>
																<span class='edit_span s_studio_id'><?php echo e($check_timetable_value->studios_name); ?> - <?php echo e($check_timetable_value->studios_capacity); ?></span>
																<fieldset class='form-group hide'>
																<?php $studio =  \App\Studio::with(['assistant'])->where('branch_id', $branchval->id)->where('status',1)->where('is_deleted','0')->get(); ?>
																	<select class='form-control select-multiple11 studio_id' name='studio_id[]' onChange='getStudioName(this)'>
																	<option value=''> Select Studio </option>
																	<?php if(count($studio) > 0): ?>
																	<?php $__currentLoopData = $studio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<?php if(!empty($value->name) && !empty($value->name) && !empty($value->assistant)): ?>
																	<option value='<?php echo e($value->id); ?>' data-asst-id='<?php echo e($value->assistant_id); ?>'  <?php if(!empty($check_timetable_value->studio_id) && $check_timetable_value->studio_id == $value->id): ?><?php echo e('selected'); ?> <?php endif; ?>><?php echo e($value->name); ?> - <?php echo e($value->capacity); ?></option>
																	<?php endif; ?>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?>
																	</select>												
																</fieldset>
															</td>
															
															<td style='width:10%'>
																<span class='edit_span s_from_time'><?php echo e(date("h:i A", strtotime($check_timetable_value->from_time))); ?></span>
																<fieldset class='form-group hide'>
																	<input type='text' name='from_time[]' class='form-control from_time timepicker' placeholder='Time' value='<?php echo e($check_timetable_value->from_time); ?>' autocomplete='off' style='width:120px'>
																</fieldset>
															 </td>
															 
															 <td style='width:10%'>
																<span class='edit_span s_to_time'><?php echo e(date("h:i A", strtotime($check_timetable_value->to_time))); ?></span>
																<fieldset class='form-group hide'>
																	<input type='text' name='to_time[]' class='form-control to_time timepicker' placeholder='Time' value='<?php echo e($check_timetable_value->to_time); ?>' autocomplete='off' style='width:120px'>
																</fieldset>
															 </td>
															 
															 <td style='width:15%'>
																<span class='edit_span s_faculty'><?php echo e($check_timetable_value->faculty_name); ?></span>
																<fieldset class='form-group hide'>
																	<?php $faculty = \App\User::where('status', '1')->where('role_id', 2)->where('is_deleted','0')->orderBy('id','desc')->get(); ?>
																	<select class='form-control select-multiple11 faculty' name='faculty_id[]' onChange='getSubjectByFaculty(this)'>
																	<option value=''> Select Faculty </option>
																	<?php if(count($faculty) > 0): ?>
																	<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value='<?php echo e($value->id); ?>'   <?php if(!empty($check_timetable_value->faculty_id) && $check_timetable_value->faculty_id == $value->id): ?><?php echo e('selected'); ?> <?php endif; ?>><?php echo e($value->name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?> 
																	</select>												
																</fieldset>
															</td>
															
															<td style='width:15%'>
																<span class='edit_span s_subject_id'><?php echo e($check_timetable_value->subject_name); ?></span>
																<fieldset class='form-group hide'>
																<?php 
																	$subjects = DB::table('batchrelations')
																				->select('subject.id', 'subject.name')
																				->join('subject', 'subject.id', '=', 'batchrelations.subject_id') 
																				->where('batchrelations.batch_id', $check_timetable_value->batch_id)
																				->groupBy('batchrelations.subject_id')
																				->get();
																?>
																	<select class='form-control select-multiple11 subject_id' name='subject_id[]'>
																		<?php if(count($subjects) > 0): ?>
																			<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subjects_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																				<option value='<?php echo e($subjects_val->id); ?>' <?php if(!empty($check_timetable_value->subject_id) && $check_timetable_value->subject_id == $subjects_val->id): ?><?php echo e('selected'); ?><?php endif; ?>><?php echo e($subjects_val->name); ?></option>
																			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
																		<?php endif; ?>
																	</select>												
																</fieldset>
															 </td>
															 
															 <td style='width:10%'>
																<span class='edit_span s_new_remark'><?php echo e($check_timetable_value->remark); ?></span>
																<fieldset class='form-group hide'>
																	<input type='text' name='new_remark[]' class='form-control new_remark' placeholder='Remark' value='<?php echo e($check_timetable_value->remark); ?>' autocomplete='off' style="width:120px">
																</fieldset>
															 </td>
															 
															<?php if((!empty($selecteddDate) && $selecteddDate >= date('Y-m-d')) || Auth::user()->role_id == 27): ?>
															<td style='width:5%'>
																<span class='edit_span'>
																	<a href='javascript:void(0)' class='float-right pl-1' onclick='deleteTimetable(this, <?php echo e($check_timetable_value->id); ?>)'>
																		<span class='btn btn-danger btn-sm action-delete delete_id'><i class='feather icon-trash'></i></span>
																	</a>
																	<a href='javascript:void(0)' class='float-right' data-id="<?php echo e($j); ?>" onclick="editTimetable(this)">
																		<span class='btn btn-success btn-sm action-edit edit_id'><i class='feather icon-edit'></i></span>
																	</a>
																	
																</span>
																<fieldset class='form-group hide'>
																	<button type='submit' id='time_table_edit_btn<?php echo e($j); ?>' data-id="<?php echo e($j); ?>" class='btn btn-outline-primary btn-sm float-right click_edit_class'>
																		<i class='feather icon-check'></i>
																		<i class='fa fa-spinner fa-spin set-loader' style='display: none;'></i>
																	</button>
																</fieldset>
																<input type='hidden' name='id[]' class='id' value='<?php echo e($check_timetable_value->id); ?>'>
															</td>
															<?php endif; ?>
															</tr>	
															</table>
															<div class='row mt-2' style='display:none'>
															<input type='hidden' class='class_type' name='class_type[]' value='online'>
															<input type='hidden' class='assistant_id' name='assistant_id[]' value='<?php echo e($check_timetable_value->assistant_id); ?>'>
															<input type='hidden' class='cdate' name='cdate[]' value='<?php echo e($selecteddDate); ?>'>
															<input type='hidden' class='branch_id' name='branch_id[]' value='<?php echo e($branchval->id); ?>'>
														</div>
														</form>	
														</td></tr>

														<?php $j++; $multiple_batch_array = array(); $multiple_batch_str = ''; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</table>			
														</div>
												
												<?php endif; ?>
												<?php endif; ?>
												</div-->

											</div>
										</div>
									</div>
								<?php $i++; ?>	
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
							
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<!--link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script-->

<link href="<?php echo e(asset('laravel/public/css/jquery.timepicker.css')); ?>" rel="stylesheet"/>
<script src="<?php echo e(asset('laravel/public/js/jquery.timepicker.js')); ?>"></script>

<script>
function getBranchTimetable(branch_id,selecteddDate){
    if($('.branch_head'+branch_id).is(':empty')){
    	$('.branch_head'+branch_id).html("<div class='text-center' style='color:red;'>Please wait..loading branch timetable <br> <i class='fa fa-spinner fa-spin set-loader'></i></div>");
	    $.ajax({
			type : 'POST',
			url : '<?php echo e(route('studiomanager.branch-wise-timetable')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id,'selecteddDate':selecteddDate},
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
				url : '<?php echo e(route('studiomanager.publish-timetable')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'copy_date': copy_date_val, 'location': location},
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
			url : '<?php echo e(route('studiomanager.get-studio-by-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'index_count': index_count, 'date_val': date_val},
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
			url : '<?php echo e(route('studiomanager.get-course')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id},
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
			url : '<?php echo e(route('studiomanager.get-class-batch-subject')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id},
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
			url : '<?php echo e(route('studiomanager.get-class-batch-subject-by-faculty')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id, 'faculty_id': faculty_id,date_val:date_val},
			dataType : 'json',
			success : function (data){
				$(e).parents('.add_row').find('.subject_id').empty();
				if(data.status){
				  $(e).parents('.add_row').find('.subject_id').append(data.subject);
				  if(data.is_birthday){
				  	swal("Alert","Selected Faculty have Bithday on "+date_val, "warning");
				  }
				}else{
					//no subjects found
				}
				
			}
		});
	}
}

function getChapter(e){ 
	var subject_id = $(e).val();
	var course_id = $(e).parents('.add_row').find('.course_id').val();
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('studiomanager.get-chapter')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id, 'subject_id': subject_id,},
		dataType : 'html',
		success : function (data){
			$(e).parents('.add_row').find('.chapter_id').empty();
			$(e).parents('.add_row').find('.chapter_id').append(data);
		}
	});
}

function getTopic(e){ 
	var chapter_id = $(e).val();
	var subject_id = $(e).parents('.add_row').find('.subject_id').val();
	var batch_id = $(e).parents('.add_row').find('.batch_id').val();
	var course_id = $(e).parents('.add_row').find('.course_id').val();
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('studiomanager.get-topic')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id,'batch_id':batch_id,'subject_id': subject_id,'chapter_id':chapter_id},
		dataType : 'html',
		success : function (data){
			$(e).parents('.add_row').find('.topic_id').empty();
			$(e).parents('.add_row').find('.topic_id').append(data);
		}
	});
}

function deleteTimetable(e, id){
	if (id) {
		$.ajax({
			beforeSend:function(){ return confirm("Are you sure To Want Delete This!"); },
			type : 'POST',
			url : '<?php echo e(route('studiomanager.delete-timetable')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id},
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
						url : '<?php echo e(route('studiomanager.timetable.store')); ?>',
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
						url : '<?php echo e(route('studiomanager.edit-timetable')); ?>',
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
								$($forms).find('.s_chapter_id').text(data.result.chapter_name);
								$($forms).find('.s_topic_id').text(data.result.topic_name);
								$($forms).find('.s_online_class_type').text(data.result.online_class_type);
								$($forms).find('.s_new_remark').text(data.result.remark);
							}
						}
					});
		}
	});
})

function fixedFaculty(e, branch_id){ 
	var checkClassType = $(e).val();
	
		var is_obs = 'No';
		if(checkClassType =='YouTube & App Live'){
			var is_obs = 'Yes';
		}
		
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}, 
			type : 'POST',
			url : '<?php echo e(route('studiomanager.get-obs-studio')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>','branch_id':branch_id,'is_obs':is_obs},
			dataType : 'html',
			success : function (data){
				$('.studio_id').empty();
				$('.studio_id').append(data);
			}
		});
		if (checkClassType=='Test') {
			$(e).parents('.add_row').find('.faculty_id').val(5838).change();
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/timetable/index.blade.php ENDPATH**/ ?>