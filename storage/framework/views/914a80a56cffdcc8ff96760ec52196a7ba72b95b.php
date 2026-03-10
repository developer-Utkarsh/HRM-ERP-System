
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Faculty/SME Chat</h2>
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
				<div>
					<?php 
						foreach($record as $re){ 
							if($re->user_id ==  Auth::user()->id){
								$heading = 'SME';
								$class = 'ml-auto';
							}else{
								$heading = 'Faculty';
								$class = '';
							}
					?>
					<div class="bg-white col-lg-6 mt-1 p-1 border <?=$class;?>" style="border-radius:15px;">
						<b class="text-primary"><?php echo e($heading); ?></b>
						<p class="my-0"><?php echo e($re->message); ?></p>
						<b><?php echo e($re->name); ?> <i><?php echo e(date("jS F, Y | h:ia", strtotime($re->created_at))); ?></i></b>
					</div>
					<?php } ?>
				</div>
				<div class="card mt-1">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.faculty-sme-chat-submit')); ?>" method="post">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="request_id" value="<?php echo e($request_id); ?>"/> 
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-10">
											<fieldset class="form-group">
												<textarea name="chat_msg" class="form-control" placeholder="Write Message..."></textarea>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Send</button>
											</fieldset>
										</div>
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
 
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Upload File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-4 form-group">
						<label><b>Category</b></label>
						<select name="" class="form-control">
							
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Exam</b></label>
						<select name="" class="form-control select-multiple">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Subject</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Chapter</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>No Of Question</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Mode</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">Manual</option>
							<option value="">PrashnKosh</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Level</b></label>
						<select name="" class="form-control">
							<option value="">Select </option>
							<option value="">Easy</option>
							<option value="">Medium</option>
							<option value="">Hard</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Requirement For</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">YouTube</option>
							<option value="">Offline Batch</option>
							<option value="">Online Batch</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Language</b></label>
						<select name="" class="form-control">
							<option value="">Select</option>
							<option value="">English</option>
							<option value="">Hindi</option>
							<option value="">Bilingual</option>
						</select>
					</div>
					<div class="col-lg-4 form-group">
						<label><b>Browse File</b></label>
						<input type="file" name="" value="" class="form-control"/>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">
	
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_sme/chat.blade.php ENDPATH**/ ?>