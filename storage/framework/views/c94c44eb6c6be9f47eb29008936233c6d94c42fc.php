
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Message to Chairman</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 float-right">
						<div class="float-right">
						   <i class="filterIcon fa fa-filter text-primary" style="font-size:30px;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
		        <?php echo $__env->make('admin.support_category.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		        <div class="row">
					<?php if(count($enquiry) > 0): ?>
						<?php $__currentLoopData = $enquiry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="col-md-6">
								<div class="card">
									<div class="card-body card-<?php echo e($val->id); ?>">
										<div class="row">
											<div class="col">
												
												<div class="row">
													<p class="col-4">Ticket No :</p>
												    <p class="col-8">##-<?php echo e($val->id); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Enquiry :</p>
												    <p class="col-8"><?php echo e($val->description); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Rating :</p>
												    <p class="col-8"><?php echo e($val->rating); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Student Name :</p>
												    <p class="col-8"><?php echo e($val->student_name); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Reg No :</p>
												    <p class="col-8"><?php echo e($val->reg_no); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Mobile No :</p>
												    <p class="col-8"><?php echo e($val->mobile_no); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Batch:</p>
												    <p class="col-8"><?php echo e($val->batch_name); ?> <br> <?php echo e($val->course_name); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Branch :</p>
												    <p class="col-8"><?php echo e($val->branch_name); ?> - <?php echo e($val->location); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Category :</p>
												    <p class="col-8"><?php echo e($val->cat); ?></p>
												</div>
												<div class="row">
													<p class="col-4">Status :</p>
												    <p class="col-8"><?php echo e(ucwords($val->status)); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Created At :</p>
												    <p class="col-8"><?php echo e($val->updated_at); ?></p>
												</div>

												<div class="row">
													<p class="col-4">Updated At :</p>
												    <p class="col-8"><?php echo e($val->created_at); ?></p>
												</div>
											</div>
											
											<div class="col-auto">
												<a href="javascript:void(0)" class="btn btn-outline-info btn-sm mt-1 old_query_data" data-id="<?php echo e($val->id); ?>" title="Old Query" style="padding: 0.5rem 0.5rem;"> <i class="fa fa-reply"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					   <div class="col-12 mt-3 text-center">
						  <?php echo e($enquiry->links()); ?>

					    </div>
					<?php else: ?>
					  No record found
					<?php endif; ?>
				</div>
			</section>
		</div>
	</div>
</div>

<div id="old-query-form" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Query List</h5>
			</div>
			
			<form method="post" action="<?php echo e(route('admin.support-enquiryReply')); ?>"  id="replyForm">
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<div class="old_data"></div>
					
					<div class="modal-body">
						<div class="form-body">
							
							<h3>Reply</h3><hr>
							<textarea name="description" rows="5" class="form-control"></textarea>
						</div>
					</div>
				</div>
				
				<div class="modal-footer">
					<input type="hidden" name="enquiry_id" class="enquiry_id" value="">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script type="text/javascript">
	$(document).on("click",".old_query_data", function() { 
		var enq_id = $(this).attr("data-id");
	    $.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.get-old-query')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'enq_id': enq_id},
			dataType : 'html',
			success : function (data){
				$('.old_data').empty();
				$('.old_data').html(data);
				$(".enquiry_id").val(enq_id);
				
				$('#old-query-form').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			    });
		    }
		});
	});

	$("#replyForm").submit(function(e) {
		e.preventDefault(); 
		var _this=$(this);
		var enq_id=$(_this).find(".enquiry_id").val();
		var form = document.getElementById('replyForm');
		var dataForm = new FormData(form);
	    $.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.support-enquiryReply')); ?>',
			data : dataForm,
			processData : false,  
			contentType : false,
			dataType : 'json',
			success : function (data){
				if(data.status){
					$(_this).trigger("reset");
					$(".card-"+enq_id).css('background','gray');
					$('#old-query-form').modal('hide');
					alert(data.msg);
				}else{
					alert(data.msg);
				}
		    }
		});
	});

	$("body").on("click", "#download_excel", function (e) {

		var form = document.getElementById('filtersubmit');
	    var data = new FormData(form);

		var url=window.location.href;
		//url=url.replace('enquiry','enquiry-excel');
		url=url+"?&excel_export=yes";
		window.location=url;
		console.log("dddd",url);
	});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/support_category/enquery.blade.php ENDPATH**/ ?>