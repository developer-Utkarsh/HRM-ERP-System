
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Employees Birthday List</h2>
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
								<form action="<?php echo e(route('admin.employees.birthday')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="<?php echo e(app('request')->input('search')); ?>">
											</fieldset>
										</div>
										
										<div class="col-md-2">
											<label for="users-list-role">From</label>
											<fieldset class="form-group">
												<input type="date" class="form-control b_date" name="b_date" value="<?php if(!empty(Request::get('b_date'))): ?><?php echo e(Request::get('b_date')); ?><?php else: ?><?php echo e(date('Y-m-d')); ?><?php endif; ?>">
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-role">To</label>
											<fieldset class="form-group">
												<input type="date" class="form-control t_date" name="t_date" value="<?php if(!empty(Request::get('t_date'))): ?><?php echo e(Request::get('t_date')); ?><?php else: ?><?php echo e(date('Y-m-d')); ?><?php endif; ?>">
											</fieldset>
										</div>
										
										<div class="col-md-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.employees.birthday')); ?>" class="btn btn-warning">Reset</a>
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
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Emp Code</th>
								<th>Mobile</th>
								<th>Branch</th>
								<th>Designation</th>
								<th>DOB Date</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($employees) > 0): ?>
								<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($key+1); ?></td>
									<td class="product-price"><?php echo e(!empty($value->name) ? $value->name : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->register_id) ? $value->register_id : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->mobile) ? $value->mobile : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->branches_name) ? $value->branches_name : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->degination) ? $value->degination : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->dob) ? date('d-m-Y',strtotime($value->dob)) : ''); ?></td>
									
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
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

<script>
$("body").on("click", "#download_excel", function (e) {
	var data = {};
		data.search    = $('.search').val(),
		data.b_date = $('.b_date').val(),
		data.t_date = $('.t_date').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-birthday-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/birthday.blade.php ENDPATH**/ ?>