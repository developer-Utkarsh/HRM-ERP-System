
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Inventory Valuation</h2>
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
								<form action="<?php echo e(route('admin.request.reports.inventory-valuation')); ?>" method="get" name="filtersubmit">
									<div class="row pt-2">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<label for="users-list-status">Month</label>
												<input type="month" class="form-control" name="fmonth" placeholder="Title" value="<?php echo e(app('request')->input('fmonth')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php 
											$branch_location = app('request')->input('branch_id');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('id', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_id" name="branch_id" required>
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Asset Type</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple asset_type" name="asset_type">
													<option value="">Select Any</option>
													<option value="Asset" <?php if('Asset' == app('request')->input('asset_type')): ?> selected="selected" <?php endif; ?>>Asset</option>
													<option value="Non Asset" <?php if('Non Asset' == app('request')->input('asset_type')): ?> selected="selected" <?php endif; ?>>Non Asset</option>
													<option value="All" <?php if('All' == app('request')->input('asset_type')): ?> selected="selected" <?php endif; ?>>All</option>											
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3 pt-2">
											<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
											<a href="<?php echo e(route('admin.request.reports.inventory-valuation')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Req. Type</th>
								<th>Request No.</th>
								<th>Date</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product</th>
								<th>Total Amt</th>		
								<th>Branch</th>
								<th>City</th>		
							</tr>
						</thead>
						<tbody>
							<?php 
								$i =1; 
								$totalAmount = 0;
									foreach($record as $re){ 
							?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php if($re->request_type=='1'){ echo 'WRL'; }else{ echo 'MRL';} ?></td>
								<td>REQ-<?php echo e($re->unique_no); ?></td>
								<td><?php echo e(date('d-m-Y', strtotime($re->created_at))); ?></td>
								
								<td><?php echo e($re->cname); ?></td>
								<td><?php echo e($re->sname); ?></td>
								<td><?php echo e($re->pname); ?></td>
								<td><?php echo e($re->total); ?></td>
								<td><?php echo e($re->bname); ?></td>
								<td><?php echo e(ucwords($re->branch_location)); ?></td>
							</tr>
							<?php 
										$i++; 
									$totalAmount = $totalAmount + $re->total;
								} 
							?>
							<tr>
								<td colspan="7" class="text-right"><h4>Total Amount</h4></td>
								<td colspan="3"><h4>RS. <?=$totalAmount;?> /-</h4></td>
							</tr>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>

<style type="text/css">
	.table tbody td {
		word-break: break-word;
	}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/reports/inventory_valuation.blade.php ENDPATH**/ ?>