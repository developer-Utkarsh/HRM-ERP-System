
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Inventory</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.student-get-inventory')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Registration Number</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="reg_no" placeholder="Search" value="<?php echo e(app('request')->input('reg_no')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.student-get-inventory')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div> 
									</div>
								</form>
							</div>
							<div>
								<?php if(!empty($student->s_name)){ ?><div><b>Student Name : </b> <?php echo e($student->s_name); ?></div><?php } ?>
								<?php if(!empty($student->batch_id)){ ?><div><b>Batch Code : </b><?php echo e($student->batch_id); ?></div><?php } ?>
								<?php if(!empty($student->batch)){ ?><div><b>Batch Name : </b><?php echo e($student->batch); ?></div><?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Inventory Name</th>
								<th>Type</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 					
								if(!empty($student)){
									$assign_inventory = explode(',',$student->assign_inventory);
									// $get_inventory = DB::table("batch_inventory")->where('batch_code',$student->batch_id)->where('status',1)->orWhere('batch_code',0)->groupby('name','inventory_type')->get();
									
									$get_inventory = DB::table('batch_inventory')
										->where('status', 1)
										->where(function ($query) use ($student) {
											$query->where('batch_code', $student->batch_id)
												  ->orWhere('batch_code', 0);
										})
										->groupBy('name', 'inventory_type')
										->get();

									if(count($get_inventory) > 0){												
										$i = 1;
										foreach($get_inventory as $gi){
							?>
							<tr>
								<td><?=$i;?></td>
								<td><?php echo e($gi->name); ?></td>
								<td><?php echo e($gi->inventory_type); ?></td>
								<td>
									<?php 
										if(in_array($gi->id,$assign_inventory)){
											echo 'Given';
										}else{
									?>
										<a href="<?php echo e(route('admin.student-assign-inventory',[$student->reg_number,$gi->id])); ?>">Pending</a>
									<?php } ?>
								</td>
							</tr>	
							<?php 		$i++; }
									}else{
							?>
							<tr>
								<td colspan="5" class="text-center">No Inventory Found</td>
							</tr>							
							<?php 
									}
								}else{ 
							?>
							<tr>
								<td colspan="5" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>							
						</tbody>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$('.select-multiple1').select2({
		placeholder: "Select Batch",
		allowClear: true
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/student-get-inventory.blade.php ENDPATH**/ ?>