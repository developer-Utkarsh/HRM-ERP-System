
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Subjects List</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('studiomanager.faculty-reports.subjects')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="<?php echo e(app('request')->input('search')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="branch_id">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('studiomanager.faculty-reports.subjects')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Employee</th>
								<!--th>Branch</th-->
								<th>Subjects</th>
								<th>Mobile</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php
							//echo "<pre>";print_r($value->user_branches); die;
							?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td><?php echo e($value->name); ?></td>
								<td class="product-name">
								<?php echo e($value->register_id); ?>

								</td>
								<!--td>
								<?php
								/*$branch_names = "";
								if(isset($value->user_branches) && !empty($value->user_branches)){
									foreach($value->user_branches as $key => $val) { 
										if(!empty($val->branch->name)) {
											$branch_names .= $val->branch->name .", ";
										}
									}
								}
								echo rtrim($branch_names, ", "); */
								?>
								</td-->
								<td>
								<?php
								$subject_names = "";
								if(isset($value->faculty_subjects) && !empty($value->faculty_subjects)){
									foreach($value->faculty_subjects as $key => $val) { 
										if(!empty($val->subject->name)) {
											$subject_names .= $val->subject->name .", ";
										}
									}
								}
								echo rtrim($subject_names, ", "); 
								?>
								</td>
								
								<td class="product-price"><?php echo e($value->mobile); ?></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>                   
			</section>
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
		$('.select-multiple2,.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/faculty_reports/subjects.blade.php ENDPATH**/ ?>