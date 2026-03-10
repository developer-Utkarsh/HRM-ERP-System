<?php $__currentLoopData = $topic_relation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php $total_duration = 0; ?>
	
	<div class="subject-form-block" data-subject="<?php echo e($items->subject_id); ?>">
		<input type="hidden" name="req_id" value="<?php echo e($record[0]->id??0); ?>">
		<input type="hidden" name="course_id" value="<?php echo e($record[0]->course_id??0); ?>">
		
		<!-- Subject Wise Add -->
		<?php echo $__env->make('admin.multi-course-planner.subject_wise', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		
		<?php if($items->topic_sme_status!=1): ?>		
			<!-- your existing subject HTML -->
			<div class="row filter-section" id="filter-section">
				<?php
					$topicn = DB::table('topic_master')
						->orderBy('id', 'desc')
						->where('subject_id', $items->subject_id)
						->where('status', 1)
						->get();
				?>
				
				<div class="col-12 col-sm-6 col-lg-3">
					<label for="users-list-role">Topic</label>
					<fieldset class="form-group">
						<select class="form-control select-multiple topic_id" name="topic_id[]" onchange="setSubjectId(this);">

							<option value="">Select Any</option>
							<?php $__currentLoopData = $topicn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $to): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($to->id); ?>" data-id="<?php echo e($to->subject_id); ?>">
								<?php echo e($to->name); ?><?php echo e((!empty($to->name) || !empty($to->en_name)) ? ' || ' . $to->en_name : ''); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</select>
						<input type="hidden" name="subject_id[]" class="subject_id" />
					</fieldset>
				</div>

				<div class="col-12 col-sm-6 col-lg-3 d-none">
					<label for="users-list-status">Sub Topic</label>
					<fieldset class="form-group">												
						<select class="form-control select-multiple sub_topic" name="sub_topic[]">
							<?php if(!empty(old('topic_id'))): ?>
								<?php
									$subtopicData = DB::table('sub_topic_master')->where('topic_id', old('topic_id'))->get();
								?>
								<?php $__currentLoopData = $subtopicData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subtopicDataValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($subtopicDataValue->id); ?>" <?php echo e(old('sub_cat_id', !empty(old('cat_id')) && $subtopicDataValue->id == old('cat_id') ? 'selected' : '' )); ?>>
										<?php echo e($subtopicDataValue->name); ?><?php echo e((!empty($subtopicDataValue->name) || !empty($subtopicDataValue->en_name)) ? ' || ' . $subtopicDataValue->en_name : ''); ?>

									</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<option value="">Select Sub Topic</option>
							<?php endif; ?>
						</select>											
					</fieldset>
				</div>
				<?php if(Auth::user()->role_id==21 || Auth::user()->id==8232){ ?>
				<div class="col-12 col-sm-6 col-lg-3">
					<label>Duration</label>
					<fieldset class="form-group">
						<input type="number" name="duration[]" class="form-control duration" value="<?php echo e(old('duration')); ?>">
					</fieldset>
				</div>
				<?php } ?>
				<div class="col-12 col-sm-6 col-lg-1">
					<label for="">&nbsp;</label>
					<fieldset class="form-group">
						<button type="button" class="btn btn-sm p-1 btn-primary add-more">Add</button>
						<button type="button" class="btn btn-sm p-1 btn-danger remove-section" style="display:none;">Remove</button>
					</fieldset>
				</div>
			</div>
			
			<!-- place your submit buttons here -->
			<button type="button" class="submit-btn btn btn-secondary submit" data-id="2">Saved As Draft</button>
			<button type="button" class="submit-btn btn btn-primary submit" data-id="1">Submit</button>
		<?php endif; ?>
	</div>
	
		
	<?php if(!empty($total_duration)): ?>
	<div class="row">
		<div class="col-12 text-right">
			<h6><strong>Total Duration: <span class="text-primary"><?php echo e($total_duration); ?></span> mins</strong></h6>
		</div>
	</div>
	<?php endif; ?>
	<hr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /var/www/html/laravel/resources/views/admin/multi-course-planner/sme_summary.blade.php ENDPATH**/ ?>