
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Employee Complaint</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">	
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Creator</th>
								<th>Message</th>
								<th>CEO Reply</th>
								<th>Created Date</th>
							</tr>
						</thead>
						<tbody>		
							<?php 
								$i=1; 
								foreach($query as $f){ 
							?>
							<tr>
								<td><?=$i++;?></td>
								<td><?=$f->uname;?></td>
								<td><?=$f->message;?></td>
								<td><?=$f->reply;?></td>								
								<td><?=$f->created_at;?></td>								
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/complaint/complaint.blade.php ENDPATH**/ ?>