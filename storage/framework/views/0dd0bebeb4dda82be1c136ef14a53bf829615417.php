
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Training Video</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="<?php echo e(route('admin.training_video.create')); ?>" class="btn btn-primary float-right ">Add Training Video</a>
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
								<form action="<?php echo e(route('admin.training_video.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control title" name="title" placeholder="Title" value="<?php echo e(app('request')->input('title')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple status" name="status">
													<?php $status = ['Active', 'Inactive']; ?>
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
											<a href="<?php echo e(route('admin.training_video.index')); ?>" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
								<th>Image</th>
								<th>Employee Name</th>
								<th>Category Name</th>
								<th>Title</th>
								<th>Date</th>
								<th>Video ID</th>
								<th>Video Url</th>
								<th>Description</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($training_video) > 0): ?>
							<?php $__currentLoopData = $training_video; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><img src="<?php echo e(asset('laravel/public/training_video_image_url/')); ?><?php echo e(!empty($value->image_url) ? '/'.$value->image_url : '/default-image.png'); ?>" style="width: 100px;height: 100px;"></td>
								<td class="product-category"><?php echo e(!empty($value->user_name) ? $value->user_name : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->cat_name) ? $value->cat_name : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->title) ? $value->title : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->date) ? date('d-m-Y',strtotime($value->date)) : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->video_id) ? $value->video_id : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->video_url) ? $value->video_url : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->description) ? $value->description : ''); ?></td>
								<td class="product-category"><?php echo e(!empty($value->status) ? $value->status : ''); ?></td>
								<td class="product-action">
									<a href="<?php echo e(route('admin.training_video.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('admin.training_video.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Knowledge Based')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
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
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
			data.title   = $('.title').val(),
			data.status = $('.status').val(), 
			window.location.href = "<?php echo URL::to('/admin/'); ?>/training-video-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/training_video/index.blade.php ENDPATH**/ ?>