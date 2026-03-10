
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Paternity Leave</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Paternity Leave View</li>
							</ol>
						</div>
					</div>
					<div class="col-4">
						<a class="btn btn-primary" style ="float:right;" href="<?php echo url('admin/paternity-leave') ?>">Add Paternity Leave</a>
					</div>
					
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
			
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Employee Id</th>	
								<th>Employee Name</th>
								<th>Paternity Leave</th>	
								<th>Session</th>
												
							</tr>
						</thead>
						<tbody >
						<?php 	if(count($paternity_records) > 0){
									$i = 1;
									?>
                                    <?php $__currentLoopData = $paternity_records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo e($records->user_id); ?></td> 
										<td><?php echo e($records->name); ?></td>                                     					 <td><?php echo e($records->paternity_leave); ?></td>
                                        <td><?php echo e($records->session); ?></td>
                                    </tr>
                                    <?php $i++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									 <?php } else { ?>
										<tr class="raw_data">
											<td colspan="6" style="text-align: center;">No record found.</td>
										</tr>
									<?php } ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/leave/paternity_leave_list.blade.php ENDPATH**/ ?>