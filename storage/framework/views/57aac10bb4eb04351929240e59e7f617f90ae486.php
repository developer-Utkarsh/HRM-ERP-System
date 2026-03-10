
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Planner Request View</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
							</ol>
						</div>
					</div>  
				</div>
			</div>
			<?php if(Auth::user()->department_type==50){?>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
				<a href="<?php echo e(route('admin.multi-course-planner.multi-planner-request')); ?>"><button class="btn btn-primary" type="button">Request Planner</button></a>
			</div>
			<?php } ?>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										<form action="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>" method="get" name="filtersubmit">
											<div class="row">
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="users-list-role">Planner Type Naming</label>
													<fieldset class="form-group">
														<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo e(app('request')->input('name')); ?>">
													</fieldset>
												</div>
												
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="users-list-status">Status</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple status" name="status">
															<?php $status = array('1' => 'Pending','2' => 'Approved','3' => 'Rejected'); ?>
															<option value="">Select Any</option>
															<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($key); ?>" <?php if( app('request')->input('status') != '' && $key == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>												
													</fieldset>
												</div>	
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="users-list-status">City</label>
													<fieldset class="form-group">												
														<?php
															$locations = \App\Branch::select('branch_location as name')
																->where('status', '1')
																->where('is_deleted', '0')
																->orderBy('name')
																->groupBy('branch_location')
																->get(); 
														?>								
														<select class="form-control select-multiple2 branch_location" name="branch_location">
															<option value="">Select Any</option>
															<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->name); ?>"
																	<?php if(request()->input('branch_location') == $value->name || old('branch_location') == $value->name): ?> selected <?php endif; ?>>
																	<?php echo e(ucwords($value->name)); ?>

																</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>												
													</fieldset>
												</div>	
												<div class="col-12 col-sm-6 col-lg-3">
													<label for="" style="">&nbsp;</label>
													<fieldset class="form-group">		
													<button type="submit" class="btn btn-primary">Search</button>
													<a href="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>S. No.</th>													
													<th>Course</th>
													<th>Planner Type Naming</th>
													<th>City Of Batch</th>
													<th>Expected Batch Duration (In Days)</th>
													<th>Course Mode</th>
													<th>Planner Timelines</th>
													<th>Special Instructions</th>
													<th>Request By</th>
													<th>Status</th>													
													<th>Reject Reason</th>													
													<th>
														Work Status 
														<i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Work In-Progress / Total"></i>
													</th>												
													<th>Action</th>													
												</tr>
											</thead>
											<tbody>
												<?php 
													if(count($record) > 0){
													$i =1;
													foreach($record as $re){
														

														
												?>
												<tr>
													<td><?php echo e($i); ?></td>													
													<td><?php echo e($re->cname); ?></td>
													<td><?php echo e($re->planner_name); ?></td>
													<td><?php echo e($re->city); ?></td>
													<td><?php echo e($re->duration); ?></td>
													<td><?php echo e($re->mode); ?></td>
													<td><?php echo e(date('d-m-Y',strtotime($re->timelines))); ?></td>
													<td><?php echo e($re->remark); ?></td>
													<td><?php echo e($re->uname); ?></td>
													<td>
														<?php															
															if ($re->status == 2) {
																$sText = "Approved By Content Head";
															} else if ($re->status == 3) {
																$sText = "Rejected By Content Head";
															} else {
																$sText = "Pending at Content Head";
															}
															
															echo $sText;
														?>
													</td>
													<td><?php echo e($re->reason??'-'); ?></td>
													<td>
														<button type="button" class="btn btn-primary btn-sm get_edit_data" data-id="<?php echo e($re->id); ?>">View Status</button>
													</td>
													<td>
														<?php if((Auth::user()->role_id==21 && Auth::user()->department_type==4) || Auth::user()->role_id==25 || Auth::user()->id==8232 || Auth::user()->role_id==27 || Auth::user()->id==8866){ ?>
														<a href="<?php echo e(route('admin.multi-course-planner.subject-assign', $re->id)); ?>" title="Subject & SME Assign">
															<span class="action-edit"><i class="feather icon-edit"></i></span>
														</a>
														<?php } ?>
														
														
														<?php if($re->status==2){ ?>
														<a href="<?php echo e(route('admin.multi-course-planner.multi-planner-summary', $re->id)); ?>" title="Course Planner View">
															<i class="fa fa-eye"></i>
														</a>
														<?php } ?>
														
														<?php if($re->status==3 && Auth::user()->department_type==50){ ?>
														<a href="<?php echo e(route('admin.multi-course-planner.edit-multi-planner-request', $re->id)); ?>" title="Reopen Reject Request">
															<i class="fa fa-pencil"></i>
														</a>
														<?php } ?>
														
														
														<?php if(Auth::user()->id==8232 || Auth::user()->id==8866){ ?>
														<i class="fa fa-upload uploadPlanner" aria-hidden="true" data-id="<?php echo e($re->id); ?>"></i>
														<?php } ?>
													</td>
												</tr>
												<?php $i++; } 
													}else{ 
												?>
												<tr>
													<td colspan="13" class="text-center">No Record Found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
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


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form action="<?php echo e(route('admin.multi-course-planner.upload-planner')); ?>" method="post" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Planner Upload</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="request_id" value="" class="request_id"/>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Chooes File</label>
						<input type="file" class="form-control"name="planner_file" id="recipient-name">
					</div>
					<div class="text-right"><a href="<?php echo e(asset('laravel/public/planner_upload.xlsx')); ?>" title="">Sample File</a></div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="myModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Planner Work Status</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<table class="table table-bordered">
					<thead class="thead-light">
						<tr>
							<th>Assigned Subjects</th>
							<th>Content Approved</th>
							<th>Assigned to Faculty</th>
							<th>Pending At Faculty</th>
							<th>Planner Created</th>
						</tr>
					</thead>
					<tbody class="fill-name">
					</tbody>					
				</table>
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

function selectAll() {
    $(".select-multiple1 > option").prop("selected", true);
    $(".select-multiple1").trigger("change");
}

function deselectAll() {
    $(".select-multiple1 > option").prop("selected", false);
    $(".select-multiple1").trigger("change");
}


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
	
	
	$(".get_edit_data").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#myModal').modal({
			backdrop: 'static',
			keyboard: true, 
			show: true
		});
				
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.multi-course-planner.show-work-status')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});		
	}); 
	
	
		
	$(".uploadPlanner").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#exampleModal').modal({
			backdrop: 'static',
			keyboard: true, 
			show: true
		});
		
		$('.request_id').val(request_id);
			
	}); 
</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/multi-course-planner/request_view.blade.php ENDPATH**/ ?>