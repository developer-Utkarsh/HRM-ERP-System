
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Details</li>
							</ol>
						</div>
					</div> 
				</div>
			</div>
			<div class="content-header-left col-md-3 col-12 mb-2 text-right">
				<a class="btn btn-primary" href="<?php echo e(route('admin.course.index')); ?>">Back</a>
			</div>
		</div>
		<div class="content-body">
			<div class="accordion" id="accordionExample">
				<div class="">
					<div class="card-header" id="headingOne">
						<h2 class="mb-0">
							<button class="btn btn-link btn-block text-left p-0" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								Default Planner
							</button>
						</h2>
					</div>

					<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
						<div class="card-body px-0">
							<!-- Data list view starts -->
							<section id="data-list-view" class="data-list-view-header">				 
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th>S. No.</th>
												<th>Course Name</th>
												<th>Subject Name</th>
												<th>Chapter Name</th>
												<th>Topic Name</th>
												<th>Duration</th>
											</tr>
										</thead>
										<tbody>
										
											<?php $__currentLoopData = $send_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<tr>
												<td><?php echo e($value['s_no']); ?></td>
												<td class="product-category"><?php echo e($value['course_name']); ?></td>
												<td class="product-category"><?php echo e($value['subject_name']); ?></td>
												<td class="product-category"><?php echo e($value['chapter_name']); ?></td>
												<td class="product-category"><?php echo e($value['topic_name']); ?></td>
												<td class="product-category"><?php echo e($value['duration']); ?></td>
												
											 
											</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>							
										</tbody>
									</table>
								</div>                   
							</section>
						</div>
					</div>
				</div>
				
				<?php foreach($course_planner as $cp){ ?>
				<div class="mt-1">
					<div class="card-header" id="headingThree">
						<h2 class="mb-0">
							<a href="<?php echo e(route('admin.courses.planner_view',[$cp->id,$course_id])); ?>">
								<button class="btn btn-link btn-block text-left collapsed p-0" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
									<?php echo e($cp->planner_name); ?>

								</button>
							</a>
						</h2>
					</div>
				</div>
				<?php } ?>
			</div>
		
			
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/course/view.blade.php ENDPATH**/ ?>