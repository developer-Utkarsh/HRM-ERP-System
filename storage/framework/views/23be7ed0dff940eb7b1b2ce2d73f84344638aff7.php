
<?php $__env->startSection('content'); ?>

<link href="<?php echo e(asset('laravel/public/course-planner/styles.css')); ?>" rel="stylesheet">
<body style="background-color: #FFFEF5;">
	<div class="pvr-detail mid-section"> 
    <!-- Header Section -->
    <div class="header">
		<a href="<?php echo e(route('faculty-planner-verification')); ?>?user_id=<?php echo e($user_id); ?>">
			<button class="back-button">
				<img src="<?php echo e(asset('laravel/public/course-planner/back-arrow.svg')); ?>" alt="Back">
			</button>
		</a>
    </div>
    <!-- Main Content Section -->
    <div class="mcontent">
        <!-- Planner Information -->
        <div class="planner-info">
            <div class="label">Planner For</div>
            <div class="value"><?=$course;?> - <?=$planner_name;?></div>
        </div>
		
		<div class="planner-info np-sec">
            <div class="label">Subject</div>
            <div class="value"><?=$subject;?></div>
        </div>
		<form id="facultyTimeForm" method="POST">
			<?php echo csrf_field(); ?>
			<!-- Subject Information -->
			<div class="subject">			
				<div> 
					<div class="value">
						<textarea name="faculty_remark" class="form-control" style="border:none !important"><?php echo e($topic_relation[0]->faculty_remark??''); ?></textarea>
						<input type="hidden" name="faculty_id" value="<?php echo e($user_id); ?>"/>
						<input type="hidden" name="req_id" value="<?php echo e($req_id); ?>"/>
						<input type="hidden" name="cpsr_id" value="<?php echo e($cpsr_id); ?>"/>
					</div>
				</div>
			</div>

			<!-- Topics Table -->		
			<div class="scroll-table">
				<table class="topic-table">
					<thead>
						<tr>
							<th>Topic Name</th>
							<th>Sub Topic</th>
							<th>Assign Time (min)</th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $topic_relation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($tr->topic_name); ?></td>
								<td><?php echo e($tr->sub_topic_name); ?></td>
								<td>
									<input type="number" class="time-input" name="duration[]" value="<?php echo e($tr->duration ?? 0); ?>" <?php echo e($tr->fstatus == 1 ? 'readonly' : ''); ?>>

									<input type="hidden" name="tr_id[]" value="<?php echo e($tr->id); ?>">
								</td>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
			</div>

			<?php
				$hasEditable = collect($topic_relation)->contains(function($item) {
					return $item->fstatus != 1;
				});
			?>

			<?php if($hasEditable): ?>
				<div class="form-actions">
					<button type="button" class="submit-btn btn btn-secondary text-dark" data-id="2">Save as Draft</button>
					<button type="button" class="submit-btn btn btn-primary" data-id="1">Submit</button>
				</div>
			<?php endif; ?>
		</form>


    </div>
	</div>
</body>
<script src="<?php echo e(asset('laravel/public/course-planner/filter.js')); ?>" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
$(document).ready(function () {
    $('.submit-btn').click(function (e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        let form = $('#facultyTimeForm');
        let formData = form.serialize() + '&submit_type=' + dataId;
        $.ajax({
            url: '<?php echo e(route("faculty-add-time")); ?>', // double quotes for Blade inside JS
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


$(document).ready(function () {
    $('.remark-btn').click(function (e) {
        e.preventDefault();
        let form = $('#facultyRemarkForm');
        let formData = form.serialize();
        $.ajax({
            url: '<?php echo e(route("faculty-add-remark")); ?>',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert('Remark updated successfully!');
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/mutli-course-planner/assign-time.blade.php ENDPATH**/ ?>