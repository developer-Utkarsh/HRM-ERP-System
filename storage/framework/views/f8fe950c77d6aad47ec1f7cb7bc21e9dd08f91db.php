
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Inventory Dashboard</h2>
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
								<form action="<?php echo e(route('admin.inventory-dashboard')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<?php $branch_location = ['Jodhpur', 'Jaipur','Prayagraj','Indore','Delhi']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $branch_location; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php
											$branch_location = app('request')->input('branch_location');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('branch_location', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_id" name="branch_id" onchange="locationBatch(this.value);">
													<option value="">Select Any</option>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
											<input type="hidden" name="allbatch_ids" class="allbatch_ids"/>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch</label>
											<?php
											$ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
											$batch = DB::table('batch')
												->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
												->leftjoin('timetables as tt','tt.batch_id','batch.id')
												->where('batch.status', '1')
												->where('tt.is_deleted', '0')
												->where('tt.is_publish', '1')
												->where('tt.is_cancel', 0)
												->where('tt.cdate','>',$ttdate)
												->where('batch.batch_code','!=',0);
												if(!empty(app('request')->input('branch_id'))){
													$batch->where('tt.branch_id',app('request')->input('branch_id'));
												}
											$batch= $batch->groupby('batch.batch_code')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple batch_id" name="batch_id[]">
													<option value="">Select Any</option>
													<?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->batch_code); ?>" <?php if(in_array($value->batch_code,$batch_id)): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.inventory-dashboard')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
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
								<th>Inventory Name</th>
								<th>Quantity</th>
								<!--<th>Stock</th>-->
								<th>Total Student</th>
								<th>Given Count</th>
								<th>Pending</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($inventory) > 0){
									$total = DB::connection('mysql2')->table("tbl_registration")->where("batch_id", $batch_id)->count();
									
									$pending	=	0;
									$stock	=	0;
									$i = 1;
									foreach($inventory as $key => $value){
										$query = DB::connection('mysql2')->table("tbl_registration")->select(DB::raw('count(assign_inventory) as given'))->where("batch_id", $batch_id)->whereRaw("find_in_set($value->id , assign_inventory)")->get();
										foreach($query as $key => $assign){
											$pending = $total - $assign->given;											
											$stock	 = $value->total_qty - $assign->given;
										}
							?>
							<tr>
								<td><?php echo e($i++); ?></td>
								<td><?php echo e($value->name); ?> - (<?php echo e($value->inventory_type); ?>)</td>
								<td><?php echo e($value->total_qty); ?></td>
								<!--td><?php echo e($stock); ?></td-->
								<td><?php echo e($total); ?></td>
								<td><a href="<?php echo e(route('admin.student-inventory-view',[$value->batch_code,$value->id,1])); ?>" title="Click Here"><?php echo e($assign->given); ?></a></td>
								<td><a href="<?php echo e(route('admin.student-inventory-view',[$value->batch_code,$value->id,2])); ?>" title="Click Here"><?php echo e($pending); ?></a></td>
							</tr>
							<?php 		
									}
								}else{ 
							?>
							<tr>
								<td colspan="7" class="text-center">No Inventory Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>

<div id="overlay_loader">
	<div>
		<span>Please Wait.. Request Is In Processing.</span><br>
		<i class="fa fa-refresh fa-spin fa-5x"></i>
	</div>
</div>

<style>
.table thead th {
	font-size:16px !important;
}

.table tbody td{
	font-size:16px !important;
}

#overlay_loader {
    position: fixed;
	display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    cursor: pointer;
}
#overlay_loader div {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 40px;
    text-align: center;
    color: white;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    width: 100%;
}

</style>
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
	
	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.employee.get-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
				$("#overlay_loader").css('display','none');
			}
		});
	}
	
	function locationBatch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.get-batch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
			dataType : 'json',
			success : function (data){
				if(data.status){
					$('.batch_id').empty();
					$('.batch_id').append(data.batches);
					$('.allbatch_ids').val(data.allbatch_ids);
					$("#overlay_loader").css('display','none');
				}
			}
		});
	}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/inventory-dashboard.blade.php ENDPATH**/ ?>