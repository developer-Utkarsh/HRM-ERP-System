
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Buyer List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4"></div>
				</div>
			</div>
		</div>
		<div class="content-body">
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card d-none">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.buyer.index')); ?>" method="get" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Name, Contact No, GST</label>
														<input type="text" class="form-control search" placeholder="Ex. Name, Contact No, GST .." name="search" value="<?php echo e(app('request')->input('search')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-4">
													<div class="form-group">
														<label>IS MEME</label>
														<select name="msme" class="msme form-control">
															<option value="">Select</option>
															<option value="1" <?php echo e(request('msme') == '1' ? 'selected' : ''); ?>>Yes</option>
															<option value="2" <?php echo e(request('msme') == '2' ? 'selected' : ''); ?>>No</option>
														</select>

														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-8">
													
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="<?php echo e(route('admin.buyer.index')); ?>" class="btn btn-warning">Reset</a>
														<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Nature of Vendor</th>
								<th>Address</th>
								<th>State</th>
								<th>Country</th>
								<th>Registration Type</th>
								<th>GSTIN/UIN</th>
								<th>PAN/IT No.</th>
								<th>RAJ STATE</th>
								<th>Account Number</th>
								<th>IFSC Code</th>
								<th>Bank Name</th>
							</tr>
						</thead>
						<tbody>
						<?php if(count($buyer) > 0): ?>
							<?php $__currentLoopData = $buyer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($pageNumber++); ?></td>
								<td class="product-category"><?php echo e($value->name ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->nature ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->address ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->state ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->country ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->registration_type ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->gstin_un ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->pan_it_no ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->raj_state ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->account_no ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->ifsc ?? '-'); ?></td>
								<td class="product-category"><?php echo e($value->bank ?? '-'); ?></td>
							</tr> 
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
						<tr ><td class="text-center text-primary" colspan="13">No Record Found</td></tr>
						<?php endif; ?>	
						</tbody>
					</table>
					
					<div class="d-flex justify-content-center">					
					<?php echo $buyer->appends($params)->links(); ?>

					</div>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script type="text/javascript">
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.search  = $('.search').val(),
		data.msme 	 = $('.msme').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/buyer-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	}); 
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/buyer/vendor_new_list.blade.php ENDPATH**/ ?>