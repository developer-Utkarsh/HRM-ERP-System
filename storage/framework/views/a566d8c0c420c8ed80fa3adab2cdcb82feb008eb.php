
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Faculty Invoive History</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
						
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
								<form action="<?php echo e(route('admin.freelancer.faculty-invoice-history')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" name="month" placeholder="Month" value="<?php echo e(app('request')->input('month', date('Y-m'))); ?>">

											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Faculty</label>
											<fieldset class="form-group">
												<select class="form-control faculty_id select-multiple1" name="faculty_id">
													<option value="">Select</option>
													<?php foreach($faculty as $fa){ ?>
													<option value="<?php echo e($fa->id); ?>" 
														<?php echo e(app('request')->input('faculty_id') == $fa->id ? 'selected' : ''); ?>>
														<?php echo e($fa->name); ?>

													</option>
													<?php } ?>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.freelancer.faculty-invoice-history')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Faculty Name</th>
								<th>Month</th>
								<th>Invoice</th>
								<th>Status</th>
								<th>Reject Remark</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){
								$i =1; 
									foreach($record as $r){ 
							?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php echo e($r->name); ?></td>
								<td><?php echo e($r->month); ?></td>
								<td>
									<a href="<?php echo e(asset('laravel/public/invoice/' . $r->invoice)); ?>" download>
										Preview
									</a>
								</td>
								<td>
									<?php if($r->status == 1): ?>
										Approved
									<?php elseif($r->status == 2): ?>
										Rejected
									<?php else: ?>
										Pending
									<?php endif; ?>
								</td>
								<td><?php echo e($r->reason??'-'); ?></td>
								<td>
									<?php if($r->status==0){ ?>
									<button type="button" class="btn btn-primary btn-sm updateStatus" data-id="<?php echo e($r->id); ?>">
										<i class="fa fa-pencil" aria-hidden="true"></i>
									</button>
									<?php }else{ echo '-'; } ?>
								</td>
							</tr>
							
							<?php $i++; } 
								}else{
							?>			
							<tr>
								<td class="text-center" colspan="6">No record found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					
				</div>                   
			</section>
		</div>
	</div>
</div>
 

 <!--GetInvoice---start-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?php echo e(route('admin.freelancer.invoice-stauts-update')); ?>" method="post">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="invoice_id" class="invoice_id" value=""/>
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Invoice Status Update</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<select name="status" class="status form-control" required>
						<option value="">-- Select Status --</option>
						<option value="1">Approved</option>
						<option value="2">Rejected</option>
					</select>
				</div>
				<div class="form-group remark" style="display:none;">
					<textarea name="remark" class="remark form-control" placeholder="Reject Reason"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</div>
	</form>
  </div>
</div>
<!--GetInvoice--end-->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});			
	
	$(document).on('click', '.updateStatus', function () {
		const invoice_id = $(this).attr('data-id');
		
		$('#exampleModal').modal('show');
		
		$('.invoice_id').val(invoice_id);
	});
	
	$(document).on('change', 'select[name="status"]', function () {
		if ($(this).val() == 2) {
			$(".remark").show();
			$(".remark textarea").attr("required", true);
		} else {
			$(".remark").hide();
			$(".remark textarea").removeAttr("required");
			$(".remark textarea").val(""); // clear old value
		}
	});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/freelancer/faculty_invoice.blade.php ENDPATH**/ ?>