
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Inventory</h2>
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
								<form action="<?php echo e(route('admin.batchinventory.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Inventory Name / Batch Code</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Search" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Inventory Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple" name="itype">
													<?php $itype = ['all', 'batch']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $itype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('itype')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<div class="form-group">
												<label for="first-name-column">Batch Name</label>
												<fieldset class="form-group">
													<input type="text" class="form-control" name="batch_name" placeholder="Search" value="<?php echo e(app('request')->input('batch_name')); ?>">
												</fieldset>
												<?php if($errors->has('batch_name')): ?>
												<span class="text-danger"><?php echo e($errors->first('batch_name')); ?> </span>
												<?php endif; ?>
											</div>	
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple" name="status">
													<?php $status = ['Inactive', 'Active']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Created by </label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="uname" placeholder="Search" value="<?php echo e(app('request')->input('uname')); ?>">
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Created Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control" name="created_date" placeholder="Search" value="<?php echo e(app('request')->input('created_date')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.batchinventory.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Type</th>
								<th>Created By</th>
								<th>Inventory Name</th>
								<th>Qty</th>
								<th>Batch Code</th>
								<th>Batch Name</th>
								<th>Inventory Type</th>
								<th>Status</th>								
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($pageNumber++); ?></td>
								<td class="product-category"><?php echo e($value->type); ?></td>
								<td class="product-category"><?php echo e($value->userName); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category"><?php echo e($value->quantity); ?></td>
								<td class="product-category"><?=($value->batch_code)?$value->batch_code:'-';?></td>
								<td class="product-category"><?=($value->bname)?$value->bname:'-';?></td>
								<td class="product-category"><?=($value->inventory_type)?$value->inventory_type:'-';?></td>
								<td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td>								 
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
								<td class="product-action">
									<!--
									<a href="<?php echo e(route('admin.batchinventory.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a> &nbsp;&nbsp;
									-->
									<a href="<?php echo e(route('admin.batchinventory.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Inventory')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $inventory->appends($params)->links(); ?>

					</div>
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
	
	$('.select-multiple1').select2({
		placeholder: "Select Batch",
		allowClear: true
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/index.blade.php ENDPATH**/ ?>