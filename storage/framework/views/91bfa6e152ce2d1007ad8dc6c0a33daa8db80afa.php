
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">UAN NO List</h2>
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
								<form action="<?php echo e(route('admin.employees.uan-no-detail')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="<?php echo e(app('request')->input('search')); ?>" id="myInputSearch">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 status" name="status">
													<?php $status = ['0'=>'Empty', '1'=>'Not Empty', '2'=>'All']; ?>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($key); ?>" <?php if($key == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.employees.uan-no-detail')); ?>" class="btn btn-warning">Reset</a>
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
                                <th>Emp. Code</th>
								<th>Name</th>
								<th>UAN No.</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($employees) > 0): ?>
								<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td><?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?></td>
									<td><?php echo e(!empty($value->name) ? $value->name : '--'); ?></td>
									<td><?php echo e(!empty($value->user_details->uan_no) ? $value->user_details->uan_no : '--'); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							<?php endif; ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $employees->appends($params)->links(); ?>

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
<script>
function myFunctionSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInputSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableSearch");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/uan_no.blade.php ENDPATH**/ ?>