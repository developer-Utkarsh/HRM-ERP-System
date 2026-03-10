
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Request Pending Accept</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="<?php echo e(route('admin.request-pending-accept')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">Req. No.</label>
												<input type="text" class="form-control mrl" name="mrl" placeholder="MRL Number" value="<?php echo e(app('request')->input('mrl')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">Created Date</label>
												<input type="date" class="form-control cdate" name="cdate" placeholder="Date" value="<?php echo e(app('request')->input('cdate')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">User Name</label>
												<input type="text" class="form-control uname" name="uname" placeholder="User" value="<?php echo e(app('request')->input('uname')); ?>">
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
												<select class="form-control select-multiple1 branch_id" name="branch_id" onchange="locationBatch(this.value);">
													<option value="">Select Any</option>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.request-pending-accept')); ?>" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_report" class="btn btn-primary">Report</a>
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
								<th>MRL Number</th>
								<th>User</th>								
								<th>Category</th>
								<th>Sub Category</th>										
								<th>Product</th>	
								<th>Qty</th>
								<th>Branch</th>
								<th>Created Date</th>
							</tr>
						</thead>
						<tbody>			
							<?php if(count($getRequest) > 0){ ?>
								<?php $__currentLoopData = $getRequest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td>REQ-<?php echo e($value->unique_no); ?></td>
									<td><?php if(!empty($value->uname)){ echo $value->uname; }else{ echo '-'; } ?></td>									
									<td><?php if(!empty($value->name)){ echo $value->name; }else{ echo '-'; } ?></td>
									<td><?php if(!empty($value->sub_name)){ echo $value->sub_name; }else{ echo '-'; } ?></td>
									<td><?php if(!empty($value->pname)){ echo $value->pname; }else{ echo '-'; } ?></td>
									<td><?php echo e($value->qty); ?></td>
									<td><?php if(!empty($value->brname)){ echo $value->brname; }else{ echo '-'; } ?></td>
									<td><?=date('d-m-Y',strtotime($value->created_at));?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>									
							<?php }else{ ?>
								
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							<?php } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $getRequest->appends($params)->links(); ?>

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
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_report", function (e) {
		var data = {};					
		data.mrl 		= $('.mrl').val(),
		data.cdate 		= $('.cdate').val(),
		data.uname  	= $('.uname').val(),
		data.branch_id  	= $('.branch_id').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/request-pending-accept-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});	
	
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/request_pending_accept.blade.php ENDPATH**/ ?>