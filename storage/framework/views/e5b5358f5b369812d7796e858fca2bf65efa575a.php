
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Appraisal Questions</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="<?php echo e(route('admin.appraisal.create')); ?>" class="btn btn-primary float-right ">Add Appraisal Question</a>
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
								<form action="<?php echo e(route('admin.appraisal.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Question</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="question" placeholder="Question" value="<?php echo e(app('request')->input('question')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple status" name="status">
													<?php $status = ['Active', 'Deactive']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.appraisal.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Question</th>
								<th>Status</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($appraisal_question_result) > 0): ?>
							<?php $__currentLoopData = $appraisal_question_result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e(!empty($value->question) ? $value->question : ''); ?></td>
								<td class="product-category">
									<a href="<?php echo e(route('admin.appraisal.status', $value->id)); ?>">
										<strong class="fa fa-lg <?php echo e($value->status == 'Active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
									</a>
								</td>
								<td><?php echo e(date('d-m-Y', strtotime($value->created_at))); ?></td>
								<td class="product-action">
									<a href="<?php echo e(route('admin.appraisal.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<form id="appraisal-<?php echo e($value->id); ?>" action="<?php echo e(route('admin.appraisal.destroy', $value->id)); ?>" method="POST">
										<?php echo csrf_field(); ?>
										<?php echo method_field('DELETE'); ?>
										<a href="javascript::void();" onclick="javascript:if(confirm('Are You Sure To Delete?')){ document.getElementById('appraisal-<?php echo e($value->id); ?>').submit();}">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
									</form>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
							<tr><td class="text-center text-primary" colspan="10">No Record Found</td></tr>	
							<?php endif; ?>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/appraisal/index.blade.php ENDPATH**/ ?>