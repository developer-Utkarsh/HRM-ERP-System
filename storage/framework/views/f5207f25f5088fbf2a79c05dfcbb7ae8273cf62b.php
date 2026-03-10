
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Faculty Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
						<a href="<?php echo e(route('admin.faculty-sme.index')); ?>" class="btn btn-primary">Back</a>
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
								<form action="<?php echo e(route('admin.faculty-sme-uploadfile-submit')); ?>" method="post" name="filtersubmit" enctype='multipart/form-data'>
									<?php echo csrf_field(); ?>
									<input type="hidden" name="reuqest_id" value="<?php echo e($request_id); ?>"/>
									<div class="row">
										<div class="col-lg-4 form-group">
											<label><b>Category</b></label>
											<select name="category" class="form-control select_category_name" required>
												<option value="">Select</option>
												<?php
												if(!empty($all_courses)){
													foreach($all_courses as $val){
														if(!empty($get_details->category)){
															$selected = ($val->title == $get_details->category) ? 'selected' : '';
														}else{
															$selected = '';
														}
														echo '<option value="' . htmlspecialchars($val->title) . '" data-id="' . $val->id . '" ' . $selected . '>' . htmlspecialchars($val->title) . '</option>';
													}
												}
												?>
											</select>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Exam</b></label>
											<select name="exam[]" class="select-multiple form-control select-multiple select_exam" multiple required>
												<option value="">Select</option>
												<?php 
													if(!empty($get_details->exam)){
													$exam = json_decode($get_details->exam); 													
													foreach($exam as $ex){												
												?>
													<option value="<?=$ex->name."$#".$ex->id?>" data-id="<?php echo e($ex->id); ?>" selected><?php echo e($ex->name); ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Subject</b></label>
											<select name="subject[]" class="select-multiple form-control select_subject" multiple required>
												<option value="">Select</option>
												<?php 
													if(!empty($get_details->subject)){
													$subject = json_decode($get_details->subject); 													
													foreach($subject as $su){												
												?>
													<option value="<?=$su->name."$#".$su->id?>" data-id="<?php echo e($su->id); ?>" selected><?php echo e($su->name); ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Chapter</b></label>
											<select name="chapter[]" class="select-multiple form-control select_chapter" multiple required>
												<option value="">Select</option>												
												<?php 
													if(!empty($get_details->topic)){
													$topic = json_decode($get_details->topic); 													
													foreach($topic as $to){												
												?>
													<option value="<?=$to->name."$#".$to->id?>" data-id="<?php echo e($to->id); ?>" selected><?php echo e($to->name); ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>No Of Question</b></label>
											<input type="number" name="no_of_question" value="<?=$get_details->no_question??''?>" class="form-control" required/>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Mode</b></label>
											<select name="mode" class="form-control" required onchange="getMode(this.value)">
												<option value="">Select</option>
												<option value="Manual" <?php echo e((!empty($get_details->mode) && $get_details->mode == 'Manual') ? 'selected' : ''); ?>>Manual</option>
												<option value="PrashnKosh" <?php echo e((!empty($get_details->mode) && $get_details->mode == 'PrashnKosh') ? 'selected' : ''); ?>>PrashnKosh</option>
											</select>
										</div>
										
										<div class="col-lg-4 form-group test_id" style="display:<?php if(($get_details->test_id??0)!=0){ echo 'block'; }else{ echo 'none';} ?>">
											<label><b>Test ID</b></label>
											<input type="number" name="test_id" value="<?=$get_details->test_id??''?>" class="form-control"/>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Level</b></label>
											<select name="level[]" class="select-multiple form-control" multiple required>
												<option value="">Select</option>
												<option value="Easy" <?php echo e((!empty($get_details->level) && in_array('Easy', explode(',', $get_details->level))) ? 'selected' : ''); ?>>Easy</option>
												<option value="Medium" <?php echo e((!empty($get_details->level) && in_array('Medium', explode(',', $get_details->level))) ? 'selected' : ''); ?>>Medium</option>
												<option value="Hard" <?php echo e((!empty($get_details->level) && in_array('Hard', explode(',', $get_details->level))) ? 'selected' : ''); ?>>Hard</option>
											</select>

										</div> 
										<div class="col-lg-4 form-group">
											<label><b>Requirement For</b></label>
											<select name="requirement_for" class="form-control" required>
												<option value="">Select</option>
												<option value="YouTube" <?php echo e((!empty($get_details->requirement_for) && $get_details->requirement_for == 'YouTube') ? 'selected' : ''); ?>>YouTube</option>
												<option value="Offline Batch" <?php echo e((!empty($get_details->requirement_for) && $get_details->requirement_for == 'Offline Batch') ? 'selected' : ''); ?>>Offline Batch</option>
												<option value="Online Batch" <?php echo e((!empty($get_details->requirement_for) && $get_details->requirement_for == 'Online Batch') ? 'selected' : ''); ?>>Online Batch</option>
											</select>
										</div>
										<div class="col-lg-4 form-group">
											<label><b>Language</b></label>
											<select name="language" class="form-control" required>
												<option value="">Select</option>
												<option value="English" <?php echo e((!empty($get_details->language) && $get_details->language == 'English') ? 'selected' : ''); ?>>English</option>
												<option value="Hindi" <?php echo e((!empty($get_details->language) && $get_details->language == 'Hindi') ? 'selected' : ''); ?>>Hindi</option>
												<option value="Bilingual" <?php echo e((!empty($get_details->language) && $get_details->language == 'Bilingual') ? 'selected' : ''); ?>>Bilingual</option>
											</select>
										</div>
										<div class="col-lg-12 form-group">
											<label><b>Browse File</b></label>
											<input type="file" name="pdf_file" value="" class="form-control" <?php if(empty($get_details->file)){ echo 'required'; } ?> />
											
											<?php if(!empty($get_details->file)){ ?>
											<br>
											<iframe src="<?php echo e(asset('laravel/public/faculty_sme/'.$get_details->file)); ?>" width="100%" height="400px" style="border: none;"></iframe>
											<?php } ?>
										</div>
										
										<?php if($viewonly!=1){ ?>
										<div class="col-12 col-sm-12 col-lg-12">
											<input type="hidden" name="resue_by" value="<?php echo e($resue_by); ?>"/>
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Submit</button>
											<a href="<?php echo e(route('admin.faculty-sme-uploadfile')); ?>?request_id=<?php echo e($request_id); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
										<?php } ?>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
 

<div id="overlay_loader">
	<div>
		<span>Please Wait.. Request Is In Processing.</span><br>
		<i class="fa fa-refresh fa-spin fa-5x"></i>
	</div>
</div>


<style>
#overlay_loader {
  position: fixed;
	display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    cursor: pointer;
}
#overlay_loader div {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 40px;
    text-align: center;
    color: white;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    width: 100%;
}

.select2.select2-container{
	width:100% !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">		
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	
	$(document).on("change",".select_category_name",function(){
		var category_name = $(this).find(":selected").attr("data-id");
		var _this = $(this);
		if(category_name){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('admin.faculty-sme.get_cat_exam')); ?>',
				data : {'category_name': category_name},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_exam").html(data.html);
					}
				}
			});   
		}
	})
	
	
	$(document).on("change",".select_exam",function(){
		var exam_id =  	$(this).find(":selected").map(function () {
							return $(this).attr("data-id");
						}).get().join(",");
		
		if(exam_id){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('admin.faculty-sme.get_exam_subject')); ?>',
				data : {'exam_id': exam_id},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_subject").html(data.html);
					}
				}
			});   
		}
	})
	
	$(document).on("change",".select_subject",function(){
		var subject_id = $(this).find(":selected").map(function () {
							return $(this).attr("data-id");
						}).get().join(",");;
		var _this = $(this);
		if(subject_id){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('admin.faculty-sme.get_subject_chapter')); ?>',
				data : {'subject_id': subject_id},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_chapter").html(data.html);
					}
				}
			});    
		}
	})
	
	
	function getMode(value){
		$('.test_id').hide();
		$('.test_id input').prop('required',false);
			
		if(value=='PrashnKosh'){
			$('.test_id').show();
			$('.test_id input').prop('required',true);
		}
	}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_sme/uploadfile.blade.php ENDPATH**/ ?>