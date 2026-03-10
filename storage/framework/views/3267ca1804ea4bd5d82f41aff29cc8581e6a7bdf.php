
<?php $__env->startSection('content'); ?>
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Software Management System</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        <?php if(Auth::user()->id == 901): ?>
                        <div class="col-4 text-right">
                            <a href="<?php echo e(route('software-management.create')); ?>" class="btn btn-outline-primary mr-1">Add Software</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="content-body">
            <section id="multiple-column-form" style="display: none;">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('coupon.index')); ?>" method="get" name="filtersubmit">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="number" class="form-control" placeholder="Enter Mobile number to search..." value="<?php echo e(request('mobile')); ?>" id="mobile" name="mobile">
														<?php if($errors->has('mobile')): ?>
														<span class="text-danger"><?php echo e($errors->first('mobile')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		                                
												<div class="col-md-8">
													<fieldset class="form-group">		
														<a href="<?php echo e(route('coupon.index')); ?>" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
                <section id="data-list-view" class="data-list-view-header">

                    <div class="table-responsive">
                        <table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th >Software Name</th>
                                    <th >Type</th>
                                    <th >Description</th>
                                    <th >Software Owner</th>
                                    <th >Status</th>
                                    <th >Date</th>
                                    <th >Action</th>
                                </tr>
                            </thead>
                           <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=> $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($item->name); ?></td>
                                    <td><?php echo e($item->soft_type ?? '-'); ?></td>
                                    <td><?php echo e($item->description ?? '-'); ?></td>
                                    <td><?php echo e($item->owner_name); ?> <span class="text-primary font-weight-bold">(<?php echo e($item->owner_register_id); ?>)</span></td>
                                    <td><?php echo e($item->status); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($item->created)->format('d-m-Y')); ?></td>
                                    <td><a href="<?php echo e(route('software-management.edit', $item->id)); ?>" class="btn btn-sm btn-primary">Edit</a></td>

                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-center" colspan="8">No Software Found</td>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/software-management/index.blade.php ENDPATH**/ ?>