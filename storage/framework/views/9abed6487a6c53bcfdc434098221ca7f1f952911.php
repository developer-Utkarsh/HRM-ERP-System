
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Message to Chairman</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6">
						<div class="float-right">
						   <i class="filterIcon fa fa-filter text-primary" style="font-size:30px;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php echo $__env->make('admin.support_category.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="row">
					<?php 
					 $totalStatus=[];
					 $totalStatus[]=["id"=>0,"name"=>'pending','total'=>0,"className"=>'text-danger'];
					 $totalStatus[]=["id"=>0,"name"=>'replied','total'=>0,"className"=>'text-primary'];
					 $totalStatus[]=["id"=>0,"name"=>'resolved','total'=>0,"className"=>'text-success'];
					 $totalStatus[]=["id"=>0,"name"=>'reopen','total'=>0,"className"=>'text-warning'];
					?>

					<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					    <?php if(count($val->ticket_count)): ?>
						<div class="col-xl-4 col-md-6 mb-1">
							<div class="card" style="margin-bottom:0px">
								<div class="card-body">
									<div class="row">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-black text-uppercase"><?php echo e($val->name); ?></div>
										</div>
									</div>
									<?php $total=0;?>
									<div class="row mt-2">
										<?php
										 $tkStatus=[];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'pending','total'=>0,"className"=>'text-danger'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'replied','total'=>0,"className"=>'text-primary'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'resolved','total'=>0,"className"=>'text-success'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'reopen','total'=>0,"className"=>'text-warning'];
									    ?>
										
										<?php $__currentLoopData = $val->ticket_count; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										    <?php 
										     $key = array_search($tval->status, array_column($tkStatus, 'name'));
                                             $tkStatus[$key]['total']=$tval->total;

                                             $index= array_search($tval->status, array_column($totalStatus, 'name'));
                                             $totalStatus[$index]['total']=$totalStatus[$index]['total']+$tval->total;
										    ?>
										    
									        <?php $total+=$tval->total;?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

										<?php $__currentLoopData = $tkStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										    <div class="col mr-1">
											  <a href="<?php echo e(route('admin.support-enquiry')); ?>?search=search&category_id=<?php echo e($tval['id']); ?>&status=<?php echo e($tval['name']); ?>">
											  	<div class="<?php echo e($tval['className']); ?>">
											  	 <?php echo e(ucwords($tval['name'])); ?>

											  </div></a>
											  <div>
											  	<?php echo e($tval['total']); ?>

											  </div><hr>
			                               </div>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									   <div class="col mr-1">
										  <a href="<?php echo e(route('admin.support-enquiry')); ?>?search=search&category_id=<?php echo e($val->id); ?>">
										  	<div class="text-danger">
										  	 Total
										  </div></a>
										  <div>
										  	<?php echo e($total); ?>

										  </div><hr>
		                               </div>
									</div>
								</div>
							</div>
						</div>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <div class="col-xl-4 col-md-6 mb-1">
						<div class="card" style="margin-bottom:0px">
							<div class="card-body" style="background:#b8c2cc;">
								<div class="row">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-black text-uppercase">Total </div>
									</div>
								</div>

								<div class="row mt-2">
									<?php $__currentLoopData = $totalStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									    <div class="col mr-1">
										  <a href="<?php echo e(route('admin.support-enquiry')); ?>?search=search&category_id=<?php echo e($tval['id']); ?>&status=<?php echo e($tval['name']); ?>">
										  	<div class="<?php echo e($tval['className']); ?>">
										  	 <?php echo e(ucwords($tval['name'])); ?>

										  </div></a>
										  <div>
										  	<?php echo e($tval['total']); ?>

										  </div><hr>
				                       </div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				                </div>
				            </div>
				        </div>
				    </div>

				</div>
			</section>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/support_category/dashboard.blade.php ENDPATH**/ ?>