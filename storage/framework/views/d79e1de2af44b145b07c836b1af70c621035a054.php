
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-12"> 
						<h2 class="content-header-title float-left mb-0">City-wise Faculty Utilization Dashboard</h2>
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
			<section>				
				<div class="match-height">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="users-list-filter">
									<form action="<?php echo e(route('admin.faculty-utilization-dashboard')); ?>" method="get">
										<?php echo csrf_field(); ?>
										<div class="row mx-0">								
											<div class="col-3 d-none">
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
												<a href="<?php echo e(route('admin.prediction-report')); ?>" class="btn btn-warning">Reset</a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th scope="col">Location</th>
									<th scope="col">Total Faculty</th>
									<th scope="col">Total Commitment Hours</th>
									<th scope="col">Planned Cost</th>
									<th scope="col">Total Spent Hours</th>
									<th scope="col">Forecast Overrun Cost (₹)</th>
									<th scope="col">Overutilized</th>
									<th scope="col">Underutilized</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$planned = 0;
									$overruncost = 0;
									foreach($report as $re){ 
										$planned = $planned + $re->total_planned_cost;
										$overruncost = $overruncost + $re->total_overrun_cost;
										
										if($planned > $overruncost){
											$arrow = '<i class="fa fa-arrow-circle-down text-success" aria-hidden="true" style="font-size:17px;"></i>';
										}else{
											$arrow = '<i class="fa fa-arrow-circle-up text-danger" aria-hidden="true" style="font-size:17px;"></i>';
										}
								?>
								<tr>
									<th scope="row"><?php echo e(ucwords($re->branch_location)); ?></th>
									<td><?php echo e($re->total_users); ?></td>
									<td><?php echo e($re->total_committed_hours); ?></td>
									<td><?php echo e($planned); ?></td>
									<td><?php echo e($re->total_spent_hours); ?></td>
									<td><?php echo e($overruncost); ?> <?=$arrow;?></td>
									<td><?php echo e($re->users_exceeding_commitment); ?></td>
									<td><?php echo e($re->users_within_commitment); ?></td>
								</tr>
								<?php } ?>										
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">Planned Cost (₹):	<b><?php echo e($planned); ?></b></td>
									<td colspan="4">Forecast Overrun Cost (₹) : <b><?php echo e($overruncost); ?></b></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>					
			</section>
		</div>
	</div>
</div>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/academic/faculty_utilization_dashboard.blade.php ENDPATH**/ ?>