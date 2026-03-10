
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Studios</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.studios.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
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
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Assistants</label>
											<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="assistant_id">
													<option value="">Select Any</option>
													<?php if(count($assistants) > 0): ?>
													<?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('assistant_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control" name="type">
													<option value="">Select Type</option>
													<option value="Online" <?php if('Online' == app('request')->input('type')): ?> selected="selected" <?php endif; ?>>Online</option>
													<option value="Offline" <?php if('Offline' == app('request')->input('type')): ?> selected="selected" <?php endif; ?>>Offline</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.studios.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Branch</th>
								<th>Studio Name</th>
								<th>Order Number </th>
								<th>Studio Assistant</th>
								<!--th>View Schedule</th-->
								<th>Type</th>
								<th>Capacity</th>
								<th>Change Assistant</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($studios) > 0): ?>
							<?php $__currentLoopData = $studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e(isset($value->branch->name) ?  $value->branch->name : ''); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category"><?php echo e($value->order_no); ?></td>
								<td class="product-category"><?php echo e(isset($value->assistant->name) ?  $value->assistant->name : ''); ?></td>
								<!--td class="product-category">
									<a href="#" class="btn btn-sm btn-primary">
										View Schedule
									</a>
								</td-->
								<td><?php echo e($value->type); ?></td>
								<td>
								<?php
								if($value->type=='Offline'){
									echo $value->capacity;
								}
								else{
									echo "-";
								}
								?>
								</td>
								<td class="product-category">
									<a href="<?php echo e(route('admin.studios.edit', $value->id)); ?>" class="btn btn-sm btn-primary">
										Change Assistant
									</a>
									
								</td>
								<!--td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td-->
								<td>
									
									<a href="<?php echo e(route('admin.studios.status', $value->id)); ?>">
										<strong class="fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
									</a>
								</td>
								<td class="product-action">
									<a href="<?php echo e(route('admin.studios.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('admin.studio.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Studio')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center" colspan="10">No Data Found</td>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/studio/index.blade.php ENDPATH**/ ?>