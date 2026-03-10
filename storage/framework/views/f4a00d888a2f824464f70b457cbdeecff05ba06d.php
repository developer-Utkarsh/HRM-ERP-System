
<?php $__env->startSection('content'); ?>

 <style>
	body {
		background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);
	}
	
    .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media  screen and (max-width: 770px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
.table tbody + tbody {
    border-top: 2px solid #ccc;
}

</style>
	
<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">All Reports</h2>
						 
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="row">

				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="https://zfrmz.in/O4Y0vavTyE7EMwdciYUc?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Employee Family Details</a> 
				</div>

				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="https://zfrmz.in/fZuyNUf2oVaVcjoLnwML?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">ASSET DECLARATION FORM</a> 
				</div>
				


				<?php if($app_config['is_faculty_report']==1){ ?>	
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('nps-reports')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Offline Feedback Report</a>
					</div>
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('nps-reports-new')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">NPS 4.0. Report</a>
					</div>
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('nps-reports-five')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">NPS 5.0. Report</a>
					</div>
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('nps-reports-six')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">NPS 6.0. Report</a>
					</div>
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('nps-reports-seven')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">NPS 7.0. Report <sup style="background: #fff;color: red;padding: 2px;">New</sup></a>
					</div>
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;display:block;">		
						<a href="<?php echo e(route('online-nps-reports')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Online Feedback Report</a>
					</div>
					
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
						<a href="<?php echo e(route('faculty-reports')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Faculty  Report</a>
					</div>
					
						
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
						<a href="<?php echo e(route('faculty-leave-report')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Faculty Leave Report</a> 
					</div>
					
					<div class="col-md-12 btn-danger text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
						<a href="<?php echo e(route('faculty-sme-request-index')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Panel File Request</a> 
					</div>
					
					<div class="col-md-12 btn-primary text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
						<a href="<?php echo e(route('faculty-planner-verification')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Planner Verification</a> 
					</div>
				<?php } 
				if($app_config['is_faculty_hours_reports_new']==1){
				?>					
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;">		
					<a href="<?php echo e(route('faculty-hours-reports-new')); ?>?faculty_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Faculty Hours Report</a>
				</div>	
				<?php } 
				if($app_config['is_test_report']==1){
				?>
				
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('test-report')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Test Classes</a> 
				</div>
				
				<?php }
				
				if($app_config['is_employee_leave']==1){
				?>
				
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('employee-leave-detail')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Leave Details</a> 
				</div>
				
				<?php }
				
				/*
				if(count($mentor_batch_list) > 0){
					foreach($mentor_batch_list as $val){
					?>
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
						<a href="{{route('mentor-report-batch-detail')}}?batch_id=<?=$val['batch_id']?>&&mentor_id=<?=$val['mentor_id']?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Batch Name -  <strong><?=$val['batch_name']?></strong></a> 
					</div>
				
				<?php }
				}
				*/
				?>
				
				<?php if($user_id==7541 || $user_id==902 || $user_id==7087 || $user_id==7985 || $user_id==5408 || $user_id==8455){ ?>
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('acge')); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Employee Message</a> 
				</div>
				<?php } ?>
				
				<?php if($user_id==5408 || $user_id==8455){ ?>
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('acge')); ?>/isFaculty" class="btn clickonloader" style="color:#fff !important;width:100%;">Faculty Message</a> 
				</div>
				<?php } ?>
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('cleanliness-report')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Cleanliness</a> 
				</div>
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="<?php echo e(route('complaint-cleanliness')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Complaint Cleanliness Report</a> 
				</div>
				
				<?php if($app_config['is_faculty_report']==1){ ?>	
				<div class="col-md-12 btn-dark text-center text-dark" style="margin-top: 15px;margin-bottom: 30px;border-radius: 10px;" >		
					<a href="<?php echo e(route('faculty-invoice-add')); ?>?user_id=<?php echo e($user_id); ?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Upload Invoice <sup style="background: #fff;color: red;padding: 2px;">New</sup></a> 
				</div>
				<?php } ?>
				</div>	
				
			</section>
		</div>
	</div>
</div>
 <div id="loading"></div>
	
<style>
#loading {
    background: url("<?php echo e(asset('/laravel/public/images/loading-gif.gif')); ?>") no-repeat center center;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 9999999;
}
</style>	
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$('#loading').hide();
$(document).on("click",".clickonloader",function(){
	$('#loading').show();
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/index.blade.php ENDPATH**/ ?>