<?php 
	$status = 'Pending';
	if ($items->topic_sme_status == 2) {
		$status = 'Save As Draft';
	} elseif ($items->topic_sme_status == 1) {
		$status = 'Submit';
	}
?>
<div class="row">
	<div class="col-lg-6">
		<h5 class="mb-2">Subject: 
		<span class="text-danger"><?php echo e($items->subject_name ?? 'N/A'); ?> - <?php echo e($items->sme_name ?? ''); ?></span></br>
		Faculty Comment : <?php echo e($items->faculty_remark ?? '-'); ?></h5>
	</div>
	<div class="col-lg-6 text-right">
		<h5 class="mb-2"><strong>SME Status: <span class="text-danger"><?php echo e($status); ?></span></strong></h5>
	</div>
</div>

<?php 
	$get_topic = DB::table('course_planner_topic_relation')->where('subject_id',$items->subject_id)->where('req_id',$items->req_id)->get();
?>

<?php $__currentLoopData = $get_topic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<input type="hidden" name="sr_id[]" value="<?php echo e($tr->id); ?>"/>
<div class="row">
	<div class="col-12 col-sm-6 col-lg-4">
		<label>Topic</label>
		<fieldset class="form-group">											
			<select name="topic_id[]" class="topic_id form-control select-multiple1" onchange="setSubjectId(this);">
				<option value="">-- Select --</option>
				<?php $__currentLoopData = $topic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $to): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if($to->subject_id == $tr->subject_id): ?>
						<option value="<?php echo e($to->id); ?>" data-id="<?php echo e($to->subject_id); ?>" <?php echo e($tr->topic_id == $to->id ? 'selected' : ''); ?>>							
							<?php echo e($to->name); ?><?php echo e((!empty($to->name) || !empty($to->en_name)) ? ' || ' . $to->en_name : ''); ?>

						</option>
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>
			<input type="hidden" name="subject_id[]" class="subject_id" value="<?php echo e($tr->subject_id); ?>"/>
		</fieldset>
	</div>

	<div class="col-12 col-sm-6 col-lg-3 d-none">
		<label>Sub Topic</label>
		<fieldset class="form-group">										
			<select name="sub_topic[]" class="sub_topic form-control select-multiple1">
				<option value="">-- Select --</option>
				<?php
					$sub_topic = DB::table('sub_topic_master')
						->where('topic_id', $tr->topic_id)
						->where('status', 1)
						->get();
				?>
				<?php $__currentLoopData = $sub_topic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($st->id); ?>" <?php echo e($tr->sub_topic_id == $st->id ? 'selected' : ''); ?>>
						<?php echo e($st->name); ?><?php echo e((!empty($st->name) || !empty($st->en_name)) ? ' || ' . $st->en_name : ''); ?>

					</option>

				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>
		</fieldset>
	</div>
	
	<?php $total_duration += (int) $tr->duration; ?>
	<div class="col-12 col-sm-6 col-lg-4">
		<label>Time (In Minutes)</label>
		<fieldset class="form-group">											
			<input type="number" value="<?php echo e($tr->duration); ?>" name="duration[]" class="form-control" readonly/>
		</fieldset>
	</div>		
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /var/www/html/laravel/resources/views/admin/multi-course-planner/subject_wise.blade.php ENDPATH**/ ?>