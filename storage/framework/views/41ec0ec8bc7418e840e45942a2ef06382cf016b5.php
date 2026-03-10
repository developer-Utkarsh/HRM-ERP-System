
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Batch</h2>
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
								<form action="<?php echo e(route('studiomanager.batch.index')); ?>" method="get" name="filtersubmit">
									<div class="row">

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<fieldset class="form-group">												
												<select class="form-control" name="branch">
													<?php $branch = \App\Batch::select('branch')->where('status', '1')->groupby('branch')->get(); ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													  <option value="<?php echo e($val->branch); ?>" <?php if($val->branch == app('request')->input('branch')): ?> selected="selected" <?php endif; ?>><?php echo e($val->branch); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Batch</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Batch" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="course_id">
													<?php $courses = \App\Course::where('status', '1')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('course_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="status">
													<?php $status = ['Inactive', 'Active','Completed']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="type">
													<?php $type = ['online', 'offline','Live From Classroom']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('type')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass">
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Chanakya Assigned</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="is_chanakya">
													<?php $chanakya = ['Yes', 'No']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $chanakya; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($cvalue); ?>" <?php if($cvalue == app('request')->input('is_chanakya')): ?> selected="selected" <?php endif; ?>><?php echo e($cvalue); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course Planner</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="cPlanner">
													<option value="">Select Any</option>
													<option value="1"  <?php if('1' == app('request')->input('cPlanner')): ?> selected="selected" <?php endif; ?>>Yes</option>
													<option value="2" <?php if('2' == app('request')->input('cPlanner')): ?> selected="selected" <?php endif; ?>>No</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Batch Code</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="bcode" placeholder="Batch Code" value="<?php echo e(app('request')->input('bcode')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">ERP ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="erp_code" placeholder="ERP Code" value="<?php echo e(app('request')->input('erp_code')); ?>">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('studiomanager.batch.index')); ?>" class="btn btn-warning">Reset</a>
									</fieldset>
									 
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Branch</th>
								<th>Batch</th>
								<th>Master Planner</th>
								<th>Batch Code</th>
								<th>Course</th>
								<th>Category</th>
								<th>ERP Course ID</th>
								<th>Chankya Name</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>No of hours</th>
								<th>Course Planner</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $no_of_count = 0; ?>
								<?php if(count($value->batch_relations) > 0): ?>
									<?php $__currentLoopData = $value->batch_relations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch_relations_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
										<?php $no_of_count += $batch_relations_val->no_of_hours; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>
								
								<?php 
									$topic=DB::table("topic")
									->select(DB::RAW('sum(duration) as total_time'))
									->where("course_id",$value->course->id??0)
									->where('status',1)
									->first();
									$duration="00:00 h";
									if(!empty($topic)){
									  $duration=round($topic->total_time/60,2);
									}
								?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->branch); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category">
									<?php 
										if($value->master_planner!=0){ 
											echo $value->planner_name.'-'.$value->master_planner; 
										}else{
											echo '-';
										} 
									?>	
								</td>
								<td class="product-category" style="word-wrap:break-word;"><?php echo e($value->batch_code); ?></td>
								<td class="product-category"><?php echo e(isset($value->course->name) ? $value->course->name : ''); ?></td>	
								<td class="product-category"><?php echo e(isset($value->category) ? $value->category : ''); ?></td>	
								<td class="product-category"><?php echo e($value->erp_course_id); ?></td>	
								<td class="product-category"><?php echo e(isset($value->mentor->name) ? $value->mentor->name : ''); ?></td>
								<td class="product-category"><?php echo e(date('d-m-Y', strtotime($value->start_date))); ?></td>
								<td class="product-category">
									<?php if($value->end_date=="1970-01-01"){ echo '-'; }else{ echo date('d-m-Y', strtotime($value->end_date)); } ?>
								</td>
								<td class="product-category"><?php echo e($duration); ?></td>
								<td class="product-category"><?php if($value->course_planer_enable == 1): ?> Yes <?php else: ?> No <?php endif; ?></td>
								<td class="product-category"><?php if($value->status == 1): ?> Active <?php elseif($value->status == 2): ?> Completed <?php else: ?> Inactive <?php endif; ?></td>								
								<td class="product-action">
									<a href="<?php echo e(route('studiomanager.batch.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('studiomanager.batch.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Batch')" class="d-none">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									<a href="<?php echo e(route('studiomanager.batch.view', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-eye"></i></span>
									</a>

									<a href="https://courses.utkarsh.com/offline-student-timetable/home.php?batch_name=<?php echo e(urlencode($value->name)); ?>&batch_code=<?php echo e($value->batch_code); ?>" traget="_blank">
										<span class="action-edit"><i class="feather icon-clock"></i></span>
									</a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
					
					<div class="d-flex justify-content-center">					
					 <?php echo $batches->appends($params)->links(); ?>

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
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});			
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/batch/index.blade.php ENDPATH**/ ?>