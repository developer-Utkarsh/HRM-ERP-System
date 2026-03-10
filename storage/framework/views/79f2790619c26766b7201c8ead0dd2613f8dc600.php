<?php
namespace App\Http\Controllers\Admin;
use DB;
?>

<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<!--a href="<?php echo e(route('admin.courses.import')); ?>" class="btn btn-primary mr-1">Import</a-->
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
								<form action="<?php echo e(route('admin.course.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple" name="status">
													<?php $status = ['Inactive', 'Active']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="type">
													<?php $type = ['online', 'offline']; ?>
													<option value="all">All</option>
													<?php $__currentLoopData = $type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<!--option value="<?php echo e($value); ?>" <?php //if($value == app('request')->input('type')){ echo "selected"; } elseif(empty(app('request')->input('type')) && $value=="offline"){ echo "selected"; } ?>><?php echo e($value); ?></option-->
													
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('type')){ echo "selected"; } ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.course.index')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
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
								<th>Course</th>
								<th>Total Duration</th>
								<th>Type</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($courses) > 0): ?>
								<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
								// print_r($value->id); die;
								$duration = 0;
								$course_id = $value->id;
								$relation_subjects = DB::table('course_subject_relations')
									->select('*')
									->where('course_id', $course_id)
									->get();
								if(!empty($relation_subjects)){
									foreach ($relation_subjects as $details) 
									{
										if(!empty($details)){
											$subjects = DB::table('subject')
												->select('*')
												->where('id', $details->subject_id)
												->where('status', 1)
												->first();
											if(!empty($subjects)){
												$subject_id = $subjects->id;
												$chapter = DB::table('chapter')
													->select('*')
													->where('course_id', $course_id)
													->where('subject_id', $subject_id)
													->where('status', 1)
													->get();
												if(!empty($chapter)){
													foreach ($chapter as $Cdetails){
														$chapter_id = $Cdetails->id;
														$topic = DB::table('topic')
															->select('*')
															->where('course_id', $course_id)
															->where('subject_id', $subject_id)
															->where('chapter_id', $chapter_id)
															->where('status', 1)
															->get();
														if(!empty($topic)){
															foreach ($topic as $Tdetails){
																$duration += $Tdetails->duration;
															}
														}
													}
												}
											}
										}
										
									}
								}
								
								$duration = intdiv($duration, 60).'h : '. ($duration % 60)."m";
								?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td class="product-category"><?php echo e($value->name); ?></td>
									<td class="product-category"><?php echo e($duration); ?></td>
									<td class="product-category"><?php echo e($value->type); ?></td>
									<!--td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td-->
									<td>
										
										<a href="<?php echo e(route('admin.course.status', $value->id)); ?>">
											<strong class="fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
										</a>
									</td>
									<td><?php echo e(date('d-m-Y',strtotime($value->created_at))); ?></td>
									<td class="product-action">
										<a href="<?php echo e(route('admin.course.export_csv', $value->id)); ?>">
											<span class="action-edit"><i class="feather icon-download"></i></span>
										</a>
										<a href="<?php echo e(route('admin.course.view', $value->id)); ?>">
											<span class="action-edit"><i class="feather icon-eye"></i></span>
										</a>
										<a href="<?php echo e(route('admin.course.edit', $value->id)); ?>">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<a href="<?php echo e(route('admin.course.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Course')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
										
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center"  colspan="10">No Data Found</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $courses->appends($params)->links(); ?>

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
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	$(document).ready(function() {
		$('#example').DataTable();
	});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/course/index.blade.php ENDPATH**/ ?>