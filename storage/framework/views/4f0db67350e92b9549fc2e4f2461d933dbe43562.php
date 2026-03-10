
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Class Change Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">				
				<div class="card-content">
					<div class="card-body">						
						<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#reschedule" role="tab" aria-controls="home-fill" aria-selected="true">Reschedule Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#swap" role="tab" aria-controls="profile-fill" aria-selected="false">Swap Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#delete" role="tab" aria-controls="messages-fill" aria-selected="false">Delete Request</a>
							</li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content pt-1">
							<div class="tab-pane active" id="reschedule" role="tabpanel" aria-labelledby="home-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												
												<th>To Time</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(count($studios) > 0): ?>
											<?php $__currentLoopData = $studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $studio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($studio->timetable)): ?>
											<?php $__currentLoopData = $studio->timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count => $time_table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($time_table->reschedule)): ?>
											<?php $__currentLoopData = $time_table->reschedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single_reschedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($single_reschedule)): ?>
											<tr>
												
												<td><?php echo e(isset($single_reschedule->to_time) ? $single_reschedule->to_time : ''); ?></td>
												<td><?php echo e(isset($single_reschedule->faculty_reason) ? $single_reschedule->faculty_reason : ''); ?></td>
												<td><?php echo e(isset($single_reschedule->admin_reason) ? $single_reschedule->admin_reason : ''); ?></td>
												<td><?php echo e(isset($single_reschedule->status) ? $single_reschedule->status : ''); ?></td>
												<td>
													<?php if(!empty($single_reschedule->created_at)): ?>
													<?php echo e($single_reschedule->created_at->format('d-m-Y')); ?>

													<?php endif; ?>
												</td>
												<td>
													<a href="<?php echo e(route('studiomanager.reschedule.edit', $single_reschedule->id)); ?>">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="swap" role="tabpanel" aria-labelledby="profile-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												
												<th>Time Table</th>
												<th>Swap Faculty</th>
												<th>Swap Time Table</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(count($studios) > 0): ?>
											<?php $__currentLoopData = $studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $studio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($studio->timetable)): ?>
											<?php $__currentLoopData = $studio->timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count => $time_table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($time_table->swap)): ?>
											<?php $__currentLoopData = $time_table->swap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single_swap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($single_swap)): ?>
											<tr>
												
												<td>
													<?php if(!empty($single_swap->s_timetable)): ?>
													<?php echo e($single_swap->s_timetable->from_time); ?> - <?php echo e($single_swap->s_timetable->to_time); ?>

													
													<?php endif; ?>
												</td>
												<td>
													<?php if(!empty($single_swap->faculty)): ?>
													<?php echo e($single_swap->faculty->name); ?>

													<?php endif; ?>
												</td>
												<td>
													<?php if(!empty($single_swap->swap_timetable)): ?>
													<?php echo e($single_swap->swap_timetable->from_time); ?> - <?php echo e($single_swap->swap_timetable->to_time); ?>

													
													<?php endif; ?>
												</td>
												<td><?php echo e(isset($single_swap->status) ? $single_swap->status : ''); ?></td>
												<td>
													<?php if(!empty($single_swap->created_at)): ?>
													<?php echo e($single_swap->created_at->format('d-m-Y')); ?>

													<?php endif; ?>
												</td>
												<td>
													<a href="<?php echo e(route('studiomanager.swap.edit', $single_swap->id)); ?>">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="delete" role="tabpanel" aria-labelledby="messages-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												
												<th>Days</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(count($studios) > 0): ?>
											<?php $__currentLoopData = $studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $studio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($studio->timetable)): ?>
											<?php $__currentLoopData = $studio->timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count => $time_table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($time_table->cancelclass)): ?>
											<?php $__currentLoopData = $time_table->cancelclass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single_cancelclass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!empty($single_cancelclass)): ?>
											<tr>
												
												<td><?php echo e(isset($single_cancelclass->days) ? $single_cancelclass->days : ''); ?></td>
												<td><?php echo e(isset($single_cancelclass->faculty_reason) ? $single_cancelclass->faculty_reason : ''); ?></td>
												<td><?php echo e(isset($single_cancelclass->admin_reason) ? $single_cancelclass->admin_reason : ''); ?></td>
												<td><?php echo e(isset($single_cancelclass->status) ? $single_cancelclass->status : ''); ?></td>
												<td><?php if(!empty($single_cancelclass->created_at)): ?>
													<?php echo e($single_cancelclass->created_at->format('d-m-Y')); ?>

													<?php endif; ?>
												</td>
												<td>
													<a href="<?php echo e(route('studiomanager.cancelclass.edit', $single_cancelclass->id)); ?>">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</tbody>
									</table>
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

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/classchangerequest/index.blade.php ENDPATH**/ ?>