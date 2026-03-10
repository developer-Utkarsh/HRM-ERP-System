
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Assigned Incharge</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
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
								<form action="<?php echo e(route('admin.assigned-incharge')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="<?php echo e(app('request')->input('search')); ?>" id="myInputSearch">
											</fieldset>
										</div>
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="branch_id" id="">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
									</div>
									
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.assigned-incharge')); ?>" class="btn btn-warning">Reset</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<table class="table data-list-view">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Employee Name</th>
							<th>Branch</th>
							<th>Designation</th>
							<th>Action</th>
							
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($production_employee) > 0) { 
					foreach ($production_employee as $employees) { //echo '<pre>'; print_r($FacultyArray); die;
								
						?>
							<tr class="trRaw">
							<td style="width: 10%;"><?=$dataFound?></td>
							<td style="width: 20%;"><?php echo $employees->name; ?> (<?php echo $employees->register_id; ?>)
							</td>
							<td style="width: 30%;">
							<input type="hidden" class="user_id" value="<?=$employees->id?>" />
							<select class="form-control branch_id select-multiple4" >
								<option value="">Select</option>
								<?php
									foreach($userLocationBranchesName as $key=>$branches){
									?>
										<option value="<?=$key?>" <?=($key==$employees->branch_id)?'selected':'';?>> <?=$branches?></option>
									<?php
									}
								?>
							</select>
							</td>
							<td style="width: 30%;">
							<select class="form-control designation_name select-multiple3" >
								<option value="">Select</option>
								<?php
									foreach($degination_name as $key=>$deginations){
									?>
										<option value="<?=$deginations?>" <?=($deginations==$employees->degination)?'selected':'';?>> <?=$deginations?></option>
									<?php
									}
								?>
							</select>
							</td>
							<td style="width: 10%;">
								<button name="" class="change_incharge btn btn-success" value="" >Submit </button>
							</td>
							
							
							
							
							</tr>
						 
						<?php 
						$dataFound++; 
					} 
				}
				?>
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
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple3').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple4').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});


$(".change_incharge").on("click",function(e) {
		e.preventDefault();
		/* if (!confirm("Do you want change.")){
		  return false;
		} */
		var user_id = $(this).parents('.trRaw').find('.user_id').val();
		var branch_id = $(this).parents('.trRaw').find('.branch_id').val();
		var designation_name = $(this).parents('.trRaw').find('.designation_name').val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.assigned_incharge_update')); ?>',
			data : {'user_id':user_id,'branch_id':branch_id,'designation_name':designation_name},
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){					
					swal("Done!", data.message, "success").then(function(){ 
						//location.reload();
					});
				}
			}
		});
	});
		
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/assigned_assistants/assigned_incharge.blade.php ENDPATH**/ ?>