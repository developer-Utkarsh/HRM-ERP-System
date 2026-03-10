
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Appraisal User List</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.appraisal-user-list')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-2">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="<?php if(!empty(Request::get('year_wise_month'))): ?><?php echo e(Request::get('year_wise_month')); ?><?php else: ?><?php echo e(date('Y-m')); ?><?php endif; ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.appraisal-user-list')); ?>" class="btn btn-warning">Reset</a>
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
								<th>User Name</th>
								<th>Employee Date</th>
								<th>Overall Remark</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($appraisal_user_result) > 0): ?>
							<?php $__currentLoopData = $appraisal_user_result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php
								$check_role = \App\User::where('id',$value->user_id)->first();
							?>
							<tr>
								<td><?php echo e($key + 1); ?></td> 
								<td class="product-category"><?php echo e(!empty($value->user_name) ? $value->user_name : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->date) ? date('d-m-Y', strtotime($value->date)) : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->overall_remark) ? $value->overall_remark : ''); ?></td>
								<td class="product-category">
									<a href="<?php echo e(route('admin.appraisal-user-question-list', [$value->id,$value->user_id])); ?>" class="btn btn-primary btn-sm">View</a>
									<?php if($check_role->role_id == 21): ?>
									<a href="<?php echo e(route('admin.appraisal-user-question-response', [$value->id,$value->user_id])); ?>" class="btn btn-primary btn-sm">Response</a>
									<?php endif; ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/appraisal/appraisal-user-list.blade.php ENDPATH**/ ?>