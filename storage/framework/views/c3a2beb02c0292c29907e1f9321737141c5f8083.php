<?php
namespace App\Http\Controllers\StudioManager;
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
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<!--a href="<?php echo e(route('studiomanager.courses.import')); ?>" class="btn btn-primary mr-1">Import</a-->
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
								<form action="<?php echo e(route('studiomanager.course.index')); ?>" method="get" name="filtersubmit">
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
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('type')){ echo "selected"; } elseif(empty(app('request')->input('type')) && $value=="offline"){ echo "selected"; } ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('studiomanager.course.index')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="row m-2">
					<div class="col-md-12">
						<a class="float-right" href="<?php echo e(asset('laravel/public/CourseBySubjectData.xlsx')); ?>"><span>Sample Import File</span></a>
					</div>				
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Course</th>
								<th>Total Duration</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
								<th>Import</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php
							$course_id = $value->id;
							$topic=DB::table("topic")
							->select(DB::RAW('sum(duration) as total_time'))
							->where("course_id",$value->id)
							->where('status', 1)
							->first();
							$duration="00:00 h";
							if(!empty($topic)){
							  $duration=round($topic->total_time/60,2);
							}
							?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category"><?php echo e($duration); ?></td>
								<!--td><?php if($value->status == 1): ?> Active <?php else: ?> Inactive <?php endif; ?></td-->
								<td>
									
									<a href="<?php echo e(route('studiomanager.course.status', $value->id)); ?>">
										<strong class="fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish"></strong>
									</a>
								</td>
								<td><?php echo e(date('Y-m-d',strtotime($value->created_at))); ?></td>
								<td class="product-action">
									<a href="<?php echo e(route('studiomanager.course.view', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-eye"></i></span>
									</a>
									<a href="<?php echo e(route('studiomanager.course.edit', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('studiomanager.course.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Course')" class="d-none">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
								<td>
									<a href="<?php echo e(route('studiomanager.course.export_csv', $value->id)); ?>" class="px-2">
										<span class="action-edit"><i class="feather icon-download"></i></span>
									</a>

									<a href="javascript:void(0);" data-toggle="modal" data-course-id="<?php echo e($value->id); ?>" class="btn btn-sm btn-primary import_data"><span class="action-edit"><i class="feather icon-upload"></i></span></a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>							
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


<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-l">
		<div class="modal-content">
			<form method="post" id="submit_import_file">
				<div class="modal-header">
					<h5 class="modal-title">Import</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class='col-md-12 col-12 mb-4'>
							 <ul style="color: red;">
							 	<li>Import Only One Subject Course Planner one time.</li>
							 	<li>Chapter must be proper Name (Not Use Part -1 or 2 in chapter name)</li>
							 	<li>Topic name must be proper and Use Part-1 or 2 to at strating of topic.</li>
							 	<li>Hindi Subject Topic Name Only in Hindi and No need to add Pipe Symbol and duplicate content.</li>
							 </ul>	
							</div>
						
							<div class='col-md-12 col-12'>	
								<div class='form-label-group'>	
									<input type="file" class="form-control" name="import_file">
								</div>
							</div>
							
						</div>
					</div>
				</div>
				<input type="hidden" name="course_id" class="course_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="import_btn" class="btn btn-primary dsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
				</div>
			</form>	
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	$(document).ready(function() {
		$('#example').DataTable();
	});

	$(function() {
		$(".import_data").on("click", function() {
			var course_id = $(this).attr("data-course-id");
			if(course_id){
				var studioname = $(this).attr("studioname");
				$(".course_id").val(course_id);
				$('#myModal').modal({
						backdrop: 'static',
						keyboard: true, 
						show: true
				});
			}else{
				alert('Course ID Not Found.');
			}
			
		});     
	});
	
	var $form = $('#submit_import_file');
	validatorprice = $form.validate({
		ignore: [],
		rules: {
			'import_file' : {
				required: true,                
			},       
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});

	$("#submit_import_file").submit(function(e) {
		var form = document.getElementById('submit_import_file');
		var dataForm = new FormData(form); 
		e.preventDefault();
		if(validatorprice.valid()){
			$('#import_btn').attr('disabled', 'disabled');
			$.ajax({
				beforeSend: function(){
					$("#import_btn i").show();
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('studiomanager.import-chapter')); ?>',
				data : dataForm,
				processData : false, 
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
						$('#import_btn').removeAttr('disabled');
						$("#import_btn i").hide();
					} else if(data.status == true){
						$('#submit_timetable_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
						$('#import_btn').removeAttr('disabled');
						$("#import_btn i").hide();
					}
				}
			});
		}       
	});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/course/index.blade.php ENDPATH**/ ?>