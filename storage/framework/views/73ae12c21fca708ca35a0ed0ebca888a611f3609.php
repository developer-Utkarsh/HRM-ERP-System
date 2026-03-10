
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Discount Approvel</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card d-none">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.discountApprovel.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" name="month" placeholder="Month" value="<?php echo e(app('request')->input('month')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.discountApprovel.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Agent Name</th>
								<th>Reference Emaployee Name</th>
								<th>Type</th>
								<th>Student Mobile</th>
								<th>Discount Amount</th>
								<th>Category</th>
								<th>Discount Percentage</th>
								<th>Created At</th>
								<th>Action</th>
								<th>Remark</th>
								<th>Proof</th>
								<th>Doc</th>
								<th>Updated At</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($loop->iteration); ?></td>
									<td><?php echo e($val->agent_name); ?></td>
									<td><?php echo e($val->reference_name); ?></td>
									<td><?php echo e($val->type); ?></td>
									<td><?php echo e($val->student_mobile); ?></td>
									<td><?php echo e($val->discount_amount); ?></td>
									<td><?php echo e($val->category); ?></td>
									<td><?php echo e($val->discount_percentage); ?></td>
									<td><?php echo e(date("d-M-Y, h:i A",strtotime($val->created_at))); ?></td>
									<td>
										<?php if($val->status==0): ?>
										   <form method="POST" action="<?php echo e(route('admin.discountApprovel.store')); ?>" onsubmit='return confirm("Are you sure you want to Approve this?");' enctype="multipart/form-data">
										   	    <?php echo csrf_field(); ?>
										   	    <input type="hidden" name="record_id" value="<?php echo e($val->id); ?>">
										   	    <input type="hidden" name="type" value="<?php echo e($val->type); ?>">
                                                
                                                <div class="d-flex">
											   	    <select class='form-select' name="status">
				                                        <option value="" disabled selected>Select Status</option>
				                                        <option value="1">Approved</option>
				                                        <option value="2">Rejected</option>
				                                    </select>

											   	    <input type="file" id="fileInput-<?php echo e($val->id); ?>" name="doc_url" class="fileInput d-none" data-id="<?php echo e($val->id); ?>">
		                                            <label for="fileInput-<?php echo e($val->id); ?>" style="font-size:18px;margin-left:5px;">
		                                                <i class="fa fa-paperclip fileInput-name-<?php echo e($val->id); ?>"></i>
		                                            </label>
				                                </div>

										   	    <input type="submit" value="Action"  style="background:red;border-radius:4px;padding:2px;color:#fff;float:right;margin-top:8px;">
			                                </form>
										<?php elseif($val->status==1): ?>
										  Approved
										<?php elseif($val->status==2): ?>
										  Rejected
										<?php endif; ?>
									</td>
									<td><?php echo e($val->remark); ?></td>
									<td>
										<?php if($val->proof_doc): ?>
										 <a href="<?php echo e(asset('laravel/public/discount_approvel/' . $val->proof_doc)); ?>" target="_blank">View</a>
										<?php endif; ?>
									</td>
									<td>
										<?php if($val->doc_url): ?>
										<a href="<?php echo e(asset('laravel/public/discount_approvel/' . $val->doc_url)); ?>" target="_blank">View</a>
										<?php endif; ?>
									</td>
									<td><?php echo e($val->updated_at); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

	
    $(".fileInput").on("change",function(){
    	let record_id=$(this).attr("data-id");
        let files = Array.from(this.files).map(file => file.name).join(', ');
        if(files){
           $(".fileInput-name-"+record_id).addClass('text-success');
        }else{
            $(".fileInput-name-"+record_id).addClass('text-dark');
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/discountApprovel/index.blade.php ENDPATH**/ ?>