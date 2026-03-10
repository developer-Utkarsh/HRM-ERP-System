
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Return Product List</h2>
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
								<form action="<?php echo e(route('admin.request.return-product-list')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-status">Employee</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id">
													<option value="">Select</option>	
													<?php foreach($employee as $em){ ?>
													<option value="<?php echo e($em->id); ?>"  <?php if($em->id == app('request')->input('emp_id')): ?> selected="selected" <?php endif; ?>><?php echo e($em->name); ?> - <?php echo e($em->register_id); ?></option>	
													<?php } ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 rstatus" name="rstatus">
													<option value="">Select Any</option>													
													<option value="1" <?php if('1' == app('request')->input('rstatus')): ?> selected="selected" <?php endif; ?>>Approved</option>
													<option value="2" <?php if('2' == app('request')->input('rstatus')): ?> selected="selected" <?php endif; ?>>Pending</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3 pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.request.return-product-list')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Request No.</th>
								<th>Employee</th>
								<th>Product Name</th>	
								<th>Qty</th>
								<th>Branch</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>			
							<?php 
								$i =1; 
								if(count($record) > 0){
								foreach($record as $re){ ?>
							<tr>
								<td><?=$i;?></td>
								<td>REQ-<?php echo e($re->unique_no); ?></td>
								<td><?php echo e($re->uname); ?></td>
								<td><?php echo e($re->pname); ?></td>
								<td><?php echo e($re->qty); ?></td>
								<td><?php echo e($re->bname); ?></td>
								<td>
									<?php if(($re->inventory_status==2 || !empty($emp_id)) && empty($re->inventory_grn)){ ?>
									<a href="javascript:void(0)" title="Accept" data-id="<?php echo e($re->id); ?>" data-product-id="<?php echo e($re->product_id); ?>" class="product_accept btn-success" style="padding:5px;">Accept</a>
									<?php }else{ echo 'Accepted'; }?>
								</td>
							</tr>
							<?php  $i++;} }else{ ?>
							<tr>
								<td colspan="7" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>	
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
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
	
	$(".product_accept").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		var product_id = $(this).attr("data-product-id"); 
	
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.request.inventory-product-accept')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id,'product_id':product_id},
			dataType : 'json',
			success : function (data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){		
					swal("Done!", data.message, "success").then(function(){  		
						location.reload();
					});
				}
			}
		});
	}); 
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/return-product-list.blade.php ENDPATH**/ ?>