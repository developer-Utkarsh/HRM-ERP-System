
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
						<h2 class="content-header-title float-left mb-0">Add</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('admin.freelancer.index')); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="<?php echo e(route('admin.freelancer.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label>Title</label>														
														<input type="text" name="tittle" class="form-control" value=""/>
														<?php if($errors->has('course_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label>Message</label>														
														<textarea name="msg" class="form-control" rows="5"></textarea>
														<?php if($errors->has('course_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-10">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).on('keyup', '.cal_hour', function(){
		var cnt = 0; 
		$('.count_div').removeClass('hide');
		$(".cal_hour").each(function() {  
			if($(this).val() != ''){
				cnt += parseFloat($(this).val());
			}
		});
		$('#count_hour').text(cnt);
	});
	
	
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Course",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "- Select Faculty -",
			allowClear: true
		});

		$('.select-batche').select2({
			placeholder: "Select Course",
			allowClear: true
		});

		$(".select-batche").on("change",function(){
			var batch_id=$(".select-batche :selected").attr("data");
			$("input[name='batch_code']").val(batch_id);
			$("input[name='branch']").val($('option:selected',this).attr("data-branch"));
			$("input[name='capacity']").val($('option:selected',this).attr("data-capacity"));
			$("input[name='start_date']").val($('option:selected',this).attr("data-start-date"));
			$("input[name='end_date']").val($('option:selected',this).attr("data-end-date"));
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".after-add-more").after(html);    
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".control-group").remove();
		});
	}); 
</script>
<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('admin.get-batch-subject')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id},
				dataType : 'html',
				success : function (data){
					$("#course_loader i").hide();

					$('.subjects').empty();
                    $(".course_subjects").empty();		

					$(".subjects").append(data);
					$('.select-multiple2').select2({
						placeholder: "- Select Faculty -",
						allowClear: true
					});

					$(data).each(function(){
						if(this.value!=""){
							var hh=`<div class="row">
									<div class='col-md-4 mb-2'>
										<div class='input-group'>
											<input type="hidden" class='form-control subjects' name='course[subject_id][]' required value="`+this.value+`">
											<input class='form-control' value="`+this.text+`" readonly>
										</div>
									</div>
									<div class='col-md-4 col-12 mb-2'>
										<div class='input-group'>
											<input type='number' class='form-control cal_hour' name='course[no_of_hours][]' placeholder='No of hours' step=0.01 required
											value="`+$(this).attr('data-duration')+`">
										</div>
									</div>
									<div class='col-md-2 col-12 mb-2 text-danger p-1 removeSubject hide'>X</div>
								</div>`;
							$(".course_subjects").append(hh);
						}
					});



				}
			});
		}
	});

	$(".subject_addMore").on("click",function(){
       $(".course_subjects").append($(".copy_subject").html());
       var rowNo=$(".subject_addMore").data("row");
	   $(".subject_addMore").data("row",rowNo+1);		

	});

	$(document).on("click",".removeSubject",function(){
		var rowNo=$(".subject_addMore").data("row");
		if(rowNo!=1){
			$(".subject_addMore").data("row",rowNo-1);
            $(this).parent(".row").remove();
        }
	});
    
    var add_subjects="";
	$(document).on("change",".subjects",function(){
        subjects=add_subjects.split('$$'); 
		if(jQuery.inArray($(this).val(),subjects)==-1){
		  add_subjects=add_subjects+"$$"+$(this).val();

		  var duration=$('option:selected',this).attr("data-duration");
		  var ddd=$(this).closest(".row");
		  $(ddd).find(".cal_hour").val(duration);
	    }else{
	    	alert('Subject Already Selected');
	    	$(this).val('');
	    }
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/freelancer/add.blade.php ENDPATH**/ ?>