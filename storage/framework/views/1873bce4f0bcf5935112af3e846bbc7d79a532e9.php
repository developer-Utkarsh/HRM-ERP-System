
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Notifications</h2>
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
								<form action="<?php echo e(route('admin.notification.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="title" placeholder="Title" value="<?php echo e(app('request')->input('title')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control" name="s_date" value="<?php if(!empty(app('request')->input('s_date'))): ?><?php echo e(app('request')->input('s_date')); ?><?php else: ?><?php echo e(date('Y-m-d')); ?><?php endif; ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
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
								<th>Title</th>
								<th>Description</th>
								<th>Image</th>
								<th>Created</th>
								<?php if(Auth::user()->role_id == 24 ||  Auth::user()->role_id == 29): ?>
								<th>Action</th>
							<?php endif; ?>
							</tr>
						</thead>
						<tbody>
						<?php if(count($notifications) > 0): ?>
							<?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->title); ?></td>
								<td><?php echo $value->description; ?></td>
								<td>
									<?php if(!empty($value->image)): ?>
									<img src="<?php echo e(asset('laravel/public/notification/'.$value->image)); ?>" height="80" width="80">
									<?php endif; ?>
								</td>
								<td>
								<?php
								if(!empty($value->created_at)){
									echo $value->created_at->format('d-m-Y');
								}
								?>
								</td>
								<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29): ?>
								<td class="product-action">
									<a href="<?php echo e(route('admin.notification.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('admin.notifications.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Notification')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
								<?php endif; ?>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
							<tr>
							<td class="text-center" colspan="6">No Data Found</td>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/notification/index.blade.php ENDPATH**/ ?>