<div class="card filterCard" <?php if(empty(app('request')->input('search'))): ?> style="display:none;" <?php endif; ?>>
	<div class="card-content collapse show">
		<div class="card-body">
			<div class="users-list-filter">
				<form action="<?php echo e(\Request::Url()); ?>" method="get" name="filtersubmit">
					<div class="row">
						<div class="col-md-3">
							<label for="users-list-role">Search</label>
							<fieldset class="form-group">
								<input type="text" class="form-control" name="reg_no" 
								value="<?php if(!empty(app('request')->input('reg_no'))): ?> <?php echo e(app('request')->input('reg_no')); ?> <?php endif; ?>" placeholder="Ex:Mobile, Reg Number">
							</fieldset>
						</div>

						<div class="col-12 col-md-3">
							<label for="users-list-status">Category</label>
							<?php 
							$category=\App\SupportCategory::where('status', 'Active')
									->where('is_deleted','0')->get();
							?>
							<fieldset class="form-group">												
								<select class="form-control" name="category_id">
									<option value="">Select Any</option>
									<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($val->id); ?>"  <?php if(!empty(app('request')->input('category_id')) && $val->id==app('request')->input('category_id')): ?> selected="selected" <?php endif; ?>><?php echo e($val->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</fieldset>
						</div>
						
						<div class="col-12 col-sm-6 col-lg-3 branch_loader">
							<label for="users-list-status">Location</label>
							<fieldset class="form-group">
								<select class="form-control branch_location" name="branch_location" id="">
									<option value="">Select Any</option>
									<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
									<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
									<option value="delhi" <?php if('delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
									<option value="prayagraj" <?php if('prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
									<option value="indore" <?php if('indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Indore</option>
								</select>
							</fieldset>
						</div>
					
						<div class="col-12 col-md-3 branch_loader">
							<label for="users-list-status">Branch</label>
							<?php 
							$branches = \App\Branch::where('status', 1)
									->where('is_deleted','0');
							if(!empty($login_brances)){
								$branches->whereIn('id', $login_brances);
							}
							$branches = $branches->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
							?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple1 branch_id" name="branch_id[]" multiple style="width:100%;">
									<option value="">Select Any</option>
									<?php if(count($branches) > 0): ?>
									<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($value->id); ?>"  <?php if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</select>
								<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
							</fieldset>
						</div>

						<div class="clearfix"></div>
						
						<div class="col-12 col-md-3">
							<label for="users-list-status">Class Room / Studio</label>
							<fieldset class="form-group">
								<select class="form-control select-multiple3 studio_id" name="studio_id" style="width:100%;">
									<option value="">Select Any</option>
								</select>												
							</fieldset>
						</div>
						
						<input type="hidden" class="assistant_id_get" value="<?php echo e(app('request')->input('assistant_id')); ?>">
						
						<div class="col-12 col-md-3">
							<label for="users-list-status">Assistants</label>
							<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple2 assistant_id" name="assistant_id" style="width:100%;">
									<option value="">Select Any</option>
									<?php if(count($assistants) > 0): ?>
									<?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('assistant_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</select>												
							</fieldset>
						</div>
						
						<div class="col-12 col-sm-6 col-lg-2">
							<label for="users-list-status">Status</label>
								<select class="form-control type" name="status">
									<option value="">Select Status</option>
									<option value="pending" <?php if('pending' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Pending</option>
									<option value="replied" <?php if('replied' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Replied</option>
									<option value="resolved" <?php if('resolved' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Resolved</option>
									<option value="reopen" <?php if('reopen' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Reopen</option>
								</select>												
							</fieldset>
						</div>

						<div class="col-12 col-sm-6 col-lg-3">
							<label>Rating</label>
							<fieldset class="form-group">
								<select class="form-control" name="rating">
									<option value="">Select Any</option>
									<option value="Excellent" <?php if('Excellent' == app('request')->input('rating')): ?> selected="selected" <?php endif; ?>>Excellent</option>
									<option value="Average" <?php if('Average' == app('request')->input('rating')): ?> selected="selected" <?php endif; ?>>Average</option>
									<option value="Poor" <?php if('Poor' == app('request')->input('rating')): ?> selected="selected" <?php endif; ?>>Poor</option>
								</select>
							</fieldset>
						</div>

						<div class="col-12 col-md-3">
							<label for="users-list-status">Course</label>
							<?php
							$tt=DB::table('timetables')->select('course_id')->where('is_deleted', '0')
							->where('cdate','>=', date('Y-m-d',strtotime(now().' -10 days')))
							->groupby('course_id')->get();
							$course_ids=[];
							foreach ($tt as $val) {
							  $course_ids[]=$val->course_id;
							}

							$courses = \App\Course::where('status', '1')->where('is_deleted', '0')
							->whereIN('id',$course_ids)->orderBy('id','DESC')->get(); ?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple4 course_id" name="course_id[]" multiple style="width:100%">
									<option value="">Select Any</option>
									<?php if(count($courses) > 0): ?>
										<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('course_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</select>												
							</fieldset>
						</div>
						
						<div class="col-12 col-md-2">
							<label for="users-list-status">From Date</label>
							<fieldset class="form-group">									
								<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">
							</fieldset>
						</div>
						
						<div class="col-12 col-md-2">
							<label for="users-list-status">To Date</label>
							<fieldset class="form-group">												
								<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
							</fieldset>
						</div>

						<div class="col-12 col-md-4 mt-2">
							<fieldset class="form-group">		
								<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
								<a href="<?php echo e(\Request::Url()); ?>" class="btn btn-warning">Reset</a>
							</fieldset>
					    </div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>

<?php $__env->startPush('js'); ?>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.select-multiple1').select2({
				placeholder: "Select Any",
				allowClear: true
			});

			$('.select-multiple2').select2({
				placeholder: "Select Any",
				allowClear: true
			});
			$('.select-multiple3').select2({
				placeholder: "Select Any",
				allowClear: true
			});

			$('.select-multiple4').select2({
				placeholder: "Select Any",
				allowClear: true
			});

			$(".filterIcon").on("click",function(){
                 $(".filterCard").toggle();
			})
		});
	</script>

	<script type="text/javascript">
		$(".branch_id").on("change", function () {
			var branch_id = $(".branch_id option:selected").attr('value');
			var assistant_id = $("input[name=assistant_id]").val();
			if (branch_id) {
				$.ajax({
					beforeSend: function(){
						// $(".branch_loader i").show();
					},
					type : 'POST',
					url : '<?php echo e(route('get-branchwise-studio')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id},
					dataType : 'html',
					success : function (data){
						// $(".branch_loader i").hide();
						$('.studio_id').empty();
						$('.studio_id').append(data);
					}
				});
				
				$.ajax({
					beforeSend: function(){
						// $(".branch_loader i").show();
					},
					type : 'POST',
					url : '<?php echo e(route('get-branchwise-assistant')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'assistant_id': assistant_id},
					dataType : 'html',
					success : function (data){
						// $(".branch_loader i").hide();
						$('.assistant_id').empty();
						$('.assistant_id').append(data);
					}
				});
				
				
			}
		});
		
		$(document).ready(function() {
			var branch_id = $(".branch_id option:selected").attr('value');
			var assistant_id = $(".assistant_id_get").val();
			if (branch_id) {
				$.ajax({
					beforeSend: function(){
						// $(".branch_loader i").show();
					},
					type : 'POST',
					url : '<?php echo e(route('get-branchwise-assistant')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'assistant_id': assistant_id},
					dataType : 'html',
					success : function (data){
						// $(".branch_loader i").hide();
						$('.assistant_id').empty();
						$('.assistant_id').append(data);
					}
				});
			}
		});
		
		$(".branch_location").on("change", function () {
			var b_location = $(this).val();
			if (b_location) {
				$.ajax({
					beforeSend: function(){
						// $(".branch_loader i").show();
					},
					type : 'POST',
					url : '<?php echo e(route('get-location-wise-branch')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'b_location': b_location},
					dataType : 'html',
					success : function (data){
						// $(".branch_loader i").hide();
						$('.branch_id').empty();
						$('.branch_id').append(data);
					}
				});
				
			}
		});
		
	</script>
<?php $__env->stopPush(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/support_category/filter.blade.php ENDPATH**/ ?>