
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0"><?php echo e($heading); ?> Question</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><?php echo e($heading); ?> Question
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('admin.feedback-question')); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="<?php echo e($url); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?> 
										<div>											
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Question</label> 
													<textarea name="question" placeholder="Question" class="form-control remark" required><?php if(!empty($question['question'])){ echo $question['question']; }?></textarea>
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Question Type</label> 
													<select class="form-control select-multiple1" name="question_type" required  onchange="qu_typechange(this.value)">
														<?php 
															$rSelected = '';
															$wSelected = '';
															$raSelected = '';
															if(!empty($question['qtype'])){
																if($question['qtype']=='rating'){ 
																	$rSelected = 'selected'; 
																}else if($question['qtype']=='write'){ 
																	$wSelected = 'selected'; 
																}else if($question['qtype']=='radio'){ 
																	$raSelected = 'selected'; 
																}
															}
														?>
														<option value=""> - Select Type - </option>
														<option value="rating" <?=$rSelected;?>>Raiting</option>
														<option value="write" <?=$wSelected;?>>Write</option>
														<option value="radio" <?=$raSelected;?>>Radio</option>
													</select>
												</div>
											</div>
											
											<?php if(!empty($question['options'])){ $class = 'block'; }else{ $class = 'none'; } ?>
											<div class="col-md-12 col-12"  id="q_options" style="display:<?=$class;?>;">
												<div class="form-group" >
													<label for="first-name-column">Options{add multiple option by && separated}</label> 
													<textarea name="options" placeholder="Options" class="form-control remark"><?php if(!empty($question['options'])){ echo $question['options']; }?></textarea>
												</div>
											</div>                           
											<div class="col-12">
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
<script type="text/javascript">

  function qu_typechange(ntType){
    if(ntType=='Radio'){
      $('#q_options').show();
    }else{
       $('#q_options').hide();
    }
  } 
</script> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/feedback_question/add.blade.php ENDPATH**/ ?>