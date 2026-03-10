
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Proposed Plan</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="match-height">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="users-list-filter">
									<form action="<?php echo e(route('admin.proposed-plan')); ?>" method="get">
										<?php echo csrf_field(); ?>
										<div class="row mx-0">								
											<div class="col-3">
												<label>Subject</label>
												<fieldset class="form-group">
													<select class="form-control subject select-multiple1" name="subject">
														<option value=""> -- Select -- </option>
														<?php $__currentLoopData = $subject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $su): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($su->id); ?>" <?php if($su->id == app('request')->input('subject')): ?> selected="selected" <?php endif; ?>><?php echo e($su->name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Faculty</label>
												<fieldset class="form-group">
													<select class="form-control faculty select-multiple1" name="faculty">
														<option value=""> -- Select -- </option>
														<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($fa->id); ?>" <?php if($fa->id == app('request')->input('faculty')): ?> selected="selected" <?php endif; ?>><?php echo e($fa->name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Location</label>
												<fieldset class="form-group">
													<select class="form-control location select-multiple1" name="location">
														<option value=""> -- Select -- </option>
														<?php $__currentLoopData = $location; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $la): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($la->branch_location); ?>" <?php if($la->branch_location == app('request')->input('location')): ?> selected="selected" <?php endif; ?>><?php echo e(ucwords($la->branch_location)); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Agreement</label>
												<fieldset class="form-group">
													<select class="form-control agreement select-multiple1" name="agreement">
														<option value=""> -- Select -- </option>
														<option value="Yes" <?php if('Yes' == app('request')->input('agreement')): ?> selected="selected" <?php endif; ?>>Fixed</option>
														<option value="No" <?php if('No' == app('request')->input('agreement')): ?> selected="selected" <?php endif; ?>>Variable</option>
														<option value="Both" <?php if('Both' == app('request')->input('agreement')): ?> selected="selected" <?php endif; ?>>Fixed+Variable</option>
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label for="users-list-verified">Month</label>
												<fieldset class="form-group">
													<input type="month" name="month" value="<?php echo e(app('request')->input('month') ?: now()->format('Y-m')); ?>" class="form-control"/>
												</fieldset>
											</div>
											<div class="col-4 pt-2">
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="<?php echo e(route('admin.proposed-plan')); ?>" class="btn btn-warning">Reset</a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
					
					<div class="">												
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>S.No.</th>
													<th>Subject</th>
													<th>Faculty</th>
													<th>City</th>
													<th>Agreement Type</th>
													<th>Agreement Hours</th>
													<th>Spent Hours</th>
													<th>Proposed Faculty</th>
													<th>Spent Hours</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													if(count($report) >0){
													$i =1;
													$data_r=json_decode(json_encode($report),true);
													foreach($report as $re){ 
														$daysInMonth = now()->daysInMonth;														
														$minUser = getMinSpentUserBySubject($data_r, $re->subject_id);

												?>
												<tr>
													<td><?php echo e($i++); ?></td>
													<td><?php echo e($re->subject_name); ?></td>
													<td><?php echo e($re->name); ?></td>
													<td><?php echo e(ucwords($re->city)); ?></td>
													<td>
														<?php 
															if($re->agreement=='Yes'){
																$agreement =  'Fixed';
															}else if($re->agreement=='No'){
																$agreement =  'Variable';
															}else{
																$agreement =  'Fixed + Variable';
															}
															
															echo $agreement;
														?>
													</td>
													<td><?php echo e($re->committed_hours); ?></td>
											
													<td><?php echo e(round($re->spent_hrs_1,2)); ?></td>																									
													<td><?php echo e($minUser['name']); ?></td>												
													<td><?php echo e($minUser['spent_hrs_1']); ?></td>												
												</tr>
												<?php } }else{ ?>
												<tr>
													<td colspan="15">No Record Found</td>
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
		</div>
	</div>
</div>

<?php

function getMinSpentUserBySubject($data, $subjectId) {
    $filtered = array_filter($data, function ($item) use ($subjectId) {
        return $item['subject_id'] == $subjectId;
    });

    if (empty($filtered)) {
        return null; // No user found for this subject_id
    }

    $minUser = null;
    $minSpent = PHP_FLOAT_MAX;

    foreach ($filtered as $user) {
        $spent = floatval($user['spent_hrs_1']);
        if ($spent < $minSpent) {
            $minSpent = $spent;
            $minUser = $user;
        }
    }

    return $minUser;
}
?>

<style>
	.dashboard b{
		font-size:22px;
		padding-bottom:10px;
	}
	
	.daywise thead th{
		font-size:11px !important; 
	}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>


<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/academic/proposed_plan.blade.php ENDPATH**/ ?>