
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
								<form action="<?php echo e(route('admin.faculty-sme.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Request ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="request_id" placeholder="Request ID" value="<?php echo e(app('request')->input('month')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Status</label>
											<fieldset class="form-group">
												<select class="form-control req_status" name="req_status">
													<option value="">Select Status</option>
													<?php $__currentLoopData = [0 => 'Pending', 1 => 'Completed', 2 => 'Rejected', 3 => 'Picked']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($value); ?>" <?php echo e(request('req_status') == $value ? 'selected' : ''); ?>>
															<?php echo e($label); ?>

														</option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.faculty-sme.index')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Request ID</th>
								<th>Faculty Name</th>
								<th>Message</th>
								<th>Date</th>
								<th>Status</th>
								<th>Reason</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){
								$i = 1;
								foreach($record as $re){
									
							?>
							<tr>
								<td><?php echo e($i++); ?> </td>
								<td><?php echo e($re->request_id); ?> </td>
								<td><?php echo e($re->name); ?></td>
								<td><?php echo e($re->message); ?></td>
								<td>
									<?php 
										if(!empty($re->date)){ 
											echo date('d-m-Y h:i:s',strtotime($re->date));
										} 
									?>
								</td>
								<td>
									<?php if($re->status == 0): ?>
										<span class="badge bg-warning">Pending</span>
									<?php elseif($re->status == 1): ?>
										<span class="badge bg-success">Completed</span>
									<?php elseif($re->status == 2): ?>
										<span class="badge bg-danger">Rejected</span>
									<?php elseif($re->status == 3): ?>
										<span class="badge bg-primary">Picked</span>
									<?php endif; ?>
								</td>
								</td>
								<td><?php echo e($re->reason??'-'); ?></td>
								<td class="actionBtn">
									<a href="<?php echo e(route('admin.faculty-sme.faculty-sme-chat')); ?>?request_id=<?php echo e($re->request_id); ?>" title="Chat" class="text-success"><i class="fa fa-commenting-o" aria-hidden="true"></i>
									
									<?php if($re->chat_status_2_count >0){ ?>
									<i class="fa fa-circle text-danger" aria-hidden="true" style="position:relative;top:-5px;font-size:8px;"></i>
									<?php } ?></a>
									
									<?php if($re->status==0){ ?>
									<a href="<?php echo e(route('admin.faculty-sme.faculty-sme-request-pick')); ?>?request_id=<?php echo e($re->request_id); ?>" class="text-warning" title="Pick Task"><i class="fa fa-check" aria-hidden="true"></i></a>
									<?php }else if($re->picked_id == Auth::user()->id){  ?>
										
										<?php 
											if(empty($re->assistant_used)){
												// if($re->status==0 || $re->status==3){ 
													if(empty($re->file)){ 
										?>
										<a href="<?php echo e(route('admin.faculty-sme-uploadfile')); ?>?request_id=<?php echo e($re->request_id); ?>" title="Upload File"><i class="fa fa-upload" aria-hidden="true"></i></a>
										<?php }else{ ?>
										<a href="<?php echo e(route('admin.faculty-sme-uploadfile')); ?>?request_id=<?php echo e($re->request_id); ?>" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
										<?php 
													} 
												// }
											}else{
										?>
											<a href="<?php echo e(route('admin.faculty-sme-uploadfile')); ?>?request_id=<?php echo e($re->request_id); ?>&viewonly=1" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
										<?php } ?>
										
										<?php if(empty($re->file)){ ?>
										<a href="<?php echo e(route('admin.faculty-sme.faculty-sme-reuse')); ?>?request_id=<?php echo e($re->request_id); ?>" class="text-warning"><i class="fa fa-repeat" aria-hidden="true" title="Reuse"></i></a>
										<?php } ?>									
										
										
									
										<?php if($re->status==0 || $re->status==3){ ?>
										<a href="javascript:void(0)" class="status-model text-danger" data-id="<?php echo e($re->id); ?>"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
										<?php } ?>
										
									<?php }else{ echo '-'; }  ?>
								</td>
							</tr>
							<?php } }else{ ?>
							<tr>
								<td colspan="9" class="text-center">No Data Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>					
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


<!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Update Request Status</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form action="<?php echo e(route('admin.faculty-sme.faculty-sme-update-stauts')); ?>" method="post">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="request_id" value="" class="pop_rid"/>
			<div class="modal-body">
				<div>
					<select class="form-control" name="status" onchange="getStatus(this.value)">
						<option value="">--Select Status--</option>
						<!--option value="0">Pending</option-->
						<!--option value="3">Picked</option-->
						<option value="1">Completed</option>
						<option value="2">Rejected</option>
					</select>
				</div>
				<div class="pt-2 rejectFiled" style="display:none">
					<textarea name="reject_reason" class="form-control" rows="3" placeholder="Write Reject Reason"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
    </div>
  </div>
</div>

<style>
	.actionBtn i{
		font-size:14px;
		letter-spacing: 2px;
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
	
	function getStatus(val){
		if(val==2){
			$('.rejectFiled').show();
			$('.rejectFiled textarea').prop('required',true);
		}else{
			$('.rejectFiled').hide();
			$('.rejectFiled textarea').prop('required',false);
		}
	}
	
	$(document).on("click", ".status-model", function() {
		let dataId = $(this).attr("data-id"); 
		$(".pop_rid").val(dataId); 
		$("#exampleModalLong").modal("show"); 
	});

	
	$(document).on("change",".select_category_name",function(){
		var category_name = $(this).val();
		var _this = $(this);
		if(category_name){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('admin.crm-desk.get_main_cat')); ?>',
				data : {'category_name': category_name},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status == false){
						
					}
					else if(data.status == true){					
						$(".select_main_category").html(data.html);
					}
				}
			});   
		}
	})
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_sme/index.blade.php ENDPATH**/ ?>