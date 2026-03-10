
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Branches</h2>
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
								<form action="<?php echo e(route('admin.branch.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Branch Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<!--div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-name">Branch Name</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple" name="name">
													<option value="">Select Branch Name</option>
													<?php $__currentLoopData = $branch_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('name')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div-->
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="status">
													<?php $status = ['Inactive', 'Active']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.branch.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Related Branch</th>
								<th>Nickname</th>
								<th>Show IN Web</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($branches) > 0): ?>
								<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($key + 1); ?></td>
									<td class="product-category"><?php echo e($value->name); ?></td>
									<td>
										<?php if($value->related == "2"): ?>
												JAIPUR
										<?php elseif($value->related == "3"): ?>
										    PRAYAGRAJ
										<?php elseif($value->related == "4"): ?>
										    DELHI
										<?php elseif($value->related == "5"): ?>
										    INDORE
										<?php elseif($value->related == "6"): ?>
										    LUCKNOW
										<?php elseif($value->related == "7"): ?>
										    PATNA
										<?php else: ?>
											JODHPUR
										<?php endif; ?> 
									</td>
									<td class="product-category">
										<?php if($value->nickname!=""): ?>
											<?php echo e($value->nickname); ?>

										<?php else: ?>
											N/A
										<?php endif; ?>
									</td>
									<td>
										<?php if($value->show_in_web == 1): ?>
											Yes
										<?php else: ?>
											No
										<?php endif; ?>
									</td>
									<!--td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td-->
									<td>
										
										<a href="<?php echo e(route('admin.branch.status', $value->id)); ?>">
											<strong class="fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
										</a>
									</td>
									<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
									<td class="product-action">
										<a href="<?php echo e(route('admin.branch.edit', $value->id)); ?>">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<a href="<?php echo e(route('admin.branch.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Branch')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td colspan="10" class="text-center">No Data Found</td>
								</tr>
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
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/branch/index.blade.php ENDPATH**/ ?>