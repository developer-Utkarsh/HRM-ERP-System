
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Employee Asset</h2>
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
		<!--section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.asset.employee-asset')); ?>" method="get" name="filtersubmit">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="text" class="form-control" placeholder="Name" id="asset_name" name="name" value="<?php echo e(app('request')->input('name')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		                                
												<div class="col-md-8">
													
													<fieldset class="form-group">		
														<a href="<?php echo e(route('admin.asset.employee-asset')); ?>" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section-->
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.asset.employee-asset')); ?>" method="get" name="filtersubmit">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">	
												<div class="col-md-4">
													<label for="users-list-status">Employee</label>
													<?php $users = \App\User::where('status', '1')->orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 eID" name="eID">
															<option value="">Select Any</option>
															<?php if(count($users) > 0): ?>
															<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('eID')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?><?php if(!empty($value->register_id)): ?><?php echo e(' - ('.$value->register_id.')'); ?><?php endif; ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>												
													</fieldset>
												</div>

												<div class="col-md-8">
													<label for="" style="">&nbsp;</label>
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="<?php echo e(route('admin.asset.employee-asset')); ?>" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Employee Name</th>
								<th>Assign List</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						<?php $key = 0; $sts = ''; ?>
						<?php if(count($emp_asset_list) > 0): ?>
							<?php $__currentLoopData = $emp_asset_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php 
							$assigned_asset = DB::table('asset')
												->select('products.name','assign_asset.qty','assign_asset.is_accepted','assign_asset.created_at','assign_asset.return_date')
												->join('products', 'products.id', '=', 'asset.product_id')
												->join('assign_asset', 'assign_asset.asset_id', '=', 'asset.id')
												->where('assign_asset.emp_id', $value->emp_id)
												->get();
							
							?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td>
									<?php if(count($assigned_asset) > 0): ?>
									<table class="table data-list-view" style="background: #f7f7f73d; width:60%">
										<tr>
											<th>Name</th>
											<th>Qty</th>
											<th>Status</th>
											<th>Assigned Date</th>
											<th>Return Date</th>
										</tr>
										<?php
										foreach($assigned_asset as $assigned_asset_value){
											if($assigned_asset_value->is_accepted == '1'){
												$sts = 'Returned';
											}
											else{
												$sts = 'Assigned';
											}
											
											?>
											<tr>
												<td><?=$assigned_asset_value->name?></td>
												<td><?=$assigned_asset_value->qty?></td>
												<td><?=$sts?></td>
												<td><?=date('d-m-Y',strtotime($assigned_asset_value->created_at))?></td>
												<td><?=!empty($assigned_asset_value->return_date) ? date('d-m-Y',strtotime($assigned_asset_value->return_date)) : ''?></td>
											</tr>
											<?php
										}
										?>
										
										
									</table>
									<?php endif; ?>	
								</td>
								<td class="product-action">
									<a title="Update Asset" href="<?php echo e(route('admin.asset.edit', $value->emp_id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
						<tr ><td class="text-center text-primary" colspan="7">No Record Found</td></tr>
						<?php endif; ?>	
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
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Employee Name",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/asset/employee_asset.blade.php ENDPATH**/ ?>