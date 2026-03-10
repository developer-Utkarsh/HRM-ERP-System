
<style type="text/css">
	.set_time{
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(-90deg);
		display: inline-block;
		font-size: 9px;
		margin: -4.7px;
		margin-top: 2px;
	}
	.ui-timepicker-container.ui-timepicker-standard.ui-timepicker-no-scrollbar {
		z-index: 9999999 !important;
	}
	.table thead th {
		border-bottom: 0px solid #f8f8f8 !important;
	}
	.table th, .table td {
		padding: 8px 6px !important;
	}
	.btn-sm, .btn-group-sm > .btn {
		padding: 8px 8px !important;
	}
	.faculty-set{
		/*width: 7%;*/
		/*float: left;*/
		border: 1px solid #ececec;
		padding: 8px 8px;

	}
	.faculty-set p{
		font-size: 12px;
		font-weight: bold;
		text-align: center;
		margin-bottom: 2px;
	}

	.faculty-set span{
		font-size: 8px;
		font-weight: bold;
		width: 100%;
		text-align: center;
		display: inline-block;
	}

	.from-time{
		border-left: 2px solid #7367f0 !important;
	}
	.to-time{
		border-right: 2px solid #7367f0 !important;
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
								<li class="breadcrumb-item"><a href="<?php echo e(route('faculty.dashboard')); ?>">Home</a>
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
								<form action="<?php echo e(route('studioassistant.timetable.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<fieldset class="form-group">
												<?php $faculty = \App\User::where('role_id', '2')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control" name="faculty_id">
													<option value=""> - Select Faculty - </option>
													<?php if(count($faculty) > 0): ?>
													<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(app('request')->input('faculty_id') == $value->id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Date</label>
											<fieldset class="form-group">
												<?php if(!empty(app('request')->input('fdate'))): ?>
												<input type="date" name="fdate" placeholder="Date" class="form-control" value="<?php echo e(app('request')->input('fdate')); ?>">
												<?php else: ?>
												<input type="date" name="fdate" placeholder="Date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
												<?php endif; ?>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row" id="table-responsive">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<table class="table table-responsive mb-0">
									<thead>
										<tr>											
											<th scope="col">Studio\Time</th>
											<?php if(count($timeslots)): ?>
											<?php $__currentLoopData = $timeslots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<th scope="col">
												<div class="set_time" id="<?php echo e($time->id); ?>"><?php echo e($time->time_slot); ?></div>
											</th>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</tr>
									</thead>
									<tbody>
										<?php if(count($get_studios) > 0): ?>
										<?php $__currentLoopData = $get_studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td scope="row text-center">
												<p><?php echo e($value->name); ?></p>
											</td>
											<?php $faculty_timetables = $value->timetable; ?>
											<?php if(count($faculty_timetables) > 0): ?>										
											
											<?php 

											$from_time = date('H:i', strtotime($faculty_timetables[0]->from_time));

											$timeslot = \App\TimeSlot::where('time_slot', $from_time)->first();

											for($i=1;$i<$timeslot->id;$i++){
												?>
												<td></td>
												<?php } ?>
												<?php
												$faculty = [];											
												for ($g=0;$g<count($faculty_timetables);$g++)											
												{
													
													$faculty = $faculty_timetables[$g];

													$start=strtotime(date('H:i', strtotime($faculty->from_time)));
													$end=strtotime(date('H:i', strtotime($faculty->to_time)));
													$number = 0;
													for ($i=$start;$i<=$end;$i = $i + 5*60)
													{
														$number++;
													}
													
													$online = true;
													$topic_name = "";
													if($faculty->class_type=="offline"){
														$ii = 1;
														$online = false;
														$topic_name .= isset($faculty->chapter->name) ? "($ii) " .$faculty->chapter->name. ' - ' : "($ii) ";
														$topic_name .= isset($faculty->topic->name) ? $faculty->topic->name : '';
														$parent_timetables = \App\Timetable::with(['chapter','topic'])->where('time_table_parent_id', $faculty->id)->get();
														if(count($parent_timetables) > 0 ){
															foreach($parent_timetables as $pTimetable){
																$ii++;
																$topic_name .= isset($pTimetable->chapter->name) ? "($ii) " .$pTimetable->chapter->name. ' - ' : "($ii) ";
																$topic_name .= isset($pTimetable->topic->name) ? $pTimetable->topic->name : '';
															}
														}
														
													}
													else{
														$topic_name = isset($faculty->topic->name) ? $faculty->topic->name : '';
													}
													?>
													<td colspan="<?php echo e($number); ?>">
														<div class="faculty-set from-time to-time">
															<div class="row">
																<div class="col-lg-6">
																	<p><?php echo e(isset($value->assistant->name) ? $value->assistant->name : ''); ?></p>
																	<span><?php echo e(isset($value->assistant->mobile) ? $value->assistant->mobile : ''); ?></span>
																	<span><?php echo e($topic_name); ?></span>
																</div>
																<div class="col-lg-6">
																	<p><?php echo e($faculty->faculty->name); ?></p>
																	<span><?php echo e(date('H:i', strtotime($faculty->from_time))); ?> - <?php echo e(date('H:i', strtotime($faculty->to_time))); ?></span>
																	<span><?php echo e($faculty->faculty->mobile); ?></span>
																</div>
															</div>
															<?php
															$startclass = \App\StartClass::where('timetable_id', $faculty->id)->first();
															?>
															<?php if(isset($startclass) && !empty($startclass)): ?>
															<?php if($startclass->status == 'Start Class'): ?>
															<a href="javascript:void(0);" data-id="<?php echo e($faculty->id); ?>" class_status="Partially" class="btn btn-sm btn-primary mt-1 end_class" data-class_type="<?php echo e($faculty->class_type); ?>" data-batch_id="<?php echo e($faculty->batch_id); ?>" data-course_id="<?php echo e($faculty->course_id); ?>" data-subject_id="<?php echo e($faculty->subject_id); ?>" >
																Partially End Class
															</a>
															<a href="javascript:void(0);" data-id="<?php echo e($faculty->id); ?>" class_status="End Class" class="btn btn-sm btn-primary mt-1 end_class" data-class_type="<?php echo e($faculty->class_type); ?>">
																End Class
															</a>
															<?php else: ?>
															<?php if($startclass->status == 'Partially'): ?>
															<a href="#" class="btn btn-sm btn-primary mt-1">
																Partially Class Completed
															</a>
															<?php else: ?>
															<a href="#" class="btn btn-sm btn-primary mt-1">
																Class Completed
															</a>
															<?php endif; ?>
															<?php endif; ?>
															<?php else: ?>
															<a href="#" data-id="<?php echo e($faculty->id); ?>" class="btn btn-sm btn-primary mt-1 start_class">
																Start Time
															</a>
															<?php endif; ?>
														</div>											
													</td>

													<?php
													if($g<count($faculty_timetables)-1){
														$az = date('H:i', strtotime($faculty_timetables[$g+1]->from_time));
														$timeslot1 = \App\TimeSlot::where('time_slot', $az)->first();
														$timeslot2 = \App\TimeSlot::where('time_slot', date('H:i', strtotime($faculty_timetables[$g]->from_time)))->first();

														for($s=0;$s<$timeslot1->id-($number+$timeslot2->id);$s++) {
															?>
															<td></td>

															<?php } } ?>

															<?php } ?>
															<?php endif; ?>
														</tr>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>              
							</section>
						</div>
					</div>
				</div>
				
				<div id="class_end_popup" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<form method="post" id="submit_reschedule_form">
								<div class="modal-header">
									<h5 class="modal-title class_type_set">Start Time</h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2 online_class">
											<div class="col-md-12 col-12">
												<div class="form-label-group">
													<strong for="">Are you sure you want to <span class="class_type_set" style="color:#4839eb;"></span> ? </strong>
												</div>
											</div>
											<div class="col-md-12 col-12 partially_online">
											
												
												
											</div>
											
										</div>
										
											
									</div>
								</div>
								<input type="hidden" name="timetable_id" class="timetable_id" value="">
								<input type="hidden" name="class_status" class="class_status" value="">
								<input type="hidden" name="class_type" class="class_type" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary endclass_btn">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
							
							
						</div>
					</div>
				</div>
				
				<div id="reschedule" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_timetable_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="<?php echo e(old('from_time')); ?>" autocomplete="off">
													<label for="first-name-column">From Time</label>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="<?php echo e(old('to_time')); ?>" autocomplete="off">
													<label for="first-name-column">To Time</label>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="date" class="form-control" placeholder="Date" name="cdate" value="<?php echo e(date('Y-m-d')); ?>" autocomplete="off">
													<label for="first-name-column">Date</label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="studio_id" class="studio_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="timetable_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<?php $__env->stopSection(); ?>

				<?php $__env->startSection('scripts'); ?>				
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
				<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
				<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
				<script type="text/javascript">
					$(document).ready(function(){
						$(document).on('focus', '.timepicker', function(){
							$(this).timepicker({
								interval: 5,
								timeFormat: 'HH:mm',
								minTime: '5:00',
							});
						});
					}); 
				</script>
				
				<script type="text/javascript">
				
				    $(document).on("change",".batch_accord_subject", function () { 
						var thisVal = $(this);
						var batch_id = $(this).val(); 
						if (batch_id) {
							$.ajax({
								type : 'POST',
								url : '<?php echo e(route('studioassistant.get-course')); ?>',
								data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									// $('.batch_accord_course').empty();
									thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').html(data);
									thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').trigger("change");
									
								}
							});
						}
					});
					
					$(document).on("change", ".batch_accord_course", function () { 
						var thisVal = $(this);
						var course_id =  $(this).val();
						var batch_id = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
						
						if (course_id) {
							$.ajax({
								type : 'POST',
								url : '<?php echo e(route('studioassistant.get-class-batch-subject')); ?>',
								data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subjects').html(data);
								}
							});
						}
					});
					
					
					$(document).on("change", ".batch_accord_subjects", function () {
						var thisVal    = $(this);
						var subject_id = $(this).val();
						var batch_id   = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
						var course_id  = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').val();
						
						if (subject_id && course_id) {
							$.ajax({
								type : 'POST',
								url : '<?php echo e(route('studioassistant.get-chapter')); ?>',
								data : {'_token' : '<?php echo e(csrf_token()); ?>', 'subject_id': subject_id,'course_id':course_id},
								dataType : 'html',
								success : function (data){
									thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_chapter').html(data);
								}
							});
						}
					});
					
					$(document).on("change", ".batch_accord_chapter", function () {
						var thisVal    = $(this);
						var chapter_id = $(this).val();
						var batch_id   = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
						var course_id  = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').val();
						var subject_id = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subjects').val();
						
						if (chapter_id) {
							$.ajax({
								type : 'POST',
								url : '<?php echo e(route('studioassistant.get-topic')); ?>',
								data : {'_token' : '<?php echo e(csrf_token()); ?>', 'batch_id': batch_id, 'course_id': course_id, 'subject_id': subject_id, 'chapter_id': chapter_id},
								dataType : 'html',
								success : function (data){
									thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_topic').html(data);
								}
							});
						}
					});
					
				</script>
				
				
				<script type="text/javascript">
					$(".start_class").on("click", function() {

						var timetable_id = $(this).attr("data-id");

						job=confirm("Are you sure to start class?");

						if(job != true){
							return false;
						}
						
						if(timetable_id){
							$.ajax({
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '<?php echo e(route('studioassistant.startclass.store')); ?>',
								data : {'timetable_id': timetable_id},
								dataType : 'json',
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
						}       
					});
				</script>
				<script type="text/javascript">
				
					$(".end_class").on("click", function() {
						var timetable_id = $(this).attr("data-id");
						$(".timetable_id").val(timetable_id);
						var class_status = $(this).attr("class_status");
						$(".class_status").val(class_status);
						if(class_status == 'Partially'){
							$(".forOnlineBatch").hide();
							$(".class_type_set").text('Partially End Class');
						}else{
							$(".forOnlineBatch").show();
							$(".class_type_set").text('End Class');
						}
						var class_type = $(this).attr("data-class_type"); 
						$(".class_type").val(class_type);
						if(class_type=="online"){
							$(".online_class").show();
							$(".endclass_btn").text('Yes');
							
							if(class_status == 'Partially' || class_status == 'End Class'){
								$.ajax({   
									type: "POST",
									url : '<?php echo e(route('studioassistant.partially_end_class_data')); ?>',
									data : {'_token' : '<?php echo e(csrf_token()); ?>', 'timetable_id': timetable_id},
									dataType : 'html',
									success : function(data){
										$('.partially_online').empty();
										$('.partially_online').append(data);
										$("#class_end_popup").modal('show');
									}
								});
							}
							
						}
					});
					
					$(".endclass_btn").on("click", function() {
						//$(".endclass_btn").attr('disabled',true);
						var end_date = $(".end_date").val();
						 var timetable_id = $(".timetable_id").val();
						// var class_status = $(".class_status").val();
						var class_type = $(".class_type").val();
						
						var form = document.getElementById('submit_reschedule_form');
						var dataForm = new FormData(form); 

						if(end_date != "" || class_type=='online'){ 
							if(timetable_id){
								$.ajax({
									headers: {
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									},      
									type: "POST",
									url : '<?php echo e(route('studioassistant.endclass.update')); ?>',
									//data : {'timetable_id': timetable_id, 'class_status': class_status, 'end_date': end_date, 'class_type' : class_type},
									data : dataForm,
									processData : false, 
									contentType : false,
									dataType : 'json',
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
							}
							else{
								alert('Something went wrong.');
								$(".endclass_btn").attr('disabled',false);
							}
						}
					})
					
					/*$(".end_class").on("click", function() {

						var timetable_id = $(this).attr("data-id");
						var class_status = $(this).attr("class_status");

						if(class_status == 'Partially'){
							job=confirm("Are you sure to partially end class?");
						}else{
							job=confirm("Are you sure to end class?");
						}						

						if(job != true){
							return false;
						}
						
						if(timetable_id){
							$.ajax({
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '<?php echo e(route('studioassistant.endclass.update')); ?>',
								data : {'timetable_id': timetable_id, 'class_status': class_status},
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error").then(function(){ 
											location.reload();
										});;
									} else if(data.status == true){
										swal("Done!", data.message, "success").then(function(){ 
											location.reload();
										});
									}
								}
							});
						}       
					});*/
				</script>
				<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studioassistant', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studioassistant/timetable/index.blade.php ENDPATH**/ ?>