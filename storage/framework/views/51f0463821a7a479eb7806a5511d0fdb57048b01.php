
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
		</div>
		<div class="content-body">
			<!-- Dashboard Analytics Start -->
			<section id="dashboard-analytics">
				<!--div class="row">
					<div class="col-lg-6 col-md-12 col-sm-12">
						<div class="card bg-analytics text-white">
							<div class="card-content">
								<div class="card-body text-center">                                        
									<div class="avatar avatar-xl bg-primary shadow mt-0">
										<div class="avatar-content">
											<i class="feather icon-award white font-large-1"></i>
										</div>
									</div>
									<div class="text-center">
										<h1 class="mb-2 text-white">Welcome <?php echo e(Auth::user()->name); ?>,</h1>
										<p class="m-auto w-75">You have done <strong>57.6%</strong> more sales today. Check your new badge in your profile.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex flex-column align-items-start pb-0">
								<div class="avatar bg-rgba-primary p-50 m-0">
									<div class="avatar-content">
										<i class="feather icon-users text-primary font-medium-5"></i>
									</div>
								</div>
								<h2 class="text-bold-700 mt-1 mb-25">92.6k</h2>
								<p class="mb-0">Subscribers Gained</p>
							</div>
							<div class="card-content">
								<div id="subscribe-gain-chart"></div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex flex-column align-items-start pb-0">
								<div class="avatar bg-rgba-warning p-50 m-0">
									<div class="avatar-content">
										<i class="feather icon-package text-warning font-medium-5"></i>
									</div>
								</div>
								<h2 class="text-bold-700 mt-1 mb-25">97.5K</h2>
								<p class="mb-0">Orders Received</p>
							</div>
							<div class="card-content">
								<div id="orders-received-chart"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="row pb-50">
										<div class="col-lg-6 col-12 d-flex justify-content-between flex-column order-lg-1 order-2 mt-lg-0 mt-2">
											<div>
												<h2 class="text-bold-700 mb-25">2.7K</h2>
												<p class="text-bold-500 mb-75">Avg Sessions</p>
												<h5 class="font-medium-2">
													<span class="text-success">+5.2% </span>
													<span>vs last 7 days</span>
												</h5>
											</div>                                              
										</div>
										<div class="col-lg-6 col-12 d-flex justify-content-between flex-column text-right order-lg-2 order-1">
											<div class="dropdown chart-dropdown">
												<button class="btn btn-sm border-0 dropdown-toggle p-0" type="button" id="dropdownItem5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Last 7 Days
												</button>
												<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem5">
													<a class="dropdown-item" href="#">Last 28 Days</a>
													<a class="dropdown-item" href="#">Last Month</a>
													<a class="dropdown-item" href="#">Last Year</a>
												</div>
											</div>
											<div id="avg-session-chart"></div>
										</div>
									</div>
									<hr />
									<div class="row avg-sessions pt-50">
										<div class="col-6">
											<p class="mb-0">Goal: $100000</p>
											<div class="progress progress-bar-primary mt-25">
												<div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="50" aria-valuemax="100" style="width:50%"></div>
											</div>
										</div>
										<div class="col-6">
											<p class="mb-0">Users: 100K</p>
											<div class="progress progress-bar-warning mt-25">
												<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="60" aria-valuemax="100" style="width:60%"></div>
											</div>
										</div>
										<div class="col-6">
											<p class="mb-0">Retention: 90%</p>
											<div class="progress progress-bar-danger mt-25">
												<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="70" aria-valuemax="100" style="width:70%"></div>
											</div>
										</div>
										<div class="col-6">
											<p class="mb-0">Duration: 1yr</p>
											<div class="progress progress-bar-success mt-25">
												<div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="90" aria-valuemax="100" style="width:90%"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between pb-0">
								<h4 class="card-title">Support Tracker</h4>
								<div class="dropdown chart-dropdown">
									<button class="btn btn-sm border-0 dropdown-toggle p-0" type="button" id="dropdownItem4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Last 7 Days
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem4">
										<a class="dropdown-item" href="#">Last 28 Days</a>
										<a class="dropdown-item" href="#">Last Month</a>
										<a class="dropdown-item" href="#">Last Year</a>
									</div>
								</div>
							</div>
							<div class="card-content">
								<div class="card-body pt-0">
									<div class="row">
										<div class="col-sm-2 col-12 d-flex flex-column flex-wrap text-center">
											<h1 class="font-large-2 text-bold-700 mt-2 mb-0">163</h1>
											<small>Tickets</small>
										</div>
										<div class="col-sm-10 col-12 d-flex justify-content-center">
											<div id="support-tracker-chart"></div>
										</div>
									</div>
									<div class="chart-info d-flex justify-content-between">
										<div class="text-center">
											<p class="mb-50">New Tickets</p>
											<span class="font-large-1">29</span>
										</div>
										<div class="text-center">
											<p class="mb-50">Open Tickets</p>
											<span class="font-large-1">63</span>
										</div>
										<div class="text-center">
											<p class="mb-50">Response Time</p>
											<span class="font-large-1">1d</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div-->
				
				<div class="row">
					<div class="col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between pb-0 mb-2">
								<h4 class="card-title">Total Employees</h4>
							</div>
							<div class="card-content">
								<div class="card-body pt-0">
									<div class="chart-info d-flex justify-content-between">
										<div class="text-center">
											<p class="mb-50">Active</p>
											<a href="<?php echo e(url('admin/employees?status=Active')); ?>"><span class="font-large-1"><?php echo e($active); ?></span></a>
										</div>
										<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
										<div class="text-center">
											<p class="mb-50">Inactive</p>
											<a href="<?php echo e(url('admin/employees?status=Inactive')); ?>"><span class="font-large-1"><?php echo e($inactive); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">Total</p>
											<a href="<?php echo e(route('admin.employees.index')); ?>"><span class="font-large-1"><?php echo e($total); ?></span></a>
										</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between pb-0 mb-2">
								<h4 class="card-title">Today Attendance</h4>
							</div>
							<?php $app = 0; $rfid = 0; ?>
							<?php if(count($comman_result) > 0): ?>
								<?php $__currentLoopData = $comman_result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comman_result_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
										if($comman_result_value->table_name == 'RFID'){ 
											$rfid++;
										}
										if($comman_result_value->table_name == 'App'){
											$app++;
										}
									?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						

							<?php if(Auth::user()->role_id == 21): ?>
							<?php 
								if(!empty($comman_result)){
									foreach($comman_result as $comman_result_value){
							?>		
								<input type="hidden" name="totalPresent" value="<?php echo e($comman_result_value->id); ?>" class="totalPresent"/>
							<?php
									}
								}								
							?>
							
							<?php 
								if(!empty($employees)){
									foreach($employees as $key=>$value){
							?>		
								<input type="hidden" name="totalAbesent" value="<?php echo e($value->id); ?>" class="totalAbesent"/>
							<?php
									}
								}								
							?>
							<?php endif; ?>

							<div class="card-content">
								<div class="card-body pt-0">
									<div class="chart-info d-flex justify-content-between">
										<div class="text-center">
											<p class="mb-50">Total Present</p>
											<a href="<?php echo e(route('admin.attendance.fullattendence')); ?>"><span class="font-large-1"><?php echo e(count($comman_result)); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">APP Present</p>
											<a href="<?php echo e(route('admin.attendance.index')); ?>"><span class="font-large-1"><?php echo e($app); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">RFID Present</p>
											<a href="<?php echo e(route('admin.attendance.rpattendance')); ?>"><span class="font-large-1"><?php echo e($rfid); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">Total Absent</p>
											<a href="<?php echo e(route('admin.attendance.absentfullattendence')); ?>">
											<span class="font-large-1"><?php echo e($active-count($comman_result)); ?></span>
											<!--span class="font-large-1"><?php echo e($total_users-count($comman_result)); ?></span-->
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between pb-0 mb-2">
								<h4 class="card-title">Total Leave</h4>
							</div>
							<?php
								$total_leave        = 0;
								$total_approved     = 0;
								$total_not_approved = 0;
							?>
							<?php if(count($leave_data) > 0): ?> 
								<?php $total_leave = count($leave_data); ?>
								<?php $__currentLoopData = $leave_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave_data_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php $__currentLoopData = $leave_data_value->leave_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave_details_data_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if($leave_details_data_value->status == "Approved"): ?>
										<?php $total_approved += 1; ?>
										<?php elseif($leave_details_data_value->status == "Pending"): ?>
										<?php $total_not_approved += 1; ?>
										<?php endif; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>        
							<div class="card-content">
								<div class="card-body pt-0">
									<div class="chart-info d-flex justify-content-between">
										<div class="text-center">
											<p class="mb-50">Today's Approved</p>
											<a href="<?php echo e(url('admin/leave?status=Approved')); ?>"><span class="font-large-1"><?php echo e($total_approved); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">Today's Pending</p>
											<a href="<?php echo e(url('admin/leave?status=Pending')); ?>"><span class="font-large-1"><?php echo e($total_not_approved); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">Today's Total</p>
											<a href="<?php echo e(route('admin.leave.index')); ?>"><span class="font-large-1"><?php echo e($total_leave); ?></span></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex flex-column align-items-start pb-0">
								<div class="p-50 m-0">
									<h4 class="card-title">Time Shift</h4>
								</div>
								<div class="row" style="width: 100%;">
									<div class="col-md-7">
										<div class="p-50 m-0">
											<p class="mt-1 mb-25">Punch Late</p>
										</div>
									</div>
									<div class="col-md-5">
									<div class="p-50 mt-1">
										<?php if($time_shift_count): ?>
										<a href="<?php echo e(route('admin.employee.late-employee-list')); ?>" class="text-bold-700 mt-1 mb-25"><?php echo e($time_shift_count); ?></a>
										<?php else: ?>
										<a href="" class="text-bold-700 mt-1 mb-25"><?php echo e($time_shift_count); ?></a>
										<?php endif; ?>
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-6 col-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between pb-0 mb-2">
								<h4 class="card-title">Total Wishes</h4>
							</div>      
							<div class="card-content">
								<div class="card-body pt-0">
									<div class="chart-info d-flex justify-content-between">
										<div class="text-center">
											<p class="mb-50">Staff B'day</p>
											<a href="<?php echo e(route('admin.employees.birthday')); ?>"><span class="font-large-1"><?php echo e($b_day); ?></span></a>
										</div>
										<div class="text-center">
											<p class="mb-50">Work Anniversary</p>
											<a href="<?php echo e(route('admin.employees.work-anniversary')); ?>"><span class="font-large-1"><?php echo e($work_anniversary); ?></span></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</section>
			<!-- Dashboard Analytics end -->
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
			data.totalPresent   = $('.totalPresent').val(),
			data.totalAbesent 	= $('.totalAbesent').val(), 
			window.location.href = "<?php echo URL::to('/admin/'); ?>/today-attendance-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/index.blade.php ENDPATH**/ ?>