
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Issue Raise Faculty Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="<?php echo e(route('admin.course-planner.issue-raise-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Location Wise</label>
											<fieldset class="form-group">
												<?php 
													$branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->groupby('branch_location')->get(); ?>
												<?php if(count($branches) > 0): ?>
												<select class="form-control branch_id get_branch" name="branch_location">
													<option value=""> - Select - </option>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->branch_location); ?>" <?php if($value->branch_location == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>><?php echo e(ucwords( $value->branch_location)); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
												<?php endif; ?>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Course Wise</label>
											<fieldset class="form-group">
												<?php $course = \App\Course::where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control select-multiple1" name="course_id">
													<option value=""> - Select Course - </option>
													<?php if(count($course) > 0): ?>
													<?php $__currentLoopData = $course; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(app('request')->input('course_id') == $value->id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch Wise</label>
											<fieldset class="form-group">
												<?php $batch = \App\Batch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control select-multiple2" name="batch_id[]" multiple>
													<option value=""> - Select Batch - </option>
													<?php if(count($batch) > 0): ?>
													<?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(in_array($value->id,$_GET['batch_id']??[])){ echo "selected"; } ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Studio Assistant Wise</label>
											<?php
												$assistant = \App\User::where('role_id', '3')->where('status', '1')->orderBy('id', 'desc')->get();
											?>
											<fieldset class="form-group">
												<select class="form-control select-multiple3" name="assistant_id">
													<option value=""> - Select Month - </option>
													<?php if(count($assistant) > 0): ?>
													<?php $__currentLoopData = $assistant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(app('request')->input('assistant_id') == $value->id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From Date</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control" value="<?php echo e(app('request')->input('fdate')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To Date</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control" value="<?php echo e(app('request')->input('tdate')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">IS Remark Available</label>
											<fieldset class="form-group">
												<!--<input type="text" name="remark" class="form-control" value="<?php echo e(app('request')->input('remark')); ?>">-->
												<select name="remark" class="select-multiple3 form-control">
													<option value="">Select</option>
													<option value="Yes" <?php if('Yes' == app('request')->input('remark')): ?> selected="selected" <?php endif; ?>>Yes</option>
													<option value="No" <?php if('No' == app('request')->input('remark')): ?> selected="selected" <?php endif; ?>>No</option>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
											<a href="<?php echo e(route('admin.course-planner.issue-raise-reports')); ?>" class="btn btn-warning mt-1">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row mx-0">
					<div class="table-responsive">
						<table class="table data-list-view">
							<thead>
								<tr>
									<th>S. No.</th>
									<th>Class Date</th>
									<th>Location</th>
									<th>Course Name</th>
									<th>Batch Name</th>
									<th>Subject Name</th>
									<th>Faculty Name</th>
									<th>Studio Assistant Name & Number</th>
									<th>Topics</th>
									<th>Issue Raised By Faculty</th>
									<th>Remark Submitted By Admin</th>
								</tr>
							</thead>
							<tbody>	
								<?php  
									if(count($issue_report) >0){
									$i = 1;
									foreach($issue_report as $val){
										$topin_name = '';
										$get_topic = DB::table('timetable_topic')
													->select('topic.name')
													->leftjoin('topic','topic.id','timetable_topic.topic_id')
													->where('timetable_id',$val->timetable_id)
													->get();
										foreach($get_topic as $key){
											$topin_name .= $key->name.', ';
										}
								?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td><?php echo e($val->cdate); ?></td>
									<td><?php echo e($val->branch_name); ?></td>
									<td><?php echo e($val->course_name); ?></td>
									<td><?php echo e($val->batch_name); ?></td>
									<td><?php echo e($val->subject_name); ?></td>
									<td><?php echo e($val->faculty_name); ?></td>
									<td><?php echo e($val->assistant_name); ?></td>
									<td><?php echo e($topin_name); ?></td>
									<td><?php echo e($val->topic_issue); ?></td>
									<td>
										<?php echo e($val->topic_issue_remark); ?>

										<?php if(empty($val->topic_issue_remark)): ?>
										  </br></br>
										 <button type="button" class="raise_issue" data-toggle="modal" data-target="#modalRaiseIssue" data-id="<?php echo e($val->timetable_id); ?>">Remark</button>
										<?php endif; ?>
									</td>
								</tr>
								<?php $i++; } 
								
									}else{ 
								?>
								<tr>
									<td colspan="11" class="text-center">No Record Found</td>
								</tr>
								<?php } ?>
							</tbody>
							
						</table>
						<div class="d-flex justify-content-center">					
						<?php echo $issue_report->appends($params)->links(); ?>

						</div>
					</div>
				</form>                   
			</section>
		</div>
	</div>
</div>

<!-- Submit Issue on topic -->
<div class="modal fade" id="modalRaiseIssue" tabindex="-1" role="dialog" a>
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" style="color:#FF6F0E">Submit Remark</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<input type="hidden" name="timetable_id" class="timetable_id">
			
			<div class="modal-body">
				<textarea name="topic_issue" class="form-control topic_issue" rows="4" placeholder="Type your issue here" style="resize:none;"></textarea>
				<span class="text-secondary topic_issue_chr_limit">0 /200</span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn w-100 raise_issue_btn text-dark" style="background:#DEDEDE"><b>Submit Now</b></button>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>

<script type="text/javascript">

	$(document).on("keypress",".topic_issue",function(){
        if($(this).val().length>10){
        	$(".raise_issue_btn").attr("disabled",false);
        	$(".raise_issue_btn").addClass("btn-primary");
        	$(".raise_issue_btn").removeClass("btn-outline-dark");
        }else{
        	$(".raise_issue_btn").attr("disabled",true);
        	$(".raise_issue_btn").removeClass("btn-primary");
        	$(".raise_issue_btn").addClass("btn-outline-dark");
        	
        }

        $(".topic_issue_chr_limit").text($(this).val().length+"/200");
	});

	$(document).on("click",".raise_issue",function(){
		var id=$(this).attr("data-id");
		$("#modalRaiseIssue .timetable_id").val(id);
	});


	$(".raise_issue_btn").on("click",function(){
		var timetable_id=$("#modalRaiseIssue .timetable_id").val();
		var topic_issue=$("#modalRaiseIssue .topic_issue").val();
		
		if(topic_issue.length<10 || topic_issue.length>300){
		   alert("Please Enter Proper Issue why topic is wrong.");
		  return;
		}

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}, 
			type: "POST",
			url : '<?php echo e(route('raise_issue_on_topic')); ?>',
			data:{"remark_admin":"remark_by_admin","timetable_id":timetable_id,"topic_issue":topic_issue},
			dataType : 'json',
			success : function(data){
				if(data.status){
					swal("Success", data.message, "success");
					$('#modalRaiseIssue').modal('toggle');
                    $("#modalRaiseIssue .topic_issue").val('');
				}else{
					swal("Error!", data.message, "error");
				}
			}
		});
	});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/report/course_planner/faculty_topic_issue.blade.php ENDPATH**/ ?>