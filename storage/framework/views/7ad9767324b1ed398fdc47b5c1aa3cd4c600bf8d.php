
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-5 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Multi Planner Summary</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
							</ol>
						</div>
					</div>
				</div>  
			</div>
			<div class="content-header-right text-md-right col-md-7 col-12 d-md-block">
				<?php if(Auth::user()->role_id==21 || Auth::user()->role_id==27){ ?>
				<a href="<?php echo e(route('admin.multi-course-planner.subject-assign',[$req_id])); ?>"><button class="btn btn-primary" type="button">Go To Approval</button></a>
				<?php } ?>
				
				<?php if(Auth::user()->id==8232 || Auth::user()->id==8866){ ?>
				<a href="<?php echo e(route('admin.multi-course-planner.subject-assign',[$req_id])); ?>"><button class="btn btn-primary" type="button">View Subject Details</button></a>
				<?php } ?>
				
				<a href="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>"><button class="btn btn-primary" type="button">Back</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										<?php if(Auth::user()->role_id == 29 || Auth::user()->department_type == 13 || (Auth::user()->department_type==50 && Auth::user()->id!=8232 && Auth::user()->id!=8866)){ ?>
											<?php if(count($topic_relation) > 0): ?>
												<?php $__currentLoopData = $topic_relation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject_id => $topics): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<table class="table table-bordered">
														<thead>
															<tr>
																<th colspan="3">
																	<h5><strong>Subject: <?php echo e($topics[0]->subject_name); ?></strong></h5>
																</th>
															</tr>
															<tr>
																<th>Topic</th>
																<!--th>Sub Topic</th-->
																<th>Duration</th>
															</tr>
														</thead>
														
														<?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<tr>
															<td>
																<?php echo e($tr->topic_name); ?><?php echo e((!empty($tr->topic_name) && !empty($tr->topic_en_name)) ? ' || ' . $tr->topic_en_name : ''); ?>

															</td>
															<?php 
															/*
															<td>
																{{ $tr->sub_topic_name }}{{ (!empty($tr->sub_topic_name) && !empty($tr->sub_topic_en_name)) ? ' || ' . $tr->sub_topic_en_name : '' }}
															</td>
															*/ ?>
															<td>
																<?php echo e($tr->duration); ?>

															</td>
														</tr>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</table>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php else: ?>
												<div class="alert alert-warning text-center">
													No planner uploaded.
												</div>
											<?php endif; ?>

										<?php }else{ ?>
											<form id="facultyTimeForm" action="" method="post" name="filtersubmit">
												<?php echo csrf_field(); ?>
												
												
												<?php if(Auth::user()->department_type==4 && Auth::user()->role_id!=21){ ?>
												<table class="table table-bordered">
													<thead class="thead-dark">
														<tr>
															<th>Subject</th> 
															<th>SME Remark</th>
															<th>Timelines</th>
														</tr>
													</thead>
													<?php foreach($record as $re){ ?>
													<tr>
														<td><?php echo e($re->subject_name); ?></td>
														<td><?php echo e($re->sme_remark); ?></td>
														<td><?php echo e(date('d-m-Y', strtotime($re->date))); ?></td>
													</tr>
													<?php } ?>
												</table>
												<hr>
												<?php } ?>
																							
												<div id="filter-wrapper">
													<?php if(Auth::user()->role_id == 21 || Auth::user()->id == 8232 || Auth::user()->id == 8866): ?>
														<?php echo $__env->make('admin.multi-course-planner.dh_summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
													<?php else: ?>
														<?php echo $__env->make('admin.multi-course-planner.sme_summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
													<?php endif; ?>
												</div>												
											</form>
										<?php } ?>
									</div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/*
$(document).ready(function () {
    $('.filter-section').first().show();

    $(document).on('click', '.add-more', function () {
        let clone = $('#filter-section').first().clone();

        clone.find('select').val('');
        clone.find('input').val('');

        clone.find('.sub_topic').html('<option value="">Select Sub Topic</option>');

        clone.find('.add-more').hide();
        clone.find('.remove-section').show();

        $('.filter-section').last().before(clone);
    });

    // Remove section
    $(document).on('click', '.remove-section', function () {
        $(this).closest('.filter-section').remove();
    });
});
*/
$(document).ready(function () {
    $('.filter-section').first().show();

    $(document).on('click', '.add-more', function () {
        let currentBlock = $(this).closest('.subject-form-block');
        let firstSection = currentBlock.find('.filter-section').first();
        let clone = firstSection.clone();

        clone.find('select').val('');
        clone.find('input').val('');
        clone.find('.sub_topic').html('<option value="">Select Sub Topic</option>');

        clone.find('.add-more').hide();
        clone.find('.remove-section').show();
        currentBlock.find('.filter-section').last().after(clone);
    });

    $(document).on('click', '.remove-section', function () {
        $(this).closest('.filter-section').remove();
    });
});





$(document).on("change", ".topic_id", function () {
    var $this = $(this); // current changed select
    var topic_id = $this.val(); // since it's a select, .val() gives selected value directly

    if (topic_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo e(route('admin.multi-course-planner.get-sub-topic')); ?>',
            data: {
                '_token': '<?php echo e(csrf_token()); ?>',
                'topic_id': topic_id
            },
            dataType: 'html',
            success: function (data) {
                // find the closest .sub_topic inside the same row/group
                $this.closest('.row').find('.sub_topic').html(data);
            }
        });
    }
});
</script>
<script type="text/javascript">
/*
$(document).ready(function () {
    $('.submit-btn').click(function (e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        let form = $('#facultyTimeForm');
        let formData = form.serialize() + '&submit_type=' + dataId;
						
        $.ajax({
            url: '<?php echo e(route("admin.multi-course-planner.save-planner-summary")); ?>', // double quotes for Blade inside JS
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});
*/
$(document).ready(function () {
    $('.submit-btn').on('click', function (e) {
        e.preventDefault();
        
        const button = $(this);
        const submitType = button.data('id');
        const subjectBlock = button.closest('.subject-form-block');

        const inputs = subjectBlock.find(':input[name]');
        const formData = new URLSearchParams();

        inputs.each(function () {
            const input = $(this);
            const name = input.attr('name');
            const value = input.val();

            if (name.endsWith('[]')) {
                formData.append(name, value);
            } else {
                formData.set(name, value);
            }
        });

        formData.append('submit_type', submitType);

        $.ajax({
            url: '<?php echo e(route("admin.multi-course-planner.save-planner-summary")); ?>',
            type: 'POST',
            data: formData.toString(),
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});

</script>

<script>
	function setSubjectId(select) {
		const subjectId = select.options[select.selectedIndex].getAttribute('data-id');		
		const hiddenInput = select.closest('.form-group').querySelector('.subject_id');
		if (hiddenInput) {
			hiddenInput.value = subjectId;
		}
	}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/multi-course-planner/multi_planner_summary.blade.php ENDPATH**/ ?>