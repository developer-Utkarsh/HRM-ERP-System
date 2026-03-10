
<?php $__env->startSection('content'); ?>
	<div class="app-content content">
		<div class="content-overlay"></div>
		<div class="header-navbar-shadow"></div>
		<div class="content-wrapper">
			<div class="content-header row">
				<div class="content-header-left col-md-12 col-12 mb-2">
					<div class="row breadcrumbs-top">
						<div class="col-8">
							<h2 class="content-header-title float-left mb-0">Access Request </h2>
							<div class="breadcrumb-wrapper col-12">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
									<li class="breadcrumb-item active">Create Access Request </li>
								</ol>
							</div>
						</div>
						<div class="col-4 text-right">
							<a href="<?php echo e(route('request-access')); ?>" class="btn btn-outline-primary mr-1">← Back</a>
						</div>
					</div>
				</div>
			</div>

			<div class="content-body">
				<section id="multiple-column-form">
					<div class="row match-height">
						<div class="col-12">
							<div class="card">
								<div class="card-content">
									<div class="card-body">
										<form class="form" method="POST" action="<?php echo e(route('request-access.store')); ?>">
											<?php echo csrf_field(); ?>
											<div class="form-body">
												<div class="row">
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label>Request For</label>
															<select name="request_for" id="request_for"
																class="form-control">
																<option value="">-- Select --</option>
																<option value="self">Self</option>
																<option value="team_member">Team Member</option>
															</select>
															<?php if($errors->has('request_for')): ?>
																<span class="text-danger"><?php echo e($errors->first('request_for')); ?></span>
															<?php endif; ?>
														</div>
													</div>
													<div class="col-md-6 col-12 d-none" id="employee_div">
														<div class="form-group">
															<label>Select Employee</label>
															<select name="employee_id" class="form-control select2"
																id="employee_id">
																<option value="">-- Select Employee --</option>
																<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?>

																		(<?php echo e($employee->register_id); ?>)</option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
															<?php if($errors->has('employee_id')): ?>
																<span class="text-danger"><?php echo e($errors->first('employee_id')); ?></span>
															<?php endif; ?>
														</div>
													</div>
												</div>
												<div id="more_request_wrapper">
													<div id="more_request" class="software-request">
														<div class="row">
															<div class="col-md-12 mt-2">
																<hr>
																<h5 class="text-bold">Software Access Requests</h5>
																<hr>
															</div>

															<div class="col-md-3">
																<div class="form-group">
																	<label>Access For</label>
																	<select name="software_ids[]" class="form-control ">
																		<option value="">-- Select Software --</option>
																		<?php $__currentLoopData = $software_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $software): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($software->id); ?>">
																				<?php echo e($software->name); ?>

																			</option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	</select>
																	<?php if($errors->has('software_ids.0')): ?>
																		<span class="text-danger"><?php echo e($errors->first('software_ids.0')); ?></span>
																	<?php endif; ?>
																</div>
															</div>
															<div class="col-md-3">
    															<div class="form-group">
    															    <label>Already Have Access?</label><br>
    															    <div>
    															        <input type="radio" name="already_have_access[0]" id="already_have_access_yes_0" value="yes" class="already-have-access">
    															        <label for="already_have_access_yes_0">Yes</label>
    															        <input type="radio" name="already_have_access[0]" id="already_have_access_no_0" value="no" class="already-have-access">
    															        <label for="already_have_access_no_0">No</label>
    															    </div>
    															    <?php if($errors->has('already_have_access.0')): ?>
    															        <span class="text-danger"><?php echo e($errors->first('already_have_access.0')); ?></span>
    															    <?php endif; ?>
    															</div>
															</div>
															<div class="col-md-3 d-none request-type">
																<div class="form-group">
																	<label>Request Type</label>
																	<select name="request_type[]" class="form-control">
																		<option value="">-- Select --</option>
																		<option value="New Request">New Request</option>
																		<option value="Upgrade Access">Upgrade Access </option>
																		<option value="Downgrade Access">Downgrade Access </option>
																	</select>
																	<?php if($errors->has('request_type.0')): ?>
																		<span class="text-danger"><?php echo e($errors->first('request_type.0')); ?></span>
																	<?php endif; ?>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Access Level</label>
																	<select name="access_level[]" required class="form-control">
																		<option value="">-- Select --</option>
																		<option value="Read-Only">Read-Only</option>
																		<option value="Edit & Write">Edit & Write</option>
																		<option value="Admin/Full Access">Admin / Full Access</option>
																	</select>
																	<?php if($errors->has('access_level.0')): ?>
																		<span class="text-danger"><?php echo e($errors->first('access_level.0')); ?></span>
																	<?php endif; ?>
																</div>
															</div>
															<div class="col-md-12">
																<div class="form-group">
																	<label>Purpose of Access</label>
																	<textarea name="purpose[]" class="form-control" placeholder="Describe why this access is needed in the context of your KRAs."></textarea>
																	<?php if($errors->has('purpose.0')): ?>
																		<span class="text-danger"><?php echo e($errors->first('purpose.0')); ?></span>
																	<?php endif; ?>
																</div>
															</div>
															<div class="col-md-12">
																<div class="form-group">
																	<label>Additional Comments / Remarks (Optional)</label>
																	<textarea name="remarks[]" class="form-control" placeholder="Add any specific information or context for the software owner (optional)."></textarea>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-12 text-right mb-2 d-none p-0" id="addMoreSoftware">
													<button type="button" class="btn btn-outline-primary btn-sm" id="addSoftwareBtn"> + Add Another Software Request </button>
												</div>
												<div class="col-md-12 d-flex justify-content-end mt-2">
													<button type="submit" class="btn font-weight-bold btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('.select2').select2({
				placeholder: "-- Select --",
				allowClear: true,
				width: '100%'
			});

			$('#request_for').on('change', function () {
				if($(this).val() == 'self'){
					$('#addMoreSoftware').removeClass('d-none');
				}else if ($(this).val() === 'team_member' ) {
					$('#employee_div').removeClass('d-none');
					$('#addMoreSoftware').removeClass('d-none');
				} else {
					$('#employee_div').addClass('d-none');
					$('#employee_id').val('');
					$('#addMoreSoftware').addClass('d-none');
				}
			});

			$(document).on('change', '.already-have-access', function () {
				let $section = $(this).closest('.software-request');
				let selectedValue = $(this).val();
				let $requestType = $section.find('.request-type');
				// let $accessLevel = $section.find('.access-level');

				if (selectedValue === 'no') {
					$requestType.removeClass('d-none');
					$accessLevel.removeClass('d-none');
				} else {
					$requestType.addClass('d-none');
					$accessLevel.addClass('d-none');
				}
			});

			let index = 1;
			$('#addSoftwareBtn').on('click', function () {
				let $clone = $('#more_request').clone();
				$clone.removeAttr('id');
				$clone.addClass('software-request');
				$clone.find('input, select, textarea').each(function () {
					let name = $(this).attr('name');
					if (name) {
						name = name.replace(/\[\d*\]/, `[${index}]`);
						$(this).attr('name', name);
					}

					if ($(this).is(':radio') || $(this).is(':checkbox')) {
						$(this).prop('checked', false);
					} else {
						$(this).val('');
					}
				});

				$clone.find('.request-type').addClass('d-none');
				// $clone.find('.access-level').addClass('d-none');
				$clone.find('select').each(function () {
					$(this).val('').trigger('change');
				});
				$clone.find('.row').append(`
									<div class="col-md-12 text-right mt-2">
										<button type="button" class="btn mb-2 btn-outline-danger btn-sm remove-software-btn">
											Remove Software Request
										</button>
									</div>
								`);
				$('#more_request_wrapper').append($clone);

				$clone.find('select').select2({
					placeholder: "-- Select --",
					allowClear: true,
					width: '100%'
				});

				index++;
			});

			$(document).on('click', '.remove-software-btn', function () {
				$(this).closest('.software-request').remove();
			});
		});
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request-access/create.blade.php ENDPATH**/ ?>