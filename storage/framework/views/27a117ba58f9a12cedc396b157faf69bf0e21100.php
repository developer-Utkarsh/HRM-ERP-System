
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">View Role List</h2>
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
			
			<div class="table-responsive">
				<table class="table data-list-view" id="">
					<thead>
						<tr>
							<th>S. No.</th>
							<th>Name</th>
							<th>Created</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$i = 1;
							foreach($roles as $r){
						?>
						<tr>
							<td><?=$i;?></td>
							<td><?php echo e($r->name); ?></td>
							<td><?php echo e($r->created_at); ?></td>
							<td>
								<a href="<?php echo e(route('admin.permission-edit', $r->id.'/'.$r->name)); ?>"  title="Employee Details">
									<span class="action-edit"><i class="feather icon-edit"></i></span>
								</a>
								
								<a href="<?php echo e(route('admin.permission-delete', $r->id)); ?>" onclick="return confirm('Are You Sure To Delete Role')">
									<span class="action-delete"><i class="feather icon-trash"></i></span>
								</a>
							</td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div> 
		</div>
	</div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/roles_permission/index.blade.php ENDPATH**/ ?>