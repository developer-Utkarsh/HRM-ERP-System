
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Chapters</h2>
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
			<div class="content-header-left col-md-6 col-12 mb-2 text-right">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<a href="<?php echo e(route('studiomanager.chapters.create')); ?>" class="btn btn-primary">
							Add Chapter
						</a> &nbsp;&nbsp;&nbsp;
						<a href="<?php echo e(route('studiomanager.chapter.import')); ?>" class="btn btn-primary mr-1">Import Chapter</a>
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
								<form action="<?php echo e(route('studiomanager.chapters.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="course_id">
													<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('course_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Subjects</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="subject_id">
													<?php $subjects = \App\Subject::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('subject_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Chapter</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Chapter" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control" name="status">
													<?php $status = ['Inactive', 'Active']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Course</th>
								<th>Subject</th>
								<th>Chapter</th>
								<th>Duration</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e(isset($value->course->name) ?  $value->course->name : ''); ?></td>
								<td class="product-category"><?php echo e(isset($value->subject->name) ?  $value->subject->name : ''); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category">
									<?php 
										$total_duration = DB::table('topic')->select(DB::raw("sum(duration) as duration"))->where('chapter_id',$value->id)->get();
										echo round($total_duration[0]->duration / 60,2)." Hr";
									?>
								</td>
								<!--td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td-->
								<td>
									
									<a href="<?php echo e(route('studiomanager.chapter.status', $value->id)); ?>">
										<strong class="fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
									</a>
								</td>
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
								<td class="product-action">
									<a href="<?php echo e(route('studiomanager.chapters.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('studiomanager.chapter.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Chapter')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $chapters->appends($params)->links(); ?>

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
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true

		});
		
		$('.select-multiple2').select2({
			placeholder: "- Select -",
			allowClear: true
		});

		$('.select-multiple3').select2({
			placeholder: "- Select -",
			allowClear: true
		});

	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/chapter/index.blade.php ENDPATH**/ ?>