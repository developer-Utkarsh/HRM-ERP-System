
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('studiomanager.faculty-topic')); ?>" method="get" name="filtersubmit">										
									<div class="row">											
										<div class="col-12 col-sm-6 col-lg-3">											
											<label for="users-list-status">Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="tdate"  placeholder="Date"  value="<?php echo e(app('request')->input('tdate', now()->format('Y-m-d'))); ?>" class="form-control StartDateClass tdate">
	
											</fieldset>	
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select</option>													
													<option value="jodhpur" <?php if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
													<option value="jaipur" <?php if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
													<option value="delhi" <?php if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
													<option value="prayagraj" <?php if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
													<option value="indore" <?php if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Indore</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Type</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 batch_type" name="batch_type">
													<option value="">Select</option>													
													<option value="online" <?php if(!empty(app('request')->input('batch_type')) && 'online' == app('request')->input('batch_type')): ?> selected="selected" <?php endif; ?>>Online</option>
													<option value="offline" <?php if(!empty(app('request')->input('batch_type')) && 'offline' == app('request')->input('batch_type')): ?> selected="selected" <?php endif; ?>>offline</option>
													<option value="Live From Classroom" <?php if(!empty(app('request')->input('batch_type')) && 'Live From Classroom' == app('request')->input('batch_type')): ?> selected="selected" <?php endif; ?>>Live From Classroom</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Topic</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 topic" name="topic">
													<option value="">Select</option>													
													<option value="Selected" <?php if(!empty(app('request')->input('topic')) && 'Selected' == app('request')->input('topic')): ?> selected="selected" <?php endif; ?>>Selected</option>
													<option value="Not Selected" <?php if(!empty(app('request')->input('topic')) && 'Not Selected' == app('request')->input('topic')): ?> selected="selected" <?php endif; ?>>Not Selected</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-status">&nbsp;</label>
											<fieldset class="form-group">												
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="<?php echo e(route('studiomanager.faculty-topic')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>	
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style=''>
						<head>
							<tr style="">
								<th scope="col">S. No</th>
								<th scope="col">Batch Name</th>
								<th scope="col">Location</th>
								<th scope="col">Faculty</th>
								<th scope="col">From Time</th>
								<th scope="col">To Time</th>
								<th scope="col">Topic (Select/Not Selected)</th>
							</tr>
						</head>
						<body>
							<?php 
								if(count($cards) > 0){
								$i =1; foreach($cards as $ca){ ?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php echo e($ca->batch_name); ?></td>
								<td><?php echo e($ca->location); ?></td>
								<td><?php echo e($ca->faculty_name); ?></td>
								<td><?php echo e($ca->from_time); ?></td>
								<td><?php echo e($ca->to_time); ?></td>
								<td><?php echo $ca->topic_id ? '<b class="text-success">Selected</b>' : '<b class="text-danger">Not Selected</b>'; ?></td>

							</tr> 
							<?php $i++; } 
								}else{
							?>
							<tr>
								<td colspan="4">No Record Found</td>
							</tr>
							<?php } ?>
						</body>					
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
<link href="<?php echo e(asset('laravel/public/css/jquery.timepicker.css')); ?>" rel="stylesheet"/>
<script src="<?php echo e(asset('laravel/public/js/jquery.timepicker.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/faculty_reports/faculty_topic.blade.php ENDPATH**/ ?>