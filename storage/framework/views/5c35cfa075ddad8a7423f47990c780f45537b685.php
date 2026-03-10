
<style type="text/css">
	.hide {
		display: none!important;
	}
</style>
<?php $__env->startSection('content'); ?>

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Comp Off</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Comp Off will be Paid
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<?php 
							$check = $_SERVER['QUERY_STRING'];
							if(!empty($check)){
								$nCheck	=	$check;
							}else{
								$nCheck	=	"";
							}
						?>
						
						<a href="<?php echo e(route('admin.employees.index', $nCheck)); ?>" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.employees.store-comp-off', $employee->id.'?'.$_SERVER['QUERY_STRING'])); ?>" method="post" enctype="multipart/form-data"> 
									<?php echo csrf_field(); ?>
										<div class="row">
										<div class="col-md-4col-12">
											<div class="form-group">
												<label for="company-column"><b>Comp. Off Will Be Paid ?</b></label>
												<div class="form-group d-flex align-items-center mt-1">														
												<label>
													<input type="radio" name="extraPay" value="1" <?php echo e(($employee->is_extra_working_salary == '1') ? "checked" : ""); ?>>
													Yes
												</label>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<label>
													<input type="radio" name="extraPay" value="0" <?php echo e(($employee->is_extra_working_salary == '0') ? "checked" : ""); ?>>
													No
												</label>
												</div>
											</div>
										</div>
										
										<div class="col-md-4 col-12 comp_off_div <?php if($employee->is_extra_working_salary  == '0'): ?><?php echo e('show'); ?><?php else: ?><?php echo e('hide'); ?><?php endif; ?>">
											<?php 
											if(!empty($employee->comp_off_start_date)){
												$comp_off_start_date = date("Y-m", strtotime($employee->comp_off_start_date));
											}
											?>
											<div class="form-group">
												<label for="company-column"><b>Comp Off Start Month</b></label>
												<input type="month" class="form-control comp_off_start_date" name="comp_off_start_date" value="<?php echo e(old('comp_off_start_date', isset($comp_off_start_date) ?  $comp_off_start_date : '')); ?>" max="<?=date('Y-m-d')?>">
												<?php if($errors->has('comp_off_start_date')): ?>
												<span class="text-danger"><?php echo e($errors->first('comp_off_start_date')); ?> </span>
												<?php endif; ?>
											</div>
										</div>
										
										<div class="col-md-4 col-12 mt-2">
											<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
										</div>
												
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<script>
$('input[name="extraPay"]').on("click", function () {
	var extraPay = $('input[name="extraPay"]:checked').val();
	if(extraPay == '1'){
		$('.comp_off_div').removeClass('show');
		$('.comp_off_div').addClass('hide');
		// $('.comp_off_start_date').val('');
	}
	else if(extraPay == '0'){
		$('.comp_off_div').removeClass('hide');
		$('.comp_off_div').addClass('show');
	} 
	else{
		$('.comp_off_div').removeClass('show');
		$('.comp_off_div').addClass('hide');
		// $('.comp_off_start_date').val('');
	}
});	
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/comp-off.blade.php ENDPATH**/ ?>